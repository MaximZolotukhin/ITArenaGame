<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные карт основателей.
 *
 * Структура полей:
 *  - type: string
 *  - price: string|null
 *  - name: string
 *  - color: string
 *  - speciality: string
 *  - effect: string
 *  - starterOrFinisher: string (char)
 *  - management: string (char)
 *  - firstGame: bool
 *  - additionalSkill: string (char|null)
 *  - victoryPoints: int
 *  - img: string
 */
class FoundersData
{
    /*
    Отделы: sales-department - Отдел продаж
            back-office - Бэк офис
            technical-department - Техотдел
            universal - Универсальный

    Цвета
    #FFFF00 - Желтый
     #0000FF - Синий
     #008000 - Зеленый 
    #800000 -  Красный 
     */

    /**
     * @var array<int, array<string, mixed>>
     */
    private const CARDS = [
        1 => [
            'id' => 1,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Дмитрий', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => ['badger' => '+ 4', 'card' => '+ 3', 'task' => '+ 3', "move_task" => ['move_count' => 3, 'move_color' => 'any']], // Эффект 'task' => '+ 3', 
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Четкий старт. Возьмите 4Б, 3 карты, 3 задачи и передвиньте жетоны задач любых цветов на 3 этапа по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Dmitry.png', // Изображение
        ],
        2 => [
            'id' => 2,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Владимир', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'technical-department', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Технологии Бескодов. В фазу "спринт" улучшите минимальный трек в техотделе на 1 пункт', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vladimir.png', // Изображение
        ],
        3 => [
            'id' => 3,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Нелли', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'universal', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Везунчик. Вместо штрафа за карту события в фазу "Итоги" получите 1Б', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Nellie.png', // Изображение
        ],
        // 4 => [
        //     'id' => 4,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Роман', // Имя основателя
        //     'color' => '#008000', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'sales-department', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Пассионарий. Получите 2Б из банка за каждый выполненный IT проект', // Описание эффекта
        //     'starterOrFinisher' => 'S', // Стартер или финишер
        //     'management' => 'E', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => null, // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Roman.png', // Изображение
        // ],
        // 5 => [
        //     'id' => 5,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Михаил Алистер', // Имя основателя
        //     'color' => '#800000', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'back-office', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Личный бренд. В фазу «итоги» активируйте повторно перк 1 карты.(1 карту только 1 раз). Эту карту нельзя уволить.', // Описание эффекта
        //     'starterOrFinisher' => 'S', // Стартер или финишер
        //     'management' => 'I', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => null, // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Michael_Alistair.png', // Изображение
        // ],
        // 6 => [
        //     'id' => 6,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Эмиль', // Имя основателя
        //     'color' => '#FFFF00', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'universal', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Оптимизатор. Улучшение отделов для вас, бонус трека x2', // Описание эффекта
        //     'starterOrFinisher' => 'F', // Стартер или финишер
        //     'management' => 'P', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => null, // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Emil.png', // Изображение
        // ],
        // 7 => [
        //     'id' => 7,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Вардгес', // Имя основателя
        //     'color' => '#008000', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'sales-department', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Семья это все! Возьмите 7Б из банка и возьмите 7 карт из колоды найма', // Описание эффекта
        //     'starterOrFinisher' => 'S', // Стартер или финишер
        //     'management' => 'A', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => null, // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Vardges.png', // Изображение
        // ],
        // 8 => [
        //     'id' => 8,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Виталий', // Имя основателя
        //     'color' => '#800000', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'back-office', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Комбинатор. Бонус IT проектов для вас приносит 7 ресурса', // Описание эффекта
        //     'starterOrFinisher' => 'F', // Стартер или финишер
        //     'management' => 'A', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => null, // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Vitaly.png', // Изображение
        // ],
        9 => [
            'id' => 9,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Лидия', // Имя основателя
            'color' => '#800000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'back-office', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Отличник. Улучшите на 1 трек дохода в отделе продаж и на 1 трек найма и 1 трек задач в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Lydia.png', // Изображение
        ],
        // 10 => [
        //     'id' => 10,
        //     'type' => 'founder', // Тип карты
        //     'typeName' => 'Основатель', // Тип карты название на русском
        //     'price' => null, // Цена карты
        //     'name' => 'Илья', // Имя основателя
        //     'color' => '#0000FF', // Цвет основателя
        //     'speciality' => 'Основатель', // Специальность основателя
        //     'effect' => null, // Эффект
        //     'department' => 'technical-department', // Отдел
        //     'activationStage' => null, // Этап активации эффекта
        //     'effectDescription' => 'Айтишник. IT проекты стоимостью 3 задачи и более стоят для вас на 1 задачу меньше', // Описание эффекта
        //     'starterOrFinisher' => 'F', // Стартер или финишер
        //     'management' => 'I', // Управление
        //     'firstGame' => false, // Если первая игра то будет доступны только 4 карты
        //     'additionalSkill' => 'E', // Дополнительный навык
        //     'victoryPoints' => 0, // Очки победы
        //     'img' => 'img/founder/Ilya.png', // Изображение
        // ],
        11 => [
            'id' => 11,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Сергей', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'technical-department', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Мастер рекрутинга. Когда нанимаешь 3 карты за 1 ход, то 3я карта найма стоит 1Б', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Sergey.png', // Изображение
        ],
        12 => [
            'id' => 12,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Елена', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'sales-department', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Финансист. Улучшите на 3 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Elena.png', // Изображение
        ],
    ];

    /**
     * Шаблон карточки (используется для заполнения данных).
     */
    public static function getCardTemplate(int $id = 0): array
    {
        return [
            'id' => $id,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => null,
            'department' => 'universal',
            'activationStage' => null,
            'effectDescription' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => null,
            'victoryPoints' => 0,
            'img' => 'img/founder/Dmitry.png',
        ];
    }

    /**
     * Возвращает все карты лидеров.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAllCards(): array
    {
        return self::CARDS;
    }

    /**
     * Возвращает данные конкретной карты-лидера.
     */
    public static function getCard(int $id): ?array
    {
        return self::CARDS[$id] ?? null;
    }
}
