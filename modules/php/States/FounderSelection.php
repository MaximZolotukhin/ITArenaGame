<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\States\NextPlayer;

/**
 * Состояние выбора карты основателя (только для основного режима)
 */
class FounderSelection extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 20,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose a founder card or finish turn'),
            descriptionMyTurn: clienttranslate('${you} must choose a founder card or finish turn'),
        );
    }

    /**
     * Метод вызывается при входе в состояние выбора карты основателя
     */
    public function onEnteringState(): void
    {
        // Активный игрок должен быть установлен до входа в это состояние
        // (в setupNewGame или в предыдущем состоянии)
        $activePlayerId = $this->game->getActivePlayerId();
        if ($activePlayerId === null) {
            error_log('FounderSelection::onEnteringState - WARNING: No active player set!');
            // Устанавливаем первого игрока как активного
            $this->game->activeNextPlayer();
        }
        
        $activePlayerId = $this->game->getActivePlayerId();
        $founderOptions = $this->game->getFounderOptionsForPlayer((int)$activePlayerId);
        error_log('FounderSelection::onEnteringState - Active player: ' . $activePlayerId . ', Founder options count: ' . count($founderOptions));
    }

    /**
     * Возвращает аргументы для состояния выбора карты основателя
     */
    public function getArgs(): array
    {
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        
        if ($activePlayerIdRaw === null) {
            error_log('FounderSelection::getArgs - WARNING: No active player set! Setting first player as active.');
            $this->game->activeNextPlayer();
            $activePlayerIdRaw = $this->game->getActivePlayerId();
        }
        
        $activePlayerId = is_int($activePlayerIdRaw) ? $activePlayerIdRaw : (int)$activePlayerIdRaw;
        
        $founderOptions = $this->game->getFounderOptionsForPlayer($activePlayerId);
        $hasSelectedFounder = $this->game->globals->get('founder_player_' . $activePlayerId, null) !== null;
        $mustPlaceFounder = $hasSelectedFounder && $this->game->hasUnplacedUniversalFounder($activePlayerId);
        
        error_log('FounderSelection::getArgs - Active player: ' . $activePlayerId . ', Options count: ' . count($founderOptions) . ', Has selected: ' . ($hasSelectedFounder ? 'yes' : 'no'));
        if (empty($founderOptions)) {
            error_log('FounderSelection::getArgs - WARNING: No founder options found for active player ' . $activePlayerId);
            // Проверяем все игроков
            $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
            foreach ($allPlayers as $playerId) {
                $options = $this->game->getFounderOptionsForPlayer((int)$playerId);
                error_log('FounderSelection::getArgs - Player ' . $playerId . ' has ' . count($options) . ' options');
            }
        }
        
        return [
            "activePlayerId" => $activePlayerId,
            "founderOptions" => $founderOptions, // Массив из 3 карт на выбор (пустой, если карта уже выбрана)
            "hasSelectedFounder" => $hasSelectedFounder, // Выбрал ли игрок карту
            "mustPlaceFounder" => $mustPlaceFounder, // Обязательно ли разместить карту основателя
        ];
    }


    /**
     * Действие игрока: выбор карты основателя
     */
    #[PossibleAction]
    public function actSelectFounder(int $activePlayerId, int $cardId)
    {
        $this->game->checkAction('actSelectFounder');
        
        // Проверяем, что карта доступна для выбора
        $options = $this->game->getFounderOptionsForPlayer($activePlayerId);
        if (empty($options)) {
            throw new UserException(clienttranslate('У вас нет доступных карт основателей для выбора'));
        }
        
        $availableIds = array_column($options, 'id');
        if (!in_array($cardId, $availableIds, true)) {
            throw new UserException(clienttranslate('Выбранная карта недоступна'));
        }
        
        // Проверяем, что карта еще не выбрана
        $existingCardId = $this->game->globals->get('founder_player_' . $activePlayerId, null);
        if ($existingCardId !== null) {
            throw new UserException(clienttranslate('Карта основателя уже выбрана'));
        }
        
        // Выбираем карту для игрока
        $this->game->selectFounderForPlayer($activePlayerId, $cardId);
        
        // Уведомляем о выборе
        $founderCard = \Bga\Games\itarenagame\FoundersData::getCard($cardId);
        $founderName = $founderCard['name'] ?? clienttranslate('Неизвестный основатель');
        
        $this->notify->all('founderSelected', clienttranslate('${player_name} выбрал карту основателя: ${founder_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'founder_name' => $founderName,
            'card_id' => $cardId,
            'founder' => $founderCard,
        ]);
        
        $this->game->giveExtraTime($activePlayerId);
        
        // Если карта универсальная, остаемся в этом состоянии для размещения
        // Иначе переходим к следующему игроку
        $founderDepartment = $founderCard['department'] ?? 'universal';
        if ($founderDepartment !== 'universal') {
            // Карта автоматически размещена, переходим к следующему игроку
            return NextPlayer::class;
        }
        
        // Карта универсальная, остаемся в состоянии для размещения
        return null;
    }

    /**
     * Действие игрока: завершение хода
     */
    #[PossibleAction]
    public function actFinishTurn(int $activePlayerId)
    {
        // Проверяем, есть ли у игрока неразмещенная универсальная карта основателя
        if ($this->game->hasUnplacedUniversalFounder($activePlayerId)) {
            throw new UserException(clienttranslate('Вы должны разместить карту основателя в один из отделов перед завершением хода'));
        }

        $this->notify->all('turnFinished', clienttranslate('${player_name} завершает ход'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
        ]);

        $this->game->giveExtraTime($activePlayerId);

        // Переходим к следующему игроку
        return NextPlayer::class;
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: choose a random founder card).
     * 
     * See more about Zombie Mode: https://en.doc.boardgamearena.com/Zombie_Mode
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, 
     * but use the $playerId passed in parameter and $this->game->getPlayerNameById($playerId) instead.
     */
    function zombie(int $playerId)
    {
        // Для зомби-игрока просто переходим к следующему игроку
        return NextPlayer::class;
    }
}

