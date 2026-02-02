<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame;

/**
 * Данные навыков для фазы «Навыки».
 * Игрок выбирает один из четырёх навыков (жетон в round-panel__skill-token-column
 * размещается в round-panel__skill-column с data-skill). Эффект навыка применяется на текущий раунд.
 */
class SkillsData
{
    public const SKILL_ELOQUENCE = 'eloquence';
    public const SKILL_DISCIPLINE = 'discipline';
    public const SKILL_INTELLECT = 'intellect';
    public const SKILL_FRUGALITY = 'frugality';

    /** Ключи навыков, допустимые для выбора */
    public const VALID_KEYS = [
        self::SKILL_ELOQUENCE,
        self::SKILL_DISCIPLINE,
        self::SKILL_INTELLECT,
        self::SKILL_FRUGALITY,
    ];

    /**
     * Проверяет, что ключ навыка допустим.
     */
    public static function isValidKey(string $key): bool
    {
        return in_array($key, self::VALID_KEYS, true);
    }

    /**
     * Возвращает список навыков для выбора (для getArgs / клиента).
     *
     * @return array<int, array{key: string, name: string, description: string}>
     */
    public static function getSkillsForSelection(): array
    {
        $all = self::getAllSkills();
        $result = [];
        foreach (self::VALID_KEYS as $key) {
            if (isset($all[$key])) {
                $result[] = $all[$key];
            }
        }
        return $result;
    }

    /**
     * Возвращает данные навыка по ключу.
     */
    public static function getSkill(string $key): ?array
    {
        $all = self::getAllSkills();
        return $all[$key] ?? null;
    }

    /**
     * Все навыки: ключ, название, описание, эффекты на раунд.
     * Эффекты в формате, совместимом с существующими обработчиками (badger, incomeTrack и т.д.).
     *
     * @return array<string, array{key: string, name: string, description: string, effects: array}>
     */
    public static function getAllSkills(): array
    {
        return [
            self::SKILL_ELOQUENCE => [
                'key' => self::SKILL_ELOQUENCE,
                'name' => clienttranslate('Красноречие'),
                'description' => clienttranslate('+2 к доходу в этом раунде'),
                'effects' => [
                    'incomeTrack' => 2,
                ],
            ],
            self::SKILL_DISCIPLINE => [
                'key' => self::SKILL_DISCIPLINE,
                'name' => clienttranslate('Дисциплина'),
                'description' => clienttranslate('+1 баджер в этом раунде'),
                'effects' => [
                    'badger' => 1,
                ],
            ],
            self::SKILL_INTELLECT => [
                'key' => self::SKILL_INTELLECT,
                'name' => clienttranslate('Интеллект'),
                'description' => clienttranslate('+1 к доходу и +1 баджер в этом раунде'),
                'effects' => [
                    'incomeTrack' => 1,
                    'badger' => 1,
                ],
            ],
            self::SKILL_FRUGALITY => [
                'key' => self::SKILL_FRUGALITY,
                'name' => clienttranslate('Бережливость'),
                'description' => clienttranslate('+2 баджера в этом раунде'),
                'effects' => [
                    'badger' => 2,
                ],
            ],
        ];
    }
}
