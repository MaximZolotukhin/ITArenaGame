
-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- ITArenaGame implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table
-- ALTER TABLE `player` ADD `player_my_custom_field` INT UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `event_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Таблица для хранения жетонов штрафа игроков
-- Каждый игрок может иметь до 2 жетонов штрафа
CREATE TABLE IF NOT EXISTS `player_penalty_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `penalty_value` int(11) NOT NULL DEFAULT 0,
  `token_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `player_order` (`player_id`, `token_order`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Таблица для хранения жетонов задач игроков
-- Жетоны задач привязываются к игроку, количество меняется по ходу игры
-- Всего 108 жетонов: по 27 каждого цвета (cyan, pink, orange, purple)
-- location: 'backlog', 'in-progress', 'testing', 'completed' - колонка трека спринта
-- row_index: номер строки в колонке (1-6 для колонки "Задачи", null для других колонок)
CREATE TABLE IF NOT EXISTS `player_task_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `color` varchar(20) NOT NULL,
  `location` varchar(20) NOT NULL DEFAULT 'backlog',
  `row_index` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`token_id`),
  KEY `player_id` (`player_id`),
  KEY `color` (`color`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Таблица для хранения жетонов проектов
-- Жетоны проектов закрепляются за планшетом проектов
-- В ходе игры они будут добавляться игрокам
CREATE TABLE IF NOT EXISTS `project_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `color` varchar(20) NOT NULL,
  `shape` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` varchar(20) NOT NULL,
  `effect` varchar(50) NOT NULL,
  `effect_description` text,
  `victory_points` int(11) NOT NULL DEFAULT 0,
  `player_count` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `board_position` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `number` (`number`),
  KEY `color` (`color`),
  KEY `shape` (`shape`),
  KEY `player_count` (`player_count`),
  KEY `board_position` (`board_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Таблица для связи игроков с жетонами проектов
-- Хранит информацию о том, какие жетоны проектов принадлежат игрокам
CREATE TABLE IF NOT EXISTS `player_project_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `token_id` int(10) unsigned NOT NULL,
  `location` varchar(50) NOT NULL DEFAULT 'board',
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `token_id` (`token_id`),
  KEY `location` (`location`),
  CONSTRAINT `fk_player_project_token_token` FOREIGN KEY (`token_id`) REFERENCES `project_token` (`token_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Таблица для хранения игровых данных игрока
-- Хранит позиции на треках, жетон навыка, бонусы IT проектов, цели игры и прогресс трека задач
CREATE TABLE IF NOT EXISTS `player_game_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `income_track` tinyint(3) unsigned DEFAULT 1 COMMENT 'Позиция на треке дохода (income-track, energy)',
  `badgers` int(11) DEFAULT 0 COMMENT 'Количество баджерсов',
  `back_office_col1` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке бэк-офиса колонка 1',
  `back_office_col2` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке бэк-офиса колонка 2',
  `back_office_col3` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке бэк-офиса колонка 3',
  `tech_dev_col1` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке технического развития колонка 1',
  `tech_dev_col2` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке технического развития колонка 2',
  `tech_dev_col3` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке технического развития колонка 3',
  `tech_dev_col4` tinyint(3) unsigned DEFAULT NULL COMMENT 'Позиция на треке технического развития колонка 4',
  `skill_token` varchar(50) DEFAULT NULL COMMENT 'Жетон навыка',
  `sprint_column_tasks_progress` tinyint(3) unsigned DEFAULT NULL COMMENT 'Прогресс улучшения трека задач',
  `sprint_track_backlog` text DEFAULT NULL COMMENT 'Трек спринта: бэклог (JSON массив жетонов задач)',
  `sprint_track_in_progress` text DEFAULT NULL COMMENT 'Трек спринта: в работе (JSON массив жетонов задач)',
  `sprint_track_testing` text DEFAULT NULL COMMENT 'Трек спринта: тестирование (JSON массив жетонов задач)',
  `sprint_track_completed` text DEFAULT NULL COMMENT 'Трек спринта: готово (JSON массив жетонов задач)',
  `task_tokens` text DEFAULT NULL COMMENT 'Все жетоны задач игрока (JSON массив)',
  `project_tokens` text DEFAULT NULL COMMENT 'Все жетоны проектов игрока (JSON массив)',
  `specialist_hand` text DEFAULT NULL COMMENT 'Карты специалистов на руке для выбора (JSON массив ID)',
  `player_specialists` text DEFAULT NULL COMMENT 'Карты сотрудников подтвержденные (JSON массив ID)',
  `it_project_bonuses` text DEFAULT NULL COMMENT 'Бонусы IT проектов (JSON массив)',
  `game_goals` text DEFAULT NULL COMMENT 'Цели игры (JSON массив)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_id` (`player_id`),
  KEY `player_id_idx` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

