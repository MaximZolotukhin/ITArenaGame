<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SkillsData;
use Bga\Games\itarenagame\TaskTokensData;

/**
 * Фаза «Навыки» (Skills) раунда.
 * Игрок выбирает навык (жетон из round-panel__skill-token-column размещает в round-panel__skill-column).
 * Навык сохраняется на раунд и применяется эффект.
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
            description: clienttranslate('${actplayer} must choose a skill for this round'),
            descriptionMyTurn: clienttranslate('${you} must choose a skill for this round'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $taskTokenColors = [];
        foreach (TaskTokensData::getAllColors() as $id => $color) {
            $taskTokenColors[] = [
                'id' => $id,
                'name' => $color['name'] ?? $id,
            ];
        }
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        $activeSkillKey = $gameData['skillToken'] ?? null;
        $skillEffectPending = false;
        $skillEffectHint = '';
        if ($activeSkillKey === SkillsData::SKILL_INTELLECT) {
            $pendingJson = $this->game->globals->get('pending_task_moves_' . $activePlayerId, '');
            if ($pendingJson !== '' && $pendingJson !== null) {
                $skillEffectPending = true;
                $skillEffectHint = clienttranslate('Примените эффект: передвиньте задачи на треке и нажмите «Подтвердить»');
            }
        }
        // Ячейки навыков, уже занятые другими игроками в этом раунде (текущий не может их выбрать)
        $occupiedSkillKeys = [];
        foreach (array_keys($this->game->loadPlayersBasicInfos()) as $pid) {
            if ((int) $pid === $activePlayerId) {
                continue;
            }
            $otherData = $this->game->getPlayerGameData((int) $pid);
            if ($otherData === null) {
                continue;
            }
            $tok = $otherData['skillToken'] ?? null;
            if ($tok !== null && $tok !== '') {
                $occupiedSkillKeys[] = $tok;
            }
        }
        return [
            'phaseKey' => 'skills',
            'phaseName' => $this->game->getPhaseName('skills'),
            'skillOptions' => SkillsData::getSkillsForSelection(),
            'taskTokenColors' => $taskTokenColors,
            'skillEffectPending' => $skillEffectPending,
            'skillEffectHint' => $skillEffectHint,
            'occupiedSkillKeys' => $occupiedSkillKeys,
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
     * Игрок выбрал навык: сохраняем, применяем эффект. Ход не передаётся — игрок нажимает «Завершить фазу навыков».
     * Для навыка «Дисциплина» обязателен параметр taskColor (cyan, pink, orange, purple).
     */
    #[PossibleAction]
    public function actSelectSkill(string $skillKey, ?string $taskColor = null): ?string
    {
        $this->game->checkAction('actSelectSkill');
        $playerId = (int)$this->game->getActivePlayerId();
        $gameData = $this->game->getPlayerGameData($playerId);
        if ($gameData && ($gameData['skillToken'] ?? null) !== null && $gameData['skillToken'] !== '') {
            throw new UserException(clienttranslate('You have already chosen a skill for this round'));
        }
        if (!SkillsData::isValidKey($skillKey)) {
            throw new UserException(clienttranslate('Invalid skill selected'));
        }
        // Ячейка на треке навыков уже занята другим игроком — выбирать нельзя
        $occupiedSkillKeys = [];
        foreach (array_keys($this->game->loadPlayersBasicInfos()) as $pid) {
            if ((int) $pid === $playerId) {
                continue;
            }
            $otherData = $this->game->getPlayerGameData((int) $pid);
            if ($otherData === null) {
                continue;
            }
            $tok = $otherData['skillToken'] ?? null;
            if ($tok !== null && $tok !== '') {
                $occupiedSkillKeys[] = $tok;
            }
        }
        if (in_array($skillKey, $occupiedSkillKeys, true)) {
            throw new UserException(clienttranslate('This skill slot is already taken by another player'));
        }
        $skill = SkillsData::getSkill($skillKey);

        // Дисциплина: один жетон задачи в бэклог, игрок выбирает цвет
        if ($skillKey === SkillsData::SKILL_DISCIPLINE) {
            $validColors = TaskTokensData::getColorIds();
            if ($taskColor === null || $taskColor === '' || !in_array($taskColor, $validColors, true)) {
                throw new UserException(clienttranslate('Please choose a task color for the Discipline skill'));
            }
            $addedTokens = $this->game->addTaskTokens($playerId, [['color' => $taskColor, 'quantity' => 1]], 'backlog');
            $this->notify->all('skillTaskTokenAdded', clienttranslate('${player_name} gets 1 task token (${color_name}) in backlog'), [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'color' => $taskColor,
                'color_name' => TaskTokensData::getColor($taskColor)['name'] ?? $taskColor,
                'added_tokens' => $addedTokens,
                'i18n' => ['color_name'],
            ]);
        }

        $effectResults = $this->game->applySkillEffects($playerId, $skillKey);

        // Уведомление о картах (Красноречие)
        foreach ($effectResults as $result) {
            if (isset($result['type']) && $result['type'] === 'card' && !empty($result['cardIds'])) {
                $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} gets ${amount} specialist card(s) from skill'), [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'cardIds' => $result['cardIds'],
                    'amount' => count($result['cardIds']),
                ]);
                break;
            }
        }

        // Уведомление о перемещении задач (Интеллект) — та же механика, что у карт основателей
        foreach ($effectResults as $result) {
            if (isset($result['type']) && $result['type'] === 'move_task' && ($result['move_count'] ?? 0) > 0) {
                $this->notify->player($playerId, 'taskMovesRequired', '', [
                    'player_id' => $playerId,
                    'move_count' => (int)$result['move_count'],
                    'move_color' => $result['move_color'] ?? 'any',
                    'founder_name' => $skill['name'] ?? clienttranslate('Интеллект'),
                ]);
                break;
            }
        }

        // Уведомление о баджерсах (Бережливость) — всегда отправляем с skill_name и skill_description из SkillsData
        foreach ($effectResults as $result) {
            if (isset($result['type']) && $result['type'] === 'badger') {
                $badgersSupply = $this->game->getBadgersSupply();
                $amount = (int)($result['amount'] ?? 0);
                $this->notify->all('badgersChanged', clienttranslate('${player_name} ${action_text} ${amount}Б благодаря навыку «${skill_name}»'), [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'action_text' => $amount > 0 ? clienttranslate('получает') : clienttranslate('теряет'),
                    'amount' => abs($amount),
                    'skill_key' => $skillKey,
                    'skill_name' => $skill['name'] ?? clienttranslate('Бережливость'),
                    'skill_description' => $skill['description'] ?? clienttranslate('Получите 3 баджерса'),
                    'oldValue' => $result['oldValue'] ?? 0,
                    'newValue' => $result['newValue'] ?? 0,
                    'badgersSupply' => $badgersSupply,
                    'i18n' => ['action_text', 'skill_name', 'skill_description'],
                ]);
                break;
            }
        }

        $this->notify->all('skillSelected', clienttranslate('${player_name} chose skill: ${skill_name}'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'skill_key' => $skillKey,
            'skill_name' => $skill['name'] ?? $skillKey,
            'i18n' => ['skill_name'],
        ]);
        return null; // остаёмся в RoundSkills до нажатия «Завершить фазу навыков»
    }

    /**
     * Игрок завершил фазу навыков без выбора (пропуск).
     */
    #[PossibleAction]
    public function actCompleteSkillsPhase(): string
    {
        $this->game->checkAction('actCompleteSkillsPhase');
        $playerId = (int) $this->game->getActivePlayerId();
        $gameData = $this->game->getPlayerGameData($playerId);
        $skillKey = $gameData['skillToken'] ?? null;
        if ($skillKey === SkillsData::SKILL_INTELLECT) {
            $pendingJson = $this->game->globals->get('pending_task_moves_' . $playerId, '');
            if ($pendingJson !== '' && $pendingJson !== null) {
                throw new UserException(clienttranslate('Сначала примените эффект навыка: передвиньте задачи на треке и нажмите «Подтвердить»'));
            }
        }
        $this->game->globals->set('skills_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    /**
     * Подтверждение перемещений задач (навык «Интеллект») — та же механика, что у карт основателей.
     *
     * @param int $activePlayerId ID активного игрока
     * @param string $movesJson JSON строка с массивом перемещений
     */
    #[PossibleAction]
    public function actConfirmTaskMoves(int $activePlayerId, string $movesJson): ?string
    {
        $this->game->checkAction('actConfirmTaskMoves');

        $globalsKey = 'pending_task_moves_' . $activePlayerId;
        $pendingMovesJson = $this->game->globals->get($globalsKey, null);

        if ($pendingMovesJson === null) {
            $moves = json_decode($movesJson, true);
            if (is_array($moves) && count($moves) > 0) {
                $totalBlocks = 0;
                foreach ($moves as $move) {
                    if (is_array($move) && isset($move['blocks'])) {
                        $totalBlocks += (int)$move['blocks'];
                    }
                }
                if ($totalBlocks > 0) {
                    $pendingMovesData = [
                        'move_count' => $totalBlocks,
                        'move_color' => 'any',
                        'used_moves' => 0,
                        'founder_id' => 0,
                        'founder_name' => clienttranslate('Интеллект'),
                    ];
                    $pendingMovesJson = json_encode($pendingMovesData);
                    $this->game->globals->set($globalsKey, $pendingMovesJson);
                } else {
                    throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
                }
            } else {
                throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
            }
        }

        $pendingMoves = json_decode($pendingMovesJson, true);
        if (!is_array($pendingMoves) || !isset($pendingMoves['move_count'])) {
            throw new UserException(clienttranslate('Неверные данные ожидающих перемещений'));
        }

        $requiredMoves = (int)$pendingMoves['move_count'];
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }

        $totalBlocks = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['tokenId']) || !isset($move['toLocation'])) {
                continue;
            }
            $totalBlocks += (int)($move['blocks'] ?? 0);
        }

        // Принимаем, если использованы все ходы ИЛИ на треке не было больше задач для перемещения
        $maxBlocksAvailable = $this->game->getMaxTaskMoveBlocksForPlayer($activePlayerId);
        $allRequiredUsed = ($totalBlocks === $requiredMoves);
        $noMoreAvailable = ($maxBlocksAvailable <= $totalBlocks && $totalBlocks <= $requiredMoves);
        if (!$allRequiredUsed && !$noMoreAvailable) {
            throw new UserException(clienttranslate('Вы должны использовать ровно ${amount} ходов или переместить все доступные задачи на треке', [
                'amount' => $requiredMoves,
            ]));
        }

        $movedTokens = [];
        foreach ($moves as $move) {
            $tokenId = (int)($move['tokenId'] ?? 0);
            $toLocation = $move['toLocation'] ?? '';
            if ($tokenId > 0 && $toLocation !== '') {
                $success = $this->game->updateTaskTokenLocation($tokenId, $toLocation, null);
                if ($success) {
                    $movedTokens[] = $tokenId;
                }
            }
        }

        $this->game->globals->set($globalsKey, null);

        $sourceName = $pendingMoves['founder_name'] ?? clienttranslate('Интеллект');
        $this->notify->all('taskMovesCompleted', clienttranslate('${player_name} переместил задачи от навыка ${source_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $sourceName,
            'source_name' => $sourceName,
            'moves' => $moves,
            'moved_tokens' => $movedTokens,
            'i18n' => ['founder_name', 'source_name'],
        ]);

        return null; // остаёмся в RoundSkills до «Завершить фазу навыков»
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
