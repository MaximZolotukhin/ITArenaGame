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
                    return FounderSelection::class;
                }
                
                // Ищем любого игрока, который ещё не выбрал карту
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    $founder = $this->game->globals->get('founder_player_' . (int)$playerId, null);
                    if ($founder === null) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        error_log('NextPlayer - TUTORIAL: Found player ' . $playerId . ' who needs to select founder');
                        return FounderSelection::class;
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
                    return FounderSelection::class;
                }
                
                // Ищем любого игрока с неразмещённой картой
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    if ($this->game->hasUnplacedUniversalFounder((int)$playerId)) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        error_log('NextPlayer - TUTORIAL: Found player ' . $playerId . ' with unplaced founder');
                        return FounderSelection::class;
                    }
                }
                
                // Если дошли сюда, значит что-то не так - логируем и возвращаемся к FounderSelection для текущего игрока
                error_log('NextPlayer - TUTORIAL: WARNING - allFoundersPlaced=false but no player found with unplaced founder!');
                return FounderSelection::class;
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
                
                return RoundEvent::class;
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
                    return FounderSelection::class;
                }
                
                // Ищем игрока, который ещё не выбрал карту
                $players = array_keys($this->game->loadPlayersBasicInfos());
                foreach ($players as $playerId) {
                    $founder = $this->game->globals->get('founder_player_' . $playerId, null);
                    if ($founder === null) {
                        $this->game->gamestate->changeActivePlayer((int)$playerId);
                        return FounderSelection::class;
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
                        return SpecialistSelection::class;
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
                return RoundEvent::class;
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
                return RoundEvent::class;
            } else {
                // Не все игроки завершили выбор - возвращаемся к выбору специалистов
                error_log('NextPlayer - Не все игроки завершили выбор, возвращаемся к SpecialistSelection');
                return SpecialistSelection::class;
            }
        }
        
        // ========================================
        // НОВАЯ ЛОГИКА: Фазы раунда
        // Каждый игрок проходит все фазы по очереди
        // После того как все игроки прошли все фазы, переходим к следующему раунду
        // ========================================
        
        $phases = $this->game->getRoundPhases();
        $currentPhaseIndex = (int)$this->game->getGameStateValue('current_phase_index', 0);
        $playersCompletedCurrentPhase = (int)$this->game->getGameStateValue('players_completed_current_phase', 0);
        $playersCount = count($this->game->loadPlayersBasicInfos());
        
        error_log('🎯🎯🎯 NextPlayer - ЭТАП 2: currentRound=' . $currentRound . ', currentPhaseIndex=' . $currentPhaseIndex . ', phasesCount=' . count($phases));
        error_log('🎯 NextPlayer - playersCompletedCurrentPhase=' . $playersCompletedCurrentPhase . ', playersCount=' . $playersCount);
        
        // Проверяем, есть ли еще фазы
        if ($currentPhaseIndex >= count($phases)) {
            // Все фазы пройдены - переходим к следующему раунду
            error_log('🎯🎯🎯 NextPlayer - Все фазы раунда ' . $currentRound . ' пройдены! Переход к следующему раунду...');
            
            $totalRounds = (int)$this->game->getGameStateValue('total_rounds');
            $nextRound = $currentRound + 1;
            
            if ($totalRounds <= 0) {
                throw new \Exception("ROUND TRANSITION ERROR: total_rounds is not set! currentRound=$currentRound, nextRound=$nextRound, totalRounds=$totalRounds");
            }
            
            if ($nextRound > $totalRounds) {
                // Игра окончена
                error_log('🎯 NextPlayer - Game finished! All rounds completed.');
                $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), [
                    'rounds' => $totalRounds,
                ]);
                return EndScore::class;
            }
            
                // Подготовка к следующему раунду: не переходим в RoundEvent в том же запросе,
                // иначе BGA выдаёт "Unexpected final game state (15)". Остаёмся в NextPlayer,
                // ставим pending_round_event — клиент вызовет actStartRoundEvent для перехода в RoundEvent.
                error_log('🎯🎯🎯 NextPlayer - PREPARING for round ' . $nextRound . ', staying in NextPlayer (pending_round_event)');
                
                $this->game->setGameStateValue('round_number', $nextRound);
                $this->game->setGameStateValue('current_phase_index', 0);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                $this->game->setGameStateValue('last_cube_round', 0);
                $this->game->setGameStateValue('last_event_cards_round', 0);
                $this->game->globals->set('pending_round_event', (string)$nextRound);
                
                // ВАЖНО: возвращаем null явно, чтобы остаться в NextPlayer (BGA требует явного возврата)
                return null;
        }
        
        // Получаем текущую фазу
        $currentPhase = $phases[$currentPhaseIndex];
        
        error_log('🎯 NextPlayer - Current phase: ' . $currentPhase['key'] . ' (index: ' . $currentPhaseIndex . ', state: ' . $currentPhase['state'] . ')');
        error_log('🎯 NextPlayer - playersCompletedCurrentPhase: ' . $playersCompletedCurrentPhase . ' / ' . $playersCount);
        
        // Проверяем, начало ли это фазы (когда счетчик равен 0)
        if ($playersCompletedCurrentPhase === 0) {
            $phaseState = $currentPhase['state'];
            // Фаза «Событие» (RoundEvent): мы только что вышли из RoundEvent (он отправил
            // roundStart и вернул NextPlayer). Не уходим в RoundEvent снова — считаем фазу
            // завершённой и идём в блок «все сделали».
            if ($phaseState === RoundEvent::class && $this->game->globals->get('event_phase_just_finished', '') === '1') {
                $this->game->globals->delete('event_phase_just_finished');
                error_log('🎯 NextPlayer - Back from RoundEvent (event_phase_just_finished), marking phase complete to avoid loop');
                $this->game->setGameStateValue('players_completed_current_phase', $playersCount);
                $playersCompletedCurrentPhase = $playersCount;
                // Продолжаем выполнение ниже, чтобы попасть в блок "все завершили фазу"
                // ВАЖНО: не делаем return здесь, продолжаем выполнение
                // НЕ инкрементируем счетчик ниже, так как фаза уже помечена как завершённая
            } else {
                // Первый заход в фазу или фаза с ходами — переходим в неё
                error_log('🎯 NextPlayer - Phase start! Activating first player for phase: ' . $currentPhase['key']);
                $this->game->activeNextPlayer();
                return $phaseState;
            }
        }
        
        // Игрок завершил текущую фазу - увеличиваем счетчик
        // ВАЖНО: НЕ инкрементируем, если мы уже пометили фазу как завершённую выше (RoundEvent case)
        if ($playersCompletedCurrentPhase < $playersCount) {
            $playersCompletedCurrentPhase++;
            $this->game->setGameStateValue('players_completed_current_phase', $playersCompletedCurrentPhase);
        }
        
        error_log('🎯 NextPlayer - Player completed phase! playersCompletedCurrentPhase: ' . $playersCompletedCurrentPhase . ' / ' . $playersCount);
        
        // Проверяем, все ли игроки завершили текущую фазу
        if ($playersCompletedCurrentPhase >= $playersCount) {
            // Все игроки завершили текущую фазу - переходим к следующей фазе
            error_log('🎯🎯🎯 NextPlayer - All players completed phase ' . $currentPhase['key'] . '! Moving to next phase...');
            
            $nextPhaseIndex = $currentPhaseIndex + 1;
            $this->game->setGameStateValue('current_phase_index', $nextPhaseIndex);
            $this->game->setGameStateValue('players_completed_current_phase', 0);
            
            // Проверяем, есть ли еще фазы
            if ($nextPhaseIndex >= count($phases)) {
                // Все фазы пройдены - переходим к следующему раунду
                error_log('🎯🎯🎯 NextPlayer - Все фазы раунда ' . $currentRound . ' пройдены! Переход к следующему раунду...');
                
                $totalRounds = (int)$this->game->getGameStateValue('total_rounds');
                $nextRound = $currentRound + 1;
                
                if ($totalRounds <= 0) {
                    throw new \Exception("ROUND TRANSITION ERROR: total_rounds is not set! currentRound=$currentRound, nextRound=$nextRound, totalRounds=$totalRounds");
                }
                
                if ($nextRound > $totalRounds) {
                    // Игра окончена
                    error_log('🎯 NextPlayer - Game finished! All rounds completed.');
                    $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), [
                        'rounds' => $totalRounds,
                    ]);
                    return EndScore::class;
                }
                
                // Подготовка к следующему раунду: остаёмся в NextPlayer, ставим pending_round_event
                // ВАЖНО: не переходим в RoundEvent в том же запросе, иначе BGA выдаёт "Unexpected final game state (15)"
                error_log('🎯🎯🎯 NextPlayer - PREPARING for round ' . $nextRound . ', staying (pending_round_event)');
                
                $this->game->setGameStateValue('round_number', $nextRound);
                $this->game->setGameStateValue('current_phase_index', 0);
                $this->game->setGameStateValue('players_completed_current_phase', 0);
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                $this->game->setGameStateValue('last_cube_round', 0);
                $this->game->setGameStateValue('last_event_cards_round', 0);
                $this->game->globals->set('pending_round_event', (string)$nextRound);
                
                // ВАЖНО: возвращаем null явно, чтобы остаться в NextPlayer (BGA требует явного возврата)
                // Клиент увидит pendingRoundEvent и покажет кнопку "Продолжить" для перехода в RoundEvent следующего раунда
                return null;
            } else {
                // Переходим к следующей фазе
                $nextPhase = $phases[$nextPhaseIndex];
                error_log('🎯🎯🎯 NextPlayer - Transitioning to next phase: ' . $nextPhase['key'] . ' (index: ' . $nextPhaseIndex . ')');
                
                // Активируем первого игрока для новой фазы
                $this->game->activeNextPlayer();
                $firstPlayerId = $this->game->getActivePlayerId();
                error_log('🎯 NextPlayer - First player activated for new phase: ' . $firstPlayerId);
                
                return $nextPhase['state'];
            }
        }
        
        // Переход к следующему игроку в текущей фазе
        error_log('🎯 NextPlayer - Moving to next player in current phase (completed: ' . $playersCompletedCurrentPhase . ' / ' . $playersCount . ')');
        $this->game->activeNextPlayer();
        $nextPlayerId = $this->game->getActivePlayerId();
        error_log('🎯 NextPlayer - Next player activated: ' . $nextPlayerId);
        
        // Возвращаемся к состоянию текущей фазы
        return $currentPhase['state'];
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
        return RoundEvent::class;
    }
}