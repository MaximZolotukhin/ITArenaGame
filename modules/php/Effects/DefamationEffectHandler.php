<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Эффект «Диффамация»: −1 к треку найма соперника, +1 к своему (колонка 1 бэк-офиса).
 */
class DefamationEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        $config = $this->parseEffectConfig($effectValue);
        $amount = (int) ($config['amount'] ?? 1);
        if ($amount <= 0) {
            $amount = 1;
        }

        $sourceName = (string) ($cardData['name'] ?? '');
        $eligibleTargetIds = $this->game->getDefamationEligibleTargetIds($playerId);

        if ($eligibleTargetIds === []) {
            return [
                'type' => 'defamation',
                'amount' => $amount,
                'skipped' => true,
                'requires_target_player' => false,
                'founder_name' => $sourceName,
                'founderName' => $sourceName,
                'message' => 'Диффамация не применена: нет подходящих соперников',
            ];
        }

        $this->game->globals->set(
            'pending_defamation_' . $playerId,
            json_encode([
                'amount' => $amount,
                'founder_name' => $sourceName,
                'founder_id' => (int) ($cardData['id'] ?? 0),
                'requires_target_player' => true,
                'eligible_target_ids' => $eligibleTargetIds,
            ], JSON_UNESCAPED_UNICODE),
        );

        return [
            'type' => 'defamation',
            'amount' => $amount,
            'requires_target_player' => true,
            'requires_selection' => true,
            'founder_name' => $sourceName,
            'founderName' => $sourceName,
            'eligible_target_ids' => $eligibleTargetIds,
            'message' => 'Игрок должен выбрать соперника для диффамации (−1 / +1 трек найма)',
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
