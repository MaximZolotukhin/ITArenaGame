<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные жетонов проектов.
 *
 * Жетоны проектов закрепляются за планшетом проектов.
 * В ходе игры они будут добавляться игрокам.
 */
class ProjectTokensData
{
    /**
     * Кэш для жетонов проектов
     * 
     * @var array<int, array<string, mixed>>|null
     */
    private static ?array $tokensCache = null;

    /**
     * Возвращает все жетоны проектов
     * 
     * @return array<int, array<string, mixed>>
     */
    private static function getTokensData(): array
    {
        if (self::$tokensCache !== null) {
            return self::$tokensCache;
        }

        self::$tokensCache = [
        // Пример структуры - нужно будет заполнить реальными данными
        // [
        //     'number' => 1,
        //     'color' => 'red',
        //     'shape' => 'square',
        //     'name' => clienttranslate('Название проекта'),
        //     'price' => '10',
        //     'effect' => 'effect_type',
        //     'effect_description' => clienttranslate('Описание эффекта'),
        //     'victory_points' => 5,
        //     'player_count' => 2,
        //     'image_url' => 'img/project-tokens/project-square/token-1.png',
        // ],
        1 => [
            'number' => 1,
            'color' => 'purple',
            'shape' => 'hex',
            'name' => clienttranslate('ITMS–система'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +2'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/1.png',
        ],
        2 => [
            'number' => 2,
            'color' => 'purple',
            'shape' => 'square',
            'name' => clienttranslate('API интеграция'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/2.png',
        ],
        3 => [
            'number' => 3,
            'color' => 'cyan',
            'shape' => 'circle',
            'name' => clienttranslate('Чат–бот'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/3.png',
        ],
        4 => [
            'number' => 4,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Онлайн сервисы'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 3,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/4.png',
        ],
        5 => [
            'number' => 5,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Контекстная реклама'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/5.png',
        ],
        6 => [
            'number' => 6,
            'color' => 'purple',
            'shape' => 'circle',
            'name' => clienttranslate('SEO продвижение'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 3,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/6.png',
        ],
        7 => [
            'number' => 7,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Интернет–магазин (маркетплейс)'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +3Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/7.png',
        ],
        8 => [
            'number' => 8,
            'color' => 'purple',
            'shape' => 'circle',
            'name' => clienttranslate('Агрегатор'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 3,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/8.png',
        ],
        9 => [
            'number' => 9,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Промо–сайт'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/9.png',
        ],
        10 => [
            'number' => 10,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Сайт–визитка'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/10.png',
        ],
        11 => [
            'number' => 11,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Сайт–каталог'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/11.png',
        ],
        12 => [
            'number' => 12,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Нативная реклама'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/12.png',
        ],
        13 => [
            'number' => 13,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Медийная реклама'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/13.png',
        ],
        14 => [
            'number' => 14,
            'color' => 'purple',
            'shape' => 'square',
            'name' => clienttranslate('Гит'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/14.png',
        ],
        15 => [
            'number' => 15,
            'color' => 'pink',
            'shape' => 'hex',
            'name' => clienttranslate('Блокчейн'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +2'),
            'victory_points' => 4,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/15.png',
        ],
        16 => [
            'number' => 16,
            'color' => 'cyan',
            'shape' => 'circle',
            'name' => clienttranslate('CRM–система'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/16.png',
        ],
        17 => [
            'number' => 17,
            'color' => 'cyan',
            'shape' => 'hex',
            'name' => clienttranslate('Облачные технологии'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/17.png',
        ],
        18 => [
            'number' => 18,
            'color' => 'pink',
            'shape' => 'square',
            'name' => clienttranslate('Доски обьявлений'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек HR +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/18.png',
        ],
        19 => [
            'number' => 19,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Таргетированная реклама'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода +2Б'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/19.png',
        ],
        20 => [
            'number' => 20,
            'color' => 'purple',
            'shape' => 'circle',
            'name' => clienttranslate('Программа лояльности'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/20.png',
        ],
        21 => [
            'number' => 21,
            'color' => 'orange',
            'shape' => 'square',
            'name' => clienttranslate('Справочник'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/21.png',
        ],
        22 => [
            'number' => 22,
            'color' => 'orange',
            'shape' => 'hex',
            'name' => clienttranslate('Машинное обучение'),
            'price' => '4',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +2'),
            'victory_points' => 5,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/22.png',
        ],
        23 => [
            'number' => 23,
            'color' => 'cyan',
            'shape' => 'hex',
            'name' => clienttranslate('Хостинг'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/23.png',
        ],
        24 => [
            'number' => 24,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Система коммуникации'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек HR +2'),
            'victory_points' => 3,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/24.png',
        ],
        25 => [
            'number' => 25,
            'color' => 'orange',
            'shape' => 'square',
            'name' => clienttranslate('Сайт дорвей'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек HR +1'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/25.png',
        ],
        26 => [
            'number' => 26,
            'color' => 'cyan',
            'shape' => 'hex',
            'name' => clienttranslate('Бэкап'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/26.png',
        ],
        27 => [
            'number' => 27,
            'color' => 'purple',
            'shape' => 'square',
            'name' => clienttranslate('Нагрузочное тестирование'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-square/27.png',
        ],
        28 => [
            'number' => 28,
            'color' => 'pink',
            'shape' => 'hex',
            'name' => clienttranslate('Техподдержка'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-hex/28.png',
        ],
        29 => [
            'number' => 29,
            'color' => 'cyan',
            'shape' => 'circle',
            'name' => clienttranslate('Презентация'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 2,
            'image_url' => 'img/project-tokens/project-circle/29.png',
        ],
        30 => [
            'number' => 30,
            'color' => 'orange',
            'shape' => 'hex',
            'name' => clienttranslate('Дашборд'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 1,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-hex/30.png',
        ],
        31 => [
            'number' => 31,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Квиз'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-circle/31.png',
        ],
        32 => [
            'number' => 32,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Тизерная реклама'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-circle/32.png',
        ],
        33 => [
            'number' => 33,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Реклама в соцсетях'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-circle/33.png',
        ],
        34 => [
            'number' => 34,
            'color' => 'purple',
            'shape' => 'hex',
            'name' => clienttranslate('PMBOK стандарты'),
            'price' => '4',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +2'),
            'victory_points' => 5,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-hex/34.png',
        ],
        35 => [
            'number' => 35,
            'color' => 'purple',
            'shape' => 'circle',
            'name' => clienttranslate('Распознование речи'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-circle/35.png',
        ],
        36 => [
            'number' => 36,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Таск–менеджер'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 3,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-square/36.png',
        ],
        37 => [
            'number' => 37,
            'color' => 'cyan',
            'shape' => 'hex',
            'name' => clienttranslate('Scrum методология'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +2'),
            'victory_points' => 3,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-hex/37.png',
        ],
        38 => [
            'number' => 38,
            'color' => 'orange',
            'shape' => 'square',
            'name' => clienttranslate('Сайт корпоративный'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек HR +1'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-square/38.png',
        ],
        39 => [
            'number' => 39,
            'color' => 'pink',
            'shape' => 'square',
            'name' => clienttranslate('Мессенджеры'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек HR +1'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-square/39.png',
        ],
        40 => [
            'number' => 40,
            'color' => 'purple',
            'shape' => 'hex',
            'name' => clienttranslate('CMS система'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 3,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-hex/40.png',
        ],
        41 => [
            'number' => 41,
            'color' => 'orange',
            'shape' => 'hex',
            'name' => clienttranslate('Сайт саттелит'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-hex/41.png',
        ],
        42 => [
            'number' => 42,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Agile методология'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 3,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-square/42.png',
        ],
        43 => [
            'number' => 43,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Лидогенерация'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 3,
            'image_url' => 'img/project-tokens/project-circle/43.png',
        ],
        44 => [
            'number' => 44,
            'color' => 'purple',
            'shape' => 'hex',
            'name' => clienttranslate('Нейронная сеть'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +3'),
            'victory_points' => 3,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-hex/44.png',
        ],
        45 => [
            'number' => 45,
            'color' => 'purple',
            'shape' => 'circle',
            'name' => clienttranslate('Бигдата'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/45.png',
        ],
        46 => [
            'number' => 46,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Мобильное приложение'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +2Б'),
            'victory_points' => 2,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/46.png',
        ],
        47 => [
            'number' => 47,
            'color' => 'orange',
            'shape' => 'circle',
            'name' => clienttranslate('Лендинг'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/47.png',
        ],
        48 => [
            'number' => 48,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Дополненная реальность'),
            'price' => '4',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +2'),
            'victory_points' => 5,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-square/48.png',
        ],
        49 => [
            'number' => 49,
            'color' => 'cyan',
            'shape' => 'square',
            'name' => clienttranslate('Аутсорсинг'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 1,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-square/49.png',
        ],
        50 => [
            'number' => 50,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Email маркетинг'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 3,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/50.png',
        ],
        51 => [
            'number' => 51,
            'color' => 'pink',
            'shape' => 'hex',
            'name' => clienttranslate('Push–уведомления'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 1,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-hex/51.png',
        ],
        52 => [
            'number' => 52,
            'color' => 'cyan',
            'shape' => 'hex',
            'name' => clienttranslate('Документация'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 2,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-hex/52.png',
        ],
        53 => [
            'number' => 53,
            'color' => 'orange',
            'shape' => 'hex',
            'name' => clienttranslate('Интернет портал'),
            'price' => '3',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек спринта +1'),
            'victory_points' => 3,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-hex/53.png',
        ],
        54 => [
            'number' => 54,
            'color' => 'pink',
            'shape' => 'square',
            'name' => clienttranslate('Онлайн чат'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 1,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-square/54.png',
        ],
        55 => [
            'number' => 55,
            'color' => 'purple',
            'shape' => 'square',
            'name' => clienttranslate('Автоматизация логов'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек задач +1'),
            'victory_points' => 1,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-square/55.png',
        ],
        56 => [
            'number' => 56,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('Холодные звонки'),
            'price' => '1',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 1,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/56.png',
        ],
        57 => [
            'number' => 57,
            'color' => 'pink',
            'shape' => 'circle',
            'name' => clienttranslate('SMM продвижение'),
            'price' => '2',
            'effect' => 'effect_type',
            'effect_description' => clienttranslate('Трек дохода  +1Б'),
            'victory_points' => 2,
            'player_count' => 4,
            'image_url' => 'img/project-tokens/project-circle/57.png',
        ],
        ];

        return self::$tokensCache;
    }

    /**
     * Возвращает все жетоны проектов.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAllTokens(): array
    {
        return self::getTokensData();
    }

    /**
     * Возвращает жетон проекта по номеру.
     *
     * @param int $number Номер жетона
     * @return array<string, mixed>|null
     */
    public static function getTokenByNumber(int $number): ?array
    {
        foreach (self::getTokensData() as $token) {
            if ($token['number'] === $number) {
                return $token;
            }
        }
        return null;
    }

    /**
     * Возвращает жетоны проектов по цвету.
     *
     * @param string $color Цвет жетона (red, blue, green)
     * @return array<int, array<string, mixed>>
     */
    public static function getTokensByColor(string $color): array
    {
        $result = [];
        foreach (self::getTokensData() as $token) {
            if ($token['color'] === $color) {
                $result[] = $token;
            }
        }
        return $result;
    }

    /**
     * Возвращает жетоны проектов по форме.
     *
     * @param string $shape Форма жетона (square, circle, hex)
     * @return array<int, array<string, mixed>>
     */
    public static function getTokensByShape(string $shape): array
    {
        $result = [];
        foreach (self::getTokensData() as $token) {
            if ($token['shape'] === $shape) {
                $result[] = $token;
            }
        }
        return $result;
    }

    /**
     * Возвращает жетоны проектов для указанного количества игроков.
     *
     * @param int $playerCount Количество игроков
     * @return array<int, array<string, mixed>>
     */
    public static function getTokensByPlayerCount(int $playerCount): array
    {
        $result = [];
        foreach (self::getTokensData() as $token) {
            if ($token['player_count'] === $playerCount) {
                $result[] = $token;
            }
        }
        return $result;
    }

    /**
     * Возвращает список всех доступных цветов.
     *
     * @return array<string>
     */
    public static function getAvailableColors(): array
    {
        $colors = [];
        foreach (self::getTokensData() as $token) {
            $color = $token['color'] ?? '';
            if ($color && !in_array($color, $colors, true)) {
                $colors[] = $color;
            }
        }
        return $colors;
    }

    /**
     * Возвращает список всех доступных форм.
     *
     * @return array<string>
     */
    public static function getAvailableShapes(): array
    {
        return ['square', 'circle', 'hex'];
    }
}

