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
     * Все жетоны проектов
     * 
     * @var array<int, array<string, mixed>>
     */
    private const TOKENS = [
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
    ];

    /**
     * Возвращает все жетоны проектов.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAllTokens(): array
    {
        return self::TOKENS;
    }

    /**
     * Возвращает жетон проекта по номеру.
     *
     * @param int $number Номер жетона
     * @return array<string, mixed>|null
     */
    public static function getTokenByNumber(int $number): ?array
    {
        foreach (self::TOKENS as $token) {
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
        foreach (self::TOKENS as $token) {
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
        foreach (self::TOKENS as $token) {
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
        foreach (self::TOKENS as $token) {
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
        foreach (self::TOKENS as $token) {
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

