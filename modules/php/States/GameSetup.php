<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;

/**
 * Состояние подготовки игры - раздача стартовых ресурсов
 */
class GameSetup extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 2,
            type: StateType::MULTIPLE_ACTIVE_PLAYER,
            description: clienttranslate('Подготовка к игре - все игроки должны нажать "Начать игру"'),
        );
    }

    public function onEnteringState()
    {
        $playerIds = array_keys($this->game->loadPlayersBasicInfos());
        
        // Уведомляем игроков о начале этапа подготовки (без текста в логе)
        $this->notify->all('gameSetupStart', '', [
            'players' => $playerIds,
            'stageName' => clienttranslate('Подготовка к игре'),
        ]);
        
        // ЭТАП ПОДГОТОВКИ:
        // 1. Распределяем начальные баджерсы (деньги) - по 5 баджерсов каждому игроку
        error_log('GameSetup::onEnteringState - Step 1: Distributing initial badgers');
        $this->game->distributeInitialBadgers($playerIds, 5);
        
        // Проверяем, что все игроки получили по 5 баджерсов
        foreach ($playerIds as $playerId) {
            $badgers = $this->game->playerBadgers->get((int)$playerId);
            error_log('GameSetup::onEnteringState - Player ' . $playerId . ' has ' . $badgers . ' badgers (expected: 5)');
            if ($badgers !== 5) {
                error_log('GameSetup::onEnteringState - ERROR: Player ' . $playerId . ' has incorrect badgers count! Expected: 5, Got: ' . $badgers);
            }
        }
        
        // 2. Выдаем карты основателей игрокам
        error_log('GameSetup::onEnteringState - Step 2: Assigning founder cards');
        error_log('GameSetup::onEnteringState - Tutorial mode: ' . ($this->game->isTutorialMode() ? 'true' : 'false'));
        $this->game->assignInitialFounders($playerIds);
        error_log('GameSetup::onEnteringState - assignInitialFounders completed');
        
        // Проверяем, что данные сохранились
        if (!$this->game->isTutorialMode()) {
            error_log('GameSetup::onEnteringState - Checking founder options for all players (main mode)');
            $allPlayersFounderOptions = [];
            foreach ($playerIds as $playerId) {
                $playerId = (int)$playerId;
                $key = 'founder_options_' . $playerId;
                $rawValue = $this->game->globals->get($key, null);
                error_log('GameSetup::onEnteringState - Player ' . $playerId . ' - Raw globals value: ' . var_export($rawValue, true));
                
                $options = $this->game->getFounderOptionsForPlayer($playerId);
                error_log('GameSetup::onEnteringState - Player ' . $playerId . ' has ' . count($options) . ' founder options after assignment');
                
                if (count($options) !== 3) {
                    error_log('GameSetup::onEnteringState - ERROR: Player ' . $playerId . ' should have 3 options, but got ' . count($options));
                } else {
                    $allPlayersFounderOptions[$playerId] = $options;
                }
            }
            
        } else {
            error_log('GameSetup::onEnteringState - Tutorial mode - skipping founder options check');
        }
        
        // 3. Раздаем стартовые карты специалистов
        $this->game->distributeStartingSpecialistCards($playerIds);
        
        // 4. Раздаем стартовые проекты
        $this->game->distributeStartingProjects($playerIds);
        
        // 5. Инициализируем жетоны проектов (если еще не инициализированы)
        $this->game->initializeProjectTokensIfNeeded();
        
        // 6. Размещаем жетоны проектов в красной колонке планшета проектов
        $this->game->placeProjectTokensOnRedColumn();
        
        // 7. Размещаем жетоны проектов в синей колонке планшета проектов
        $this->game->placeProjectTokensOnBlueColumn();
        
        // 8. Размещаем жетоны проектов в зеленой колонке планшета проектов
        $this->game->placeProjectTokensOnGreenColumn();
        
        // 9. Раздаем начальные жетоны задач (1 розовый + 1 голубой в бэклог)
        $this->game->distributeInitialTaskTokens($playerIds);
        
        // 10. Устанавливаем компоненты на планшеты (загрузка планшетов, расстановка жетонов)
        $this->game->setupPlayerBoards($playerIds);
        
        // Определяем следующее состояние для перехода
        $isTutorial = $this->game->isTutorialMode();
        // ID состояний: RoundEvent = 15, FounderSelection = 20
        $nextStateId = $isTutorial ? 15 : 20;
        error_log('GameSetup::onEnteringState - Tutorial mode: ' . ($isTutorial ? 'YES' : 'NO') . ', Next state ID: ' . $nextStateId);
        
        if (!$isTutorial) {
            // Основной режим: устанавливаем первого активного игрока для выбора основателя
            $this->game->activeNextPlayer();
            $activePlayerId = $this->game->getActivePlayerId();
            error_log('GameSetup::onEnteringState - Set active player for FounderSelection: ' . $activePlayerId);
        
            // Уведомление "ЭТАП 2: НАЧАЛО ИГРЫ" будет отправлено только после того,
            // как ВСЕ игроки завершат выбор карт основателей (в NextPlayer.php)
        } else {
            // В обучающем режиме сразу переходим к началу игры (без текста в логе)
            $this->notify->all('gameStart', '', [
            'stageName' => clienttranslate('Начало игры'),
        ]);
        }
        
        // Автоматически переходим к следующему состоянию
        error_log('GameSetup::onEnteringState - Automatically transitioning to state ID: ' . $nextStateId);
        $this->game->gamestate->jumpToState($nextStateId);
    }

    public function getArgs(): array
    {
        return [
            'projectTokensOnBoard' => $this->game->getProjectTokensOnBoard(),
        ];
    }

    public function zombie(int $playerId): void
    {
        // Для зомби-игрока переход происходит автоматически в onEnteringState
    }
}

