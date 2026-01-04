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
        error_log('GameSetup::onEnteringState - Step 2: Distributing initial badgers');
        $this->game->distributeInitialBadgers($playerIds, 5);
        
        
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
        
        // ВЫВОДИМ НАЧАЛЬНЫЕ ЗНАЧЕНИЯ ВСЕХ ИГРОКОВ ПОСЛЕ ПОЛНОЙ ИНИЦИАЛИЗАЦИИ
        error_log('GameSetup::onEnteringState - Collecting initial values for all players');
        $initialValues = [];
        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            
            // Базовые параметры
            $badgers = $this->game->playerBadgers->get($playerId);
            $incomeTrack = $this->game->playerEnergy->get($playerId); // Трек дохода (income-track)
            
            // Жетоны задач
            $taskTokens = $this->game->getTaskTokensByPlayer($playerId);
            $taskTokensCount = count($taskTokens);
            $taskTokensByLocation = [];
            foreach ($taskTokens as $token) {
                $location = $token['location'] ?? 'unknown';
                $taskTokensByLocation[$location] = ($taskTokensByLocation[$location] ?? 0) + 1;
            }
            
            // Жетоны проектов
            $projectTokens = $this->game->getProjectTokensByPlayer($playerId);
            $projectTokensCount = count($projectTokens);
            
            // Карты специалистов на руке (для выбора)
            $specialistHandIdsJson = $this->game->globals->get('specialist_hand_' . $playerId, '');
            $specialistHandIds = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
            $specialistHandCount = is_array($specialistHandIds) ? count($specialistHandIds) : 0;
            
            // Карты сотрудников (подтвержденные)
            $playerSpecialistsIdsJson = $this->game->globals->get('player_specialists_' . $playerId, '');
            $playerSpecialistsIds = !empty($playerSpecialistsIdsJson) ? json_decode($playerSpecialistsIdsJson, true) : [];
            $playerSpecialistsCount = is_array($playerSpecialistsIds) ? count($playerSpecialistsIds) : 0;
            
            // Треки бэк-офиса
            // Инициализируем игровые данные игрока в БД
            $this->game->initPlayerGameData($playerId);
            
            // Получаем данные из БД
            $gameData = $this->game->getPlayerGameData($playerId);
            $backOfficeCol1 = $gameData ? $gameData['backOfficeCol1'] : null;
            $backOfficeCol2 = $gameData ? $gameData['backOfficeCol2'] : null;
            $backOfficeCol3 = $gameData ? $gameData['backOfficeCol3'] : null;
            
            // Треки технического развития
            $techDevCol1 = $gameData ? $gameData['techDevCol1'] : null;
            $techDevCol2 = $gameData ? $gameData['techDevCol2'] : null;
            $techDevCol3 = $gameData ? $gameData['techDevCol3'] : null;
            $techDevCol4 = $gameData ? $gameData['techDevCol4'] : null;
            
            // Жетон навыка (если есть)
            $skillToken = $gameData ? $gameData['skillToken'] : null;
            
            $initialValues[$playerId] = [
                'badgers' => $badgers,
                'incomeTrack' => $incomeTrack,
                'taskTokens' => ['total' => $taskTokensCount, 'byLocation' => $taskTokensByLocation],
                'projectTokens' => $projectTokensCount,
                'specialistHand' => ['count' => $specialistHandCount, 'ids' => $specialistHandIds],
                'playerSpecialists' => ['count' => $playerSpecialistsCount, 'ids' => $playerSpecialistsIds],
                'backOfficeCol1' => $backOfficeCol1,
                'backOfficeCol2' => $backOfficeCol2,
                'backOfficeCol3' => $backOfficeCol3,
                'techDevCol1' => $techDevCol1,
                'techDevCol2' => $techDevCol2,
                'techDevCol3' => $techDevCol3,
                'techDevCol4' => $techDevCol4,
                'skillToken' => $skillToken,
            ];
            
            if ($badgers !== 5) {
                error_log('GameSetup::onEnteringState - ERROR: Player ' . $playerId . ' has incorrect badgers count! Expected: 5, Got: ' . $badgers);
            }
        }
        
        // Отправляем уведомление с начальными значениями ПОСЛЕ полной инициализации
        error_log('GameSetup::onEnteringState - Sending initialPlayerValues notification for ' . count($initialValues) . ' players');
        $this->notify->all('initialPlayerValues', '', [
            'initialValues' => $initialValues,
        ]);
        error_log('GameSetup::onEnteringState - initialPlayerValues notification sent');
        
        // Определяем следующее состояние для перехода
        $isTutorial = $this->game->isTutorialMode();
        error_log('GameSetup::onEnteringState - Tutorial mode: ' . ($isTutorial ? 'YES' : 'NO'));
        
        if ($isTutorial) {
            // В обучающем режиме используем ту же логику что и в основном режиме (FounderSelection)
            // Карта уже выбрана и лежит в globals, игроку нужно только разместить если universal
            $nextStateId = 20; // FounderSelection - та же логика что в основном режиме
            $this->game->activeNextPlayer();
            $activePlayerId = $this->game->getActivePlayerId();
            error_log('GameSetup::onEnteringState - Tutorial: Going to FounderSelection (same as main mode), active player: ' . $activePlayerId);
        } else {
            // Основной режим: устанавливаем первого активного игрока для выбора основателя
            $nextStateId = 20; // FounderSelection
            $this->game->activeNextPlayer();
            $activePlayerId = $this->game->getActivePlayerId();
            error_log('GameSetup::onEnteringState - Set active player for FounderSelection: ' . $activePlayerId);
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

