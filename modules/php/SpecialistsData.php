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
            'name' => 'Камилла', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'SEO–специалист', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 оранжевый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/2.png', // Изображение
        ],
        3 => [
            'id' => 3,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Медина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'SММ–специалист', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на трек спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/3.png', // Изображение
        ],
        4 => [
            'id' => 4,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Алексей', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Frontend–разработчик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 розовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/4.png', // Изображение
        ],
        // TODO: посомтреть где и почему не залили фото картчоки дял алексея если чо его img 4.png пока оставим чтоб не было путаницы
        5 => [
            'id' => 5,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Степан', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Link–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/5.png', // Изображение
        ],
        6 => [
            'id' => 6,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Татьяна', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Контент–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 голубой трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/6.png', // Изображение
        ],
        7 => [
            'id' => 7,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Леонид', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–копирайтер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте голубые жетоны задач на 4 этапа по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/7.png', // Изображение
        ],
        8 => [
            'id' => 8,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Святослав', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Backend–разработчик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек задач в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/8.png', // Изображение
        ],
        9 => [
            'id' => 9,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Артем', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Программист 1С', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте фиолетовые жетоны задач на 4 этапа по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/9.png', // Изображение
        ],
        10 => [
            'id' => 10,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Игорь', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'QA–инженер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/10.png', // Изображение
        ],
        11 => [
            'id' => 11,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Виктория', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Администратор сайта', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте розовые жетоны задач на 4 этапа по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/11.png', // Изображение
        ],
        12 => [
            'id' => 12,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Лилия', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Тестировщик ПО', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек задач в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/12.png', // Изображение
        ],
        13 => [
            'id' => 13,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Тимофей', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Системный администратор', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/13.png', // Изображение
        ],
        14 => [
            'id' => 14,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ильмир', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Программист JavaScript', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 розовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/14.png', // Изображение
        ],
        15 => [
            'id' => 15,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Петр', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Разработчик баз данных', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 фиолетовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/15.png', // Изображение
        ],
        16 => [
            'id' => 16,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Игнат', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Программист Python', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 фиолетовый трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/16.png', // Изображение
        ],
        17 => [
            'id' => 17,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Екатерина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Модератор', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Улучшите на 1 голубой трек в техотделе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/17.png', // Изображение
        ],
        18 => [
            'id' => 18,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Александр', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Риск–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите 3 карты из колоды найма', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/18.png', // Изображение
        ],
        19 => [
            'id' => 19,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Егор', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер по развитию', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите 3 карты из колоды найма', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-officet/19.png', // Изображение
        ],
        20 => [
            'id' => 20,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Глеб', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Тайм–брокер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/20.png', // Изображение
        ],
        21 => [
            'id' => 21,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Любовь', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Офис–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/21.png', // Изображение
        ],
        22 => [
            'id' => 22,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Илья', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бизнес–консультант', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Передвиньте оранжевые жетоны задач на 4 этапа по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/22.png', // Изображение
        ],
        23 => [
            'id' => 23,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Тихон', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–аналитик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/23.png', // Изображение
        ],
        24 => [
            'id' => 24,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Айлин', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Специалист по бизнес процессам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/24.png', // Изображение
        ],
        25 => [
            'id' => 25,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Мирослава', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Event–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/25.png', // Изображение
        ],
        26 => [
            'id' => 26,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Савва', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Специалист по планированию', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/26.png', // Изображение
        ],
        27 => [
            'id' => 27,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ксения', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/27.png', // Изображение
        ],
        28 => [
            'id' => 28,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ирина', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер по развитию', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите 3 карты из колоды найма', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/28.png', // Изображение
        ],
        29 => [
            'id' => 29,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Вадим', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Хед–хантер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/29.png', // Изображение
        ],
        30 => [
            'id' => 30,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Тимур', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 2Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/30.png', // Изображение
        ],
        31 => [
            'id' => 31,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Мирон', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/31.png', // Изображение
        ],
        32 => [
            'id' => 32,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дарья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 2Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/32.png', // Изображение
        ],
        33 => [
            'id' => 33,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Макар', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/33.png', // Изображение
        ],
        34 => [
            'id' => 34,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Майя', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 2Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/34.png', // Изображение
        ],
        35 => [
            'id' => 35,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Пьер', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/35.png', // Изображение
        ],
        36 => [
            'id' => 36,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Виктор', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 2Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/36.png', // Изображение
        ],
        37 => [
            'id' => 37,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Влада', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/37.png', // Изображение
        ],
        38 => [
            'id' => 38,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Лейла', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 5Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/38.png', // Изображение
        ],
        39 => [
            'id' => 39,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Константин', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/39.png', // Изображение
        ],
        40 => [
            'id' => 40,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Мария', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 5Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/40.png', // Изображение
        ],
        41 => [
            'id' => 41,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Роман', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/41.png', // Изображение
        ],
        42 => [
            'id' => 42,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Софья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/42.png', // Изображение
        ],
        43 => [
            'id' => 43,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Олеся', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'CRM–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любые 2 задачи и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/43.png', // Изображение
        ],
        44 => [
            'id' => 44,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Евгения', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–продюсер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Возьмите любые 2 задачи и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/44.png', // Изображение
        ],
        45 => [
            'id' => 45,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Энтони', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'BI–разработчик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте фиолетовые жетоны задач на 5 этапов по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/45.png', // Изображение
        ],
        46 => [
            'id' => 46,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Арсений', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Frontend–разработчик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте розовые жетоны задач на 5 этапов по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/46.png', // Изображение
        ],
        47 => [
            'id' => 47,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Марк', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–аналитик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'technical-department', // Отдел
            'effectDescription' => 'Передвиньте оранжевые жетоны задач на 5 этапов по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/47.png', // Изображение
        ],
        48 => [
            'id' => 48,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Артур', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Антикризисный управляющий', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите любые 2 задачи и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/48.png', // Изображение
        ],
        49 => [
            'id' => 49,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Николь', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Аккаунт–менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Возьмите любые 2 задачи и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/49.png', // Изображение
        ],
        50 => [
            'id' => 50,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Всеволод', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Инновационный менеджер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'back-office', // Отдел
            'effectDescription' => 'Передвиньте голубые жетоны задач на 5 этапов по треку спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/50.png', // Изображение
        ],
        51 => [
            'id' => 51,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Михаил', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Брокер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 3Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/51.png', // Изображение
        ],
        52 => [
            'id' => 52,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Есения', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Оператор чата', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 3Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/52.png', // Изображение
        ],
        53 => [
            'id' => 53,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Арина', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Маркетплейсер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 3Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/53.png', // Изображение
        ],
        54 => [
            'id' => 54,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Давид', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Оператор коллцентра', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 3Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/54.png', // Изображение
        ],
        55 => [
            'id' => 55,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Матвей', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Продавец–консультант', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'sales-department', // Отдел
            'effectDescription' => 'Возьмите 3Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/55.png', // Изображение
        ],
        56 => [
            'id' => 56,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Анна', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'universal', // Отдел
            'effectDescription' => 'Возьмите 4 карты из колоды найма', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/56.png', // Изображение
        ],
        57 => [
            'id' => 57,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Алия', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Оператор техподдержки', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'universal', // Отдел
            'effectDescription' => 'Улучшите на 1 трек дохода в отделе продаж', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/57.png', // Изображение
        ],
        58 => [
            'id' => 58,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Вячеслав', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–специалист', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'universal', // Отдел
            'effectDescription' => 'Улучшите на 1 трек задач в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/58.png', // Изображение
        ],
        59 => [
            'id' => 59,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Юлия', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–аналитик', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'universal', // Отдел
            'effectDescription' => 'Улучшите на 1 трек найма в бэк–офисе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/59.png', // Изображение
        ],
        60 => [
            'id' => 60,
            'type' => 'specialist', // Тип карты
            'typeName' => 'specialist', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Филипп', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–тренер', // Специальность специалиста
            'effect' => '', // Эффект
            'department' => 'universal', // Отдел
            'effectDescription' => 'Возьмите 6Б из банка', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/60.png', // Изображение
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

