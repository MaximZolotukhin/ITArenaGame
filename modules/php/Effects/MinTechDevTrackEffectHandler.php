<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Улучшение колонки техотдела с минимальной позицией жетона (на 1+ пункт).
 * Используется основателями/картами с activationStage «SprintPhase» и effect minTechDevTrack.
 */
class MinTechDevTrackEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        $requiredDepartment = $this->parseRequiredDepartment($effectValue);
        if ($requiredDepartment !== null) {
            $placed = $this->game->getFounderForPlayer($playerId);
            $placedDept = strtolower(trim((string) ($placed['department'] ?? '')));
            if ($placed === null || $placedDept !== $requiredDepartment) {
                return [
                    'type' => 'minTechDevTrack',
                    'amount' => 0,
                    'skipped' => true,
                    'message' => 'Эффект не активен в текущем отделе основателя',
                ];
            }
        }

        $amount = $this->parseAmount($effectValue);
        if ($amount <= 0) {
            return [
                'type' => 'minTechDevTrack',
                'amount' => 0,
                'message' => 'minTechDevTrack: некорректное значение',
            ];
        }

        $cardId = (int) ($cardData['id'] ?? 0);
        $round = (int) $this->game->getGameStateValue('round_number');
        $guardKey = 'founder_effect_minTechDevTrack_' . $playerId . '_' . $cardId . '_round_' . $round;
        if ($this->game->globals->get($guardKey, '') === '1') {
            return [
                'type' => 'minTechDevTrack',
                'amount' => 0,
                'skipped' => true,
                'message' => 'Уже применено в этом раунде',
            ];
        }

        $values = [];
        for ($col = 1; $col <= 4; $col++) {
            $values[$col] = $this->game->getTechDevColumnEffectiveValue($playerId, $col);
        }
        $minValue = min($values);
        $allowedColumns = [];
        foreach ($values as $col => $value) {
            if ($value === $minValue) {
                $allowedColumns[] = (int) $col;
            }
        }

        if (empty($allowedColumns)) {
            return [
                'type' => 'minTechDevTrack',
                'amount' => 0,
                'message' => 'minTechDevTrack: колонка не найдена',
            ];
        }

        $sourceName = (string) ($cardData['name'] ?? '');
        $pendingData = [
            'move_count' => $amount,
            'founder_name' => $sourceName,
            'source_name' => $sourceName,
            'allowed_columns' => $allowedColumns,
            'effect_type' => 'minTechDevTrack',
            'card_id' => $cardId,
            'round' => $round,
            'min_value' => $minValue,
            'message' => clienttranslate('Выберите минимальный трек техотдела для улучшения благодаря «${source_name}»'),
        ];
        $this->game->globals->set(
            'pending_technical_development_moves_' . $playerId,
            json_encode($pendingData, JSON_UNESCAPED_UNICODE),
        );
        $this->game->globals->set($guardKey, '1');

        return [
            'type' => 'minTechDevTrack',
            'pending' => true,
            'moveCount' => $amount,
            'amount' => $amount,
            'allowedColumns' => $allowedColumns,
            'minValue' => $minValue,
            'founderName' => $sourceName,
            'sourceName' => $sourceName,
            'cardId' => $cardId,
        ];
    }

    private function parseRequiredDepartment(mixed $effectValue): ?string
    {
        if (!is_array($effectValue)) {
            return null;
        }
        $item = isset($effectValue[0]) && is_array($effectValue[0]) ? $effectValue[0] : $effectValue;
        $dept = $item['requiredDepartment'] ?? $item['department'] ?? null;
        if ($dept === null || trim((string) $dept) === '') {
            return null;
        }

        return strtolower(trim((string) $dept));
    }

    private function parseAmount(mixed $effectValue): int
    {
        if (is_array($effectValue)) {
            $item = isset($effectValue[0]) && is_array($effectValue[0]) ? $effectValue[0] : $effectValue;
            $effectValue = $item['amount'] ?? '+ 1';
        }
        $cleanValue = str_replace(' ', '', trim((string) $effectValue));
        if (preg_match('/^([+-]?)(\d+)$/', $cleanValue, $matches)) {
            $sign = ($matches[1] ?? '') === '-' ? -1 : 1;

            return $sign * (int) ($matches[2] ?? 0);
        }

        return max(0, (int) $cleanValue);
    }
}
