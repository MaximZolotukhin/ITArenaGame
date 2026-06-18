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
    // v1 - ensure deploy: line 202 effect fixed

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
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ильгиз', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Mobile–разработчик ', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт розовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/1.png', // Изображение
        ],
        2 => [
            'id' => 2,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Камилла', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'SEO–специалист', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт оранжевый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/2.png', // Изображение
        ],
        3 => [
            'id' => 3,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Иван', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'SММ–специалист', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на трек спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/3.png', // Изображение
        ],
        4 => [
            'id' => 4,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Святослав', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Frontend–разработчик', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт розовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/4.png', // Изображение
        ],
        5 => [
            'id' => 5,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Степан', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Link–менеджер', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/5.png', // Изображение
        ],
        6 => [
            'id' => 6,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Кристина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Контент–менеджер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/6.png', // Изображение
        ],
        7 => [
            'id' => 7,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–аналитик ', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 3, 'move_color' => 'cyan']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте голубой(ые) жетон(ы) задач на 3 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/7.png', // Изображение
        ],
        8 => [
            'id' => 8,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Леонид', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Backend–разработчик', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/8.png', // Изображение
        ],
        9 => [
            'id' => 9,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Артем', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => '1С–разработчик ', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 3, 'move_color' => 'purple']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте фиолетовый(ые) жетон(ы) задач на 3 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/9.png', // Изображение
        ],
        10 => [
            'id' => 10,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Игорь', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'QA–инженер', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/10.png', // Изображение
        ],
        11 => [
            'id' => 11,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Виктория', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Администратор сайта', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 3, 'move_color' => 'pink']], // Эффект — розовые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте розовый(ые) жетон(ы) задач на 3 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/11.png', // Изображение
        ],
        12 => [
            'id' => 12,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Лилия', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Тестировщик ПО', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/12.png', // Изображение
        ],
        13 => [
            'id' => 13,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Тимофей', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Системный администратор', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/13.png', // Изображение
        ],
        14 => [
            'id' => 14,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'JavaScript–разработчик ', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт розовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/14.png', // Изображение
        ],
        15 => [
            'id' => 15,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Петр', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Разработчик баз данных', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/15.png', // Изображение
        ],
        16 => [
            'id' => 16,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Игнат', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Python–разработчик ', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/16.png', // Изображение
        ],
        17 => [
            'id' => 17,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Екатерина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Модератор', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/17.png', // Изображение
        ],
        18 => [
            'id' => 18,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Артур', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Риск–менеджер', // Специальность специалиста
            'effect' => ['card' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2 карты из колоды найма', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/18.png', // Изображение
        ],
        19 => [
            'id' => 19,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Адам', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер по развитию', // Специальность специалиста
            'effect' => ['card' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2 карты из колоды найма', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/19.png', // Изображение
        ],
        20 => [
            'id' => 20,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Валерия', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–менеджер', // Специальность специалиста
            'effect' => ['card' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2 карты из колоды найма', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/20.png', // Изображение
        ],
        21 => [
            'id' => 21,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Глеб', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Тайм–брокер', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/21.png', // Изображение
        ],
        22 => [
            'id' => 22,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Любовь', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Офис–менеджер', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/22.png', // Изображение
        ],
        23 => [
            'id' => 23,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Савелий', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бизнес–консультант', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 3, 'move_color' => 'orange']], // Эффект — розовые жетоны, до 4 шагов, // Эффект
            'effectDescription' => 'Передвиньте оранжевый(ые) жетон(ы) задач на 3 этапа на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/23.png', // Изображение
        ],
        24 => [
            'id' => 24,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Тихон', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–аналитик', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/24.png', // Изображение
        ],
        25 => [
            'id' => 25,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Айлин', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Специалист по бизнес процессам', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/25.png', // Изображение
        ],
        26 => [
            'id' => 26,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Мирослава', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Event–менеджер', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/26.png', // Изображение
        ],
        27 => [
            'id' => 27,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Савва', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Специалист по планированию', // Специальность специалиста
            'effect' => ['task' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите любую 1 задачу и отправьте в "Бэклог" на треке спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/27.png', // Изображение
        ],
        28 => [
            'id' => 28,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ксения', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–менеджер', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/28.png', // Изображение
        ],
        29 => [
            'id' => 29,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Ирина', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бизнес–консультант', // Специальность специалиста
            'effect' => ['card' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2 карты из колоды найма', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/29.png', // Изображение
        ],
        30 => [
            'id' => 30,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Вадим', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Хед–хантер', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/30.png', // Изображение
        ],
        31 => [
            'id' => 31,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Влада', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Архитектор туннелей продаж', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт розовый трек в техотделе', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/31.png', // Изображение
        ],
        32 => [
            'id' => 32,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Аркадий', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/32.png', // Изображение
        ],
        33 => [
            'id' => 33,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/33.png', // Изображение
        ],
        34 => [
            'id' => 34,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Дарья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/34.png', // Изображение
        ],
        35 => [
            'id' => 35,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Макар', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/35.png', // Изображение
        ],
        36 => [
            'id' => 36,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Яна', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/36.png', // Изображение
        ],
        37 => [
            'id' => 37,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Пьер', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/37.png', // Изображение
        ],
        38 => [
            'id' => 38,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Виктор', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/38.png', // Изображение
        ],
        39 => [
            'id' => 39,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Олеся', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/39.png', // Изображение
        ],
        40 => [
            'id' => 40,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Владимир', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/40.png', // Изображение
        ],
        41 => [
            'id' => 41,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Михаил', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/41.png', // Изображение
        ],
        42 => [
            'id' => 42,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Мария', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/42.png', // Изображение
        ],
        43 => [
            'id' => 43,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Андрей', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/43.png', // Изображение
        ],
        44 => [
            'id' => 44,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Софья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/44.png', // Изображение
        ],
        45 => [
            'id' => 45,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Александр', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/45.png', // Изображение
        ],
        46 => [
            'id' => 46,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Илья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/46.png', // Изображение
        ],
        47 => [
            'id' => 47,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Герман', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 2'], // Эффект
            'effectDescription' => 'Возьмите 2Б из банка ', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/47.png', // Изображение
        ],
        48 => [
            'id' => 48,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Белла', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Улучшите на 1 пункт любой трек в офисе. А все остальные игроки получают ресурс этого трека', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/48.png', // Изображение
        ],
        49 => [
            'id' => 49,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 1, // Цена карты
            'name' => 'Роберт', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–специалист', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Улучшите на 1 пункт любой трек в офисе. А все остальные игроки получают ресурс этого трека', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/49.png', // Изображение
        ],
        50 => [
            'id' => 50,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Алина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'CRM–менеджер', // Специальность специалиста
            'effect' => ['task_gift_player' => ['amount' => 3, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите любые 3 задачи и отправьте в "Бэклог" на панели спринта. Выберите другого игрока он тоже получет 1 задачу', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/50.png', // Изображение
        ],
        51 => [
            'id' => 51,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Лея', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–продюсер', // Специальность специалиста
            'effect' => ['task_gift_player' => ['amount' => 3, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите любые 3 задачи и отправьте в "Бэклог" на панели спринта. Выберите другого игрока он тоже получет 1 задачу', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/51.png', // Изображение
        ],
        52 => [
            'id' => 52,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Розалин', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'BI–разработчик', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 4, 'move_color' => 'purple']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте фиолетовый(ые) жетон(ы) задач на 4 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/52.png', // Изображение
        ],
        53 => [
            'id' => 53,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Андрей', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Frontend–разработчик', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 4, 'move_color' => 'pink']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте розовый(ые) жетон(ы) задач на 4 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/53.png', // Изображение
        ],
        54 => [
            'id' => 54,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Марк', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–аналитик ', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 4, 'move_color' => 'orange']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте оранжевый(ые) жетон(ы) задач на 4 этапа на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/54.png', // Изображение
        ],
        55 => [
            'id' => 55,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Аделина', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Антикризисный управляющий', // Специальность специалиста
            'effect' => ['task_gift_player' => ['amount' => 3, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите любые 3 задачи и отправьте в "Бэклог" на панели спринта. Выберите другого игрока он тоже получет 1 задачу', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/55.png', // Изображение
        ],
        56 => [
            'id' => 56,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Николь', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Аккаунт–менеджер', // Специальность специалиста
            'effect' => ['task_gift_player' => ['amount' => 3, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите любые 3 задачи и отправьте в "Бэклог" на панели спринта. Выберите другого игрока он тоже получет 1 задачу', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/56.png', // Изображение
        ],
        57 => [
            'id' => 57,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Арина', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер по развитию', // Специальность специалиста
            'effect' => ['task_gift_player' => ['amount' => 3, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите любые 3 задачи и отправьте в "Бэклог" на панели спринта. Выберите другого игрока он тоже получет 1 задачу', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/57.png', // Изображение
        ],
        58 => [
            'id' => 58,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Ева', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Специалист по планированию', // Специальность специалиста
            'effect' => ['task' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите любые 4 задачи и отправьте в "Бэклог" на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/58.png', // Изображение
        ],
        59 => [
            'id' => 59,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Майя', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'HR–менеджер', // Специальность специалиста
            'effect' => ['card_gift_player' => ['amount' => 4, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите 4 карты из колоды найма. Выберите соперника он тоже получает 1 карту', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/59.png', // Изображение
        ],
        60 => [
            'id' => 60,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Малика', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Хед–хантер', // Специальность специалиста
            'effect' => ['card_gift_player' => ['amount' => 4, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите 4 карты из колоды найма. Выберите соперника он тоже получает 1 карту', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/60.png', // Изображение
        ],
        61 => [
            'id' => 61,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Егор', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Инновационный менеджер', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 4, 'move_color' => 'cyan']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте голубой(ые) жетон(ы) задач на 4 этапа на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/back-office/61.png', // Изображение
        ],
        62 => [
            'id' => 62,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Михаил', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Брокер', // Специальность специалиста
            'effect' => ['badger' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите 4Б из банка. Выберите другого игрока, он тоже получает 1Б.', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/62.png', // Изображение
        ],
        63 => [
            'id' => 63,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Есения', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Оператор чата', // Специальность специалиста
            'effect' => ['badger' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите 4Б из банка. Выберите другого игрока, он тоже получает 1Б.', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/63.png', // Изображение
        ],
        64 => [
            'id' => 64,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Анастасия', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Маркетплейсер', // Специальность специалиста
            'effect' => ['badger' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите 4Б из банка. Выберите другого игрока, он тоже получает 1Б.', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/64.png', // Изображение
        ],
        65 => [
            'id' => 65,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Давид', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите 4Б из банка. Выберите другого игрока, он тоже получает 1Б.', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/65.png', // Изображение
        ],
        66 => [
            'id' => 66,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Матвей', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Менеджер по продажам', // Специальность специалиста
            'effect' => ['badger' => '+ 4'], // Эффект
            'effectDescription' => 'Возьмите 4Б из банка. Выберите другого игрока, он тоже получает 1Б.', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/66.png', // Изображение
        ],
        67 => [
            'id' => 67,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Злата', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Контент-мейкер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт оранжевый трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/67.png', // Изображение
        ],
        68 => [
            'id' => 68,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Зинаида', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–специалист', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/68.png', // Изображение
        ],
        69 => [
            'id' => 69,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Анна', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => ['card_gift_player' => ['amount' => 4, 'gift_amount' => 1]], // Эффект
            'effectDescription' => 'Возьмите 4 карты из колоды найма. Выберите другого игрока, он тоже получает 1 карту', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/69.png', // Изображение
        ],
        70 => [
            'id' => 70,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Арина', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Оператор техподдержки', // Специальность специалиста
            'effect' => ['choose_office_track' => ['amount' => 1]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт любой трек в офисе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/70.png', // Изображение
        ],
        71 => [
            'id' => 71,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Станислав', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–специалист', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/71.png', // Изображение
        ],
        72 => [
            'id' => 72,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Юлия', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–аналитик', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/72.png', // Изображение
        ],
        73 => [
            'id' => 73,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Филипп', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–аналитик', // Специальность специалиста
            'effect' => ['badger' => '+ 7'], // Эффект
            'effectDescription' => 'Возьмите 7Б из банка. Все игроки тоже получают 2Б', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/73.png', // Изображение
        ],
        74 => [
            'id' => 74,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Амалия', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/74.png', // Изображение
        ],
        75 => [
            'id' => 75,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Юрий', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => [
                'updateTrack' => [
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ]
            ], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек найма в бэк–офисе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/75.png', // Изображение
        ],
        76 => [
            'id' => 76,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Богдан', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Системный аналитик ', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт оранжевый трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/76.png', // Изображение
        ],
        77 => [
            'id' => 77,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Лев', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'GR–менеджер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/77.png', // Изображение
        ],
        78 => [
            'id' => 78,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 2, // Цена карты
            'name' => 'Руслан', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Экспедитор', // Специальность специалиста
            'effect' => ['choose_office_track' => ['amount' => 1]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт любой трек в офисе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => null, // Управление 
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/78.png', // Изображение
        ],
        79 => [
            'id' => 79,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Вероника', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'QA–инженер', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/79.png', // Изображение
        ],
        80 => [
            'id' => 80,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Леон', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Embedded–разработчик', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/80.png', // Изображение
        ],
        81 => [
            'id' => 81,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Ярослав', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Frontend–разработчик', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт розовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/81.png', // Изображение
        ],
        82 => [
            'id' => 82,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Веб–дизайнер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт оранжевый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/82.png', // Изображение
        ],
        83 => [
            'id' => 83,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Фёдор', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Backend–разработчик', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/83.png', // Изображение
        ],
        84 => [
            'id' => 84,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Валентина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Архитектор баз данных', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/84.png', // Изображение
        ],
        85 => [
            'id' => 85,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Мила', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 1,
            ],
            'effectDescription' => 'Наймите 1 дополнительную карту сотрудника с руки, заплатив ее стоимость', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/85.png', // Изображение
        ],
        86 => [
            'id' => 86,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Рустам', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Тимлид', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 1,
            ],
            'effectDescription' => 'Наймите 1 дополнительную карту сотрудника с руки, заплатив ее стоимость', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/86.png', // Изображение
        ],
        87 => [
            'id' => 87,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Павел', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Арт–менеджер', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/87.png', // Изображение
        ],
        88 => [
            'id' => 88,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Алиса', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Инновационный менеджер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 1']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 1 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/88.png', // Изображение
        ],
        89 => [
            'id' => 89,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Эдуард', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Продукт–менеджер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт оранжевый трек в техотделе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/89.png', // Изображение
        ],
        90 => [
            'id' => 90,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Александра', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Юрист', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/90.png', // Изображение
        ],
        91 => [
            'id' => 91,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Василиса', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бухгалтер', // Специальность специалиста
            'effect' => ['updateTrackSprints' => [['amount' => '+ 1']]], // Трек спринта (sprint-column-tasks) +1
            'effectDescription' => 'Улучшите на 1 пункт трек задач на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/91.png', // Изображение
        ],
        92 => [
            'id' => 92,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Алексей', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер IT–проектов', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 1,
            ],
            'effectDescription' => 'Наймите 1 дополнительную карту сотрудника с руки, заплатив ее стоимость', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/92.png', // Изображение
        ],
        93 => [
            'id' => 93,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Валерий', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер интернет–проекта', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 1,
            ],
            'effectDescription' => 'Наймите 1 дополнительную карту сотрудника с руки, заплатив ее стоимость', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/93.png', // Изображение
        ],
        94 => [
            'id' => 94,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Тамара', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 2']]], // Эффект
            'effectDescription' => 'Улучшите на 2 трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/94.png', // Изображение
        ],
        95 => [
            'id' => 95,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Владимир', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Трафик–менеджер', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 2']]], // Эффект
            'effectDescription' => 'Улучшите на 2 трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/sales-department/95.png', // Изображение
        ],
        96 => [
            'id' => 96,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Зоя', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Таргетолог', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/96.png', // Изображение
        ],
        97 => [
            'id' => 97,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Инна', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Директолог', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/97.png', // Изображение
        ],
        98 => [
            'id' => 98,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Денис', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/98.png', // Изображение
        ],
        99 => [
            'id' => 99,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Василий', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'BTL–менеджер', // Специальность специалиста
            'effect' => ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 1']]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт трек дохода в отделе продаж', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/99.png', // Изображение
        ],
        100 => [
            'id' => 100,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Михаил', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Руководитель отдела продаж', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 1,
            ],
            'effectDescription' => 'Наймите 1 дополнительную карту сотрудника с руки, заплатив ее стоимость', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/100.png', // Изображение
        ],
        101 => [
            'id' => 101,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Елена', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Логист', // Специальность специалиста
            'effect' => ['choose_office_track' => ['amount' => 1]], // Эффект
            'effectDescription' => 'Улучшите на 1 пункт любой трек в офисе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/101.png', // Изображение
        ],
        102 => [
            'id' => 102,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Милана', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Юрист', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/102.png', // Изображение
        ],
        103 => [
            'id' => 103,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Никита', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–консультант', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/103.png', // Изображение
        ],
        104 => [
            'id' => 104,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 3, // Цена карты
            'name' => 'Элина', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Офис-менеджер', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/104.png', // Изображение
        ],
        105 => [
            'id' => 105,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Семён', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Дата–сайнтист', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 5, 'move_color' => 'purple']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте фиолетовый(ые) жетон(ы) задач на 5 этапов на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/105.png', // Изображение
        ],
        106 => [
            'id' => 106,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Григорий', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Fullstack–разработчик', // Специальность специалиста
            'effect' => ['move_task' => ['move_count' => 6, 'move_color' => 'any']], // Эффект — любые жетоны, 6 этапов на панели спринта
            'effectDescription' => 'Передвиньте жетоны задач любого(ых) цвета(ов) на 6 этапов на панели спринта', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/technical-department/106.png', // Изображение
        ],
        107 => [
            'id' => 107,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Ольга', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Специалист по кибербезопасности', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/technical-department/107.png', // Изображение
        ],
        108 => [
            'id' => 108,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Артур', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Scrum–мастер', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'cyan-track', 'amount' => '+ 2']]], // Эффект
            'effectDescription' => 'Улучшите на 2 пункт голубой трек в техотделе', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/108.png', // Изображение
        ],
        109 => [
            'id' => 109,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Рада', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Agile–коуч', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 5, 'move_color' => 'pink']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте розовый(ые) жетон(ы) задач на 5 этапов на панели спринта', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/109.png', // Изображение
        ],
        110 => [
            'id' => 110,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Диана', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бухгалтер', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/back-office/110.png', // Изображение
        ],
        111 => [
            'id' => 111,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Антон', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Интернет–маркетолог', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'pink-track', 'amount' => '+ 2']]], // Эффект
            'effectDescription' => 'Улучшите на 2 розовый трек в техотделе', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/111.png', // Изображение
        ],
        112 => [
            'id' => 112,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Марат', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Бренд–менеджер', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 5, 'move_color' => 'orange']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте оранжевый(ые) жетон(ы) задач на 5 этапов на панели спринта', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/112.png', // Изображение
        ],
        113 => [
            'id' => 113,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Вячеслав', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/113.png', // Изображение
        ],
        114 => [
            'id' => 114,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Кирилл', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Консультант по продажам', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Возьми жетон маркер на карту. Можешь защититься от атаки другого игрока 1 раз', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/sales-department/114.png', // Изображение
        ],
        115 => [
            'id' => 115,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Данил', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Персональный ассистент', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Передвиньте голубой(ые) жетон(ы) задачи на 2 этапа на панели спринта. Все игроки могут тоже передвинуть голубой жетон задачи на 1 этап ', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/115.png', // Изображение
        ],
        116 => [
            'id' => 116,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Пелагея', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–аналитик', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 3, 'move_color' => 'orange']], // Эффект — розовые жетоны, до 4 шагов, // Эффект
            'effectDescription' => 'Передвиньте оранжевый(ые) жетон(ы) задачи на 2 этапа на панели спринта. Все игроки могут тоже передвинуть оранжевый жетон задачи на 1 этап ', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/116.png', // Изображение
        ],
        117 => [
            'id' => 117,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Лейла', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Бизнес–консультант', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Передвиньте розовый(ые) жетон(ы) задачи на 2 этапа на панели спринта. Все игроки могут тоже передвинуть розовый жетон задачи на 1 этап ', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/117.png', // Изображение
        ],
        118 => [
            'id' => 118,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Адам', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Передвиньте фиолетовый(ые) жетон(ы) задачи на 2 этапа на панели спринта. Все игроки могут тоже передвинуть розовый жетон задачи на 1 этап ', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/118.png', // Изображение
        ],
        119 => [
            'id' => 119,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Полина', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 2,
            ],
            'effectDescription' => 'Наймите 2 дополнительные карты сотрудников с руки, заплатив их стоимость', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/119.png', // Изображение
        ],
        120 => [
            'id' => 120,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Демьян', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => [
                'hire_from_hand' => 2,
            ],
            'effectDescription' => 'Наймите 2 дополнительные карты сотрудников с руки, заплатив их стоимость', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/specialists/universal/120.png', // Изображение
        ],
        121 => [
            'id' => 121,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Таисия', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => ["move_task" => ['move_count' => 5, 'move_color' => 'cyan']], // Эффект — голубые жетоны, до 4 шагов
            'effectDescription' => 'Передвиньте голубой(ые) жетон(ы) задач на 5 этапов на панели спринта', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/121.png', // Изображение
        ],
        122 => [
            'id' => 122,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Константин', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Backend–разработчик', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'purple-track', 'amount' => '+ 2']]], // Эффект — голубой трек в техотделе
            'effectDescription' => 'Улучшите на 2 фиолетовый трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/122.png', // Изображение
        ],
        123 => [
            'id' => 123,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 4, // Цена карты
            'name' => 'Роман', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => ['updateTrackDepartmentTechnical' => [['track' => 'orange-track', 'amount' => '+ 2']]], // Эффект
            'effectDescription' => 'Улучшите на 2 оранжевый трек в техотделе', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 1, // Очки победы
            'img' => 'img/specialists/universal/123.png', // Изображение
        ],
        124 => [
            'id' => 124,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Ольга', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Бизнес–аналитик', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Диффамация. Уменьшите трек найма у другого игрока на 1 пункт и увеличьте свой на 1 пункт', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/back-office/124.png', // Изображение
        ],
        125 => [
            'id' => 125,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Светлана', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Директор по персоналу', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Диффамация. Уменьшите трек найма у другого игрока на 1 пункт и увеличьте свой на 1 пункт', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/back-office/125.png', // Изображение
        ],
        126 => [
            'id' => 126,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Медина', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Промышленный шпионаж. Уменьшите трек задач у другого игрока на 1 пункт и увеличьте свой на 1 пункт', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/technical-department/126.png', // Изображение
        ],
        127 => [
            'id' => 127,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Татьяна', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'Директор по развитию', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Промышленный шпионаж. Уменьшите трек задач у другого игрока на 1 пункт и увеличьте свой на 1 пункт', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/technical-department/127.png', // Изображение
        ],
        128 => [
            'id' => 128,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Николай', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Руководитель отдела продаж', // Специальность специалиста
            'effect' => ['counter_advertising' => ['amount' => 1]], // Эффект
            'effectDescription' => 'Контрреклама. Уменьшите трек дохода у другого игрока на 1Б и увеличьте свой на 1Б', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/sales-department/128.png', // Изображение
        ],
        129 => [
            'id' => 129,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Алиса', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Руководитель отдела продаж', // Специальность специалиста
            'effect' => ['counter_advertising' => ['amount' => 1]], // Эффект
            'effectDescription' => 'Контрреклама. Уменьшите трек дохода у другого игрока на 1Б и увеличьте свой на 1Б', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/sales-department/129.png', // Изображение
        ],
        130 => [
            'id' => 130,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Захар', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Директор по персоналу', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Хэдхантинг. Увольте карту соперника стоимостью 1Б,2Б,3Б или 4Б, и можете нанять ее заплатив стоимость', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/back-office/130.png', // Изображение
        ],
        131 => [
            'id' => 131,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Дмитрий', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Супервайзер', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Хэдхантинг. Увольте карту соперника стоимостью 1Б,2Б,3Б или 4Б, и можете нанять ее заплатив стоимость', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/sales-department/131.png', // Изображение
        ],
        132 => [
            'id' => 132,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 5, // Цена карты
            'name' => 'Наталья', // Имя специалиста
            'color' => '#008000', // Цвет специалиста
            'speciality' => 'Супервайзер', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Хэдхантинг. Увольте карту соперника стоимостью 1Б,2Б,3Б или 4Б, и можете нанять ее заплатив стоимость', // Описание эффекта
            'department' => 'sales-department', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/sales-department/132.png', // Изображение
        ],
        133 => [
            'id' => 133,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 6, // Цена карты
            'name' => 'Павел', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–директор', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Кибератака. Уменьшите на 1 трек в техотделе у другого игрока и увеличьте свой на 1 этого же цвета', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/technical-department/133.png', // Изображение
        ],
        134 => [
            'id' => 134,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 6, // Цена карты
            'name' => 'Максим', // Имя специалиста
            'color' => '#0000FF', // Цвет специалиста
            'speciality' => 'IT–директор', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Кибератака. Уменьшите на 1 трек в техотделе у другого игрока и увеличьте свой на 1 этого же цвета', // Описание эффекта
            'department' => 'technical-department', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/technical-department/134.png', // Изображение
        ],
        135 => [
            'id' => 135,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 6, // Цена карты
            'name' => 'Анастасия', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'IT–менеджер', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Кибератака. Уменьшите на 1 трек в техотделе у другого игрока и увеличьте свой на 1 этого же цвета', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/back-office/135.png', // Изображение
        ],
        136 => [
            'id' => 136,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 6, // Цена карты
            'name' => 'Малика', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер IT–проектов', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Хэдхантинг. Увольте карту соперника стоимостью 1Б,2Б,3Б или 4Б, и можете нанять ее заплатив стоимость', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/back-office/136.png', // Изображение
        ],
        137 => [
            'id' => 137,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 7, // Цена карты
            'name' => 'Евгений', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'IT–директор', // Специальность специалиста
            'effect' => ['counter_advertising' => ['amount' => 2]], // Эффект
            'effectDescription' => 'Контрреклама. Уменьшите трек дохода у другого игрока на 2Б и увеличьте свой на 2Б', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/137.png', // Изображение
        ],
        138 => [
            'id' => 138,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 7, // Цена карты
            'name' => 'Алексей', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Коммерческий директор', // Специальность специалиста
            'effect' => ['counter_advertising' => ['amount' => 2]], // Эффект
            'effectDescription' => 'Контрреклама. Уменьшите трек дохода у другого игрока на 2Б и увеличьте свой на 2Б', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/138.png', // Изображение
        ],
        139 => [
            'id' => 139,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 7, // Цена карты
            'name' => 'Варвара', // Имя специалиста
            'color' => '#FFFF00', // Цвет специалиста
            'speciality' => 'Коммерческий директор', // Специальность специалиста
            'effect' => ['counter_advertising' => ['amount' => 2]], // Эффект
            'effectDescription' => 'Контрреклама. Уменьшите трек дохода у другого игрока на 2Б и увеличьте свой на 2Б', // Описание эффекта
            'department' => 'universal', // Отдел
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 2, // Очки победы
            'img' => 'img/specialists/universal/139.png', // Изображение
        ],
        140 => [
            'id' => 140,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 8, // Цена карты
            'name' => 'Олег', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'IT–евангелист', // Специальность специалиста
            'effect' => null, // Эффект
            'effectDescription' => 'Хэдхантинг. Увольте карту соперника стоимостью 1Б,2Б,3Б или 4Б, и можете нанять ее заплатив стоимость', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P ', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 3, // Очки победы
            'img' => 'img/specialists/back-office/140.png', // Изображение
        ],
        141 => [
            'id' => 141,
            'type' => 'specialist', // Тип карты
            'typeName' => 'специалист', // Тип карты название на русском
            'price' => 8, // Цена карты
            'name' => 'Жанна', // Имя специалиста
            'color' => '#800000', // Цвет специалиста
            'speciality' => 'Менеджер IT–проектов', // Специальность специалиста
            'effect' => ['card' => '+ 1'], // Эффект
            'effectDescription' => 'Возьмите 1 карту из колоды найма', // Описание эффекта
            'department' => 'back-office', // Отдел
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Первая игра
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 5, // Очки победы
            'img' => 'img/specialists/back-office/141.png', // Изображение
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
            'effect' => null,
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
        // Возвращаем массив карт из константы CARD_COUNT
        return self::CARD_COUNT;
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

