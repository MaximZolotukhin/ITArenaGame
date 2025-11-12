<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные карт лидеров.
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
 *  - additionalSkill: string (char)
 *  - victoryPoints: int
 *  - img: string
 */
class LeaderCardsData
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private const CARDS = [
        1 => [
            'id' => 1,
            'type' => 'leader',
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
        2 => [
            'id' => 2,
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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
            'type' => 'leader',
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

