<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные карт специалистов.
 *
 * Структура полей совпадает с картами лидеров.
 */
class SpecialistsData
{
    private const CARD_COUNT = 110;

    /**
     * Шаблон карточки специалиста для удобства заполнения.
     */
    public static function getCardTemplate(int $id = 0): array
    {
        return [
            'id' => $id,
            'type' => 'specialist',
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
     * Возвращает все карты специалистов.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAllCards(): array
    {
        $cards = [];
        for ($id = 1; $id <= self::CARD_COUNT; $id++) {
            $cards[$id] = self::getCardTemplate($id);
        }

        return $cards;
    }

    /**
     * Возвращает данные конкретной карты специалиста.
     */
    public static function getCard(int $id): ?array
    {
        $cards = self::getAllCards();
        return $cards[$id] ?? null;
    }
}

