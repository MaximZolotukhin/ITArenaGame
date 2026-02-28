<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\itarenagame\Game;

/**
 * Фаза «Продажи» (Sales) раунда.
 * Все игроки получают баджерсы по значению трека продаж (трек дохода в отделе продаж).
 * Завершается по нажатию кнопки «Завершить фазу «Продажи»».
 */
class RoundSales extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 18,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must confirm the Sales phase'),
            descriptionMyTurn: clienttranslate('${you} must confirm the Sales phase'),
            updateGameProgression: true,
            transitions: [
                'toNextPlayer' => 90,
            ],
        );
    }

    public function getArgs(): array
    {
        $round = (int) $this->game->getGameStateValue('round_number');
        $phase = $this->game->getPhaseByKey('sales');
        $phaseName = $phase ? $phase['name'] : '';
        $phaseNumber = $phase ? $phase['number'] : 4;

        // Данные только из БД (player_game_data), как и в getAllDatas — чтобы рендер совпадал с состоянием персонажа
        $playerPayouts = [];
        foreach (array_keys($this->game->loadPlayersBasicInfos()) as $playerId) {
            $playerId = (int) $playerId;
            $gameData = $this->game->getPlayerGameData($playerId);
            $salesTrackValue = $gameData !== null && isset($gameData['incomeTrack'])
                ? (int) $gameData['incomeTrack']
                : (int) $this->game->playerEnergy->get($playerId);
            $playerPayouts[$playerId] = [
                'salesTrackValue' => $salesTrackValue,
                'player_name' => $this->game->getPlayerNameById($playerId),
            ];
        }

        $activePlayerId = (int) $this->game->getActivePlayerId();
        $activePlayerBadgers = $this->game->getPlayerBadgersForCheck($activePlayerId);

        return [
            'phaseKey' => 'sales',
            'phaseName' => $phaseName,
            'phaseNumber' => $phaseNumber,
            'round' => $round,
            'roundName' => $this->game->getRoundName($round),
            'playerPayouts' => $playerPayouts,
            'activePlayerId' => $activePlayerId,
            'activePlayerBadgers' => $activePlayerBadgers,
        ];
    }

    /**
     * При входе в фазу «Продажи» начисляем баджерсы и отправляем одно сообщение только активному игроку
     * (когда начинается его ход в фазе «Продажи»). Остальные игроки получат своё сообщение при своём ходе.
     */
    public function onEnteringState(): ?string
    {
        $this->game->globals->set('current_phase_name', 'sales');

        $activePlayerId = (int) $this->game->getActivePlayerId();
        // Трек продаж из БД (player_game_data.income_track), как и в getArgs
        $gameData = $this->game->getPlayerGameData($activePlayerId);
        $salesTrackValue = $gameData !== null && isset($gameData['incomeTrack'])
            ? (int) $gameData['incomeTrack']
            : (int) $this->game->playerEnergy->get($activePlayerId);

        if ($salesTrackValue <= 0) {
            $newBadgers = $this->game->getPlayerBadgersForCheck($activePlayerId);
            $this->notify->all('salesPhasePayout', clienttranslate('${player_name} receives 0 badgers (sales track: 0)'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'amount' => 0,
                'salesTrackValue' => 0,
                'newValue' => $newBadgers,
            ]);
        } else {
            $withdrawn = $this->game->withdrawBadgersFromBank($salesTrackValue);
            $amount = $withdrawn ? $salesTrackValue : 0;

            if ($amount > 0) {
                $this->game->addPlayerBadgers($activePlayerId, $amount);
            }

            $newBadgers = $this->game->getPlayerBadgersForCheck($activePlayerId);
            $this->notify->all('salesPhasePayout', clienttranslate('${player_name} receives ${amount} badgers (sales track: ${track})'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'amount' => $amount,
                'track' => $salesTrackValue,
                'salesTrackValue' => $salesTrackValue,
                'newValue' => $newBadgers,
                'i18n' => ['track'],
            ]);
        }

        $this->notify->all('badgersChanged', '', []);

        return null; // остаёмся в RoundSales до нажатия «Завершить фазу «Продажи»»
    }

    /**
     * Игрок нажал «Завершить фазу «Продажи»» — переходим в NextPlayer.
     */
    #[PossibleAction]
    public function actCompleteSalesPhase(): string
    {
        $this->game->checkAction('actCompleteSalesPhase');
        $this->game->globals->set('sales_phase_just_finished', '1');
        return 'toNextPlayer';
    }

    /**
     * Zombie: игрок вышел из игры во время фазы «Продажи» — считаем фазу завершённой и переходим дальше.
     */
    public function zombie(int $playerId): string
    {
        $this->game->globals->set('sales_phase_just_finished', '1');
        return 'toNextPlayer';
    }
}
