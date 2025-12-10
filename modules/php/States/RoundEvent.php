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

    public function getArgs(): array
    {
        // Получаем данные кубика и карт событий
        // Проверяем, готовы ли данные (кубик брошен, карты подготовлены)
        $round = (int)$this->game->getGameStateValue('round_number');
        $faceIndex = (int)$this->game->getGameStateValue('round_cube_face');
        
        // Получаем грани кубика
        $faces = $this->game->getCubeFaces();
        $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
        
        // Получаем карты событий
        // Если карты еще не подготовлены (пустой массив), значит onEnteringState еще не выполнился
        // В этом случае возвращаем пустой массив - данные придут через уведомление
        $roundEventCards = $this->game->getRoundEventCards();
        
        // Если кубик еще не брошен (faceIndex = -1 или невалидный), возвращаем пустую строку
        // Данные придут через уведомление roundStart
        if ($faceIndex < 0 || $faceIndex >= count($faces)) {
            $cubeFace = '';
        }
        
        // Логирование для отладки
        error_log('RoundEvent::getArgs() - round: ' . $round . ', faceIndex: ' . $faceIndex . ', cubeFace: ' . $cubeFace . ', cards count: ' . count($roundEventCards));
        
        return [
            'cubeFace' => $cubeFace,
            'round' => $round,
            'roundName' => $this->game->getRoundName($round),
            'phaseName' => $this->game->getPhaseName('event'),
            'roundEventCards' => $roundEventCards,
            'eventCard' => $roundEventCards[0] ?? null,
        ];
    }

    public function onEnteringState() // Метод входа в состояние "Событие"
    {
        $round = (int)$this->game->getGameStateValue('round_number'); // Текущий раунд

        $eventCards = $this->game->prepareRoundEventCard();

        // Бросаем кубик этой фазы
        $cubeFace = $this->game->rollRoundCube(); // Значение кубика на раунд

        // Сохраняем ключ фазы в глобальную переменную (перевод на клиенте)
        $this->game->globals->set('current_phase_name', 'event');

        // Уведомляем игроков о начале раунда и значении кубика
        $this->notify->all('roundStart', clienttranslate('Начало раунда ${round}'), [ // Уведомление о начале раунда
            'round' => $round, // Текущий раунд
            'roundName' => $this->game->getRoundName($round), // Название этапа
            'cubeFace' => $cubeFace, // Значение кубика на раунд
            'phaseName' => $this->game->getPhaseName('event'), // Название фазы
            'roundEventCards' => $eventCards,
            'eventCard' => $eventCards[0] ?? null,
            'founders' => $this->game->getFoundersByPlayer(),
            'i18n' => ['roundName', 'phaseName'], // Название раунда и фазы
        ]); // Уведомление о начале раунда

        // После события — активируем первого игрока и переходим к его ходу
        $this->game->activeNextPlayer();
        return PlayerTurn::class;
    }
}


