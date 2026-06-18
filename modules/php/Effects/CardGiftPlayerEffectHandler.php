<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SpecialistsData;

/**
 * Эффект: игрок берёт N карт из колоды найма на руку и дарит 1 случайную карту другому игроку.
 */
class CardGiftPlayerEffectHandler implements EffectHandlerInterface
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
                'type' => 'card_gift_player',
                'amount' => 0,
                'gift_amount' => 0,
                'message' => 'Эффект выдачи карт не применён (amount <= 0)',
            ];
        }

        if ($giftAmount <= 0) {
            $giftAmount = 1;
        }

        $needTotal = $amount + $giftAmount;
        $drawn = array_values(array_map('intval', $this->game->drawFromActiveDeck($needTotal)));

        if (empty($drawn)) {
            return [
                'type' => 'card_gift_player',
                'amount' => 0,
                'gift_amount' => 0,
                'message' => 'Нет доступных карт в колоде найма',
            ];
        }

        $giftCardId = 0;
        $selfCardIds = [];

        if (count($drawn) >= $needTotal) {
            $selfCardIds = array_slice($drawn, 0, $amount);
            $giftCardId = (int) $drawn[$amount];
        } elseif (count($drawn) > $giftAmount) {
            $selfCardIds = array_slice($drawn, 0, count($drawn) - $giftAmount);
            $giftCardId = (int) $drawn[count($drawn) - 1];
        } else {
            $selfCardIds = $drawn;
        }

        if (!empty($selfCardIds)) {
            $this->game->addSpecialistCardsToPlayerSpecialists($playerId, $selfCardIds);
        }

        $sourceName = (string) ($cardData['name'] ?? '');
        $requiresTargetPlayer = $giftCardId > 0;

        if ($requiresTargetPlayer) {
            $this->game->globals->set(
                'pending_card_gift_' . $playerId,
                json_encode([
                    'gift_card_id' => $giftCardId,
                    'gift_amount' => $giftAmount,
                    'requires_target_player' => true,
                    'founder_name' => $sourceName,
                    'founder_id' => (int) ($cardData['id'] ?? 0),
                ], JSON_UNESCAPED_UNICODE),
            );
        }

        $cardNames = [];
        foreach ($selfCardIds as $cardId) {
            $card = SpecialistsData::getCard((int) $cardId);
            if ($card) {
                $cardNames[] = $card['name'] ?? 'Карта #' . $cardId;
            }
        }

        return [
            'type' => 'card_gift_player',
            'amount' => count($selfCardIds),
            'gift_amount' => $requiresTargetPlayer ? $giftAmount : 0,
            'cardIds' => $selfCardIds,
            'cardNames' => $cardNames,
            'requires_target_player' => $requiresTargetPlayer,
            'founder_name' => $sourceName,
            'founderName' => $sourceName,
            'requires_selection' => $requiresTargetPlayer,
            'message' => $requiresTargetPlayer
                ? "Игрок получает " . count($selfCardIds) . " карт и должен выбрать соперника для подарка $giftAmount карты"
                : "Игрок получает " . count($selfCardIds) . " карт из колоды найма",
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
