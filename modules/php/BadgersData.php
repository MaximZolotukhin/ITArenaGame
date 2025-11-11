<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные о валюте "Баджерс"
 *
 * Хранит информацию о всех номиналах монет и их стартовом количестве.
 */
class BadgersData
{
    /**
     * Возвращает список всех номиналов монет и сопутствующие данные.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getDenominations(): array
    {
        return [
            1 => [
                'value' => 1,
                'name' => clienttranslate('Монета 1 баджерс'),
                'short_label' => clienttranslate('Баджерс'),
                'quantity' => 15,
                'image_url' => 'img/money/1.png',
            ],
            2 => [
                'value' => 2,
                'name' => clienttranslate('Монета 2 баджерса'),
                'short_label' => clienttranslate('баджерса'),
                'quantity' => 7,
                'image_url' => 'img/money/2.png',
            ],
            3 => [
                'value' => 3,
                'name' => clienttranslate('Монета 3 баджерса'),
                'short_label' => clienttranslate('баджерса'),
                'quantity' => 5,
                'image_url' => 'img/money/3.png',
            ],
            5 => [
                'value' => 5,
                'name' => clienttranslate('Монета 5 баджерсов'),
                'short_label' => clienttranslate('баджерсов'),
                'quantity' => 5,
                'image_url' => 'img/money/5.png',
            ],
            10 => [
                'value' => 10,
                'name' => clienttranslate('Монета 10 баджерсов'),
                'short_label' => clienttranslate('баджерсов'),
                'quantity' => 5,
                'image_url' => 'img/money/10.png',
            ],
        ];
    }

    /**
     * Возвращает данные по конкретному номиналу.
     */
    public static function getDenomination(int $value): ?array
    {
        $denominations = self::getDenominations();
        return $denominations[$value] ?? null;
    }

    /**
     * Возвращает массив стартовых количеств монет по номиналу.
     *
     * @return array<int, int>
     */
    public static function getInitialSupply(): array
    {
        $result = [];
        foreach (self::getDenominations() as $value => $coin) {
            $result[$value] = (int) ($coin['quantity'] ?? 0);
        }

        return $result;
    }
}

