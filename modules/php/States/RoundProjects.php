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
    private const PROJECTS_PASS_AFTER_TECH_KEY_PREFIX = 'proj_pass_after_td_';

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
        foreach ($this->game->getProjectTokensOnBoard() as $token) {
            $tid = (int) ($token['token_id'] ?? 0);
            if ($tid <= 0) {
                continue;
            }
            $color = strtolower(trim((string) ($token['color'] ?? '')));
            $price = (int) ($token['price'] ?? 0);
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
        $this->game->assertNoPendingTechnicalDevelopmentMoves((int) $this->game->getActivePlayerId());
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
        $turnPassesAfterPurchase = $pendingTech === null;
        $this->game->notify->all(
            'projectTokenPurchased',
            clienttranslate('${player_name} покупает IT-проект «${project_name}» за ${price} ${color_name}'),
            [
                'player_id' => $playerId,
                'player_name' => $this->game->getPlayerNameById($playerId),
                'project_name' => $token['name'],
                'price' => (int) $token['price'],
                'color' => $token['color'],
                'color_name' => $this->colorDisplayName((string) $token['color']),
                'tokenId' => (int) $token['token_id'],
                'boardPosition' => $result['boardPosition'] ?? $token['board_position'],
                'projectToken' => $token,
                'spentTaskTokenIds' => $result['spent_task_token_ids'],
                'completedByColor' => $result['completed_by_color'],
                'projectTokensOnBoard' => $result['projectTokensOnBoard'],
                'playerProjectTokens' => $result['playerProjectTokens'],
                // Новый жетон, выложенный на освободившуюся ячейку (или null,
                // если запас исчерпан и ячейка осталась пустой).
                'replacementToken' => $result['replacementToken'] ?? null,
                'appliedEffects' => $appliedEffects,
                'badgersSupply' => $this->game->getBadgersSupply(),
                'hasPendingTechnicalDevelopment' => $pendingTech !== null,
                'purchaseEndsTurn' => true,
                'i18n' => ['project_name', 'color_name'],
            ],
        );

        if ($pendingTech !== null) {
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
}

