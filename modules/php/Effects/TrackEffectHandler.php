<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Универсальный обработчик эффекта изменения треков
 * Обрабатывает различные типы треков: incomeTrack, sprintTrack, taskTrack и т.д.
 * Обрабатывает как положительные (+2), так и отрицательные (-1) значения
 */
class TrackEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    /**
     * Получает PlayerCounter для указанного типа трека
     * @param string $trackType Тип трека (income, sprint, task и т.д.)
     * @return \Bga\GameFramework\Components\Counters\PlayerCounter|null
     */
    private function getTrackCounter(string $trackType): ?\Bga\GameFramework\Components\Counters\PlayerCounter
    {
        return match ($trackType) {
            'income' => $this->game->playerEnergy,
            // В будущем можно добавить другие треки:
            // 'sprint' => $this->game->playerSprintTrack,
            // 'task' => $this->game->playerTaskTrack,
            default => null,
        };
    }

    /**
     * Получает название трека для сообщений
     * @param string $trackType Тип трека
     * @return string Название трека
     */
    private function getTrackName(string $trackType): string
    {
        return match ($trackType) {
            'income' => 'трек дохода',
            // В будущем можно добавить другие треки:
            // 'sprint' => 'трек спринта',
            // 'task' => 'трек задач',
            default => 'трек',
        };
    }

    /**
     * Определяет тип трека из ключа эффекта
     * Например: 'incomeTrack' -> 'income', 'sprintTrack' -> 'sprint'
     * @param string $effectKey Ключ эффекта (например, 'incomeTrack')
     * @return string Тип трека (например, 'income')
     */
    private function extractTrackType(string $effectKey): string
    {
        // Убираем суффикс 'Track' из ключа
        if (str_ends_with($effectKey, 'Track')) {
            return substr($effectKey, 0, -5); // Убираем 'Track' (5 символов)
        }
        
        // Если ключ не содержит 'Track', возвращаем как есть
        return $effectKey;
    }

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        // Получаем ключ эффекта из cardData (передается из processFounderEffectType)
        $effectKey = $cardData['_effectKey'] ?? 'incomeTrack';
        
        // Определяем тип трека из ключа эффекта
        $trackType = $this->extractTrackType($effectKey);
        $trackName = $this->getTrackName($trackType);
        
        // Получаем PlayerCounter для этого типа трека
        $trackCounter = $this->getTrackCounter($trackType);
        
        if ($trackCounter === null) {
            error_log("TrackEffectHandler::apply - ERROR: Unknown track type: $trackType");
            return [
                'type' => $effectKey,
                'amount' => 0,
                'message' => "Неизвестный тип трека: $trackType",
            ];
        }
        
        // Парсим значение: '+ 2' -> +2, '- 1' -> -1, '+ 2' -> 2
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
        
        error_log("TrackEffectHandler::apply - Player: $playerId, TrackType: $trackType, OriginalValue: $effectValueStr, CleanValue: $cleanValue, Amount: $amount");
        
        if ($amount === 0) {
            return [
                'type' => $effectKey,
                'amount' => 0,
                'message' => "Эффект $trackName не применён (значение 0)",
            ];
        }
        
        // Получаем текущую позицию трека через PlayerCounter
        $currentValue = $trackCounter->get($playerId);
        
        // Увеличиваем/уменьшаем позицию трека
        // При отрицательном значении уменьшаем, но не ниже 0
        if ($amount > 0) {
            $trackCounter->inc($playerId, $amount);
        } else {
            // При отрицательном значении уменьшаем, но не ниже 0
            $decreaseAmount = min(abs($amount), $currentValue);
            $trackCounter->inc($playerId, -$decreaseAmount);
        }
        
        // Получаем новое значение
        $newValue = $trackCounter->get($playerId);
        
        error_log("TrackEffectHandler::apply - Updated $trackName from $currentValue to $newValue for player $playerId");
        
        // Формируем сообщение для уведомления
        $actionText = $amount > 0 ? 'увеличивает' : 'уменьшает';
        $absAmount = abs($amount);
        
        return [
            'type' => $effectKey,
            'trackType' => $trackType,
            'amount' => $amount,
            'oldValue' => $currentValue,
            'newValue' => $newValue,
            'message' => "Игрок $actionText $trackName на {$absAmount} благодаря эффекту карты «{$cardData['name']}»",
            'founderName' => $cardData['name'],
        ];
    }
}

