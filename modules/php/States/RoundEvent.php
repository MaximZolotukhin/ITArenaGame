<?php

declare(strict_types=1); // Строгий режим типов

namespace Bga\Games\itarenagame\States; // Пространство имен состояний игры

use Bga\GameFramework\StateType; // Тип состояния
use Bga\Games\itarenagame\Game; // Класс игры

/**
 * Фаза 1 раунда: "Событие" — бросок кубика и объявление начала раунда
 */
class RoundEvent extends \Bga\GameFramework\States\GameState // Класс состояния "Событие"
{
    function __construct( // Конструктор состояния "Событие"
        protected Game $game, // Класс игры
    ) {
        parent::__construct( // Родительский конструктор
            $game,  // Класс игры
            id: 15, // ID состояния
            type: StateType::GAME, // Тип состояния
            updateGameProgression: true, // Обновление прогрессии игры
        ); // Родительский конструктор
    }

    public function onEnteringState() // Метод входа в состояние "Событие"
    {
        $round = (int)$this->game->getGameStateValue('round_number'); // Текущий раунд

        $eventCard = $this->game->prepareRoundEventCard();

        // Бросаем кубик этой фазы
        $cubeFace = $this->game->rollRoundCube(); // Значение кубика на раунд

        // Сохраняем ключ фазы в глобальную переменную (перевод на клиенте)
        $this->game->globals->set('current_phase_name', 'event');

        // Уведомляем игроков о начале раунда и значении кубика
        $this->notify->all('roundStart', clienttranslate('Начало раунда ${round}'), [ // Уведомление о начале раунда
            'round' => $round, // Текущий раунд
            'stageName' => $this->game->getStageName($round), // Название этапа
            'cubeFace' => $cubeFace, // Значение кубика на раунд
            'phaseName' => $this->game->getPhaseName('event'), // Название фазы
            'eventCard' => $eventCard,
            'i18n' => ['stageName', 'phaseName'], // Название этапа и фазы
        ]); // Уведомление о начале раунда

        // После события — активируем первого игрока и переходим к его ходу
        $this->game->activeNextPlayer();
        return PlayerTurn::class;
    }
}


