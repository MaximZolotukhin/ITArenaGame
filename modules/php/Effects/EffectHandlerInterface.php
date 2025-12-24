<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\Effects;

/**
 * Интерфейс для обработчиков эффектов карт
 */
interface EffectHandlerInterface
{
    /**
     * Применяет эффект к игроку
     * 
     * @param int $playerId ID игрока
     * @param mixed $effectValue Значение эффекта (например, '+ 4', '- 1', или массив для move_task)
     * @param array $cardData Данные карты (основателя или специалиста)
     * @return array Информация о применённом эффекте или null, если эффект не применён
     */
    public function apply(int $playerId, mixed $effectValue, array $cardData): array;
}

