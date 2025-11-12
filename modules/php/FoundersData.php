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
            'color' => '#ffd700', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => '', // Эффект
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
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        3 => [
            'id' => 3,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        4 => [
            'id' => 4,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        5 => [
            'id' => 5,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        6 => [
            'id' => 6,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        7 => [
            'id' => 7,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        8 => [
            'id' => 8,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        9 => [
            'id' => 9,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        10 => [
            'id' => 10,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        11 => [
            'id' => 11,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
        ],
        12 => [
            'id' => 12,
            'type' => 'founder',
            'price' => null,
            'name' => '',
            'color' => '',
            'speciality' => '',
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
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
            'effect' => '',
            'starterOrFinisher' => '',
            'management' => '',
            'firstGame' => false,
            'additionalSkill' => '',
            'victoryPoints' => 0,
            'img' => '',
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

