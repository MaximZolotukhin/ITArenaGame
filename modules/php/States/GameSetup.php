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
        
        // Уведомляем игроков о начале этапа подготовки
        $this->notify->all('gameSetupStart', clienttranslate('🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ'), [
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
            
            // Отправляем уведомление всем игрокам с данными о картах основателей
            // Это нужно, чтобы клиент обновил gamedatas после того, как данные сохранены в globals
            error_log('GameSetup::onEnteringState - Sending founderOptionsAssigned notification');
            $this->notify->all('founderOptionsAssigned', clienttranslate('Карты основателей распределены'), [
                'allPlayersFounderOptions' => $allPlayersFounderOptions,
            ]);
            error_log('GameSetup::onEnteringState - founderOptionsAssigned notification sent');
        } else {
            error_log('GameSetup::onEnteringState - Tutorial mode - skipping founder options check');
        }
        
        // 3. Раздаем стартовые карты специалистов
        $this->game->distributeStartingSpecialistCards($playerIds);
        
        // 4. Раздаем стартовые проекты
        $this->game->distributeStartingProjects($playerIds);
        
        // 5. Раздаем начальные жетоны задач (1 розовый + 1 голубой в бэклог)
        $this->game->distributeInitialTaskTokens($playerIds);
        
        // 6. Устанавливаем компоненты на планшеты (загрузка планшетов, расстановка жетонов)
        $this->game->setupPlayerBoards($playerIds);
        
        // Инициализируем список игроков, которые нажали "Начать игру"
        $this->game->globals->set('players_ready_for_game', json_encode([]));
        
        // Делаем всех игроков активными, чтобы они могли нажать кнопку
        $this->game->gamestate->setAllPlayersMultiactive();
    }

    public function getArgs(): array
    {
        $readyPlayersJson = $this->game->globals->get('players_ready_for_game', '[]');
        $readyPlayers = json_decode($readyPlayersJson, true) ?? [];
        $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
        $allReady = count($readyPlayers) === count($allPlayers);
        
        return [
            'readyPlayers' => $readyPlayers,
            'allReady' => $allReady,
            'totalPlayers' => count($allPlayers),
            'readyCount' => count($readyPlayers),
        ];
    }

    /**
     * Действие игрока: нажатие кнопки "Начать игру"
     */
    #[PossibleAction]
    public function actStartGame(int $playerId): void
    {
        error_log('GameSetup::actStartGame - === CALLED === Player ID: ' . $playerId);
        
        // В MULTIPLE_ACTIVE_PLAYER состоянии проверяем, что игрок активен
        if (!$this->game->gamestate->isPlayerActive($playerId)) {
            error_log('GameSetup::actStartGame - ERROR: Player ' . $playerId . ' is not active!');
            throw new UserException(clienttranslate('Вы не можете выполнить это действие сейчас'));
        }

        // Получаем список готовых игроков
        $readyPlayersJson = $this->game->globals->get('players_ready_for_game', '[]');
        $readyPlayers = json_decode($readyPlayersJson, true) ?? [];
        error_log('GameSetup::actStartGame - Current ready players: ' . json_encode($readyPlayers));
        
        // Проверяем, не нажал ли игрок уже кнопку
        if (in_array($playerId, $readyPlayers, true)) {
            error_log('GameSetup::actStartGame - ERROR: Player ' . $playerId . ' already pressed button!');
            throw new UserException(clienttranslate('Вы уже нажали кнопку "Начать игру"'));
        }

        // Добавляем игрока в список готовых
        $readyPlayers[] = $playerId;
        $this->game->globals->set('players_ready_for_game', json_encode($readyPlayers));
        error_log('GameSetup::actStartGame - Added player ' . $playerId . ' to ready list. New ready players: ' . json_encode($readyPlayers));

        // Уведомляем всех игроков
        $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
        error_log('GameSetup::actStartGame - Total players: ' . count($allPlayers));
        $this->notify->all('playerReadyForGame', clienttranslate('${player_name} готов начать игру'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'readyPlayers' => $readyPlayers,
            'readyCount' => count($readyPlayers),
            'totalPlayers' => count($allPlayers),
        ]);

        // Проверяем, все ли игроки готовы
        $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
        $allReady = count($readyPlayers) === count($allPlayers);
        error_log('GameSetup::actStartGame - Ready count: ' . count($readyPlayers) . ', Total players: ' . count($allPlayers) . ', All ready: ' . ($allReady ? 'YES' : 'NO'));
        
        // Определяем следующее состояние для перехода
        $isTutorial = $this->game->isTutorialMode();
        $nextState = $isTutorial ? RoundEvent::class : FounderSelection::class;
        error_log('GameSetup::actStartGame - Tutorial mode: ' . ($isTutorial ? 'YES' : 'NO') . ', Next state: ' . $nextState);
        
        // Если все игроки готовы, отправляем уведомления и переходим к следующему состоянию
        if ($allReady) {
            error_log('GameSetup::actStartGame - ALL PLAYERS READY! Sending notifications and transitioning...');
            
            $this->notify->all('gameSetupComplete', clienttranslate('Все игроки готовы! Начинаем игру...'), [
                'players' => $allPlayers,
            ]);
            error_log('GameSetup::actStartGame - gameSetupComplete notification sent');
            
            if (!$isTutorial) {
                // Основной режим: устанавливаем первого активного игрока для выбора основателя
                error_log('GameSetup::actStartGame - MAIN MODE: Setting active player...');
                $this->game->activeNextPlayer();
                $activePlayerId = $this->game->getActivePlayerId();
                error_log('GameSetup::actStartGame - Set active player for FounderSelection: ' . $activePlayerId);
                
                // Проверяем, что у активного игрока есть карты на выбор
                $founderOptions = $this->game->getFounderOptionsForPlayer((int)$activePlayerId);
                error_log('GameSetup::actStartGame - Active player founder options count: ' . count($founderOptions));
                if (empty($founderOptions)) {
                    error_log('GameSetup::actStartGame - WARNING: Active player has NO founder options!');
                }
            }
            
            // Уведомляем о переходе к следующему этапу
            $this->notify->all('gameStart', clienttranslate('🎮 ЭТАП 2: НАЧАЛО ИГРЫ'), [
                'stageName' => clienttranslate('Начало игры'),
            ]);
            error_log('GameSetup::actStartGame - gameStart notification sent');
            
            // Переходим к следующему состоянию сразу, так как все игроки готовы
            // В MULTIPLE_ACTIVE_PLAYER состоянии нужно использовать setAllPlayersNonMultiactive
            error_log('GameSetup::actStartGame - Calling setAllPlayersNonMultiactive(' . $nextState . ')');
            $transitioned = $this->game->gamestate->setAllPlayersNonMultiactive($nextState);
            error_log('GameSetup::actStartGame - setAllPlayersNonMultiactive() returned: ' . ($transitioned ? 'true' : 'false'));
            return;
        }
        
        // Делаем игрока неактивным
        // Переход произойдет автоматически, когда последний игрок станет неактивным
        error_log('GameSetup::actStartGame - Not all ready yet. Setting player ' . $playerId . ' non-multiactive with nextState: ' . $nextState);
        $this->game->gamestate->setPlayerNonMultiactive($playerId, $nextState);
        error_log('GameSetup::actStartGame - setPlayerNonMultiactive() called');
    }

    public function zombie(int $playerId): void
    {
        // Для зомби-игрока автоматически нажимаем кнопку
        $this->actStartGame($playerId);
    }
}

