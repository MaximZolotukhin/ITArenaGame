<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

/**
 * Эффект «нанять N дополнительных специалистов с руки за стоимость» в фазе найма.
 * Не выполняет найм сам — увеличивает лимит слотов на фазу (см. Game::applyHiringPhaseEffectBonuses).
 */
class HireFromHandEffectHandler implements EffectHandlerInterface
{
    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        $amount = $this->parsePositiveAmount($effectValue);
        if ($amount <= 0) {
            return [
                'type' => 'hire_from_hand',
                'amount' => 0,
                'message' => 'Дополнительный найм: некорректное значение эффекта',
            ];
        }

        return [
            'type' => 'hire_from_hand',
            'amount' => $amount,
            'sourceName' => $cardData['name'] ?? '',
            'message' => 'Дополнительный найм с руки: +' . $amount . ' слот(а) в этой фазе',
        ];
    }

    private function parsePositiveAmount(mixed $effectValue): int
    {
        if (is_int($effectValue) || is_float($effectValue)) {
            return max(0, (int) $effectValue);
        }
        $effectValueStr = trim((string) $effectValue);
        $cleanValue = str_replace(' ', '', $effectValueStr);
        if (preg_match('/^([+-]?)(\d+)$/', $cleanValue, $matches)) {
            $sign = $matches[1] === '-' ? -1 : 1;
            $n = $sign * (int) $matches[2];

            return max(0, $n);
        }

        return max(0, (int) $cleanValue);
    }
}
