<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * ITArenaGame implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */
declare(strict_types=1);

namespace Bga\Games\itarenagame;

use Bga\Games\itarenagame\States\PlayerTurn; // Добавляем класс PlayerTurn для работы с ходом игрока
use Bga\GameFramework\Components\Counters\PlayerCounter; // Добавляем класс PlayerCounter
use Bga\GameFramework\Components\Deck; // Добавляем класс Deck
use Bga\Games\itarenagame\EventCardsData; // Добавляем класс EventCardsData для работы с картами событий

class Game extends \Bga\GameFramework\Table
{
    public static array $CARD_TYPES;

    public PlayerCounter $playerEnergy;
    public Deck $eventDeck; // Добавляем переменную для работы с колодой карт событий

    // Названия этапов
    public function getStageName(int $round): string
    {
        return match ($round) {
            1 => clienttranslate('Рождение идеи'),
            2 => clienttranslate('Младенчество'),
            3 => clienttranslate('Детство'),
            4 => clienttranslate('Юность'),
            5 => clienttranslate('Расцвет'),
            6 => clienttranslate('Стабильность'),
            default => clienttranslate('Неизвестный этап'),
        };
    }

    /** Возвращает название фазы по ключу */
    public function getPhaseName(string $phaseKey): string
    {
        return match ($phaseKey) {
            'event' => clienttranslate('Событие'),
            default => '',
        };
    }

    /** Возвращает список граней кубика (20 значений) */
    private function getCubeFaces(): array
    {
        return [
            'P', 'A', 'E', 'I', 'EI', 'AI', 'AE', 'PI', 'PE', 'PA',
            'AEI', 'PEI', 'PAI', 'PAE', 'S', 'S', 'F', 'F', 'PS', 'SF',
        ];
    }

    /** Бросает кубик текущего раунда, сохраняет индекс и возвращает строковое значение */
    public function rollRoundCube(): string
    {
        $faces = $this->getCubeFaces();
        $index = bga_rand(0, count($faces) - 1);
        $this->setGameStateValue('round_cube_face', $index);
        return $faces[$index];
    }

    /**
     * Your global variables labels:
     *
     * Here, you can assign labels to global variables you are using for this game. You can use any number of global
     * variables with IDs between 10 and 99. If you want to store any type instead of int, use $this->globals instead.
     *
     * NOTE: afterward, you can get/set the global variables with `getGameStateValue`, `setGameStateInitialValue` or
     * `setGameStateValue` functions.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initGameStateLabels([
            // Global game state integers (IDs must be 10..99)
            // Настройка числа раундов, числа игроков в раунде
            'round_number' => 10, // Текущий раунд
            'players_left_in_round' => 11, // Количество игроков в раунде
            'total_rounds' => 12, // Общее количество раундов
            'round_cube_face' => 13, // Индекс текущей грани кубика (0..19)
        ]); // mandatory, even if the array is empty

        $this->playerEnergy = $this->counterFactory->createPlayerCounter('energy');
        $this->eventDeck = $this->deckFactory->createDeck('event_card');
        $this->eventDeck->init('event_card');

        self::$CARD_TYPES = [
            1 => [
                "card_name" => clienttranslate('Troll'), // ...
            ],
            2 => [
                "card_name" => clienttranslate('Goblin'), // ...
            ],
            // ...
        ];

        /* example of notification decorator.
        // automatically complete notification args when needed
        $this->notify->addDecorator(function(string $message, array $args) {
            if (isset($args['player_id']) && !isset($args['player_name']) && str_contains($message, '${player_name}')) {
                $args['player_name'] = $this->getPlayerNameById($args['player_id']);
            }
        
            if (isset($args['card_id']) && !isset($args['card_name']) && str_contains($message, '${card_name}')) {
                $args['card_name'] = self::$CARD_TYPES[$args['card_id']]['card_name'];
                $args['i18n'][] = ['card_name'];
            }
            
            return $args;
        });*/
    }

    /**
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     * @see ./states.inc.php
     */
    public function getGameProgression()
    {
        //Мой код для прогресса в процентах
        $current = (int)$this->getGameStateValue('round_number');// Текущий раунд
        $total = (int)$this->getGameStateValue('total_rounds');// Общее количество раундов
        if ($total <= 0) { return 0; }
        $progress = (int) floor((($current - 1) * 100) / $total);// Прогресс в процентах
        if ($progress < 0) { $progress = 0; }
        if ($progress > 99) { $progress = 99; }  // keep <100 until EndScore
        return $progress;
    }

    /**
     * Migrate database.
     *
     * You don't have to care about this until your game has been published on BGA. Once your game is on BGA, this
     * method is called everytime the system detects a game running with your old database scheme. In this case, if you
     * change your database scheme, you just have to apply the needed changes in order to update the game database and
     * allow the game to continue to run with your new version.
     *
     * @param int $from_version
     * @return void
     */
    public function upgradeTableDb($from_version)
    {
//       if ($from_version <= 1404301345)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "ALTER TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
//
//       if ($from_version <= 1405061421)
//       {
//            // ! important ! Use `DBPREFIX_<table_name>` for all tables
//
//            $sql = "CREATE TABLE `DBPREFIX_xxxxxxx` ....";
//            $this->applyDbUpgradeToAllDB( $sql );
//       }
    }

    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    protected function getAllDatas(): array
    {
        $result = [];

        // WARNING: We must only return information visible by the current player.
        $current_player_id = (int) $this->getCurrentPlayerId();

        // Get information about players.
        // NOTE: you can retrieve some extra field you added for "player" table in `dbmodel.sql` if you need it.
        $result["players"] = $this->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_score` `score` FROM `player`"
        );
        $this->playerEnergy->fillResult($result);

        // Round info for client banner
        $result['round'] = (int)$this->getGameStateValue('round_number'); // Текущий раунд
        $result['totalRounds'] = (int)$this->getGameStateValue('total_rounds'); // Общее количество раундов
        $result['stageName'] = $this->getStageName($result['round']); // Название этапа
        $faces = $this->getCubeFaces(); // Значения граней кубика
        $faceIndex = (int)$this->getGameStateValue('round_cube_face'); // Индекс текущей грани кубика (0..19)
        $result['cubeFace'] = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : ''; // Значение кубика на раунд
        // Текущее имя фазы из глобальной переменной (переводим ключ в название)
        $phaseKey = $this->globals->get('current_phase_name', '');
        $result['phaseName'] = $this->getPhaseName($phaseKey);
        $result['eventCards'] = EventCardsData::getAllCards();
        $result['roundEventCard'] = $this->getRoundEventCard();

        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        return $result;
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = [])
    {
        $this->playerEnergy->initDb(array_keys($players), initialValue: 2);

        // Set the colors of the players with HTML color code. The default below is red/green/blue/orange/brown. The
        // number of colors defined here must correspond to the maximum number of players allowed for the gams.
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            // Now you can access both $player_id and $player array
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s')", [
                $player_id,
                array_shift($default_colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
            ]);
        }

        // Create players based on generic information.
        //
        // NOTE: You can add extra field on player table in the database (see dbmodel.sql) and initialize
        // additional fields directly here.
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        //Мой код для инициализации глобальных значений
        // Init global values with their initial values.
        $this->setGameStateInitialValue('total_rounds', 6); // Общее количество раундов
        $this->setGameStateInitialValue('round_number', 1); // Текущий раунд
        $this->setGameStateInitialValue('players_left_in_round', count($players)); // Количество игроков в раунде
        $this->setGameStateInitialValue('round_cube_face', -1); // Пока не брошен
        // Устанавливаем начальное название фазы, так как сразу переходим в RoundEvent
        // Используем ключ для перевода на клиенте
        $this->globals->set('current_phase_name', 'event');

        $this->eventDeck->autoreshuffle = false;
        $this->eventDeck->createCards(EventCardsData::getCardsForDeck(), 'deck');
        $this->eventDeck->shuffle('deck');

        // Init game statistics.
        //
        // NOTE: statistics used in this file must be defined in your `stats.inc.php` file.

        // Dummy content.
        // $this->tableStats->init('table_teststat1', 0);
        // $this->playerStats->init('player_teststat1', 0);

        // TODO: Setup the initial game situation here.
        // Переходим в фазу 1: "Событие" (кубик и объявление раунда)
        return \Bga\Games\itarenagame\States\RoundEvent::class;
    }

    private function getRoundEventCard(): ?array
    {
        $card = $this->eventDeck->getCardOnTop('table');
        if ($card === null) {
            return null;
        }

        $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
        if ($data !== null) {
            $card = array_merge($card, $data);
        }

        return $card;
    }

    public function prepareRoundEventCard(): ?array
    {
        $onTable = $this->eventDeck->getCardsInLocation('table');
        foreach ($onTable as $tableCard) {
            $this->eventDeck->moveCard((int)$tableCard['id'], 'discard');
        }

        $round = (int)$this->getGameStateValue('round_number');
        $cardId = $this->selectEventCardIdForRound($round);
        if ($cardId === null) {
            return null;
        }

        $this->eventDeck->moveCard($cardId, 'table');

        return $this->getRoundEventCard();
    }

    private function selectEventCardIdForRound(int $round): ?int
    {
        $deckCards = array_values($this->eventDeck->getCardsInLocation('deck'));
        if (empty($deckCards)) {
            return null;
        }

        $eligibleCards = $deckCards;

        if ($round === 1 || $round === 2) {
            $eligibleCards = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 1;
            }));

            if (empty($eligibleCards)) {
                $eligibleCards = $deckCards;
            }
        }

        $index = bga_rand(0, count($eligibleCards) - 1);
        return (int)$eligibleCards[$index]['id'];
    }

    /**
     * Example of debug function.
     * Here, jump to a state you want to test (by default, jump to next player state)
     * You can trigger it on Studio using the Debug button on the right of the top bar.
     */
    public function debug_goToState(int $state = 3) {
        $this->gamestate->jumpToState($state);
    }

    /**
     * Another example of debug function, to easily test the zombie code.
     */
    public function debug_playAutomatically(int $moves = 50) {
        $count = 0;
        while (intval($this->gamestate->getCurrentMainStateId()) < 99 && $count < $moves) {
            $count++;
            foreach($this->gamestate->getActivePlayerList() as $playerId) {
                $playerId = (int)$playerId;
                $this->gamestate->runStateClassZombie($this->gamestate->getCurrentState($playerId), $playerId);
            }
        }
    }

    /*
    Another example of debug function, to easily create situations you want to test.
    Here, put a card you want to test in your hand (assuming you use the Deck component).

    public function debug_setCardInHand(int $cardType, int $playerId) {
        $card = array_values($this->cards->getCardsOfType($cardType))[0];
        $this->cards->moveCard($card['id'], 'hand', $playerId);
    }
    */
}
