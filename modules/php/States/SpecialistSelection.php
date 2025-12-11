<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SpecialistsData;

/**
 * Состояние выбора карт сотрудников (после выбора основателей)
 * Игрок получает 7 карт и должен выбрать 3 из них
 */
class SpecialistSelection extends GameState
{
    // Количество карт, выдаваемых игроку
    private const CARDS_TO_DEAL = 7;
    
    // Количество карт, которые игрок должен оставить
    private const CARDS_TO_KEEP = 3;

    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 25, // ID состояния между FounderSelection(20) и RoundEvent
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose ${cards_to_keep} specialist cards to keep'),
            descriptionMyTurn: clienttranslate('${you} must choose ${cards_to_keep} specialist cards to keep'),
        );
    }

    /**
     * Метод вызывается при входе в состояние выбора карт сотрудников
     */
    public function onEnteringState(): void
    {
        $activePlayerId = $this->game->getActivePlayerId();
        if ($activePlayerId === null) {
            error_log('SpecialistSelection::onEnteringState - WARNING: No active player set!');
            $this->game->activeNextPlayer();
            $activePlayerId = $this->game->getActivePlayerId();
        }
        
        // Проверяем, раздали ли уже карты этому игроку
        // ВАЖНО: globals хранит только скалярные значения, используем JSON
        $dealtCardsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $dealtCards = !empty($dealtCardsJson) ? json_decode($dealtCardsJson, true) : null;
        
        if ($dealtCards === null || empty($dealtCards)) {
            // Раздаём 7 случайных карт сотрудников
            $dealtCards = $this->game->dealSpecialistCards((int)$activePlayerId, self::CARDS_TO_DEAL);
            $this->game->globals->set('specialist_hand_' . $activePlayerId, json_encode($dealtCards));
            
            error_log('SpecialistSelection::onEnteringState - Dealt ' . count($dealtCards) . ' cards to player ' . $activePlayerId);
            error_log('SpecialistSelection::onEnteringState - Card IDs: ' . json_encode(array_column($dealtCards, 'id')));
        } else {
            error_log('SpecialistSelection::onEnteringState - Player ' . $activePlayerId . ' already has ' . count($dealtCards) . ' cards');
        }
    }

    /**
     * Возвращает аргументы для состояния выбора карт сотрудников
     * ВАЖНО: getArgs() вызывается ДО onEnteringState(), поэтому раздача карт происходит здесь
     * ВАЖНО: Храним только ID карт в globals (полные данные слишком большие для bga_globals)
     */
    public function getArgs(): array
    {
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        
        if ($activePlayerIdRaw === null) {
            error_log('SpecialistSelection::getArgs - WARNING: No active player set!');
            $this->game->activeNextPlayer();
            $activePlayerIdRaw = $this->game->getActivePlayerId();
        }
        
        $activePlayerId = is_int($activePlayerIdRaw) ? $activePlayerIdRaw : (int)$activePlayerIdRaw;
        
        // Получаем ID карт на руке игрока (из JSON) - только ID, не полные данные!
        $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        
        // ВАЖНО: Если карт нет, раздаём их прямо здесь (getArgs вызывается ДО onEnteringState)
        if (empty($handCardIds)) {
            error_log('SpecialistSelection::getArgs - No cards for player ' . $activePlayerId . ', dealing now...');
            $dealtCards = $this->game->dealSpecialistCards($activePlayerId, self::CARDS_TO_DEAL);
            // Сохраняем ТОЛЬКО ID карт (чтобы не превысить лимит bga_globals)
            $handCardIds = array_column($dealtCards, 'id');
            $this->game->globals->set('specialist_hand_' . $activePlayerId, json_encode($handCardIds));
            error_log('SpecialistSelection::getArgs - Dealt ' . count($handCardIds) . ' card IDs to player ' . $activePlayerId);
        }
        
        // Получаем полные данные карт по ID
        $handCards = [];
        foreach ($handCardIds as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $handCards[] = $card;
            }
        }
        
        // Получаем выбранные карты (если есть) - массив ID
        $selectedCardsJson = $this->game->globals->get('specialist_selected_' . $activePlayerId, '');
        $selectedCards = !empty($selectedCardsJson) ? json_decode($selectedCardsJson, true) : [];
        
        error_log('SpecialistSelection::getArgs - Active player: ' . $activePlayerId . 
                  ', Hand cards: ' . count($handCards) . ', Selected: ' . count($selectedCards));
        error_log('SpecialistSelection::getArgs - Hand card IDs: ' . json_encode($handCardIds));
        
        return [
            "activePlayerId" => $activePlayerId,
            "handCards" => $handCards,
            "selectedCards" => $selectedCards,
            "cardsToKeep" => self::CARDS_TO_KEEP,
            "cardsDealt" => self::CARDS_TO_DEAL,
            // BGA шаблон требует snake_case для подстановки
            "cards_to_keep" => self::CARDS_TO_KEEP,
        ];
    }

    /**
     * Действие игрока: выбор/снятие выбора карты сотрудника
     */
    #[PossibleAction]
    public function actToggleSpecialist(int $cardId, int $activePlayerId)
    {
        $this->game->checkAction('actToggleSpecialist');
        
        // Получаем ID карт на руке (из JSON) - только ID!
        $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        
        if (!in_array($cardId, $handCardIds, true)) {
            throw new UserException(clienttranslate('Эта карта не находится на вашей руке'));
        }
        
        // Получаем текущий список выбранных карт (массив ID)
        $selectedCardsJson = $this->game->globals->get('specialist_selected_' . $activePlayerId, '');
        $selectedCards = !empty($selectedCardsJson) ? json_decode($selectedCardsJson, true) : [];
        
        // Переключаем выбор карты
        $cardIndex = array_search($cardId, $selectedCards);
        if ($cardIndex !== false) {
            // Карта уже выбрана - снимаем выбор
            array_splice($selectedCards, $cardIndex, 1);
            $action = 'deselected';
        } else {
            // Карта не выбрана - добавляем
            if (count($selectedCards) >= self::CARDS_TO_KEEP) {
                throw new UserException(clienttranslate('Вы уже выбрали максимальное количество карт'));
            }
            $selectedCards[] = $cardId;
            $action = 'selected';
        }
        
        // Сохраняем выбор (в JSON)
        $this->game->globals->set('specialist_selected_' . $activePlayerId, json_encode(array_values($selectedCards)));
        
        // Получаем данные карты по ID
        $cardData = SpecialistsData::getCard($cardId);
        
        // Уведомляем только текущего игрока
        $this->notify->player($activePlayerId, 'specialistToggled', '', [
            'card_id' => $cardId,
            'card' => $cardData,
            'action' => $action,
            'selected_count' => count($selectedCards),
            'cards_to_keep' => self::CARDS_TO_KEEP,
        ]);
        
        $this->game->giveExtraTime($activePlayerId);
        
        return null;
    }

    /**
     * Действие игрока: подтверждение выбора карт
     */
    #[PossibleAction]
    public function actConfirmSpecialists(int $activePlayerId)
    {
        $this->game->checkAction('actConfirmSpecialists');
        
        // Проверяем, что выбрано нужное количество карт (из JSON)
        $selectedCardIdsJson = $this->game->globals->get('specialist_selected_' . $activePlayerId, '');
        $selectedCardIds = !empty($selectedCardIdsJson) ? json_decode($selectedCardIdsJson, true) : [];
        
        if (count($selectedCardIds) !== self::CARDS_TO_KEEP) {
            throw new UserException(sprintf(
                clienttranslate('Вы должны выбрать ровно %d карты'),
                self::CARDS_TO_KEEP
            ));
        }
        
        // Получаем ID всех карт на руке (из JSON) - только ID!
        $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        
        // Разделяем ID карт на оставляемые и сбрасываемые
        $keptCardIds = $selectedCardIds;
        $discardedCardIds = array_diff($handCardIds, $selectedCardIds);
        
        // Сохраняем ID оставленных карт (только ID для экономии места!)
        $this->game->globals->set('player_specialists_' . $activePlayerId, json_encode(array_values($keptCardIds)));
        
        // Добавляем сброшенные карты в стопку сброса (в JSON)
        $discardPileJson = $this->game->globals->get('specialist_discard_pile', '');
        $discardPile = !empty($discardPileJson) ? json_decode($discardPileJson, true) : [];
        $discardPile = array_merge($discardPile, array_values($discardedCardIds));
        $this->game->globals->set('specialist_discard_pile', json_encode($discardPile));
        
        // Очищаем временные данные
        $this->game->globals->delete('specialist_hand_' . $activePlayerId);
        $this->game->globals->delete('specialist_selected_' . $activePlayerId);
        
        // Помечаем, что игрок завершил выбор
        $this->game->globals->set('specialist_selection_done_' . $activePlayerId, true);
        
        error_log('SpecialistSelection::actConfirmSpecialists - Player ' . $activePlayerId . 
                  ' kept ' . count($keptCardIds) . ' cards, discarded ' . count($discardedCardIds));
        
        // Уведомляем всех о завершении выбора
        $this->notify->all('specialistsConfirmed', clienttranslate('${player_name} выбрал карты сотрудников'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'kept_count' => count($keptCardIds),
            'discarded_count' => count($discardedCardIds),
        ]);
        
        $this->game->giveExtraTime($activePlayerId);
        
        // Переходим к следующему игроку
        return NextPlayer::class;
    }

    /**
     * Zombie turn handling
     */
    function zombie(int $playerId)
    {
        // Для зомби-игрока выбираем первые 3 карты автоматически
        // Получаем ID карт на руке (только ID!)
        $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $playerId, '');
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        
        // Выбираем первые 3 ID
        $keptCardIds = array_slice($handCardIds, 0, self::CARDS_TO_KEEP);
        $discardedCardIds = array_slice($handCardIds, self::CARDS_TO_KEEP);
        
        // Сохраняем выбранные ID
        $this->game->globals->set('player_specialists_' . $playerId, json_encode($keptCardIds));
        
        // Добавляем сброшенные в стопку сброса
        $discardPileJson = $this->game->globals->get('specialist_discard_pile', '');
        $discardPile = !empty($discardPileJson) ? json_decode($discardPileJson, true) : [];
        $discardPile = array_merge($discardPile, $discardedCardIds);
        $this->game->globals->set('specialist_discard_pile', json_encode($discardPile));
        
        $this->game->globals->delete('specialist_hand_' . $playerId);
        $this->game->globals->delete('specialist_selected_' . $playerId);
        $this->game->globals->set('specialist_selection_done_' . $playerId, true);
        
        return NextPlayer::class;
    }
}

