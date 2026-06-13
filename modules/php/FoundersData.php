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
            'effect' => ['badger' => '+ 4', 'card' => '+ 4', 'task' => '+ 4', "move_task" => ['move_count' => 4, 'move_color' => 'any']], // Эффект 'task' => '+ 3',
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Четкий старт. Возьмите 4Б, 4 карты, 4 задачи и передвиньте жетоны задач любых цветов на 4 этапа на панели спринта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Dmitry.jpg', // Изображение
        ],
        2 => [
            'id' => 2,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Владимир', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => [
                'minTechDevTrack' => [
                    'amount' => '+ 1',
                    'requiredDepartment' => 'technical-department',
                ],
            ],
            'department' => 'technical-department', // Отдел
            'activationStage' => 'SprintPhase', // Этап активации эффекта
            'effectDescription' => 'Технологии Бескодов. В каждую фазу "Спринт" улучшите минимальный трек в техотделе на 1 пункт', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vladimir.jpg', // Изображение
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
            'effectDescription' => 'Везунчик. Можете заплатить 1Б и не получать штраф от события в этом раунде', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Nellie.jpg', // Изображение
        ],
        4 => [
            'id' => 4,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Роман', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => ['badger' => '+ 2'], // Эффект: 2Б из банка за каждый купленный IT-проект
            'department' => 'sales-department', // Отдел
            'activationStage' => 'ProjectsPhase', // Этап активации эффекта
            'effectDescription' => 'Пассионарий. Получите 2Б из банка когда выполняете IT проект', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Roman.jpg', // Изображение
        ],
        5 => [
            'id' => 5,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Михаил Алистер', // Имя основателя
            'color' => '#800000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'back-office', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Личный бренд. В каждой фазе «Найм» активируйте повторно перк 1 карты стоимостью 1,2, 3 или 4Б. (Возьмите 6 прозрачных жетонов маркеров (Можно иконкой) для отметки активированных карт для их маркировки)', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Michael_Alistair.jpg', // Изображение
        ],
        6 => [
            'id' => 6,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Эмиль', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Оптимизатор. Когда вы нанимаете 5 карт в отдел, то получите двойной бонус активации отдела. А тот отдел, в котором основатель, можно активировать с 4 карт.', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Emil.jpg', // Изображение
        ],
        7 => [
            'id' => 7,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Вардгес', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => ['badger' => '+ 8', 'card' => '+ 8'], // Эффект
            'department' => 'sales-department', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Семья - это всё! Возьмите 8Б из банка и возьмите 8 карт из колоды найма', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vardges.jpg', // Изображение
        ],
        8 => [
            'id' => 8,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Виталий', // Имя основателя
            'color' => '#800000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'back-office', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Комбинатор. Когда вы получаете бонус IT проекта за новый цвет, то получите двойной бонус и вы можете выбрать 1 из 3 типов ресурсов(задачи, монеты или смены этапа)', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vitaly.jpg', // Изображение
        ],
        9 => [
            'id' => 9,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Лидия', // Имя основателя
            'color' => '#800000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => [
                'updateTrack' => [
                    ['track' => 'income-track', 'amount' => '+ 1'], // Трек дохода в отделе продаж
                    ['track' => 'player-department-back-office-evolution-column-1', 'amount' => '+ 1'], // Трек найма в бэк-офисе
                ],
                'updateTrackSprints' => [['amount' => '+ 1']], // Трек спринта (sprint-column-tasks) +1
                'badger' => '+ 2' // получить 2 битка
            ], // Эффект
            'department' => 'back-office', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Отличник. Улучшите на 1 пункт трек дохода в отделе продаж и на 1 трек найма и 1 трек задач на панели спринта. В начале игры получите 2Б', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Lydia.jpg', // Изображение
        ],
        10 => [
            'id' => 10,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Илья', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => ['projectTaskCostDiscount' => ['prices' => [3, 4], 'amount' => 1]], // Эффект
            'department' => 'technical-department', // Отдел
            'activationStage' => null, // Этап активации эффекта
            'effectDescription' => 'Айтишник. IT проекты стоимостью 3[\/] и 4[\/] стоят на 1[\/] меньше.', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'A', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Ilya.jpg', // Изображение
        ],
        11 => [
            'id' => 11,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Сергей', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => ['hireAnyForOneBadgerOncePerHiringPhase' => true], // Эффект
            'department' => 'technical-department', // Отдел
            'activationStage' => 'RoundHiring', // Этап активации эффекта
            'effectDescription' => 'Мастер рекрутинга. Один раз в фазу найм вы можете нанять 1 карту стоимостью 2-4 за 1Б или 1 карту стоимостью 5-8 за 2Б', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Sergey.jpg', // Изображение
        ],
        12 => [
            'id' => 12,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Елена', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => [
                'updateTrack' => [
                    ['track' => 'income-track', 'amount' => '+ 3'], // Трек дохода в отделе продаж, // Эффект
                    'badger' => '+ 2' // получить 2 битка
                ]],
            'department' => 'sales-department', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Финансист. Улучшите на 3 трек дохода в отделе продаж. В начале игры получите 2Б', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Elena.jpg', // Изображение
        ],
        13 => [
            'id' => 13,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Вера', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'back-office', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Оратор. Количество получаемых карт за раунд +1 карту от трека найма (Иконка получения карт) и количество нанимаемых карт за раунд +1 карту от трека найма (Иконка найма)', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vera.jpg', // Изображение
        ],
        14 => [
            'id' => 14,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Вячеслав', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'sales-department', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Настольщик. При выполнении общей цели получите Б из банка. 2 игрока 6Б 3Б, 3 игрока 7Б 4Б, 4 игрока 10Б 5Б', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'I', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Vyacheslav.jpg', // Изображение
        ],
        15 => [
            'id' => 15,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Иван', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Кредитор. Чтобы получить монеты Б уменьшите свой доход  1 ур. -1 доход = 3Б | 2 ур. -3 доход = 10Б.', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Ivan.jpg', // Изображение
        ],
        16 => [
            'id' => 16,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Джессика', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Достигатор. Когда выполняете общую цель, можете сразу ее отметить на игровом поле. При подсчете очков за личную цель получите в 2 раза больше ПО ', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'P', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => 'E', // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Jesika.jpg', // Изображение
        ],
        17 => [
            'id' => 17,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Джастин', // Имя основателя
            'color' => '#008000', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'sales-department', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Нарцисс. Каждый раунд 1 раз в фазу "Продажи" можете за 1Б улучшить любой трек в офисе на 1 пункт, а за 3Б - можете улучшить любой(ые) трек(и) в офисе на 2 пункта', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Jastin.jpg', // Изображение
        ],
        18 => [
            'id' => 18,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Екатерина', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'technical-department', // Отдел
            'activationStage' => 'SprintPhase', // Этап активации эффекта
            'effectDescription' => 'Гений. В начале игры вместо розового и голубого жетонов возьмите 1 прозрачный жетон маркер и положите в работу на панели спринта. Этот жетон считается задачей любого цвета. После выполнения IT проекта этот жетон возвращается на этап в работе', // Описание эффекта
            'starterOrFinisher' => 'F', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Ekaterina.jpg', // Изображение
        ],
        19 => [
            'id' => 19,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Денис', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'technical-department', // Отдел
            'activationStage' => 'SprintPhase', // Этап активации эффекта
            'effectDescription' => 'Доминатор. Возьмите 4 прозрачных жетона маркера (иконка). В свой ход в фазу проекты можете блокировать ими IT-проекты. Когда делаете это то получите 1 смену этапа задачи того цвета, какой цвет IT-проекта.', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'A', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Denis.jpg', // Изображение
        ],
        20 => [
            'id' => 20,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Татьяна', // Имя основателя
            'color' => '#0000FF', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null,
            'department' => 'back-office', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Находчивый. 1 раз за раунд в фазу спринт вы можете сбросить 1 карту, чтобы дублировать любую задачу на этапе выполнено на панели спринта.', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'E', // Управление
            'firstGame' => true, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Tatyana.jpg', // Изображение
        ],
        21 => [
            'id' => 21,
            'type' => 'founder', // Тип карты
            'typeName' => 'Основатель', // Тип карты название на русском
            'price' => null, // Цена карты
            'name' => 'Ярослава', // Имя основателя
            'color' => '#FFFF00', // Цвет основателя
            'speciality' => 'Основатель', // Специальность основателя
            'effect' => null, // Эффект
            'department' => 'universal', // Отдел
            'activationStage' => 'GameSetup', // Этап активации эффекта
            'effectDescription' => 'Решала. Возьмите планшет обмена и положите поверх базового обмена на планшете игрока', // Описание эффекта
            'starterOrFinisher' => 'S', // Стартер или финишер
            'management' => 'I', // Управление
            'firstGame' => false, // Если первая игра то будет доступны только 4 карты
            'additionalSkill' => null, // Дополнительный навык
            'victoryPoints' => 0, // Очки победы
            'img' => 'img/founder/Yaroslava.jpg', // Изображение
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
