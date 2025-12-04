<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\Games\itarenagame\Game;

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
        // ЭТАП 1: ПОДГОТОВКА К ИГРЕ (только для основного режима)
        // ========================================
        if (!$isTutorial) {
            // Проверяем, все ли игроки выбрали карты основателей
            $allPlayersSelected = $this->game->allPlayersSelectedFounders();
            error_log('NextPlayer - allPlayersSelected: ' . ($allPlayersSelected ? 'yes' : 'no'));
            
            if (!$allPlayersSelected) {
                // Мы всё ещё на ЭТАПЕ 1 - переходим к следующему игроку для выбора карты
                $this->game->activeNextPlayer();
                $nextPlayerId = $this->game->getActivePlayerId();
                error_log('NextPlayer - ЭТАП 1: Moving to next player for FounderSelection: ' . $nextPlayerId);
                
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
            
            // Все игроки выбрали карты - проверяем, начался ли уже ЭТАП 2
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