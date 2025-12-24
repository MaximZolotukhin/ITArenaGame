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
        // Парсим значение: '+ 4' -> +4, '- 2' -> -2
        $effectValueStr = (string)$effectValue;
        $cleanValue = str_replace(' ', '', $effectValueStr);
        $amount = (int)$cleanValue;
        
        error_log("BadgerEffectHandler::apply - Player: $playerId, CleanValue: $cleanValue, Amount: $amount");
        
        if ($amount === 0) {
            return [
                'type' => 'badger',
                'amount' => 0,
                'message' => 'Эффект баджерсов не применён (значение 0)',
            ];
        }
        
        // Получаем текущее количество баджерсов через PlayerCounter
        $currentBadgers = $this->game->playerBadgers->get($playerId);
        
        // Добавляем/вычитаем баджерсы через PlayerCounter
        if ($amount > 0) {
            // Списываем баджерсы из банка
            if (!$this->game->withdrawBadgersFromBank($amount)) {
                error_log("BadgerEffectHandler::apply - ERROR: Failed to withdraw $amount badgers from bank");
                return [
                    'type' => 'badger',
                    'amount' => 0,
                    'message' => 'Недостаточно баджерсов в банке',
                ];
            }
            $this->game->playerBadgers->inc($playerId, $amount);
        } else {
            // При отрицательном значении уменьшаем, но не ниже 0
            // и возвращаем баджерсы в банк
            $decreaseAmount = min(abs($amount), $currentBadgers);
            $this->game->playerBadgers->inc($playerId, -$decreaseAmount);
            $this->game->depositBadgersToBank($decreaseAmount);
        }
        
        // Получаем новое значение
        $newBadgers = $this->game->playerBadgers->get($playerId);
        
        error_log("BadgerEffectHandler::apply - Updated badgers from $currentBadgers to $newBadgers for player $playerId");
        
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

