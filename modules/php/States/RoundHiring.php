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
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $this->game->initPlayerGameData($activePlayerId);
        $gameData = $this->game->getPlayerGameData($activePlayerId);
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

        return [
            'phaseKey' => 'hiring',
            'phaseName' => $this->game->getPhaseName('hiring'),
            'currentRoundPlayerOrder' => $roundOrder,
            'hiringTrackValue' => $hiringTrackValue,
            'recruitingDone' => $recruitingDone,
            'hiringTrackHire' => $hiringTrackHire,
            'badgers' => $badgers,
            'handCardsWithPrices' => $handCardsWithPrices,
            'maxHireCount' => $hiringTrackHire,
            'hiringConfirmed' => $hiringConfirmed,
            'hiringHiredCount' => $hiringHiredCount,
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'hiring');
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $this->game->globals->delete('hiring_confirmed_' . $activePlayerId);
        $this->game->globals->set('hiring_hired_count_' . $activePlayerId, '0');
        $this->game->ensureSpecialistDeckInitialized();

        // Этап «собеседование»: при входе в фазу найма выдаём игроку hiringTrackInterview карт из колоды
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        $count = (int) ($gameData['hiringTrackInterview'] ?? 0);
        if ($count > 0) {
            $drawn = $this->game->drawFromActiveDeck($count);
            if (!empty($drawn)) {
                $this->game->addSpecialistCardsToPlayerSpecialists($activePlayerId, $drawn);
                $gameDataAfter = $this->game->getPlayerGameData($activePlayerId);
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
        $maxHire = $this->game->getHiringTrackHireCount($playerId);
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
        $price = (int) ($card['price'] ?? 0);
        $maxHire = $this->game->getHiringTrackHireCount($playerId);
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
            'i18n' => ['badgers'],
        ]);
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
        $maxHire = $this->game->getHiringTrackHireCount($playerId);

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
            foreach ($selectedCardIds as $cid) {
                $c = SpecialistsData::getCard($cid);
                if ($c) {
                    $totalPrice += (int) ($c['price'] ?? 0);
                    if (isset($c['effect']) && $c['effect'] !== null && is_array($c['effect'])) {
                        $allAppliedEffects = array_merge($allAppliedEffects, $this->game->applySpecialistEffect($playerId, (int) $cid));
                    }
                }
            }
            $maxHire = $this->game->getHiringTrackHireCount($playerId);
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
                'i18n' => ['badgers'],
            ]);
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
        $this->game->savePlayerGameDataOnTurnEnd($playerId);
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    public function zombie(int $playerId): string
    {
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
