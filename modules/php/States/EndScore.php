<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\Games\itarenagame\Game;

const ST_END_GAME = 99;

class EndScore extends \Bga\GameFramework\States\GameState
{

    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 98,
            type: StateType::GAME,
        );
    }

    /**
     * Game state action, example content.
     *
     * The onEnteringState method of state `EndScore` is called just before the end of the game.
     */
    public function onEnteringState() {
        foreach ($this->game->loadPlayersBasicInfos() as $playerId => $_info) {
            $playerId = (int) $playerId;
            $breakdown = $this->game->getVictoryPointsBreakdown($playerId);
            $this->game->playerScore->set($playerId, $breakdown['total'], null);
        }

        return ST_END_GAME;
    }
}