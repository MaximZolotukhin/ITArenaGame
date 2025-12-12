<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
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

    /**
     * Game state action, example content.
     *
     * The onEnteringState method of state `nextPlayer` is called everytime the current game state is set to `nextPlayer`.
     */
    function onEnteringState(int $activePlayerId) {
        // Give some extra time to the active player when he completed an action
        $this->game->giveExtraTime($activePlayerId);
        
        $isTutorial = $this->game->isTutorialMode();
        $currentRound = (int)$this->game->getGameStateValue('round_number');
        
        error_log('NextPlayer - isTutorial: ' . ($isTutorial ? 'yes' : 'no') . ', currentRound: ' . $currentRound);
        
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
                $playersCount = count($this->game->loadPlayersBasicInfos());
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                
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
                
                // Инициализируем счётчик игроков для первого раунда
                $playersCount = count($this->game->loadPlayersBasicInfos());
                $this->game->setGameStateValue('players_left_in_round', $playersCount);
                
                // Переходим к первому раунду (RoundEvent)
                return RoundEvent::class;
            }
            // Если currentRound > 0, значит ЭТАП 2 уже начался, продолжаем логику раундов ниже
        }
        
        // ========================================
        // ЭТАП 2: ОСНОВНАЯ ИГРА (раунды)
        // Выполняется для всех режимов после начала раундов
        // ========================================
        
        // Уменьшаем счётчик игроков в раунде
        $remaining = (int)$this->game->getGameStateValue('players_left_in_round');
        $remaining = max(0, $remaining - 1);
        $this->game->setGameStateValue('players_left_in_round', $remaining);
        
        error_log('NextPlayer - ЭТАП 2: remaining players in round: ' . $remaining);

        // Если раунд завершён (все игроки сделали ходы)
        if ($remaining === 0) {
            $currentRound = (int)$this->game->getGameStateValue('round_number');
            $totalRounds = (int)$this->game->getGameStateValue('total_rounds');
            $nextRound = $currentRound + 1;
            
            error_log('NextPlayer - Round finished! currentRound: ' . $currentRound . ', nextRound: ' . $nextRound . ', totalRounds: ' . $totalRounds);

            if ($nextRound > $totalRounds) {
                // Игра окончена
                $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), [
                    'rounds' => $totalRounds,
                ]);
                return EndScore::class;
            }

            // Подготовка к следующему раунду
            $this->game->setGameStateValue('round_number', $nextRound);
            $playersCount = count($this->game->loadPlayersBasicInfos());
            $this->game->setGameStateValue('players_left_in_round', $playersCount);
            
            error_log('NextPlayer - Starting round ' . $nextRound);
            return RoundEvent::class;
        }
        
        // Переход к следующему игроку в текущем раунде
        $this->game->activeNextPlayer();
        error_log('NextPlayer - Moving to next player in current round');
        return PlayerTurn::class;
    }
}