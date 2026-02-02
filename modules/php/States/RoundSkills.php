<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;
use Bga\Games\itarenagame\SkillsData;

/**
 * Фаза «Навыки» (Skills) раунда.
 * Игрок выбирает навык (жетон из round-panel__skill-token-column размещает в round-panel__skill-column).
 * Навык сохраняется на раунд и применяется эффект.
 */
class RoundSkills extends \Bga\GameFramework\States\GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 16,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose a skill for this round'),
            descriptionMyTurn: clienttranslate('${you} must choose a skill for this round'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        return [
            'phaseKey' => 'skills',
            'phaseName' => $this->game->getPhaseName('skills'),
            'skillOptions' => SkillsData::getSkillsForSelection(),
        ];
    }

    /**
     * При входе в фазу навыков только выставляем имя фазы; переход — по действию игрока.
     */
    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'skills');
        return null; // остаёмся в состоянии до действия игрока
    }

    /**
     * Игрок выбрал навык: сохраняем, применяем эффект. Ход не передаётся — игрок нажимает «Завершить фазу навыков».
     */
    #[PossibleAction]
    public function actSelectSkill(string $skillKey): ?string
    {
        $this->game->checkAction('actSelectSkill');
        if (!SkillsData::isValidKey($skillKey)) {
            throw new UserException(clienttranslate('Invalid skill selected'));
        }
        $playerId = (int)$this->game->getActivePlayerId();
        $this->game->applySkillEffects($playerId, $skillKey);
        $skill = SkillsData::getSkill($skillKey);
        $this->notify->all('skillSelected', clienttranslate('${player_name} chose skill: ${skill_name}'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'skill_key' => $skillKey,
            'skill_name' => $skill['name'] ?? $skillKey,
            'i18n' => ['skill_name'],
        ]);
        return null; // остаёмся в RoundSkills до нажатия «Завершить фазу навыков»
    }

    /**
     * Игрок завершил фазу навыков без выбора (пропуск).
     */
    #[PossibleAction]
    public function actCompleteSkillsPhase(): string
    {
        $this->game->checkAction('actCompleteSkillsPhase');
        $this->game->globals->set('skills_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    /**
     * Zombie mode: игрок вышел из игры — считаем фазу навыков завершённой и переходим в NextPlayer.
     */
    public function zombie(int $playerId): string
    {
        $this->game->globals->set('skills_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
