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

use Bga\Games\itarenagame\BadgersData;
use Bga\Games\itarenagame\States\PlayerTurn; // Добавляем класс PlayerTurn для работы с ходом игрока
use Bga\GameFramework\Components\Counters\PlayerCounter; // Добавляем класс PlayerCounter
use Bga\GameFramework\Components\Deck; // Добавляем класс Deck
use Bga\Games\itarenagame\EventCardsData; // Добавляем класс EventCardsData для работы с картами событий
use Bga\Games\itarenagame\FoundersData;
use Bga\Games\itarenagame\SpecialistsData;
use Bga\Games\itarenagame\TaskTokensData;
use Bga\Games\itarenagame\ProjectTokensData;

class Game extends \Bga\GameFramework\Table
{
    public static array $CARD_TYPES;

    public PlayerCounter $playerEnergy;
    public PlayerCounter $playerBadgers; // Добавляем переменную для работы с баджерсами
    public Deck $eventDeck; // Добавляем переменную для работы с колодой карт событий

    private array $founderAssignments = [];

    // Константы режимов игры (соответствуют gameoptions.json)
    public const GAME_MODE_TUTORIAL = 1; // Обучающий режим
    public const GAME_MODE_MAIN = 2; // Основной режим

    /**
     * Получает режим игры из опций таблицы
     * @return int Режим игры (GAME_MODE_TUTORIAL или GAME_MODE_MAIN)
     */
    public function getGameMode(): int
    {
        $mode = $this->tableOptions->get(100); // 100 - ID опции в gameoptions.json
        
        // Логирование для отладки
        error_log('Game::getGameMode() - Raw option value: ' . var_export($mode, true) . ' (type: ' . gettype($mode) . ')');
        
        // Проверяем на null или не установленное значение
        if ($mode === null || $mode === false || $mode === 0 || $mode === '0' || $mode === '') {
            // Если опция не установлена, используем обучающий режим по умолчанию
            error_log('Game::getGameMode() - Option not set or invalid, defaulting to TUTORIAL');
            return self::GAME_MODE_TUTORIAL;
        }
        
        // Преобразуем в число (обрабатываем и строки, и числа)
        $modeInt = (int)$mode;
        
        error_log('Game::getGameMode() - Parsed mode value: ' . $modeInt . ' (1=TUTORIAL, 2=MAIN)');
        
        // Убеждаемся, что возвращаем валидное значение
        // 1 = Обучающий (TUTORIAL)
        // 2 = Основной (MAIN)
        if ($modeInt === self::GAME_MODE_TUTORIAL) {
            error_log('Game::getGameMode() - Returning TUTORIAL mode (1)');
            return self::GAME_MODE_TUTORIAL;
        }
        
        if ($modeInt === self::GAME_MODE_MAIN) {
            error_log('Game::getGameMode() - Returning MAIN mode (2)');
            return self::GAME_MODE_MAIN;
        }
        
        // Если значение неожиданное, используем обучающий режим по умолчанию
        error_log('Game::getGameMode() - Unexpected mode value: ' . $modeInt . ', defaulting to TUTORIAL');
        return self::GAME_MODE_TUTORIAL;
    }

    /**
     * Проверяет, является ли текущий режим игры обучающим
     * @return bool
     */
    public function isTutorialMode(): bool
    {
        $mode = $this->getGameMode();
        $isTutorial = $mode === self::GAME_MODE_TUTORIAL;
        error_log('Game::isTutorialMode() - Mode: ' . $mode . ', isTutorial: ' . ($isTutorial ? 'true' : 'false'));
        return $isTutorial;
    }

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
    public function getCubeFaces(): array
    {
        return [
            'P', 'A', 'E', 'I', 'P', 'A', 'E', 'I', 'P', 'A', 'E', 'I', 'SF', 'SF', 'PA', 'PE', 'PI', 'AE', 'AI', 'EI'
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
            'badgers_supply_1' => 20,
            'badgers_supply_2' => 21,
            'badgers_supply_3' => 22,
            'badgers_supply_5' => 23,
            'badgers_supply_10' => 24,
        ]); // mandatory, even if the array is empty

        $this->playerEnergy = $this->counterFactory->createPlayerCounter('energy');
        $this->playerBadgers = $this->counterFactory->createPlayerCounter('badgers');
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
            "SELECT `player_id` `id`, `player_score` `score`, `player_color` `color` FROM `player`"
        );
        $this->playerEnergy->fillResult($result);
        $this->playerBadgers->fillResult($result);

        $basicInfos = $this->loadPlayersBasicInfos();
        $foundersByPlayer = $this->getFoundersByPlayer();
        $penaltyTokensByPlayer = $this->getPenaltyTokensByPlayer(); // Получаем жетоны штрафа для всех игроков
        
        // Получаем жетоны задач для всех игроков
        $taskTokensByPlayer = [];
        foreach ($result["players"] as $player) {
            $playerId = (int)($player['id'] ?? 0);
            $taskTokensByPlayer[$playerId] = $this->getTaskTokensByPlayer($playerId);
        }
        
        foreach ($result["players"] as &$player) {
            $playerId = (int)($player['id'] ?? 0);
            $color = $this->resolvePlayerColor($player, $basicInfos, $playerId); // Цвет игрока
            $player['color'] = $color;
            if (isset($foundersByPlayer[$playerId])) { // Если игрок имеет основателя
                $player['founder'] = $foundersByPlayer[$playerId];
            }
            // Добавляем жетоны штрафа для игрока
            $player['penaltyTokens'] = $penaltyTokensByPlayer[$playerId] ?? [];
            // Добавляем жетоны задач для игрока
            $player['taskTokens'] = $taskTokensByPlayer[$playerId] ?? [];
            // Добавляем жетоны проектов для игрока
            $player['projectTokens'] = $this->getProjectTokensByPlayer($playerId);
        }
        unset($player);

        // Добавляем жетоны проектов на планшете (доступны всем игрокам)
        $result['projectTokensOnBoard'] = $this->getProjectTokensOnBoard();

        // Round info for client banner
        $result['round'] = (int)$this->getGameStateValue('round_number'); // Текущий раунд
        $result['totalRounds'] = (int)$this->getGameStateValue('total_rounds'); // Общее количество раундов
        $result['stageName'] = $this->getStageName($result['round']); // Название этапа
        $faces = $this->getCubeFaces(); // Значения граней кубика
        $faceIndex = (int)$this->getGameStateValue('round_cube_face'); // Индекс текущей грани кубика (0..19)
        $result['cubeFace'] = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : ''; // Значение кубика на раунд
        
        // Логирование для отладки
        error_log('Game::getAllDatas() - cubeFace: ' . var_export($result['cubeFace'], true) . ', faceIndex: ' . $faceIndex);
        // Текущее имя фазы из глобальной переменной (переводим ключ в название)
        $phaseKey = $this->globals->get('current_phase_name', '');
        $result['phaseName'] = $this->getPhaseName($phaseKey);
        $result['eventCards'] = EventCardsData::getAllCards();
        $roundEventCards = $this->getRoundEventCards();
        $result['roundEventCards'] = $roundEventCards;
        $result['roundEventCard'] = $roundEventCards[0] ?? null;
        $result['badgers'] = $this->getBadgersSupply();
        $result['founders'] = $foundersByPlayer; // Данные по основателям игроков
        $result['gameMode'] = $this->getGameMode(); // Режим игры (1 - Обучающий, 2 - Основной)
        $result['isTutorialMode'] = $this->isTutorialMode(); // Является ли режим обучающим
        
        // В основном режиме передаем карты на выбор для текущего игрока (если еще не выбрал)
        // Также передаем для активного игрока, если состояние - FounderSelection
        if (!$this->isTutorialMode()) {
            error_log('getAllDatas - Main mode: checking founder options');
            
            // ВАЖНО: Проверяем, выбрал ли игрок уже карту основателя
            $currentPlayerHasFounder = $this->globals->get('founder_player_' . $current_player_id, null) !== null;
            error_log('getAllDatas - Current player ' . $current_player_id . ' has founder: ' . ($currentPlayerHasFounder ? 'yes' : 'no'));
            
            // Получаем опции для текущего игрока ТОЛЬКО если он еще не выбрал карту
            $founderOptions = [];
            if (!$currentPlayerHasFounder) {
                $founderOptions = $this->getFounderOptionsForPlayer($current_player_id);
                error_log('getAllDatas - Current player ' . $current_player_id . ' has ' . count($founderOptions) . ' founder options');
            } else {
                error_log('getAllDatas - Skipping founderOptions for current player (already has founder)');
            }
            
            if (!empty($founderOptions)) {
                $result['founderOptions'] = $founderOptions; // Карты на выбор для текущего игрока
                error_log('getAllDatas - Added founderOptions to result: ' . count($founderOptions) . ' cards');
            }
            
            // Также проверяем активного игрока (может отличаться от текущего)
            $activePlayerId = $this->getActivePlayerId();
            error_log('getAllDatas - Active player ID: ' . ($activePlayerId !== null ? $activePlayerId : 'null'));
            
            if ($activePlayerId !== null && $activePlayerId !== $current_player_id) {
                // Проверяем, выбрал ли активный игрок уже карту
                $activePlayerHasFounder = $this->globals->get('founder_player_' . (int)$activePlayerId, null) !== null;
                if (!$activePlayerHasFounder) {
                    $activeFounderOptions = $this->getFounderOptionsForPlayer((int)$activePlayerId);
                    error_log('getAllDatas - Active player ' . $activePlayerId . ' has ' . count($activeFounderOptions) . ' founder options');
                    if (!empty($activeFounderOptions)) {
                        $result['activeFounderOptions'] = $activeFounderOptions; // Карты на выбор для активного игрока
                        error_log('getAllDatas - Added activeFounderOptions to result: ' . count($activeFounderOptions) . ' cards');
                    }
                } else {
                    error_log('getAllDatas - Active player ' . $activePlayerId . ' already has founder, skipping options');
                }
            } elseif ($activePlayerId !== null && $activePlayerId === $current_player_id) {
                // Если активный игрок = текущий, используем те же опции
                if (!empty($founderOptions)) {
                    $result['activeFounderOptions'] = $founderOptions;
                    error_log('getAllDatas - Added activeFounderOptions (same as founderOptions): ' . count($founderOptions) . ' cards');
                } else {
                    error_log('getAllDatas - Active player = current player, no options (already has founder or none available)');
                }
            } else {
                error_log('getAllDatas - Active player is null or different from current player');
            }
            
            // ВАЖНО: Также передаем опции для ВСЕХ игроков, чтобы клиент мог их отобразить
            // Это нужно, потому что getAllDatas может вызываться до onEnteringState
            // НО: Только для игроков, которые еще не выбрали карту!
            $allPlayersFounderOptions = [];
            foreach ($result["players"] as $player) {
                $playerId = (int)($player['id'] ?? 0);
                // Проверяем, выбрал ли этот игрок уже карту
                $playerHasFounder = $this->globals->get('founder_player_' . $playerId, null) !== null;
                if (!$playerHasFounder) {
                    $playerOptions = $this->getFounderOptionsForPlayer($playerId);
                    if (!empty($playerOptions)) {
                        $allPlayersFounderOptions[$playerId] = $playerOptions;
                        error_log('getAllDatas - Player ' . $playerId . ' has ' . count($playerOptions) . ' founder options');
                    }
                } else {
                    error_log('getAllDatas - Player ' . $playerId . ' already has founder, skipping options');
                }
            }
            if (!empty($allPlayersFounderOptions)) {
                $result['allPlayersFounderOptions'] = $allPlayersFounderOptions;
                error_log('getAllDatas - Added allPlayersFounderOptions with ' . count($allPlayersFounderOptions) . ' players');
            }
        } else {
            error_log('getAllDatas - Tutorial mode: skipping founder options');
        }

        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        return $result;
    }

    /**
     * This method is called only once, when a new game is launched. In this method, you must setup the game
     *  according to the game rules, so that the game is ready to be played.
     */
    protected function setupNewGame($players, $options = [])
    {
        $playerIds = array_keys($players); // Идентификаторы игроков
        $this->playerEnergy->initDb($playerIds, initialValue: 2);
        $this->playerBadgers->initDb($playerIds, initialValue: 0);

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

        foreach (BadgersData::getInitialSupply() as $value => $quantity) {
            $this->setGameStateInitialValue('badgers_supply_' . $value, $quantity);
        }

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
        // Переходим в состояние подготовки игры
        // Вся логика подготовки (раздача денег, карт, установка планшетов) будет в GameSetup
        return \Bga\Games\itarenagame\States\GameSetup::class;
    }

    public function getRoundEventCards(): array
    {
        $cards = $this->eventDeck->getCardsInLocation('table');
        $result = [];

        foreach ($cards as $card) {
            $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
            if ($data !== null) {
                $card = array_merge($card, $data);
            }

            $result[] = $card;
        }

        return $result;
    }

    /**
     * Возвращает данные по доступной валюте "Баджерс".
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBadgersSupply(): array
    {
        $coins = [];

        foreach (BadgersData::getDenominations() as $value => $coinData) {
            $available = (int)$this->getGameStateValue('badgers_supply_' . $value);
            $coins[] = [
                'value' => $value,
                'name' => $coinData['name'],
                'label' => $coinData['short_label'],
                'display_label' => $value === 1
                    ? clienttranslate('Баджерс')
                    : ($coinData['short_label'] ?? ''),
                'initial_quantity' => (int)$coinData['quantity'],
                'available_quantity' => $available,
                'image_url' => $coinData['image_url'],
            ];
        }

        usort($coins, static fn(array $a, array $b): int => $a['value'] <=> $b['value']);

        return $coins;
    }

    // Определяем цвет игрока
    private function resolvePlayerColor(array $player, array $basicInfos, int $playerId): string
    {
        $color = (string)($player['color'] ?? '');

        if ($color === '' && isset($basicInfos[$playerId]['player_color'])) {
            $color = (string)$basicInfos[$playerId]['player_color'];
        }

        if ($color === '') {
            $color = (string)$this->getPlayerColorById($playerId);
        }

        if ($color !== '') {
            $color = '#' . ltrim($color, '#');
        } else {
            $color = '#FFFFFF';
        }

        return $color;
    }

    public function distributeInitialBadgers(array $playerIds, int $amountPerPlayer = 5): void // Распределяем начальные баджерсы между игроками
    {
        error_log('distributeInitialBadgers - Starting distribution. Players: ' . count($playerIds) . ', Amount per player: ' . $amountPerPlayer);
        
        if (empty($playerIds) || $amountPerPlayer <= 0) {
            error_log('distributeInitialBadgers - ERROR: Empty playerIds or invalid amountPerPlayer');
            return;
        }

        $totalNeeded = $amountPerPlayer * count($playerIds);
        $totalAvailable = $this->getTotalBadgersValue();
        error_log('distributeInitialBadgers - Total needed: ' . $totalNeeded . ', Total available: ' . $totalAvailable);
        
        if ($totalAvailable < $totalNeeded) {
            error_log('distributeInitialBadgers - ERROR: Not enough badgers in bank. Available: ' . $totalAvailable . ', Needed: ' . $totalNeeded);
            return;
        }

        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            error_log('distributeInitialBadgers - Processing player ' . $playerId);
            
            // Получаем текущее количество баджерсов игрока до распределения
            $currentBadgers = $this->playerBadgers->get($playerId);
            error_log('distributeInitialBadgers - Player ' . $playerId . ' current badgers: ' . $currentBadgers);
            
            if (!$this->withdrawBadgersFromBank($amountPerPlayer)) {
                error_log('distributeInitialBadgers - ERROR: Failed to withdraw ' . $amountPerPlayer . ' badgers from bank for player ' . $playerId);
                break;
            }

            $this->playerBadgers->inc($playerId, $amountPerPlayer);
            
            // Проверяем, что баджерсы были добавлены
            $newBadgers = $this->playerBadgers->get($playerId);
            error_log('distributeInitialBadgers - Player ' . $playerId . ' new badgers: ' . $newBadgers . ' (added: ' . $amountPerPlayer . ')');
            
            if ($newBadgers !== $currentBadgers + $amountPerPlayer) {
                error_log('distributeInitialBadgers - WARNING: Badgers count mismatch for player ' . $playerId . '. Expected: ' . ($currentBadgers + $amountPerPlayer) . ', Got: ' . $newBadgers);
            }
        }
        
        error_log('distributeInitialBadgers - Distribution completed');
    }

    private function withdrawBadgersFromBank(int $amount): bool // Снимаем баджерсы с банка
    {
        if ($amount <= 0) {
            return true;
        }

        $available = [];
        foreach (BadgersData::getDenominations() as $value => $coinData) {
            $available[$value] = (int)$this->getGameStateValue('badgers_supply_' . $value);
        }

        $denominations = array_keys($available);
        rsort($denominations);

        $combination = $this->findBadgersCombination($amount, $available, $denominations, 0);
        if ($combination === null) {
            return false;
        }

        foreach ($combination as $value => $count) {
            if ($count <= 0) {
                continue;
            }
            $current = (int)$this->getGameStateValue('badgers_supply_' . $value);
            $this->setGameStateValue('badgers_supply_' . $value, max(0, $current - $count));
        }

        return true;
    }

    private function findBadgersCombination(int $amount, array $available, array $values, int $index): ?array // Находим комбинацию баджерсов для снятия
    {
        if ($amount === 0) {
            return [];
        }

        if (!isset($values[$index])) {
            return null;
        }

        $value = (int)$values[$index];
        $maxCount = min($available[$value] ?? 0, intdiv($amount, $value));

        for ($count = $maxCount; $count >= 0; $count--) {
            $remaining = $amount - ($count * $value);
            $nextAvailable = $available;
            if ($count > 0) {
                $nextAvailable[$value] -= $count;
            }

            $result = $this->findBadgersCombination($remaining, $nextAvailable, $values, $index + 1);
            if ($result !== null) {
                if ($count > 0) {
                    $result[$value] = ($result[$value] ?? 0) + $count;
                }
                return $result;
            }
        }

        return null;
    }

    private function getTotalBadgersValue(): int // Возвращаем общее количество баджерсов
    {
        $total = 0;
        foreach (BadgersData::getDenominations() as $value => $coinData) {
            $total += $value * (int)$this->getGameStateValue('badgers_supply_' . $value);
        }

        return $total;
    }

    public function assignInitialFounders(array $playerIds): void // Выдаем карты основателей игрокам
    {
        if (empty($playerIds)) {
            return;
        }

        $existing = $this->getFoundersByPlayer();
        if (!empty($existing)) {
            return;
        }

        $founders = FoundersData::getAllCards();
        if (empty($founders)) {
            return;
        }

        // В обучающем режиме используем только основателей с firstGame = true
        $isTutorial = $this->isTutorialMode();
        $gameMode = $this->getGameMode();
        error_log('assignInitialFounders - START');
        error_log('assignInitialFounders - Game mode: ' . $gameMode . ' (1=TUTORIAL, 2=MAIN)');
        error_log('assignInitialFounders - isTutorialMode: ' . ($isTutorial ? 'true' : 'false'));
        error_log('assignInitialFounders - Player count: ' . count($playerIds));
        error_log('assignInitialFounders - Total founders before filter: ' . count($founders));
        
        if ($isTutorial) {
            $foundersBeforeFilter = count($founders);
            error_log('assignInitialFounders - Tutorial mode: filtering founders with firstGame = true');
            
            // Фильтруем только карты с firstGame = true (строгая проверка)
            $filteredFounders = [];
            foreach ($founders as $cardId => $founder) {
                $hasFirstGame = isset($founder['firstGame']);
                $firstGameValue = $hasFirstGame ? $founder['firstGame'] : null;
                $firstGameType = $hasFirstGame ? gettype($founder['firstGame']) : 'not set';
                
                // Строгая проверка: только true (булево), 1 (число) или '1' (строка)
                $isTrue = $hasFirstGame && (
                    $founder['firstGame'] === true || 
                    $founder['firstGame'] === 1 || 
                    $founder['firstGame'] === '1'
                );
                
                $founderId = $founder['id'] ?? $cardId;
                $founderName = $founder['name'] ?? 'unknown';
                
                if ($isTrue) {
                    $filteredFounders[$cardId] = $founder;
                    error_log('assignInitialFounders - Keeping founder ID ' . $founderId . ' (' . $founderName . ') - firstGame: ' . var_export($firstGameValue, true) . ' (type: ' . $firstGameType . ')');
                } else {
                    error_log('assignInitialFounders - Filtering out founder ID ' . $founderId . ' (' . $founderName . ') - firstGame: ' . var_export($firstGameValue, true) . ' (type: ' . $firstGameType . ')');
                }
            }
            
            $founders = $filteredFounders;
            $foundersAfterFilter = count($founders);
            error_log('assignInitialFounders - Total founders after filter: ' . $foundersAfterFilter . ' (filtered out: ' . ($foundersBeforeFilter - $foundersAfterFilter) . ')');
        } else {
            error_log('assignInitialFounders - NOT tutorial mode, using all founders');
        }

        $availableIds = array_keys($founders);
        if (empty($availableIds)) {
            throw new \RuntimeException('No founder cards available for assignment.');
        }
        
        // Проверяем достаточность карт в зависимости от режима
        if ($isTutorial) {
            // В обучающем режиме: каждому игроку нужна 1 карта
            if (count($availableIds) < count($playerIds)) {
                throw new \RuntimeException('Not enough founder cards to assign unique founders to all players. Available: ' . count($availableIds) . ', Required: ' . count($playerIds));
            }
        } else {
            // В основном режиме: каждому игроку нужно 3 карты для выбора
            $requiredCards = count($playerIds) * 3;
            if (count($availableIds) < $requiredCards) {
                throw new \RuntimeException('Not enough founder cards for selection. Available: ' . count($availableIds) . ', Required: ' . $requiredCards . ' (3 per player for ' . count($playerIds) . ' players)');
            }
        }
        
        // Дополнительная проверка в обучающем режиме: убеждаемся, что все карты имеют firstGame = true
        if ($isTutorial) {
            foreach ($availableIds as $cardId) {
                $founder = $founders[$cardId] ?? null;
                if ($founder === null) {
                    error_log('assignInitialFounders - WARNING: Founder ID ' . $cardId . ' not found in filtered array!');
                    continue;
                }
                $firstGame = $founder['firstGame'] ?? null;
                $name = $founder['name'] ?? 'unknown';
                if (!($firstGame === true || $firstGame === 1 || $firstGame === '1')) {
                    error_log('assignInitialFounders - ERROR: Founder ID ' . $cardId . ' (' . $name . ') has firstGame = ' . var_export($firstGame, true) . ' but should be true!');
                    throw new \RuntimeException('Invalid founder card in tutorial mode: ID ' . $cardId . ' (' . $name . ') has firstGame = ' . var_export($firstGame, true));
                }
                error_log('assignInitialFounders - Verified founder ID ' . $cardId . ' (' . $name . ') - firstGame: ' . var_export($firstGame, true));
            }
        }
        
        shuffle($availableIds);

        if ($isTutorial) {
            // В обучающем режиме: раздаем по одной карте каждому игроку
            foreach ($playerIds as $playerId) {
                $playerId = (int)$playerId;
                $cardId = (int)array_shift($availableIds);
                $founder = $founders[$cardId] ?? null;
                $founderName = $founder['name'] ?? 'unknown';
                error_log('assignInitialFounders - Assigning founder ID ' . $cardId . ' (' . $founderName . ') to player ' . $playerId);
                
                // Получаем данные карты, чтобы проверить её department
                $founderCard = FoundersData::getCard($cardId);
                $founderDepartment = $founderCard['department'] ?? 'universal';
                
                // Если универсальная, оставляем на руке (department='universal')
                // Иначе автоматически размещаем в указанном отделе
                if ($founderDepartment !== 'universal') {
                    error_log('assignInitialFounders - Tutorial: Founder ' . $cardId . ' has department ' . $founderDepartment . ', placing automatically');
                    $this->setFounderForPlayer($playerId, $cardId, $founderDepartment);
                } else {
                    error_log('assignInitialFounders - Tutorial: Founder ' . $cardId . ' is universal, leaving in hand');
                    $this->setFounderForPlayer($playerId, $cardId, 'universal');
                }
            }
        } else {
            // В основном режиме: раздаем по 3 карты каждому игроку для выбора
            error_log('assignInitialFounders - MAIN MODE: Distributing 3 cards per player');
            error_log('assignInitialFounders - Available card IDs count: ' . count($availableIds));
            
            foreach ($playerIds as $playerId) {
                $playerId = (int)$playerId;
                error_log('assignInitialFounders - Processing player ' . $playerId);
                $playerOptions = [];
                
                // Берем 3 карты для этого игрока
                for ($i = 0; $i < 3; $i++) {
                    if (empty($availableIds)) {
                        error_log('assignInitialFounders - ERROR: Not enough cards! Need 3, but only ' . count($availableIds) . ' available for player ' . $playerId);
                        throw new \RuntimeException('Not enough founder cards for player ' . $playerId . '. Need 3, but only ' . count($availableIds) . ' available.');
                    }
                    $cardId = (int)array_shift($availableIds);
                    $playerOptions[] = $cardId;
                    error_log('assignInitialFounders - Added card ID ' . $cardId . ' to player ' . $playerId . ' options (card ' . ($i + 1) . '/3)');
                }
                
                error_log('assignInitialFounders - Player ' . $playerId . ' options array: ' . var_export($playerOptions, true));
                
                // Сохраняем опции для игрока в globals
                $key = 'founder_options_' . $playerId;
                $jsonValue = json_encode($playerOptions);
                error_log('assignInitialFounders - Saving to globals. Key: ' . $key . ', JSON: ' . $jsonValue);
                
                $this->globals->set($key, $jsonValue);
                error_log('assignInitialFounders - Saved to globals successfully');
                
                // Проверяем, что данные сохранились
                $savedValue = $this->globals->get($key, null);
                error_log('assignInitialFounders - Verification: retrieved value from globals: ' . var_export($savedValue, true));
                
                if ($savedValue !== $jsonValue) {
                    error_log('assignInitialFounders - ERROR: Saved value does not match! Expected: ' . $jsonValue . ', Got: ' . var_export($savedValue, true));
                } else {
                    error_log('assignInitialFounders - Verification PASSED: values match');
                }
                
                // Дополнительная проверка через getFounderOptionsForPlayer
                $retrievedOptions = $this->getFounderOptionsForPlayer($playerId);
                error_log('assignInitialFounders - Retrieved through getFounderOptionsForPlayer: ' . count($retrievedOptions) . ' cards');
                if (count($retrievedOptions) !== 3) {
                    error_log('assignInitialFounders - ERROR: Retrieved options count mismatch! Expected: 3, Got: ' . count($retrievedOptions));
                }
            }
            
            error_log('assignInitialFounders - MAIN MODE: Distribution completed for all players');
        }
    }

    private function setFounderForPlayer(int $playerId, int $cardId, ?string $department = null): void
    {
        $this->founderAssignments[$playerId] = $cardId;
        $this->globals->set('founder_player_' . $playerId, $cardId);
        if ($department !== null) {
            $this->globals->set('founder_department_' . $playerId, $department);
        }
    }

    /**
     * Получает карты основателей на выбор для игрока (только для основного режима)
     * @param int $playerId
     * @return array Массив карт основателей
     */
    public function getFounderOptionsForPlayer(int $playerId): array
    {
        $key = 'founder_options_' . $playerId;
        $optionsJson = $this->globals->get($key, null);
        
        error_log('getFounderOptionsForPlayer - Player ID: ' . $playerId . ', Key: ' . $key . ', JSON: ' . var_export($optionsJson, true));
        
        if ($optionsJson === null) {
            error_log('getFounderOptionsForPlayer - No options found for player ' . $playerId);
            return [];
        }

        $cardIds = json_decode($optionsJson, true);
        error_log('getFounderOptionsForPlayer - Decoded card IDs: ' . var_export($cardIds, true));
        
        if (!is_array($cardIds)) {
            error_log('getFounderOptionsForPlayer - Decoded value is not an array: ' . gettype($cardIds));
            return [];
        }

        if (empty($cardIds)) {
            error_log('getFounderOptionsForPlayer - Card IDs array is empty');
            return [];
        }

        $result = [];
        foreach ($cardIds as $cardId) {
            $card = FoundersData::getCard((int)$cardId);
            if ($card !== null) {
                $result[] = $card;
                // Логирование для отладки
                error_log('getFounderOptionsForPlayer - Found card ID ' . $cardId . ': ' . ($card['name'] ?? 'unknown'));
            } else {
                error_log('getFounderOptionsForPlayer - WARNING: Card ID ' . $cardId . ' not found in FoundersData');
            }
        }

        error_log('getFounderOptionsForPlayer - Returning ' . count($result) . ' cards for player ' . $playerId);
        return $result;
    }

    /**
     * Выбирает карту основателя для игрока (только для основного режима)
     * @param int $playerId
     * @param int $cardId
     */
    public function selectFounderForPlayer(int $playerId, int $cardId): void
    {
        // Проверяем, что карта доступна для выбора
        $options = $this->getFounderOptionsForPlayer($playerId);
        $availableIds = array_column($options, 'id');
        
        if (!in_array($cardId, $availableIds, true)) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Invalid founder card selected'));
        }

        // Проверяем, что карта еще не выбрана
        $existingCardId = $this->globals->get('founder_player_' . $playerId, null);
        if ($existingCardId !== null) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Founder already selected'));
        }

        // Получаем данные карты, чтобы проверить её department
        $founderCard = FoundersData::getCard($cardId);
        $founderDepartment = $founderCard['department'] ?? 'universal';
        
        // Отправляем две другие карты в отбой (не выбранные карты)
        $discardedCardIds = array_filter($availableIds, fn($id) => $id !== $cardId);
        if (!empty($discardedCardIds)) {
            // Сохраняем информацию о сброшенных картах для уведомления
            $discardedCards = [];
            foreach ($discardedCardIds as $discardedId) {
                $discardedCard = FoundersData::getCard($discardedId);
                if ($discardedCard !== null) {
                    $discardedCards[] = $discardedCard['name'] ?? clienttranslate('Неизвестная карта');
                }
            }
            
            // Уведомляем о сбросе карт
            $this->notify->all('founderCardsDiscarded', clienttranslate('Две карты основателя отправлены в отбой'), [
                'player_id' => $playerId,
                'player_name' => $this->getPlayerNameById($playerId),
                'discarded_cards' => $discardedCards,
            ]);
        }
        
        // Если карта не универсальная, автоматически размещаем её в соответствующий отдел
        // Если универсальная, оставляем на руке (department='universal')
        if ($founderDepartment !== 'universal') {
            // Автоматически размещаем карту в отдел
            $this->setFounderForPlayer($playerId, $cardId, $founderDepartment);
        } else {
            // Оставляем на руке для ручного размещения
            $this->setFounderForPlayer($playerId, $cardId, 'universal');
        }

        // Удаляем опции выбора (они больше не нужны - две другие карты ушли в отбой)
        $this->globals->set('founder_options_' . $playerId, null);
    }

    /**
     * Проверяет, все ли игроки выбрали карты основателей
     * @return bool
     */
    public function allPlayersSelectedFounders(): bool
    {
        $playerIds = array_keys($this->loadPlayersBasicInfos());
        
        foreach ($playerIds as $playerId) {
            $cardId = $this->globals->get('founder_player_' . (int)$playerId, null);
            if ($cardId === null) {
                // Игрок еще не выбрал карту
                return false;
            }
        }
        
        return true;
    }

    public function placeFounder(int $playerId, string $department): void
    {
        $validDepartments = ['sales-department', 'back-office', 'technical-department'];
        $department = strtolower(trim($department));
        if (!in_array($department, $validDepartments, true)) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Invalid department'));
        }

        $cardId = $this->globals->get('founder_player_' . $playerId, null);
        if ($cardId === null) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Founder not assigned to player'));
        }

        $this->setFounderForPlayer($playerId, (int)$cardId, $department);
    }

    /**
     * Проверяет, есть ли у игрока универсальная карта основателя на руках
     * (не размещенная в отдел)
     */
    public function hasUnplacedUniversalFounder(int $playerId): bool
    {
        $founders = $this->getFoundersByPlayer();
        if (!isset($founders[$playerId])) {
            return false;
        }

        $founder = $founders[$playerId];
        $department = strtolower(trim($founder['department'] ?? ''));
        
        // Если отдел 'universal', значит карта еще на руках
        return $department === 'universal';
    }

    public function getFoundersByPlayer(): array
    {
        if (!empty($this->founderAssignments)) {
            return $this->expandFounders($this->founderAssignments);
        }

        $result = [];
        foreach ($this->loadPlayersBasicInfos() as $playerId => $info) {
            $value = $this->globals->get('founder_player_' . (int)$playerId, null);
            if ($value !== null) {
                $result[(int)$playerId] = (int)$value;
            }
        }

        if (!empty($result)) {
            $this->founderAssignments = $result;
        }

        return $this->expandFounders($result);
    }

    private function expandFounders(array $assignments): array
    {
        $result = [];
        foreach ($assignments as $playerId => $cardId) {
            $card = FoundersData::getCard((int)$cardId);
            if ($card !== null) {
                // Загружаем отдел из globals, если он был установлен
                // Если не установлен в globals, используем отдел из данных карты
                $department = $this->globals->get('founder_department_' . (int)$playerId, null);
                if ($department !== null) {
                    $card['department'] = $department;
                } elseif (isset($card['department'])) {
                    // Отдел уже есть в данных карты (например, 'universal')
                    // Оставляем как есть
                } else {
                    // Если отдела нет ни в globals, ни в данных карты, устанавливаем 'universal' по умолчанию
                    $card['department'] = 'universal';
                }
                $result[(int)$playerId] = $card;
            }
        }

        return $result;
    }

    public function prepareRoundEventCard(): array
    {
        $onTable = $this->eventDeck->getCardsInLocation('table');
        foreach ($onTable as $tableCard) {
            $this->eventDeck->moveCard((int)$tableCard['id'], 'discard');
        }

        $round = (int)$this->getGameStateValue('round_number');

        $cardIds = $this->selectEventCardIdsForRound($round);
        if (empty($cardIds)) {
            return [];
        }

        foreach ($cardIds as $cardId) {
            $this->eventDeck->moveCard($cardId, 'table');
        }

        return $this->getRoundEventCards();
    }

    private function selectEventCardIdsForRound(int $round): array
    {
        $deckCards = array_values($this->eventDeck->getCardsInLocation('deck'));
        if (empty($deckCards)) {
            return [];
        }

        $eligibleCardsSets = [];

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

            $index = bga_rand(0, count($eligibleCards) - 1);
            return [(int)$eligibleCards[$index]['id']];
        }

        if ($round === 4) {
            $eligibleCards = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 3; // Может быть только 3
            }));

            if (empty($eligibleCards)) {
                $eligibleCards = $deckCards;
            }

            $index = bga_rand(0, count($eligibleCards) - 1);
            return [(int)$eligibleCards[$index]['id']];
        }

        if ($round === 3) {
            $firstPool = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 1;
            }));

            $secondPool = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 2;
            }));

            $selectedIds = [];

            if (!empty($firstPool)) {
                $index = bga_rand(0, count($firstPool) - 1);
                $selectedIds[] = (int)$firstPool[$index]['id'];
                $deckCards = array_values(array_filter($deckCards, static fn(array $card): bool => (int)$card['id'] !== $selectedIds[0]));
            }

            if (!empty($secondPool)) {
                $secondPool = array_values(array_filter($deckCards, static function (array $card) use ($selectedIds): bool {
                    if (in_array((int)$card['id'], $selectedIds, true)) {
                        return false;
                    }
                    $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                    if ($data === null) {
                        return false;
                    }
                    return (int)($data['power_round'] ?? 0) === 2;
                }));

                if (!empty($secondPool)) {
                    $index = bga_rand(0, count($secondPool) - 1);
                    $selectedIds[] = (int)$secondPool[$index]['id'];
                }
            }

            if (empty($selectedIds) && !empty($deckCards)) {
                $index = bga_rand(0, count($deckCards) - 1);
                $selectedIds[] = (int)$deckCards[$index]['id'];
                $deckCards = array_values(array_filter($deckCards, static fn(array $card): bool => !in_array((int)$card['id'], $selectedIds, true)));
            }

            if (count($selectedIds) < 2 && !empty($deckCards)) {
                $index = bga_rand(0, count($deckCards) - 1);
                $selectedIds[] = (int)$deckCards[$index]['id'];
            }

            return $selectedIds;
        }

        if ($round === 5 || $round === 6) {
            $powerTwoPool = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 2;
            }));

            $powerThreePool = array_values(array_filter($deckCards, static function (array $card): bool {
                $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                if ($data === null) {
                    return false;
                }
                return (int)($data['power_round'] ?? 0) === 3;
            }));

            $selectedIds = [];

            if (!empty($powerTwoPool)) {
                $index = bga_rand(0, count($powerTwoPool) - 1);
                $selectedIds[] = (int)$powerTwoPool[$index]['id'];
                $deckCards = array_values(array_filter($deckCards, static fn(array $card): bool => (int)$card['id'] !== $selectedIds[0]));
            }

            if (!empty($powerThreePool)) {
                $powerThreePool = array_values(array_filter($deckCards, static function (array $card) use ($selectedIds): bool {
                    if (in_array((int)$card['id'], $selectedIds, true)) {
                        return false;
                    }
                    $data = EventCardsData::getCard((int)($card['type_arg'] ?? 0));
                    if ($data === null) {
                        return false;
                    }
                    return (int)($data['power_round'] ?? 0) === 3;
                }));

                if (!empty($powerThreePool)) {
                    $index = bga_rand(0, count($powerThreePool) - 1);
                    $selectedIds[] = (int)$powerThreePool[$index]['id'];
                }
            }

            if (empty($selectedIds) && !empty($deckCards)) {
                $index = bga_rand(0, count($deckCards) - 1);
                $selectedIds[] = (int)$deckCards[$index]['id'];
                $deckCards = array_values(array_filter($deckCards, static fn(array $card): bool => !in_array((int)$card['id'], $selectedIds, true)));
            }

            if (count($selectedIds) < 2 && !empty($deckCards)) {
                $index = bga_rand(0, count($deckCards) - 1);
                $selectedIds[] = (int)$deckCards[$index]['id'];
            }

            return $selectedIds;
        }

        $index = bga_rand(0, count($deckCards) - 1);
        return [(int)$deckCards[$index]['id']];
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

    /**
     * Раздает стартовые карты специалистов игрокам
     * @param array $playerIds Массив ID игроков
     */
    public function distributeStartingSpecialistCards(array $playerIds): void
    {
        if (empty($playerIds)) {
            return;
        }

        // Получаем все карты специалистов
        $allSpecialists = SpecialistsData::getAllCards();
        
        // Фильтруем только стартовые карты (starterOrFinisher = 'S')
        $startingSpecialists = [];
        foreach ($allSpecialists as $cardId => $card) {
            if (isset($card['starterOrFinisher']) && $card['starterOrFinisher'] === 'S') {
                $startingSpecialists[$cardId] = $card;
            }
        }

        if (empty($startingSpecialists)) {
            error_log('distributeStartingSpecialistCards - No starting specialist cards found');
            return;
        }

        // Раздаем по одной стартовой карте каждому игроку
        $availableIds = array_keys($startingSpecialists);
        shuffle($availableIds);

        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            if (empty($availableIds)) {
                // Если карт не хватает, перемешиваем заново
                $availableIds = array_keys($startingSpecialists);
                shuffle($availableIds);
            }
            
            $cardId = (int)array_shift($availableIds);
            $card = $startingSpecialists[$cardId] ?? null;
            
            if ($card !== null) {
                // Сохраняем карту специалиста для игрока в globals
                $playerSpecialists = json_decode($this->globals->get('specialist_cards_' . $playerId, '[]'), true);
                if (!is_array($playerSpecialists)) {
                    $playerSpecialists = [];
                }
                $playerSpecialists[] = $cardId;
                $this->globals->set('specialist_cards_' . $playerId, json_encode($playerSpecialists));
                
                error_log('distributeStartingSpecialistCards - Assigned specialist card ' . $cardId . ' to player ' . $playerId);
            }
        }
    }

    /**
     * Раздает стартовые проекты игрокам
     * @param array $playerIds Массив ID игроков
     */
    public function distributeStartingProjects(array $playerIds): void
    {
        if (empty($playerIds)) {
            return;
        }

        // TODO: Реализовать раздачу стартовых проектов
        // Пока что это заглушка - нужно будет добавить ProjectCardsData и логику раздачи
        error_log('distributeStartingProjects - Called for ' . count($playerIds) . ' players');
        
        // Пример структуры (когда будет реализовано):
        // $allProjects = ProjectCardsData::getAllCards();
        // $startingProjects = [];
        // foreach ($allProjects as $cardId => $card) {
        //     if (isset($card['isStarting']) && $card['isStarting'] === true) {
        //         $startingProjects[$cardId] = $card;
        //     }
        // }
        // 
        // foreach ($playerIds as $playerId) {
        //     // Раздаем проекты игроку
        //     $this->globals->set('project_cards_' . $playerId, json_encode([...]));
        // }
    }

    /**
     * Устанавливает компоненты на планшеты игроков
     * @param array $playerIds Массив ID игроков
     */
    public function setupPlayerBoards(array $playerIds): void
    {
        if (empty($playerIds)) {
            return;
        }

        // Устанавливаем начальные компоненты на планшеты
        // Это может включать размещение маркеров, установку начальных значений и т.д.
        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            
            // Инициализируем 2 пустых жетона штрафа для каждого игрока
            $this->initPenaltyTokens($playerId);
            
            // Устанавливаем флаг, что планшет готов
            $this->globals->set('board_setup_' . $playerId, true);
            
            error_log('setupPlayerBoards - Board setup completed for player ' . $playerId);
        }
    }

    /**
     * Инициализирует 2 пустых жетона штрафа для игрока
     * @param int $playerId ID игрока
     * @return void
     */
    private function initPenaltyTokens(int $playerId): void
    {
        // Удаляем существующие жетоны (если есть)
        $this->DbQuery("DELETE FROM `player_penalty_token` WHERE `player_id` = $playerId");
        
        // Создаем 2 пустых жетона (значение 0 означает, что жетон пустой)
        for ($order = 0; $order < 2; $order++) {
            $this->DbQuery("
                INSERT INTO `player_penalty_token` (`player_id`, `penalty_value`, `token_order`)
                VALUES ($playerId, 0, $order)
            ");
        }
        
        error_log("initPenaltyTokens - Initialized 2 penalty tokens for player $playerId");
    }

    /**
     * Получает жетоны штрафа для всех игроков
     * @return array Массив [playerId => [token1, token2]]
     */
    public function getPenaltyTokensByPlayer(): array
    {
        $result = [];
        
        $tokens = $this->getCollectionFromDb("
            SELECT `token_id`, `player_id`, `penalty_value`, `token_order`
            FROM `player_penalty_token`
            ORDER BY `player_id`, `token_order`
        ");
        
        foreach ($tokens as $token) {
            $playerId = (int)$token['player_id'];
            $order = (int)$token['token_order'];
            $value = (int)$token['penalty_value'];
            
            if (!isset($result[$playerId])) {
                $result[$playerId] = [];
            }
            
            $result[$playerId][$order] = [
                'token_id' => (int)$token['token_id'],
                'value' => $value,
                'order' => $order,
            ];
        }
        
        return $result;
    }

    /**
     * Устанавливает значение штрафа для жетона игрока
     * @param int $playerId ID игрока
     * @param int $tokenOrder Порядок жетона (0 или 1)
     * @param int $penaltyValue Значение штрафа (0 = пустой жетон, -1, -2, -3, -4, -5, -10)
     * @return void
     */
    public function setPenaltyToken(int $playerId, int $tokenOrder, int $penaltyValue): void
    {
        if ($tokenOrder < 0 || $tokenOrder > 1) {
            throw new \InvalidArgumentException("Token order must be 0 or 1");
        }
        
        $this->DbQuery("
            UPDATE `player_penalty_token`
            SET `penalty_value` = $penaltyValue
            WHERE `player_id` = $playerId AND `token_order` = $tokenOrder
        ");
        
        error_log("setPenaltyToken - Player $playerId, token $tokenOrder, value $penaltyValue");
    }

    /**
     * Распределяет начальные жетоны задач всем игрокам
     * Каждый игрок получает: 1 розовый жетон и 1 голубой жетон в бэклог
     * 
     * @param array $playerIds Массив ID игроков
     * @return void
     */
    public function distributeInitialTaskTokens(array $playerIds): void
    {
        if (empty($playerIds)) {
            return;
        }

        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            
            // Удаляем существующие жетоны (если есть)
            $this->DbQuery("DELETE FROM `player_task_token` WHERE `player_id` = $playerId");
            
            // Создаем 1 розовый жетон в бэклоге
            $this->DbQuery("
                INSERT INTO `player_task_token` (`player_id`, `color`, `location`, `row_index`)
                VALUES ($playerId, 'pink', 'backlog', NULL)
            ");
            
            // Создаем 1 голубой жетон в бэклоге
            $this->DbQuery("
                INSERT INTO `player_task_token` (`player_id`, `color`, `location`, `row_index`)
                VALUES ($playerId, 'cyan', 'backlog', NULL)
            ");
            
            error_log("distributeInitialTaskTokens - Player $playerId: 1 pink + 1 cyan token in backlog");
        }
    }

    /**
     * Получает жетоны задач для игрока
     * 
     * @param int $playerId ID игрока
     * @return array Массив жетонов задач
     */
    public function getTaskTokensByPlayer(int $playerId): array
    {
        $tokens = $this->getCollectionFromDb("
            SELECT `token_id`, `player_id`, `color`, `location`, `row_index`
            FROM `player_task_token`
            WHERE `player_id` = $playerId
            ORDER BY `token_id`
        ");
        
        $result = [];
        foreach ($tokens as $token) {
            $result[] = [
                'token_id' => (int)$token['token_id'],
                'color' => $token['color'],
                'location' => $token['location'],
                'row_index' => $token['row_index'] !== null ? (int)$token['row_index'] : null,
            ];
        }
        
        return $result;
    }

    /**
     * Получает жетоны проектов для игрока
     * @param int $playerId ID игрока
     * @return array Массив жетонов проектов игрока
     */
    public function getProjectTokensByPlayer(int $playerId): array
    {
        $tokens = $this->getCollectionFromDb("
            SELECT 
                ppt.`id`,
                ppt.`player_id`,
                ppt.`token_id`,
                ppt.`location`,
                pt.`number`,
                pt.`color`,
                pt.`shape`,
                pt.`name`,
                pt.`price`,
                pt.`effect`,
                pt.`effect_description`,
                pt.`victory_points`,
                pt.`player_count`,
                pt.`image_url`
            FROM `player_project_token` ppt
            INNER JOIN `project_token` pt ON ppt.`token_id` = pt.`token_id`
            WHERE ppt.`player_id` = $playerId
            ORDER BY ppt.`id`
        ");
        
        $result = [];
        foreach ($tokens as $token) {
            $result[] = [
                'id' => (int)$token['id'],
                'token_id' => (int)$token['token_id'],
                'player_id' => (int)$token['player_id'],
                'location' => $token['location'],
                'number' => (int)$token['number'],
                'color' => $token['color'],
                'shape' => $token['shape'],
                'name' => $token['name'],
                'price' => $token['price'],
                'effect' => $token['effect'],
                'effect_description' => $token['effect_description'],
                'victory_points' => (int)$token['victory_points'],
                'player_count' => (int)$token['player_count'],
                'image_url' => $token['image_url'],
            ];
        }
        
        return $result;
    }

    /**
     * Получает все жетоны проектов на планшете (не привязанные к игрокам)
     * @return array Массив жетонов проектов на планшете
     */
    public function getProjectTokensOnBoard(): array
    {
        $tokens = $this->getCollectionFromDb("
            SELECT 
                pt.`token_id`,
                pt.`number`,
                pt.`color`,
                pt.`shape`,
                pt.`name`,
                pt.`price`,
                pt.`effect`,
                pt.`effect_description`,
                pt.`victory_points`,
                pt.`player_count`,
                pt.`image_url`,
                pt.`board_position`
            FROM `project_token` pt
            LEFT JOIN `player_project_token` ppt ON pt.`token_id` = ppt.`token_id`
            WHERE ppt.`token_id` IS NULL
            AND pt.`board_position` IS NOT NULL
            ORDER BY pt.`board_position`, pt.`number`
        ");
        
        $result = [];
        foreach ($tokens as $token) {
            $result[] = [
                'token_id' => (int)$token['token_id'],
                'number' => (int)$token['number'],
                'color' => $token['color'],
                'shape' => $token['shape'],
                'name' => $token['name'],
                'price' => $token['price'],
                'effect' => $token['effect'],
                'effect_description' => $token['effect_description'],
                'victory_points' => (int)$token['victory_points'],
                'player_count' => (int)$token['player_count'],
                'image_url' => $token['image_url'],
                'board_position' => $token['board_position'],
            ];
        }
        
        return $result;
    }

    /**
     * Добавляет жетон проекта игроку
     * @param int $playerId ID игрока
     * @param int $tokenId ID жетона проекта
     * @param string $location Местоположение жетона (по умолчанию 'board')
     * @return void
     */
    public function addProjectTokenToPlayer(int $playerId, int $tokenId, string $location = 'board'): void
    {
        $this->DbQuery("
            INSERT INTO `player_project_token` (`player_id`, `token_id`, `location`)
            VALUES ($playerId, $tokenId, '" . addslashes($location) . "')
        ");
        
        error_log("addProjectTokenToPlayer - Added project token $tokenId to player $playerId at location $location");
    }

    /**
     * Инициализирует жетоны проектов в БД (если еще не инициализированы)
     * @return void
     */
    public function initializeProjectTokensIfNeeded(): void
    {
        $existingCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token`");
        if ($existingCount > 0) {
            return; // Уже инициализированы
        }
        
        // Получаем количество игроков
        $playerCount = count($this->loadPlayersBasicInfos());
        error_log("initializeProjectTokensIfNeeded - Player count: $playerCount");
        
        // Фильтруем жетоны по количеству игроков (player_count <= количество_игроков)
        $allTokens = ProjectTokensData::getAllTokens();
        $tokens = [];
        foreach ($allTokens as $token) {
            $tokenPlayerCount = (int)($token['player_count'] ?? 0);
            if ($tokenPlayerCount > 0 && $tokenPlayerCount <= $playerCount) {
                $tokens[] = $token;
            }
        }
        
        if (empty($tokens)) {
            error_log("initializeProjectTokensIfNeeded - WARNING: No tokens found for player count: $playerCount");
            return;
        }
        
        error_log("initializeProjectTokensIfNeeded - Filtered tokens count: " . count($tokens) . " (from " . count($allTokens) . " total)");
        
        // Разбиваем на части по 20 записей для оптимизации
        $batches = array_chunk($tokens, 20);
        
        foreach ($batches as $batch) {
            $values = [];
            foreach ($batch as $token) {
                $number = (int)($token['number'] ?? 0);
                $color = addslashes($token['color'] ?? '');
                $shape = addslashes($token['shape'] ?? '');
                $name = addslashes($token['name'] ?? '');
                $price = addslashes($token['price'] ?? '');
                $effect = addslashes($token['effect'] ?? '');
                $effectDescription = addslashes($token['effect_description'] ?? '');
                $victoryPoints = (int)($token['victory_points'] ?? 0);
                $playerCount = (int)($token['player_count'] ?? 0);
                $imageUrl = isset($token['image_url']) ? addslashes($token['image_url']) : null;
                $imageUrlSql = $imageUrl ? "'$imageUrl'" : 'NULL';
                
                $values[] = "($number, '$color', '$shape', '$name', '$price', '$effect', '$effectDescription', $victoryPoints, $playerCount, $imageUrlSql, NULL)";
            }
            
            if (!empty($values)) {
                $valuesString = implode(', ', $values);
                $this->DbQuery("
                    INSERT INTO `project_token` (
                        `number`, `color`, `shape`, `name`, `price`, 
                        `effect`, `effect_description`, `victory_points`, 
                        `player_count`, `image_url`, `board_position`
                    )
                    VALUES $valuesString
                ");
            }
        }
    }

    /**
     * Размещает жетоны проектов в красной колонке планшета проектов
     * @return void
     */
    public function placeProjectTokensOnRedColumn(): void
    {
        // Проверяем, не размещены ли уже жетоны
        $placedCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('red-hex', 'red-square', 'red-circle-1', 'red-circle-2')");
        if ($placedCount >= 4) {
            error_log("placeProjectTokensOnRedColumn - Tokens already placed, count: $placedCount");
            return; // Уже размещены
        }
        
        // Получаем количество игроков
        $playerCount = count($this->loadPlayersBasicInfos());
        error_log("placeProjectTokensOnRedColumn - Starting placement, current count: $placedCount, player count: $playerCount");
        
        // Получаем жетоны для размещения (только те, что еще не размещены, не у игроков и соответствуют количеству игроков)
        $hexTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'hex'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $squareTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'square'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $circleTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'circle'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 2
        ");
        
        // Размещаем hex
        if (!empty($hexTokens) && is_array($hexTokens)) {
            $hexTokens = array_values($hexTokens); // Переиндексируем массив
            if (isset($hexTokens[0]['token_id'])) {
                $tokenId = (int)$hexTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'red-hex' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnRedColumn - Placed hex token $tokenId at red-hex");
            } else {
                error_log("placeProjectTokensOnRedColumn - WARNING: hexTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnRedColumn - WARNING: No hex tokens found");
        }
        
        // Размещаем square
        if (!empty($squareTokens) && is_array($squareTokens)) {
            $squareTokens = array_values($squareTokens); // Переиндексируем массив
            if (isset($squareTokens[0]['token_id'])) {
                $tokenId = (int)$squareTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'red-square' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnRedColumn - Placed square token $tokenId at red-square");
            } else {
                error_log("placeProjectTokensOnRedColumn - WARNING: squareTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnRedColumn - WARNING: No square tokens found");
        }
        
        // Размещаем circle (2 штуки)
        if (!empty($circleTokens) && is_array($circleTokens)) {
            $circleTokens = array_values($circleTokens); // Переиндексируем массив
            $circlePositions = ['red-circle-1', 'red-circle-2'];
            foreach ($circleTokens as $index => $token) {
                if ($index < 2 && isset($token['token_id'])) {
                    $tokenId = (int)$token['token_id'];
                    $position = $circlePositions[$index];
                    $this->DbQuery("UPDATE `project_token` SET `board_position` = '$position' WHERE `token_id` = $tokenId");
                    error_log("placeProjectTokensOnRedColumn - Placed circle token $tokenId at $position");
                }
            }
        } else {
            error_log("placeProjectTokensOnRedColumn - WARNING: No circle tokens found");
        }
        
        $finalCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('red-hex', 'red-square', 'red-circle-1', 'red-circle-2')");
        error_log("placeProjectTokensOnRedColumn - Completed placement, final count: $finalCount");
    }

    /**
     * Размещает жетоны проектов в синей колонке планшета проектов
     * @return void
     */
    public function placeProjectTokensOnBlueColumn(): void
    {
        // Проверяем, не размещены ли уже жетоны
        $placedCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('blue-hex', 'blue-square', 'blue-circle-1', 'blue-circle-2')");
        if ($placedCount >= 4) {
            error_log("placeProjectTokensOnBlueColumn - Tokens already placed, count: $placedCount");
            return; // Уже размещены
        }
        
        // Получаем количество игроков
        $playerCount = count($this->loadPlayersBasicInfos());
        error_log("placeProjectTokensOnBlueColumn - Starting placement, current count: $placedCount, player count: $playerCount");
        
        // Получаем жетоны для размещения (только те, что еще не размещены, не у игроков и соответствуют количеству игроков)
        $hexTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'hex'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $squareTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'square'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $circleTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'circle'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 2
        ");
        
        // Размещаем hex
        if (!empty($hexTokens) && is_array($hexTokens)) {
            $hexTokens = array_values($hexTokens); // Переиндексируем массив
            if (isset($hexTokens[0]['token_id'])) {
                $tokenId = (int)$hexTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'blue-hex' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnBlueColumn - Placed hex token $tokenId at blue-hex");
            } else {
                error_log("placeProjectTokensOnBlueColumn - WARNING: hexTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnBlueColumn - WARNING: No hex tokens found");
        }
        
        // Размещаем square
        if (!empty($squareTokens) && is_array($squareTokens)) {
            $squareTokens = array_values($squareTokens); // Переиндексируем массив
            if (isset($squareTokens[0]['token_id'])) {
                $tokenId = (int)$squareTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'blue-square' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnBlueColumn - Placed square token $tokenId at blue-square");
            } else {
                error_log("placeProjectTokensOnBlueColumn - WARNING: squareTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnBlueColumn - WARNING: No square tokens found");
        }
        
        // Размещаем circle (2 штуки)
        if (!empty($circleTokens) && is_array($circleTokens)) {
            $circleTokens = array_values($circleTokens); // Переиндексируем массив
            $circlePositions = ['blue-circle-1', 'blue-circle-2'];
            foreach ($circleTokens as $index => $token) {
                if ($index < 2 && isset($token['token_id'])) {
                    $tokenId = (int)$token['token_id'];
                    $position = $circlePositions[$index];
                    $this->DbQuery("UPDATE `project_token` SET `board_position` = '$position' WHERE `token_id` = $tokenId");
                    error_log("placeProjectTokensOnBlueColumn - Placed circle token $tokenId at $position");
                }
            }
        } else {
            error_log("placeProjectTokensOnBlueColumn - WARNING: No circle tokens found");
        }
        
        $finalCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('blue-hex', 'blue-square', 'blue-circle-1', 'blue-circle-2')");
        error_log("placeProjectTokensOnBlueColumn - Completed placement, final count: $finalCount");
    }

    /**
     * Размещает жетоны проектов в зеленой колонке планшета проектов
     * @return void
     */
    public function placeProjectTokensOnGreenColumn(): void
    {
        // Проверяем, не размещены ли уже жетоны
        $placedCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('green-hex', 'green-square', 'green-circle-1', 'green-circle-2')");
        if ($placedCount >= 4) {
            error_log("placeProjectTokensOnGreenColumn - Tokens already placed, count: $placedCount");
            return; // Уже размещены
        }
        
        // Получаем количество игроков
        $playerCount = count($this->loadPlayersBasicInfos());
        error_log("placeProjectTokensOnGreenColumn - Starting placement, current count: $placedCount, player count: $playerCount");
        
        // Получаем жетоны для размещения (только те, что еще не размещены, не у игроков и соответствуют количеству игроков)
        // ВАЖНО: исключаем жетоны, которые уже размещены в других колонках
        $hexTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'hex'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $squareTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'square'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 1
        ");
        
        $circleTokens = $this->getCollectionFromDb("
            SELECT `token_id` FROM `project_token`
            WHERE `shape` = 'circle'
            AND `board_position` IS NULL
            AND `token_id` NOT IN (SELECT `token_id` FROM `player_project_token`)
            AND `player_count` <= $playerCount
            ORDER BY RAND()
            LIMIT 2
        ");
        
        // Размещаем hex
        if (!empty($hexTokens) && is_array($hexTokens)) {
            $hexTokens = array_values($hexTokens); // Переиндексируем массив
            if (isset($hexTokens[0]['token_id'])) {
                $tokenId = (int)$hexTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'green-hex' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnGreenColumn - Placed hex token $tokenId at green-hex");
            } else {
                error_log("placeProjectTokensOnGreenColumn - WARNING: hexTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnGreenColumn - WARNING: No hex tokens found");
        }
        
        // Размещаем square
        if (!empty($squareTokens) && is_array($squareTokens)) {
            $squareTokens = array_values($squareTokens); // Переиндексируем массив
            if (isset($squareTokens[0]['token_id'])) {
                $tokenId = (int)$squareTokens[0]['token_id'];
                $this->DbQuery("UPDATE `project_token` SET `board_position` = 'green-square' WHERE `token_id` = $tokenId");
                error_log("placeProjectTokensOnGreenColumn - Placed square token $tokenId at green-square");
            } else {
                error_log("placeProjectTokensOnGreenColumn - WARNING: squareTokens array is empty or invalid");
            }
        } else {
            error_log("placeProjectTokensOnGreenColumn - WARNING: No square tokens found");
        }
        
        // Размещаем circle (2 штуки)
        if (!empty($circleTokens) && is_array($circleTokens)) {
            $circleTokens = array_values($circleTokens); // Переиндексируем массив
            $circlePositions = ['green-circle-1', 'green-circle-2'];
            foreach ($circleTokens as $index => $token) {
                if ($index < 2 && isset($token['token_id'])) {
                    $tokenId = (int)$token['token_id'];
                    $position = $circlePositions[$index];
                    $this->DbQuery("UPDATE `project_token` SET `board_position` = '$position' WHERE `token_id` = $tokenId");
                    error_log("placeProjectTokensOnGreenColumn - Placed circle token $tokenId at $position");
                }
            }
        } else {
            error_log("placeProjectTokensOnGreenColumn - WARNING: No circle tokens found");
        }
        
        $finalCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('green-hex', 'green-square', 'green-circle-1', 'green-circle-2')");
        error_log("placeProjectTokensOnGreenColumn - Completed placement, final count: $finalCount");
    }

}
