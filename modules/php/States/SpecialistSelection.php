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
        
        // ВАЖНО: getArgs() вызывается ДО onEnteringState() и уже раздаёт карты
        // Здесь мы просто проверяем, что карты есть, и логируем для отладки
        $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        
        if (empty($handCardIds)) {
            error_log('SpecialistSelection::onEnteringState - WARNING: No cards in hand for player ' . $activePlayerId . '! Cards should have been dealt in getArgs().');
        } else {
            // Проверяем, что это массив ID, а не полных объектов
            if (isset($handCardIds[0]) && is_array($handCardIds[0]) && isset($handCardIds[0]['id'])) {
                // Это полные объекты - конвертируем в ID (на случай, если что-то пошло не так)
                error_log('SpecialistSelection::onEnteringState - Converting full card objects to IDs');
                $handCardIds = array_column($handCardIds, 'id');
                // ВАЖНО: Приводим все ID к целым числам
                $handCardIds = array_map('intval', $handCardIds);
                $this->game->globals->set('specialist_hand_' . $activePlayerId, json_encode($handCardIds));
            } else {
                // ВАЖНО: Приводим все ID к целым числам (JSON может сохранить их как строки)
                $handCardIds = array_map('intval', $handCardIds);
            }
            error_log('SpecialistSelection::onEnteringState - Player ' . $activePlayerId . ' has ' . count($handCardIds) . ' card IDs');
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
        
        // ВАЖНО: Проверяем, завершил ли игрок уже выбор специалистов
        // Если нет - это первый раз, когда он входит в SpecialistSelection
        // В этом случае нужно очистить specialist_hand_ (там могут быть карты от эффекта 'card')
        // и раздать новые 7 карт для выбора
        $selectionDone = $this->game->globals->get('specialist_selection_done_' . $activePlayerId, false);
        
        // ВАЖНО: Проверяем, есть ли уже карты на руке (getArgs() может вызываться несколько раз!)
        $existingHandJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
        $existingHandIds = !empty($existingHandJson) ? json_decode($existingHandJson, true) : [];
        $hasExistingCards = !empty($existingHandIds) && is_array($existingHandIds) && count($existingHandIds) > 0;
        
        error_log('SpecialistSelection::getArgs - Player: ' . $activePlayerId . 
                  ', Selection done: ' . ($selectionDone ? 'YES' : 'NO') . 
                  ', Has existing cards: ' . ($hasExistingCards ? 'YES (' . count($existingHandIds) . ')' : 'NO'));
        
        if (!$selectionDone && !$hasExistingCards) {
            // Игрок еще не завершил выбор И карт на руке нет - это первый раз
            // ВАЖНО: Всегда раздаём 7 новых карт для выбора, независимо от карт от эффекта
            // Карты от эффекта уже в player_specialists_ и не участвуют в выборе
            error_log('SpecialistSelection::getArgs - First time for player ' . $activePlayerId . ', dealing 7 new cards for selection...');
            
            // Очищаем временные данные выбора
            $this->game->globals->delete('specialist_hand_' . $activePlayerId);
            $this->game->globals->delete('specialist_selected_' . $activePlayerId);
            
            // Раздаём 7 новых карт для выбора (карты от эффекта уже учтены в getUsedSpecialistCardIds)
            $dealtCards = $this->game->dealSpecialistCards($activePlayerId, self::CARDS_TO_DEAL);
            
            // ВАЖНО: Проверяем структуру карт
            if (empty($dealtCards)) {
                error_log('SpecialistSelection::getArgs - ERROR: dealSpecialistCards returned empty array!');
                $handCardIds = [];
            } else {
                // Проверяем первую карту для отладки
                $firstCard = $dealtCards[0];
                error_log('SpecialistSelection::getArgs - First card structure: ' . json_encode(array_keys($firstCard)));
                error_log('SpecialistSelection::getArgs - First card ID: ' . ($firstCard['id'] ?? 'NOT SET') . ' (type: ' . gettype($firstCard['id'] ?? null) . ')');
                
            // Сохраняем ТОЛЬКО ID карт (чтобы не превысить лимит bga_globals)
            $handCardIds = array_column($dealtCards, 'id');
                
                // ВАЖНО: Проверяем, что array_column вернул ID
                if (empty($handCardIds)) {
                    error_log('SpecialistSelection::getArgs - ERROR: array_column returned empty! Trying manual extraction...');
                    // Пробуем вручную извлечь ID
                    $handCardIds = [];
                    foreach ($dealtCards as $card) {
                        if (isset($card['id'])) {
                            $handCardIds[] = (int)$card['id'];
                        } else {
                            error_log('SpecialistSelection::getArgs - ERROR: Card missing ID! Card keys: ' . json_encode(array_keys($card)));
                        }
                    }
                }
                
                // ВАЖНО: Приводим все ID к целым числам перед сохранением
                $handCardIds = array_map('intval', $handCardIds);
            }
            
            // ВАЖНО: Логируем для отладки
            error_log('SpecialistSelection::getArgs - Dealt ' . count($handCardIds) . ' card IDs to player ' . $activePlayerId);
            error_log('SpecialistSelection::getArgs - Dealt card IDs: ' . json_encode($handCardIds));
            error_log('SpecialistSelection::getArgs - Dealt cards count: ' . count($dealtCards));
            
            // Сохраняем в globals ТОЛЬКО если есть карты
            if (!empty($handCardIds)) {
            $this->game->globals->set('specialist_hand_' . $activePlayerId, json_encode($handCardIds));
            
            // ВАЖНО: Проверяем, что сохранилось правильно
            $savedJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
            $savedIds = !empty($savedJson) ? json_decode($savedJson, true) : [];
            error_log('SpecialistSelection::getArgs - Saved card IDs: ' . json_encode($savedIds));
            error_log('SpecialistSelection::getArgs - Saved IDs count: ' . count($savedIds));
                error_log('SpecialistSelection::getArgs - Saved IDs types: ' . json_encode(array_map('gettype', $savedIds)));
            } else {
                error_log('SpecialistSelection::getArgs - ERROR: No card IDs to save!');
            }
        } else if ($hasExistingCards) {
            // Карты уже есть - используем их (getArgs() вызван повторно)
            // ВАЖНО: Это должны быть 7 карт для выбора, а не карты от эффекта
            error_log('SpecialistSelection::getArgs - Using existing cards for player ' . $activePlayerId . ' (getArgs() called again)');
            $handCardIds = array_map('intval', $existingHandIds);
        } else {
            // Игрок уже завершил выбор - получаем существующие карты (если есть)
            error_log('SpecialistSelection::getArgs - Player ' . $activePlayerId . ' has selection done');
            $handCardIdsJson = $this->game->globals->get('specialist_hand_' . $activePlayerId, '');
            $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
            
            // ВАЖНО: Приводим все ID к целым числам (JSON может сохранить их как строки)
            $handCardIds = array_map('intval', $handCardIds);
            
            // Если карт нет (не должно быть, но на всякий случай)
            if (empty($handCardIds)) {
                error_log('SpecialistSelection::getArgs - WARNING: Player ' . $activePlayerId . ' has selection done but no hand cards!');
            }
        }
        
        // ВАЖНО: Убеждаемся, что все ID - это числа (на случай, если они были сохранены как строки)
        $handCardIds = array_map('intval', $handCardIds);
        
        // Получаем полные данные карт по ID
        $handCards = [];
        foreach ($handCardIds as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $handCards[] = $card;
            }
        }
        
        // ВАЖНО: Проверяем, что получили 7 карт, а не 3
        if (count($handCards) !== self::CARDS_TO_DEAL && count($handCards) > 0) {
            error_log('⚠️ SpecialistSelection::getArgs - WARNING: Expected ' . self::CARDS_TO_DEAL . ' cards, but got ' . count($handCards) . ' for player ' . $activePlayerId);
            error_log('⚠️ SpecialistSelection::getArgs - Hand card IDs count: ' . count($handCardIds));
        }
        
        // Получаем выбранные карты (если есть) - массив ID
        $selectedCardsJson = $this->game->globals->get('specialist_selected_' . $activePlayerId, '');
        $selectedCards = !empty($selectedCardsJson) ? json_decode($selectedCardsJson, true) : [];
        
        error_log('SpecialistSelection::getArgs - Active player: ' . $activePlayerId . 
                  ', Hand cards: ' . count($handCards) . ' (expected: ' . self::CARDS_TO_DEAL . '), Selected: ' . count($selectedCards));
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
        error_log('actToggleSpecialist - Raw JSON from globals: ' . var_export($handCardIdsJson, true));
        
        $handCardIds = !empty($handCardIdsJson) ? json_decode($handCardIdsJson, true) : [];
        error_log('actToggleSpecialist - Decoded handCardIds (before intval): ' . json_encode($handCardIds));
        error_log('actToggleSpecialist - handCardIds type: ' . gettype($handCardIds));
        error_log('actToggleSpecialist - handCardIds is_array: ' . (is_array($handCardIds) ? 'YES' : 'NO'));
        
        if (is_array($handCardIds) && !empty($handCardIds)) {
            error_log('actToggleSpecialist - First ID: ' . $handCardIds[0] . ' (type: ' . gettype($handCardIds[0]) . ')');
        }
        
        // ВАЖНО: Приводим все ID к целым числам (JSON может сохранить их как строки)
        $handCardIds = array_map('intval', $handCardIds);
        $cardId = (int)$cardId;
        
        // ВАЖНО: Логируем для отладки
        error_log('actToggleSpecialist - Player: ' . $activePlayerId . ', Card ID: ' . $cardId . ' (type: ' . gettype($cardId) . ')');
        error_log('actToggleSpecialist - Hand card IDs (after intval): ' . json_encode($handCardIds) . ' (count: ' . count($handCardIds) . ')');
        error_log('actToggleSpecialist - Hand card IDs types: ' . json_encode(array_map('gettype', $handCardIds)));
        error_log('actToggleSpecialist - Card ID in hand: ' . (in_array($cardId, $handCardIds, true) ? 'YES' : 'NO'));
        
        // Дополнительная проверка без строгого сравнения
        $foundWithoutStrict = in_array($cardId, $handCardIds, false);
        error_log('actToggleSpecialist - Card ID in hand (without strict): ' . ($foundWithoutStrict ? 'YES' : 'NO'));
        
        if (!is_array($handCardIds)) {
            error_log('actToggleSpecialist - ERROR: handCardIds is not an array! Type: ' . gettype($handCardIds));
            throw new UserException(clienttranslate('Ошибка: данные карт на руке повреждены'));
        }
        
        if (empty($handCardIds)) {
            error_log('actToggleSpecialist - ERROR: Player ' . $activePlayerId . ' has no cards in hand!');
            error_log('actToggleSpecialist - Checking if cards were deleted or not saved...');
            
            // ВАЖНО: Проверяем, может быть карты были удалены по ошибке
            // Попробуем получить их из getArgs() (но это не должно происходить в нормальной ситуации)
            $selectionDone = $this->game->globals->get('specialist_selection_done_' . $activePlayerId, false);
            if (!$selectionDone) {
                error_log('actToggleSpecialist - WARNING: Selection not done but no cards! This should not happen.');
                error_log('actToggleSpecialist - This might be a race condition or cards were deleted.');
            }
            
            throw new UserException(clienttranslate('У вас нет карт на руке'));
        }
        
        // ВАЖНО: Используем строгое сравнение после приведения типов
        $cardFound = in_array($cardId, $handCardIds, true);
        
        if (!$cardFound) {
            // Дополнительная проверка без строгого сравнения (на случай проблем с типами)
            $cardFoundLoose = in_array($cardId, $handCardIds, false);
            
            error_log('actToggleSpecialist - ERROR: Card ' . $cardId . ' (type: ' . gettype($cardId) . ') not in hand!');
            error_log('actToggleSpecialist - Available IDs: ' . json_encode($handCardIds));
            error_log('actToggleSpecialist - ID types: ' . json_encode(array_map('gettype', $handCardIds)));
            error_log('actToggleSpecialist - Found with loose comparison: ' . ($cardFoundLoose ? 'YES' : 'NO'));
            
            // Если найдено без строгого сравнения - это проблема типов, исправляем
            if ($cardFoundLoose) {
                error_log('actToggleSpecialist - WARNING: Card found with loose comparison but not strict! Type mismatch issue.');
                error_log('actToggleSpecialist - This should not happen after intval conversion. Checking again...');
                
                // Повторно приводим все к int и проверяем
                $handCardIds = array_map('intval', $handCardIds);
                $cardId = (int)$cardId;
                $cardFound = in_array($cardId, $handCardIds, true);
                
                if ($cardFound) {
                    error_log('actToggleSpecialist - FIXED: Card found after re-conversion!');
                } else {
                    error_log('actToggleSpecialist - STILL NOT FOUND after re-conversion!');
                    throw new UserException(clienttranslate('Эта карта не находится на вашей руке'));
                }
            } else {
            throw new UserException(clienttranslate('Эта карта не находится на вашей руке'));
            }
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
            // ВАЖНО: Проверяем, что карта еще не выбрана (защита от дубликатов)
            if (!in_array($cardId, $selectedCards, true)) {
                $selectedCards[] = $cardId;
                $action = 'selected';
            } else {
                error_log('actToggleSpecialist - WARNING: Card ' . $cardId . ' already selected!');
                $action = 'already_selected';
            }
        }
        
        // ВАЖНО: Убираем дубликаты перед сохранением
        $selectedCards = array_values(array_unique($selectedCards));
        
        // Сохраняем выбор (в JSON)
        $this->game->globals->set('specialist_selected_' . $activePlayerId, json_encode($selectedCards));
        
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
        
        // ВАЖНО: Получаем существующие закреплённые карты (от эффекта основателя)
        $existingSpecialistIdsJson = $this->game->globals->get('player_specialists_' . $activePlayerId, '');
        $existingSpecialistIds = !empty($existingSpecialistIdsJson) ? json_decode($existingSpecialistIdsJson, true) : [];
        if (!is_array($existingSpecialistIds)) {
            $existingSpecialistIds = [];
        }
        
        // Объединяем существующие карты (от эффекта) с выбранными картами
        $allSpecialistIds = array_merge($existingSpecialistIds, $keptCardIds);
        // Убираем дубликаты (на всякий случай)
        $allSpecialistIds = array_values(array_unique($allSpecialistIds));
        
        // Сохраняем все карты (от эффекта + выбранные)
        $this->game->globals->set('player_specialists_' . $activePlayerId, json_encode($allSpecialistIds));
        
        error_log('SpecialistSelection::actConfirmSpecialists - Existing cards from effect: ' . count($existingSpecialistIds));
        error_log('SpecialistSelection::actConfirmSpecialists - Selected cards: ' . count($keptCardIds));
        error_log('SpecialistSelection::actConfirmSpecialists - Total cards after merge: ' . count($allSpecialistIds));
        
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

