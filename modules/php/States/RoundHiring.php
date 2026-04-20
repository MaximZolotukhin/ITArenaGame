<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SpecialistsData;

/**
 * Фаза «Найм» (Hiring) раунда.
 * Действия: 1) Рекрутинг — взять карты из колоды найма по треку найма в бэк-офисе; 2–3) позже.
 */
class RoundHiring extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 17,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must complete the hiring phase'),
            descriptionMyTurn: clienttranslate('${you} must complete the hiring phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        if ($activePlayerIdRaw === null) {
            // Иногда при редиректе в состояние активный игрок может быть не установлен.
            // В этом случае выбираем следующего игрока, чтобы не работать с player_id=0.
            $this->game->activeNextPlayer();
            $activePlayerIdRaw = $this->game->getActivePlayerId();
        }
        $activePlayerId = (int) $activePlayerIdRaw;
        if ($activePlayerId <= 0) {
            // Безопасный возврат: не даём состоянию уронить весь запрос из-за отсутствия active player.
            // UI всё равно не сможет корректно отрисовать фазу без playerId.
            return [
                'phaseKey' => 'hiring',
                'phaseName' => $this->game->getPhaseName('hiring'),
                'currentRoundPlayerOrder' => $this->game->getCurrentRoundPlayerOrder(),
                'hiringTrackValue' => 0,
                'recruitingDone' => false,
                'hiringTrackHire' => 0,
                'badgers' => 0,
                'handCardsWithPrices' => [],
                'maxHireCount' => 0,
                'hiringConfirmed' => false,
                'hiringHiredCount' => 0,
                'pendingTaskSelection' => null,
                'pendingTaskMoves' => null,
            ];
        }

        $this->game->initPlayerGameData($activePlayerId);
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        if (!is_array($gameData)) {
            // Доп. защита от повреждённых/неинициализированных данных.
            $gameData = [];
        }
        $hiringTrackValue = $this->game->getHiringTrackValue($activePlayerId);
        $recruitingDone = $this->game->globals->get('hiring_recruiting_done_' . $activePlayerId, '') === '1';
        $hiringTrackHire = $this->game->getHiringTrackHireCount($activePlayerId);
        $badgers = $this->game->getPlayerBadgersForCheck($activePlayerId);

        $handIds = $gameData['playerSpecialists'] ?? [];
        $handIds = is_array($handIds) ? array_map('intval', $handIds) : [];
        $handCardsWithPrices = [];
        foreach ($handIds as $cardId) {
            $card = SpecialistsData::getCard($cardId);
            if ($card) {
                $handCardsWithPrices[] = [
                    'id' => (int) $card['id'],
                    'price' => (int) ($card['price'] ?? 0),
                    'department' => $card['department'] ?? 'universal',
                    'name' => $card['name'] ?? '',
                    'img' => $card['img'] ?? '',
                ];
            }
        }

        $hiringConfirmed = $this->game->globals->get('hiring_confirmed_' . $activePlayerId, '') === '1';
        $hiringHiredCount = (int) $this->game->globals->get('hiring_hired_count_' . $activePlayerId, '0');
        $roundOrder = $this->game->getCurrentRoundPlayerOrder();

        $pendingTaskJson = $this->game->globals->get('pending_task_selection_' . $activePlayerId, '');
        $pendingTaskSelection = null;
        if ($pendingTaskJson !== null && $pendingTaskJson !== '') {
            $decoded = json_decode($pendingTaskJson, true);
            if (is_array($decoded) && isset($decoded['amount']) && (int) $decoded['amount'] > 0) {
                $pendingTaskSelection = [
                    'amount' => (int) $decoded['amount'],
                    'founder_name' => $decoded['founder_name'] ?? '',
                ];
            }
        }

        $pendingTaskMoves = null;
        $pendingMovesJson = $this->game->globals->get('pending_task_moves_' . $activePlayerId, '');
        if ($pendingMovesJson !== null && $pendingMovesJson !== '') {
            $decoded = json_decode($pendingMovesJson, true);
            if (is_array($decoded) && isset($decoded['move_count']) && (int) $decoded['move_count'] > 0) {
                $rawColor = $decoded['move_color'] ?? 'any';
                $moveColor = ($rawColor === 'any' || $rawColor === '') ? 'any' : strtolower(trim((string) $rawColor));
                if ($moveColor !== 'any' && $moveColor === 'cayn') {
                    $moveColor = 'cyan';
                }
                $pendingTaskMoves = [
                    'move_count' => (int) $decoded['move_count'],
                    'move_color' => $moveColor,
                    'founder_name' => $decoded['founder_name'] ?? '',
                ];
            }
        }

        return [
            'phaseKey' => 'hiring',
            'phaseName' => $this->game->getPhaseName('hiring'),
            'currentRoundPlayerOrder' => $roundOrder,
            'hiringTrackValue' => $hiringTrackValue,
            'recruitingDone' => $recruitingDone,
            'hiringTrackHire' => $hiringTrackHire,
            'badgers' => $badgers,
            'handCardsWithPrices' => $handCardsWithPrices,
            'maxHireCount' => $this->game->getEffectiveHiringMaxCount($activePlayerId),
            'hiringConfirmed' => $hiringConfirmed,
            'hiringHiredCount' => $hiringHiredCount,
            'pendingTaskSelection' => $pendingTaskSelection,
            'pendingTaskMoves' => $pendingTaskMoves,
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'hiring');
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        if ($activePlayerIdRaw === null) {
            $this->game->activeNextPlayer();
            $activePlayerIdRaw = $this->game->getActivePlayerId();
        }
        $activePlayerId = (int) $activePlayerIdRaw;
        if ($activePlayerId <= 0) {
            // Нечего инициализировать; дадим фреймворку шанс выставить активного игрока позже.
            error_log('RoundHiring::onEnteringState - WARNING: invalid activePlayerId=' . $activePlayerId);
            return null;
        }
        $this->game->globals->delete('hiring_confirmed_' . $activePlayerId);
        $this->game->globals->set('hiring_hired_count_' . $activePlayerId, '0');
        $this->game->resetHiringBonusHireSlots($activePlayerId);
        // Сбрасываем ожидание выбора задач при входе в фазу найма — блокировка только после найма карты с эффектом task
        $this->game->globals->set('pending_task_selection_' . $activePlayerId, null);
        $this->game->ensureSpecialistDeckInitialized();

        // Этап «собеседование»: при входе в фазу найма выдаём игроку hiringTrackInterview карт из колоды
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        if (!is_array($gameData)) {
            $gameData = [];
        }
        $count = (int) ($gameData['hiringTrackInterview'] ?? 0);
        if ($count > 0) {
            $drawn = $this->game->drawFromActiveDeck($count);
            if (!empty($drawn)) {
                $this->game->addSpecialistCardsToPlayerSpecialists($activePlayerId, $drawn);
                $gameDataAfter = $this->game->getPlayerGameData($activePlayerId);
                if (!is_array($gameDataAfter)) {
                    $gameDataAfter = [];
                }
                $handIds = $gameDataAfter['playerSpecialists'] ?? [];
                $handIds = is_array($handIds) ? array_map('intval', $handIds) : [];
                $handCardsWithPrices = [];
                foreach ($handIds as $cardId) {
                    $card = SpecialistsData::getCard($cardId);
                    if ($card) {
                        $handCardsWithPrices[] = [
                            'id' => (int) $card['id'],
                            'price' => (int) ($card['price'] ?? 0),
                            'department' => $card['department'] ?? 'universal',
                            'name' => $card['name'] ?? '',
                            'img' => $card['img'] ?? '',
                        ];
                    }
                }
                $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} получает ${amount} карт из колоды найма (собеседование)'), [
                    'player_id' => $activePlayerId,
                    'player_name' => $this->game->getPlayerNameById($activePlayerId),
                    'cardIds' => $drawn,
                    'amount' => count($drawn),
                    'source' => 'hiring_recruiting',
                    'handCardsWithPrices' => $handCardsWithPrices,
                    'i18n' => [],
                ]);
                // Собеседование не закрывает рекрутинг: игрок может ещё нажать «Рекрутинг»
            }
        }

        return null;
    }

    /**
     * Действие 1: Рекрутинг — взять из колоды найма столько карт, сколько показывает трек найма в бэк-офисе (колонка 1).
     * Карты добавляются в руку игрока.
     */
    #[PossibleAction]
    public function actRecruiting(): ?string
    {
        $this->game->checkAction('actRecruiting');
        $playerId = (int) $this->game->getActivePlayerId();
        if ($this->game->globals->get('hiring_recruiting_done_' . $playerId, '') === '1') {
            throw new UserException(clienttranslate('You have already performed recruiting this turn'));
        }
        $count = $this->game->getHiringTrackValue($playerId);
        $drawn = $this->game->drawFromActiveDeck($count);
        if (empty($drawn)) {
            // Не отмечаем recruiting_done, если карты реально не были выданы
            throw new UserException(clienttranslate('No specialist cards available to draw'));
        }

        // Рекрутинг выдаёт карты игроку "на руку" (закреплённые), чтобы они сразу отображались в UI
        $this->game->addSpecialistCardsToPlayerSpecialists($playerId, $drawn);
        $gameDataAfter = $this->game->getPlayerGameData($playerId);
        $handIds = $gameDataAfter['playerSpecialists'] ?? [];
        $handIds = is_array($handIds) ? array_map('intval', $handIds) : [];
        $handCardsWithPrices = [];
        foreach ($handIds as $cardId) {
            $card = SpecialistsData::getCard($cardId);
            if ($card) {
                $handCardsWithPrices[] = [
                    'id' => (int) $card['id'],
                    'price' => (int) ($card['price'] ?? 0),
                    'department' => $card['department'] ?? 'universal',
                    'name' => $card['name'] ?? '',
                    'img' => $card['img'] ?? '',
                ];
            }
        }
        $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} получает ${amount} карт из колоды найма (рекрутинг)'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'cardIds' => $drawn,
            'amount' => count($drawn),
            'source' => 'hiring_recruiting',
            'handCardsWithPrices' => $handCardsWithPrices,
            'i18n' => [],
        ]);
        $this->game->globals->set('hiring_recruiting_done_' . $playerId, '1');
        return null;
    }

    /**
     * Нанять одну карту с руки: клик по карте — карта переходит в отдел, баджерсы списываются сразу.
     * Для универсальной карты отдел передаётся (игрок выбирает отдел кликом); для остальных — из карты.
     *
     * @param int $cardId ID карты с руки
     * @param string|null $department отдел (sales-department|back-office|technical-department), обязателен для universal
     */
    #[PossibleAction]
    public function actHireOneSpecialist(int $cardId, ?string $department = null): ?string
    {
        $this->game->checkAction('actHireOneSpecialist');
        $playerId = (int) $this->game->getActivePlayerId();
        $maxHire = $this->game->getEffectiveHiringMaxCount($playerId);
        $hiredCount = (int) $this->game->globals->get('hiring_hired_count_' . $playerId, '0');
        if ($hiredCount >= $maxHire) {
            throw new UserException(clienttranslate('You cannot hire more specialists this phase'));
        }
        $card = SpecialistsData::getCard((int) $cardId);
        if (!$card) {
            throw new UserException(clienttranslate('Invalid card'));
        }
        $isUniversal = ($card['department'] ?? '') === 'universal';
        if ($isUniversal && ($department === null || $department === '')) {
            throw new UserException(clienttranslate('You must choose a department for a universal specialist'));
        }
        $validDepartments = ['sales-department', 'back-office', 'technical-department'];
        if ($department !== null && !in_array($department, $validDepartments, true)) {
            throw new UserException(clienttranslate('Invalid department'));
        }
        $this->game->hireOneSpecialistFromHand($playerId, (int) $cardId, $department);
        $newHiredCount = $hiredCount + 1;
        $this->game->globals->set('hiring_hired_count_' . $playerId, (string) $newHiredCount);
        // Если у карты есть эффект — применяем его (те же типы, что у основателей: badger, updateTrack, move_task и т.д.)
        $appliedEffects = [];
        if (isset($card['effect']) && $card['effect'] !== null && is_array($card['effect'])) {
            $appliedEffects = $this->game->applySpecialistEffect($playerId, (int) $cardId);
        }
        $this->game->applyHiringPhaseEffectBonuses($playerId, $appliedEffects);
        $bonusHireFromHand = 0;
        foreach ($appliedEffects as $eff) {
            if (is_array($eff) && ($eff['type'] ?? '') === 'hire_from_hand') {
                $bonusHireFromHand += (int) ($eff['amount'] ?? 0);
            }
        }
        $price = (int) ($card['price'] ?? 0);
        $maxHire = $this->game->getEffectiveHiringMaxCount($playerId);
        $pendingTaskSelection = null;
        $pendingTaskMoves = null;
        foreach ($appliedEffects as $eff) {
            if (isset($eff['type']) && $eff['type'] === 'task' && isset($eff['amount']) && (int) $eff['amount'] > 0) {
                $pendingTaskSelection = [
                    'amount' => (int) $eff['amount'],
                    'founder_name' => $card['name'] ?? '',
                ];
            }
            if (isset($eff['type']) && $eff['type'] === 'move_task' && isset($eff['move_count']) && (int) $eff['move_count'] > 0) {
                $pendingTaskMoves = [
                    'move_count' => (int) $eff['move_count'],
                    'move_color' => $eff['move_color'] ?? 'any',
                    'founder_name' => $card['name'] ?? '',
                ];
            }
        }
        $this->notify->all('specialistsHired', clienttranslate('${player_name} нанимает специалиста за ${badgers} баджерсов'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'cardIds' => [(int) $cardId],
            'amount' => 1,
            'badgers' => $price,
            'playerHiredSpecialists' => $this->game->getPlayerHiredSpecialists($playerId),
            'playerHiredSpecialistsDetails' => $this->game->getPlayerHiredSpecialistsDetails($playerId),
            'hiringHiredCount' => $newHiredCount,
            'maxHireCount' => $maxHire,
            'specialistEffectApplied' => !empty($appliedEffects),
            'appliedEffects' => $appliedEffects,
            'pendingTaskSelection' => $pendingTaskSelection,
            'i18n' => ['badgers'],
        ]);
        if ($bonusHireFromHand > 0) {
            $this->notify->player($playerId, 'hiringBonusAvailable', '', [
                'player_id' => $playerId,
                'amount' => $bonusHireFromHand,
                'source_name' => $card['name'] ?? '',
                'maxHireCount' => $maxHire,
            ]);
        }
        if ($pendingTaskSelection !== null) {
            $this->notify->player($playerId, 'taskSelectionRequired', '', [
                'player_id' => $playerId,
                'amount' => $pendingTaskSelection['amount'],
                'founder_name' => $pendingTaskSelection['founder_name'],
            ]);
        }
        if ($pendingTaskMoves !== null) {
            $this->notify->player($playerId, 'taskMovesRequired', '', [
                'player_id' => $playerId,
                'move_count' => (int) $pendingTaskMoves['move_count'],
                'move_color' => $pendingTaskMoves['move_color'] ?? 'any',
                'founder_name' => $pendingTaskMoves['founder_name'] ?? '',
            ]);
        } else {
            $pendingMovesJson = $this->game->globals->get('pending_task_moves_' . $playerId, '');
            if ($pendingMovesJson !== null && $pendingMovesJson !== '') {
                $pendingMoves = json_decode($pendingMovesJson, true);
                if (is_array($pendingMoves) && isset($pendingMoves['move_count']) && (int) $pendingMoves['move_count'] > 0) {
                    $this->notify->player($playerId, 'taskMovesRequired', '', [
                        'player_id' => $playerId,
                        'move_count' => (int) $pendingMoves['move_count'],
                        'move_color' => $pendingMoves['move_color'] ?? 'any',
                        'founder_name' => $pendingMoves['founder_name'] ?? '',
                    ]);
                }
            }
        }
        return null;
    }

    /**
     * Этап «найм»: подтвердить выбор карт для найма (пакетное действие, опционально).
     * Количество выбранных карт не больше hiringTrackHire, сумма цен не больше баджерсов игрока.
     * Для универсальных карт обязательно передать отдел в universalDepartments[cardId].
     *
     * @param array $selectedCardIds ID карт с руки для найма
     * @param array $universalDepartments для универсальных карт: cardId => отдел (sales-department|back-office|technical-department)
     */
    #[PossibleAction]
    public function actConfirmHiringSelection(array $selectedCardIds, array $universalDepartments = []): ?string
    {
        $this->game->checkAction('actConfirmHiringSelection');
        $playerId = (int) $this->game->getActivePlayerId();
        if ($this->game->globals->get('hiring_confirmed_' . $playerId, '') === '1') {
            throw new UserException(clienttranslate('You have already confirmed hiring this turn'));
        }

        $selectedCardIds = array_map('intval', array_values($selectedCardIds));
        $maxHire = $this->game->getEffectiveHiringMaxCount($playerId);

        if (count($selectedCardIds) > $maxHire) {
            throw new UserException(
                clienttranslate('You can hire at most ${n} specialist(s)') . ' → ' . $maxHire
            );
        }

        $validDepartments = ['sales-department', 'back-office', 'technical-department'];
        foreach ($selectedCardIds as $cid) {
            $card = SpecialistsData::getCard($cid);
            if ($card && ($card['department'] ?? '') === 'universal') {
                $chosen = $universalDepartments[$cid] ?? $universalDepartments[(string) $cid] ?? null;
                if ($chosen === null || !in_array($chosen, $validDepartments, true)) {
                    throw new UserException(clienttranslate('You must choose a department for each universal specialist'));
                }
            }
        }

        if (!empty($selectedCardIds)) {
            $universalMap = [];
            foreach ($universalDepartments as $k => $v) {
                $universalMap[(int) $k] = $v;
            }
            $this->game->hireSpecialistsFromHand($playerId, $selectedCardIds, $universalMap);
            $prevHired = (int) $this->game->globals->get('hiring_hired_count_' . $playerId, '0');
            $newHiredCount = $prevHired + count($selectedCardIds);
            $this->game->globals->set('hiring_hired_count_' . $playerId, (string) $newHiredCount);
            $totalPrice = 0;
            $allAppliedEffects = [];
            // Эффект применяем один раз на уникальный id карты (защита от дубликатов в selectedCardIds и от +2 вместо +1)
            $uniqueCardIds = array_unique($selectedCardIds);
            foreach ($selectedCardIds as $cid) {
                $c = SpecialistsData::getCard($cid);
                if ($c) {
                    $totalPrice += (int) ($c['price'] ?? 0);
                }
            }
            foreach ($uniqueCardIds as $cid) {
                $c = SpecialistsData::getCard($cid);
                if ($c && isset($c['effect']) && $c['effect'] !== null && is_array($c['effect'])) {
                    $allAppliedEffects = array_merge($allAppliedEffects, $this->game->applySpecialistEffect($playerId, (int) $cid));
                }
            }
            $this->game->applyHiringPhaseEffectBonuses($playerId, $allAppliedEffects);
            $bonusHireFromHand = 0;
            foreach ($allAppliedEffects as $eff) {
                if (is_array($eff) && ($eff['type'] ?? '') === 'hire_from_hand') {
                    $bonusHireFromHand += (int) ($eff['amount'] ?? 0);
                }
            }
            $taskAmountSum = 0;
            $pendingTaskMoves = null;
            foreach ($allAppliedEffects as $eff) {
                if (isset($eff['type']) && $eff['type'] === 'task' && isset($eff['amount'])) {
                    $taskAmountSum += (int) $eff['amount'];
                }
                if (isset($eff['type']) && $eff['type'] === 'move_task' && isset($eff['move_count']) && (int) $eff['move_count'] > 0) {
                    $pendingTaskMoves = [
                        'move_count' => (int) $eff['move_count'],
                        'move_color' => $eff['move_color'] ?? 'any',
                        'founder_name' => $eff['founder_name'] ?? '',
                    ];
                }
            }
            $pendingTaskSelection = null;
            if ($taskAmountSum > 0) {
                $pendingJson = $this->game->globals->get('pending_task_selection_' . $playerId, '');
                $pending = is_string($pendingJson) ? json_decode($pendingJson, true) : [];
                if (is_array($pending)) {
                    $pending['amount'] = $taskAmountSum;
                    $this->game->globals->set('pending_task_selection_' . $playerId, json_encode($pending));
                }
                $founderName = is_array($pending) ? ($pending['founder_name'] ?? '') : '';
                $pendingTaskSelection = [
                    'amount' => $taskAmountSum,
                    'founder_name' => $founderName,
                ];
            }
            $maxHire = $this->game->getEffectiveHiringMaxCount($playerId);
            $this->notify->all('specialistsHired', clienttranslate('${player_name} нанимает ${amount} специалистов за ${badgers} баджерсов'), [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'cardIds' => $selectedCardIds,
                'amount' => count($selectedCardIds),
                'badgers' => $totalPrice,
                'playerHiredSpecialists' => $this->game->getPlayerHiredSpecialists($playerId),
                'playerHiredSpecialistsDetails' => $this->game->getPlayerHiredSpecialistsDetails($playerId),
                'hiringHiredCount' => $newHiredCount,
                'maxHireCount' => $maxHire,
                'specialistEffectApplied' => !empty($allAppliedEffects),
                'appliedEffects' => $allAppliedEffects,
                'pendingTaskSelection' => $pendingTaskSelection,
                'i18n' => ['badgers'],
            ]);
            if ($bonusHireFromHand > 0) {
                // В пакетном найме может быть несколько карт с эффектом — источник не привязываем к одной карте
                $this->notify->player($playerId, 'hiringBonusAvailable', '', [
                    'player_id' => $playerId,
                    'amount' => $bonusHireFromHand,
                    'source_name' => '',
                    'maxHireCount' => $maxHire,
                ]);
            }
            if ($pendingTaskSelection !== null) {
                $this->notify->player($playerId, 'taskSelectionRequired', '', [
                    'player_id' => $playerId,
                    'amount' => $pendingTaskSelection['amount'],
                    'founder_name' => $pendingTaskSelection['founder_name'],
                ]);
            }
            if ($pendingTaskMoves !== null) {
                $this->notify->player($playerId, 'taskMovesRequired', '', [
                    'player_id' => $playerId,
                    'move_count' => (int) $pendingTaskMoves['move_count'],
                    'move_color' => $pendingTaskMoves['move_color'] ?? 'any',
                    'founder_name' => $pendingTaskMoves['founder_name'] ?? '',
                ]);
            } else {
                $pendingMovesJson = $this->game->globals->get('pending_task_moves_' . $playerId, '');
                if ($pendingMovesJson !== null && $pendingMovesJson !== '') {
                    $pendingMoves = json_decode($pendingMovesJson, true);
                    if (is_array($pendingMoves) && isset($pendingMoves['move_count']) && (int) $pendingMoves['move_count'] > 0) {
                        $this->notify->player($playerId, 'taskMovesRequired', '', [
                            'player_id' => $playerId,
                            'move_count' => (int) $pendingMoves['move_count'],
                            'move_color' => $pendingMoves['move_color'] ?? 'any',
                            'founder_name' => $pendingMoves['founder_name'] ?? '',
                        ]);
                    }
                }
            }
        }

        $this->game->globals->set('hiring_confirmed_' . $playerId, '1');
        return null;
    }

    /**
     * Игрок завершил фазу найма.
     */
    #[PossibleAction]
    public function actCompleteHiringPhase(): string
    {
        $this->game->checkAction('actCompleteHiringPhase');
        $playerId = (int) $this->game->getActivePlayerId();
        $pendingSelectionJson = $this->game->globals->get('pending_task_selection_' . $playerId, null);
        if ($pendingSelectionJson !== null) {
            $pendingSelection = json_decode($pendingSelectionJson, true);
            $amount = (int) ($pendingSelection['amount'] ?? 0);
            $msg = clienttranslate('Вы должны выбрать ${amount} задач в бэклог перед завершением фазы найма');
            throw new UserException(str_replace('${amount}', (string) $amount, $msg));
        }
        $pendingMovesJson = $this->game->globals->get('pending_task_moves_' . $playerId, null);
        if ($pendingMovesJson !== null && $pendingMovesJson !== '') {
            $pendingMoves = json_decode($pendingMovesJson, true);
            if (is_array($pendingMoves) && isset($pendingMoves['move_count']) && (int) $pendingMoves['move_count'] > 0) {
                throw new UserException(clienttranslate('Сначала подтвердите перемещение задач по треку спринта (эффект карты)'));
            }
        }
        $this->game->savePlayerGameDataOnTurnEnd($playerId);
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    /**
     * Подтверждение выбора задач от эффекта специалиста (effect task).
     */
    #[PossibleAction]
    public function actConfirmTaskSelection(string $selectedTasksJson): void
    {
        $this->game->checkAction('actConfirmTaskSelection');
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $pendingSelectionJson = $this->game->globals->get('pending_task_selection_' . $activePlayerId, null);
        if ($pendingSelectionJson === null) {
            throw new UserException(clienttranslate('Нет ожидающего выбора задач'));
        }
        $pendingSelection = json_decode($pendingSelectionJson, true);
        if (!is_array($pendingSelection) || !isset($pendingSelection['amount'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающего выбора задач'));
        }
        $requiredAmount = (int) $pendingSelection['amount'];
        $selectedTasks = json_decode($selectedTasksJson, true);
        if (!is_array($selectedTasks)) {
            throw new UserException(clienttranslate('Неверный формат данных выбранных задач'));
        }
        $totalSelected = 0;
        foreach ($selectedTasks as $task) {
            if (!is_array($task)) {
                continue;
            }
            $quantity = (int) ($task['quantity'] ?? 0);
            if ($quantity < 0) {
                throw new UserException(clienttranslate('Количество задач не может быть отрицательным'));
            }
            $totalSelected += $quantity;
        }
        if ($totalSelected !== $requiredAmount) {
            throw new UserException(clienttranslate('Вы должны выбрать ровно ${amount} задач', [
                'amount' => $requiredAmount,
            ]));
        }
        $addedTokens = $this->game->addTaskTokens($activePlayerId, $selectedTasks, 'backlog');
        $this->game->globals->set('pending_task_selection_' . $activePlayerId, null);
        $founderName = $pendingSelection['founder_name'] ?? '';
        $this->notify->all('tasksSelected', clienttranslate('${player_name} выбрал ${amount} задач от эффекта ${founder_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'amount' => $totalSelected,
            'founder_name' => $founderName,
            'selected_tasks' => $selectedTasks,
            'added_tokens' => $addedTokens,
            'i18n' => ['founder_name'],
        ]);
    }

    /**
     * Подтверждение перемещений задач (эффект move_task специалиста, напр. Леонид).
     */
    #[PossibleAction]
    public function actConfirmTaskMoves(int $activePlayerId, string $movesJson): void
    {
        $this->game->checkAction('actConfirmTaskMoves');
        $globalsKey = 'pending_task_moves_' . $activePlayerId;
        $pendingMovesJson = $this->game->globals->get($globalsKey, null);
        if ($pendingMovesJson === null || $pendingMovesJson === '') {
            throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
        }
        $pendingMoves = json_decode($pendingMovesJson, true);
        if (!is_array($pendingMoves) || !isset($pendingMoves['move_count'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающих перемещений'));
        }
        $requiredMoves = (int) $pendingMoves['move_count'];
        $moveColor = $pendingMoves['move_color'] ?? 'any';
        if ($moveColor !== 'any') {
            $moveColor = strtolower(trim((string) $moveColor));
            if ($moveColor === 'cayn') {
                $moveColor = 'cyan';
            }
        }
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }
        $totalBlocks = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['tokenId']) || !isset($move['toLocation'])) {
                continue;
            }
            if ($moveColor !== 'any') {
                $tokenId = (int) ($move['tokenId'] ?? 0);
                $tokenColor = $this->game->getTaskTokenColor($activePlayerId, $tokenId);
                if ($tokenColor === null) {
                    throw new UserException(clienttranslate('Жетон не найден'));
                }
                $normalized = ($tokenColor === 'cayn' ? 'cyan' : $tokenColor);
                $expected = ($moveColor === 'cayn' ? 'cyan' : $moveColor);
                if ($normalized !== $expected) {
                    throw new UserException(clienttranslate('Можно перемещать только жетоны указанного цвета'));
                }
            }
            $totalBlocks += (int) ($move['blocks'] ?? 0);
        }
        $maxBlocksAvailable = $this->game->getMaxTaskMoveBlocksForPlayer($activePlayerId, $moveColor);
        $allRequiredUsed = ($totalBlocks === $requiredMoves);
        $noMoreAvailable = ($maxBlocksAvailable === 0 && $totalBlocks === 0) || ($maxBlocksAvailable <= $totalBlocks && $totalBlocks <= $requiredMoves);
        if (!$allRequiredUsed && !$noMoreAvailable) {
            throw new UserException(clienttranslate('Вы должны использовать до ${amount} ходов или переместить все доступные задачи указанного цвета', [
                'amount' => $requiredMoves,
            ]));
        }
        $movedTokens = [];
        foreach ($moves as $move) {
            $tokenId = (int) ($move['tokenId'] ?? 0);
            $toLocation = $move['toLocation'] ?? '';
            if ($tokenId > 0 && $toLocation !== '') {
                $success = $this->game->updateTaskTokenLocation($tokenId, $toLocation, null);
                if ($success) {
                    $movedTokens[] = $tokenId;
                }
            }
        }
        $this->game->globals->set($globalsKey, null);
        $sourceName = $pendingMoves['founder_name'] ?? '';
        $this->notify->all('taskMovesCompleted', clienttranslate('${player_name} переместил задачи от эффекта ${founder_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $sourceName,
            'moves' => $moves,
            'moved_tokens' => $movedTokens,
            'i18n' => ['founder_name'],
        ]);
    }

    public function zombie(int $playerId): string
    {
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
