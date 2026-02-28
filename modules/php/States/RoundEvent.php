<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Событие» (Event) раунда.
 *
 * В этой фазе выполняются два действия:
 * 1) Получаем карту(ы) событий текущего раунда — выбираются из колоды и кладутся на стол (location 'table').
 * 2) Бросаем кость и сохраняем выпавшее значение в переменные раунда:
 *    - round_cube_face — индекс грани кубика;
 *    - round_cube_paei_count — количество символов PAEI на грани (1 или 2).
 *
 * Значение кости и карты событий сохраняются и используются в течение всего раунда при срабатывании эффектов.
 */
class RoundEvent extends GameState
{
    function __construct( // Конструктор состояния "Событие"
        protected Game $game, // Класс игры
    ) {
        parent::__construct( // Родительский конструктор
            $game,  // Класс игры
            id: 15, // ID состояния
            type: StateType::GAME, // Тип состояния
            updateGameProgression: true, // Обновление прогрессии игры
            transitions: [
                'toNextPlayer' => 90, // NextPlayer
            ],
        ); // Родительский конструктор
    }

    public function getArgs(): array
    {
        error_log('🎲 RoundEvent::getArgs() CALLED');
        
        $round = (int)$this->game->getGameStateValue('round_number');
        $faceIndex = (int)$this->game->getGameStateValue('round_cube_face');
        $lastCubeRound = (int)$this->game->getGameStateValue('last_cube_round', 0);
        
        error_log('🎲 RoundEvent::getArgs() - round: ' . $round . ', faceIndex: ' . $faceIndex . ', lastCubeRound: ' . $lastCubeRound);
        
        // Действие 2: Бросок кости — сохраняем значение в round_cube_face и round_cube_paei_count
        if ($lastCubeRound !== $round || $faceIndex < 0 || $faceIndex >= count($this->game->getCubeFaces())) {
            error_log('🎲 RoundEvent::getArgs() - Rolling NEW cube for round ' . $round);
            $cubeFace = $this->game->rollRoundCube();
            $this->game->setGameStateValue('last_cube_round', $round);
            error_log('🎲 RoundEvent::getArgs() - Cube rolled: ' . $cubeFace);
        } else {
            $faces = $this->game->getCubeFaces();
            $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
            error_log('🎲 RoundEvent::getArgs() - Using existing cube face for round ' . $round . ': ' . $cubeFace);
        }
        
        // Действие 1: Карта событий раунда — выбираем из колоды и кладём на стол
        $lastEventCardsRound = (int)$this->game->getGameStateValue('last_event_cards_round', 0);
        if ($lastEventCardsRound !== $round) {
            error_log('🎲 RoundEvent::getArgs() - Preparing NEW event cards for round ' . $round);
            $roundEventCards = $this->game->prepareRoundEventCard();
            $this->game->setGameStateValue('last_event_cards_round', $round);
            error_log('🎲 RoundEvent::getArgs() - Event cards prepared: ' . count($roundEventCards));
        } else {
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
            'cubeFacePaeiCount' => $this->game->getRoundCubePaeiCount(), // Количество символов PAEI на грани (1 или 2) для расчёта эффекта карты события
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
            return 'toNextPlayer';
        }

        // Очередь хода в этом раунде — по положению на треке навыков (левее = раньше ход в этом раунде)
        $nextRoundOrder = $this->game->takeNextRoundPlayerOrder();
        if ($nextRoundOrder !== null && $nextRoundOrder !== []) {
            $this->game->setCurrentRoundPlayerOrder($nextRoundOrder);
            $firstPlayerId = $nextRoundOrder[0];
            $this->game->gamestate->changeActivePlayer($firstPlayerId);
            error_log('🎲 RoundEvent - Порядок хода по треку навыков: ' . json_encode($nextRoundOrder) . ', первый: ' . $firstPlayerId);
        } else {
            // Первый раунд: порядка ещё нет — задаём по порядку стола, чтобы фаза навыков не пропускала игроков
            $tableOrder = array_map('intval', array_keys($this->game->loadPlayersBasicInfos()));
            $this->game->setCurrentRoundPlayerOrder($tableOrder);
            $firstPlayerId = $tableOrder[0];
            $this->game->gamestate->changeActivePlayer($firstPlayerId);
            error_log('🎲 RoundEvent - Первый раунд, порядок по столу: ' . json_encode($tableOrder) . ', первый: ' . $firstPlayerId);
        }
        // В начале раунда (фаза «Событие») жетоны навыков возвращаются на начальные позиции и сбрасываем временные выборы фазы навыков
        $this->game->clearAllSkillTokens();
        $this->game->clearSkillsPhaseChoices();

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

        // Действие 2: бросок кости (значение сохраняется в round_cube_face, round_cube_paei_count)
        if ($lastCubeRound !== $round) {
            error_log('🎲 RoundEvent::onEnteringState() - Rolling NEW cube for round ' . $round);
            $cubeFace = $this->game->rollRoundCube();
            $this->game->setGameStateValue('last_cube_round', $round);
            error_log('🎲 RoundEvent::onEnteringState() - Cube rolled: ' . $cubeFace);
        } else {
            $faceIndex = (int)$this->game->getGameStateValue('round_cube_face');
            $faces = $this->game->getCubeFaces();
            $cubeFace = ($faceIndex >= 0 && $faceIndex < count($faces)) ? $faces[$faceIndex] : '';
            error_log('🎲 RoundEvent::onEnteringState() - Using existing cube face for round ' . $round . ': ' . $cubeFace);
        }
        
        // Действие 1: карта событий раунда (берётся из колоды, кладётся на стол)
        $lastEventCardsRound = (int)$this->game->getGameStateValue('last_event_cards_round', 0);
        if ($lastEventCardsRound !== $round) {
            error_log('🎲 RoundEvent::onEnteringState() - Preparing NEW event cards for round ' . $round);
            $eventCards = $this->game->prepareRoundEventCard();
            $this->game->setGameStateValue('last_event_cards_round', $round);
            error_log('🎲 RoundEvent::onEnteringState() - Event cards prepared: ' . count($eventCards));
        } else {
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
        
        $currentOrder = $this->game->getCurrentRoundPlayerOrder();
        if ($currentOrder === null || $currentOrder === []) {
            $currentOrder = array_map('intval', array_keys($this->game->loadPlayersBasicInfos()));
        }
        $this->notify->all('roundStart', clienttranslate('Начало раунда ${round}'), [ // Уведомление о начале раунда
            'round' => $round, // Текущий раунд
            'roundName' => $this->game->getRoundName($round), // Название этапа
            'cubeFace' => $cubeFace, // Значение кубика на раунд
            'cubeFacePaeiCount' => $this->game->getRoundCubePaeiCount(), // Количество символов PAEI (1 или 2) для эффекта карты события
            'phaseName' => $phaseName, // Название фазы
            'phaseNumber' => $phaseNumber, // Номер фазы
            'phaseKey' => 'event', // Ключ фазы
            'roundEventCards' => $eventCards,
            'eventCard' => $eventCards[0] ?? null,
            'founders' => $this->game->getFoundersByPlayer(),
            'currentRoundPlayerOrder' => $currentOrder, // Порядок хода в раунде (слева направо на треке жетонов)
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
        // Всегда возвращаем переход к NextPlayer по имени перехода (не классу).
        return 'toNextPlayer';
    }
}


