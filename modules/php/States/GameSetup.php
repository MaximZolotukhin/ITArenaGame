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
        $this->game->distributeInitialBadgers($playerIds, 5);
        
        // 2. Выдаем карты основателей игрокам
        $this->game->assignInitialFounders($playerIds);
        
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
        // В MULTIPLE_ACTIVE_PLAYER состоянии проверяем, что игрок активен
        if (!$this->game->gamestate->isPlayerActive($playerId)) {
            throw new UserException(clienttranslate('Вы не можете выполнить это действие сейчас'));
        }

        // Получаем список готовых игроков
        $readyPlayersJson = $this->game->globals->get('players_ready_for_game', '[]');
        $readyPlayers = json_decode($readyPlayersJson, true) ?? [];
        
        // Проверяем, не нажал ли игрок уже кнопку
        if (in_array($playerId, $readyPlayers, true)) {
            throw new UserException(clienttranslate('Вы уже нажали кнопку "Начать игру"'));
        }

        // Добавляем игрока в список готовых
        $readyPlayers[] = $playerId;
        $this->game->globals->set('players_ready_for_game', json_encode($readyPlayers));

        // Уведомляем всех игроков
        $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
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
        
        // Определяем следующее состояние для перехода
        $isTutorial = $this->game->isTutorialMode();
        $nextState = $isTutorial ? RoundEvent::class : FounderSelection::class;
        
        // Если все игроки готовы, отправляем уведомления перед переходом
        if ($allReady) {
            $this->notify->all('gameSetupComplete', clienttranslate('Все игроки готовы! Начинаем игру...'), [
                'players' => $allPlayers,
            ]);
            
            if (!$isTutorial) {
                // Основной режим: устанавливаем первого активного игрока
                $this->game->activeNextPlayer();
            }
            
            // Уведомляем о переходе к следующему этапу
            $this->notify->all('gameStart', clienttranslate('🎮 ЭТАП 2: НАЧАЛО ИГРЫ'), [
                'stageName' => clienttranslate('Начало игры'),
            ]);
        }
        
        // Делаем игрока неактивным
        // Переход произойдет автоматически, когда последний игрок станет неактивным
        $this->game->gamestate->setPlayerNonMultiactive($playerId, $nextState);
    }

    public function zombie(int $playerId): void
    {
        // Для зомби-игрока автоматически нажимаем кнопку
        $this->actStartGame($playerId);
    }
}

