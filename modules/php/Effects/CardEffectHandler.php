<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SpecialistsData;

/**
 * Обработчик эффекта выдачи карт специалистов
 * Карты сразу закрепляются за игроком (player_specialists_) и не попадают в выбор из 7 карт
 */
class CardEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        // Парсим значение: '+ 3' -> 3, '+ 7' -> 7
        $effectValueStr = trim((string)$effectValue);
        $cleanValue = str_replace(' ', '', $effectValueStr);
        
        // Используем регулярное выражение для извлечения знака и числа
        if (preg_match('/^([+-]?)\s*(\d+)$/', $cleanValue, $matches)) {
            $sign = $matches[1] === '-' ? -1 : 1;
            $amount = $sign * (int)$matches[2];
        } else {
            // Fallback на старый способ
            $amount = (int)$cleanValue;
        }
        
        error_log("CardEffectHandler::apply - Player: $playerId, OriginalValue: $effectValueStr, CleanValue: $cleanValue, Amount: $amount");
        
        if ($amount <= 0) {
            return [
                'type' => 'card',
                'amount' => 0,
                'message' => 'Эффект выдачи карт специалистов не применён (значение <= 0)',
            ];
        }
        
        // Используем новую систему колод для получения карт
        // Берем карты из активной колоды (основная -> промежуточная -> колода сброса)
        $dealtCardIds = $this->game->drawFromActiveDeck($amount);
        
        if (empty($dealtCardIds)) {
            error_log("CardEffectHandler::apply - ERROR: No available specialist cards for player $playerId");
            return [
                'type' => 'card',
                'amount' => 0,
                'message' => 'Нет доступных карт специалистов для выдачи',
            ];
        }
        
        // Берём не больше доступных карт
        $cardsToDeal = min($amount, count($dealtCardIds));
        $dealtCardIds = array_slice($dealtCardIds, 0, $cardsToDeal);
        
        // ВАЖНО: Эффект 'card' сразу закрепляет карты за игроком (player_specialists_)
        // Эти карты НЕ попадают в specialist_hand_ и НЕ участвуют в выборе из 7 карт
        // Получаем текущие закреплённые карты
        $currentSpecialistIdsJson = $this->game->globals->get('player_specialists_' . $playerId, '');
        $currentSpecialistIds = !empty($currentSpecialistIdsJson) ? json_decode($currentSpecialistIdsJson, true) : [];
        if (!is_array($currentSpecialistIds)) {
            $currentSpecialistIds = [];
        }
        
        // Добавляем новые карты к существующим закреплённым
        $newSpecialistIds = array_merge($currentSpecialistIds, $dealtCardIds);
        
        // Сохраняем обновлённый список закреплённых карт
        $this->game->globals->set('player_specialists_' . $playerId, json_encode($newSpecialistIds));
        
        error_log("CardEffectHandler::apply - Player $playerId: Added $cardsToDeal specialist cards to player_specialists_ (locked). Total locked: " . count($newSpecialistIds));
        error_log("CardEffectHandler::apply - Dealt card IDs: " . json_encode($dealtCardIds));
        
        // Получаем данные выданных карт для сообщения
        $dealtCards = [];
        foreach ($dealtCardIds as $cardId) {
            $card = SpecialistsData::getCard((int)$cardId);
            if ($card) {
                $dealtCards[] = $card['name'] ?? 'Карта #' . $cardId;
            }
        }
        
        return [
            'type' => 'card',
            'amount' => $cardsToDeal,
            'cardIds' => $dealtCardIds,
            'cardNames' => $dealtCards,
            'message' => "Игрок получает {$cardsToDeal} карт специалистов благодаря эффекту карты «{$cardData['name']}»",
            'founderName' => $cardData['name'],
        ];
    }
}

