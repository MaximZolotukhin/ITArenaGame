<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\ProjectTokensData;

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
    private const PROJECTS_PASS_AFTER_TECH_KEY_PREFIX = 'proj_pass_after_td_';
    private const SQUARE_PROJECT_TASK_BONUS_SOURCE = 'square_project_unique_colors';
    private const HEX_PROJECT_TASK_MOVES_SOURCE = 'hex_project_unique_colors';

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

        // Количество выполненных задач активного игрока по цветам —
        // нужно UI для подсветки доступных IT-проектов.
        $completedByColor = $this->game->getCompletedTaskTokensCountByColor($activePlayerId);

        // Список ID жетонов IT-проектов, которые активный игрок может купить
        // в текущий момент (цвет совпадает, выполненных задач хватает).
        $purchasableTokenIds = [];
        $effectiveProjectPrices = [];
        foreach ($this->game->getProjectTokensOnBoard() as $token) {
            $tid = (int) ($token['token_id'] ?? 0);
            if ($tid <= 0) {
                continue;
            }
            $color = strtolower(trim((string) ($token['color'] ?? '')));
            $price = $this->game->getEffectiveProjectTokenPriceForPlayer($activePlayerId, $token);
            $effectiveProjectPrices[$tid] = $price;
            if ($color !== '' && $price > 0 && ($completedByColor[$color] ?? 0) >= $price) {
                $purchasableTokenIds[] = $tid;
            }
        }

        return [
            'phaseKey' => 'projects',
            'phaseName' => $this->game->getPhaseName('projects'),
            'activePlayerId' => $activePlayerId,
            'donePlayers' => $donePlayers,
            'canPass' => $canPass,
            'badgers' => $this->game->getPlayerBadgersForCheck($activePlayerId),
            'completedByColor' => $completedByColor,
            'purchasableTokenIds' => $purchasableTokenIds,
            'effectiveProjectPrices' => $effectiveProjectPrices,
            'pendingTaskSelection' => $this->getPendingTaskSelection($activePlayerId),
            'pendingTaskMoves' => $this->getPendingTaskMoves($activePlayerId),
            'pendingTechnicalDevelopmentMoves' => $this->game->getPendingTechnicalDevelopmentMovesData($activePlayerId),
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'projects');
        $this->game->applyRoundEventEffectsForPhase('projects');
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
        $playerId = (int) $this->game->getActivePlayerId();
        $this->assertNoPendingTaskSelection($playerId);
        $this->assertNoPendingTaskMoves($playerId);
        $this->game->assertNoPendingTechnicalDevelopmentMoves($playerId);
        $this->game->globals->set('projects_phase_pass', '1');
        return 'toNextPlayer';
    }

    /**
     * Покупка IT-проекта. Списывает с активного игрока выполненные задачи
     * совпадающего цвета (стоимость указана в самом жетоне проекта) и переводит
     * жетон проекта в `player_project_token` с локацией 'completed'.
     *
     * Проверки выполняются в Game::canPlayerPurchaseProjectToken / purchaseProjectToken;
     * при невозможности покупки выбрасывается UserException.
     *
     * После покупки ход передаётся следующему игроку в очереди проектов.
     * Если эффект проекта требует выбора трека техотдела, переход выполняется
     * после подтверждения этого выбора.
     */
    #[PossibleAction]
    public function actPurchaseProject(int $tokenId): ?string
    {
        $this->game->checkAction('actPurchaseProject');
        $playerId = (int) $this->game->getActivePlayerId();
        $this->assertNoPendingTaskSelection($playerId);
        $this->assertNoPendingTaskMoves($playerId);
        $this->game->assertNoPendingTechnicalDevelopmentMoves($playerId);
        $result = $this->game->purchaseProjectToken($playerId, $tokenId);

        $token = $result['token'];
        $appliedEffects = array_merge(
            $result['applied_effects'] ?? [],
            $this->game->applyFounderEffectsForStage($playerId, 'ProjectsPhase'),
        );
        $pendingTech = $this->game->setupPendingTechnicalDevelopmentFromApplied(
            $playerId,
            $appliedEffects,
            (string) $token['name'],
        );
        $projectShapeColorBonus = is_array($result['project_shape_color_bonus'] ?? null)
            ? $result['project_shape_color_bonus']
            : null;
        $bonusShape = (string) ($projectShapeColorBonus['shape'] ?? '');
        $pendingProjectTaskSelection = null;
        $pendingProjectTaskMoves = null;
        if ($projectShapeColorBonus !== null && $bonusShape === 'square' && (int) ($projectShapeColorBonus['amount'] ?? 0) > 0) {
            $pendingProjectTaskSelection = [
                'amount' => (int) $projectShapeColorBonus['amount'],
                'founder_name' => (string) ($projectShapeColorBonus['source_name'] ?? clienttranslate('бонус квадратных IT-проектов')),
                'source' => self::SQUARE_PROJECT_TASK_BONUS_SOURCE,
                'triggered_counts' => $projectShapeColorBonus['triggered_counts'] ?? [],
                'unique_color_count' => (int) ($projectShapeColorBonus['unique_color_count'] ?? 0),
            ];
            $this->game->globals->set(
                'pending_task_selection_' . $playerId,
                json_encode($pendingProjectTaskSelection, JSON_UNESCAPED_UNICODE),
            );
        }
        if ($projectShapeColorBonus !== null && $bonusShape === 'hex' && (int) ($projectShapeColorBonus['amount'] ?? 0) > 0) {
            $pendingProjectTaskMoves = [
                'move_count' => (int) $projectShapeColorBonus['amount'],
                'move_color' => 'any',
                'used_moves' => 0,
                'founder_id' => 0,
                'founder_name' => (string) ($projectShapeColorBonus['source_name'] ?? clienttranslate('бонус hex IT-проектов')),
                'source' => self::HEX_PROJECT_TASK_MOVES_SOURCE,
                'triggered_counts' => $projectShapeColorBonus['triggered_counts'] ?? [],
                'unique_color_count' => (int) ($projectShapeColorBonus['unique_color_count'] ?? 0),
            ];
            $this->game->globals->set(
                'pending_task_moves_' . $playerId,
                json_encode($pendingProjectTaskMoves, JSON_UNESCAPED_UNICODE),
            );
        }
        $circleProjectBadgerBonus = null;
        if ($projectShapeColorBonus !== null && $bonusShape === 'circle' && (int) ($projectShapeColorBonus['amount'] ?? 0) > 0) {
            $oldBadgers = $this->game->getPlayerBadgersForCheck($playerId);
            $amount = (int) $projectShapeColorBonus['amount'];
            $this->game->addPlayerBadgers($playerId, $amount);
            $newBadgers = $this->game->getPlayerBadgersForCheck($playerId);
            $circleProjectBadgerBonus = [
                'amount' => $amount,
                'oldValue' => $oldBadgers,
                'newValue' => $newBadgers,
                'source_name' => (string) ($projectShapeColorBonus['source_name'] ?? clienttranslate('бонус круглых IT-проектов')),
            ];
            $this->notify->all(
                'badgersChanged',
                clienttranslate('${player_name} получает ${amount}Б благодаря эффекту «${source_name}»'),
                [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'action_text' => clienttranslate('получает'),
                    'amount' => $amount,
                    'source_name' => $circleProjectBadgerBonus['source_name'],
                    'oldValue' => $oldBadgers,
                    'newValue' => $newBadgers,
                    'badgersSupply' => $this->game->getBadgersSupply(),
                    'i18n' => ['action_text', 'source_name'],
                ],
            );
        }
        foreach ($appliedEffects as $effect) {
            if (($effect['type'] ?? '') !== 'badger') {
                continue;
            }
            $amount = (int) ($effect['amount'] ?? 0);
            if ($amount === 0) {
                continue;
            }
            $sourceName = (string) ($effect['founderName'] ?? $token['name'] ?? clienttranslate('IT-проект'));
            $this->notify->all(
                'badgersChanged',
                clienttranslate('${player_name} ${action_text} ${amount}Б благодаря эффекту «${source_name}»'),
                [
                    'player_id' => $playerId,
                    'player_name' => $this->game->getPlayerNameById($playerId),
                    'action_text' => $amount > 0 ? clienttranslate('получает') : clienttranslate('теряет'),
                    'amount' => abs($amount),
                    'source_name' => $sourceName,
                    'oldValue' => $effect['oldValue'] ?? 0,
                    'newValue' => $effect['newValue'] ?? 0,
                    'badgersSupply' => $this->game->getBadgersSupply(),
                    'i18n' => ['action_text', 'source_name'],
                ],
            );
        }
        $turnPassesAfterPurchase = $pendingTech === null && $pendingProjectTaskSelection === null && $pendingProjectTaskMoves === null;
        $effectiveProjectPrices = [];
        foreach (($result['projectTokensOnBoard'] ?? []) as $projectTokenOnBoard) {
            $tid = (int) ($projectTokenOnBoard['token_id'] ?? 0);
            if ($tid <= 0) {
                continue;
            }
            $effectiveProjectPrices[$tid] = $this->game->getEffectiveProjectTokenPriceForPlayer(
                $playerId,
                $projectTokenOnBoard,
            );
        }
        $this->game->notify->all(
            'projectTokenPurchased',
            clienttranslate('${player_name} покупает IT-проект «${project_name}» за ${price} ${color_name}'),
            [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'project_name' => $token['name'],
                'price' => (int) ($result['purchase_price'] ?? $token['price']),
                'originalPrice' => (int) ($result['original_price'] ?? $token['price']),
                'color' => $token['color'],
                'color_name' => $this->colorDisplayName((string) $token['color']),
                'tokenId' => (int) $token['token_id'],
                'boardPosition' => $result['boardPosition'] ?? $token['board_position'],
                'projectToken' => $token,
                'spentTaskTokenIds' => $result['spent_task_token_ids'],
                'completedByColor' => $result['completed_by_color'],
                'effectiveProjectPrices' => $effectiveProjectPrices,
                'projectTokensOnBoard' => $result['projectTokensOnBoard'],
                'playerProjectTokens' => $result['playerProjectTokens'],
                // Новый жетон, выложенный на освободившуюся ячейку (или null,
                // если запас исчерпан и ячейка осталась пустой).
                'replacementToken' => $result['replacementToken'] ?? null,
                'appliedEffects' => $appliedEffects,
                'badgersSupply' => $this->game->getBadgersSupply(),
                'hasPendingTechnicalDevelopment' => $pendingTech !== null,
                'hasPendingProjectTaskSelection' => $pendingProjectTaskSelection !== null,
                'hasPendingProjectTaskMoves' => $pendingProjectTaskMoves !== null,
                'projectShapeColorBonus' => $projectShapeColorBonus,
                'circleProjectBadgerBonus' => $circleProjectBadgerBonus,
                'squareProjectColorBonus' => ($projectShapeColorBonus['shape'] ?? null) === 'square' ? $projectShapeColorBonus : null,
                'purchaseEndsTurn' => $turnPassesAfterPurchase,
                'i18n' => ['project_name', 'color_name'],
            ],
        );

        if ($pendingProjectTaskSelection !== null) {
            $this->notify->player($playerId, 'taskSelectionRequired', '', [
                'player_id' => $playerId,
                'amount' => (int) $pendingProjectTaskSelection['amount'],
                'founder_name' => $pendingProjectTaskSelection['founder_name'],
                'source' => self::SQUARE_PROJECT_TASK_BONUS_SOURCE,
                'triggered_counts' => $pendingProjectTaskSelection['triggered_counts'],
                'unique_color_count' => $pendingProjectTaskSelection['unique_color_count'],
                'i18n' => ['founder_name'],
            ]);
        }

        if ($pendingProjectTaskMoves !== null) {
            $this->notify->player($playerId, 'taskMovesRequired', '', [
                'player_id' => $playerId,
                'move_count' => (int) $pendingProjectTaskMoves['move_count'],
                'move_color' => 'any',
                'founder_name' => $pendingProjectTaskMoves['founder_name'],
                'source' => self::HEX_PROJECT_TASK_MOVES_SOURCE,
                'triggered_counts' => $pendingProjectTaskMoves['triggered_counts'],
                'unique_color_count' => $pendingProjectTaskMoves['unique_color_count'],
                'i18n' => ['founder_name'],
            ]);
        }

        if ($pendingTech !== null && $pendingProjectTaskSelection === null && $pendingProjectTaskMoves === null) {
            $sourceName = $pendingTech['source_name'];
            $this->game->globals->set(self::PROJECTS_PASS_AFTER_TECH_KEY_PREFIX . $playerId, '1');
            $this->notify->player($playerId, 'technicalDevelopmentMovesRequired', '', [
                'player_id' => $playerId,
                'move_count' => $pendingTech['move_count'],
                'founder_name' => $sourceName,
                'source_name' => $sourceName,
            ]);
        }

        if ($turnPassesAfterPurchase) {
            $this->game->globals->set('projects_phase_pass', '1');
            return 'toNextPlayer';
        }

        return null;
    }

    /**
     * Подтверждение выбора 3 задач в бэклог за бонус уникальных цветов квадратных IT-проектов.
     */
    #[PossibleAction]
    public function actConfirmTaskSelection(string $selectedTasksJson): ?string
    {
        $this->game->checkAction('actConfirmTaskSelection');
        $playerId = (int) $this->game->getActivePlayerId();
        $pendingSelection = $this->getPendingTaskSelection($playerId);
        if ($pendingSelection === null) {
            throw new UserException(clienttranslate('Нет ожидающего выбора задач'));
        }

        $requiredAmount = (int) $pendingSelection['amount'];
        $selectedTasks = json_decode($selectedTasksJson, true);
        if (!is_array($selectedTasks)) {
            throw new UserException(clienttranslate('Неверный формат данных выбранных задач'));
        }

        $normalizedTasks = [];
        $totalSelected = 0;
        foreach ($selectedTasks as $task) {
            if (!is_array($task)) {
                continue;
            }

            $color = strtolower(trim((string) ($task['color'] ?? '')));
            if ($color === 'cayn') {
                $color = 'cyan';
            }
            $quantity = (int) ($task['quantity'] ?? 0);
            if ($quantity < 0) {
                throw new UserException(clienttranslate('Количество задач не может быть отрицательным'));
            }
            if ($quantity === 0) {
                continue;
            }
            if (!ProjectTokensData::isValidItProjectColor($color)) {
                throw new UserException(clienttranslate('Некорректный цвет задачи'));
            }

            $normalizedTasks[] = [
                'color' => $color,
                'quantity' => $quantity,
            ];
            $totalSelected += $quantity;
        }

        if ($totalSelected !== $requiredAmount) {
            throw new UserException(clienttranslate('Вы должны выбрать ровно ${amount} задач', [
                'amount' => $requiredAmount,
            ]));
        }

        $addedTokens = $this->game->addTaskTokens($playerId, $normalizedTasks, 'backlog');
        $this->game->globals->set('pending_task_selection_' . $playerId, null);

        $sourceName = (string) ($pendingSelection['founder_name'] ?? clienttranslate('бонус квадратных IT-проектов'));
        $this->notify->all('tasksSelected', clienttranslate('${player_name} выбрал ${amount} задач от эффекта ${founder_name}'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'amount' => $totalSelected,
            'founder_name' => $sourceName,
            'selected_tasks' => $normalizedTasks,
            'added_tokens' => $addedTokens,
            'project_bonus' => true,
            'source' => self::SQUARE_PROJECT_TASK_BONUS_SOURCE,
            'i18n' => ['founder_name'],
        ]);

        $pendingTech = $this->game->getPendingTechnicalDevelopmentMovesData($playerId);
        if ($pendingTech !== null) {
            $techSourceName = (string) ($pendingTech['source_name'] ?? $pendingTech['founder_name'] ?? $sourceName);
            $this->game->globals->set(self::PROJECTS_PASS_AFTER_TECH_KEY_PREFIX . $playerId, '1');
            $this->notify->player($playerId, 'technicalDevelopmentMovesRequired', '', [
                'player_id' => $playerId,
                'move_count' => (int) ($pendingTech['move_count'] ?? 0),
                'founder_name' => $techSourceName,
                'source_name' => $techSourceName,
            ]);
            return null;
        }

        $this->game->globals->set('projects_phase_pass', '1');
        return 'toNextPlayer';
    }

    /**
     * Подтверждение 3 перемещений задач за бонус уникальных цветов hex IT-проектов.
     */
    #[PossibleAction]
    public function actConfirmTaskMoves(int $activePlayerId, string $movesJson): ?string
    {
        $this->game->checkAction('actConfirmTaskMoves');
        $playerId = (int) $activePlayerId;
        if ($playerId !== (int) $this->game->getActivePlayerId()) {
            throw new UserException(clienttranslate('Не ваш ход'));
        }

        $pendingMoves = $this->getPendingTaskMoves($playerId);
        if ($pendingMoves === null) {
            throw new UserException(clienttranslate('Нет ожидающих перемещений задач'));
        }

        $requiredMoves = (int) $pendingMoves['move_count'];
        $moves = json_decode($movesJson, true);
        if (!is_array($moves)) {
            throw new UserException(clienttranslate('Неверный формат данных перемещений'));
        }

        $totalBlocks = 0;
        foreach ($moves as $move) {
            if (!is_array($move) || !isset($move['tokenId']) || !isset($move['toLocation'])) {
                continue;
            }
            $this->validateTaskMove($playerId, $move);
            $totalBlocks += (int) ($move['blocks'] ?? 0);
        }

        $maxBlocksAvailable = $this->game->getMaxTaskMoveBlocksForPlayer($playerId, 'any');
        $allRequiredUsed = $totalBlocks === $requiredMoves;
        $noMoreAvailable = ($maxBlocksAvailable === 0 && $totalBlocks === 0)
            || ($maxBlocksAvailable <= $totalBlocks && $totalBlocks <= $requiredMoves);
        if (!$allRequiredUsed && !$noMoreAvailable) {
            throw new UserException(clienttranslate('Вы должны использовать до ${amount} ходов или переместить все доступные задачи на треке', [
                'amount' => $requiredMoves,
            ]));
        }

        $movedTokens = [];
        foreach ($moves as $move) {
            $tokenId = (int) ($move['tokenId'] ?? 0);
            $toLocation = (string) ($move['toLocation'] ?? '');
            if ($tokenId > 0 && $toLocation !== '') {
                $success = $this->game->updateTaskTokenLocation($tokenId, $toLocation, null);
                if ($success) {
                    $movedTokens[] = $tokenId;
                }
            }
        }

        $this->game->globals->set('pending_task_moves_' . $playerId, null);
        $sourceName = (string) ($pendingMoves['founder_name'] ?? clienttranslate('бонус hex IT-проектов'));
        $this->notify->all('taskMovesCompleted', clienttranslate('${player_name} переместил задачи от эффекта ${founder_name}'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'founder_name' => $sourceName,
            'source_name' => $sourceName,
            'moves' => $moves,
            'moved_tokens' => $movedTokens,
            'project_bonus' => true,
            'source' => self::HEX_PROJECT_TASK_MOVES_SOURCE,
            'i18n' => ['founder_name', 'source_name'],
        ]);

        $pendingTech = $this->game->getPendingTechnicalDevelopmentMovesData($playerId);
        if ($pendingTech !== null) {
            $techSourceName = (string) ($pendingTech['source_name'] ?? $pendingTech['founder_name'] ?? $sourceName);
            $this->game->globals->set(self::PROJECTS_PASS_AFTER_TECH_KEY_PREFIX . $playerId, '1');
            $this->notify->player($playerId, 'technicalDevelopmentMovesRequired', '', [
                'player_id' => $playerId,
                'move_count' => (int) ($pendingTech['move_count'] ?? 0),
                'founder_name' => $techSourceName,
                'source_name' => $techSourceName,
            ]);
            return null;
        }

        $this->game->globals->set('projects_phase_pass', '1');
        return 'toNextPlayer';
    }

    /**
     * Подтверждение улучшения техотдела после покупки IT-проекта с эффектом «выбор колонки».
     */
    #[PossibleAction]
    public function actConfirmTechnicalDevelopmentMoves(int $activePlayerId, string $movesJson): ?string
    {
        $this->game->checkAction('actConfirmTechnicalDevelopmentMoves');
        $playerId = (int) $activePlayerId;
        if ($playerId !== (int) $this->game->getActivePlayerId()) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Не ваш ход'));
        }
        $this->assertNoPendingTaskSelection($playerId);
        $this->assertNoPendingTaskMoves($playerId);
        $this->game->confirmTechnicalDevelopmentMoves($playerId, $movesJson);

        $passAfterTechKey = self::PROJECTS_PASS_AFTER_TECH_KEY_PREFIX . $playerId;
        if ($this->game->globals->get($passAfterTechKey, '') === '1') {
            $this->game->globals->delete($passAfterTechKey);
            $this->game->globals->set('projects_phase_pass', '1');
            return 'toNextPlayer';
        }

        return null;
    }

    /**
     * Возвращает локализованное название цвета (для логов уведомления).
     */
    private function colorDisplayName(string $color): string
    {
        switch (strtolower(trim($color))) {
            case 'cyan':   return clienttranslate('голубой');
            case 'pink':   return clienttranslate('розовый');
            case 'orange': return clienttranslate('оранжевый');
            case 'purple': return clienttranslate('фиолетовый');
            default:       return $color;
        }
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
        $this->assertNoPendingTaskSelection($playerId);
        $this->assertNoPendingTaskMoves($playerId);
        $this->game->assertNoPendingTechnicalDevelopmentMoves($playerId);
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

    private function getPendingTaskSelection(int $playerId): ?array
    {
        $pendingJson = $this->game->globals->get('pending_task_selection_' . $playerId, null);
        if ($pendingJson === null || $pendingJson === '') {
            return null;
        }

        $pending = json_decode((string) $pendingJson, true);
        if (!is_array($pending) || !isset($pending['amount']) || (int) $pending['amount'] <= 0) {
            return null;
        }

        return [
            'amount' => (int) $pending['amount'],
            'founder_name' => (string) ($pending['founder_name'] ?? ''),
            'source' => (string) ($pending['source'] ?? ''),
            'triggered_counts' => is_array($pending['triggered_counts'] ?? null) ? $pending['triggered_counts'] : [],
            'unique_color_count' => (int) ($pending['unique_color_count'] ?? 0),
        ];
    }

    private function assertNoPendingTaskSelection(int $playerId): void
    {
        $pendingSelection = $this->getPendingTaskSelection($playerId);
        if ($pendingSelection === null) {
            return;
        }

        throw new UserException(clienttranslate('Сначала выберите задачи в бэклог за бонус IT-проектов'));
    }

    private function getPendingTaskMoves(int $playerId): ?array
    {
        $pendingJson = $this->game->globals->get('pending_task_moves_' . $playerId, null);
        if ($pendingJson === null || $pendingJson === '') {
            return null;
        }

        $pending = json_decode((string) $pendingJson, true);
        if (!is_array($pending) || !isset($pending['move_count']) || (int) $pending['move_count'] <= 0) {
            return null;
        }

        return [
            'move_count' => (int) $pending['move_count'],
            'move_color' => (string) ($pending['move_color'] ?? 'any'),
            'founder_name' => (string) ($pending['founder_name'] ?? ''),
            'source' => (string) ($pending['source'] ?? ''),
            'triggered_counts' => is_array($pending['triggered_counts'] ?? null) ? $pending['triggered_counts'] : [],
            'unique_color_count' => (int) ($pending['unique_color_count'] ?? 0),
        ];
    }

    private function assertNoPendingTaskMoves(int $playerId): void
    {
        if ($this->getPendingTaskMoves($playerId) === null) {
            return;
        }

        throw new UserException(clienttranslate('Сначала переместите задачи по треку спринта за бонус IT-проектов'));
    }

    /**
     * Проверяет одно бонусное перемещение по треку спринта: только вперёд и ровно
     * на указанное клиентом число колонок. Этот бонус не ограничен техотделом.
     *
     * @param array<string,mixed> $move
     */
    private function validateTaskMove(int $playerId, array $move): void
    {
        $tokenId = (int) ($move['tokenId'] ?? 0);
        $from = strtolower(trim((string) ($move['fromLocation'] ?? '')));
        $to = strtolower(trim((string) ($move['toLocation'] ?? '')));
        $blocks = (int) ($move['blocks'] ?? 0);

        if ($tokenId <= 0 || $from === '' || $to === '' || $blocks <= 0) {
            throw new UserException(clienttranslate('Неверные данные перемещения'));
        }

        $tokens = $this->game->getTaskTokensByPlayer($playerId);
        $found = false;
        foreach ($tokens as $token) {
            if ((int) ($token['token_id'] ?? 0) !== $tokenId) {
                continue;
            }
            $location = strtolower(trim((string) ($token['location'] ?? 'backlog')));
            if ($location !== $from) {
                throw new UserException(clienttranslate('Неверная исходная колонка задачи'));
            }
            $found = true;
            break;
        }
        if (!$found) {
            throw new UserException(clienttranslate('Жетон не найден'));
        }

        $order = ['backlog', 'in-progress', 'testing', 'completed'];
        $fromIndex = array_search($from, $order, true);
        $toIndex = array_search($to, $order, true);
        if ($fromIndex === false || $toIndex === false) {
            throw new UserException(clienttranslate('Неверная колонка спринта'));
        }
        if ($toIndex <= $fromIndex) {
            throw new UserException(clienttranslate('Нельзя перемещать задачу назад по треку спринта'));
        }
        if (($toIndex - $fromIndex) !== $blocks) {
            throw new UserException(clienttranslate('Неверное число шагов перемещения'));
        }
    }
}

