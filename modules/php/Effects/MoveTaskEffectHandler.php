<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Обработчик эффекта перемещения жетонов задач
 * Сохраняет информацию о необходимости перемещения жетонов через UI
 */
class MoveTaskEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        error_log("🎯🎯🎯 MoveTaskEffectHandler::apply - START: playerId=$playerId, effectValue=" . json_encode($effectValue) . ", cardData effect=" . json_encode($cardData['effect'] ?? null));
        
        // Для move_task эффект передается как массив, а не строка
        // Поэтому effectValue будет JSON строка или массив
        $moveConfig = null;
        
        // Если effectValue уже массив, используем его
        if (is_array($effectValue)) {
            $moveConfig = $effectValue;
            error_log("✅ MoveTaskEffectHandler::apply - effectValue is array: " . json_encode($moveConfig));
        }
        // Пытаемся декодировать JSON, если это строка
        elseif (is_string($effectValue) && !empty($effectValue)) {
            $decoded = json_decode($effectValue, true);
            if (is_array($decoded)) {
                $moveConfig = $decoded;
                error_log("✅ MoveTaskEffectHandler::apply - Decoded JSON string: " . json_encode($moveConfig));
            } else {
                error_log("❌ MoveTaskEffectHandler::apply - Failed to decode JSON string: $effectValue");
            }
        } else {
            error_log("❌ MoveTaskEffectHandler::apply - effectValue is not array or string: " . gettype($effectValue));
        }
        
        // Если не получилось, пытаемся получить из cardData (основатель: effect, навык: effects)
        if ($moveConfig === null && isset($cardData['effect']['move_task'])) {
            $moveConfig = $cardData['effect']['move_task'];
            error_log("✅ MoveTaskEffectHandler::apply - Got move_task from cardData[effect]: " . json_encode($moveConfig));
        }
        if ($moveConfig === null && isset($cardData['effects']['move_task'])) {
            $moveConfig = $cardData['effects']['move_task'];
            error_log("✅ MoveTaskEffectHandler::apply - Got move_task from cardData[effects] (skill): " . json_encode($moveConfig));
        }
        if ($moveConfig === null) {
            error_log("🔍 MoveTaskEffectHandler::apply - cardData effect keys: " . (isset($cardData['effect']) ? implode(', ', array_keys($cardData['effect'])) : 'NO EFFECT'));
        }
        
        // Если все еще null, создаем дефолтную конфигурацию
        if ($moveConfig === null) {
            error_log("❌❌❌ MoveTaskEffectHandler::apply - WARNING: Could not parse move_task effect, using defaults");
            $moveConfig = ['move_count' => 0, 'move_color' => 'any'];
        }
        
        $moveCount = (int)($moveConfig['move_count'] ?? 0);
        $moveColor = $moveConfig['move_color'] ?? 'any';
        
        error_log("🎯🎯🎯 MoveTaskEffectHandler::apply - Player: $playerId, MoveCount: $moveCount, MoveColor: $moveColor");
        
        if ($moveCount <= 0) {
            return [
                'type' => 'move_task',
                'move_count' => 0,
                'message' => 'Эффект перемещения задач не применён (количество ходов = 0)',
            ];
        }
        
        // Сохраняем информацию о необходимости перемещения задач
        // Игрок должен переместить задачи через UI
        $globalsKey = 'pending_task_moves_' . $playerId;
        $pendingMovesData = [
            'move_count' => $moveCount,
            'move_color' => $moveColor,
            'used_moves' => 0,
            'founder_id' => $cardData['id'] ?? 0,
            'founder_name' => $cardData['name'] ?? '',
        ];
        $pendingMovesJson = json_encode($pendingMovesData);
        
        error_log("🔍🔍🔍 MoveTaskEffectHandler::apply - Saving to globals key: $globalsKey");
        error_log("🔍🔍🔍 MoveTaskEffectHandler::apply - Data: $pendingMovesJson");
        
        $this->game->globals->set($globalsKey, $pendingMovesJson);
        
        // Проверяем, что данные действительно сохранились
        $savedData = $this->game->globals->get($globalsKey, null);
        if ($savedData === null) {
            error_log("❌❌❌ MoveTaskEffectHandler::apply - ERROR: Data was NOT saved to globals!");
        } else {
            error_log("✅✅✅ MoveTaskEffectHandler::apply - Data confirmed saved: $savedData");
        }
        
        error_log("MoveTaskEffectHandler::apply - Player $playerId: Pending task moves saved, move_count: $moveCount, move_color: $moveColor");
        
        return [
            'type' => 'move_task',
            'move_count' => $moveCount,
            'move_color' => $moveColor,
            'message' => "Игрок должен переместить задачи на $moveCount блоков",
            'requires_selection' => true, // Флаг, что требуется выбор от игрока
        ];
    }
}

