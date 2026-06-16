<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Эффект: игрок выбирает N задач в бэклог и дарит 1 задачу другому игроку.
 */
class TaskGiftPlayerEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        $config = $this->parseEffectConfig($effectValue);
        $amount = (int) ($config['amount'] ?? 0);
        $giftAmount = (int) ($config['gift_amount'] ?? 1);

        if ($amount <= 0) {
            return [
                'type' => 'task_gift_player',
                'amount' => 0,
                'gift_amount' => 0,
                'message' => 'Эффект выдачи задач не применён (amount <= 0)',
            ];
        }

        if ($giftAmount <= 0) {
            $giftAmount = 1;
        }

        $sourceName = (string) ($cardData['name'] ?? '');
        $this->game->globals->set(
            'pending_task_selection_' . $playerId,
            json_encode([
                'amount' => $amount,
                'gift_amount' => $giftAmount,
                'requires_target_player' => true,
                'founder_name' => $sourceName,
                'founder_id' => (int) ($cardData['id'] ?? 0),
            ], JSON_UNESCAPED_UNICODE),
        );

        return [
            'type' => 'task_gift_player',
            'amount' => $amount,
            'gift_amount' => $giftAmount,
            'requires_target_player' => true,
            'founder_name' => $sourceName,
            'requires_selection' => true,
            'message' => "Игрок должен выбрать $amount задач и подарить $giftAmount задачу другому игроку",
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseEffectConfig(mixed $effectValue): array
    {
        if (is_array($effectValue)) {
            return $effectValue;
        }
        if (is_string($effectValue) && $effectValue !== '') {
            $decoded = json_decode($effectValue, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}
