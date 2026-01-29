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
        error_log('🎲 RoundEvent::getArgs() CALLED');
        
        $round = (int)$this->game->getGameStateValue('round_number');
        $faceIndex = (int)$this->game->getGameStateValue('round_cube_face');
        $lastCubeRound = (int)$this->game->getGameStateValue('last_cube_round', 0);
        
        error_log('🎲 RoundEvent::getArgs() - round: ' . $round . ', faceIndex: ' . $faceIndex . ', lastCubeRound: ' . $lastCubeRound);
        
        // ВАЖНО: Бросаем новый кубик только если он еще не был брошен для этого раунда
        if ($lastCubeRound !== $round || $faceIndex < 0 || $faceIndex >= count($this->game->getCubeFaces())) {
            error_log('🎲 RoundEvent::getArgs() - Rolling NEW cube for round ' . $round);
            $cubeFace = $this->game->rollRoundCube();
            $this->game->setGameStateValue('last_cube_round', $round);
            error_log('🎲 RoundEvent::getArgs() - Cube rolled: ' . $cubeFace);
        } else {
            // Кубик уже брошен для этого раунда - используем существующее значение
            $faces = $this->game->getCubeFaces();
            $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
            error_log('🎲 RoundEvent::getArgs() - Using existing cube face for round ' . $round . ': ' . $cubeFace);
        }
        
        // ВАЖНО: Подготавливаем новые карты событий только если они еще не были подготовлены для этого раунда
        $lastEventCardsRound = (int)$this->game->getGameStateValue('last_event_cards_round', 0);
        if ($lastEventCardsRound !== $round) {
            error_log('🎲 RoundEvent::getArgs() - Preparing NEW event cards for round ' . $round);
            $roundEventCards = $this->game->prepareRoundEventCard();
            $this->game->setGameStateValue('last_event_cards_round', $round);
            error_log('🎲 RoundEvent::getArgs() - Event cards prepared: ' . count($roundEventCards));
        } else {
            // Карты уже подготовлены для этого раунда - используем существующие
            $roundEventCards = $this->game->getRoundEventCards();
            error_log('🎲 RoundEvent::getArgs() - Using existing event cards for round ' . $round . ': ' . count($roundEventCards));
        }
        
        // Логирование для отладки
        error_log('🎲 RoundEvent::getArgs() - FINAL: round: ' . $round . ', cubeFace: ' . $cubeFace . ', cards count: ' . count($roundEventCards));
        
        // Получаем данные фазы из массива фаз
        $phase = $this->game->getPhaseByKey('event');
        $phaseName = $phase ? $phase['name'] : '';
        $phaseNumber = $phase ? $phase['number'] : null;
        
        return [
            'cubeFace' => $cubeFace,
            'round' => $round,
            'roundName' => $this->game->getRoundName($round),
            'phaseName' => $phaseName,
            'phaseNumber' => $phaseNumber,
            'phaseKey' => 'event',
            'roundEventCards' => $roundEventCards,
            'eventCard' => $roundEventCards[0] ?? null,
        ];
    }

    public function onEnteringState() // Метод входа в состояние "Событие"
    {
        error_log('🎲🎲🎲 RoundEvent::onEnteringState() CALLED!');
        $round = (int)$this->game->getGameStateValue('round_number'); // Текущий раунд
        
        // ВАЖНО: Проверяем, что мы на ЭТАПЕ 2 (round > 0)
        // RoundEvent должен вызываться только на ЭТАПЕ 2, не на ЭТАПЕ 1
        if ($round === 0) {
            error_log('🎲❌ RoundEvent::onEnteringState() - ОШИБКА: Попытка выполнить RoundEvent при round=0 (ЭТАП 1)!');
            error_log('🎲❌ RoundEvent не должен вызываться на ЭТАПЕ 1. Это критическая ошибка.');
            // Возвращаемся к NextPlayer, который должен обработать переход к ЭТАПУ 2
            return NextPlayer::class;
        }
        
        $playersLeftInRound = (int)$this->game->getGameStateValue('players_left_in_round');
        $playersCount = count($this->game->loadPlayersBasicInfos());
        $lastCubeRound = (int)$this->game->getGameStateValue('last_cube_round', 0);
        error_log('🎲 RoundEvent::onEnteringState() - round: ' . $round . ', players_left_in_round: ' . $playersLeftInRound . ', playersCount: ' . $playersCount . ', lastCubeRound: ' . $lastCubeRound);
        
        // ВАЖНО: Проверяем, что счетчик игроков установлен правильно
        // NextPlayer устанавливает players_left_in_round = playersCount при переходе к новому раунду
        // Это нужно для правильного определения начала раунда в NextPlayer
        // НЕ исправляем счетчик, если он уже установлен правильно NextPlayer'ом
        if ($playersLeftInRound === 0 || $playersLeftInRound > $playersCount) {
            // Только если счетчик равен 0 (ошибка) или больше playersCount (ошибка), исправляем
            error_log('🎲 WARNING: RoundEvent - players_left_in_round (' . $playersLeftInRound . ') is 0 or > playersCount (' . $playersCount . ')!');
            error_log('🎲 WARNING: This is an error. Fixing...');
            $this->game->setGameStateValue('players_left_in_round', $playersCount);
            error_log('🎲 RoundEvent - Fixed players_left_in_round to: ' . $playersCount);
        } else if ($playersLeftInRound === $playersCount) {
            // Счетчик равен playersCount - это нормально для начала раунда
            error_log('🎲 RoundEvent - players_left_in_round is correct for round start: ' . $playersLeftInRound . ' = ' . $playersCount);
        } else {
            // Счетчик меньше playersCount - это нормально, если раунд уже начался
            error_log('🎲 RoundEvent - players_left_in_round (' . $playersLeftInRound . ') < playersCount (' . $playersCount . ') - round in progress');
        }

        // ВАЖНО: Бросаем новый кубик только если он еще не был брошен для этого раунда
        // (getArgs() мог уже бросить кубик, но на всякий случай проверяем здесь тоже)
        if ($lastCubeRound !== $round) {
            error_log('🎲 RoundEvent::onEnteringState() - Rolling NEW cube for round ' . $round);
            $cubeFace = $this->game->rollRoundCube();
            $this->game->setGameStateValue('last_cube_round', $round);
            error_log('🎲 RoundEvent::onEnteringState() - Cube rolled: ' . $cubeFace);
        } else {
            // Кубик уже брошен для этого раунда - используем существующее значение
            $faceIndex = (int)$this->game->getGameStateValue('round_cube_face');
            $faces = $this->game->getCubeFaces();
            $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
            error_log('🎲 RoundEvent::onEnteringState() - Using existing cube face for round ' . $round . ': ' . $cubeFace);
        }
        
        // ВАЖНО: Подготавливаем новые карты событий только если они еще не были подготовлены для этого раунда
        $lastEventCardsRound = (int)$this->game->getGameStateValue('last_event_cards_round', 0);
        if ($lastEventCardsRound !== $round) {
            error_log('🎲 RoundEvent::onEnteringState() - Preparing NEW event cards for round ' . $round);
            $eventCards = $this->game->prepareRoundEventCard();
            $this->game->setGameStateValue('last_event_cards_round', $round);
            error_log('🎲 RoundEvent::onEnteringState() - Event cards prepared: ' . count($eventCards));
        } else {
            // Карты уже подготовлены для этого раунда - используем существующие
            $eventCards = $this->game->getRoundEventCards();
            error_log('🎲 RoundEvent::onEnteringState() - Using existing event cards for round ' . $round . ': ' . count($eventCards));
        }
        
        error_log('🎲 RoundEvent::onEnteringState() - FINAL: cubeFace: ' . $cubeFace . ', eventCards: ' . count($eventCards));

        // Сохраняем ключ фазы в глобальную переменную (перевод на клиенте)
        $this->game->globals->set('current_phase_name', 'event');

        // ВАЖНО: Отправляем уведомление СИНХРОННО перед переходом в следующее состояние
        // Это гарантирует, что клиент получит данные до перехода в PlayerTurn
        error_log('🎲 RoundEvent::onEnteringState() - Sending roundStart notification...');
        // Получаем данные фазы из массива фаз
        $phase = $this->game->getPhaseByKey('event');
        $phaseName = $phase ? $phase['name'] : '';
        $phaseNumber = $phase ? $phase['number'] : null;
        
        $this->notify->all('roundStart', clienttranslate('Начало раунда ${round}'), [ // Уведомление о начале раунда
            'round' => $round, // Текущий раунд
            'roundName' => $this->game->getRoundName($round), // Название этапа
            'cubeFace' => $cubeFace, // Значение кубика на раунд
            'phaseName' => $phaseName, // Название фазы
            'phaseNumber' => $phaseNumber, // Номер фазы
            'phaseKey' => 'event', // Ключ фазы
            'roundEventCards' => $eventCards,
            'eventCard' => $eventCards[0] ?? null,
            'founders' => $this->game->getFoundersByPlayer(),
            'i18n' => ['roundName', 'phaseName'], // Название раунда и фазы
        ]); // Уведомление о начале раунда
        error_log('🎲 RoundEvent::onEnteringState() - roundStart notification sent! cubeFace: ' . $cubeFace . ', cards: ' . count($eventCards));

        // ВАЖНО: Проверяем счетчик игроков перед переходом к NextPlayer
        // НО: НЕ исправляем его, если он уже установлен правильно NextPlayer'ом
        // NextPlayer устанавливает players_left_in_round = playersCount при переходе к новому раунду
        // Это нужно для правильного определения начала раунда в NextPlayer
        $finalPlayersLeft = (int)$this->game->getGameStateValue('players_left_in_round');
        $finalPlayersCount = count($this->game->loadPlayersBasicInfos());
        error_log('🎲 RoundEvent::onEnteringState() - Before NextPlayer: players_left_in_round=' . $finalPlayersLeft . ', playersCount=' . $finalPlayersCount);
        
        // ВАЖНО: Исправляем ТОЛЬКО если счетчик равен 0 или больше playersCount (ошибка)
        // НЕ исправляем если он равен playersCount (это нормально для начала раунда)
        if ($finalPlayersLeft === 0 || $finalPlayersLeft > $finalPlayersCount) {
            error_log('🎲 RoundEvent::onEnteringState() - WARNING: Counter is 0 or too high! Fixing before NextPlayer...');
            $this->game->setGameStateValue('players_left_in_round', $finalPlayersCount);
            error_log('🎲 RoundEvent::onEnteringState() - Fixed players_left_in_round to: ' . $finalPlayersCount);
        } else if ($finalPlayersLeft === $finalPlayersCount) {
            error_log('🎲 RoundEvent::onEnteringState() - Counter is correct for round start: ' . $finalPlayersLeft . ' = ' . $finalPlayersCount);
        } else {
            error_log('🎲 RoundEvent::onEnteringState() - Counter is less than playersCount: ' . $finalPlayersLeft . ' < ' . $finalPlayersCount . ' (this is OK if round is in progress)');
        }

        // Помечаем, что фаза «Событие» отработала (roundStart отправлен).
        // NextPlayer при повторном заходе не уйдёт в цикл RoundEvent↔NextPlayer.
        $this->game->globals->set('event_phase_just_finished', '1');
        error_log('🎲 RoundEvent::onEnteringState() - Set event_phase_just_finished, transitioning to NextPlayer');
        // Всегда возвращаем NextPlayer — BGA не допускает «финальное» состояние RoundEvent (15).
        return NextPlayer::class;
    }
}


