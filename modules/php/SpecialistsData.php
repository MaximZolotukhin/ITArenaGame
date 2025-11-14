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

    /*
    Отделы: 
        sales-department - Отдел продаж
        back-office - Бэк офис
        technical-department - Техотдел
        universal - Универсальный

    Цвета
    #FFFF00 - Желтый
    #0000FF - Синий
    #008000 - Зеленый 
    #800000 -  Красный 
    */
    private const CARD_COUNT = [
        1 => [
            'id' => 1,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–программист', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 розовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/1.png', // Изображение
        ],
        2 => [
            'id' => 2,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–программист', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 розовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/1.png', // Изображение
        ],
    ];

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

