<?php

declare(strict_types=1);

namespace Bga\Games\itarenagame\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\GameFramework\UserException;
use Bga\Games\itarenagame\Game;

/**
 * Состояние выбора карты основателя (только для основного режима)
 */
class FounderSelection extends GameState
{
    function __construct(
        protected Game $game,
    ) {
        parent::__construct(
            $game,
            id: 20,
            type: StateType::ACTIVE_PLAYER,
            description: clienttranslate('${actplayer} must choose a founder card or finish turn'),
            descriptionMyTurn: clienttranslate('${you} must choose a founder card or finish turn'),
        );
    }

    /**
     * Метод вызывается при входе в состояние выбора карты основателя
     */
    public function onEnteringState(): void
    {
        // Активный игрок должен быть установлен до входа в это состояние
        // (в setupNewGame или в предыдущем состоянии)
    }

    /**
     * Возвращает аргументы для состояния выбора карты основателя
     */
    public function getArgs(): array
    {
        $activePlayerIdRaw = $this->game->getActivePlayerId();
        $activePlayerId = is_int($activePlayerIdRaw) ? $activePlayerIdRaw : (int)$activePlayerIdRaw;
        
        $founderOptions = $this->game->getFounderOptionsForPlayer($activePlayerId);
        $hasSelectedFounder = $this->game->globals->get('founder_player_' . $activePlayerId, null) !== null;
        $mustPlaceFounder = $hasSelectedFounder && $this->game->hasUnplacedUniversalFounder($activePlayerId);
        
        return [
            "activePlayerId" => $activePlayerId,
            "founderOptions" => $founderOptions, // Массив из 3 карт на выбор (пустой, если карта уже выбрана)
            "hasSelectedFounder" => $hasSelectedFounder, // Выбрал ли игрок карту
            "mustPlaceFounder" => $mustPlaceFounder, // Обязательно ли разместить карту основателя
        ];
    }

    /**
     * Действие игрока: выбор карты основателя
     */
    #[PossibleAction]
    public function actSelectFounder(int $founderCardId, int $activePlayerId)
    {
        // Проверяем, что карта доступна для выбора
        $founderOptions = $this->game->getFounderOptionsForPlayer($activePlayerId);
        $availableIds = array_column($founderOptions, 'id');
        
        if (!in_array($founderCardId, $availableIds, true)) {
            throw new UserException(clienttranslate('Invalid founder card selected'));
        }

        // Назначаем выбранную карту игроку (автоматически размещается в отдел, если не универсальная)
        $this->game->selectFounderForPlayer($activePlayerId, $founderCardId);

        $founder = $this->game->getFoundersByPlayer()[$activePlayerId] ?? null;
        $founderName = $founder['name'] ?? clienttranslate('Unknown');
        $founderDepartment = $founder['department'] ?? 'universal';

        // Если карта была автоматически размещена в отдел, отправляем уведомление о размещении
        if ($founderDepartment !== 'universal') {
            $departmentNames = [
                'sales-department' => clienttranslate('Отдел продаж'),
                'back-office' => clienttranslate('Бэк-офис'),
                'technical-department' => clienttranslate('Техотдел'),
            ];
            $departmentName = $departmentNames[$founderDepartment] ?? $founderDepartment;

            $this->notify->all('founderPlaced', clienttranslate('${player_name} выбрал основателя ${founder_name}, который автоматически размещен в ${department_name}'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'founder' => $founder,
                'founder_name' => $founderName,
                'department' => $founderDepartment,
                'department_name' => $departmentName,
                'i18n' => ['founder_name', 'department_name'],
            ]);
        } else {
            // Уведомляем всех игроков о выборе (карта осталась на руке)
            $this->notify->all('founderSelected', clienttranslate('${player_name} выбрал основателя: ${founder_name}'), [
                'player_id' => $activePlayerId,
                'player_name' => $this->game->getPlayerNameById($activePlayerId),
                'founder' => $founder,
                'founder_name' => $founderName,
                'i18n' => ['founder_name'],
            ]);
        }

        // После выбора карты игрок должен остаться в своем ходе
        // Остаемся в том же состоянии FounderSelection, но теперь игрок может разместить карту и завершить ход
        // Не возвращаем переход - остаемся в том же состоянии с тем же активным игроком
        return null;
    }

    /**
     * Действие игрока: размещение карты основателя в отдел
     */
    #[PossibleAction]
    public function actPlaceFounder(string $department, int $activePlayerId)
    {
        $this->game->placeFounder($activePlayerId, $department);

        $founder = $this->game->getFoundersByPlayer()[$activePlayerId] ?? null;
        $departmentNames = [
            'sales-department' => clienttranslate('Отдел продаж'),
            'back-office' => clienttranslate('Бэк-офис'),
            'technical-department' => clienttranslate('Техотдел'),
        ];
        $departmentName = $departmentNames[$department] ?? $department;

        $this->notify->all('founderPlaced', clienttranslate('${player_name} разместил основателя в ${department_name}'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
            'department' => $department,
            'department_name' => $departmentName,
            'founder' => $founder,
            'i18n' => ['department_name'],
        ]);

        // Остаемся в том же состоянии
        return null;
    }

    /**
     * Действие игрока: завершение хода
     */
    #[PossibleAction]
    public function actFinishTurn(int $activePlayerId)
    {
        // Проверяем, есть ли у игрока неразмещенная универсальная карта основателя
        if ($this->game->hasUnplacedUniversalFounder($activePlayerId)) {
            throw new UserException(clienttranslate('Вы должны разместить карту основателя в один из отделов перед завершением хода'));
        }

        $this->notify->all('turnFinished', clienttranslate('${player_name} завершает ход'), [
            'player_id' => $activePlayerId,
            'player_name' => $this->game->getPlayerNameById($activePlayerId),
        ]);

        $this->game->giveExtraTime($activePlayerId);

        // Переходим к следующему игроку
        return NextPlayer::class;
    }

    /**
     * This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
     * You can do whatever you want in order to make sure the turn of this player ends appropriately
     * (ex: choose a random founder card).
     * 
     * See more about Zombie Mode: https://en.doc.boardgamearena.com/Zombie_Mode
     *
     * Important: your zombie code will be called when the player leaves the game. This action is triggered
     * from the main site and propagated to the gameserver from a server, not from a browser.
     * As a consequence, there is no current player associated to this action. In your zombieTurn function,
     * you must _never_ use `getCurrentPlayerId()` or `getCurrentPlayerName()`, 
     * but use the $playerId passed in parameter and $this->game->getPlayerNameById($playerId) instead.
     */
    function zombie(int $playerId)
    {
        // Получаем доступные карты для выбора
        $founderOptions = $this->game->getFounderOptionsForPlayer($playerId);
        
        if (empty($founderOptions)) {
            // Если нет доступных карт, просто переходим к следующему игроку
            return NextPlayer::class;
        }

        // Случайным образом выбираем одну из доступных карт
        $randomCard = $founderOptions[array_rand($founderOptions)];
        $founderCardId = $randomCard['id'];

        // Вызываем действие выбора карты
        return $this->actSelectFounder($founderCardId, $playerId);
    }
}

