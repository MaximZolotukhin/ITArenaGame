<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;

class PlayerTurn extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct($game,
            id: 11,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must play a card or pass'),
            descriptionMyTurn: clienttranslate('${you} must play a card or pass'),
        );
    }

    /**
     * Game state arguments, example content.
     *
     * This method returns some additional information that is very specific to the `PlayerTurn` game state.
     */
    public function getArgs(): array
    {
        // Get some values from the current game situation from the database.
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        $activePlayerId = is_int($activePlayerIdRaw) ? $activePlayerIdRaw : (int)$activePlayerIdRaw;
        $mustPlaceFounder = $this->game->hasUnplacedUniversalFounder($activePlayerId);

        return [
            "playableCardsIds" => [1, 2], // Идентификаторы доступных карт
            "activePlayerId" => $activePlayerId, // Идентификатор активного игрока
            "mustPlaceFounder" => $mustPlaceFounder, // Обязательно ли разместить карту основателя
        ];
    }    

    /**
     * Player action, example content.
     *
     * In this scenario, each time a player plays a card, this method will be called. This method is called directly
     * by the action trigger on the front side with `bgaPerformAction`.
     *
     * @throws UserException
     */
    #[PossibleAction]
    public function actPlayCard(int $card_id, int $activePlayerId, array $args)
    {
        // check input values
        $playableCardsIds = $args['playableCardsIds'];
        if (!in_array($card_id, $playableCardsIds)) {
            throw new UserException('Invalid card choice');
        }

        // Add your game logic to play a card here.
        $card_name = Game::$CARD_TYPES[$card_id]['card_name'];

        // Notify all players about the card played.
        $this->notify->all("cardPlayed", clienttranslate('${player_name} plays ${card_name}'), [
            "player_id" => $activePlayerId,
            "player_name" => $this->game->getPlayerNameById($activePlayerId), // remove this line if you uncomment notification decorator
            "card_name" => $card_name, // remove this line if you uncomment notification decorator
            "card_id" => $card_id,
            "i18n" => ['card_name'], // remove this line if you uncomment notification decorator
        ]);

        // in this example, the player gains 1 points each time he plays a card
        $this->playerScore->inc($activePlayerId, 1);

        // at the end of the action, move to the next state
        return NextPlayer::class;
    }

    /**
     * Player action, example content.
     *
     * In this scenario, each time a player pass, this method will be called. This method is called directly
     * by the action trigger on the front side with `bgaPerformAction`.
     */
    #[PossibleAction]
    public function actPass(int $activePlayerId)
    {
        // Notify all players about the choice to pass.
        $this->notify->all("pass", clienttranslate('${player_name} passes'), [
            "player_id" => $activePlayerId,
            "player_name" => $this->game->getPlayerNameById($activePlayerId), // remove this line if you uncomment notification decorator
        ]);

        // in this example, the player gains 1 energy each time he passes
        $this->game->playerEnergy->inc($activePlayerId, 1);

        // at the end of the action, move to the next state
        return NextPlayer::class;
    }

    #[PossibleAction]
    public function actPlaceFounder(string $department, int $activePlayerId) // Размещение карты основателя в отдел
    {
        $this->game->placeFounder($activePlayerId, $department);

        $founder = $this->game->getFoundersByPlayer()[$activePlayerId] ?? null;
        $departmentNames = [
            'sales-department' => clienttranslate('Отдел продаж'),
            'back-office' => clienttranslate('Бэк-офис'),
            'technical-department' => clienttranslate('Техотдел'), //
        ];
        $departmentName = $departmentNames[$department] ?? $department;

        $this->notify->all('founderPlaced', clienttranslate('${player_name} разместил основателя в ${department_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'department' => $department,
            'department_name' => $departmentName,
            'founder' => $founder,
            'i18n' => ['department_name'],
        ]);

        // ВАЖНО: Разблокируем кнопку «Завершить ход» на клиенте. В PlayerTurn эффекты основателя
        // уже применены на этапе FounderSelection, поэтому только уведомляем.
        $this->notify->player($activePlayerId, 'founderEffectsApplied', '', [
            'player_id' => $activePlayerId,
        ]);
    }

    #[PossibleAction]
    public function actFinishTurn(int $activePlayerId) // конец хода игрока
    {
        error_log('🎮🎮🎮 PlayerTurn::actFinishTurn() CALLED! activePlayerId: ' . $activePlayerId);
        
        $currentRound = (int)$this->game->getGameStateValue('round_number');
        $playersLeftInRound = (int)$this->game->getGameStateValue('players_left_in_round');
        $playersCount = count($this->game->loadPlayersBasicInfos());
        
        error_log('🎮 PlayerTurn::actFinishTurn() - currentRound: ' . $currentRound . ', players_left_in_round: ' . $playersLeftInRound . ', playersCount: ' . $playersCount);
        
        // Проверяем, есть ли у игрока неразмещенная универсальная карта основателя
        if ($this->game->hasUnplacedUniversalFounder($activePlayerId)) {
            throw new UserException(clienttranslate('Вы должны разместить карту основателя в один из отделов перед завершением хода'));
        }

        // ВАЖНО: Сохраняем все данные игрока в таблицу player_game_data перед завершением хода
        $this->game->savePlayerGameDataOnTurnEnd($activePlayerId);
        
        $this->notify->all('turnFinished', clienttranslate('${player_name} завершает ход'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
        ]);

        $this->game->giveExtraTime($activePlayerId);
        
        error_log('🎮 PlayerTurn::actFinishTurn() - Returning NextPlayer::class');

        return NextPlayer::class;
    }

    /**
     * Обновляет локацию жетона задачи
     * @param int $activePlayerId ID активного игрока
     * @param int $tokenId ID жетона задачи
     * @param string $location Новая локация (backlog, in-progress, testing, completed)
     * @param int|null $rowIndex Индекс строки (опционально)
     */
    #[PossibleAction]
    public function actUpdateTaskTokenLocation(int $activePlayerId, int $tokenId, string $location, ?int $rowIndex = null)
    {
        $this->game->checkAction('actUpdateTaskTokenLocation');
        
        // Проверяем, что жетон принадлежит активному игроку
        $token = $this->game->getCollectionFromDb("
            SELECT * FROM `player_task_token`
            WHERE `token_id` = $tokenId
            AND `player_id` = $activePlayerId
        ");
        
        if (empty($token)) {
            throw new UserException(clienttranslate('Жетон задачи не найден или не принадлежит вам'));
        }
        
        // Обновляем локацию жетона
        $success = $this->game->updateTaskTokenLocation($tokenId, $location, $rowIndex);
        
        if (!$success) {
            throw new UserException(clienttranslate('Ошибка при обновлении локации жетона задачи'));
        }
        
        // Уведомляем всех игроков об обновлении
        $tokenData = reset($token);
        $this->notify->all('taskTokenLocationUpdated', clienttranslate('${player_name} переместил задачу'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'token_id' => $tokenId,
            'color' => $tokenData['color'],
            'old_location' => $tokenData['location'],
            'new_location' => $location,
            'row_index' => $rowIndex,
            'i18n' => ['player_name'],
        ]);
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: play a random card).
     * 
     * See more about Zombie Mode: https://en.doc.boardgamearena.com/Zombie_Mode
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, 
     * but use the $playerId passed in parameter and $this->game->getPlayerNameById($playerId) instead.
     */
    function zombie(int $playerId) {
        // Example of zombie level 0: return NextPlayer::class; or $this->actPass($playerId);

        // Example of zombie level 1:
        $args = $this->getArgs();
        $zombieChoice = $this->getRandomZombieChoice($args['playableCardsIds']); // random choice over possible moves
        return $this->actPlayCard($zombieChoice, $playerId, $args); // this function will return the transition to the next state
    }
}