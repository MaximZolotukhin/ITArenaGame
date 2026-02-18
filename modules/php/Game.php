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
use Bga\Games\itarenagame\SkillsData;
use Bga\Games\itarenagame\SpecialistsData;
use Bga\Games\itarenagame\TaskTokensData;
use Bga\Games\itarenagame\ProjectTokensData;
use Bga\Games\itarenagame\Effects\EffectHandlerInterface;
use Bga\Games\itarenagame\Effects\BadgerEffectHandler;
use Bga\Games\itarenagame\Effects\CardEffectHandler;
use Bga\Games\itarenagame\Effects\TaskEffectHandler;
use Bga\Games\itarenagame\Effects\MoveTaskEffectHandler;
use Bga\Games\itarenagame\Effects\TrackEffectHandler;
use Bga\Games\itarenagame\Effects\UpdateTrackEffectHandler;

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

    // Названия раундов (внутри этапа игры)
    public function getRoundName(int $round): string
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

    /**
     * Массив фаз раунда (ЭТАП 2: Игра).
     * Логика: считываем текущую фазу по current_phase_index, выполняем фазу, ждём "все игроки закончили",
     * затем смотрим в массив — есть ли следующая фаза; если да — загружаем её, если нет — следующий раунд.
     * Каждая фаза:
     * - key: ключ фазы (event, skills, ...)
     * - name: название для отображения
     * - number: номер в раунде (1, 2, 3...)
     * - state: класс состояния фазы
     * - transition: имя перехода из NextPlayer в это состояние (toRoundEvent, toRoundSkills, ...)
     */
    public function getRoundPhases(): array
    {
        return [
            [
                'key' => 'event',
                'number' => 1,
                'name' => clienttranslate('Событие'),
                'state' => \Bga\Games\itarenagame\States\RoundEvent::class,
                'transition' => 'toRoundEvent',
            ],
            [
                'key' => 'skills',
                'number' => 2,
                'name' => clienttranslate('Навыки'),
                'state' => \Bga\Games\itarenagame\States\RoundSkills::class,
                'transition' => 'toRoundSkills',
            ],
            [
                'key' => 'hiring',
                'number' => 3,
                'name' => clienttranslate('Найм'),
                'state' => \Bga\Games\itarenagame\States\RoundHiring::class,
                'transition' => 'toRoundHiring',
            ],
        ];
    }

    /**
     * Количество фаз раунда (длина массива getRoundPhases()).
     * Номер текущей фазы (current_phase_index) увеличивается только после того,
     * как походил последний игрок за текущую фазу. Если номер текущей фазы
     * равен количеству фаз — переход на следующий раунд.
     */
    public function getNumberOfPhases(): int
    {
        return count($this->getRoundPhases());
    }

    /**
     * Определяет следующее состояние после фазы раунда (ЭТАП 2).
     * Вызывается из NextPlayer при каждой фазе; ходы игроков не являются отдельной фазой —
     * счёт ведётся через players_left_in_round.
     *
     * @param bool $eventPhaseJustFinished true, если только что завершилась фаза «Событие» (RoundEvent)
     * @param int $playersLeftInRound сколько игроков ещё должны сделать ход в этом раунде (после декремента, если пришли из PlayerTurn)
     * @param int $playersCount всего игроков
     * @return string|null класс состояния (PlayerTurn::class) или null (переход к следующему раунду, остаёмся в NextPlayer)
     */
    public function getNextStateForRoundPhase(bool $eventPhaseJustFinished, int $playersLeftInRound, int $playersCount): ?string
    {
        if ($eventPhaseJustFinished) {
            return PlayerTurn::class;
        }
        if ($playersLeftInRound > 0) {
            return PlayerTurn::class;
        }
        return null;
    }

    /**
     * Возвращает фазу по ключу
     * 
     * @param string $phaseKey Ключ фазы
     * @return array|null Данные фазы или null если не найдена
     */
    public function getPhaseByKey(string $phaseKey): ?array
    {
        $phases = $this->getRoundPhases();
        foreach ($phases as $phase) {
            if ($phase['key'] === $phaseKey) {
                return $phase;
            }
        }
        return null;
    }

    /**
     * Возвращает фазу по номеру
     * 
     * @param int $phaseNumber Номер фазы (1, 2, 3...)
     * @return array|null Данные фазы или null если не найдена
     */
    public function getPhaseByNumber(int $phaseNumber): ?array
    {
        $phases = $this->getRoundPhases();
        foreach ($phases as $phase) {
            if ($phase['number'] === $phaseNumber) {
                return $phase;
            }
        }
        return null;
    }

    /** Возвращает название фазы по ключу */
    public function getPhaseName(string $phaseKey): string
    {
        $phase = $this->getPhaseByKey($phaseKey);
        return $phase ? $phase['name'] : '';
    }

    /**
     * Возвращает номер фазы по ключу
     * 
     * @param string $phaseKey Ключ фазы
     * @return int|null Номер фазы или null если не найдена
     */
    public function getPhaseNumber(string $phaseKey): ?int
    {
        $phase = $this->getPhaseByKey($phaseKey);
        return $phase ? $phase['number'] : null;
    }

    /** Возвращает список граней кубика (20 значений) */
    public function getCubeFaces(): array
    {
        return [
            'P', 'A', 'E', 'I', 'P', 'A', 'E', 'I', 'P', 'A', 'E', 'I', 'SF', 'SF', 'PA', 'PE', 'PI', 'AE', 'AI', 'EI'
        ];
    }

    /**
     * Количество символов PAEI на грани кубика (для расчёта эффекта карты события).
     * Одна буква (P, A, E, I) = 1, комбинация (PA, PE, PI, AE, AI, EI) = 2, SF = 2.
     */
    public function getCubeFacePaeiCount(string $cubeFace): int
    {
        return strlen($cubeFace);
    }

    /**
     * Возвращает количество символов PAEI на текущей грани кубика раунда.
     * Используется в логике эффектов карт событий.
     */
    public function getRoundCubePaeiCount(): int
    {
        $faceIndex = (int)$this->getGameStateValue('round_cube_face');
        $faces = $this->getCubeFaces();
        if ($faceIndex < 0 || $faceIndex >= count($faces)) {
            return 0;
        }
        return $this->getCubeFacePaeiCount($faces[$faceIndex]);
    }

    /** Бросает кубик текущего раунда, сохраняет индекс и количество PAEI, возвращает строковое значение */
    public function rollRoundCube(): string
    {
        $faces = $this->getCubeFaces();
        $index = bga_rand(0, count($faces) - 1);
        $this->setGameStateValue('round_cube_face', $index);
        $cubeFace = $faces[$index];
        $this->setGameStateValue('round_cube_paei_count', $this->getCubeFacePaeiCount($cubeFace));
        return $cubeFace;
    }

    /**
     * Данные фазы «Событие» текущего раунда: значение кости и карты событий.
     * Используется при срабатывании эффектов карт событий в течение раунда.
     *
     * @return array{cubeFace: string, cubeFacePaeiCount: int, roundEventCards: array}
     */
    public function getCurrentRoundEventData(): array
    {
        $faceIndex = (int)$this->getGameStateValue('round_cube_face');
        $faces = $this->getCubeFaces();
        $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
        return [
            'cubeFace' => $cubeFace,
            'cubeFacePaeiCount' => $this->getRoundCubePaeiCount(),
            'roundEventCards' => $this->getRoundEventCards(),
        ];
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
            'round_cube_face' => 13, // Значение кости раунда: индекс грани (0..19), используется при срабатывании событий
            'round_cube_paei_count' => 18, // Количество символов PAEI на грани (1 или 2), используется при срабатывании событий
            'last_cube_round' => 14, // Номер раунда, для которого был брошен кубик
            'last_event_cards_round' => 15, // Номер раунда, для которого были подготовлены карты событий
            'current_phase_index' => 16, // Индекс текущей фазы в массиве фаз (0, 1, 2...)
            'players_completed_current_phase' => 17, // Количество игроков, завершивших текущую фазу
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
        error_log("🔵🔵🔵 Game::getAllDatas() - Called for current_player_id: $current_player_id");

        // Get information about players.
        // NOTE: you can retrieve some extra field you added for "player" table in `dbmodel.sql` if you need it.
        $result["players"] = $this->getCollectionFromDb(
            "SELECT `player_id` `id`, `player_score` `score`, `player_color` `color` FROM `player`"
        );
        
        // ВЫВОДИМ СТРУКТУРУ ТАБЛИЦЫ player (ВСЕ ПОЛЯ)
        error_log('========================================');
        error_log('=== СТРУКТУРА ТАБЛИЦЫ player (ВСЕ ПОЛЯ) ===');
        error_log('========================================');
        $tableStructure = $this->getCollectionFromDb("DESCRIBE `player`");
        foreach ($tableStructure as $field) {
            error_log('ПОЛЕ: ' . $field['Field'] . ' | ТИП: ' . $field['Type'] . ' | NULL: ' . $field['Null'] . ' | КЛЮЧ: ' . $field['Key'] . ' | ПО УМОЛЧАНИЮ: ' . ($field['Default'] ?? 'NULL') . ' | ДОПОЛНИТЕЛЬНО: ' . ($field['Extra'] ?? ''));
        }
        error_log('========================================');
        error_log('');
        
        // ВЫВОДИМ ВСЕ ПОЛЯ ИЗ БД ДЛЯ КАЖДОГО ИГРОКА
        error_log('========================================');
        error_log('=== ВСЕ ПОЛЯ ИЗ БД ДЛЯ ИГРОКОВ ===');
        error_log('========================================');
        foreach ($result["players"] as $player) {
            $playerId = (int)($player['id'] ?? 0);
            error_log('');
            error_log('========================================');
            error_log('ИГРОК ' . $playerId . ' - ВСЕ ПОЛЯ ИЗ ТАБЛИЦЫ player:');
            error_log('========================================');
            
            // Получаем ВСЕ поля из таблицы player
            $allPlayerFields = $this->getObjectFromDb("
                SELECT * FROM `player` WHERE `player_id` = $playerId
            ");
            
            if ($allPlayerFields) {
                // Выводим каждое поле на отдельной строке
                foreach ($allPlayerFields as $fieldName => $fieldValue) {
                    $valueStr = is_null($fieldValue) ? 'NULL' : (is_string($fieldValue) ? $fieldValue : var_export($fieldValue, true));
                    error_log($fieldName . ' = ' . $valueStr);
                }
            } else {
                error_log('ОШИБКА: Игрок ' . $playerId . ' не найден в таблице player!');
            }
            error_log('========================================');
            error_log('');
        }
        error_log('========================================');
        // НЕ используем fillResult для energy и badgers, т.к. они теперь хранятся в player_game_data
        // $this->playerEnergy->fillResult($result);
        // $this->playerBadgers->fillResult($result);
        
        // ВАЖНО: Логируем данные баджерсов для каждого игрока (теперь из player_game_data)
        // Это покажет, сколько баджерсов ДЕЙСТВИТЕЛЬНО в базе данных у каждого игрока
        error_log('🔵🔵🔵 Game::getAllDatas() - Badgers data after fillResult for current_player_id: ' . $current_player_id);
        foreach ($result["players"] as $player) {
            $playerId = (int)($player['id'] ?? 0);
            $badgersFromFillResult = $player['badgers'] ?? null;
            
            // ВАЖНО: Проверяем, что данные правильно прочитаны из базы данных
            $dbBadgers = $this->playerBadgers->get($playerId);
            
            error_log("🔵 Player $playerId - fillResult: " . ($badgersFromFillResult !== null ? $badgersFromFillResult : 'NULL') . ", DB: $dbBadgers");
            
            if ($badgersFromFillResult !== $dbBadgers) {
                error_log("🔴🔴🔴 ERROR: Player $playerId badgers mismatch! fillResult: $badgersFromFillResult, DB: $dbBadgers");
            }
            
            // ВАЖНО: Если у игрока больше 5 баджерсов на старте, это проблема в бэкенде
            // НО: Проверяем только если это НЕ текущий игрок, для которого вызывается getAllDatas
            // (текущий игрок может иметь больше 5 баджерсов после применения эффекта)
            if ($playerId !== $current_player_id && $dbBadgers > 5) {
                error_log("🔴🔴🔴🔴🔴 CRITICAL ERROR: Player $playerId (NOT current player $current_player_id) has $dbBadgers badgers (expected 5 at start)! This indicates a backend issue - effect was applied to wrong player!");
            }
        }

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
            
            // ВАЖНО: При старте хода игрока считываем ВСЕ данные из player_game_data
            // Это основной источник данных для отображения на планшете игрока
            $gameData = $this->getPlayerGameData($playerId);
            
            if ($gameData) {
                // Базовые параметры из таблицы player_game_data (приоритетный источник)
                $player['energy'] = $gameData['incomeTrack']; // Трек дохода
                $player['badgers'] = $gameData['badgers']; // Баджерсы
                
                // Треки бэк-офиса
                $player['backOfficeCol1'] = $gameData['backOfficeCol1'];
                $player['backOfficeCol2'] = $gameData['backOfficeCol2'];
                $player['backOfficeCol3'] = $gameData['backOfficeCol3'];
                
                // Треки технического развития
                $player['techDevCol1'] = $gameData['techDevCol1'];
                $player['techDevCol2'] = $gameData['techDevCol2'];
                $player['techDevCol3'] = $gameData['techDevCol3'];
                $player['techDevCol4'] = $gameData['techDevCol4'];
                
                // Жетон навыка
                $player['skillToken'] = $gameData['skillToken'];
                
                // Прогресс трека задач
                $player['sprintColumnTasksProgress'] = $gameData['sprintColumnTasksProgress'];
                
                // Трек спринта (из player_game_data)
                $player['sprintTrack'] = $gameData['sprintTrack'];
                
                // Жетоны задач (из player_game_data)
                if (!empty($gameData['taskTokens']) && is_array($gameData['taskTokens'])) {
                    $player['taskTokens'] = $gameData['taskTokens'];
                } else {
                    // Если нет в player_game_data, читаем из БД
                    $player['taskTokens'] = $taskTokensByPlayer[$playerId] ?? [];
                }
                
                // Жетоны проектов (из player_game_data)
                if (!empty($gameData['projectTokens']) && is_array($gameData['projectTokens'])) {
                    $player['projectTokens'] = $gameData['projectTokens'];
                } else {
                    // Если нет в player_game_data, читаем из БД
                    $player['projectTokens'] = $this->getProjectTokensByPlayer($playerId);
                }
                
                // Карты специалистов на руке (из player_game_data)
                if (!empty($gameData['specialistHand']) && is_array($gameData['specialistHand'])) {
                    $player['specialistHand'] = $gameData['specialistHand'];
                } else {
                    // Если нет в player_game_data, читаем из globals
                    $specialistHandIdsJson = $this->globals->get('specialist_hand_' . $playerId, '');
                    $player['specialistHand'] = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
                }
                
                // Карты сотрудников (из player_game_data)
                if (!empty($gameData['playerSpecialists']) && is_array($gameData['playerSpecialists'])) {
                    $player['playerSpecialists'] = $gameData['playerSpecialists'];
                } else {
                    // Если нет в player_game_data, читаем из globals
                    $playerSpecialistsIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
                    $player['playerSpecialists'] = !empty($playerSpecialistsIdsJson) ? json_decode($playerSpecialistsIdsJson, true) : [];
                }

                // Нанятые специалисты по отделам (из player_game_data) + полные данные карт для отрисовки
                $hiredByDept = $gameData['playerHiredSpecialists'] ?? [];
                $player['playerHiredSpecialists'] = $hiredByDept;
                $player['playerHiredSpecialistsDetails'] = $this->expandHiredSpecialistsToDetails($hiredByDept);

                // Бонусы IT проектов
                $player['itProjectBonuses'] = $gameData['itProjectBonuses'];
                
                // Цели игры
                $player['gameGoals'] = $gameData['gameGoals'];
            } else {
                // Если данных нет в player_game_data, инициализируем и читаем из других источников
                $this->initPlayerGameData($playerId);
                
                // Читаем из PlayerCounter и других источников
                $player['energy'] = $this->playerEnergy->get($playerId);
                $player['badgers'] = $this->playerBadgers->get($playerId);
                $player['taskTokens'] = $taskTokensByPlayer[$playerId] ?? [];
                $player['projectTokens'] = $this->getProjectTokensByPlayer($playerId);
                
                // Формируем трек спринта из жетонов задач
                $sprintTrack = [
                    'backlog' => [],
                    'in-progress' => [],
                    'testing' => [],
                    'completed' => [],
                ];
                foreach ($player['taskTokens'] as $token) {
                    $location = $token['location'] ?? 'backlog';
                    if (isset($sprintTrack[$location])) {
                        $sprintTrack[$location][] = $token;
                    }
                }
                $player['sprintTrack'] = [
                    'backlog' => $sprintTrack['backlog'],
                    'inProgress' => $sprintTrack['in-progress'],
                    'testing' => $sprintTrack['testing'],
                    'completed' => $sprintTrack['completed'],
                    'backlogCount' => count($sprintTrack['backlog']),
                    'inProgressCount' => count($sprintTrack['in-progress']),
                    'testingCount' => count($sprintTrack['testing']),
                    'completedCount' => count($sprintTrack['completed']),
                ];
                
                // Читаем из globals
                $specialistHandIdsJson = $this->globals->get('specialist_hand_' . $playerId, '');
                $player['specialistHand'] = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
                
                $playerSpecialistsIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
                $player['playerSpecialists'] = !empty($playerSpecialistsIdsJson) ? json_decode($playerSpecialistsIdsJson, true) : [];
                $hiredByDept = $this->getPlayerHiredSpecialists($playerId);
                $player['playerHiredSpecialists'] = $hiredByDept;
                $player['playerHiredSpecialistsDetails'] = $this->expandHiredSpecialistsToDetails($hiredByDept);

                // Значения по умолчанию для остальных полей
                $player['backOfficeCol1'] = null;
                $player['backOfficeCol2'] = null;
                $player['backOfficeCol3'] = null;
                $player['techDevCol1'] = null;
                $player['techDevCol2'] = null;
                $player['techDevCol3'] = null;
                $player['techDevCol4'] = null;
                $player['skillToken'] = null;
                $player['sprintColumnTasksProgress'] = null;
                $player['itProjectBonuses'] = [];
                $player['gameGoals'] = [];
            }
            
        }
        unset($player);

        // Добавляем жетоны проектов на планшете (доступны всем игрокам)
        $result['projectTokensOnBoard'] = $this->getProjectTokensOnBoard();
        error_log('Game::getAllDatas() - projectTokensOnBoard count: ' . count($result['projectTokensOnBoard']));
        if (count($result['projectTokensOnBoard']) > 0) {
            error_log('Game::getAllDatas() - First token: ' . json_encode($result['projectTokensOnBoard'][0]));
        } else {
            error_log('Game::getAllDatas() - WARNING: No project tokens on board!');
        }

        // Round info for client banner
        $result['round'] = (int)$this->getGameStateValue('round_number'); // Текущий раунд
        $result['totalRounds'] = (int)$this->getGameStateValue('total_rounds'); // Общее количество раундов
        $result['roundName'] = $this->getRoundName($result['round']); // Название раунда
        $faces = $this->getCubeFaces(); // Значения граней кубика
        $faceIndex = (int)$this->getGameStateValue('round_cube_face'); // Индекс текущей грани кубика (0..19)
        // ВАЖНО: Проверяем текущее состояние игры
        $currentState = $this->gamestate->state()['name'] ?? '';
        $round = (int)$this->getGameStateValue('round_number');
        error_log('🎲 Game::getAllDatas() - currentState: ' . $currentState . ', round: ' . $round);
        
        // Если мы на этапе 2 (round > 0) и кубик еще не брошен, подготавливаем данные СЕЙЧАС
        // Это гарантирует, что данные будут доступны даже если RoundEvent был пропущен
        if ($round > 0 && ($faceIndex < 0 || $faceIndex >= count($faces))) {
            error_log('🎲 Game::getAllDatas() - Round > 0 but cube not rolled! Rolling now...');
            $cubeFace = $this->rollRoundCube();
            $faceIndex = (int)$this->getGameStateValue('round_cube_face');
            error_log('🎲 Game::getAllDatas() - Cube rolled in getAllDatas: ' . $cubeFace);
        } else {
            $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
        }
        
        $result['cubeFace'] = $cubeFace; // Значение кубика на раунд
        $result['cubeFacePaeiCount'] = $this->getRoundCubePaeiCount(); // Количество символов PAEI (1 или 2) для расчёта эффекта карты события
        
        // Логирование для отладки
        error_log('🎲 Game::getAllDatas() - cubeFace: ' . var_export($result['cubeFace'], true) . ', faceIndex: ' . $faceIndex . ', paeiCount: ' . $result['cubeFacePaeiCount']);
        
        // Текущее имя фазы из глобальной переменной (переводим ключ в название)
        // Получаем данные текущей фазы из массива фаз
        $phaseKey = $this->globals->get('current_phase_name', '');
        if (!empty($phaseKey)) {
            $phase = $this->getPhaseByKey($phaseKey);
            if ($phase) {
                $result['phaseName'] = $phase['name'];
                $result['phaseNumber'] = $phase['number'];
                $result['phaseKey'] = $phase['key'];
            } else {
                $result['phaseName'] = '';
                $result['phaseNumber'] = null;
                $result['phaseKey'] = '';
            }
        } else {
            $result['phaseName'] = '';
            $result['phaseNumber'] = null;
            $result['phaseKey'] = '';
        }
        
        // Добавляем массив всех фаз для клиента
        $result['roundPhases'] = $this->getRoundPhases();
        // ВАЖНО: НЕ отправляем все карты событий в getAllDatas() - это слишком большой JSON
        // Клиент получает только текущие карты раунда через roundEventCards
        // $result['eventCards'] = EventCardsData::getAllCards();
        $result['specialists'] = SpecialistsData::getAllCards(); // Все карты специалистов для клиента
        
        // ВАЖНО: Если мы на этапе 2 и карты событий пустые, подготавливаем их СЕЙЧАС
        $roundEventCards = $this->getRoundEventCards();
        if ($round > 0 && empty($roundEventCards)) {
            error_log('🎲 Game::getAllDatas() - Round > 0 but event cards empty! Preparing now...');
            $roundEventCards = $this->prepareRoundEventCard();
            error_log('🎲 Game::getAllDatas() - Event cards prepared in getAllDatas: ' . count($roundEventCards));
        }
        
        $result['roundEventCards'] = $roundEventCards;
        $result['roundEventCard'] = $roundEventCards[0] ?? null;
        $result['badgers'] = $this->getBadgersSupply();
        $result['founders'] = $foundersByPlayer; // Данные по основателям игроков
        $result['gameMode'] = $this->getGameMode(); // Режим игры (1 - Обучающий, 2 - Основной)
        $result['isTutorialMode'] = $this->isTutorialMode(); // Является ли режим обучающим
        
        // ВСЕГДА добавляем начальные значения всех игроков в getAllDatas
        // Это гарантирует, что данные будут доступны при каждой загрузке страницы
        error_log('========================================');
        error_log('=== НАЧАЛЬНЫЕ ЗНАЧЕНИЯ ВСЕХ ИГРОКОВ ===');
        error_log('=== Game::getAllDatas() - Called for current_player_id: ' . $current_player_id . ' ===');
        error_log('========================================');
        $initialValues = [];
        $allPlayerIds = array_keys($basicInfos);
        error_log('Обрабатываем ' . count($allPlayerIds) . ' игроков для начальных значений');
        foreach ($allPlayerIds as $playerId) {
            $playerId = (int)$playerId;
            
            // Базовые параметры
            $badgers = $this->playerBadgers->get($playerId);
            $incomeTrack = $this->playerEnergy->get($playerId);
            
            // Жетоны задач
            $taskTokens = $this->getTaskTokensByPlayer($playerId);
            $taskTokensCount = count($taskTokens);
            $taskTokensByLocation = [];
            $sprintTrackData = [
                'backlog' => [],
                'in-progress' => [],
                'testing' => [],
                'completed' => [],
            ];
            foreach ($taskTokens as $token) {
                $location = $token['location'] ?? 'unknown';
                $taskTokensByLocation[$location] = ($taskTokensByLocation[$location] ?? 0) + 1;
                if (isset($sprintTrackData[$location])) {
                    $sprintTrackData[$location][] = $token;
                }
            }
            
            // Жетоны проектов
            $projectTokens = $this->getProjectTokensByPlayer($playerId);
            $projectTokensCount = count($projectTokens);
            
            // Карты специалистов на руке (для выбора)
            $specialistHandIdsJson = $this->globals->get('specialist_hand_' . $playerId, '');
            $specialistHandIds = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
            $specialistHandCount = is_array($specialistHandIds) ? count($specialistHandIds) : 0;
            
            // Карты сотрудников (подтвержденные)
            $playerSpecialistsIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
            $playerSpecialistsIds = !empty($playerSpecialistsIdsJson) ? json_decode($playerSpecialistsIdsJson, true) : [];
            $playerSpecialistsCount = is_array($playerSpecialistsIds) ? count($playerSpecialistsIds) : 0;
            
            // Получаем игровые данные из таблицы player_game_data
            $gameData = $this->getPlayerGameData($playerId);
            
            // Треки бэк-офиса
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
            
            // Бонусы IT проектов
            $itProjectBonuses = $gameData ? $gameData['itProjectBonuses'] : [];
            
            // Цели игры
            $gameGoals = $gameData ? $gameData['gameGoals'] : [];
            
            // Прогресс улучшения трека задач (sprint-column-tasks)
            $sprintColumnTasksProgress = $gameData ? $gameData['sprintColumnTasksProgress'] : null;
            
            $initialValues[$playerId] = [
                'badgers' => $badgers,
                'incomeTrack' => $incomeTrack,
                'taskTokens' => ['total' => $taskTokensCount, 'byLocation' => $taskTokensByLocation],
                'sprintTrack' => [
                    'backlog' => $sprintTrackData['backlog'],
                    'inProgress' => $sprintTrackData['in-progress'],
                    'testing' => $sprintTrackData['testing'],
                    'completed' => $sprintTrackData['completed'],
                    'backlogCount' => count($sprintTrackData['backlog']),
                    'inProgressCount' => count($sprintTrackData['in-progress']),
                    'testingCount' => count($sprintTrackData['testing']),
                    'completedCount' => count($sprintTrackData['completed']),
                ],
                'sprintColumnTasksProgress' => $sprintColumnTasksProgress,
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
                'itProjectBonuses' => $itProjectBonuses,
                'gameGoals' => $gameGoals,
            ];
            
            // ВЫВОДИМ ВСЕ НАЧАЛЬНЫЕ ЗНАЧЕНИЯ ДЛЯ КАЖДОГО ИГРОКА В error_log
            error_log('--- Игрок ' . $playerId . ' ---');
            error_log('  badgers=' . var_export($badgers, true));
            error_log('  incomeTrack=' . var_export($incomeTrack, true));
            error_log('  taskTokens: всего=' . $taskTokensCount . ', по локациям=' . json_encode($taskTokensByLocation, JSON_UNESCAPED_UNICODE));
            error_log('  sprintTrack: backlog=' . count($sprintTrackData['backlog']) . ', inProgress=' . count($sprintTrackData['in-progress']) . ', testing=' . count($sprintTrackData['testing']) . ', completed=' . count($sprintTrackData['completed']));
            error_log('  sprintColumnTasksProgress=' . var_export($sprintColumnTasksProgress, true));
            error_log('  projectTokens: всего=' . $projectTokensCount);
            error_log('  specialistHand (на руке): всего=' . $specialistHandCount . ', IDs=' . json_encode($specialistHandIds, JSON_UNESCAPED_UNICODE));
            error_log('  playerSpecialists (подтвержденные): всего=' . $playerSpecialistsCount . ', IDs=' . json_encode($playerSpecialistsIds, JSON_UNESCAPED_UNICODE));
            error_log('  backOfficeCol1=' . var_export($backOfficeCol1, true) . ', backOfficeCol2=' . var_export($backOfficeCol2, true) . ', backOfficeCol3=' . var_export($backOfficeCol3, true));
            error_log('  techDevCol1=' . var_export($techDevCol1, true) . ', techDevCol2=' . var_export($techDevCol2, true) . ', techDevCol3=' . var_export($techDevCol3, true) . ', techDevCol4=' . var_export($techDevCol4, true));
            error_log('  skillToken=' . var_export($skillToken, true));
            error_log('  itProjectBonuses=' . json_encode($itProjectBonuses, JSON_UNESCAPED_UNICODE));
            error_log('  gameGoals=' . json_encode($gameGoals, JSON_UNESCAPED_UNICODE));
            
            // Дополнительная проверка: выводим все значения из массива $initialValues для этого игрока
            error_log('  === Полный массив initialValues для игрока ' . $playerId . ' ===');
            error_log('  ' . json_encode($initialValues[$playerId], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        $result['initialPlayerValues'] = $initialValues;
        error_log('========================================');
        error_log('=== Добавлено initialPlayerValues для ' . count($initialValues) . ' игроков ===');
        error_log('========================================');
        
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

        // ========================================
        // Данные о картах сотрудников (JSON) - храним только ID!
        // ========================================
        
        // ID карт сотрудников на руке (для выбора) - только для текущего игрока
        $specialistHandIdsJson = $this->globals->get('specialist_hand_' . $current_player_id, '');
        $specialistHandIds = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
        if (!empty($specialistHandIds)) {
            // Получаем полные данные карт по ID
            $specialistHand = [];
            foreach ($specialistHandIds as $cardId) {
                $card = SpecialistsData::getCard((int)$cardId);
                if ($card) {
                    $specialistHand[] = $card;
                }
            }
            $result['specialistHand'] = $specialistHand;
            $selectedJson = $this->globals->get('specialist_selected_' . $current_player_id, '');
            $result['selectedSpecialists'] = !empty($selectedJson) ? json_decode($selectedJson, true) : [];
            error_log('getAllDatas - Player ' . $current_player_id . ' has ' . count($specialistHand) . ' specialist cards in hand');
        }
        
        // Карты сотрудников на руке текущего игрока: БД — источник истины (после F5 и между ходами)
        $gameDataCurrent = $this->getPlayerGameData((int)$current_player_id);
        $playerSpecialistIds = !empty($gameDataCurrent['playerSpecialists']) && is_array($gameDataCurrent['playerSpecialists'])
            ? $gameDataCurrent['playerSpecialists']
            : [];
        if (empty($playerSpecialistIds)) {
            $playerSpecialistIdsJson = $this->globals->get('player_specialists_' . $current_player_id, '');
            $playerSpecialistIds = !empty($playerSpecialistIdsJson) ? json_decode($playerSpecialistIdsJson, true) : [];
        }
        if (!empty($playerSpecialistIds)) {
            $playerSpecialists = [];
            foreach ($playerSpecialistIds as $cardId) {
                $card = SpecialistsData::getCard((int)$cardId);
                if ($card) {
                    $playerSpecialists[] = $card;
                }
            }
            $result['playerSpecialists'] = $playerSpecialists;
            error_log('getAllDatas - Player ' . $current_player_id . ' has ' . count($playerSpecialists) . ' confirmed specialists');
        }
        
        // Добавляем сотрудников всех игроков в players (БД — источник истины)
        foreach ($result["players"] as &$player) {
            $playerId = (int)($player['id'] ?? 0);
            $playerData = $this->getPlayerGameData($playerId);
            $specialistIds = !empty($playerData['playerSpecialists']) && is_array($playerData['playerSpecialists'])
                ? $playerData['playerSpecialists']
                : [];
            if (empty($specialistIds)) {
                $specialistIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
                $specialistIds = !empty($specialistIdsJson) ? json_decode($specialistIdsJson, true) : [];
            }
            if (!empty($specialistIds)) {
                // Получаем полные данные карт по ID
                $specialists = [];
                foreach ($specialistIds as $cardId) {
                    $card = SpecialistsData::getCard((int)$cardId);
                    if ($card) {
                        $specialists[] = $card;
                    }
                }
                $player['specialists'] = $specialists;
            }
        }
        unset($player);

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
        $this->playerEnergy->initDb($playerIds, initialValue: 1); // Начальное значение трека доходов = 1
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
        $this->setGameStateInitialValue('round_number', 0); // Текущий раунд (0 = ЭТАП 1, >0 = ЭТАП 2)
        $this->setGameStateInitialValue('players_left_in_round', count($players)); // Количество игроков в раунде
        $this->setGameStateInitialValue('round_cube_face', -1); // Пока не брошен
        $this->setGameStateInitialValue('round_cube_paei_count', 0); // Количество символов PAEI на грани кубика
        $this->setGameStateInitialValue('last_cube_round', 0); // Номер раунда, для которого был брошен кубик
        $this->setGameStateInitialValue('last_event_cards_round', 0); // Номер раунда, для которого были подготовлены карты событий
        $this->setGameStateInitialValue('current_phase_index', 0); // Индекс текущей фазы в массиве фаз (0, 1, 2...)
        $this->setGameStateInitialValue('players_completed_current_phase', 0); // Количество игроков, завершивших текущую фазу
        // Устанавливаем начальное название фазы, так как сразу переходим в RoundEvent
        // Используем ключ для перевода на клиенте
        $this->globals->set('current_phase_name', 'event');

        foreach (BadgersData::getInitialSupply() as $value => $quantity) {
            $this->setGameStateInitialValue('badgers_supply_' . $value, $quantity);
        }

        $this->eventDeck->autoreshuffle = false;
        $this->eventDeck->createCards(EventCardsData::getCardsForDeck(), 'deck');
        $this->eventDeck->shuffle('deck');

        // Инициализируем колоды специалистов
        $this->initSpecialistDecks();

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
            $this->initPlayerGameData($playerId);
            if (!$this->withdrawBadgersFromBank($amountPerPlayer)) {
                error_log('distributeInitialBadgers - ERROR: Failed to withdraw ' . $amountPerPlayer . ' badgers from bank for player ' . $playerId);
                break;
            }
            $this->addPlayerBadgers($playerId, $amountPerPlayer);
        }
        
        error_log('distributeInitialBadgers - Distribution completed');
    }

    public function withdrawBadgersFromBank(int $amount): bool // Снимаем баджерсы с банка
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

    /**
     * Возвращает баджерсы в банк (при списании у игрока)
     * @param int $amount Количество баджерсов для возврата
     */
    public function depositBadgersToBank(int $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        // Разбиваем сумму на монеты, начиная с самых крупных
        $denominations = array_keys(BadgersData::getDenominations());
        rsort($denominations);

        $remaining = $amount;
        foreach ($denominations as $value) {
            if ($remaining <= 0) {
                break;
            }
            $count = intdiv($remaining, $value);
            if ($count > 0) {
                $current = (int)$this->getGameStateValue('badgers_supply_' . $value);
                $this->setGameStateValue('badgers_supply_' . $value, $current + $count);
                $remaining -= $count * $value;
            }
        }

        error_log("depositBadgersToBank - Deposited $amount badgers to bank");
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
            // ВАЖНО: Сохраняем карту в founder_options_, чтобы она показывалась на руке как опция (как в основном режиме)
            foreach ($playerIds as $playerId) {
                $playerId = (int)$playerId;
                $cardId = (int)array_shift($availableIds);
                $founder = $founders[$cardId] ?? null;
                $founderName = $founder['name'] ?? 'unknown';
                error_log('assignInitialFounders - Tutorial: Assigning founder ID ' . $cardId . ' (' . $founderName . ') to player ' . $playerId);
                
                // Сохраняем карту в founder_options_, чтобы она показывалась на руке (как в основном режиме)
                $key = 'founder_options_' . $playerId;
                $jsonValue = json_encode([$cardId]);
                $this->globals->set($key, $jsonValue);
                error_log('assignInitialFounders - Tutorial: Saved founder option for player ' . $playerId . ': ' . $jsonValue);
                
                // Получаем данные карты, чтобы проверить её department
                $founderCard = FoundersData::getCard($cardId);
                $founderDepartment = $founderCard['department'] ?? 'universal';
                
                // В Tutorial режиме карта НЕ считается "выбранной" до клика пользователя
                // Поэтому НЕ устанавливаем founder_player_ здесь - это будет сделано при клике
                // Но устанавливаем department='universal' для универсальных карт, чтобы они показывались на руке
                if ($founderDepartment !== 'universal') {
                    error_log('assignInitialFounders - Tutorial: Founder ' . $cardId . ' has department ' . $founderDepartment . ', will be placed automatically on click');
                    // Не размещаем автоматически - пользователь должен кликнуть на карту
                } else {
                    error_log('assignInitialFounders - Tutorial: Founder ' . $cardId . ' is universal, will be shown on hand for placement');
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
            error_log('setFounderForPlayer - Player ' . $playerId . ', card ' . $cardId . ', department set to: ' . $department);
        }
        // Очищаем кэш, чтобы getFoundersByPlayer() перечитал данные из globals
        // Это важно, т.к. expandFounders() читает department из globals
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

        // Удаляем опции выбора (они больше не нужны - в основном режиме две другие карты ушли в отбой, в Tutorial режиме карта выбрана)
        $this->globals->set('founder_options_' . $playerId, null);
    }

    /**
     * Применяет эффект карты основателя на указанном этапе игры
     * @param int $playerId ID игрока
     * @param int $cardId ID карты основателя
     * @param string $currentStage Текущий этап игры (например, 'GameSetup')
     * @return array Массив с информацией о применённых эффектах
     */
    public function applyFounderEffect(int $playerId, int $cardId, string $currentStage): array
    {
        $founderCard = FoundersData::getCard($cardId);
        if ($founderCard === null) {
            error_log("applyFounderEffect - Card not found: $cardId");
            return [];
        }
        
        $activationStage = $founderCard['activationStage'] ?? null;
        $effect = $founderCard['effect'] ?? null;
        
        error_log("applyFounderEffect - Player: $playerId, Card: $cardId, CurrentStage: $currentStage, ActivationStage: $activationStage");
        error_log("applyFounderEffect - Card name: " . ($founderCard['name'] ?? 'unknown'));
        error_log("applyFounderEffect - Effect from FoundersData: " . json_encode($effect));
        
        // Специальная проверка для updateTrack
        if (isset($effect['updateTrack'])) {
            error_log("🔧🔧🔧 applyFounderEffect - updateTrack found! Count: " . count($effect['updateTrack']));
            error_log("🔧 applyFounderEffect - updateTrack full: " . json_encode($effect['updateTrack']));
            error_log("🔧 applyFounderEffect - updateTrack is_array: " . (is_array($effect['updateTrack']) ? 'YES' : 'NO'));
            if (is_array($effect['updateTrack'])) {
                foreach ($effect['updateTrack'] as $idx => $track) {
                    error_log("🔧 applyFounderEffect - Track #$idx: " . json_encode($track));
                }
            }
        }
        
        // Если этап активации не совпадает с текущим, не применяем эффект
        if ($activationStage === null || $activationStage !== $currentStage) {
            error_log("applyFounderEffect - Stage mismatch, skipping effect");
            return [];
        }
        
        // Если эффект пустой, пропускаем
        if (empty($effect) || !is_array($effect)) {
            error_log("applyFounderEffect - No effect or invalid format");
            return [];
        }
        
        $appliedEffects = [];
        
        // Обрабатываем каждый тип эффекта
        error_log("🔍🔍🔍 applyFounderEffect - Effect array: " . json_encode($effect));
        error_log("🔍🔍🔍 applyFounderEffect - Effect array keys: " . implode(', ', array_keys($effect)));
        error_log("🔍🔍🔍 applyFounderEffect - Has move_task: " . (isset($effect['move_task']) ? 'YES' : 'NO'));
        if (isset($effect['move_task'])) {
            error_log("🔍🔍🔍 applyFounderEffect - move_task value: " . json_encode($effect['move_task']));
        }
        
        foreach ($effect as $effectType => $effectValue) {
            // Для массивов (например, move_task) преобразуем в JSON строку
            // НО: для updateTrack оставляем массив как есть
            if (is_array($effectValue) && $effectType !== 'updateTrack') {
                $effectValue = json_encode($effectValue);
                error_log("🔍 applyFounderEffect - Converted array to JSON for $effectType: $effectValue");
            }
            error_log("🔍 applyFounderEffect - Processing effect type: $effectType, value: " . (is_array($effectValue) ? json_encode($effectValue) : $effectValue));
            $result = $this->processFounderEffectType($playerId, $effectType, $effectValue, $founderCard);
            if ($result !== null) {
                $appliedEffects[] = $result;
                error_log("✅ applyFounderEffect - Effect $effectType applied successfully");
            } else {
                error_log("❌ applyFounderEffect - Effect $effectType returned null");
            }
        }
        
        error_log("applyFounderEffect - Applied effects: " . json_encode($appliedEffects));
        
        return $appliedEffects;
    }
    
    /**
     * Обрабатывает конкретный тип эффекта карты основателя
     * @param int $playerId ID игрока
     * @param string $effectType Тип эффекта (badger, card, task, track и т.д.)
     * @param mixed $effectValue Значение эффекта (например, '+ 4')
     * @param array $founderCard Данные карты основателя
     * @return array|null Информация о применённом эффекте или null
     */
    
    /**
     * Получает обработчик эффекта по типу
     * @param string $effectType Тип эффекта (badger, card, task и т.д.)
     * @return EffectHandlerInterface|null Обработчик эффекта или null, если тип неизвестен
     */
    private function getEffectHandler(string $effectType): ?EffectHandlerInterface
    {
        // Сначала проверяем специальные обработчики (включая updateTrack)
        $specialHandler = match ($effectType) {
            'badger' => new BadgerEffectHandler($this),
            'card' => new CardEffectHandler($this),
            'task' => new TaskEffectHandler($this),
            'move_task' => new MoveTaskEffectHandler($this),
            'updateTrack' => new UpdateTrackEffectHandler($this),
            default => null,
        };
        
        if ($specialHandler !== null) {
            return $specialHandler;
        }
        
        // Универсальный обработчик для всех треков, заканчивающихся на Track (incomeTrack, sprintTrack, taskTrack и т.д.)
        // НО: updateTrack обрабатывается выше, поэтому сюда не попадет
        if (str_ends_with($effectType, 'Track')) {
            return new TrackEffectHandler($this);
        }
        
        return null;
    }

    /**
     * Обрабатывает эффект карты основателя по типу
     * @param int $playerId ID игрока
     * @param string $effectType Тип эффекта (badger, card, task и т.д.)
     * @param mixed $effectValue Значение эффекта (например, '+ 4')
     * @param array $founderCard Данные карты основателя
     * @return array|null Информация о применённом эффекте или null
     */
    private function processFounderEffectType(int $playerId, string $effectType, $effectValue, array $founderCard): ?array
    {
        $effectValueStr = is_array($effectValue) ? json_encode($effectValue) : (string)$effectValue;
        error_log("processFounderEffectType - Player: $playerId, Type: $effectType, Value: $effectValueStr");
        
        // Специальная проверка для updateTrack
        if ($effectType === 'updateTrack' && is_array($effectValue)) {
            error_log("🔧🔧🔧 processFounderEffectType - updateTrack BEFORE handler: Count: " . count($effectValue));
            error_log("🔧 processFounderEffectType - updateTrack BEFORE handler: Full: " . json_encode($effectValue));
            foreach ($effectValue as $idx => $track) {
                error_log("🔧 processFounderEffectType - updateTrack BEFORE handler Track #$idx: " . json_encode($track));
            }
        }
        
        $handler = $this->getEffectHandler($effectType);
        
        if ($handler === null) {
            error_log("processFounderEffectType - Unknown effect type: $effectType, skipping");
            return null;
        }
        
        error_log("processFounderEffectType - Applying $effectType effect: $effectValueStr");
        
        // Для треков передаем ключ эффекта через cardData
        if (str_ends_with($effectType, 'Track')) {
            $founderCard['_effectKey'] = $effectType;
        }
        
        $result = $handler->apply($playerId, $effectValue, $founderCard);
        
        // Специальная проверка для updateTrack после обработки
        if ($effectType === 'updateTrack' && is_array($result) && isset($result['tracks'])) {
            error_log("🔧🔧🔧 processFounderEffectType - updateTrack AFTER handler: Tracks count: " . count($result['tracks']));
            error_log("🔧 processFounderEffectType - updateTrack AFTER handler: Tracks: " . json_encode($result['tracks']));
        }
        
        return $result;
    }

    /**
     * Применяет эффекты выбранного навыка к игроку (фаза «Навыки»).
     * Сохраняет выбранный навык в player_game_data.skill_token и применяет эффекты через существующие обработчики.
     *
     * @param int $playerId ID игрока
     * @param string $skillKey Ключ навыка (eloquence, discipline, intellect, frugality)
     */
    /**
     * @return array<int, array> Результаты применённых эффектов (для уведомлений)
     */
    public function applySkillEffects(int $playerId, string $skillKey): array
    {
        if (!SkillsData::isValidKey($skillKey)) {
            throw new \InvalidArgumentException("Invalid skill key: $skillKey");
        }
        $skill = SkillsData::getSkill($skillKey);
        if ($skill === null) {
            return [];
        }
        $this->setSkillToken($playerId, $skillKey);
        if (empty($skill['effects'])) {
            return [];
        }
        $results = [];
        $skillData = $skill;
        foreach ($skill['effects'] as $effectType => $effectValue) {
            $handler = $this->getEffectHandler($effectType);
            if ($handler === null) {
                continue;
            }
            $skillData['_effectKey'] = $effectType;
            $valueToPass = is_array($effectValue) ? $effectValue : (string)$effectValue;
            $results[] = $handler->apply($playerId, $valueToPass, $skillData);
        }
        return $results;
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

    /**
     * Проверяет, все ли игроки разместили свои универсальные карты основателей
     * Используется в Tutorial режиме
     */
    public function allFoundersPlaced(): bool
    {
        $playerIds = array_keys($this->loadPlayersBasicInfos());
        
        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            
            // Получаем данные основателя для детального логирования
            $founders = $this->getFoundersByPlayer();
            $founder = $founders[$playerId] ?? null;
            $department = $founder['department'] ?? 'none';
            
            error_log('allFoundersPlaced - Checking player ' . $playerId . ', founder: ' . ($founder ? 'yes' : 'no') . ', department: ' . $department);
            
            // Проверяем, есть ли у игрока неразмещённая универсальная карта
            if ($this->hasUnplacedUniversalFounder($playerId)) {
                error_log('allFoundersPlaced - Player ' . $playerId . ' has unplaced universal founder (department=' . $department . ')');
                return false;
            }
        }
        
        error_log('allFoundersPlaced - All founders placed');
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
            error_log('hasUnplacedUniversalFounder - Player ' . $playerId . ' has no founder');
            return false;
        }

        $founder = $founders[$playerId];
        $department = strtolower(trim($founder['department'] ?? ''));
        
        // Также проверяем напрямую из globals для надежности
        $globalsDepartment = $this->globals->get('founder_department_' . $playerId, null);
        if ($globalsDepartment !== null) {
            $globalsDepartment = strtolower(trim($globalsDepartment));
        }
        
        error_log('hasUnplacedUniversalFounder - Player ' . $playerId . ', department from founder: ' . $department . ', from globals: ' . ($globalsDepartment ?? 'null'));
        
        // Если отдел 'universal', значит карта еще на руках
        $isUnplaced = $department === 'universal';
        error_log('hasUnplacedUniversalFounder - Player ' . $playerId . ' has unplaced founder: ' . ($isUnplaced ? 'yes' : 'no'));
        return $isUnplaced;
    }

    public function getFoundersByPlayer(): array
    {
        // Всегда перечитываем из globals, чтобы получить актуальный department
        // (кэш founderAssignments может быть устаревшим после placeFounder)
        $result = [];
        foreach ($this->loadPlayersBasicInfos() as $playerId => $info) {
            $value = $this->globals->get('founder_player_' . (int)$playerId, null);
            if ($value !== null) {
                $result[(int)$playerId] = (int)$value;
            }
        }

        // Обновляем кэш
            $this->founderAssignments = $result;

        return $this->expandFounders($result);
    }

    /**
     * Получает данные основателя для конкретного игрока
     * @param int $playerId ID игрока
     * @return array|null Данные основателя или null если не найден
     */
    public function getFounderForPlayer(int $playerId): ?array
    {
        $founders = $this->getFoundersByPlayer();
        return $founders[$playerId] ?? null;
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
        
        // В Tutorial режиме раздаём 3 случайные карты, в основном - 1 стартовую
        $isTutorial = $this->isTutorialMode();
        $cardsPerPlayer = $isTutorial ? 3 : 1;
        
        error_log('distributeStartingSpecialistCards - Tutorial mode: ' . ($isTutorial ? 'YES' : 'NO') . ', cards per player: ' . $cardsPerPlayer);
        
        if ($isTutorial) {
            // В Tutorial режиме раздаём случайные карты (не только стартовые)
            $availableCards = $allSpecialists;
        } else {
            // В основном режиме только стартовые карты (starterOrFinisher = 'S')
            $availableCards = [];
        foreach ($allSpecialists as $cardId => $card) {
            if (isset($card['starterOrFinisher']) && $card['starterOrFinisher'] === 'S') {
                    $availableCards[$cardId] = $card;
                }
            }
        }

        if (empty($availableCards)) {
            error_log('distributeStartingSpecialistCards - No specialist cards found');
            return;
        }

        // Подготавливаем пул карт для раздачи
        $availableIds = array_keys($availableCards);
        shuffle($availableIds);

        foreach ($playerIds as $playerId) {
            $playerId = (int)$playerId;
            $playerSpecialists = [];
            
            for ($i = 0; $i < $cardsPerPlayer; $i++) {
            if (empty($availableIds)) {
                // Если карт не хватает, перемешиваем заново
                    $availableIds = array_keys($availableCards);
                shuffle($availableIds);
            }
            
            $cardId = (int)array_shift($availableIds);
                $playerSpecialists[] = $cardId;
                
                error_log('distributeStartingSpecialistCards - Assigned specialist card ' . $cardId . ' to player ' . $playerId);
                }
            
            // Сохраняем карты специалиста для игрока в globals
                $this->globals->set('specialist_cards_' . $playerId, json_encode($playerSpecialists));
                
            // В Tutorial режиме сразу подтверждаем карты (нет этапа выбора)
            if ($isTutorial) {
                $this->globals->set('player_specialists_' . $playerId, json_encode($playerSpecialists));
                error_log('distributeStartingSpecialistCards - Tutorial mode: cards immediately confirmed for player ' . $playerId);
            }
            
            error_log('distributeStartingSpecialistCards - Player ' . $playerId . ' has ' . count($playerSpecialists) . ' specialist cards: ' . json_encode($playerSpecialists));
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
     * Добавляет задачи игроку
     * @param int $playerId ID игрока
     * @param array $tasks Массив задач в формате [['color' => 'cyan', 'quantity' => 2], ...]
     * @param string $location Локация задач (по умолчанию 'backlog')
     * @param int|null $rowIndex Индекс строки (для колонки "Задачи")
     * @return array Массив добавленных токенов
     */
    public function addTaskTokens(int $playerId, array $tasks, string $location = 'backlog', ?int $rowIndex = null): array
    {
        $validColors = ['cyan', 'pink', 'orange', 'purple'];
        $validLocations = ['backlog', 'in-progress', 'testing', 'completed'];
        
        if (!in_array($location, $validLocations, true)) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Invalid task location'));
        }
        
        $addedTokens = [];
        
        foreach ($tasks as $task) {
            $color = strtolower(trim($task['color'] ?? ''));
            $quantity = (int)($task['quantity'] ?? 0);
            
            if (!in_array($color, $validColors, true)) {
                error_log("addTaskTokens - Invalid color: $color");
                continue;
            }
            
            if ($quantity <= 0) {
                continue;
            }
            
            // Добавляем указанное количество задач
            for ($i = 0; $i < $quantity; $i++) {
                $this->DbQuery("
                    INSERT INTO `player_task_token` (`player_id`, `color`, `location`, `row_index`)
                    VALUES ($playerId, '$color', '$location', " . ($rowIndex !== null ? $rowIndex : 'NULL') . ")
                ");
                
                $tokenId = (int)$this->DbGetLastId();
                $addedTokens[] = [
                    'token_id' => $tokenId,
                    'player_id' => $playerId,
                    'color' => $color,
                    'location' => $location,
                    'row_index' => $rowIndex,
                ];
            }
        }
        
        error_log("addTaskTokens - Player $playerId: Added " . count($addedTokens) . " task tokens");
        
        return $addedTokens;
    }
    
    /**
     * Удаляет задачи у игрока
     * @param int $playerId ID игрока
     * @param array $tasks Массив задач в формате [['color' => 'cyan', 'quantity' => 2], ...]
     * @param string|null $location Если указана, удаляет только из этой локации
     * @return int Количество удаленных токенов
     */
    public function removeTaskTokens(int $playerId, array $tasks, ?string $location = null): int
    {
        $validColors = ['cyan', 'pink', 'orange', 'purple'];
        $validLocations = ['backlog', 'in-progress', 'testing', 'completed'];
        
        $totalRemoved = 0;
        
        foreach ($tasks as $task) {
            $color = strtolower(trim($task['color'] ?? ''));
            $quantity = (int)($task['quantity'] ?? 0);
            
            if (!in_array($color, $validColors, true)) {
                error_log("removeTaskTokens - Invalid color: $color");
                continue;
            }
            
            if ($quantity <= 0) {
                continue;
            }
            
            // Формируем условие WHERE
            $whereConditions = ["`player_id` = $playerId", "`color` = '$color'"];
            if ($location !== null && in_array($location, $validLocations, true)) {
                $whereConditions[] = "`location` = '$location'";
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Получаем токены для удаления (ограничиваем количеством)
            $tokensToDelete = $this->getCollectionFromDb("
                SELECT `token_id`
                FROM `player_task_token`
                WHERE $whereClause
                ORDER BY `token_id` ASC
                LIMIT $quantity
            ");
            
            if (empty($tokensToDelete)) {
                continue;
            }
            
            $tokenIds = array_map(static function ($token) {
                return (int)$token['token_id'];
            }, $tokensToDelete);
            
            $tokenIdsStr = implode(',', $tokenIds);
            $this->DbQuery("DELETE FROM `player_task_token` WHERE `token_id` IN ($tokenIdsStr)");
            
            $removed = count($tokenIds);
            $totalRemoved += $removed;
            
            error_log("removeTaskTokens - Player $playerId: Removed $removed $color tokens" . ($location ? " from $location" : ""));
        }
        
        error_log("removeTaskTokens - Player $playerId: Total removed $totalRemoved task tokens");
        
        return $totalRemoved;
    }
    
    /**
     * Обновляет локацию задачи
     * @param int $tokenId ID токена задачи
     * @param string $location Новая локация
     * @param int|null $rowIndex Новый индекс строки
     * @return bool Успешность операции
     */
    public function updateTaskTokenLocation(int $tokenId, string $location, ?int $rowIndex = null): bool
    {
        $validLocations = ['backlog', 'in-progress', 'testing', 'completed'];
        
        if (!in_array($location, $validLocations, true)) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Invalid task location'));
        }
        
        $rowIndexValue = $rowIndex !== null ? $rowIndex : 'NULL';
        
        $this->DbQuery("
            UPDATE `player_task_token`
            SET `location` = '$location', `row_index` = $rowIndexValue
            WHERE `token_id` = $tokenId
        ");
        
        return true;
    }
    
    /**
     * Получает количество задач определенного цвета у игрока
     * @param int $playerId ID игрока
     * @param string|null $color Цвет задачи (если null, возвращает общее количество)
     * @param string|null $location Локация (если указана, считает только из этой локации)
     * @return int Количество задач
     */
    /**
     * Возвращает максимальное количество блоков, которые игрок может переместить по треку задач
     * в текущем состоянии (backlog → 3, in-progress → 2, testing → 1, completed → 0).
     * Используется для проверки: если на треке меньше задач, чем требуется эффектом, принимаем меньше ходов.
     */
    public function getMaxTaskMoveBlocksForPlayer(int $playerId): int
    {
        $tokens = $this->getTaskTokensByPlayer($playerId);
        $blocksByLocation = ['backlog' => 3, 'in-progress' => 2, 'testing' => 1, 'completed' => 0];
        $total = 0;
        foreach ($tokens as $token) {
            $loc = $token['location'] ?? 'backlog';
            $total += $blocksByLocation[$loc] ?? 0;
        }
        return $total;
    }

    public function getTaskTokensCount(int $playerId, ?string $color = null, ?string $location = null): int
    {
        $whereConditions = ["`player_id` = $playerId"];
        
        if ($color !== null) {
            $color = strtolower(trim($color));
            $whereConditions[] = "`color` = '$color'";
        }
        
        if ($location !== null) {
            $whereConditions[] = "`location` = '$location'";
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $result = $this->getUniqueValueFromDb("
            SELECT COUNT(*)
            FROM `player_task_token`
            WHERE $whereClause
        ");
        
        return (int)$result;
    }

    /**
     * Получает игровые данные игрока из таблицы player_game_data
     * @param int $playerId ID игрока
     * @return array|null Массив данных игрока или null если не найдено
     */
    public function getPlayerGameData(int $playerId): ?array
    {
        $data = $this->getObjectFromDb("
            SELECT * FROM `player_game_data` WHERE `player_id` = $playerId
        ");
        
        if (!$data) {
            return null;
        }
        // Нормализуем трек бэк-офиса (колонка 1 — базовая позиция трека найма) в диапазон 1–6.
        $backOfficeCol1 = $data['back_office_col1'] !== null
            ? max(1, min(6, (int)$data['back_office_col1']))
            : 1;

        // Применяем таблицу трека найма:
        // hiringTrackPosition - hiringTrackInterview - hiringTrackHire
        // 1 - 2 - 3
        // 2 - 3 - 3
        // 3 - 4 - 3
        // 4 - 5 - 3
        // 5 - 5 - 4
        // 6 - 6 - 4
        $hiringTrack = $this->mapHiringTrackFromPosition($backOfficeCol1);

        return [
            'incomeTrack' => $data['income_track'] !== null ? (int)$data['income_track'] : 1,
            'badgers' => $data['badgers'] !== null ? (int)$data['badgers'] : 0,
            // Треки бэк-офиса в БД всегда храним в диапазоне 1–6.
            // Для колонки 1 (трек найма) дополнительно гарантируем минимальное значение 1,
            // чтобы планшет и SQL всегда совпадали по логике.
            'backOfficeCol1' => $backOfficeCol1,
            // Дополнительные производные поля для трека найма:
            // - hiringTrackPosition: общая позиция трека (1–6)
            // - hiringTrackInterview: значение по треку «собеседование»
            // - hiringTrackHire: значение по треку «найм»
            'hiringTrackPosition' => $hiringTrack['position'],
            'hiringTrackInterview' => $hiringTrack['interview'],
            'hiringTrackHire' => $hiringTrack['hire'],
            'backOfficeCol2' => $data['back_office_col2'] !== null ? (int)$data['back_office_col2'] : null,
            'backOfficeCol3' => $data['back_office_col3'] !== null ? (int)$data['back_office_col3'] : null,
            'techDevCol1' => $data['tech_dev_col1'] !== null ? (int)$data['tech_dev_col1'] : null,
            'techDevCol2' => $data['tech_dev_col2'] !== null ? (int)$data['tech_dev_col2'] : null,
            'techDevCol3' => $data['tech_dev_col3'] !== null ? (int)$data['tech_dev_col3'] : null,
            'techDevCol4' => $data['tech_dev_col4'] !== null ? (int)$data['tech_dev_col4'] : null,
            'skillToken' => $data['skill_token'],
            'sprintColumnTasksProgress' => $data['sprint_column_tasks_progress'] !== null ? (int)$data['sprint_column_tasks_progress'] : null,
            'sprintTrack' => [
                'backlog' => !empty($data['sprint_track_backlog']) ? json_decode($data['sprint_track_backlog'], true) : [],
                'inProgress' => !empty($data['sprint_track_in_progress']) ? json_decode($data['sprint_track_in_progress'], true) : [],
                'testing' => !empty($data['sprint_track_testing']) ? json_decode($data['sprint_track_testing'], true) : [],
                'completed' => !empty($data['sprint_track_completed']) ? json_decode($data['sprint_track_completed'], true) : [],
            ],
            'taskTokens' => !empty($data['task_tokens']) ? json_decode($data['task_tokens'], true) : [],
            'projectTokens' => !empty($data['project_tokens']) ? json_decode($data['project_tokens'], true) : [],
            'specialistHand' => !empty($data['specialist_hand']) ? json_decode($data['specialist_hand'], true) : [],
            'playerSpecialists' => !empty($data['player_specialists']) ? json_decode($data['player_specialists'], true) : [],
            'playerHiredSpecialists' => $this->decodePlayerHiredSpecialists($data['player_hired_specialists'] ?? null),
            'itProjectBonuses' => !empty($data['it_project_bonuses']) ? json_decode($data['it_project_bonuses'], true) : [],
            'gameGoals' => !empty($data['game_goals']) ? json_decode($data['game_goals'], true) : [],
        ];
    }

    /**
     * Декодирует JSON нанятых специалистов по отделам.
     * @return array<string, array<int>> Отдел => массив ID карт
     */
    private function decodePlayerHiredSpecialists(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Возвращает нанятых специалистов игрока по отделам (из globals, затем БД).
     * @return array<string, array<int>> Отдел => массив ID карт
     */
    public function getPlayerHiredSpecialists(int $playerId): array
    {
        $json = $this->globals->get('player_hired_specialists_' . $playerId, '');
        if ($json !== '') {
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        }
        $data = $this->getPlayerGameData($playerId);
        return $data['playerHiredSpecialists'] ?? [];
    }

    /**
     * Сохраняет нанятых специалистов по отделам (globals + БД).
     * @param array<string, array<int>> $byDepartment Отдел => массив ID карт
     */
    public function setPlayerHiredSpecialists(int $playerId, array $byDepartment): void
    {
        $this->initPlayerGameData($playerId);
        $json = json_encode($byDepartment, JSON_UNESCAPED_UNICODE);
        $this->globals->set('player_hired_specialists_' . $playerId, $json);
        $escaped = addslashes($json);
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `player_hired_specialists` = '$escaped'
            WHERE `player_id` = $playerId
        ");
    }

    /** Допустимые отделы для размещения нанятых специалистов (универсальные карты). */
    private const HIRED_DEPARTMENTS = ['sales-department', 'back-office', 'technical-department'];

    /**
     * Возвращает нанятых специалистов игрока по отделам с полными данными карт (id, name, img) для отрисовки.
     * @return array<string, array<array{id: int, name: string, img: string}>>
     */
    public function getPlayerHiredSpecialistsDetails(int $playerId): array
    {
        return $this->expandHiredSpecialistsToDetails($this->getPlayerHiredSpecialists($playerId));
    }

    /**
     * Разворачивает нанятых специалистов по отделам в полные данные карт (id, name, img) для отрисовки на клиенте.
     * @param array<string, array<int>> $hiredByDept отдел => массив ID карт
     * @return array<string, array<array{id: int, name: string, img: string}>>
     */
    private function expandHiredSpecialistsToDetails(array $hiredByDept): array
    {
        $result = [];
        foreach ($hiredByDept as $dept => $ids) {
            $result[$dept] = [];
            foreach ($ids as $cardId) {
                $card = \Bga\Games\itarenagame\SpecialistsData::getCard((int) $cardId);
                if ($card) {
                    $result[$dept][] = [
                        'id' => (int) ($card['id'] ?? $cardId),
                        'name' => (string) ($card['name'] ?? ''),
                        'img' => (string) ($card['img'] ?? ''),
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * Нанять выбранные карты с руки: списать баджерсы, убрать из руки, добавить в отделы по карте.
     * Для карт с отделом 'universal' используется отдел из $universalDepartments[cardId].
     *
     * @param int $playerId ID игрока
     * @param array<int> $cardIds ID карт с руки для найма
     * @param array<int, string> $universalDepartments для универсальных карт: cardId => отдел (sales-department|back-office|technical-department)
     * @throws \Bga\GameFramework\UserException
     */
    public function hireSpecialistsFromHand(int $playerId, array $cardIds, array $universalDepartments = []): void
    {
        $cardIds = array_map('intval', array_values($cardIds));
        if (empty($cardIds)) {
            return;
        }

        $handJson = $this->globals->get('player_specialists_' . $playerId, '');
        $hand = !empty($handJson) ? json_decode($handJson, true) : [];
        $hand = is_array($hand) ? array_map('intval', $hand) : [];

        $totalPrice = 0;
        $byDepartment = [];
        foreach ($cardIds as $cardId) {
            if (!in_array($cardId, $hand, true)) {
                throw new \Bga\GameFramework\UserException(clienttranslate('Card is not in your hand'));
            }
            $card = \Bga\Games\itarenagame\SpecialistsData::getCard($cardId);
            if (!$card) {
                throw new \Bga\GameFramework\UserException(clienttranslate('Invalid card'));
            }
            $price = (int) ($card['price'] ?? 0);
            $totalPrice += $price;
            $dept = $card['department'] ?? 'universal';
            if ($dept === 'universal') {
                $chosen = $universalDepartments[$cardId] ?? null;
                if ($chosen === null || !in_array($chosen, self::HIRED_DEPARTMENTS, true)) {
                    throw new \Bga\GameFramework\UserException(clienttranslate('You must choose a department for each universal specialist'));
                }
                $dept = $chosen;
            }
            if (!isset($byDepartment[$dept])) {
                $byDepartment[$dept] = [];
            }
            $byDepartment[$dept][] = $cardId;
        }

        $badgers = $this->getPlayerBadgersForCheck($playerId);
        if ($totalPrice > $badgers) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Not enough badgers to hire selected specialists'));
        }

        $this->deductPlayerBadgers($playerId, $totalPrice);
        $remainingHand = array_values(array_diff($hand, $cardIds));
        $this->globals->set('player_specialists_' . $playerId, json_encode($remainingHand));
        $this->initPlayerGameData($playerId);
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `player_specialists` = '" . addslashes(json_encode($remainingHand)) . "'
            WHERE `player_id` = $playerId
        ");

        $currentHired = $this->getPlayerHiredSpecialists($playerId);
        foreach ($byDepartment as $dept => $ids) {
            $currentHired[$dept] = array_merge($currentHired[$dept] ?? [], $ids);
        }
        $this->setPlayerHiredSpecialists($playerId, $currentHired);
    }

    /**
     * Нанять одну карту с руки: списать баджерсы, убрать из руки, добавить в отдел.
     * Для универсальной карты $department обязателен; для остальных берётся из карты.
     *
     * @param int $playerId ID игрока
     * @param int $cardId ID карты
     * @param string|null $department отдел (обязателен для universal)
     */
    public function hireOneSpecialistFromHand(int $playerId, int $cardId, ?string $department = null): void
    {
        $cardId = (int) $cardId;
        $handJson = $this->globals->get('player_specialists_' . $playerId, '');
        $hand = !empty($handJson) ? json_decode($handJson, true) : [];
        $hand = is_array($hand) ? array_map('intval', $hand) : [];

        if (!in_array($cardId, $hand, true)) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Card is not in your hand'));
        }
        $card = \Bga\Games\itarenagame\SpecialistsData::getCard($cardId);
        if (!$card) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Invalid card'));
        }
        $price = (int) ($card['price'] ?? 0);
        $dept = $card['department'] ?? 'universal';
        if ($dept === 'universal') {
            if ($department === null || !in_array($department, self::HIRED_DEPARTMENTS, true)) {
                throw new \Bga\GameFramework\UserException(clienttranslate('You must choose a department for a universal specialist'));
            }
            $dept = $department;
        }

        $badgers = $this->getPlayerBadgersForCheck($playerId);
        if ($price > $badgers) {
            throw new \Bga\GameFramework\UserException(clienttranslate('Not enough badgers to hire this specialist'));
        }

        $this->deductPlayerBadgers($playerId, $price);
        $remainingHand = array_values(array_diff($hand, [$cardId]));
        $this->globals->set('player_specialists_' . $playerId, json_encode($remainingHand));
        $this->initPlayerGameData($playerId);
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `player_specialists` = '" . addslashes(json_encode($remainingHand)) . "'
            WHERE `player_id` = $playerId
        ");

        $currentHired = $this->getPlayerHiredSpecialists($playerId);
        if (!isset($currentHired[$dept])) {
            $currentHired[$dept] = [];
        }
        $currentHired[$dept][] = $cardId;
        $this->setPlayerHiredSpecialists($playerId, $currentHired);
    }

    /**
     * Возвращает количество баджерсов игрока для проверок.
     * Используется максимум из БД и счётчика, чтобы не было расхождений после эффектов/обновлений.
     */
    public function getPlayerBadgersForCheck(int $playerId): int
    {
        $this->initPlayerGameData($playerId);
        $gameData = $this->getPlayerGameData($playerId);
        $dbBadgers = $gameData !== null ? (int) ($gameData['badgers'] ?? 0) : 0;
        $counterBadgers = (int) $this->playerBadgers->get($playerId);
        return max($dbBadgers, $counterBadgers);
    }

    /**
     * Списывает баджерсы у игрока: обновляет БД и счётчик от одной и той же «эффективной» суммы.
     */
    public function deductPlayerBadgers(int $playerId, int $amount): void
    {
        if ($amount <= 0) {
            return;
        }
        $current = $this->getPlayerBadgersForCheck($playerId);
        $newValue = max(0, $current - $amount);
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `badgers` = $newValue
            WHERE `player_id` = $playerId
        ");
        $counterNow = (int) $this->playerBadgers->get($playerId);
        $this->playerBadgers->inc($playerId, $newValue - $counterNow);
    }

    /**
     * Добавляет баджерсы игроку: обновляет БД (player_game_data.badgers) и синхронизирует счётчик.
     */
    public function addPlayerBadgers(int $playerId, int $amount): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->initPlayerGameData($playerId);
        $gameData = $this->getPlayerGameData($playerId);
        $current = $gameData !== null ? (int) ($gameData['badgers'] ?? 0) : 0;
        $newValue = $current + $amount;
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `badgers` = $newValue
            WHERE `player_id` = $playerId
        ");
        $counterNow = (int) $this->playerBadgers->get($playerId);
        $this->playerBadgers->inc($playerId, $newValue - $counterNow);
    }

    /**
     * Фиксированная таблица трека найма.
     *
     * hiringTrackPosition - hiringTrackInterview - hiringTrackHire
     * 1 - 2 - 3
     * 2 - 3 - 3
     * 3 - 4 - 3
     * 4 - 5 - 3
     * 5 - 5 - 4
     * 6 - 6 - 4
     *
     * @param int $position Позиция трека (1–6)
     * @return array{position:int,interview:int,hire:int}
     */
    private function mapHiringTrackFromPosition(int $position): array
    {
        $position = max(1, min(6, $position));

        switch ($position) {
            case 1:
                return ['position' => 1, 'interview' => 2, 'hire' => 3];
            case 2:
                return ['position' => 2, 'interview' => 3, 'hire' => 3];
            case 3:
                return ['position' => 3, 'interview' => 4, 'hire' => 3];
            case 4:
                return ['position' => 4, 'interview' => 5, 'hire' => 3];
            case 5:
                return ['position' => 5, 'interview' => 5, 'hire' => 4];
            case 6:
            default:
                return ['position' => 6, 'interview' => 6, 'hire' => 4];
        }
    }
    
    /**
     * Инициализирует игровые данные игрока (создает запись если не существует)
     * @param int $playerId ID игрока
     */
    public function initPlayerGameData(int $playerId): void
    {
        $existing = $this->getObjectFromDb("
            SELECT `id` FROM `player_game_data` WHERE `player_id` = $playerId
        ");
        
        if (!$existing) {
            // Инициализируем с начальными значениями (треки бэк-офиса и техразвития стартуют с 1)
            $incomeTrack = $this->playerEnergy->get($playerId);
            $badgers = $this->playerBadgers->get($playerId);
            $this->DbQuery("
                INSERT INTO `player_game_data` (
                    `player_id`, `income_track`, `badgers`,
                    `back_office_col1`, `back_office_col2`, `back_office_col3`,
                    `tech_dev_col1`, `tech_dev_col2`, `tech_dev_col3`, `tech_dev_col4`
                ) VALUES (
                    $playerId, $incomeTrack, $badgers,
                    1, 1, 1,
                    1, 1, 1, 1
                )
            ");
        }
    }
    
    /**
     * Обновляет позицию на треке бэк-офиса
     * @param int $playerId ID игрока
     * @param int $column Номер колонки (1, 2, 3)
     * @param int|null $value Новое значение позиции (null для сброса)
     */
    public function setBackOfficeColumn(int $playerId, int $column, ?int $value): void
    {
        $this->initPlayerGameData($playerId);
        $columnName = 'back_office_col' . $column;
        if ($value !== null) {
            // Все треки бэк-офиса на планшете имеют диапазон 1–6.
            // Здесь жестко ограничиваем значение внутри этого диапазона,
            // чтобы состояние в SQL не расходилось с визуальным треком.
            $clampedValue = max(1, min(6, (int)$value));
            $valueStr = $clampedValue;
        } else {
            // null по-прежнему используется как сброс/отсутствие значения.
            $valueStr = 'NULL';
        }
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `$columnName` = $valueStr 
            WHERE `player_id` = $playerId
        ");
    }
    
    /**
     * Обновляет позицию на треке технического развития
     * @param int $playerId ID игрока
     * @param int $column Номер колонки (1, 2, 3, 4)
     * @param int|null $value Новое значение позиции (null для сброса)
     */
    public function setTechDevColumn(int $playerId, int $column, ?int $value): void
    {
        $this->initPlayerGameData($playerId);
        $columnName = 'tech_dev_col' . $column;
        $valueStr = $value !== null ? (int)$value : 'NULL';
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `$columnName` = $valueStr 
            WHERE `player_id` = $playerId
        ");
    }
    
    /**
     * Увеличивает позицию на треке бэк-офиса
     * @param int $playerId ID игрока
     * @param int $column Номер колонки (1, 2, 3)
     * @param int $amount На сколько увеличить
     */
    public function incBackOfficeColumn(int $playerId, int $column, int $amount): void
    {
        $this->initPlayerGameData($playerId);
        $data = $this->getPlayerGameData($playerId);
        $currentValue = $data['backOfficeCol' . $column] ?? 0;
        $newValue = $currentValue + $amount;
        $this->setBackOfficeColumn($playerId, $column, $newValue);
    }
    
    /**
     * Увеличивает позицию на треке технического развития
     * @param int $playerId ID игрока
     * @param int $column Номер колонки (1, 2, 3, 4)
     * @param int $amount На сколько увеличить
     */
    public function incTechDevColumn(int $playerId, int $column, int $amount): void
    {
        $this->initPlayerGameData($playerId);
        $data = $this->getPlayerGameData($playerId);
        $currentValue = $data['techDevCol' . $column] ?? 0;
        $newValue = $currentValue + $amount;
        $this->setTechDevColumn($playerId, $column, $newValue);
    }
    
    /**
     * Устанавливает жетон навыка
     * @param int $playerId ID игрока
     * @param string|null $skillToken Значение жетона навыка (null для удаления)
     */
    public function setSkillToken(int $playerId, ?string $skillToken): void
    {
        $this->initPlayerGameData($playerId);
        $valueStr = $skillToken !== null ? "'" . addslashes($skillToken) . "'" : 'NULL';
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `skill_token` = $valueStr 
            WHERE `player_id` = $playerId
        ");
    }

    /**
     * Сбрасывает жетоны навыков у всех игроков (в начале раунда, фаза «Событие»).
     */
    public function clearAllSkillTokens(): void
    {
        $this->DbQuery("UPDATE `player_game_data` SET `skill_token` = NULL");
    }

    /**
     * Порядок навыков на треке (слева направо = раньше ход в следующем раунде).
     * Используется для определения очереди хода в следующем раунде по положению на треке навыков.
     */
    private const SKILL_TRACK_ORDER = [
        SkillsData::SKILL_ELOQUENCE,
        SkillsData::SKILL_DISCIPLINE,
        SkillsData::SKILL_INTELLECT,
        SkillsData::SKILL_FRUGALITY,
    ];

    /**
     * Вычисляет порядок игроков для следующего раунда по положению на треке навыков.
     * Чем левее выбранный навык — тем раньше ход в следующем раунде.
     * При одинаковом навыке сохраняется порядок стола (player_no).
     *
     * @return list<int> Массив player_id в порядке хода для следующего раунда
     */
    public function getSkillOrderForNextRound(): array
    {
        $rows = $this->getCollectionFromDb("SELECT `player_id`, `skill_token` FROM `player_game_data`");
        $skillByPlayer = [];
        foreach ($rows as $row) {
            $skillByPlayer[(int) $row['player_id']] = $row['skill_token'] ?? null;
        }
        $basicInfos = $this->loadPlayersBasicInfos();
        $tableOrder = array_map('intval', array_keys($basicInfos));

        $withPosition = [];
        foreach ($tableOrder as $tableIdx => $playerId) {
            $skillToken = $skillByPlayer[$playerId] ?? null;
            $pos = 999;
            if ($skillToken !== null && $skillToken !== '') {
                $idx = array_search($skillToken, self::SKILL_TRACK_ORDER, true);
                if ($idx !== false) {
                    $pos = $idx;
                }
            }
            $withPosition[] = ['id' => $playerId, 'position' => $pos, 'tableIdx' => $tableIdx];
        }
        usort($withPosition, static fn ($a, $b) => ($a['position'] <=> $b['position']) ?: ($a['tableIdx'] <=> $b['tableIdx']));
        return array_column($withPosition, 'id');
    }

    /**
     * Сохраняет порядок игроков для следующего раунда (по треку навыков).
     */
    public function setNextRoundPlayerOrder(array $playerIds): void
    {
        $this->globals->set('next_round_player_order', json_encode(array_map('intval', $playerIds)));
    }

    /**
     * Возвращает сохранённый порядок для следующего раунда и удаляет его из globals.
     *
     * @return list<int>|null Массив player_id или null
     */
    public function takeNextRoundPlayerOrder(): ?array
    {
        $json = $this->globals->get('next_round_player_order', '');
        $this->globals->delete('next_round_player_order');
        if ($json === '' || $json === null) {
            return null;
        }
        $decoded = json_decode($json, true);
        return is_array($decoded) ? array_map('intval', $decoded) : null;
    }

    /**
     * Устанавливает порядок хода для текущего раунда (используется в фазах раунда).
     */
    public function setCurrentRoundPlayerOrder(array $playerIds): void
    {
        $this->globals->set('current_round_player_order', json_encode(array_map('intval', $playerIds)));
    }

    /**
     * Возвращает порядок хода для текущего раунда.
     *
     * @return list<int>|null Массив player_id или null (тогда используется порядок стола)
     */
    public function getCurrentRoundPlayerOrder(): ?array
    {
        $json = $this->globals->get('current_round_player_order', '');
        if ($json === '' || $json === null) {
            return null;
        }
        $decoded = json_decode($json, true);
        return is_array($decoded) ? array_map('intval', $decoded) : null;
    }

    /**
     * Возвращает ID следующего игрока в порядке текущего раунда (по треку навыков).
     * Если порядок не задан или текущий игрок последний — возвращает null (нужно использовать activeNextPlayer).
     */
    public function getNextPlayerIdInRoundOrder(): ?int
    {
        $order = $this->getCurrentRoundPlayerOrder();
        if ($order === null || $order === []) {
            return null;
        }
        $activeId = (int) $this->getActivePlayerId();
        $idx = array_search($activeId, $order, true);
        if ($idx === false || $idx + 1 >= count($order)) {
            return null;
        }
        return $order[$idx + 1];
    }

    /**
     * Передаёт ход следующему игроку: по порядку раунда (трек навыков), если задан, иначе по порядку стола.
     */
    public function advanceToNextPlayerInRound(): void
    {
        $nextId = $this->getNextPlayerIdInRoundOrder();
        if ($nextId !== null) {
            $this->gamestate->changeActivePlayer($nextId);
        } else {
            $this->activeNextPlayer();
        }
    }
    
    /**
     * Устанавливает прогресс трека задач
     * @param int $playerId ID игрока
     * @param int|null $progress Значение прогресса (null для сброса)
     */
    public function setSprintColumnTasksProgress(int $playerId, ?int $progress): void
    {
        $this->initPlayerGameData($playerId);
        $valueStr = $progress !== null ? (int)$progress : 'NULL';
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `sprint_column_tasks_progress` = $valueStr 
            WHERE `player_id` = $playerId
        ");
    }
    
    /**
     * Устанавливает бонусы IT проектов
     * @param int $playerId ID игрока
     * @param array $bonuses Массив бонусов
     */
    public function setItProjectBonuses(int $playerId, array $bonuses): void
    {
        $this->initPlayerGameData($playerId);
        $json = json_encode($bonuses, JSON_UNESCAPED_UNICODE);
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `it_project_bonuses` = '" . addslashes($json) . "' 
            WHERE `player_id` = $playerId
        ");
    }
    
    /**
     * Устанавливает цели игры
     * @param int $playerId ID игрока
     * @param array $goals Массив целей
     */
    public function setGameGoals(int $playerId, array $goals): void
    {
        $this->initPlayerGameData($playerId);
        $json = json_encode($goals, JSON_UNESCAPED_UNICODE);
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET `game_goals` = '" . addslashes($json) . "' 
            WHERE `player_id` = $playerId
        ");
    }
    
    /**
     * Сохраняет все данные игрока в таблицу player_game_data при завершении хода
     * @param int $playerId ID игрока
     */
    public function savePlayerGameDataOnTurnEnd(int $playerId): void
    {
        $this->initPlayerGameData($playerId);
        
        // Получаем текущие данные из различных источников
        $incomeTrack = $this->playerEnergy->get($playerId);
        $badgers = $this->playerBadgers->get($playerId);
        
        // Получаем жетоны задач
        $taskTokens = $this->getTaskTokensByPlayer($playerId);
        $taskTokensJson = json_encode($taskTokens, JSON_UNESCAPED_UNICODE);
        
        // Получаем жетоны проектов
        $projectTokens = $this->getProjectTokensByPlayer($playerId);
        $projectTokensJson = json_encode($projectTokens, JSON_UNESCAPED_UNICODE);
        
        // Получаем карты специалистов на руке
        $specialistHandIdsJson = $this->globals->get('specialist_hand_' . $playerId, '');
        $specialistHandIds = !empty($specialistHandIdsJson) ? json_decode($specialistHandIdsJson, true) : [];
        $specialistHandJson = json_encode($specialistHandIds, JSON_UNESCAPED_UNICODE);
        
        // Получаем карты сотрудников (подтвержденные)
        $playerSpecialistsIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
        $playerSpecialistsIds = !empty($playerSpecialistsIdsJson) ? json_decode($playerSpecialistsIdsJson, true) : [];
        $playerSpecialistsJson = json_encode($playerSpecialistsIds, JSON_UNESCAPED_UNICODE);

        // Нанятые специалисты по отделам
        $hiredSpecialistsJson = $this->globals->get('player_hired_specialists_' . $playerId, '');
        $hiredSpecialistsEscaped = $hiredSpecialistsJson !== '' ? addslashes($hiredSpecialistsJson) : '{}';
        
        // Формируем трек спринта из жетонов задач
        $sprintTrackBacklog = [];
        $sprintTrackInProgress = [];
        $sprintTrackTesting = [];
        $sprintTrackCompleted = [];
        
        foreach ($taskTokens as $token) {
            $location = $token['location'] ?? 'backlog';
            switch ($location) {
                case 'backlog':
                    $sprintTrackBacklog[] = $token;
                    break;
                case 'in-progress':
                    $sprintTrackInProgress[] = $token;
                    break;
                case 'testing':
                    $sprintTrackTesting[] = $token;
                    break;
                case 'completed':
                    $sprintTrackCompleted[] = $token;
                    break;
            }
        }
        
        $sprintTrackBacklogJson = json_encode($sprintTrackBacklog, JSON_UNESCAPED_UNICODE);
        $sprintTrackInProgressJson = json_encode($sprintTrackInProgress, JSON_UNESCAPED_UNICODE);
        $sprintTrackTestingJson = json_encode($sprintTrackTesting, JSON_UNESCAPED_UNICODE);
        $sprintTrackCompletedJson = json_encode($sprintTrackCompleted, JSON_UNESCAPED_UNICODE);
        
        // Получаем текущие данные из таблицы (чтобы не перезаписать то, что уже есть)
        $currentData = $this->getPlayerGameData($playerId);
        
        // Обновляем все данные в таблице
        $this->DbQuery("
            UPDATE `player_game_data` 
            SET 
                `income_track` = $incomeTrack,
                `badgers` = $badgers,
                `task_tokens` = '" . addslashes($taskTokensJson) . "',
                `project_tokens` = '" . addslashes($projectTokensJson) . "',
                `specialist_hand` = '" . addslashes($specialistHandJson) . "',
                `player_specialists` = '" . addslashes($playerSpecialistsJson) . "',
                `player_hired_specialists` = '" . $hiredSpecialistsEscaped . "',
                `sprint_track_backlog` = '" . addslashes($sprintTrackBacklogJson) . "',
                `sprint_track_in_progress` = '" . addslashes($sprintTrackInProgressJson) . "',
                `sprint_track_testing` = '" . addslashes($sprintTrackTestingJson) . "',
                `sprint_track_completed` = '" . addslashes($sprintTrackCompletedJson) . "'
            WHERE `player_id` = $playerId
        ");
        
        error_log("💾 savePlayerGameDataOnTurnEnd - Saved all data for player $playerId: incomeTrack=$incomeTrack, badgers=$badgers, taskTokens=" . count($taskTokens) . ", projectTokens=" . count($projectTokens));
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
        // Сначала проверим, сколько всего токенов в таблице
        $totalCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token`");
        $withPositionCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IS NOT NULL");
        error_log("getProjectTokensOnBoard - Total tokens in DB: $totalCount, with board_position: $withPositionCount");
        
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
        
        error_log("getProjectTokensOnBoard - Query returned: " . count($tokens) . " tokens");
        
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
        error_log("initializeProjectTokensIfNeeded - Existing count: $existingCount");
        if ($existingCount > 0) {
            error_log("initializeProjectTokensIfNeeded - Tokens already exist, skipping initialization");
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
        error_log("placeProjectTokensOnRedColumn - STARTING");
        
        // Проверяем, не размещены ли уже жетоны
        $placedCount = $this->getUniqueValueFromDb("SELECT COUNT(*) FROM `project_token` WHERE `board_position` IN ('red-hex', 'red-square', 'red-circle-1', 'red-circle-2')");
        error_log("placeProjectTokensOnRedColumn - Already placed count: $placedCount");
        
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

    // ========================================
    // МЕТОДЫ ДЛЯ РАБОТЫ С КАРТАМИ СОТРУДНИКОВ
    // ========================================
    // Управление колодами специалистов

    /**
     * Инициализирует основную колоду специалистов всеми 110 картами
     */
    public function initSpecialistDecks(): void
    {
        $allCards = SpecialistsData::getAllCards();
        $allCardIds = array_map(fn($card) => (int)$card['id'], $allCards);
        
        // Перемешиваем основную колоду
        shuffle($allCardIds);
        
        // Сохраняем основную колоду
        $this->globals->set('specialist_main_deck', json_encode($allCardIds));
        
        // Инициализируем пустую колоду сброса
        $this->globals->set('specialist_discard_pile', json_encode([]));
        
        // Инициализируем пустую промежуточную колоду
        $this->globals->set('specialist_intermediate_deck', json_encode([]));
        
        error_log("initSpecialistDecks - Initialized main deck with " . count($allCardIds) . " cards");
    }

    /**
     * Гарантирует, что колода специалистов инициализирована (для рекрутинга в фазе найма).
     * Вызывать при входе в RoundHiring: только если глобал вообще не задан — создаём колоду.
     * Пустой массив не перезаписываем (карты уже розданы, дублировать нельзя).
     */
    public function ensureSpecialistDeckInitialized(): void
    {
        $mainDeckRaw = $this->globals->get('specialist_main_deck', '');
        if ($mainDeckRaw === '' || $mainDeckRaw === null) {
            $this->initSpecialistDecks();
        }
    }

    /**
     * Получает основную колоду специалистов
     * @return array Массив ID карт
     */
    private function getMainDeck(): array
    {
        $deckJson = $this->globals->get('specialist_main_deck', '');
        $deck = !empty($deckJson) ? json_decode($deckJson, true) : [];
        return is_array($deck) ? array_map('intval', $deck) : [];
    }

    /**
     * Сохраняет основную колоду специалистов
     * @param array $deck Массив ID карт
     */
    private function setMainDeck(array $deck): void
    {
        $this->globals->set('specialist_main_deck', json_encode(array_map('intval', $deck)));
    }

    /**
     * Получает промежуточную колоду специалистов
     * @return array Массив ID карт
     */
    private function getIntermediateDeck(): array
    {
        $deckJson = $this->globals->get('specialist_intermediate_deck', '');
        $deck = !empty($deckJson) ? json_decode($deckJson, true) : [];
        return is_array($deck) ? array_map('intval', $deck) : [];
    }

    /**
     * Сохраняет промежуточную колоду специалистов
     * @param array $deck Массив ID карт
     */
    private function setIntermediateDeck(array $deck): void
    {
        $this->globals->set('specialist_intermediate_deck', json_encode(array_map('intval', $deck)));
    }

    /**
     * Получает колоду сброса специалистов
     * @return array Массив ID карт
     */
    private function getDiscardPile(): array
    {
        $pileJson = $this->globals->get('specialist_discard_pile', '');
        $pile = !empty($pileJson) ? json_decode($pileJson, true) : [];
        return is_array($pile) ? array_map('intval', $pile) : [];
    }

    /**
     * Сохраняет колоду сброса специалистов
     * @param array $pile Массив ID карт
     */
    private function setDiscardPile(array $pile): void
    {
        $this->globals->set('specialist_discard_pile', json_encode(array_map('intval', $pile)));
    }

    /**
     * Добавляет карты в колоду сброса
     * @param array $cardIds Массив ID карт для добавления
     */
    public function addToDiscardPile(array $cardIds): void
    {
        $pile = $this->getDiscardPile();
        $pile = array_merge($pile, array_map('intval', $cardIds));
        $this->setDiscardPile($pile);
        error_log("addToDiscardPile - Added " . count($cardIds) . " cards. Total in discard: " . count($pile));
    }

    /**
     * Берет карты из активной колоды (основной или промежуточной)
     * Автоматически переключается между колодами при необходимости
     * @param int $count Количество карт для взятия
     * @return array Массив ID карт
     */
    public function drawFromActiveDeck(int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        // Ленивая инициализация колоды специалистов для старых/недоинициализированных партий:
        // если specialist_main_deck ещё не задан в globals, создаём колоды сейчас.
        $mainDeckRaw = $this->globals->get('specialist_main_deck', '');
        if ($mainDeckRaw === '' || $mainDeckRaw === null) {
            error_log('drawFromActiveDeck - specialist_main_deck not initialized, initializing specialist decks now...');
            $this->initSpecialistDecks();
        }

        $drawnCards = [];
        
        // Сначала пытаемся взять из основной колоды
        $mainDeck = $this->getMainDeck();
        
        if (count($mainDeck) >= $count) {
            // Достаточно карт в основной колоде
            $drawnCards = array_slice($mainDeck, 0, $count);
            $remainingDeck = array_slice($mainDeck, $count);
            $this->setMainDeck($remainingDeck);
            error_log("drawFromActiveDeck - Drew $count cards from main deck. Remaining: " . count($remainingDeck));
            return $drawnCards;
        }
        
        // Берем все оставшиеся карты из основной колоды
        if (!empty($mainDeck)) {
            $drawnCards = $mainDeck;
            $this->setMainDeck([]);
            $count -= count($drawnCards);
            error_log("drawFromActiveDeck - Took all " . count($drawnCards) . " cards from main deck. Need $count more.");
        }
        
        // Основная колода пуста - проверяем промежуточную
        $intermediateDeck = $this->getIntermediateDeck();
        
        if (count($intermediateDeck) >= $count) {
            // Достаточно карт в промежуточной колоде
            $additionalCards = array_slice($intermediateDeck, 0, $count);
            $drawnCards = array_merge($drawnCards, $additionalCards);
            $remainingDeck = array_slice($intermediateDeck, $count);
            $this->setIntermediateDeck($remainingDeck);
            error_log("drawFromActiveDeck - Drew $count cards from intermediate deck. Remaining: " . count($remainingDeck));
            return $drawnCards;
        }
        
        // Берем все оставшиеся карты из промежуточной колоды
        if (!empty($intermediateDeck)) {
            $drawnCards = array_merge($drawnCards, $intermediateDeck);
            $this->setIntermediateDeck([]);
            $count -= count($intermediateDeck);
            error_log("drawFromActiveDeck - Took all " . count($intermediateDeck) . " cards from intermediate deck. Need $count more.");
        }
        
        // Промежуточная колода тоже пуста - создаем новую из колоды сброса
        $discardPile = $this->getDiscardPile();
        
        if (!empty($discardPile)) {
            // Перемешиваем колоду сброса
            shuffle($discardPile);
            
            // Создаем новую промежуточную колоду из колоды сброса
            $newIntermediateDeck = $discardPile;
            $this->setIntermediateDeck($newIntermediateDeck);
            
            // Очищаем колоду сброса
            $this->setDiscardPile([]);
            
            error_log("drawFromActiveDeck - Created new intermediate deck from discard pile with " . count($newIntermediateDeck) . " cards");
            
            // Теперь берем из новой промежуточной колоды
            if (count($newIntermediateDeck) >= $count) {
                $additionalCards = array_slice($newIntermediateDeck, 0, $count);
                $drawnCards = array_merge($drawnCards, $additionalCards);
                $remainingDeck = array_slice($newIntermediateDeck, $count);
                $this->setIntermediateDeck($remainingDeck);
                error_log("drawFromActiveDeck - Drew $count cards from new intermediate deck. Remaining: " . count($remainingDeck));
                return $drawnCards;
            } else {
                // Берем все карты из новой промежуточной колоды
                $drawnCards = array_merge($drawnCards, $newIntermediateDeck);
                $this->setIntermediateDeck([]);
                error_log("drawFromActiveDeck - Took all " . count($newIntermediateDeck) . " cards from new intermediate deck. Still need " . ($count - count($newIntermediateDeck)) . " more.");
            }
        }
        
        // Если все колоды пусты, возвращаем то, что удалось взять
        if (empty($drawnCards)) {
            error_log("drawFromActiveDeck - WARNING: All decks are empty! Cannot draw $count cards.");
        } else {
            error_log("drawFromActiveDeck - WARNING: Could only draw " . count($drawnCards) . " cards instead of $count");
        }
        
        return $drawnCards;
    }

    /**
     * Раздаёт указанное количество случайных карт сотрудников игроку
     * Использует систему колод: основная -> промежуточная -> колода сброса
     * @param int $playerId ID игрока
     * @param int $count Количество карт для раздачи
     * @return array Массив раздаённых карт
     */
    public function dealSpecialistCards(int $playerId, int $count): array
    {
        // Берем карты из активной колоды
        $drawnCardIds = $this->drawFromActiveDeck($count);
        
        if (empty($drawnCardIds)) {
            error_log("dealSpecialistCards - ERROR: No cards available for player $playerId");
            return [];
        }
        
        // Получаем полные данные карт по ID
        $dealtCards = [];
        foreach ($drawnCardIds as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $dealtCards[] = $card;
            }
        }
        
        error_log("dealSpecialistCards - Dealt " . count($dealtCards) . " cards to player $playerId");
        
        return $dealtCards;
    }

    /**
     * Получает ID всех использованных карт сотрудников
     * @return array Массив ID карт
     */
    public function getUsedSpecialistCardIds(): array
    {
        $usedIds = [];
        
        // Карты на руках игроков (теперь хранятся только ID!)
        $players = array_keys($this->loadPlayersBasicInfos());
        foreach ($players as $playerId) {
            // ID карт в процессе выбора (JSON)
            $handCardIdsJson = $this->globals->get('specialist_hand_' . $playerId, '');
            $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
            if (!empty($handCardIds) && is_array($handCardIds)) {
                $usedIds = array_merge($usedIds, $handCardIds);
            }
            
            // ID выбранных карт (JSON)
            $keptCardIdsJson = $this->globals->get('player_specialists_' . $playerId, '');
            $keptCardIds = !empty($keptCardIdsJson) ? json_decode($keptCardIdsJson, true) : [];
            if (!empty($keptCardIds) && is_array($keptCardIds)) {
                $usedIds = array_merge($usedIds, $keptCardIds);
            }
        }
        
        // ID карт в сбросе (используем метод для получения)
        $discardPile = $this->getDiscardPile();
        if (!empty($discardPile)) {
            $usedIds = array_merge($usedIds, $discardPile);
        }
        
        // ID карт в промежуточной колоде (тоже считаются использованными для проверки)
        $intermediateDeck = $this->getIntermediateDeck();
        if (!empty($intermediateDeck)) {
            $usedIds = array_merge($usedIds, $intermediateDeck);
        }
        
        // ID карт в основной колоде (тоже считаются использованными для проверки)
        $mainDeck = $this->getMainDeck();
        if (!empty($mainDeck)) {
            $usedIds = array_merge($usedIds, $mainDeck);
        }
        
        return array_unique($usedIds);
    }

    /**
     * Проверяет, все ли игроки выбрали карты сотрудников
     * @return bool
     */
    public function allPlayersSelectedSpecialists(): bool
    {
        $players = array_keys($this->loadPlayersBasicInfos());
        foreach ($players as $playerId) {
            $done = $this->globals->get('specialist_selection_done_' . $playerId, false);
            if (!$done) {
                error_log("allPlayersSelectedSpecialists - Player $playerId has NOT selected specialists yet");
                return false;
            }
        }
        error_log("allPlayersSelectedSpecialists - All players have selected specialists");
        return true;
    }

    /**
     * Получает карты сотрудников игрока (полные данные по ID)
     * @param int $playerId ID игрока
     * @return array Массив карт
     */
    public function getPlayerSpecialists(int $playerId): array
    {
        $idsJson = $this->globals->get('player_specialists_' . $playerId, '');
        $ids = !empty($idsJson) ? json_decode($idsJson, true) : [];
        
        $cards = [];
        foreach ($ids as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $cards[] = $card;
            }
        }
        return $cards;
    }

    /**
     * Получает карты сотрудников для выбора (на руке) - полные данные по ID
     * @param int $playerId ID игрока
     * @return array Массив карт
     */
    public function getSpecialistHandCards(int $playerId): array
    {
        $idsJson = $this->globals->get('specialist_hand_' . $playerId, '');
        $ids = !empty($idsJson) ? json_decode($idsJson, true) : [];
        
        $cards = [];
        foreach ($ids as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $cards[] = $card;
            }
        }
        return $cards;
    }

    /**
     * Возвращает значение трека «собеседование» для найма.
     * Игрок получает столько карт найма, сколько указано в колонке «собеседование»
     * согласно фиксированной таблице трека найма.
     *
     * @param int $playerId ID игрока
     * @return int Значение от 2 до 6 (количество карт для рекрутинга)
     */
    public function getHiringTrackValue(int $playerId): int
    {
        $data = $this->getPlayerGameData($playerId);
        if ($data === null) {
            // На всякий случай возвращаем минимум по таблице
            return 2;
        }

        // Используем значение из колонки 1 (позиция трека найма)
        $position = isset($data['backOfficeCol1']) ? (int)$data['backOfficeCol1'] : 1;
        $hiringTrack = $this->mapHiringTrackFromPosition($position);

        // Для рекрутинга используем значение собеседований
        return $hiringTrack['interview'];
    }

    /**
     * Возвращает, сколько специалистов игрок может нанять в этой фазе (по треку найма).
     * По таблице: позиции 1–4 → 3, позиции 5–6 → 4. Гарантированно не меньше 3.
     */
    public function getHiringTrackHireCount(int $playerId): int
    {
        $this->initPlayerGameData($playerId);
        $data = $this->getPlayerGameData($playerId);
        if ($data === null) {
            return 3;
        }
        $position = isset($data['backOfficeCol1']) ? (int) $data['backOfficeCol1'] : 1;
        $position = max(1, min(6, $position));
        $hiringTrack = $this->mapHiringTrackFromPosition($position);
        $hire = (int) $hiringTrack['hire'];
        return max(3, $hire);
    }

    /**
     * Добавляет карты специалистов в руку игрока (specialist_hand_).
     * Данные сохраняются в globals; при сохранении хода попадут в player_game_data.specialist_hand.
     *
     * @param int $playerId ID игрока
     * @param array $cardIds Массив ID карт для добавления в руку
     */
    public function addSpecialistCardsToHand(int $playerId, array $cardIds): void
    {
        if (empty($cardIds)) {
            return;
        }
        $cardIds = array_map('intval', $cardIds);
        $handJson = $this->globals->get('specialist_hand_' . $playerId, '');
        $hand = !empty($handJson) ? json_decode($handJson, true) : [];
        if (!is_array($hand)) {
            $hand = [];
        }
        $hand = array_merge($hand, $cardIds);
        $this->globals->set('specialist_hand_' . $playerId, json_encode($hand));
    }

    /**
     * Добавляет карты специалистов игроку как "закреплённые/на руке" (player_specialists_).
     * Используется для эффектов и рекрутинга: эти карты сразу отображаются в руке игрока.
     * Дополнительно синхронизирует список в player_game_data.player_specialists для строгого соответствия SQL.
     *
     * @param int $playerId ID игрока
     * @param array $cardIds Массив ID карт для добавления
     */
    public function addSpecialistCardsToPlayerSpecialists(int $playerId, array $cardIds): void
    {
        if (empty($cardIds)) {
            return;
        }

        $cardIds = array_map('intval', $cardIds);

        $existingJson = $this->globals->get('player_specialists_' . $playerId, '');
        $existing = !empty($existingJson) ? json_decode($existingJson, true) : [];
        if (!is_array($existing)) {
            $existing = [];
        }
        $existing = array_map('intval', $existing);

        // Мержим без дублей, сохраняя порядок (сначала старые, потом новые)
        $seen = [];
        $merged = [];
        foreach (array_merge($existing, $cardIds) as $id) {
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $merged[] = $id;
            }
        }

        $this->globals->set('player_specialists_' . $playerId, json_encode($merged));

        // Синхронизируем в SQL, чтобы данные планшета/игрока не расходились.
        $this->initPlayerGameData($playerId);
        $json = json_encode($merged, JSON_UNESCAPED_UNICODE);
        $this->DbQuery("
            UPDATE `player_game_data`
            SET `player_specialists` = '" . addslashes($json) . "'
            WHERE `player_id` = $playerId
        ");
    }

    /**
     * Получает выбранные карты сотрудников (ещё не подтверждённые)
     * @param int $playerId ID игрока
     * @return array Массив ID карт
     */
    public function getSelectedSpecialistIds(int $playerId): array
    {
        $json = $this->globals->get('specialist_selected_' . $playerId, '');
        return !empty($json) ? json_decode($json, true) : [];
    }

}
