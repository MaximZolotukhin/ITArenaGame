<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Обработчик эффекта выдачи задач
 * Сохраняет информацию о необходимости выбора задач через UI
 */
class TaskEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        // Парсим значение: '+ 3' -> 3
        $effectValueStr = (string)$effectValue;
        $cleanValue = str_replace(' ', '', $effectValueStr);
        $amount = (int)$cleanValue;
        
        error_log("TaskEffectHandler::apply - Player: $playerId, CleanValue: $cleanValue, Amount: $amount");
        
        if ($amount <= 0) {
            return [
                'type' => 'task',
                'amount' => 0,
                'message' => 'Эффект выдачи задач не применён (значение <= 0)',
            ];
        }
        
        // Сохраняем информацию о необходимости выбора задач
        // Игрок должен выбрать задачи через UI, а не получать их автоматически
        $this->game->globals->set('pending_task_selection_' . $playerId, json_encode([
            'amount' => $amount,
            'founder_id' => $cardData['id'] ?? 0,
            'founder_name' => $cardData['name'] ?? '',
        ]));
        
        error_log("TaskEffectHandler::apply - Player $playerId: Pending task selection saved, amount: $amount");
        
        return [
            'type' => 'task',
            'amount' => $amount,
            'message' => "Игрок должен выбрать $amount задач",
            'requires_selection' => true, // Флаг, что требуется выбор от игрока
        ];
    }
}

