<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Навыки» (Skills) раунда.
 * Активный игрок должен нажать «Завершить фазу навыков», после чего переход в NextPlayer.
 */
class RoundSkills extends \Bga\GameFramework\States\GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 16,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must complete the skills phase'),
            descriptionMyTurn: clienttranslate('${you} must complete the skills phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        return [
            'phaseKey' => 'skills',
            'phaseName' => $this->game->getPhaseName('skills'),
        ];
    }

    /**
     * При входе в фазу навыков только выставляем имя фазы; переход — по действию игрока.
     */
    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'skills');
        return null; // остаёмся в состоянии до действия игрока
    }

    /**
     * Игрок завершил фазу навыков (подтвердил переход).
     */
    #[PossibleAction]
    public function actCompleteSkillsPhase(): string
    {
        $this->game->checkAction('actCompleteSkillsPhase');
        $this->game->globals->set('skills_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    /**
     * Zombie mode: игрок вышел из игры — считаем фазу навыков завершённой и переходим в NextPlayer.
     */
    public function zombie(int $playerId): string
    {
        $this->game->globals->set('skills_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
