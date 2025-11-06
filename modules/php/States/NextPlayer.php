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

        // Move to next active player and continue normal play
        // Мой код для перехода к следующему игроку
        $this->game->activeNextPlayer();
        return PlayerTurn::class;
    }
}