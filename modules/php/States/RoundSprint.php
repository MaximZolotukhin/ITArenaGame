<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Спринт»: взять до N задач из банка в бэклог (N = трек sprint-column-tasks),
 * затем перемещать жетоны по треку спринта с ограничением по техотделу (techDevCol1–4 по цвету).
 */
class RoundSprint extends GameState
{
    public function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 19,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must complete the Sprint phase'),
            descriptionMyTurn: clienttranslate('${you} must complete the Sprint phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $round = (int) $this->game->getGameStateValue('round_number');
        $phase = $this->game->getPhaseByKey('sprint');
        $phaseName = $phase ? $phase['name'] : '';
        $phaseNumber = $phase ? $phase['number'] : 5;

        $activePlayerId = (int) $this->game->getActivePlayerId();
        $takeLimit = $this->game->getSprintColumnTasksTakeLimit($activePlayerId);
        // Флаги подтверждения должны быть привязаны к раунду.
        // При refresh страницы или при ошибках переходов старые globals могли "залипать" и блокировать действия.
        $bankRoundKey = 'sprint_bank_confirmed_round_' . $activePlayerId;
        $movesRoundKey = 'sprint_track_moves_done_round_' . $activePlayerId;
        $bankKey = 'sprint_bank_confirmed_' . $activePlayerId;
        $movesKey = 'sprint_track_moves_done_' . $activePlayerId;

        $bankRound = (int) ($this->game->globals->get($bankRoundKey, '0') ?: 0);
        $movesRound = (int) ($this->game->globals->get($movesRoundKey, '0') ?: 0);
        if ($bankRound !== $round) {
            $this->game->globals->delete($bankKey);
            $this->game->globals->set($bankRoundKey, (string) $round);
        }
        if ($movesRound !== $round) {
            $this->game->globals->delete($movesKey);
            $this->game->globals->set($movesRoundKey, (string) $round);
        }

        $bankConfirmed = $this->game->globals->get($bankKey, '') === '1';
        $sprintTrackMoveMandatory = $this->game->playerMustSprintTrackMoveBeforeComplete($activePlayerId);
        $sprintTrackMovesConfirmed = $this->game->globals->get($movesKey, '') === '1';

        return [
            'phaseKey' => 'sprint',
            'phaseName' => $phaseName,
            'phaseNumber' => $phaseNumber,
            'round' => $round,
            'roundName' => $this->game->getRoundName($round),
            'activePlayerId' => $activePlayerId,
            'sprintTasksTakeLimit' => $takeLimit,
            'sprintBankConfirmed' => $bankConfirmed,
            'sprintTrackMoveMandatory' => $sprintTrackMoveMandatory,
            'sprintTrackMovesConfirmed' => $sprintTrackMovesConfirmed,
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'sprint');
        $aid = (int) $this->game->getActivePlayerId();
        // На входе состояния сбрасываем подтверждения для текущего активного игрока в рамках текущего раунда.
        $round = (int) $this->game->getGameStateValue('round_number');
        $this->game->globals->delete('sprint_bank_confirmed_' . $aid);
        $this->game->globals->delete('sprint_track_moves_done_' . $aid);
        $this->game->globals->set('sprint_bank_confirmed_round_' . $aid, (string) $round);
        $this->game->globals->set('sprint_track_moves_done_round_' . $aid, (string) $round);

        return null;
    }

    /**
     * Взять задачи из банка в бэклог (не более лимита по треку sprint-column-tasks). Один раз за фазу.
     */
    #[PossibleAction]
    public function actConfirmSprintTasks(string $selectedTasksJson): void
    {
        $this->game->checkAction('actConfirmSprintTasks');
        $playerId = (int) $this->game->getActivePlayerId();
        $round = (int) $this->game->getGameStateValue('round_number');

        if ($this->game->globals->get('sprint_bank_confirmed_' . $playerId, '') === '1') {
            throw new UserException(clienttranslate('Вы уже брали задачи из банка в этой фазе'));
        }

        $limit = $this->game->getSprintColumnTasksTakeLimit($playerId);
        $selected = json_decode($selectedTasksJson, true);
        if (!is_array($selected)) {
            throw new UserException(clienttranslate('Неверный формат выбранных задач'));
        }

        $total = 0;
        foreach ($selected as $task) {
            if (!is_array($task)) {
                continue;
            }
            $q = (int) ($task['quantity'] ?? 0);
            if ($q < 0) {
                throw new UserException(clienttranslate('Количество задач не может быть отрицательным'));
            }
            $total += $q;
        }

        if ($total > $limit) {
            throw new UserException(clienttranslate('Слишком много задач для этой фазы'));
        }

        $addedTokens = [];
        if ($total > 0) {
            $addedTokens = $this->game->addTaskTokens($playerId, $selected, 'backlog');
        }

        $this->game->globals->set('sprint_bank_confirmed_' . $playerId, '1');
        $this->game->globals->set('sprint_bank_confirmed_round_' . $playerId, (string) $round);

        $this->notify->all('tasksSelected', clienttranslate('${player_name} берёт задачи из банка в бэклог'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'founder_name' => '—',
            'selected_tasks' => $selected,
            'added_tokens' => $addedTokens,
            'sprint_phase' => true,
            'i18n' => [],
        ]);
    }

    /**
     * Перемещение задач по треку спринта в фазе «Спринт»: расстояние по колонкам не больше значения
     * техотдела для цвета жетона (techDevCol1–4).
     */
    #[PossibleAction]
    public function actConfirmTaskMoves(int $activePlayerId, string $movesJson): void
    {
        $this->game->checkAction('actConfirmTaskMoves');
        $playerId = (int) $activePlayerId;
        if ($playerId !== (int) $this->game->getActivePlayerId()) {
            throw new UserException(clienttranslate('Не ваш ход'));
        }
        $round = (int) $this->game->getGameStateValue('round_number');

        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }

        foreach ($moves as $move) {
            if (!is_array($move)) {
                continue;
            }
            $this->game->validateSprintPhaseTaskMove($playerId, $move);
        }

        $movedTokens = [];
        foreach ($moves as $move) {
            if (!is_array($move)) {
                continue;
            }
            $tokenId = (int) ($move['tokenId'] ?? 0);
            $toLocation = strtolower(trim((string) ($move['toLocation'] ?? '')));
            $fromLocation = strtolower(trim((string) ($move['fromLocation'] ?? '')));
            if (
                $tokenId > 0
                && $toLocation !== ''
                && $fromLocation !== ''
                && $fromLocation !== $toLocation
                && $this->game->updateTaskTokenLocation($tokenId, $toLocation, null)
            ) {
                $movedTokens[] = $tokenId;
            }
        }

        // Подтверждение снимает блокировку завершения фазы, даже если ходов не было
        // (например, по техотделу 0 шагов или игрок не смог сделать ход).
        $this->game->globals->set('sprint_track_moves_done_' . $playerId, '1');
        $this->game->globals->set('sprint_track_moves_done_round_' . $playerId, (string) $round);

        $this->notify->all('taskMovesCompleted', clienttranslate('${player_name} перемещает задачи по спринту'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'founder_name' => '',
            'moves' => $moves,
            'moved_tokens' => $movedTokens,
            'sprint_phase' => true,
            'i18n' => [],
        ]);
    }

    #[PossibleAction]
    public function actCompleteSprintPhase(): string
    {
        $this->game->checkAction('actCompleteSprintPhase');
        $playerId = (int) $this->game->getActivePlayerId();

        if ($this->game->playerMustSprintTrackMoveBeforeComplete($playerId)) {
            if ($this->game->globals->get('sprint_track_moves_done_' . $playerId, '') !== '1') {
                throw new UserException(clienttranslate('Сначала переместите задачи по треку спринта и нажмите «Подтвердить перемещения».'));
            }
        }

        $this->game->savePlayerGameDataOnTurnEnd($playerId);
        $this->game->globals->set('sprint_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    public function zombie(int $playerId): string
    {
        $this->game->globals->set('sprint_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
