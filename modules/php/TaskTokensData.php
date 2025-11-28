<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные жетонов задач.
 *
 * Жетоны задач имеют только один параметр - цвет.
 * Всего 108 жетонов: по 27 каждого цвета.
 * Цвета: голубой (cyan), розовый (pink), оранжевый (orange), фиолетовый (purple)
 */
class TaskTokensData
{
    /**
     * Цвета жетонов задач
     */
    private const COLORS = [
        'cyan' => [
            'id' => 'cyan',
            'name' => clienttranslate('Голубой'),
            'color_code' => '#00CED1',
            'quantity' => 27,
            'image_url' => 'img/task-tokens/cyan.png',
        ],
        'pink' => [
            'id' => 'pink',
            'name' => clienttranslate('Розовый'),
            'color_code' => '#FF69B4',
            'quantity' => 27,
            'image_url' => 'img/task-tokens/pink.png',
        ],
        'orange' => [
            'id' => 'orange',
            'name' => clienttranslate('Оранжевый'),
            'color_code' => '#FF8C00',
            'quantity' => 27,
            'image_url' => 'img/task-tokens/orange.png',
        ],
        'purple' => [
            'id' => 'purple',
            'name' => clienttranslate('Фиолетовый'),
            'color_code' => '#9370DB',
            'quantity' => 27,
            'image_url' => 'img/task-tokens/purple.png',
        ],
    ];

    /**
     * Возвращает все цвета жетонов задач.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAllColors(): array
    {
        return self::COLORS;
    }

    /**
     * Возвращает данные по конкретному цвету.
     *
     * @param string $colorId ID цвета (cyan, pink, orange, purple)
     * @return array<string, mixed>|null
     */
    public static function getColor(string $colorId): ?array
    {
        return self::COLORS[$colorId] ?? null;
    }

    /**
     * Возвращает список всех ID цветов.
     *
     * @return array<string>
     */
    public static function getColorIds(): array
    {
        return array_keys(self::COLORS);
    }

    /**
     * Возвращает общее количество жетонов задач.
     *
     * @return int
     */
    public static function getTotalQuantity(): int
    {
        $total = 0;
        foreach (self::COLORS as $color) {
            $total += $color['quantity'];
        }
        return $total;
    }

    /**
     * Возвращает количество жетонов по цвету.
     *
     * @param string $colorId ID цвета
     * @return int
     */
    public static function getQuantityByColor(string $colorId): int
    {
        $color = self::getColor($colorId);
        return $color ? $color['quantity'] : 0;
    }
}

