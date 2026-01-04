<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

use Bga\Games\itarenagame\Game;

/**
 * Обработчик эффекта обновления нескольких треков
 * Обрабатывает структуру: ['updateTrack' => [['track' => 'income-track', 'amount' => '+ 2'], ...]]
 */
class UpdateTrackEffectHandler implements EffectHandlerInterface
{
    public function __construct(
        private Game $game
    ) {}

    /**
     * Получает PlayerCounter для указанного типа трека
     * @param string $trackId Идентификатор трека (например, 'income-track')
     * @return \Bga\GameFramework\Components\Counters\PlayerCounter|null
     */
    private function getTrackCounter(string $trackId): ?\Bga\GameFramework\Components\Counters\PlayerCounter
    {
        return match ($trackId) {
            'income-track' => $this->game->playerEnergy,
            // В будущем можно добавить другие треки:
            // 'sprint-track' => $this->game->playerSprintTrack,
            // 'task-track' => $this->game->playerTaskTrack,
            default => null,
        };
    }

    /**
     * Получает название трека для сообщений
     * @param string $trackId Идентификатор трека
     * @return string Название трека
     */
    private function getTrackName(string $trackId): string
    {
        return match ($trackId) {
            'income-track' => 'трек дохода',
            'player-department-back-office-evolution-column-1' => 'колонка эволюции бэк-офиса',
            'player-department-back-office-evolution-column-2' => 'трек найма в бэк-офисе',
            'player-department-back-office-evolution-column-3' => 'трек задач в бэк-офисе',
            'player-department-technical-development' => 'развитие техотдела',
            // В будущем можно добавить другие треки:
            // 'sprint-track' => 'трек спринта',
            // 'task-track' => 'трек задач',
            default => $trackId,
        };
    }

    /**
     * Парсит значение эффекта
     * @param mixed $effectValue Значение эффекта (например, '+ 2')
     * @return int Изменение значения
     */
    private function parseAmount(mixed $effectValue): int
    {
        $effectValueStr = trim((string)$effectValue);
        error_log("UpdateTrackEffectHandler::parseAmount - Input: " . var_export($effectValue, true) . ", trimmed: '$effectValueStr'");
        
        $cleanValue = str_replace(' ', '', $effectValueStr);
        error_log("UpdateTrackEffectHandler::parseAmount - Cleaned: '$cleanValue'");
        
        // Используем регулярное выражение для извлечения знака и числа
        // После удаления пробелов ищем просто знак и число: '+2', '-2', '2'
        // УБРАЛИ \s* так как пробелы уже удалены
        error_log("UpdateTrackEffectHandler::parseAmount - Testing regex on: '$cleanValue'");
        if (preg_match('/^([+-]?)(\d+)$/', $cleanValue, $matches)) {
            $signStr = $matches[1] ?? '';
            $sign = ($signStr === '-') ? -1 : 1;
            $number = (int)($matches[2] ?? 0);
            $result = $sign * $number;
            error_log("UpdateTrackEffectHandler::parseAmount - Parsed: signStr='$signStr', sign=$sign, number=$number, result=$result");
            if ($result === 0 && $number > 0) {
                error_log("UpdateTrackEffectHandler::parseAmount - WARNING: Result is 0 but number is $number! This should not happen!");
            }
            return $result;
        }
        
        // Fallback: удаляем знак + и преобразуем в число
        $fallbackValue = ltrim($cleanValue, '+');
        $fallbackResult = (int)$fallbackValue;
        error_log("UpdateTrackEffectHandler::parseAmount - Fallback: removed '+', got '$fallbackValue', result: $fallbackResult");
        return $fallbackResult;
    }

    public function apply(int $playerId, mixed $effectValue, array $cardData): array
    {
        error_log("UpdateTrackEffectHandler::apply - START: Player: $playerId");
        error_log("UpdateTrackEffectHandler::apply - effectValue type: " . gettype($effectValue));
        error_log("UpdateTrackEffectHandler::apply - effectValue: " . (is_array($effectValue) ? json_encode($effectValue) : (string)$effectValue));
        
        // effectValue должен быть массивом треков для обновления
        if (!is_array($effectValue)) {
            error_log("UpdateTrackEffectHandler::apply - ERROR: effectValue is not an array: " . gettype($effectValue));
            return [
                'type' => 'updateTrack',
                'amount' => 0,
                'message' => 'Неверный формат эффекта updateTrack',
            ];
        }

        error_log("UpdateTrackEffectHandler::apply - Player: $playerId, Tracks count: " . count($effectValue));
        error_log("🔧🔧🔧 UpdateTrackEffectHandler::apply - Full effectValue: " . json_encode($effectValue));
        error_log("🔧 UpdateTrackEffectHandler::apply - effectValue type: " . gettype($effectValue));
        error_log("🔧 UpdateTrackEffectHandler::apply - effectValue is_array: " . (is_array($effectValue) ? 'YES' : 'NO'));
        
        if (is_array($effectValue)) {
            error_log("🔧 UpdateTrackEffectHandler::apply - effectValue count: " . count($effectValue));
            foreach ($effectValue as $idx => $track) {
                error_log("🔧 UpdateTrackEffectHandler::apply - Track #$idx in effectValue: " . json_encode($track));
            }
        }
        
        $updatedTracks = [];
        $totalAmount = 0;

        // Обрабатываем каждый трек в массиве
        // ВАЖНО: Отслеживаем, какие треки уже обработаны, чтобы избежать двойной обработки
        $processedTracks = [];
        
        foreach ($effectValue as $index => $trackUpdate) {
            error_log("🔧 UpdateTrackEffectHandler::apply - Processing track #$index: " . json_encode($trackUpdate));
            error_log("🔧 UpdateTrackEffectHandler::apply - Track #$index type: " . gettype($trackUpdate));
            error_log("🔧 UpdateTrackEffectHandler::apply - Track #$index is_array: " . (is_array($trackUpdate) ? 'YES' : 'NO'));
            
            if (!is_array($trackUpdate)) {
                error_log("🔴 UpdateTrackEffectHandler::apply - Track #$index is NOT an array: " . gettype($trackUpdate) . " - " . var_export($trackUpdate, true));
                continue;
            }
            
            if (!isset($trackUpdate['track'])) {
                error_log("🔴 UpdateTrackEffectHandler::apply - Track #$index missing 'track' key: " . json_encode($trackUpdate));
                continue;
            }
            
            if (!isset($trackUpdate['amount'])) {
                error_log("🔴 UpdateTrackEffectHandler::apply - Track #$index missing 'amount' key: " . json_encode($trackUpdate));
                continue;
            }

            $trackId = $trackUpdate['track'];
            
            // ВАЖНО: Проверяем, не обработан ли уже этот трек
            if (isset($processedTracks[$trackId])) {
                error_log("🔴🔴🔴 UpdateTrackEffectHandler::apply - WARNING: Track $trackId already processed! Skipping duplicate.");
                continue;
            }
            
            $rawAmount = $trackUpdate['amount'] ?? 'N/A';
            error_log("UpdateTrackEffectHandler::apply - BEFORE parseAmount: trackId=$trackId, rawAmount=" . var_export($rawAmount, true));
            $amount = $this->parseAmount($trackUpdate['amount']);
            error_log("UpdateTrackEffectHandler::apply - AFTER parseAmount: trackId=$trackId, parsedAmount=$amount");
            
            error_log("UpdateTrackEffectHandler::apply - Processing track: $trackId, amount: $amount");

            if ($amount === 0) {
                continue;
            }

            // Получаем PlayerCounter для этого трека
            error_log("UpdateTrackEffectHandler::apply - Getting track counter for: $trackId");
            $trackCounter = $this->getTrackCounter($trackId);
            error_log("UpdateTrackEffectHandler::apply - Track counter result: " . ($trackCounter === null ? 'NULL' : get_class($trackCounter)));
            
            if ($trackCounter === null) {
                // Проверяем, требуется ли выбор колонки (column => 'any')
                $column = $trackUpdate['column'] ?? null;
                
                if ($trackId === 'player-department-technical-development' && $column === 'any') {
                    // Это эффект с выбором колонки - не обрабатываем здесь, будет обработан в FounderSelection
                    error_log("🔧🔧🔧 UpdateTrackEffectHandler::apply - Track $trackId requires column selection (column='any'), will be handled in FounderSelection");
                    error_log("🔧 UpdateTrackEffectHandler::apply - Adding track to updatedTracks with column='any'");
                    
                    $updatedTracks[] = [
                        'trackId' => $trackId,
                        'trackName' => $this->getTrackName($trackId),
                        'amount' => $amount,
                        'column' => 'any', // Флаг для выбора колонки
                        'oldValue' => 0,
                        'newValue' => 0, // Значение будет установлено после выбора игрока
                    ];
                    
                    error_log("🔧 UpdateTrackEffectHandler::apply - Added track to updatedTracks: " . json_encode($updatedTracks[count($updatedTracks) - 1]));
                    
                    $totalAmount += abs($amount);
                    $processedTracks[$trackId] = true; // Отмечаем как обработанный
                    continue;
                }
                
                // Для других визуальных треков (например, 'player-department-back-office-evolution-column-1')
                // это визуальный трек, который обрабатывается на клиенте
                // НО теперь мы сохраняем его в БД в таблице player_game_data
                error_log("UpdateTrackEffectHandler::apply - Track $trackId is a visual track (no PlayerCounter), updating in player_game_data table");
                
                // Определяем тип трека и колонку
                $oldValue = 0;
                $newValue = 0;
                
                if (preg_match('/player-department-back-office-evolution-column-(\d+)/', $trackId, $matches)) {
                    $column = (int)$matches[1];
                    $gameData = $this->game->getPlayerGameData($playerId);
                    $oldValue = $gameData ? ($gameData['backOfficeCol' . $column] ?? 0) : 0;
                    $newValue = $oldValue + $amount;
                    $this->game->setBackOfficeColumn($playerId, $column, $newValue);
                    error_log("UpdateTrackEffectHandler::apply - Updated backOfficeCol$column: $oldValue -> $newValue (amount: $amount)");
                } elseif (preg_match('/player-department-technical-development-column-(\d+)/', $trackId, $matches)) {
                    $column = (int)$matches[1];
                    $gameData = $this->game->getPlayerGameData($playerId);
                    $oldValue = $gameData ? ($gameData['techDevCol' . $column] ?? 0) : 0;
                    $newValue = $oldValue + $amount;
                    $this->game->setTechDevColumn($playerId, $column, $newValue);
                    error_log("UpdateTrackEffectHandler::apply - Updated techDevCol$column: $oldValue -> $newValue (amount: $amount)");
                } else {
                    // Для других визуальных треков просто добавляем в updatedTracks
                    $oldValue = 0;
                    $newValue = $amount;
                }
                
                $updatedTracks[] = [
                    'trackId' => $trackId,
                    'trackName' => $this->getTrackName($trackId),
                    'amount' => $amount,
                    'oldValue' => $oldValue,
                    'newValue' => $newValue,
                ];
                
                $totalAmount += abs($amount);
                continue; // Пропускаем обработку через PlayerCounter
            }
            
            if ($trackId === 'income-track') {
                error_log("🔵🔵🔵 UpdateTrackEffectHandler::apply - Processing income-track! Counter found: " . get_class($trackCounter));
            }

            // Получаем текущую позицию трека ДО изменения
            $currentValue = $trackCounter->get($playerId);
            error_log("UpdateTrackEffectHandler::apply - Current value for track $trackId: $currentValue, amount to add: $amount");
            
            // ВАЖНО: Проверяем, что значение правильное
            if ($trackId === 'income-track') {
                error_log("🔵 UpdateTrackEffectHandler::apply - income-track BEFORE: currentValue=$currentValue, amount=$amount, expected newValue=" . ($currentValue + $amount));
            }
            
            // Увеличиваем/уменьшаем позицию трека
            // ВАЖНО: Защита от двойного применения уже есть через $processedTracks в начале метода
            if ($amount > 0) {
                error_log("UpdateTrackEffectHandler::apply - Calling inc($playerId, $amount) for track $trackId");
                $trackCounter->inc($playerId, $amount);
            } else {
                // При отрицательном значении уменьшаем, но не ниже 0
                $decreaseAmount = min(abs($amount), $currentValue);
                error_log("UpdateTrackEffectHandler::apply - Calling inc($playerId, -$decreaseAmount) for track $trackId");
                $trackCounter->inc($playerId, -$decreaseAmount);
            }
            
            // Получаем новое значение ПОСЛЕ изменения
            $newValue = $trackCounter->get($playerId);
            error_log("UpdateTrackEffectHandler::apply - Updated track $trackId from $currentValue to $newValue for player $playerId (expected: " . ($currentValue + $amount) . ")");
            
            // ВАЖНО: Проверяем, что новое значение правильное
            if ($trackId === 'income-track') {
                $expectedValue = $currentValue + $amount;
                if ($newValue !== $expectedValue) {
                    error_log("🔴🔴🔴 UpdateTrackEffectHandler::apply - ERROR: income-track value mismatch! Expected: $expectedValue, Got: $newValue");
                } else {
                    error_log("✅ UpdateTrackEffectHandler::apply - income-track value correct: $currentValue + $amount = $newValue");
                }
            }
            
            $updatedTracks[] = [
                'trackId' => $trackId,
                'trackName' => $this->getTrackName($trackId),
                'amount' => $amount,
                'oldValue' => $currentValue,
                'newValue' => $newValue,
            ];
            
            // Отмечаем трек как обработанный
            $processedTracks[$trackId] = true;
            
            $totalAmount += abs($amount);
        }

        error_log("🔧 UpdateTrackEffectHandler::apply - Final updatedTracks count: " . count($updatedTracks));
        error_log("🔧 UpdateTrackEffectHandler::apply - Final updatedTracks: " . json_encode($updatedTracks));
        
        if (empty($updatedTracks)) {
            error_log("🔴 UpdateTrackEffectHandler::apply - WARNING: No tracks updated!");
            return [
                'type' => 'updateTrack',
                'amount' => 0,
                'message' => 'Нет треков для обновления',
            ];
        }

        // Формируем сообщение
        $trackNames = array_map(fn($t) => $t['trackName'], $updatedTracks);
        $tracksList = implode(', ', $trackNames);
        
        error_log("🔧 UpdateTrackEffectHandler::apply - Returning result with " . count($updatedTracks) . " tracks");
        
        return [
            'type' => 'updateTrack',
            'amount' => $totalAmount,
            'tracks' => $updatedTracks,
            'message' => "Игрок увеличивает $tracksList на $totalAmount благодаря эффекту карты «{$cardData['name']}»",
            'founderName' => $cardData['name'],
        ];
    }
}

