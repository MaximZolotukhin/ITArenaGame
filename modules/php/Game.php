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
        
        foreach ($result["players"] as &$player) {
            $playerId = (int)($player['id'] ?? 0);
            $color = $this->resolvePlayerColor($player, $basicInfos, $playerId); // Цвет игрока
            $player['color'] = $color;
            if (isset($foundersByPlayer[$playerId])) { // Если игрок имеет основателя
                $player['founder'] = $foundersByPlayer[$playerId];
            }
            // Добавляем жетоны штрафа для игрока
            $player['penaltyTokens'] = $penaltyTokensByPlayer[$playerId] ?? [];
        }
        unset($player);

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
        if (!$this->isTutorialMode()) {
            $founderOptions = $this->getFounderOptionsForPlayer($current_player_id);
            if (!empty($founderOptions)) {
                $result['founderOptions'] = $founderOptions; // Карты на выбор для текущего игрока
            }
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

    private function getRoundEventCards(): array
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
        if (empty($playerIds) || $amountPerPlayer <= 0) {
            return;
        }

        if ($this->getTotalBadgersValue() < $amountPerPlayer * count($playerIds)) {
            return;
        }

        foreach ($playerIds as $playerId) {
            if (!$this->withdrawBadgersFromBank($amountPerPlayer)) {
                break;
            }

            $this->playerBadgers->inc((int)$playerId, $amountPerPlayer);
        }
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
        error_log('assignInitialFounders - isTutorialMode: ' . ($isTutorial ? 'true' : 'false'));
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
        if (count($availableIds) < count($playerIds)) {
            throw new \RuntimeException('Not enough founder cards to assign unique founders to all players. Available: ' . count($availableIds) . ', Required: ' . count($playerIds));
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
                $this->setFounderForPlayer($playerId, $cardId);
            }
        } else {
            // В основном режиме: раздаем по 3 карты каждому игроку для выбора
            foreach ($playerIds as $playerId) {
                $playerId = (int)$playerId;
                $playerOptions = [];
                
                // Берем 3 карты для этого игрока
                for ($i = 0; $i < 3; $i++) {
                    if (empty($availableIds)) {
                        throw new \RuntimeException('Not enough founder cards for player ' . $playerId . '. Need 3, but only ' . count($availableIds) . ' available.');
                    }
                    $cardId = (int)array_shift($availableIds);
                    $playerOptions[] = $cardId;
                }
                
                // Сохраняем опции для игрока в globals
                $this->globals->set('founder_options_' . $playerId, json_encode($playerOptions));
                error_log('assignInitialFounders - Assigned 3 founder options to player ' . $playerId . ': ' . implode(', ', $playerOptions));
            }
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
        $optionsJson = $this->globals->get('founder_options_' . $playerId, null);
        if ($optionsJson === null) {
            return [];
        }

        $cardIds = json_decode($optionsJson, true);
        if (!is_array($cardIds)) {
            return [];
        }

        $result = [];
        foreach ($cardIds as $cardId) {
            $card = FoundersData::getCard((int)$cardId);
            if ($card !== null) {
                $result[] = $card;
            }
        }

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
        
        // Если карта не универсальная, автоматически размещаем её в соответствующий отдел
        // Если универсальная, оставляем на руке (department='universal')
        if ($founderDepartment !== 'universal') {
            // Автоматически размещаем карту в отдел
            $this->setFounderForPlayer($playerId, $cardId, $founderDepartment);
        } else {
            // Оставляем на руке для ручного размещения
            $this->setFounderForPlayer($playerId, $cardId, 'universal');
        }

        // Удаляем опции выбора (они больше не нужны - две другие карты уходят в сброс)
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
}
