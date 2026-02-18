<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Обработчик эффекта изменения баджерсов
 * Обрабатывает как положительные (+4), так и отрицательные (-1) значения
 */
class BadgerEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        
        // Парсим значение: '+ 4' -> +4, '- 2' -> -2, '+ 7' -> 7
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
        
        error_log("BadgerEffectHandler::apply - Player: $playerId, OriginalValue: $effectValueStr, CleanValue: $cleanValue, Amount: $amount");
        
        // Баджерсы хранятся в БД (player_game_data.badgers)
        $currentBadgers = $this->game->getPlayerBadgersForCheck($playerId);
        if ($amount === 0) {
            return [
                'type' => 'badger',
                'amount' => 0,
                'oldValue' => $currentBadgers,
                'newValue' => $currentBadgers,
                'message' => 'Эффект баджерсов не применён (значение 0)',
            ];
        }

        error_log("🔵 BadgerEffectHandler::apply - Current badgers for player $playerId (from DB): $currentBadgers");

        if ($amount > 0) {
            if (!$this->game->withdrawBadgersFromBank($amount)) {
                error_log("BadgerEffectHandler::apply - ERROR: Failed to withdraw $amount badgers from bank");
                return [
                    'type' => 'badger',
                    'amount' => 0,
                    'oldValue' => $currentBadgers,
                    'newValue' => $currentBadgers,
                    'message' => 'Недостаточно баджерсов в банке',
                ];
            }
            $this->game->addPlayerBadgers($playerId, $amount);
        } else {
            $decreaseAmount = min(abs($amount), $currentBadgers);
            $this->game->deductPlayerBadgers($playerId, $decreaseAmount);
            $this->game->depositBadgersToBank($decreaseAmount);
        }

        $newBadgers = $this->game->getPlayerBadgersForCheck($playerId);
        error_log("🔵 BadgerEffectHandler::apply - Updated badgers from $currentBadgers to $newBadgers for player $playerId");
        
        // Формируем сообщение для уведомления
        $actionText = $amount > 0 ? 'получает' : 'теряет';
        $absAmount = abs($amount);
        
        return [
            'type' => 'badger',
            'amount' => $amount,
            'oldValue' => $currentBadgers,
            'newValue' => $newBadgers,
            'message' => "Игрок $actionText {$absAmount}Б благодаря эффекту карты «{$cardData['name']}»",
            'founderName' => $cardData['name'],
        ];
    }
}

