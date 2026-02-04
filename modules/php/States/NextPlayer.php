<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\States\SpecialistSelection;
use Bga\Games\itarenagame\States\FounderSelection;

class NextPlayer extends \Bga\GameFramework\States\GameState
{

    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 90,
            type: StateType::GAME,
            updateGameProgression: true,
            transitions: [
                'toFounderSelection' => 20,
                'toSpecialistSelection' => 25,
                'toRoundEvent' => 15,
                'toPlayerTurn' => 11,
                'toEndScore' => 98,
                'toRoundSkills' => 16,
            ],
        );
    }

    public function getArgs(): array
    {
        $pending = $this->game->globals->get('pending_round_event', '');
        return ['pendingRoundEvent' => $pending !== '' ? (int)$pending : 0];
    }

    /**
     * Game state action, example content.
     *
     * The onEnteringState method of state `nextPlayer` is called everytime the current game state is set to `nextPlayer`.
     */
    function onEnteringState(int $activePlayerId): ?string {
        error_log('🎯🎯🎯 NextPlayer::onEnteringState() CALLED! activePlayerId: ' . $activePlayerId);
        
        // Give some extra time to the active player when he completed an action
        $this->game->giveExtraTime($activePlayerId);
        
        $isTutorial = $this->game->isTutorialMode();
        $currentRound = (int)$this->game->getGameStateValue('round_number');
        $playersLeftInRound = (int)$this->game->getGameStateValue('players_left_in_round');
        $playersCount = count($this->game->loadPlayersBasicInfos());
        
        error_log('🎯 NextPlayer::onEnteringState() - isTutorial: ' . ($isTutorial ? 'yes' : 'no') . ', currentRound: ' . $currentRound);
        error_log('🎯 NextPlayer::onEnteringState() - players_left_in_round: ' . $playersLeftInRound . ', playersCount: ' . $playersCount);
        
        // ========================================
        // ЭТАП 1: ПОДГОТОВКА К ИГРЕ
        // ========================================
        
        if ($isTutorial) {
            // ----------------------------------------
            // TUTORIAL: Проверяем, все ли игроки ВЫБРАЛИ и РАЗМЕСТИЛИ карты основателей
            // В Tutorial режиме карты показываются на руке, игрок должен кликнуть на карту
            // ----------------------------------------
            
            // Сначала проверяем, все ли игроки выбрали карты (как в основном режиме)
            $allFoundersSelected = $this->game->allPlayersSelectedFounders();
            error_log('NextPlayer - TUTORIAL: allFoundersSelected: ' . ($allFoundersSelected ? 'yes' : 'no'));
            
            if (!$allFoundersSelected) {
                // Ищем следующего игрока, который ещё не выбрал карту
                $this->game->activeNextPlayer();
                $nextPlayerId = $this->game->getActivePlayerId();
                
                // Проверяем, выбрал ли следующий игрок карту
                $nextPlayerFounder = $this->game->globals->get('founder_player_' . $nextPlayerId, null);
                if ($nextPlayerFounder === null) {
                    error_log('NextPlayer - TUTORIAL: Player ' . $nextPlayerId . ' needs to select founder');
                    return 'toFounderSelection';
                }
                
                // Ищем любого игрока, который ещё не выбрал карту
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    $founder = $this->game->globals->get('founder_player_' . (int)$playerId, null);
                    if ($founder === null) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        error_log('NextPlayer - TUTORIAL: Found player ' . $playerId . ' who needs to select founder');
                        return 'toFounderSelection';
                    }
                }
            }
            
            // Все игроки выбрали карты - проверяем размещение
            $allFoundersPlaced = $this->game->allFoundersPlaced();
            error_log('NextPlayer - TUTORIAL: allFoundersPlaced: ' . ($allFoundersPlaced ? 'yes' : 'no'));
            
            if (!$allFoundersPlaced) {
                // Ищем следующего игрока, который ещё не разместил карту
                $this->game->activeNextPlayer();
                $nextPlayerId = $this->game->getActivePlayerId();
                
                // Проверяем, есть ли у следующего игрока неразмещённая универсальная карта
                if ($this->game->hasUnplacedUniversalFounder((int)$nextPlayerId)) {
                    error_log('NextPlayer - TUTORIAL: Player ' . $nextPlayerId . ' needs to place founder');
                    return 'toFounderSelection';
                }
                
                // Ищем любого игрока с неразмещённой картой
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    if ($this->game->hasUnplacedUniversalFounder((int)$playerId)) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        error_log('NextPlayer - TUTORIAL: Found player ' . $playerId . ' with unplaced founder');
                        return 'toFounderSelection';
                    }
                }
                
                // Если дошли сюда, значит что-то не так - логируем и возвращаемся к FounderSelection для текущего игрока
                error_log('NextPlayer - TUTORIAL: WARNING - allFoundersPlaced=false but no player found with unplaced founder!');
                return 'toFounderSelection';
            }
            
            // Все карты размещены в Tutorial - переход к ЭТАПУ 2
            if ($currentRound === 0) {
                error_log('NextPlayer - TUTORIAL: ✅ Все карты размещены! Переход к ЭТАПУ 2');
                
                $this->notify->all('gameStart', '', [
                    'stageName' => clienttranslate('Начало игры'),
                ]);
                
                $this->game->setGameStateValue('round_number', 1);
                
                // ВАЖНО: Сбрасываем счетчики кубика и карт событий для первого раунда
                $this->game->setGameStateValue('last_cube_round', 0);
                $this->game->setGameStateValue('last_event_cards_round', 0);
                error_log('NextPlayer - TUTORIAL ЭТАП 1→2: Reset last_cube_round and last_event_cards_round to 0 for first round');
                
                $playersCount = count($this->game->loadPlayersBasicInfos());
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                error_log('NextPlayer - TUTORIAL ЭТАП 1→2: Set round_number=1, players_left_in_round=' . $playersCount);
                
                return 'toRoundEvent';
            }
        } else {
            // ----------------------------------------
            // ОСНОВНОЙ РЕЖИМ: ЭТАП 1.1: Выбор карт ОСНОВАТЕЛЕЙ
            // ----------------------------------------
            $allFoundersSelected = $this->game->allPlayersSelectedFounders();
            error_log('NextPlayer - allFoundersSelected: ' . ($allFoundersSelected ? 'yes' : 'no'));
            
            if (!$allFoundersSelected) {
                // Мы всё ещё на ЭТАПЕ 1.1 - переходим к следующему игроку для выбора карты основателя
                $this->game->activeNextPlayer();
                $nextPlayerId = $this->game->getActivePlayerId();
                error_log('NextPlayer - ЭТАП 1.1: Moving to next player for FounderSelection: ' . $nextPlayerId);
                
                // Проверяем, выбрал ли следующий игрок карту
                $nextPlayerFounder = $this->game->globals->get('founder_player_' . $nextPlayerId, null);
                if ($nextPlayerFounder === null) {
                    return 'toFounderSelection';
                }
                
                // Ищем игрока, который ещё не выбрал карту
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    $founder = $this->game->globals->get('founder_player_' . $playerId, null);
                    if ($founder === null) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        return 'toFounderSelection';
                    }
                }
            }
            
            // ----------------------------------------
            // ОСНОВНОЙ РЕЖИМ: ЭТАП 1.2: Выбор карт СОТРУДНИКОВ (после основателей)
            // ----------------------------------------
            $allSpecialistsSelected = $this->game->allPlayersSelectedSpecialists();
            error_log('NextPlayer - allSpecialistsSelected: ' . ($allSpecialistsSelected ? 'yes' : 'no'));
            
            if (!$allSpecialistsSelected) {
                // Ищем первого игрока, который ещё не выбрал карты сотрудников
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    $done = $this->game->globals->get('specialist_selection_done_' . $playerId, false);
                    error_log('NextPlayer - ЭТАП 1.2: Checking player ' . $playerId . ', done: ' . ($done ? 'yes' : 'no'));
                    if (!$done) {
                        error_log('NextPlayer - ЭТАП 1.2: Переход к SpecialistSelection для игрока: ' . $playerId);
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        return 'toSpecialistSelection';
                    }
                }
            }
            
            // ----------------------------------------
            // ОСНОВНОЙ РЕЖИМ: Все выбрали основателей И сотрудников - проверяем, начался ли уже ЭТАП 2
            // ----------------------------------------
            if ($currentRound === 0) {
                // Переход от ЭТАПА 1 к ЭТАПУ 2
                error_log('NextPlayer - ✅ Все игроки выбрали карты! Переход к ЭТАПУ 2');
                
                // Отправляем уведомление о начале ЭТАПА 2
                $this->notify->all('gameStart', '', [
                    'stageName' => clienttranslate('Начало игры'),
                ]);
                
                // Устанавливаем раунд 1
                $this->game->setGameStateValue('round_number', 1);
                
                // ВАЖНО: Сбрасываем счетчики кубика и карт событий для первого раунда
                $this->game->setGameStateValue('last_cube_round', 0);
                $this->game->setGameStateValue('last_event_cards_round', 0);
                error_log('NextPlayer - ЭТАП 1→2: Reset last_cube_round and last_event_cards_round to 0 for first round');
                
                // Инициализируем счётчик игроков для первого раунда
                $playersCount = count($this->game->loadPlayersBasicInfos());
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                
                // Инициализируем фазы для нового раунда
                $this->game->setGameStateValue('current_phase_index', 0);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                
                error_log('NextPlayer - ЭТАП 1→2: Set round_number=1, players_left_in_round=' . $playersCount . ', current_phase_index=0');
                
                // Переходим к первому раунду (RoundEvent)
                error_log('🎲🎲🎲 NextPlayer - Переход к RoundEvent (этап 2, раунд 1)');
                return 'toRoundEvent';
            }
            // Если currentRound > 0, значит ЭТАП 2 уже начался, продолжаем логику раундов ниже
        }
        
        // ========================================
        // ЭТАП 2: ОСНОВНАЯ ИГРА (раунды и фазы)
        // Выполняется ТОЛЬКО когда currentRound > 0 (ЭТАП 2 уже начался)
        // ========================================
        
        // ВАЖНО: Проверяем, что мы действительно на ЭТАПЕ 2 (currentRound > 0)
        // Если currentRound === 0, значит мы еще на ЭТАПЕ 1, и логика раундов не должна выполняться
        $currentRound = (int)$this->game->getGameStateValue('round_number');
        
        if ($currentRound === 0) {
            // Мы еще на ЭТАПЕ 1 - логика раундов не должна выполняться
            error_log('NextPlayer - ❌ ОШИБКА: Попытка выполнить логику ЭТАПА 2 при currentRound=0 (ЭТАП 1)!');
            error_log('NextPlayer - Это не должно происходить. Проверяем состояние игры...');
            
            // Проверяем, все ли игроки завершили выбор специалистов
            $allSpecialistsSelected = $this->game->allPlayersSelectedSpecialists();
            error_log('NextPlayer - allSpecialistsSelected: ' . ($allSpecialistsSelected ? 'yes' : 'no'));
            
            if ($allSpecialistsSelected) {
                // Все игроки завершили выбор - должны были перейти к ЭТАПУ 2, но не перешли
                // Это критическая ошибка - логируем и пытаемся исправить
                error_log('NextPlayer - ❌ КРИТИЧЕСКАЯ ОШИБКА: Все игроки завершили выбор, но currentRound=0!');
                error_log('NextPlayer - Принудительно переходим к ЭТАПУ 2...');
                
                $this->notify->all('gameStart', '', [
                    'stageName' => clienttranslate('Начало игры'),
                ]);
                
                $this->game->setGameStateValue('round_number', 1);
                $playersCount = count($this->game->loadPlayersBasicInfos());
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                $this->game->setGameStateValue('last_cube_round', 0);
                $this->game->setGameStateValue('last_event_cards_round', 0);
                
                // Инициализируем фазы для нового раунда
                $this->game->setGameStateValue('current_phase_index', 0);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                
                error_log('NextPlayer - Исправлено: round_number=1, players_left_in_round=' . $playersCount . ', current_phase_index=0');
                return 'toRoundEvent';
            } else {
                // Не все игроки завершили выбор - возвращаемся к выбору специалистов
                error_log('NextPlayer - Не все игроки завершили выбор, возвращаемся к SpecialistSelection');
                return 'toSpecialistSelection';
            }
        }
        
        // ========================================
        // ФАЗЫ РАУНДА: единый проход по массиву getRoundPhases()
        // 1) Сверяемся с массивом фаз — сколько фаз в раунде (фазы будут расширяться).
        // 2) Считываем текущую фазу по current_phase_index.
        // 3) Выполняем логику фазы, ждём команды «все игроки закончили ход».
        // 4) Смотрим в массив: есть ли следующая фаза? Если да — загружаем её. Если нет — следующий раунд.
        // 5) Сбрасываем счётчик фаз при переходе на новый раунд, повторяем цикл до последнего раунда.
        // ========================================
        
        $playersCount = count($this->game->loadPlayersBasicInfos());
        $playersLeftInRound = (int)$this->game->getGameStateValue('players_left_in_round');
        $playersCompletedCurrentPhase = (int)$this->game->getGameStateValue('players_completed_current_phase');

        // Берём массив фаз и количество фаз из getRoundPhases() — единственный источник правды
        $phases = $this->game->getRoundPhases();
        $numberOfPhases = count($phases);
        $currentPhaseIndex = (int)$this->game->getGameStateValue('current_phase_index');

        error_log('NextPlayer - ETAP 2: round=' . $currentRound . ', phaseIndex=' . $currentPhaseIndex . '/' . $numberOfPhases . ', playersLeft=' . $playersLeftInRound . ', completedPhase=' . $playersCompletedCurrentPhase . '/' . $playersCount);

        // Все фазы раунда пройдены — смотрим в массив: следующей фазы нет → следующий раунд
        if ($currentPhaseIndex >= $numberOfPhases) {
            error_log('NextPlayer - Все фазы раунда пройдены (индекс >= кол-ва фаз) → следующий раунд или конец игры');
            $totalRounds = (int)$this->game->getGameStateValue('total_rounds');
            $nextRound = $currentRound + 1;
            if ($totalRounds <= 0) {
                throw new \Exception("ROUND TRANSITION ERROR: total_rounds is not set! currentRound=$currentRound, nextRound=$nextRound, totalRounds=$totalRounds");
            }
            if ($nextRound > $totalRounds) {
                $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), ['rounds' => $totalRounds]);
                return 'toEndScore';
            }
            // Очередь хода в следующем раунде — по положению на треке навыков (левее = раньше ход)
            $nextRoundOrder = $this->game->getSkillOrderForNextRound();
            $this->game->setNextRoundPlayerOrder($nextRoundOrder);
            error_log('NextPlayer - Порядок хода в раунде ' . $nextRound . ' по треку навыков: ' . json_encode($nextRoundOrder));
            // Следующий раунд: сбрасываем счётчик массива фаз и всё для нового раунда
            $this->game->setGameStateValue('round_number', $nextRound);
            $this->game->setGameStateValue('current_phase_index', 0);
            $this->game->setGameStateValue('players_completed_current_phase', 0);
            $this->game->setGameStateValue('players_left_in_round', $playersCount);
            $this->game->setGameStateValue('last_cube_round', 0);
            $this->game->setGameStateValue('last_event_cards_round', 0);
            error_log('NextPlayer - Новый раунд ' . $nextRound . ', current_phase_index=0');
            return 'toRoundEvent';
        }

        // Текущая фаза из массива — по current_phase_index
        $currentPhase = $phases[$currentPhaseIndex];
        $phaseKey = $currentPhase['key'];
        $phaseState = $currentPhase['state'];
        $phaseTransition = $currentPhase['transition'] ?? null;

        // --- Фаза «Событие» (event): первый игрок → остальные → последний (здесь потом: штрафы, логика кости) ---
        if ($phaseKey === 'event') {
            // Только что вышли из RoundEvent — запускаем ходы: первый игрок в PlayerTurn (уже задан в RoundEvent по треку навыков)
            if ($this->game->globals->get('event_phase_just_finished', '') === '1') {
                $this->game->globals->delete('event_phase_just_finished');
                error_log('NextPlayer - Фаза Событие: старт ходов, первый игрок → PlayerTurn');
                if ($this->game->getCurrentRoundPlayerOrder() === null) {
                    $this->game->activeNextPlayer();
                }
                return 'toPlayerTurn';
            }
            // Пришли из PlayerTurn — проверяем: все ли закончили ход (players_left_in_round <= 0)
            if ($playersLeftInRound <= 0) {
                // Все игроки закончили ход в фазе Событие. Смотрим в массив: есть ли следующая фаза?
                $nextPhaseIndex = $currentPhaseIndex + 1;
                $this->game->setGameStateValue('current_phase_index', $nextPhaseIndex);
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                if ($nextPhaseIndex >= $numberOfPhases) {
                    // Следующей фазы нет — переход к следующему раунду (выполнен выше по current_phase_index >= numberOfPhases при следующем заходе)
                    // Но мы уже установили current_phase_index = nextPhaseIndex, поэтому при следующем вызове NextPlayer сработает блок «все фазы пройдены»
                    error_log('NextPlayer - Фаза Событие завершена, следующей фазы в массиве нет → следующий раунд');
                    return $this->goToNextRoundOrNextPhase($currentRound, $nextPhaseIndex, $numberOfPhases, $playersCount);
                }
                // Есть следующая фаза — загружаем её (первый игрок)
                $nextPhase = $phases[$nextPhaseIndex];
                $nextTransition = $nextPhase['transition'] ?? null;
                error_log('NextPlayer - Фаза Событие завершена, загружаем следующую фазу: ' . ($nextPhase['key'] ?? $nextPhaseIndex));
                $this->game->advanceToNextPlayerInRound();
                return (string)($nextTransition ?: 'toRoundSkills');
            }
            // Ещё не все закончили ход — следующий игрок в PlayerTurn (по порядку трека навыков)
            error_log('NextPlayer - Фаза Событие: следующий игрок PlayerTurn, playersLeft=' . $playersLeftInRound);
            $this->game->advanceToNextPlayerInRound();
            return 'toPlayerTurn';
        }

        // --- Фаза «Навыки» (skills) и любые будущие фазы: ждём, пока все игроки закончат ход в фазе ---
        if ($phaseKey === 'skills' || $phaseTransition !== null) {
            // Только что вышли из состояния фазы (например RoundSkills) — один игрок «закончил ход» в этой фазе
            if ($this->game->globals->get('skills_phase_just_finished', '') === '1') {
                $this->game->globals->delete('skills_phase_just_finished');
                $playersCompletedCurrentPhase = (int)$this->game->getGameStateValue('players_completed_current_phase');
                $playersCompletedCurrentPhase++;
                $this->game->setGameStateValue('players_completed_current_phase', $playersCompletedCurrentPhase);
                error_log('NextPlayer - Фаза ' . $phaseKey . ': закончили ход ' . $playersCompletedCurrentPhase . '/' . $playersCount);
                if ($playersCompletedCurrentPhase < $playersCount) {
                    // Не все закончили — следующий игрок в эту же фазу (по порядку трека навыков)
                    $this->game->advanceToNextPlayerInRound();
                    return (string)($phaseTransition ?: 'toRoundSkills');
                }
                // Все игроки закончили ход в этой фазе. Смотрим в массив: есть ли следующая фаза?
                $nextPhaseIndex = $currentPhaseIndex + 1;
                $this->game->setGameStateValue('current_phase_index', $nextPhaseIndex);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                if ($nextPhaseIndex >= $numberOfPhases) {
                    error_log('NextPlayer - Фаза ' . $phaseKey . ' завершена, следующей фазы нет → следующий раунд');
                    return $this->goToNextRoundOrNextPhase($currentRound, $nextPhaseIndex, $numberOfPhases, $playersCount);
                }
                // Есть следующая фаза — загружаем её
                $nextPhase = $phases[$nextPhaseIndex];
                $nextTransition = $nextPhase['transition'] ?? null;
                error_log('NextPlayer - Фаза ' . $phaseKey . ' завершена, загружаем следующую: ' . ($nextPhase['key'] ?? $nextPhaseIndex));
                $this->game->advanceToNextPlayerInRound();
                return (string)($nextTransition ?: 'toRoundSkills');
            }
            // Первый заход в эту фазу в раунде — переходим в состояние фазы (например RoundSkills) по порядку трека навыков
            error_log('NextPlayer - Фаза ' . $phaseKey . ': первый игрок → ' . ($phaseTransition ?? 'state'));
            $this->game->advanceToNextPlayerInRound();
            return (string)($phaseTransition ?: 'toRoundSkills');
        }

        // Запасной выход: следующая фаза по transition из массива (всегда имя перехода, не state id)
        $this->game->advanceToNextPlayerInRound();
        return (string)($phaseTransition ?? 'toPlayerTurn');
    }

    /**
     * Переход к следующему раунду или к следующей фазе.
     * Вызывается, когда текущая фаза завершена: смотрим в массив фаз — есть ли следующая фаза.
     * Если нет (current_phase_index >= numberOfPhases) — следующий раунд, сброс счётчика фаз.
     */
    private function goToNextRoundOrNextPhase(int $currentRound, int $nextPhaseIndex, int $numberOfPhases, int $playersCount): string
    {
        if ($nextPhaseIndex >= $numberOfPhases) {
            $totalRounds = (int)$this->game->getGameStateValue('total_rounds');
            $nextRound = $currentRound + 1;
            if ($totalRounds <= 0) {
                throw new \Exception("ROUND TRANSITION ERROR: total_rounds is not set!");
            }
            if ($nextRound > $totalRounds) {
                $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), ['rounds' => $totalRounds]);
                return 'toEndScore';
            }
            // Очередь хода в следующем раунде — по треку навыков
            $nextRoundOrder = $this->game->getSkillOrderForNextRound();
            $this->game->setNextRoundPlayerOrder($nextRoundOrder);
            $this->game->setGameStateValue('round_number', $nextRound);
            $this->game->setGameStateValue('current_phase_index', 0);
            $this->game->setGameStateValue('players_completed_current_phase', 0);
            $this->game->setGameStateValue('players_left_in_round', $playersCount);
            $this->game->setGameStateValue('last_cube_round', 0);
            $this->game->setGameStateValue('last_event_cards_round', 0);
            return 'toRoundEvent';
        }
        $phases = $this->game->getRoundPhases();
        $nextPhase = $phases[$nextPhaseIndex];
        // Всегда возвращаем имя перехода (строка), никогда state id — иначе BGA даёт "transition (16) impossible"
        $key = $nextPhase['key'] ?? '';
        return match ($key) {
            'event' => 'toRoundEvent',
            'skills' => 'toRoundSkills',
            default => 'toRoundSkills',
        };
    }

    /**
     * Переход к RoundEvent для показа события раунда (когда pending_round_event).
     */
    #[PossibleAction]
    public function actStartRoundEvent()
    {
        $this->game->checkAction('actStartRoundEvent');
        $pending = $this->game->globals->get('pending_round_event', '');
        if ($pending === '') {
            throw new UserException(clienttranslate('Нет ожидающего события раунда'));
        }
        $this->game->globals->delete('pending_round_event');
        return 'toRoundEvent';
    }
}