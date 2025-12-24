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
        $founderCard = \Bga\Games\itarenagame\FoundersData::getCard($cardId);
        if ($founderCard === null) {
            error_log("applyFounderEffectsAfterPlacement - Card not found: $cardId");
            return;
        }
        
        $activationStage = $founderCard['activationStage'] ?? null;
        $effect = $founderCard['effect'] ?? null;
        
        error_log("applyFounderEffectsAfterPlacement - Player: $playerId, Card: $cardId");
        error_log("applyFounderEffectsAfterPlacement - ActivationStage: " . ($activationStage ?? 'null'));
        error_log("applyFounderEffectsAfterPlacement - Effect: " . json_encode($effect));
        
        // Применяем эффекты только если activationStage == 'GameSetup'
        if ($activationStage !== 'GameSetup') {
            // Если activationStage != 'GameSetup', эффекты не применяются, но кнопка все равно разблокируется
            error_log("applyFounderEffectsAfterPlacement - ActivationStage mismatch, skipping effects");
            $this->notify->player($playerId, 'founderEffectsApplied', '', [
                'player_id' => $playerId,
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
            $expectedEffects = ['badger', 'card', 'task', 'move_task'];
            foreach ($expectedEffects as $expectedType) {
                if (!isset($effect[$expectedType])) {
                    error_log("applyFounderEffectsAfterPlacement - WARNING: Effect '$expectedType' is missing from card data!");
                } else {
                    error_log("applyFounderEffectsAfterPlacement - Found effect '$expectedType': " . json_encode($effect[$expectedType]));
                }
            }
        }
        
        $appliedEffects = $this->game->applyFounderEffect($playerId, $cardId, 'GameSetup');
        error_log("applyFounderEffectsAfterPlacement - Applied effects count: " . count($appliedEffects));
        error_log("applyFounderEffectsAfterPlacement - Applied effects: " . json_encode($appliedEffects));
        
        // Если были применены эффекты, отправляем уведомления
        // ВАЖНО: Обрабатываем эффекты в строгом порядке: badger -> card -> task
        if (!empty($appliedEffects)) {
            // Сортируем эффекты по порядку обработки для четкой последовательности
            $effectOrder = ['badger' => 1, 'card' => 2, 'task' => 3, 'move_task' => 4];
            usort($appliedEffects, function($a, $b) use ($effectOrder) {
                $orderA = $effectOrder[$a['type']] ?? 999;
                $orderB = $effectOrder[$b['type']] ?? 999;
                return $orderA <=> $orderB;
            });
            
            error_log("FounderSelection - Total effects to process: " . count($appliedEffects));
            error_log("FounderSelection - Effects array: " . json_encode($appliedEffects));
            
            $hasMoveTask = false;
            foreach ($appliedEffects as $effect) {
                $effectType = $effect['type'] ?? 'unknown';
                if ($effectType === 'move_task') {
                    $hasMoveTask = true;
                    error_log('✅✅✅ FounderSelection - Found move_task effect in appliedEffects!');
                }
                error_log("FounderSelection - Processing notification for effect type: $effectType, effect data: " . json_encode($effect));
                
                // Эффект 1: BADGER - изменение баджерсов
                if ($effectType === 'badger' && isset($effect['amount']) && $effect['amount'] !== 0) {
                    // Получаем актуальное состояние банка
                    $badgersSupply = $this->game->getBadgersSupply();
                    error_log('FounderSelection - Sending badgersChanged notification');
                    
                    // Отправляем уведомление об изменении баджерсов (включая данные банка)
                    $this->notify->all('badgersChanged', clienttranslate('${player_name} ${action_text} ${amount}Б благодаря эффекту карты «${founder_name}»'), [
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
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
                        'player_id' => $playerId,
                        'player_name' => $this->game->getPlayerNameById($playerId),
                        'amount' => $effect['amount'],
                        'founder_name' => $effect['founderName'] ?? 'Основатель',
                        'cardIds' => $effect['cardIds'] ?? [],
                        'cardNames' => $cardNames,
                    ]);
                    
                    // Обновляем данные игрока для клиента
                    $this->game->notify->player($playerId, 'specialistsUpdated', '', [
                        'player_id' => $playerId,
                    ]);
                    
                    error_log('FounderSelection - Player ' . $playerId . ' received ' . $effect['amount'] . ' specialist cards (card): ' . $cardNames);
                }
                // Эффект 3: TASK - выдача задач (task tokens)
                elseif ($effectType === 'task' && isset($effect['amount']) && $effect['amount'] > 0) {
                    // Отправляем уведомление о необходимости выбора задач
                    $this->notify->player($playerId, 'taskSelectionRequired', '', [
                        'player_id' => $playerId,
                        'amount' => $effect['amount'],
                        'founder_name' => $founderCard['name'] ?? '',
                    ]);
                    error_log('FounderSelection - Effect "task": Player ' . $playerId . ' must select ' . $effect['amount'] . ' tasks');
                }
                // Эффект 4: MOVE_TASK - перемещение жетонов задач
                elseif ($effectType === 'move_task') {
                    error_log('🎯🎯🎯 FounderSelection - Processing move_task effect: ' . json_encode($effect));
                    error_log('🎯🎯🎯 FounderSelection - move_task effect keys: ' . implode(', ', array_keys($effect)));
                    $moveCount = $effect['move_count'] ?? 0;
                    $moveColor = $effect['move_color'] ?? 'any';
                    
                    error_log('🎯🎯🎯 FounderSelection - move_task parsed: moveCount=' . $moveCount . ', moveColor=' . $moveColor);
                    
                    if ($moveCount > 0) {
                        // Отправляем уведомление о необходимости перемещения задач
                        error_log('🎯🎯🎯 FounderSelection - Sending taskMovesRequired notification to player ' . $playerId);
                        $this->notify->player($playerId, 'taskMovesRequired', '', [
                            'player_id' => $playerId,
                            'move_count' => $moveCount,
                            'move_color' => $moveColor,
                            'founder_name' => $founderCard['name'] ?? '',
                        ]);
                        error_log('✅✅✅ FounderSelection - Effect "move_task": Player ' . $playerId . ' must move tasks ' . $moveCount . ' times (color: ' . $moveColor . ') - NOTIFICATION SENT');
                    } else {
                        error_log('❌❌❌ FounderSelection - Effect "move_task": move_count is 0 or not set, skipping. moveCount=' . $moveCount);
                    }
                }
            }
            
            if (!$hasMoveTask) {
                error_log('❌❌❌ FounderSelection - WARNING: No move_task effect found in appliedEffects!');
            }
            
            // После применения всех эффектов отправляем уведомление о готовности к завершению хода
            $this->notify->player($playerId, 'founderEffectsApplied', '', [
                'player_id' => $playerId,
            ]);
        } else {
            // Если эффектов нет, все равно разблокируем кнопку
            $this->notify->player($playerId, 'founderEffectsApplied', '', [
                'player_id' => $playerId,
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
        
        // Декодируем JSON строку перемещений
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }
        
        // Проверяем, что использовано правильное количество ходов
        $totalBlocks = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['tokenId']) || !isset($move['toLocation'])) {
                continue;
            }
            $blocks = (int)($move['blocks'] ?? 0);
            $totalBlocks += $blocks;
        }
        
        // ВАЖНО: Требуем использовать все доступные ходы
        if ($totalBlocks !== $requiredMoves) {
            throw new UserException(clienttranslate('Вы должны использовать ровно ${amount} ходов', [
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

