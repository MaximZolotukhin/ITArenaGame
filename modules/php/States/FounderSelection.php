<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\States\NextPlayer;

/**
 * Состояние выбора карты основателя (только для основного режима)
 */
class FounderSelection extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 20,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose a founder card or finish turn'),
            descriptionMyTurn: clienttranslate('${you} must choose a founder card or finish turn'),
        );
    }

    /**
     * Метод вызывается при входе в состояние выбора карты основателя
     */
    public function onEnteringState(): void
    {
        // Активный игрок должен быть установлен до входа в это состояние
        // (в setupNewGame или в предыдущем состоянии)
        $activePlayerId = $this->game->getActivePlayerId();
        if ($activePlayerId === null) {
            error_log('FounderSelection::onEnteringState - WARNING: No active player set!');
            // Устанавливаем первого игрока как активного
            $this->game->activeNextPlayer();
        }
        
        $activePlayerId = $this->game->getActivePlayerId();
        $founderOptions = $this->game->getFounderOptionsForPlayer((int)$activePlayerId);
        error_log('FounderSelection::onEnteringState - Active player: ' . $activePlayerId . ', Founder options count: ' . count($founderOptions));
    }

    /**
     * Возвращает аргументы для состояния выбора карты основателя
     */
    public function getArgs(): array
    {
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        
        if ($activePlayerIdRaw === null) {
            error_log('FounderSelection::getArgs - WARNING: No active player set! Setting first player as active.');
            $this->game->activeNextPlayer();
            $activePlayerIdRaw = $this->game->getActivePlayerId();
        }
        
        $activePlayerId = is_int($activePlayerIdRaw) ? $activePlayerIdRaw : (int)$activePlayerIdRaw;
        
        $founderOptions = $this->game->getFounderOptionsForPlayer($activePlayerId);
        $hasSelectedFounder = $this->game->globals->get('founder_player_' . $activePlayerId, null) !== null;
        $mustPlaceFounder = $hasSelectedFounder && $this->game->hasUnplacedUniversalFounder($activePlayerId);
        
        error_log('FounderSelection::getArgs - Active player: ' . $activePlayerId . ', Options count: ' . count($founderOptions) . ', Has selected: ' . ($hasSelectedFounder ? 'yes' : 'no'));
        if (empty($founderOptions)) {
            error_log('FounderSelection::getArgs - WARNING: No founder options found for active player ' . $activePlayerId);
            // Проверяем все игроков
            $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
            foreach ($allPlayers as $playerId) {
                $options = $this->game->getFounderOptionsForPlayer((int)$playerId);
                error_log('FounderSelection::getArgs - Player ' . $playerId . ' has ' . count($options) . ' options');
            }
        }
        
        return [
            "activePlayerId" => $activePlayerId,
            "founderOptions" => $founderOptions, // Массив из 3 карт на выбор (пустой, если карта уже выбрана)
            "hasSelectedFounder" => $hasSelectedFounder, // Выбрал ли игрок карту
            "mustPlaceFounder" => $mustPlaceFounder, // Обязательно ли разместить карту основателя
        ];
    }


    /**
     * Действие игрока: выбор карты основателя
     */
    #[PossibleAction]
    public function actSelectFounder(int $cardId, int $activePlayerId)
    {
        $this->game->checkAction('actSelectFounder');
        
        // ВАЖНО: Проверяем, что activePlayerId из параметра совпадает с реальным активным игроком
        $realActivePlayerId = $this->game->getActivePlayerId();
        $realActivePlayerId = $realActivePlayerId !== null ? (int)$realActivePlayerId : null;
        error_log("🔴🔴🔴 actSelectFounder - Parameter activePlayerId: $activePlayerId, Real activePlayerId: " . ($realActivePlayerId ?? 'null'));
        if ($activePlayerId !== $realActivePlayerId) {
            error_log("🔴🔴🔴 actSelectFounder - CRITICAL ERROR: activePlayerId mismatch! Parameter: $activePlayerId, Real: " . ($realActivePlayerId ?? 'null'));
            // Используем реальный активный игрок, а не параметр
            $activePlayerId = $realActivePlayerId ?? $activePlayerId;
        }
        
        // Проверяем, что карта доступна для выбора
        $options = $this->game->getFounderOptionsForPlayer($activePlayerId);
        if (empty($options)) {
            throw new UserException(clienttranslate('У вас нет доступных карт основателей для выбора'));
        }
        
        $availableIds = array_column($options, 'id');
        if (!in_array($cardId, $availableIds, true)) {
            throw new UserException(clienttranslate('Выбранная карта недоступна'));
        }

        // Проверяем, что карта еще не выбрана
        $existingCardId = $this->game->globals->get('founder_player_' . $activePlayerId, null);
        if ($existingCardId !== null) {
            throw new UserException(clienttranslate('Карта основателя уже выбрана'));
        }
        
        // Получаем данные карты ДО выбора, чтобы проверить department
        $founderCard = \Bga\Games\itarenagame\FoundersData::getCard($cardId);
        $founderName = $founderCard['name'] ?? clienttranslate('Неизвестный основатель');
        $founderDepartment = $founderCard['department'] ?? 'universal';
        
        // Выбираем карту для игрока (этот метод уже обрабатывает размещение не-универсальных карт)
        $this->game->selectFounderForPlayer($activePlayerId, $cardId);
        
        // Получаем актуальный отдел из globals (установлен в selectFounderForPlayer)
        $actualDepartment = $this->game->globals->get('founder_department_' . $activePlayerId, null);
        if ($actualDepartment === null) {
            $actualDepartment = $founderDepartment;
        }
        
        // Обновляем department в данных карты для уведомления
        $founderCard['department'] = $actualDepartment;
        
        // Уведомляем о выборе

        $this->notify->all('founderSelected', clienttranslate('${player_name} выбрал карту основателя: ${founder_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $founderName,
            'card_id' => $cardId,
            'founder' => $founderCard,
            'department' => $actualDepartment,
        ]);
        
        // ВАЖНО: Если карта не-универсальная, она уже автоматически размещена в отдел
        // Применяем эффекты сразу после размещения
        if ($actualDepartment !== 'universal') {
            // Отправляем уведомление о размещении (для единообразия с универсальными картами)
            $departmentNames = [
                'sales-department' => clienttranslate('Отдел продаж'),
                'back-office' => clienttranslate('Бэк-офис'),
                'technical-department' => clienttranslate('Техотдел'),
            ];
            $departmentName = $departmentNames[$actualDepartment] ?? $actualDepartment;
            
            $this->notify->all('founderPlaced', clienttranslate('${player_name} разместил основателя в ${department_name}'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'department' => $actualDepartment,
                'department_name' => $departmentName,
                'founder' => $founderCard,
                'i18n' => ['department_name'],
            ]);
            
            // Применяем эффекты карты после размещения
            $this->applyFounderEffectsAfterPlacement($activePlayerId, $cardId);
        }
        
        $this->game->giveExtraTime($activePlayerId);
        
        // После выбора карты НЕ переходим автоматически к следующему игроку
        // Игрок должен нажать кнопку "Завершить ход"
        return null;
    }

    /**
     * Действие игрока: размещение универсальной карты основателя в отдел
     */
    #[PossibleAction]
    public function actPlaceFounder(string $department, int $activePlayerId)
    {
        $this->game->checkAction('actPlaceFounder');
        
        // ВАЖНО: Проверяем, что activePlayerId из параметра совпадает с реальным активным игроком
        $realActivePlayerId = $this->game->getActivePlayerId();
        $realActivePlayerId = $realActivePlayerId !== null ? (int)$realActivePlayerId : null;
        error_log("🔴🔴🔴 actPlaceFounder - Parameter activePlayerId: $activePlayerId, Real activePlayerId: " . ($realActivePlayerId ?? 'null'));
        if ($activePlayerId !== $realActivePlayerId) {
            error_log("🔴🔴🔴 actPlaceFounder - CRITICAL ERROR: activePlayerId mismatch! Parameter: $activePlayerId, Real: " . ($realActivePlayerId ?? 'null'));
            // Используем реальный активный игрок, а не параметр
            $activePlayerId = $realActivePlayerId ?? $activePlayerId;
        }
        
        // Проверяем, что у игрока есть неразмещенная универсальная карта
        if (!$this->game->hasUnplacedUniversalFounder($activePlayerId)) {
            throw new UserException(clienttranslate('У вас нет универсальной карты основателя для размещения'));
        }

        // Размещаем карту в отдел
        $this->game->placeFounder($activePlayerId, $department);

        $founder = $this->game->getFoundersByPlayer()[$activePlayerId] ?? null;
        $departmentNames = [
            'sales-department' => clienttranslate('Отдел продаж'),
            'back-office' => clienttranslate('Бэк-офис'),
            'technical-department' => clienttranslate('Техотдел'),
        ];
        $departmentName = $departmentNames[$department] ?? $department;

        $this->notify->all('founderPlaced', clienttranslate('${player_name} разместил основателя в ${department_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'department' => $department,
            'department_name' => $departmentName,
            'founder' => $founder,
            'i18n' => ['department_name'],
        ]);

        // ВАЖНО: После размещения карты применяем эффекты, если activationStage == 'GameSetup'
        $cardId = $founder['id'] ?? null;
        if ($cardId !== null) {
            $this->applyFounderEffectsAfterPlacement($activePlayerId, $cardId);
        }
        
        $this->game->giveExtraTime($activePlayerId);

        // После размещения карты НЕ переходим автоматически к следующему игроку
        // Игрок должен нажать кнопку "Завершить ход"
        return null;
    }
    
    /**
     * Применяет эффекты карты основателя после размещения в отдел
     * Вызывается для универсальных карт после размещения и для не-универсальных после автоматического размещения
     */
    private function applyFounderEffectsAfterPlacement(int $playerId, int $cardId): void
    {
        // ВАЖНО: Проверяем, что playerId правильный и не используем getActivePlayerId()
        $activePlayerId = $this->game->getActivePlayerId();
        error_log("🔵🔵🔵 applyFounderEffectsAfterPlacement - PlayerId from parameter: $playerId, ActivePlayerId: " . ($activePlayerId ?? 'null'));
        
        if ($playerId !== $activePlayerId) {
            error_log("🔴🔴🔵 WARNING: PlayerId mismatch! Parameter: $playerId, Active: " . ($activePlayerId ?? 'null') . " - Using parameter playerId");
        }
        
        // ВАЖНО: Используем переданный playerId, а не getActivePlayerId()
        $targetPlayerId = $playerId;
        
        $founderCard = \Bga\Games\itarenagame\FoundersData::getCard($cardId);
        if ($founderCard === null) {
            error_log("applyFounderEffectsAfterPlacement - Card not found: $cardId");
            return;
        }
        
        $activationStage = $founderCard['activationStage'] ?? null;
        $effect = $founderCard['effect'] ?? null;
        
        error_log("applyFounderEffectsAfterPlacement - Target Player: $targetPlayerId, Card: $cardId");
        error_log("applyFounderEffectsAfterPlacement - ActivationStage: " . ($activationStage ?? 'null'));
        error_log("applyFounderEffectsAfterPlacement - Effect: " . json_encode($effect));
        
        // Специальная проверка для updateTrack ДО обработки
        if (isset($effect['updateTrack'])) {
            error_log("🔧🔧🔧 applyFounderEffectsAfterPlacement - updateTrack found in effect! Count: " . count($effect['updateTrack']));
            error_log("🔧 applyFounderEffectsAfterPlacement - updateTrack full: " . json_encode($effect['updateTrack']));
            if (is_array($effect['updateTrack'])) {
                foreach ($effect['updateTrack'] as $idx => $track) {
                    error_log("🔧 applyFounderEffectsAfterPlacement - Track #$idx from FoundersData: " . json_encode($track));
                }
            }
        } else {
            error_log("🔴 applyFounderEffectsAfterPlacement - updateTrack NOT FOUND in effect!");
        }
        
        // Применяем эффекты только если activationStage == 'GameSetup'
        if ($activationStage !== 'GameSetup') {
            // Если activationStage != 'GameSetup', эффекты не применяются, но кнопка все равно разблокируется
            error_log("applyFounderEffectsAfterPlacement - ActivationStage mismatch, skipping effects");
            $this->notify->player($targetPlayerId, 'founderEffectsApplied', '', [
                'player_id' => $targetPlayerId,
            ]);
            return;
        }
        
        // Применяем эффект карты
        error_log("applyFounderEffectsAfterPlacement - Calling applyFounderEffect");
        error_log("applyFounderEffectsAfterPlacement - Full card data: " . json_encode($founderCard));
        
        // ВАЖНО: Проверяем, что эффект содержит все необходимые типы
        if (empty($effect) || !is_array($effect)) {
            error_log("applyFounderEffectsAfterPlacement - ERROR: Effect is empty or not an array!");
        } else {
            error_log("🔍🔍🔍 applyFounderEffectsAfterPlacement - Effect keys: " . implode(', ', array_keys($effect)));
            error_log("🔍🔍🔍 applyFounderEffectsAfterPlacement - Full effect: " . json_encode($effect));
            $expectedEffects = ['badger', 'card', 'task', 'move_task', 'updateTrack', 'incomeTrack'];
            foreach ($expectedEffects as $expectedType) {
                if (!isset($effect[$expectedType])) {
                    error_log("applyFounderEffectsAfterPlacement - Effect '$expectedType' is missing from card data (this is OK if card doesn't have this effect)");
                } else {
                    error_log("✅✅✅ applyFounderEffectsAfterPlacement - Found effect '$expectedType': " . json_encode($effect[$expectedType]));
                }
            }
            
            // Специальная проверка для updateTrack
            if (isset($effect['updateTrack'])) {
                error_log("🔵🔵🔵 applyFounderEffectsAfterPlacement - updateTrack effect FOUND in card data!");
                error_log("🔵 updateTrack value: " . json_encode($effect['updateTrack']));
            }
        }
        
        // ВАЖНО: Используем targetPlayerId вместо playerId для явности
        $appliedEffects = $this->game->applyFounderEffect($targetPlayerId, $cardId, 'GameSetup');
        error_log("applyFounderEffectsAfterPlacement - Applied effects count: " . count($appliedEffects));
        error_log("applyFounderEffectsAfterPlacement - Applied effects: " . json_encode($appliedEffects));
        
        // ВРЕМЕННОЕ УВЕДОМЛЕНИЕ ДЛЯ ОТЛАДКИ
        $updateTrackValue = $effect['updateTrack'] ?? null;
        $updateTrackCount = is_array($updateTrackValue) ? count($updateTrackValue) : 0;
        
        // Проверяем, есть ли updateTrack в примененных эффектах
        $updateTrackInApplied = null;
        foreach ($appliedEffects as $appliedEffect) {
            if (($appliedEffect['type'] ?? '') === 'updateTrack') {
                $updateTrackInApplied = $appliedEffect;
                break;
            }
        }
        
        $tracksInApplied = $updateTrackInApplied['tracks'] ?? [];
        $tracksInAppliedCount = is_array($tracksInApplied) ? count($tracksInApplied) : 0;
        
        $this->notify->player($targetPlayerId, 'debugUpdateTrack', '', [
            'player_id' => $targetPlayerId,
            'card_id' => $cardId,
            'card_name' => $founderCard['name'] ?? 'unknown',
            'has_updateTrack' => isset($effect['updateTrack']) ? 'YES' : 'NO',
            'updateTrack_value' => $updateTrackValue,
            'updateTrack_count' => $updateTrackCount,
            'applied_effects_count' => count($appliedEffects),
            'applied_effects' => $appliedEffects,
            'updateTrack_in_applied' => $updateTrackInApplied,
            'tracks_in_applied_count' => $tracksInAppliedCount,
            'tracks_in_applied' => $tracksInApplied,
        ]);
        
        // Если были применены эффекты, отправляем уведомления
        // ВАЖНО: Обрабатываем эффекты в строгом порядке: badger -> card -> task
        if (!empty($appliedEffects)) {
            // Сортируем эффекты по порядку обработки для четкой последовательности
            $effectOrder = ['badger' => 1, 'card' => 2, 'task' => 3, 'move_task' => 4, 'incomeTrack' => 5, 'updateTrack' => 6];
            usort($appliedEffects, function($a, $b) use ($effectOrder) {
                $orderA = $effectOrder[$a['type']] ?? 999;
                $orderB = $effectOrder[$b['type']] ?? 999;
                return $orderA <=> $orderB;
            });
            
            error_log("🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷🔷");
            error_log("FounderSelection - Total effects to process: " . count($appliedEffects));
            error_log("FounderSelection - Effects array: " . json_encode($appliedEffects));
            
            // Проверяем, есть ли updateTrack в эффектах
            $hasUpdateTrack = false;
            foreach ($appliedEffects as $effect) {
                if (($effect['type'] ?? '') === 'updateTrack') {
                    $hasUpdateTrack = true;
                    error_log('🔵🔵🔵 FOUND updateTrack in appliedEffects BEFORE loop!');
                    break;
                }
            }
            
            $hasMoveTask = false;
            foreach ($appliedEffects as $index => $effect) {
                $effectType = $effect['type'] ?? 'unknown';
                error_log("🔷🔷🔷 FounderSelection - Effect #$index: type=$effectType");
                if ($effectType === 'move_task') {
                    $hasMoveTask = true;
                    error_log('✅✅✅ FounderSelection - Found move_task effect in appliedEffects!');
                }
                if ($effectType === 'updateTrack') {
                    error_log('🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵');
                    error_log('🔵🔵🔵 FounderSelection - Found updateTrack effect in appliedEffects!');
                    error_log('🔵 Effect full data: ' . json_encode($effect));
                }
                error_log("FounderSelection - Processing notification for effect type: $effectType, effect data: " . json_encode($effect));
                
                // Эффект 1: BADGER - изменение баджерсов
                if ($effectType === 'badger' && isset($effect['amount']) && $effect['amount'] !== 0) {
                    // Получаем актуальное состояние банка
                    $badgersSupply = $this->game->getBadgersSupply();
                    error_log('FounderSelection - Sending badgersChanged notification');
                    
                    // Отправляем уведомление об изменении баджерсов (включая данные банка)
                    $this->notify->all('badgersChanged', clienttranslate('${player_name} ${action_text} ${amount}Б благодаря эффекту карты «${founder_name}»'), [
                        'player_id' => $targetPlayerId,
                        'player_name' => $this->game->getPlayerNameById($targetPlayerId),
                        'action_text' => $effect['amount'] > 0 ? clienttranslate('получает') : clienttranslate('теряет'),
                        'amount' => abs($effect['amount']),
                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                        'oldValue' => $effect['oldValue'],
                        'newValue' => $effect['newValue'],
                        'badgersSupply' => $badgersSupply,
                        'i18n' => ['action_text'],
                    ]);
                }
                // Эффект 2: CARD - выдача карт специалистов на руку (для выбора)
                elseif ($effectType === 'card' && isset($effect['amount']) && $effect['amount'] > 0) {
                    $cardNames = implode(', ', $effect['cardNames'] ?? []);
                    error_log('FounderSelection - Sending specialistsDealtToHand notification');
                    
                    // Отправляем уведомление о выдаче карт специалистов на руку
                    $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} получает ${amount} карт специалистов благодаря эффекту карты «${founder_name}»'), [
                        'player_id' => $targetPlayerId,
                        'player_name' => $this->game->getPlayerNameById($targetPlayerId),
                        'amount' => $effect['amount'],
                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                        'cardIds' => $effect['cardIds'] ?? [],
                        'cardNames' => $cardNames,
                    ]);
                    
                    // Обновляем данные игрока для клиента
                    $this->game->notify->player($targetPlayerId, 'specialistsUpdated', '', [
                        'player_id' => $targetPlayerId,
                    ]);
                    
                    error_log('FounderSelection - Player ' . $targetPlayerId . ' received ' . $effect['amount'] . ' specialist cards (card): ' . $cardNames);
                }
                // Эффект 3: INCOME_TRACK - изменение трека дохода
                elseif ($effectType === 'incomeTrack' && isset($effect['amount']) && $effect['amount'] !== 0) {
                    error_log('FounderSelection - Sending incomeTrackChanged notification');
                    
                    // Отправляем уведомление об изменении трека дохода
                    $this->notify->all('incomeTrackChanged', clienttranslate('${player_name} ${action_text} трек дохода на ${amount} благодаря эффекту карты «${founder_name}»'), [
                        'player_id' => $targetPlayerId,
                        'player_name' => $this->game->getPlayerNameById($targetPlayerId),
                        'action_text' => $effect['amount'] > 0 ? clienttranslate('увеличивает') : clienttranslate('уменьшает'),
                        'amount' => abs($effect['amount']),
                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                        'oldValue' => $effect['oldValue'],
                        'newValue' => $effect['newValue'],
                        'i18n' => ['action_text'],
                    ]);
                    
                    error_log('FounderSelection - Player ' . $targetPlayerId . ' income track changed from ' . $effect['oldValue'] . ' to ' . $effect['newValue']);
                }
                // Эффект 4: UPDATE_TRACK - обновление нескольких треков
                elseif ($effectType === 'updateTrack') {
                    error_log('🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵🔵');
                    error_log('🔵🔵🔵 FounderSelection - Processing updateTrack effect');
                    error_log('🔵 FounderSelection - Target Player: ' . $targetPlayerId);
                    error_log('🔵 FounderSelection - Effect type: ' . $effectType);
                    error_log('🔵 FounderSelection - Effect data: ' . json_encode($effect));
                    error_log('🔵 FounderSelection - Effect keys: ' . implode(', ', array_keys($effect)));
                    error_log('🔵 FounderSelection - isset(effect[tracks]): ' . (isset($effect['tracks']) ? 'YES' : 'NO'));
                    if (isset($effect['tracks'])) {
                        error_log('🔵 FounderSelection - effect[tracks] type: ' . gettype($effect['tracks']));
                        error_log('🔵 FounderSelection - effect[tracks] is_array: ' . (is_array($effect['tracks']) ? 'YES' : 'NO'));
                    }
                    
                    // Проверяем наличие массива tracks
                    if (isset($effect['tracks']) && is_array($effect['tracks'])) {
                        error_log('🔵 FounderSelection - tracks array found with ' . count($effect['tracks']) . ' items');
                        error_log('FounderSelection - Found ' . count($effect['tracks']) . ' tracks to update');
                        error_log('🔵🔵🔵 FounderSelection - Full tracks array: ' . json_encode($effect['tracks']));
                        
                        // Обрабатываем каждый обновленный трек
                        foreach ($effect['tracks'] as $index => $trackUpdate) {
                            error_log("🔵 FounderSelection - Track #$index: " . json_encode($trackUpdate));
                            error_log("🔵 FounderSelection - Track #$index keys: " . implode(', ', array_keys($trackUpdate)));
                            $trackId = $trackUpdate['trackId'] ?? '';
                            $trackName = $trackUpdate['trackName'] ?? $trackId;
                            $column = $trackUpdate['column'] ?? null;
                            
                            error_log('🔵 FounderSelection - Processing track: ' . $trackId . ', amount: ' . ($trackUpdate['amount'] ?? 'N/A') . ', column: ' . ($column ?? 'NULL'));
                            error_log('🔵 FounderSelection - trackId === "income-track": ' . ($trackId === 'income-track' ? 'YES' : 'NO'));
                            error_log('🔵 FounderSelection - trackId === "player-department-technical-development": ' . ($trackId === 'player-department-technical-development' ? 'YES' : 'NO'));
                            error_log('🔵 FounderSelection - column === "any": ' . ($column === 'any' ? 'YES' : 'NO'));
                            
                            // Для income-track отправляем специальное уведомление
                            if ($trackId === 'income-track') {
                                error_log('🔵🔵🔵 FounderSelection - MATCH! income-track found, sending notification');
                                error_log('FounderSelection - Sending incomeTrackChanged notification for income-track');
                                error_log('FounderSelection - trackUpdate data: ' . json_encode($trackUpdate));
                                
                                $amount = is_numeric($trackUpdate['amount']) ? (int)$trackUpdate['amount'] : 0;
                                $oldValue = is_numeric($trackUpdate['oldValue']) ? (int)$trackUpdate['oldValue'] : 0;
                                $newValue = is_numeric($trackUpdate['newValue']) ? (int)$trackUpdate['newValue'] : 0;
                                
                                error_log('FounderSelection - Parsed values: amount=' . $amount . ', oldValue=' . $oldValue . ', newValue=' . $newValue);
                                
                                // ВАЖНО: Проверяем, что значения правильные
                                if ($oldValue === 0 && $newValue === 0) {
                                    error_log('🔴🔴🔴 FounderSelection - ERROR: oldValue and newValue are both 0! This should not happen!');
                                    error_log('🔴 trackUpdate: ' . json_encode($trackUpdate));
                                }
                                
                                $notificationData = [
                                    'player_id' => $targetPlayerId,
                                    'player_name' => $this->game->getPlayerNameById($targetPlayerId),
                                    'action_text' => $amount > 0 ? clienttranslate('увеличивает') : clienttranslate('уменьшает'),
                                    'amount' => abs($amount),
                                    'founder_name' => $effect['founderName'] ?? 'Основатель',
                                    'oldValue' => $oldValue,
                                    'newValue' => $newValue,
                                    'i18n' => ['action_text'],
                                ];
                                
                                error_log('FounderSelection - Sending notification with data: ' . json_encode($notificationData));
                                
                                $this->notify->all('incomeTrackChanged', clienttranslate('${player_name} ${action_text} трек дохода на ${amount} благодаря эффекту карты «${founder_name}»'), $notificationData);
                                
                                error_log('✅✅✅ FounderSelection - incomeTrackChanged notification SENT for player ' . $targetPlayerId . ' from ' . $oldValue . ' to ' . $newValue);
                            }
                            // Для визуальных треков отделов отправляем уведомление для обновления на клиенте
                            elseif (str_starts_with($trackId, 'player-department-')) {
                                error_log('🔵 FounderSelection - Processing visual track: ' . $trackId);
                                error_log('🔵 FounderSelection - trackUpdate keys: ' . implode(', ', array_keys($trackUpdate)));
                                error_log('🔵 FounderSelection - trackUpdate full: ' . json_encode($trackUpdate));
                                
                                $amount = is_numeric($trackUpdate['amount']) ? (int)$trackUpdate['amount'] : 0;
                                $column = $trackUpdate['column'] ?? null;
                                
                                error_log('🔵 FounderSelection - Extracted: amount=' . $amount . ', column=' . ($column ?? 'NULL'));
                                error_log('🔵 FounderSelection - trackId check: ' . ($trackId === 'player-department-technical-development' ? 'MATCH' : 'NO MATCH'));
                                error_log('🔵 FounderSelection - column check: ' . ($column === 'any' ? 'MATCH' : 'NO MATCH (' . ($column ?? 'NULL') . ')'));
                                
                                // Проверяем, требуется ли выбор колонки (для техотдела)
                                if ($trackId === 'player-department-technical-development' && $column === 'any') {
                                    error_log('🔧🔧🔧 FounderSelection - Technical development requires column selection!');
                                    
                                    // Сохраняем данные о перемещениях в globals
                                    $globalsKey = 'pending_technical_development_moves_' . $targetPlayerId;
                                    $pendingMovesData = [
                                        'move_count' => $amount, // Количество очков для распределения (2)
                                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                                        'founder_id' => $effect['founderId'] ?? null,
                                    ];
                                    $this->game->globals->set($globalsKey, json_encode($pendingMovesData));
                                    error_log('✅ FounderSelection - Saved pending_technical_development_moves to globals: ' . json_encode($pendingMovesData));
                                    
                                    // Отправляем уведомление для активации режима выбора колонок
                                    $this->notify->player($targetPlayerId, 'technicalDevelopmentMovesRequired', '', [
                                        'player_id' => $targetPlayerId,
                                        'move_count' => $amount, // Количество очков для распределения (2)
                                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                                    ]);
                                    
                                    error_log('✅✅✅ FounderSelection - technicalDevelopmentMovesRequired notification SENT for player ' . $targetPlayerId . ' with ' . $amount . ' points to distribute');
                                } else {
                                    // Обычный визуальный трек без выбора
                                    $this->notify->all('visualTrackChanged', clienttranslate('${player_name} обновляет ${track_name} на ${amount} благодаря эффекту карты «${founder_name}»'), [
                                        'player_id' => $targetPlayerId,
                                        'player_name' => $this->game->getPlayerNameById($targetPlayerId),
                                        'track_id' => $trackId,
                                        'track_name' => $trackName,
                                        'amount' => $amount,
                                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                                        'oldValue' => $trackUpdate['oldValue'] ?? 0,
                                        'newValue' => $trackUpdate['newValue'] ?? $amount,
                                    ]);
                                    
                                    error_log('✅ FounderSelection - visualTrackChanged notification SENT for track: ' . $trackId . ', amount: ' . $amount);
                                }
                            }
                        }
                    } else {
                        error_log('🔴🔴🔴 FounderSelection - WARNING: updateTrack effect does not have tracks array!');
                        error_log('🔴 Effect structure: ' . json_encode($effect));
                        error_log('🔴 Effect keys: ' . implode(', ', array_keys($effect)));
                        error_log('🔴 isset(tracks): ' . (isset($effect['tracks']) ? 'yes' : 'no'));
                        if (isset($effect['tracks'])) {
                            error_log('🔴 tracks type: ' . gettype($effect['tracks']));
                        }
                    }
                }
                // Эффект 5: TASK - выдача задач (task tokens)
                elseif ($effectType === 'task' && isset($effect['amount']) && $effect['amount'] > 0) {
                    // Отправляем уведомление о необходимости выбора задач
                    $this->notify->player($targetPlayerId, 'taskSelectionRequired', '', [
                        'player_id' => $targetPlayerId,
                        'amount' => $effect['amount'],
                        'founder_name' => $founderCard['name'] ?? '',
                    ]);
                    error_log('FounderSelection - Effect "task": Player ' . $targetPlayerId . ' must select ' . $effect['amount'] . ' tasks');
                }
                // Эффект 6: MOVE_TASK - перемещение жетонов задач
                elseif ($effectType === 'move_task') {
                    error_log('🎯🎯🎯 FounderSelection - Processing move_task effect: ' . json_encode($effect));
                    error_log('🎯🎯🎯 FounderSelection - move_task effect keys: ' . implode(', ', array_keys($effect)));
                    $moveCount = $effect['move_count'] ?? 0;
                    $moveColor = $effect['move_color'] ?? 'any';
                    
                    error_log('🎯🎯🎯 FounderSelection - move_task parsed: moveCount=' . $moveCount . ', moveColor=' . $moveColor);
                    
                    if ($moveCount > 0) {
                        // Отправляем уведомление о необходимости перемещения задач
                        error_log('🎯🎯🎯 FounderSelection - Sending taskMovesRequired notification to player ' . $targetPlayerId);
                        $this->notify->player($targetPlayerId, 'taskMovesRequired', '', [
                            'player_id' => $targetPlayerId,
                            'move_count' => $moveCount,
                            'move_color' => $moveColor,
                            'founder_name' => $founderCard['name'] ?? '',
                        ]);
                        error_log('✅✅✅ FounderSelection - Effect "move_task": Player ' . $targetPlayerId . ' must move tasks ' . $moveCount . ' times (color: ' . $moveColor . ') - NOTIFICATION SENT');
                    } else {
                        error_log('❌❌❌ FounderSelection - Effect "move_task": move_count is 0 or not set, skipping. moveCount=' . $moveCount);
                    }
                }
            }
            
            if (!$hasMoveTask) {
                error_log('❌❌❌ FounderSelection - WARNING: No move_task effect found in appliedEffects!');
            }
            
            // После применения всех эффектов отправляем уведомление о готовности к завершению хода
            $this->notify->player($targetPlayerId, 'founderEffectsApplied', '', [
                'player_id' => $targetPlayerId,
            ]);
        } else {
            // Если эффектов нет, все равно разблокируем кнопку
            $this->notify->player($targetPlayerId, 'founderEffectsApplied', '', [
                'player_id' => $targetPlayerId,
            ]);
        }
    }

    /**
     * Действие игрока: завершение хода
     */
    #[PossibleAction]
    public function actFinishTurn(int $activePlayerId)
    {
        // Проверяем, выбрал ли игрок карту основателя
        $hasSelectedFounder = $this->game->globals->get('founder_player_' . $activePlayerId, null) !== null;
        if (!$hasSelectedFounder) {
            throw new UserException(clienttranslate('Вы должны выбрать карту основателя перед завершением хода'));
        }
        
        // Проверяем, есть ли у игрока неразмещенная универсальная карта основателя
        if ($this->game->hasUnplacedUniversalFounder($activePlayerId)) {
            throw new UserException(clienttranslate('Вы должны разместить карту основателя в один из отделов перед завершением хода'));
        }
        
        // Проверяем, есть ли ожидающий выбор задач
        $pendingSelectionJson = $this->game->globals->get('pending_task_selection_' . $activePlayerId, null);
        if ($pendingSelectionJson !== null) {
            $pendingSelection = json_decode($pendingSelectionJson, true);
            $amount = $pendingSelection['amount'] ?? 0;
            throw new UserException(clienttranslate('Вы должны выбрать ${amount} задач перед завершением хода', [
                'amount' => $amount
            ]));
        }

        // ВАЖНО: Сохраняем все данные игрока в таблицу player_game_data перед завершением хода
        $this->game->savePlayerGameDataOnTurnEnd($activePlayerId);
        
        $this->notify->all('turnFinished', clienttranslate('${player_name} завершает ход'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
        ]);

        $this->game->giveExtraTime($activePlayerId);

        // Переходим к следующему игроку
        return NextPlayer::class;
    }

    /**
     * Подтверждение выбора задач от эффекта карты основателя
     * @param int $activePlayerId ID активного игрока
     * @param string $selectedTasksJson JSON строка с массивом выбранных задач [{"color": "cyan", "quantity": 1}, ...]
     */
    #[PossibleAction]
    public function actConfirmTaskSelection(int $activePlayerId, string $selectedTasksJson)
    {
        $this->game->checkAction('actConfirmTaskSelection');
        
        // Проверяем, что есть ожидающий выбор задач
        $pendingSelectionJson = $this->game->globals->get('pending_task_selection_' . $activePlayerId, null);
        if ($pendingSelectionJson === null) {
            throw new UserException(clienttranslate('Нет ожидающего выбора задач'));
        }
        
        $pendingSelection = json_decode($pendingSelectionJson, true);
        if (!is_array($pendingSelection) || !isset($pendingSelection['amount'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающего выбора задач'));
        }
        
        $requiredAmount = (int)$pendingSelection['amount'];
        
        // Декодируем JSON строку в массив
        $selectedTasks = json_decode($selectedTasksJson, true);
        if (!is_array($selectedTasks)) {
            throw new UserException(clienttranslate('Неверный формат данных выбранных задач'));
        }
        
        // Проверяем, что выбрано правильное количество задач
        $totalSelected = 0;
        foreach ($selectedTasks as $task) {
            if (!is_array($task)) {
                continue;
            }
            $quantity = (int)($task['quantity'] ?? 0);
            if ($quantity < 0) {
                throw new UserException(clienttranslate('Количество задач не может быть отрицательным'));
            }
            $totalSelected += $quantity;
        }
        
        if ($totalSelected !== $requiredAmount) {
            throw new UserException(clienttranslate('Вы должны выбрать ровно ${amount} задач', [
                'amount' => $requiredAmount
            ]));
        }
        
        // Добавляем задачи в backlog
        $addedTokens = $this->game->addTaskTokens($activePlayerId, $selectedTasks, 'backlog');
        
        // Удаляем информацию о ожидающем выборе
        $this->game->globals->set('pending_task_selection_' . $activePlayerId, null);
        
        // Проверяем, есть ли ожидающее перемещение задач (эффект move_task)
        // Если есть, передаем данные в уведомлении, чтобы клиент мог активировать режим перемещения
        $globalsKey = 'pending_task_moves_' . $activePlayerId;
        $pendingMovesJson = $this->game->globals->get($globalsKey, null);
        error_log("🔍🔍🔍 actConfirmTaskSelection - Checking pending_task_moves for player $activePlayerId");
        error_log("🔍🔍🔍 actConfirmTaskSelection - Globals key: $globalsKey");
        error_log("🔍🔍🔍 actConfirmTaskSelection - Value: " . ($pendingMovesJson ?? 'NULL'));
        
        // Проверяем все глобальные переменные с pending_task_moves для отладки
        $allGlobals = $this->game->globals->getAll();
        $foundAny = false;
        foreach ($allGlobals as $key => $value) {
            if (strpos($key, 'pending_task_moves') !== false) {
                error_log("🔍 actConfirmTaskSelection - Found global: $key = " . ($value ?? 'NULL'));
                $foundAny = true;
            }
        }
        if (!$foundAny) {
            error_log("❌❌❌ actConfirmTaskSelection - NO globals with 'pending_task_moves' found at all!");
        }
        
        $pendingTaskMoves = null;
        if ($pendingMovesJson !== null) {
            $pendingTaskMoves = json_decode($pendingMovesJson, true);
            if (!is_array($pendingTaskMoves)) {
                error_log("❌❌❌ actConfirmTaskSelection - ERROR: pending_task_moves is not an array after decode!");
                $pendingTaskMoves = null;
            } else {
                error_log("✅✅✅ actConfirmTaskSelection - Found pending_task_moves for player $activePlayerId: " . $pendingMovesJson);
                error_log("✅✅✅ actConfirmTaskSelection - Decoded pending_task_moves: " . json_encode($pendingTaskMoves));
            }
        } else {
            error_log("❌❌❌ actConfirmTaskSelection - WARNING: No pending_task_moves found for player $activePlayerId!");
            error_log("❌❌❌ actConfirmTaskSelection - This means move_task effect was NOT saved to globals or was deleted!");
        }
        
        // Уведомляем всех игроков о выборе задач
        $founderName = $pendingSelection['founder_name'] ?? '';
        $notifArgs = [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'amount' => $totalSelected,
            'founder_name' => $founderName,
            'selected_tasks' => $selectedTasks,
            'added_tokens' => $addedTokens,
            'i18n' => ['founder_name'],
        ];
        
        // Если есть ожидающее перемещение, добавляем данные в уведомление
        if ($pendingTaskMoves !== null && is_array($pendingTaskMoves)) {
            $notifArgs['pending_task_moves'] = $pendingTaskMoves;
            error_log("actConfirmTaskSelection - Added pending_task_moves to notification: " . json_encode($pendingTaskMoves));
            error_log("actConfirmTaskSelection - Full notifArgs keys: " . implode(', ', array_keys($notifArgs)));
        } else {
            error_log("actConfirmTaskSelection - WARNING: pending_task_moves is NULL or not an array, NOT adding to notification!");
            error_log("actConfirmTaskSelection - pendingTaskMoves value: " . var_export($pendingTaskMoves, true));
            error_log("actConfirmTaskSelection - pendingMovesJson value: " . var_export($pendingMovesJson, true));
        }
        
        error_log("actConfirmTaskSelection - Sending tasksSelected notification with args: " . json_encode($notifArgs));
        $this->notify->all('tasksSelected', clienttranslate('${player_name} выбрал ${amount} задач от эффекта ${founder_name}'), $notifArgs);
        
        error_log("actConfirmTaskSelection - Player $activePlayerId selected $totalSelected tasks: " . json_encode($selectedTasks));
    }

    /**
     * Подтверждение перемещений задач от эффекта карты основателя
     * @param int $activePlayerId ID активного игрока
     * @param string $movesJson JSON строка с массивом перемещений [{"tokenId": 1, "fromLocation": "backlog", "toLocation": "in-progress", "blocks": 1}, ...]
     */
    #[PossibleAction]
    public function actConfirmTaskMoves(int $activePlayerId, string $movesJson)
    {
        $this->game->checkAction('actConfirmTaskMoves');
        
        error_log("🔍🔍🔍 actConfirmTaskMoves - START: activePlayerId=$activePlayerId");
        error_log("🔍🔍🔍 actConfirmTaskMoves - movesJson length: " . strlen($movesJson));
        
        // Проверяем, что есть ожидающие перемещения
        $globalsKey = 'pending_task_moves_' . $activePlayerId;
        error_log("🔍🔍🔍 actConfirmTaskMoves - Looking for globals key: $globalsKey");
        
        $pendingMovesJson = $this->game->globals->get($globalsKey, null);
        error_log("🔍🔍🔍 actConfirmTaskMoves - pendingMovesJson: " . ($pendingMovesJson ?? 'NULL'));
        
        // Проверяем все глобальные переменные с pending_task_moves
        $allGlobals = $this->game->globals->getAll();
        $foundAny = false;
        foreach ($allGlobals as $key => $value) {
            if (strpos($key, 'pending_task_moves') !== false) {
                error_log("🔍 actConfirmTaskMoves - Found global: $key = " . ($value ?? 'NULL'));
                $foundAny = true;
            }
        }
        if (!$foundAny) {
            error_log("❌❌❌ actConfirmTaskMoves - NO globals with 'pending_task_moves' found at all!");
        }
        
        // ВАЖНО: Если данных нет в globals, но есть перемещения в запросе, создаем их
        // Это может произойти, если клиент создал данные локально через fallback
        if ($pendingMovesJson === null) {
            error_log("⚠️⚠️⚠️ actConfirmTaskMoves - WARNING: No pending_task_moves in globals, but moves were sent!");
            error_log("⚠️⚠️⚠️ actConfirmTaskMoves - This means data was created on client side (fallback)");
            error_log("⚠️⚠️⚠️ actConfirmTaskMoves - Creating pending_task_moves in globals from moves data");
            
            // Декодируем перемещения, чтобы определить количество ходов
            $moves = json_decode($movesJson, true);
            if (is_array($moves) && count($moves) > 0) {
                // Подсчитываем общее количество блоков
                $totalBlocks = 0;
                foreach ($moves as $move) {
                    if (is_array($move) && isset($move['blocks'])) {
                        $totalBlocks += (int)$move['blocks'];
                    }
                }
                
                if ($totalBlocks > 0) {
                    // Создаем данные в globals на основе перемещений
                    $pendingMovesData = [
                        'move_count' => $totalBlocks,
                        'move_color' => 'any',
                        'used_moves' => 0,
                        'founder_id' => 0,
                        'founder_name' => 'Дмитрий', // Предполагаем, что это эффект от карты Дмитрий
                    ];
                    $pendingMovesJson = json_encode($pendingMovesData);
                    $this->game->globals->set($globalsKey, $pendingMovesJson);
                    error_log("✅✅✅ actConfirmTaskMoves - Created pending_task_moves in globals: $pendingMovesJson");
                } else {
                    error_log("❌❌❌ actConfirmTaskMoves - ERROR: No valid moves found in movesJson");
                    throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
                }
            } else {
                error_log("❌❌❌ actConfirmTaskMoves - ERROR: Invalid movesJson format");
                throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
            }
        }
        
        $pendingMoves = json_decode($pendingMovesJson, true);
        if (!is_array($pendingMoves) || !isset($pendingMoves['move_count'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающих перемещений'));
        }
        
        $requiredMoves = (int)$pendingMoves['move_count'];
        $moveColor = $pendingMoves['move_color'] ?? 'any';
        if ($moveColor !== 'any' && strtolower($moveColor) === 'cayn') {
            $moveColor = 'cyan';
        }
        
        // Декодируем JSON строку перемещений
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }
        
        // Подсчитываем количество блоков и проверяем цвет жетонов при move_color !== 'any'
        $totalBlocks = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['tokenId']) || !isset($move['toLocation'])) {
                continue;
            }
            if ($moveColor !== 'any') {
                $tokenId = (int)($move['tokenId'] ?? 0);
                $tokenColor = $this->game->getTaskTokenColor($activePlayerId, $tokenId);
                if ($tokenColor === null) {
                    throw new UserException(clienttranslate('Жетон не найден'));
                }
                $normalized = $tokenColor === 'cayn' ? 'cyan' : $tokenColor;
                $expected = $moveColor === 'cayn' ? 'cyan' : strtolower($moveColor);
                if ($normalized !== $expected) {
                    throw new UserException(clienttranslate('Можно перемещать только жетоны указанного цвета'));
                }
            }
            $blocks = (int)($move['blocks'] ?? 0);
            $totalBlocks += $blocks;
        }
        
        // Учитываем цвет: для Леонида и др. — только блоки жетонов нужного цвета
        $maxBlocksAvailable = $this->game->getMaxTaskMoveBlocksForPlayer($activePlayerId, $moveColor);
        $allRequiredUsed = ($totalBlocks === $requiredMoves);
        $noMoreAvailable = ($maxBlocksAvailable === 0 && $totalBlocks === 0) || ($maxBlocksAvailable <= $totalBlocks && $totalBlocks <= $requiredMoves);
        if (!$allRequiredUsed && !$noMoreAvailable) {
            throw new UserException(clienttranslate('Вы должны использовать ровно ${amount} ходов или переместить все доступные задачи на треке', [
                'amount' => $requiredMoves
            ]));
        }
        
        // Выполняем перемещения
        $movedTokens = [];
        foreach ($moves as $move) {
            $tokenId = (int)($move['tokenId'] ?? 0);
            $toLocation = $move['toLocation'] ?? '';
            
            if ($tokenId > 0 && !empty($toLocation)) {
                $success = $this->game->updateTaskTokenLocation($tokenId, $toLocation, null);
                if ($success) {
                    $movedTokens[] = $tokenId;
                }
            }
        }
        
        // Удаляем информацию о ожидающих перемещениях
        $this->game->globals->set('pending_task_moves_' . $activePlayerId, null);
        
        // Уведомляем всех игроков о перемещениях
        $founderName = $pendingMoves['founder_name'] ?? '';
        $this->notify->all('taskMovesCompleted', clienttranslate('${player_name} переместил задачи от эффекта ${founder_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $founderName,
            'moves' => $moves,
            'moved_tokens' => $movedTokens,
            'i18n' => ['founder_name'],
        ]);
        
        error_log("actConfirmTaskMoves - Player $activePlayerId moved " . count($movedTokens) . " task tokens: " . json_encode($moves));
    }

    /**
     * Подтверждение перемещений жетонов техотдела
     * @param int $activePlayerId ID активного игрока
     * @param string $movesJson JSON строка с массивом перемещений [{"column": 1, "fromRowIndex": 1, "toRowIndex": 3, "amount": 2}, ...]
     */
    #[PossibleAction]
    public function actConfirmTechnicalDevelopmentMoves(int $activePlayerId, string $movesJson)
    {
        $this->game->checkAction('actConfirmTechnicalDevelopmentMoves');
        
        error_log("🔧🔧🔧 actConfirmTechnicalDevelopmentMoves - START: activePlayerId=$activePlayerId");
        error_log("🔧 actConfirmTechnicalDevelopmentMoves - movesJson: $movesJson");
        
        // Проверяем, что есть ожидающие перемещения
        $globalsKey = 'pending_technical_development_moves_' . $activePlayerId;
        $pendingMovesJson = $this->game->globals->get($globalsKey, null);
        
        if ($pendingMovesJson === null) {
            throw new UserException(clienttranslate('Нет ожидающих перемещений техотдела'));
        }
        
        $pendingMoves = json_decode($pendingMovesJson, true);
        if (!is_array($pendingMoves) || !isset($pendingMoves['move_count'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающих перемещений'));
        }
        
        $requiredMoves = (int)$pendingMoves['move_count'];
        
        // Декодируем JSON строку перемещений
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }
        
        // Проверяем, что использовано правильное количество очков
        $totalAmount = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['column']) || !isset($move['amount'])) {
                throw new UserException(clienttranslate('Неверный формат перемещения'));
            }
            $totalAmount += (int)$move['amount'];
        }
        
        if ($totalAmount !== $requiredMoves) {
            throw new UserException(clienttranslate('Использовано неверное количество очков: ${used} из ${required}', [
                'used' => $totalAmount,
                'required' => $requiredMoves
            ]));
        }
        
        // Проверяем, что колонки валидны (1-4)
        foreach ($moves as $move) {
            $column = (int)$move['column'];
            if ($column < 1 || $column > 4) {
                throw new UserException(clienttranslate('Неверный номер колонки: ${column}', [
                    'column' => $column
                ]));
            }
        }
        
        // Сохраняем перемещения в БД (таблица player_game_data)
        foreach ($moves as $move) {
            $column = (int)$move['column'];
            $amount = (int)$move['amount'];
            $this->game->incTechDevColumn($activePlayerId, $column, $amount);
            error_log("🔧 actConfirmTechnicalDevelopmentMoves - Updated techDevCol$column for player $activePlayerId by $amount");
        }
        
        // Очищаем ожидающие перемещения из globals
        $this->game->globals->set($globalsKey, null);
        
        // Отправляем уведомление о завершении перемещений
        $this->notify->all('technicalDevelopmentMovesCompleted', clienttranslate('${player_name} улучшил техотдел благодаря эффекту карты «${founder_name}»'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $pendingMoves['founder_name'] ?? 'Основатель',
            'moves' => $moves,
            'i18n' => ['founder_name'],
        ]);
        
        error_log("🔧 actConfirmTechnicalDevelopmentMoves - Player $activePlayerId moved technical development tokens: " . json_encode($moves));
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: choose a random founder card).
     * 
     * See more about Zombie Mode: https://en.doc.boardgamearena.com/Zombie_Mode
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, 
     * but use the $playerId passed in parameter and $this->game->getPlayerNameById($playerId) instead.
     */
    function zombie(int $playerId)
    {
        // Для зомби-игрока просто переходим к следующему игроку
            return NextPlayer::class;
    }
}

