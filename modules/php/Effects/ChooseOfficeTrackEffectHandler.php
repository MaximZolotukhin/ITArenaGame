<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Эффект: игрок выбирает любой трек в офисе и улучшает его на N пунктов.
 */
class ChooseOfficeTrackEffectHandler implements EffectHandlerInterface
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
        $this->game->globals->set(
            'pending_office_track_choice_' . $playerId,
            json_encode([
                'amount' => $amount,
                'founder_name' => $sourceName,
                'founder_id' => (int) ($cardData['id'] ?? 0),
                'requires_selection' => true,
            ], JSON_UNESCAPED_UNICODE),
        );

        return [
            'type' => 'choose_office_track',
            'amount' => $amount,
            'requires_selection' => true,
            'founder_name' => $sourceName,
            'founderName' => $sourceName,
            'message' => "Игрок должен выбрать трек в офисе для улучшения на $amount",
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
