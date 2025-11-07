<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные карт событий
 * 
 * Этот файл содержит все данные о картах событий.
 * Карты создаются в БД только при вытаскивании (ленивая загрузка).
 */
class EventCardsData
{
    /**
     * Возвращает массив всех карт событий
     * 
     * @return array Массив карт, где ключ - card_type_arg, значение - данные карты
     */
    public static function getAllCards(): array
    {
        return [
            1 => [
                'card_type' => 'event',
                'card_type_arg' => 1,
                'number_card' => 1,
                'name' => 'Кризис на рынке труда',
                'image_url' => './img/event_card/1/1.png',
                'power_round' => 1,
                'description' => 'Компании сталкиваются с нехваткой квалифицированных специалистов, и конкуренция за кадры растет',
                'phase' => 'найм',
                'effect' => '-2 get on hand',
                'effect_description' => 'Все игроки берут на 2 карты меньше в этом раунде',
                'penalty_first' => 'штрафа нет',
                'penalty_second' => 'штрафа нет',
                'penalty_third' => 'штрафа нет',
                'type' => 'найм',
            ],
            2 => [
                'card_type' => 'event',
                'card_type_arg' => 2,
                'number_card' => 2,
                'name' => 'Кадровая миграция',
                'image_url' => './img/event_card/1/2.png',
                'power_round' => 1,
                'description' => 'Специалисты массово переходят в новые сферы, оставляя компании без кандидатов',
                'phase' => 'итоги',
                'effect' => '-2 in players',
                'effect_description' => 'Все игроки сбрасывают 2 карты сотрудника с руки',
                'penalty_first' => '-1',
                'penalty_second' => '-1',
                'penalty_third' => '-1',
                'type' => 'итоги',
            ],
            3 => [
                'card_type' => 'event',
                'card_type_arg' => 3,
                'number_card' => 3,
                'name' => 'Повышение требований к найму',
                'image_url' => './img/event_card/1/3.png',
                'power_round' => 1,
                'description' => 'Новые стандарты в компании ограничили количество подходящих кандидатов',
                'phase' => 'найм',
                'effect' => '-2 in players',
                'effect_description' => 'Все игроки берут на 2 карты меньше в этом раунде',
                'penalty_first' => 'not',
                'penalty_second' => 'not',
                'penalty_third' => 'not',
                'type' => 'найм',
            ],
            4 => [
                'card_type' => 'event',
                'card_type_arg' => 4,
                'number_card' => 4,
                'name' => 'Снижение темпов роста',
                'image_url' => './img/event_card/1/4.png',
                'power_round' => 1,
                'description' => 'Снижение количества открытых позиций заставляет компании проводить меньше собеседований',
            ],
            5 => [
                'card_type' => 'event',
                'card_type_arg' => 5,
                'number_card' => 5,
                'name' => 'Финансовая грамотность',
                'image_url' => './img/event_card/1/5.png',
                'power_round' => 1,
                'description' => 'Благодаря обучению и труду вас ждет награда и успех',
            ], 
            6 => [
                'card_type' => 'event',
                'card_type_arg' => 6,
                'number_card' => 6,
                'name' => 'Массовая утечка данных',
                'image_url' => './img/event_card/1/6.png',
                'power_round' => 1,
                'description' => 'Сразу несколько крупных компаний пострадали от кибератаки, расходы на защиту неизбежны',
            ],
            7 => [
                'card_type' => 'event',
                'card_type_arg' => 7,
                'number_card' => 7,
                'name' => 'Опережение плана',
                'image_url' => './img/event_card/1/7.png',
                'power_round' => 1,
                'description' => 'Хорошая погода  позитивно повлияла на настроение сотрудников и продуктивность выросла',
            ],
            8 => [
                'card_type' => 'event',
                'card_type_arg' => 8,
                'number_card' => 8,
                'name' => 'Повышение зарплат',
                'image_url' => './img/event_card/2/8.png',
                'power_round' => 2,
                'description' => 'Повсеместный рост зарплат в сфере IT вынуждает компании сократить найм и пересмотреть приоритеты',
            ],
            9 => [
                'card_type' => 'event',
                'card_type_arg' => 9,
                'number_card' => 9,
                'name' => 'Реорганизация рынка труда',
                'image_url' => './img/event_card/3/9.png',
                'power_round' => 3,
                'description' => 'Перемены в индустрии снизили приток кандидатов',
            ],
            10 => [
                'card_type' => 'event',
                'card_type_arg' => 10,
                'number_card' => 10,
                'name' => 'Охота за профи',
                'image_url' => './img/event_card/2/10.png',
                'power_round' => 2,
                'description' => 'Компании активно переманивают ценных сотрудников друг у друга',
            ],
            11 => [
                'card_type' => 'event',
                'card_type_arg' => 11,
                'number_card' => 11,
                'name' => 'Кадровые сокращения',
                'image_url' => './img/event_card/2/11.png',
                'power_round' => 2,
                'description' => 'Компании массово пересматривают свои структуры, сокращая часть сотрудников из-за изменений на рынке',
            ],
            12 => [
                'card_type' => 'event',
                'card_type_arg' => 12,
                'number_card' => 12,
                'name' => 'Автоматизация процессов',
                'image_url' => './img/event_card/2/12.png',
                'power_round' => 2,
                'description' => 'Повсеместное внедрение автоматизации сокращает потребность в персонале',
            ],
            13 => [
                'card_type' => 'event',
                'card_type_arg' => 13,
                'number_card' => 13,
                'name' => 'Технологический бум',
                'image_url' => './img/event_card/2/13.png',
                'power_round' => 2,
                'description' => 'Новые технологии внедряются в рынок, требуя от всех компаний обязательных инвестиций в обновления',
            ],
            14 => [
                'card_type' => 'event',
                'card_type_arg' => 14,
                'number_card' => 14,
                'name' => 'Технический коллапси',
                'image_url' => './img/event_card/3/14.png',
                'power_round' => 3,
                'description' => 'Внезапный сбой накрыл все системы, что привело к финансовым потерям компаний',
            ],
            15 => [
                'card_type' => 'event',
                'card_type_arg' => 15,
                'number_card' => 15,
                'name' => 'Проблемы с координацией',
                'image_url' => './img/event_card/3/15.png',
                'power_round' => 3,
                'description' => 'Нарушения в работе систем планирования заставляют компании временно отложить часть задач',
            ],
            16 => [
                'card_type' => 'event',
                'card_type_arg' => 16,
                'number_card' => 16,
                'name' => 'Рецессия на рынке технологий',
                'image_url' => './img/event_card/3/16.png',
                'power_round' => 3,
                'description' => 'Спад в экономике заставляет все компании урезать бюджеты и готовиться к трудным временам',
            ],
            17 => [
                'card_type' => 'event',
                'card_type_arg' => 17,
                'number_card' => 17,
                'name' => 'Технологический бум',
                'image_url' => './img/event_card/3/17.png',
                'power_round' => 3,
                'description' => 'Новые технологии внедряются в рынок, требуя от всех компаний обязательных инвестиций в обновления',
            ],
            18 => [
                'card_type' => 'event',
                'card_type_arg' => 18,
                'number_card' => 18,
                'name' => 'Государственный контроль',
                'image_url' => './img/event_card/3/18.png',
                'power_round' => 3,
                'description' => 'Новые правила и нормативы требуют от IT-компаний серьезных вложений, чтобы соответствовать установленным стандартам',
            ],
            19 => [
                'card_type' => 'event',
                'card_type_arg' => 19,
                'number_card' => 19,
                'name' => 'Заморозка сделок',
                'image_url' => './img/event_card/2/19.png',
                'power_round' => 2,
                'description' => 'На время дополнительных проверок приостанавливаются все процессы продаж ',
            ],
            20 => [
                'card_type' => 'event',
                'card_type_arg' => 20,
                'number_card' => 20,
                'name' => 'Перенос дедлайнов',
                'image_url' => './img/event_card/3/20.png',
                'power_round' => 3,
                'description' => 'Крупный клиент неожиданно перенес сроки, и компании вынуждены временно отложить часть задач',
            ],
            21 => [
                'card_type' => 'event',
                'card_type_arg' => 21,
                'number_card' => 21,
                'name' => 'Крах сети',
                'image_url' => './img/event_card/2/21.png',
                'power_round' => 2,
                'description' => 'Внезапная поломка парализовала работу компаний, вынуждая их быстро искать решения в условиях цифрового хаоса',
            ],
            22 => [
                'card_type' => 'event',
                'card_type_arg' => 22,
                'number_card' => 22,
                'name' => 'Благотворительность',
                'image_url' => './img/event_card/2/22.png',
                'power_round' => 2,
                'description' => 'Компания "Проект Гая" обратилась к всем компаниям за помощью в очистке океана.',
            ],
            23 => [ 
                'card_type' => 'event',
                'card_type_arg' => 23,
                'number_card' => 23,
                'name' => 'Сверх продуктивность',
                'image_url' => './img/event_card/3/23.png',
                'power_round' => 3,
                'description' => 'Благодаря дисциплине, осознанности и спланированному отдыху вы становитесь энергичным и продуктивным.',
            ],
            24 => [
                'card_type' => 'event',
                'card_type_arg' => 24,
                'number_card' => 24,
                'name' => 'Популярность IT',
                'image_url' => './img/event_card/1/24.png',
                'power_round' => 1,
                'description' => 'Код — новая нефть, спрос бьёт все рекорды!',
            ],
        ];
    }

    /**
     * Возвращает данные конкретной карты по card_type_arg
     * 
     * @param int $cardTypeArg
     * @return array|null Данные карты или null, если не найдена
     */
    public static function getCard(int $cardTypeArg): ?array
    {
        $cards = self::getAllCards();
        return $cards[$cardTypeArg] ?? null;
    }

    /**
     * Возвращает количество карт каждого типа для создания колоды
     * 
     * @return array Массив для использования в createCards()
     */
    public static function getCardsForDeck(): array
    {
        $result = [];
        foreach (self::getAllCards() as $cardTypeArg => $cardData) {
            $result[] = [
                'type' => $cardData['card_type'],
                'type_arg' => $cardData['card_type_arg'],
                'nbr' => 1, // Количество карт этого типа
            ];
        }
        return $result;
    }
}

