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
        
        if ($amount === 0) {
            return [
                'type' => 'badger',
                'amount' => 0,
                'message' => 'Эффект баджерсов не применён (значение 0)',
            ];
        }
        
        // Получаем текущее количество баджерсов через PlayerCounter
        $currentBadgers = $this->game->playerBadgers->get($playerId);
        error_log("🔵 BadgerEffectHandler::apply - Current badgers for player $playerId: $currentBadgers");
        
        // ВАЖНО: Проверяем баджерсы ВСЕХ игроков ДО обновления
        $allPlayers = array_keys($this->game->loadPlayersBasicInfos());
        error_log("🔵🔵🔵 BadgerEffectHandler::apply - Badgers BEFORE update for ALL players:");
        foreach ($allPlayers as $pId) {
            $pBadgers = $this->game->playerBadgers->get((int)$pId);
            error_log("🔵   Player $pId: $pBadgers badgers");
        }
        
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
            error_log("🔴🔴🔴 BadgerEffectHandler::apply - CALLING playerBadgers->inc($playerId, $amount)");
            $this->game->playerBadgers->inc($playerId, $amount);
            error_log("🔵 BadgerEffectHandler::apply - Incremented badgers for player $playerId by $amount");
        } else {
            // При отрицательном значении уменьшаем, но не ниже 0
            // и возвращаем баджерсы в банк
            $decreaseAmount = min(abs($amount), $currentBadgers);
            error_log("🔴🔴🔴 BadgerEffectHandler::apply - CALLING playerBadgers->inc($playerId, -$decreaseAmount)");
            $this->game->playerBadgers->inc($playerId, -$decreaseAmount);
            $this->game->depositBadgersToBank($decreaseAmount);
            error_log("🔵 BadgerEffectHandler::apply - Decremented badgers for player $playerId by $decreaseAmount");
        }
        
        // ВАЖНО: Проверяем баджерсы ВСЕХ игроков ПОСЛЕ обновления
        error_log("🔵🔵🔵 BadgerEffectHandler::apply - Badgers AFTER update for ALL players:");
        foreach ($allPlayers as $pId) {
            $pBadgers = $this->game->playerBadgers->get((int)$pId);
            error_log("🔵   Player $pId: $pBadgers badgers");
        }
        
        // Получаем новое значение
        $newBadgers = $this->game->playerBadgers->get($playerId);
        error_log("🔵 BadgerEffectHandler::apply - Updated badgers from $currentBadgers to $newBadgers for player $playerId");
        
        // ВАЖНО: Проверяем, что данные сохранились правильно
        $verifyBadgers = $this->game->playerBadgers->get($playerId);
        if ($verifyBadgers !== $newBadgers) {
            error_log("🔴🔴🔴 BadgerEffectHandler::apply - ERROR: Badgers mismatch! Expected: $newBadgers, Got: $verifyBadgers");
        }
        
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

