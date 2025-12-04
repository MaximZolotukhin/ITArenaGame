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
        
        // Мой код для уведомления о конце раунда
        $this->notify->all('roundEnd', clienttranslate('Конец раунда ${round}'), [
            'round' => (int)$this->game->getGameStateValue('round_number'), // Текущий раунд
        ]);
        // Decrement remaining players in this round
        $remaining = (int)$this->game->getGameStateValue('players_left_in_round'); // Количество игроков в раунде
        $remaining = max(0, $remaining - 1); // Количество игроков в раунде после выхода одного игрока
        $this->game->setGameStateValue('players_left_in_round', $remaining); // Количество игроков в раунде после выхода одного игрока

        // If round is finished, increment round and either end the game or start next round
        if ($remaining === 0) {
            $currentRound = (int)$this->game->getGameStateValue('round_number'); // Текущий раунд
            $totalRounds = (int)$this->game->getGameStateValue('total_rounds'); // Общее количество раундов
            $nextRound = $currentRound + 1; // Следующий раунд

            // Уведомление о начале следующего раунда отправим ниже, после обновления счетчиков и с именем этапа
            if ($nextRound > $totalRounds) {
                // Announce end of game and go to EndScore
                $this->notify->all('gameEnd', clienttranslate('Игра окончена после ${rounds} раундов'), [ // Общее количество раундов
                    'rounds' => $totalRounds, // Общее количество раундов
                ]);
                return EndScore::class; // Конец игры
            }

            // Prepare next round counters and go to Phase 1: "Событие"
            $this->game->setGameStateValue('round_number', $nextRound); // Следующий раунд
            $playersCount = count($this->game->loadPlayersBasicInfos());
            $this->game->setGameStateValue('players_left_in_round', $playersCount); // Количество игроков в раунде
            return RoundEvent::class;
        }

        // Проверяем, есть ли еще игроки, которые не выбрали карты основателей (для основного режима)
        $isTutorial = $this->game->isTutorialMode();
        
        if (!$isTutorial) {
            // Основной режим: проверяем, все ли игроки выбрали карты
            $allPlayersSelected = $this->game->allPlayersSelectedFounders();
            error_log('NextPlayer - allPlayersSelected: ' . ($allPlayersSelected ? 'yes' : 'no'));
            
            if (!$allPlayersSelected) {
                // Еще есть игроки без выбранных карт - переходим к выбору карты следующего игрока
                $this->game->activeNextPlayer();
                $nextPlayerId = $this->game->getActivePlayerId();
                error_log('NextPlayer - Moving to next player for FounderSelection: ' . $nextPlayerId);
                
                // Проверяем, выбрал ли следующий игрок карту
                $nextPlayerFounder = $this->game->globals->get('founder_player_' . $nextPlayerId, null);
                if ($nextPlayerFounder === null) {
                    // Игрок еще не выбрал карту - переходим к выбору
                    return FounderSelection::class;
                } else {
                    // Игрок уже выбрал карту - продолжаем искать следующего, кто не выбрал
                    // Рекурсивно ищем игрока, который еще не выбрал карту
                    $players = array_keys($this->game->loadPlayersBasicInfos());
                    foreach ($players as $playerId) {
                        $founder = $this->game->globals->get('founder_player_' . $playerId, null);
                        if ($founder === null) {
                            // Нашли игрока без карты
                            $this->game->gamestate->changeActivePlayer((int)$playerId);
                            return FounderSelection::class;
                        }
                    }
                    // Все выбрали - переходим к началу игры
                }
            }
            
            // Все игроки выбрали карты - отправляем уведомление о начале ЭТАПА 2
            error_log('NextPlayer - All players selected founders! Starting ЭТАП 2');
            
            $this->notify->all('gameStart', clienttranslate('🎮 ЭТАП 2: НАЧАЛО ИГРЫ'), [
                'stageName' => clienttranslate('Начало игры'),
            ]);
            
            // Сбрасываем счетчик игроков для первого раунда
            $playersCount = count($this->game->loadPlayersBasicInfos());
            $this->game->setGameStateValue('players_left_in_round', $playersCount);
            
            // Переходим к первому раунду (RoundEvent)
            return RoundEvent::class;
        }
        
        // Обучающий режим: обычный переход к следующему игроку
        // Move to next active player and continue normal play
        // Мой код для перехода к следующему игроку
        $this->game->activeNextPlayer();
        return PlayerTurn::class;
    }
}