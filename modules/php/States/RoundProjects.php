<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Проекты».
 *
 * Очерёдность хода в фазе: игроки ходят по порядку раунда (трек навыков).
 * У активного игрока есть две кнопки:
 *  - «Пас» (actPassProjects) — передаёт ход следующему игроку, оставаясь
 *    в очерёдности фазы (сможет сходить ещё раз позже в этой же фазе).
 *  - «Завершить ход» (actFinishProjectsTurn) — игрок выпадает из очерёдности
 *    до следующей фазы; помечается флагом projects_phase_done_<pid> в globals.
 *
 * Когда все игроки нажали «Завершить ход» (allProjectsPhaseDone), фаза
 * завершается и управление передаётся в NextPlayer для перехода к следующей
 * фазе или раунду.
 */
final class RoundProjects extends GameState
{
    public function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 21,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must complete the Projects phase'),
            descriptionMyTurn: clienttranslate('${you} must complete the Projects phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $donePlayers = $this->game->getProjectsPhaseDonePlayers();
        // «Пас» имеет смысл, только если ещё есть хотя бы один другой игрок,
        // который не нажал «Завершить ход» — иначе пасовать некому.
        $canPass = false;
        foreach (array_keys($this->game->loadPlayersBasicInfos()) as $playerId) {
            $pid = (int) $playerId;
            if ($pid !== $activePlayerId && !$this->game->isProjectsPhaseDone($pid)) {
                $canPass = true;
                break;
            }
        }

        return [
            'phaseKey' => 'projects',
            'phaseName' => $this->game->getPhaseName('projects'),
            'activePlayerId' => $activePlayerId,
            'donePlayers' => $donePlayers,
            'canPass' => $canPass,
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'projects');
        return null;
    }

    /**
     * «Пас»: игрок передаёт ход следующему, оставаясь в очерёдности фазы.
     * NextPlayer обработает флаг projects_phase_pass и выберет следующего
     * незавершившего игрока по порядку раунда.
     */
    #[PossibleAction]
    public function actPassProjects(): string
    {
        $this->game->checkAction('actPassProjects');
        $this->game->globals->set('projects_phase_pass', '1');
        return 'toNextPlayer';
    }

    /**
     * «Завершить ход»: игрок выпадает из очерёдности фазы до следующей фазы.
     * NextPlayer обработает флаг projects_phase_just_finished, отметит игрока
     * как done и либо передаст ход следующему незавершившему, либо завершит фазу.
     */
    #[PossibleAction]
    public function actFinishProjectsTurn(): string
    {
        $this->game->checkAction('actFinishProjectsTurn');
        $playerId = (int) $this->game->getActivePlayerId();
        $this->game->savePlayerGameDataOnTurnEnd($playerId);
        $this->game->globals->set('projects_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    public function zombie(int $playerId): string
    {
        // Зомби-игрок принудительно «завершает ход» в фазе.
        $this->game->globals->set('projects_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}

