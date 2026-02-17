<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Найм» (Hiring) раунда.
 * Действия: 1) Рекрутинг — взять карты из колоды найма по треку найма в бэк-офисе; 2–3) позже.
 */
class RoundHiring extends \Bga\GameFramework\States\GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 17,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must complete the hiring phase'),
            descriptionMyTurn: clienttranslate('${you} must complete the hiring phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $hiringTrackValue = $this->game->getHiringTrackValue($activePlayerId);
        $recruitingDone = $this->game->globals->get('hiring_recruiting_done_' . $activePlayerId, '') === '1';
        return [
            'phaseKey' => 'hiring',
            'phaseName' => $this->game->getPhaseName('hiring'),
            'hiringTrackValue' => $hiringTrackValue,
            'recruitingDone' => $recruitingDone,
        ];
    }

    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'hiring');
        $activePlayerId = (int) $this->game->getActivePlayerId();
        $this->game->ensureSpecialistDeckInitialized();

        // Этап «собеседование»: при входе в фазу найма выдаём игроку hiringTrackInterview карт из колоды
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        $count = (int) ($gameData['hiringTrackInterview'] ?? 0);
        if ($count > 0) {
            $drawn = $this->game->drawFromActiveDeck($count);
            if (!empty($drawn)) {
                $this->game->addSpecialistCardsToPlayerSpecialists($activePlayerId, $drawn);
                $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} получает ${amount} карт из колоды найма (собеседование)'), [
                    'player_id' => $activePlayerId,
                    'player_name' => $this->game->getPlayerNameById($activePlayerId),
                    'cardIds' => $drawn,
                    'amount' => count($drawn),
                    'source' => 'hiring_recruiting',
                    'i18n' => [],
                ]);
                $this->game->globals->set('hiring_recruiting_done_' . $activePlayerId, '1');
            }
        }

        return null;
    }

    /**
     * Действие 1: Рекрутинг — взять из колоды найма столько карт, сколько показывает трек найма в бэк-офисе (колонка 1).
     * Карты добавляются в руку игрока.
     */
    #[PossibleAction]
    public function actRecruiting(): ?string
    {
        $this->game->checkAction('actRecruiting');
        $playerId = (int) $this->game->getActivePlayerId();
        if ($this->game->globals->get('hiring_recruiting_done_' . $playerId, '') === '1') {
            throw new UserException(clienttranslate('You have already performed recruiting this turn'));
        }
        $count = $this->game->getHiringTrackValue($playerId);
        $drawn = $this->game->drawFromActiveDeck($count);
        if (empty($drawn)) {
            // Не отмечаем recruiting_done, если карты реально не были выданы
            throw new UserException(clienttranslate('No specialist cards available to draw'));
        }

        // Рекрутинг выдаёт карты игроку "на руку" (закреплённые), чтобы они сразу отображались в UI
        $this->game->addSpecialistCardsToPlayerSpecialists($playerId, $drawn);
        $this->notify->all('specialistsDealtToHand', clienttranslate('${player_name} получает ${amount} карт из колоды найма (рекрутинг)'), [
            'player_id' => $playerId,
            'player_name' => $this->game->getPlayerNameById($playerId),
            'cardIds' => $drawn,
            'amount' => count($drawn),
            'source' => 'hiring_recruiting',
            'i18n' => [],
        ]);
        $this->game->globals->set('hiring_recruiting_done_' . $playerId, '1');
        return null;
    }

    /**
     * Игрок завершил фазу найма.
     */
    #[PossibleAction]
    public function actCompleteHiringPhase(): string
    {
        $this->game->checkAction('actCompleteHiringPhase');
        $playerId = (int) $this->game->getActivePlayerId();
        $this->game->savePlayerGameDataOnTurnEnd($playerId);
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    public function zombie(int $playerId): string
    {
        $this->game->globals->set('hiring_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
