/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * ITArenaGame implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * itarenagame.js
 *
 * ITArenaGame user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define(['dojo', 'dojo/_base/declare', 'ebg/core/gamegui', 'ebg/counter'], function (dojo, declare, gamegui, counter) {
  return declare('bgagame.itarenagame', ebg.core.gamegui, {
    constructor: function () {
      console.log('itarenagame constructor')

      // Here, you can init the global variables of your user interface
      // Example:
      // this.myGlobalValue = 0;
    },

    /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

    setup: function (gamedatas) {
      console.log('Starting game setup')

      // Example to add a div on the game area
      // Мой код для баннера раунда
      this.getGameAreaElement().insertAdjacentHTML(
        'beforeend',
        `
                <div class="game-layout">
                  <div class="main-column">
                    <div class="banner-container">
                      <div id="round-banner" class="round-banner">
                        <div class="round-banner__content"></div>
                      </div>
                      <div id="game-mode-banner" class="game-mode-banner"></div>
                    </div>
                    <div class="events-and-skills"> <!-- Планшет навыков и событий -->
                      <div id="event-card-panel" class="event-card-panel">
                        <div class="event-card-panel__header">${_('Карта события')}</div>
                        <div class="event-card-panel__body"></div>
                      </div>
                      <div class="round-panel">
                        <div class="round-panel__header">${_('Планшет событий')}</div>
                        <div class="round-panel__wrapper">
                          <img src="${g_gamethemeurl}img/table/events_board.png" alt="Events board" class="round-panel__image" />
                          <div class="round-panel__rounds-track">
                            <div class="round-track-column" data-round="1">
                              <div class="round-track-column__circle"></div>
                            </div>
                            <div class="round-track-column" data-round="2">
                              <div class="round-track-column__circle"></div>
                            </div>
                            <div class="round-track-column" data-round="3">
                              <div class="round-track-column__circle"></div>
                            </div>
                            <div class="round-track-column" data-round="4">
                              <div class="round-track-column__circle"></div>
                            </div>
                            <div class="round-track-column" data-round="5">
                              <div class="round-track-column__circle"></div>
                            </div>
                            <div class="round-track-column" data-round="6">
                              <div class="round-track-column__circle"></div>
                            </div>
                          </div>
                          <div class="round-panel__skills-track">
                            <div class="round-panel__skills-track-row round-panel__skills-track-row--tokens">
                              <div class="round-panel__skills-track-row-inner">
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column round-panel__skill-token-column--large"></div>
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column"></div>
                              </div>
                            </div>
                            <div class="round-panel__skills-track-row round-panel__skills-track-row--skills">
                              <div class="round-panel__skills-track-row-inner">
                                <div class="round-panel__skill-column" data-skill="eloquence"></div>
                                <div class="round-panel__skill-column" data-skill="discipline"></div>
                                <div class="round-panel__skill-column" data-skill="intellect"></div>
                                <div class="round-panel__skill-column" data-skill="frugality"></div>
                              </div>
                            </div>
                          <div class="round-panel__skill-indicators"></div>
                          </div>
                          <div class="round-panel__goals-track">
                            <div class="round-panel__goals-track-row">
                              <div class="round-panel__goals-track-row-inner">
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                              </div>
                            </div>
                            <div class="round-panel__goals-track-row">
                              <div class="round-panel__goals-track-row-inner">
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                                <div class="round-panel__goal-column"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="dice-panel">
                        <div class="dice-panel__header">${_('Кость PAEI')}</div>
                        <div class="dice-panel__body">
                        <img src="${g_gamethemeurl}img/table/dice.png" alt="Dice" class="dice-panel__image" />
                        <div id="cube-face-display" class="dice-panel__value"></div>
                        </div>
                      </div>
                    </div>
                    <div class="money-and-project">
                    <!-- Деньги игрока -->
                      <div class="player-money-panel">
                        <div class="player-money-panel__header">${_('Деньги игрока')}</div>
                        <div class="player-money-panel__color-badge"></div>
                        <div class="player-money-panel__body"></div>
                      </div>
                      <!-- планшет проектов -->
                      <div class="project-board-panel">
                        <div class="project-board-panel__header">${_('Планшет проектов')}</div>
                        <div class="project-board-panel__body">
                          <img src="${g_gamethemeurl}img/table/project_table.png" alt="${_('Планшет проектов')}" class="project-board-panel__image" />
                          <div class="project-board-panel__columns">
                            <div class="project-board-panel__column project-board-panel__column--complex project-board-panel__column--red">
                              <div class="project-board-panel__column-header">${_('Сложные - Красный')}</div>
                              <div class="project-board-panel__column-body">
                                ${['red-circle-1', 'red-hex', 'red-square', 'red-circle-2'].map((label) => `<div class="project-board-panel__row" data-label="${label}"></div>`).join('')}
                              </div>
                            </div>
                            <div class="project-board-panel__column project-board-panel__column--long-term project-board-panel__column--blue">
                              <div class="project-board-panel__column-header">${_('Длительные - Синий')}</div>
                              <div class="project-board-panel__column-body">
                                ${['blue-circle-1', 'blue-square', 'blue-hex', 'blue-circle-2'].map((label) => `<div class="project-board-panel__row" data-label="${label}"></div>`).join('')}
                              </div>
                            </div>
                            <div class="project-board-panel__column project-board-panel__column--expensive project-board-panel__column--green">
                              <div class="project-board-panel__column-header">${_('Дорогие - Зеленый')}</div>
                              <div class="project-board-panel__column-body">
                                ${['green-circle-1', 'green-hex', 'green-square', 'green-circle-2'].map((label) => `<div class="project-board-panel__row" data-label="${label}"></div>`).join('')}
                              </div>
                            </div>
                            <div class="project-board-panel__column project-board-panel__column--task-pool">
                              <div class="project-board-panel__column-header">${_('Пулл проектов')}</div>
                              <div class="project-board-panel__task-pool-body">
                                <div class="project-board-panel__task-pool-row project-board-panel__task-pool-row--top">
                                  <div class="project-board-panel__task-pool-cell" name="круг"></div>
                                </div>
                                <div class="project-board-panel__task-pool-row project-board-panel__task-pool-row--bottom">
                                  <div class="project-board-panel__task-pool-cell" name="квадрат"></div>
                                  <div class="project-board-panel__task-pool-cell" name="гекс"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- банк -->
                      <div class="bank">
                        <div class="badgers-panel">
                          <div class="badgers-panel__header">${_('Баджерсы')}</div>
                          <div class="badgers-panel__body"></div>
                        </div>
                      </div>
                    </div>
                    <!-- Планшет игрока и его проектов -->
                    <div class="players-table">
                      <!--<div class="players-table__header">${_('IT проекты')}</div>-->
                      <div class="players-table__body">
                        <div class="it-projects">
                          <div class="it-projects__header">${_('IT проекты')}</div>
                          <div class="it-projects__columns">
                            <div class="completed-projects">
                              <div class="completed-projects__header">${_('Выполненные проекты')}</div>
                              <div class="completed-projects__body"></div>
                            </div>
                            <div class="parts-of-projects">
                              <div class="parts-of-projects__header">${_('Части проектов')}</div>
                              <div class="parts-of-projects__body"></div>
                            </div>
                          </div>
                        </div>
                        <div class="player-personal-board">
                          <div class="player-personal-board__header">${_('Планшет игрока')}</div>
                          <div class="player-personal-board__body">
                            <img src="${g_gamethemeurl}img/table/player-table-green.png" alt="${_('Планшет игрока')}" class="player-personal-board__image" data-default-src="${g_gamethemeurl}img/table/player-table-green.png" />
                            <div class="player-board-blocks">
                              <div class="player-board-block player-board-block--left player-actions-block">
                                <div class="player-board-block--left-row">
                                  <div class="player-board-block--left-cell"></div>
                                  <div class="player-board-block--left-cell player-penalty-block">
                                    <div class="player-penalty-tokens__container">
                                      <div class="player-penalty-tokens__column start-position-1"></div>
                                      <div class="player-penalty-tokens__column start-position-2"></div>
                                      <div class="player-penalty-tokens__column penalty-position-empty"></div>
                                      <div class="player-penalty-tokens__column penalty-position-1"></div>
                                      <div class="player-penalty-tokens__column penalty-position-2"></div>
                                      <div class="player-penalty-tokens__column penalty-position-3"></div>
                                      <div class="player-penalty-tokens__column penalty-position-4"></div>
                                      <div class="player-penalty-tokens__column penalty-position-5"></div>
                                      <div class="player-penalty-tokens__column penalty-position-10"></div>
                                    </div>
                                  </div>
                                  <div class="player-board-block--left-cell player-exchange-block">
                                    <div class="player-exchange-block__column player-exchange-block__column--bonus"></div>
                                    <div class="player-exchange-block__column player-exchange-block__column--exchange-scheme">
                                      <div class="player-exchange-block__block player-exchange-block__block--improvement">
                                        <div class="player-exchange-block__improvement-cell player-exchange-block__improvement-cell--off">
                                          <div class="player-exchange-token"></div>
                                        </div>
                                        <div class="player-exchange-block__improvement-cell player-exchange-block__improvement-cell--on"></div>
                                      </div>
                                      <div class="player-exchange-block__block player-exchange-block__block--choice">
                                        <div class="player-exchange-block__choice-column shema-update-off">
                                          ${Array.from({ length: 6 }, (_, i) => `<div class="player-exchange-block__choice-row" data-row="${i + 1}"></div>`).join('')}
                                        </div>
                                        <div class="player-exchange-block__choice-column shema-update-on">
                                          ${Array.from({ length: 6 }, (_, i) => `<div class="player-exchange-block__choice-row" data-row="${i + 1}"></div>`).join('')}
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="player-board-block--left-cell player-sprint-panel">
                                    ${[
                                      { class: 'player-sprint-panel__column--first', id: 'sprint-column-tasks', className: 'sprint-column-tasks', title: _('Задачи') },
                                      { class: '', id: 'sprint-column-backlog', className: 'sprint-column-backlog', title: _('Бэклог') },
                                      { class: '', id: 'sprint-column-in-progress', className: 'sprint-column-in-progress', title: _('В работе') },
                                      { class: '', id: 'sprint-column-testing', className: 'sprint-column-testing', title: _('Тестирование') },
                                      { class: '', id: 'sprint-column-completed', className: 'sprint-column-completed', title: _('Выполнено') },
                                    ]
                                      .map(
                                        (col, i) =>
                                          `<div id="${col.id}" class="player-sprint-panel__column ${col.class} ${col.className}">${
                                            i === 0
                                              ? `<div class="player-sprint-panel__rows-container">${Array(6)
                                                  .fill(0)
                                                  .map((_, j) => {
                                                    const rowNum = 6 - j
                                                    return `<div id="sprint-row-${rowNum}" class="player-sprint-panel__row" data-row-index="${rowNum}">${rowNum === 1 ? '<div class="player-sprint-panel__token"></div>' : ''}</div>`
                                                  })
                                                  .join('')}</div>`
                                              : ''
                                          }</div>`
                                      )
                                      .join('')}
                                  </div>
                                </div>
                              </div>
                              <div class="player-board-block player-board-block--right player-departments-block">
                                <div id="player-department-sales" class="player-board-block--right-row player-department-sales">
                                  <div id="player-department-sales-top" class="player-department-sales__block player-department-sales-top"></div>
                                  <div id="player-department-sales-middle" class="player-department-sales__block player-department-sales-middle">
                                    <div class="income-track-panel">
                                      <div class="income-track-panel__body">
                                        <div class="income-track">
                                          <!-- Внешняя окружность (11-20) -->
                                          <div class="income-track__circle income-track__circle--outer">
                                            ${Array.from({ length: 10 }, (_, i) => {
                                              const value = i + 11
                                              const angle = i * 36 - 90 // 36 градусов на сектор, смещение по часовой стрелке на 1
                                              return `
                                                <div class="income-track__sector income-track__sector--outer" data-value="${value}" title="Сектор ${value}" aria-label="Сектор ${value}" style="transform: rotate(${angle}deg);">
                                                  <div class="income-track__sector-content" style="transform: rotate(${-angle}deg);">
                                                  </div>
                                                </div>
                                              `
                                            }).join('')}
                                          </div>
                                          <!-- Внутренняя окружность (1-10) -->
                                          <div class="income-track__circle income-track__circle--inner">
                                            ${Array.from({ length: 10 }, (_, i) => {
                                              const value = i + 1
                                              const angle = i * 36 - 90 // 36 градусов на сектор, смещение по часовой стрелке на 1
                                              return `
                                                <div class="income-track__sector income-track__sector--inner" data-value="${value}" title="Сектор ${value}" aria-label="Сектор ${value}" style="transform: rotate(${angle}deg);">
                                                  <div class="income-track__sector-content" style="transform: rotate(${-angle}deg);">
                                                    ${value === 1 ? '<div class="income-track__token"></div>' : ''}
                                                  </div>
                                                </div>
                                              `
                                            }).join('')}
                                          </div>
                                          <div class="income-track__center"></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div id="player-department-sales-bottom" class="player-department-sales__block player-department-sales-bottom">
                                    <div id="player-department-sales-off" class="player-department-sales-bottom__half">
                                      <div class="player-department-sales__token"></div>
                                    </div>
                                    <div id="player-department-sales-on" class="player-department-sales-bottom__half"></div>
                                  </div>
                                </div>
                                <div id="player-department-back-office" class="player-board-block--right-row player-department-back-office">
                                  <div id="player-department-back-office-top" class="player-department-back-office__row player-department-back-office-top"></div>
                                  <div id="player-department-back-office-evolution" class="player-department-back-office__row player-department-back-office-evolution">
                                    <div class="player-department-back-office-evolution__columns-wrapper">
                                      ${Array(3)
                                        .fill(0)
                                        .map((_, i) => {
                                          const columnNum = i + 1
                                          if (columnNum === 1) {
                                            // Первая колонка: 6 ячеек от 1 до 6 сверху вниз
                                            const rowsHtml = Array(6)
                                              .fill(0)
                                              .map((_, j) => {
                                                const rowNum = 6 - j // Нумерация от 1 до 6 снизу вверх (row-6 сверху, row-1 снизу)
                                                const isBottomRow = rowNum === 1 // Нижняя ячейка (row-1)
                                                return `<div id="player-department-back-office-evolution-column-1-row-${rowNum}" class="player-department-back-office-evolution__row" data-row-index="${rowNum}">${
                                                  isBottomRow ? '<div class="player-department-back-office-evolution__token"></div>' : ''
                                                }</div>`
                                              })
                                              .join('')
                                            return `<div id="player-department-back-office-evolution-column-${columnNum}" class="player-department-back-office-evolution__column">
                                              <div class="player-department-back-office-evolution-column-1__rows-wrapper">${rowsHtml}</div>
                                            </div>`
                                          } else {
                                            return `<div id="player-department-back-office-evolution-column-${columnNum}" class="player-department-back-office-evolution__column"></div>`
                                          }
                                        })
                                        .join('')}
                                    </div>
                                  </div>
                                  <div id="player-department-back-office-update" class="player-department-back-office__row player-department-back-office-update">
                                    <div id="player-department-back-office-off" class="player-department-back-office-update__half player-department-back-office-off">
                                      <div class="player-department-back-office__token"></div>
                                    </div>
                                    <div id="player-department-back-office-on" class="player-department-back-office-update__half player-department-back-office-on"></div>
                                  </div>
                                </div>
                                <div id="player-department-technical" class="player-board-block--right-row player-department-technical">
                                  <div id="player-department-technical-name" class="player-department-technical__row player-department-technical-name"></div>
                                  <div id="player-department-technical-development" class="player-department-technical__row player-department-technical-development">
                                    <div class="player-department-technical-development__columns-wrapper">
                                      ${Array(4)
                                        .fill(0)
                                        .map((_, i) => {
                                          const columnNum = i + 1
                                          // Блоки 1 и 3: 5 строк (1-5), блоки 2 и 4: 6 строк (0-5)
                                          const rowCount = columnNum === 1 || columnNum === 3 ? 5 : 6
                                          const startNum = columnNum === 1 || columnNum === 3 ? 1 : 0
                                          const needsWrapper = columnNum === 1 || columnNum === 2 || columnNum === 3 || columnNum === 4
                                          const wrapperHeight = columnNum === 1 || columnNum === 3 ? '70%' : columnNum === 2 || columnNum === 4 ? '80%' : '100%'
                                          const colorClass =
                                            columnNum === 1
                                              ? 'player-department-technical-development__column--pink'
                                              : columnNum === 2
                                              ? 'player-department-technical-development__column--orange'
                                              : columnNum === 3
                                              ? 'player-department-technical-development__column--blue'
                                              : 'player-department-technical-development__column--purple'
                                          const rowsHtml = Array(rowCount)
                                            .fill(0)
                                            .map((_, j) => {
                                              const rowNum = startNum + (rowCount - 1 - j) // Нумерация снизу вверх
                                              const isBottomRow = j === rowCount - 1 // Последняя итерация = нижняя строка
                                              return `<div id="player-department-technical-development-column-${columnNum}-row-${rowNum}" class="player-department-technical-development__row" data-row-index="${rowNum}">${
                                                isBottomRow ? '<div class="player-department-technical-development__token"></div>' : ''
                                              }</div>`
                                            })
                                            .join('')
                                          return `<div id="player-department-technical-development-column-${columnNum}" class="player-department-technical-development__column ${colorClass}">
                                            ${needsWrapper ? `<div class="player-department-technical-development-column-${columnNum}__rows-wrapper" style="height: ${wrapperHeight};">${rowsHtml}</div>` : rowsHtml}
                                          </div>`
                                        })
                                        .join('')}
                                    </div>
                                  </div>
                                  <div id="player-department-technical-upgrade" class="player-department-technical__row player-department-technical-upgrade">
                                    <div id="player-department-technical-off" class="player-department-technical-upgrade__half player-department-technical-off">
                                      <div class="player-department-technical__token"></div>
                                    </div>
                                    <div id="player-department-technical-on" class="player-department-technical-upgrade__half player-department-technical-on"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="hiring-employees">
                          <div class="hiring-employees__header">${_('Найм сотрудников')}</div>
                          <div class="hiring-employees__body">
                            <div class="sales-department">
                              <div class="sales-department__body" data-department="sales-department"></div>
                            </div>
                            <div class="back-office">
                              <div class="back-office__body" data-department="back-office"></div>
                            </div>
                            <div class="technical-department">
                              <div class="technical-department__body" data-department="technical-department"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="active-player-hand" id="active-player-hand" hidden>
                       <div class="active-player-hand__header">${_('Руки игрока')}</div>
                      <div class="active-player-hand__body">
                        <div class="active-player-hand__side active-player-hand__side--left">
                          <img src="${g_gamethemeurl}img/table/hand-right.png" alt="${_('Рука игрока')}" class="active-player-hand__image active-player-hand__image--left" />
                        </div>
                        <div class="active-player-hand__center" id="active-player-hand-cards"></div>
                        <div class="active-player-hand__side active-player-hand__side--right">
                          <img src="${g_gamethemeurl}img/table/hand-right.png" alt="${_('Рука игрока')}" class="active-player-hand__image" />
                        </div>
                      </div>
                    </div>
                    <div id="player-tables" class="player-tables"></div>
                  </div>
                </div>
            `
      )
      // Мой код для баннера раунда
      // Setting up player boards
      Object.values(gamedatas.players).forEach((player) => {
        // example of setting up players boards
        this.getPlayerPanelElement(player.id).insertAdjacentHTML(
          'beforeend',
          `
                    <span id="energy-player-counter-${player.id}"></span> Energy
                `
        )
        // Мой код для счетчика энергии
        const counter = new ebg.counter()
        counter.create(`energy-player-counter-${player.id}`, { value: player.energy, playerCounter: 'energy', playerId: player.id })

        // example of adding a div for each player
        // Мой код для таблицы игроков
        document.getElementById('player-tables').insertAdjacentHTML(
          'beforeend',
          `
                    <div id="player-table-${player.id}">
                        <strong>${player.name}</strong>
                        <div>Player zone content goes here</div>
                    </div>
                `
        )
      })
      // Мой код для таблицы игроков
      this.totalRounds = gamedatas.totalRounds // Общее количество раундов
      this.gamedatas = gamedatas // Обновляем данные игры
      this.gamedatas.gamestate = this.gamedatas.gamestate || {} // Обновляем состояние игры
      this.gamedatas.founders = gamedatas.founders || {}
      this.localFounders = this.localFounders || {}
      this._applyLocalFounders()
      this.eventCardsData = gamedatas.eventCards || {} // Данные о картах событий

      // Проверяем баджерсы игроков
      console.log('=== CHECKING PLAYER BADGERS ===')
      if (gamedatas.players) {
        Object.values(gamedatas.players).forEach((player) => {
          const badgers = player.badgers || 0
          console.log('Player ' + player.id + ' (' + player.name + ') has ' + badgers + ' badgers (expected: 5)')
          if (badgers !== 5) {
            console.warn('WARNING: Player ' + player.id + ' has incorrect badgers count! Expected: 5, Got: ' + badgers)
          }
        })
      } else {
        console.warn('No players data found in gamedatas')
      }

      // Проверяем карты основателей
      console.log('=== CHECKING FOUNDER CARDS ===')
      console.log('Game mode:', gamedatas.gameMode, '(1=TUTORIAL, 2=MAIN)')
      console.log('Tutorial mode:', gamedatas.isTutorialMode)
      console.log('Founder options in gamedatas:', gamedatas.founderOptions)
      console.log('Founder options length:', gamedatas.founderOptions?.length || 0)
      console.log('Active founder options in gamedatas:', gamedatas.activeFounderOptions)
      console.log('Active founder options length:', gamedatas.activeFounderOptions?.length || 0)
      console.log('All players founder options:', gamedatas.allPlayersFounderOptions)

      if (!gamedatas.isTutorialMode) {
        console.log('MAIN MODE: Checking founder cards distribution')

        if (gamedatas.players) {
          Object.values(gamedatas.players).forEach((player) => {
            console.log('Player ' + player.id + ' (' + player.name + ') - checking founder data')
            // Проверяем, есть ли у игрока выбранный основатель
            if (player.founder) {
              console.log('Player ' + player.id + ' has selected founder:', player.founder)
            } else {
              console.log('Player ' + player.id + ' has NO selected founder (expected in main mode)')
            }
          })
        }

        // Проверяем, есть ли опции для текущего игрока
        if (gamedatas.founderOptions && gamedatas.founderOptions.length > 0) {
          console.log('✅ Current player has ' + gamedatas.founderOptions.length + ' founder options (expected: 3)')
          if (gamedatas.founderOptions.length !== 3) {
            console.warn('⚠️ WARNING: Current player should have 3 options, but got ' + gamedatas.founderOptions.length)
          }
        } else {
          console.error('❌ ERROR: Current player has NO founder options! This should not happen in MAIN mode!')
          console.error('This means cards were not distributed or not passed to client')
        }

        // Проверяем активного игрока
        if (gamedatas.activeFounderOptions && gamedatas.activeFounderOptions.length > 0) {
          console.log('✅ Active player has ' + gamedatas.activeFounderOptions.length + ' founder options')
        } else {
          console.warn('⚠️ Active player has NO founder options')
        }
      } else {
        console.log('TUTORIAL MODE: Founder selection skipped (cards assigned directly)')
      }

      // Режим игры (1 - Обучающий, 2 - Основной)
      this.gameMode = gamedatas.gameMode || 1
      this.isTutorialMode = gamedatas.isTutorialMode !== undefined ? gamedatas.isTutorialMode : this.gameMode === 1

      console.log('Game mode:', this.gameMode === 1 ? 'Tutorial' : 'Main', 'isTutorialMode:', this.isTutorialMode)
      this._renderRoundTrack(this.totalRounds)
      this._renderRoundBanner(gamedatas.round, this.totalRounds, gamedatas.stageName, gamedatas.cubeFace, gamedatas.phaseName)
      this._renderGameModeBanner()

      // Отображаем индикаторы игроков на плашете событий после рендера трека
      setTimeout(() => {
        const roundPanel = document.querySelector('.round-panel__wrapper')
        if (roundPanel) {
          console.log('Calling _renderPlayerIndicators from setup')
          this._renderPlayerIndicators(roundPanel)
        } else {
          console.error('roundPanel not found in setup!')
        }
      }, 200)

      // Обновляем отображение кубика
      this._updateCubeFace(gamedatas.cubeFace)
      const initialEventCards = gamedatas.roundEventCards || []
      this._renderEventCards(initialEventCards)
      this._renderRoundEventCards(initialEventCards)
      this._renderBadgers(gamedatas.badgers || [])
      const initialActiveId = this._getActivePlayerIdFromDatas(gamedatas) || this.player_id
      this._renderPlayerMoney(gamedatas.players, initialActiveId) // Отображаем деньги игрока

      // Проверяем, нужно ли отобразить карты для выбора (в основном режиме, в состоянии FounderSelection)
      const currentState = gamedatas?.gamestate?.name
      const isFounderSelection = currentState === 'FounderSelection'
      const isMainMode = !gamedatas.isTutorialMode

      if (isFounderSelection && isMainMode && Number(initialActiveId) === Number(this.player_id)) {
        const founderOptions = gamedatas?.founderOptions || gamedatas?.activeFounderOptions || []
        const hasSelectedFounder = gamedatas?.players?.[initialActiveId]?.founder !== undefined

        console.log('setup - FounderSelection check:', {
          isFounderSelection,
          isMainMode,
          isCurrentPlayer: Number(initialActiveId) === Number(this.player_id),
          founderOptionsCount: founderOptions.length,
          hasSelectedFounder,
          founderOptions: founderOptions,
        })

        if (!hasSelectedFounder && founderOptions.length > 0) {
          console.log('✅ setup - Rendering founder selection cards, count:', founderOptions.length)
          // Используем небольшую задержку, чтобы DOM точно был готов
          setTimeout(() => {
            this._renderFounderSelectionCards(founderOptions, initialActiveId)
          }, 100)
        } else {
          console.log('setup - Not rendering selection cards:', { hasSelectedFounder, optionsCount: founderOptions.length })
          this._renderFounderCard(gamedatas.players, initialActiveId)
        }
      } else {
        this._renderFounderCard(gamedatas.players, initialActiveId)
      }

      this._toggleActivePlayerHand(initialActiveId)
      this._updateHandHighlight(initialActiveId)

      // Отображаем жетоны штрафа для всех игроков - с небольшой задержкой для загрузки DOM
      setTimeout(() => {
        this._renderPenaltyTokens(gamedatas.players)
      }, 100)

      // Отображаем жетоны задач для всех игроков - с небольшой задержкой для загрузки DOM
      setTimeout(() => {
        this._renderTaskTokens(gamedatas.players)
      }, 150)

      // Отображаем жетоны проектов на планшете проектов
      setTimeout(() => {
        this._renderProjectTokensOnBoard(gamedatas.projectTokensOnBoard || [])
      }, 200)

      // TODO: Set up your game interface here, according to "gamedatas"

      // Setup game notifications to handle (see "setupNotifications" method below)
      // Мой код для уведомлений
      this.setupNotifications()

      console.log('Ending game setup')

      this._setupCardZoom()
      this._setupHandInteractions()
    },

    ///////////////////////////////////////////////////
    //// Game & client states

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    onEnteringState: function (stateName, args) {
      console.log('=== onEnteringState CALLED ===')
      console.log('Entering state: ' + stateName)
      console.log('Raw args:', args)

      switch (stateName) {
        /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */

        case 'dummy':
          break
        case 'GameSetup':
          // Состояние подготовки игры - отображаем информацию о подготовке
          console.log('Entering GameSetup state')

          this._renderGameSetup()

          // Отображаем жетоны проектов на планшете
          if (args?.args?.projectTokensOnBoard) {
            setTimeout(() => {
              this._renderProjectTokensOnBoard(args.args.projectTokensOnBoard)
            }, 200)
          } else if (this.gamedatas?.projectTokensOnBoard) {
            setTimeout(() => {
              this._renderProjectTokensOnBoard(this.gamedatas.projectTokensOnBoard)
            }, 200)
          }

          break
        case 'PlayerTurn':
          if (!this.gamedatas.gamestate) {
            this.gamedatas.gamestate = {}
          }
          const activeId = this._extractActivePlayerId(args) ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
          this.gamedatas.gamestate.active_player = activeId
          this._renderPlayerMoney(this.gamedatas.players, activeId)
          this._renderFounderCard(this.gamedatas.players, activeId)
          this._toggleActivePlayerHand(activeId)
          this._updateHandHighlight(activeId)
          break
        case 'FounderSelection':
          // Состояние выбора карты основателя
          const activeIdFounderSelection = this._extractActivePlayerId(args) ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id

          console.log('onEnteringState FounderSelection:', {
            activeIdFounderSelection,
            currentPlayerId: this.player_id,
            isCurrentPlayer: Number(activeIdFounderSelection) === Number(this.player_id),
            args: args?.args,
            founderOptionsFromArgs: args?.args?.founderOptions?.length || 0,
            founderOptionsFromGamedatas: this.gamedatas?.founderOptions?.length || 0,
            activeFounderOptionsFromGamedatas: this.gamedatas?.activeFounderOptions?.length || 0,
          })

          // Обновляем founderOptions из args, если они есть
          if (args?.args?.founderOptions) {
            this.gamedatas.founderOptions = args.args.founderOptions
            this.gamedatas.activeFounderOptions = args.args.founderOptions
            console.log('Updated founderOptions from args:', args.args.founderOptions.length)
          }

          // Если это текущий игрок и есть карты для выбора, отображаем их
          if (Number(activeIdFounderSelection) === Number(this.player_id)) {
            const founderOptions = args?.args?.founderOptions || this.gamedatas?.founderOptions || this.gamedatas?.activeFounderOptions || []
            const hasSelectedFounder = args?.args?.hasSelectedFounder === true

            console.log('Current player in FounderSelection:', {
              founderOptionsCount: founderOptions.length,
              hasSelectedFounder,
              founderOptions: founderOptions,
            })

            // Если карта еще не выбрана и есть опции, показываем карты для выбора
            if (!hasSelectedFounder && founderOptions.length > 0) {
              console.log('✅ Rendering selection cards in onEnteringState, count:', founderOptions.length)
              setTimeout(() => {
                this._renderFounderSelectionCards(founderOptions, activeIdFounderSelection)
              }, 100)
            } else {
              // Если карта уже выбрана, показываем обычное отображение
              console.log('Founder already selected or no options, rendering normal card')
              this._renderFounderCard(this.gamedatas.players, activeIdFounderSelection)
            }
          } else {
            // Для других игроков показываем обычное отображение
            console.log('Not current player, rendering normal card')
            this._renderFounderCard(this.gamedatas.players, activeIdFounderSelection)
          }

          this._toggleActivePlayerHand(activeIdFounderSelection)
          this._updateHandHighlight(activeIdFounderSelection)
          break
        case 'RoundEvent':
          // Состояние события раунда - обновляем данные кубика и карты событий
          // Приоритет: сначала args (данные из getArgs()), потом gamedatas
          console.log('Entering RoundEvent state, args:', args)
          console.log('Entering RoundEvent state, gamedatas.cubeFace:', this.gamedatas?.cubeFace)
          console.log('Entering RoundEvent state, gamedatas.roundEventCards:', this.gamedatas?.roundEventCards)

          // Получаем данные из args или gamedatas
          const cubeFaceFromArgs = args?.args?.cubeFace
          const cubeFaceFromGamedatas = this.gamedatas?.cubeFace
          const cubeFace = cubeFaceFromArgs || cubeFaceFromGamedatas || ''

          const roundEventCardsFromArgs = args?.args?.roundEventCards || []
          const roundEventCardsFromGamedatas = this.gamedatas?.roundEventCards || []
          const roundEventCards = roundEventCardsFromArgs.length > 0 ? roundEventCardsFromArgs : roundEventCardsFromGamedatas

          const roundFromArgs = args?.args?.round
          const roundFromGamedatas = this.gamedatas?.round
          const round = roundFromArgs || roundFromGamedatas || 1

          const stageNameFromArgs = args?.args?.stageName
          const stageNameFromGamedatas = this.gamedatas?.stageName
          const stageName = stageNameFromArgs || stageNameFromGamedatas || ''

          const phaseNameFromArgs = args?.args?.phaseName
          const phaseNameFromGamedatas = this.gamedatas?.phaseName
          const phaseName = phaseNameFromArgs || phaseNameFromGamedatas || ''

          // Обновляем данные в gamedatas для последующих обновлений
          if (cubeFaceFromArgs) {
            this.gamedatas.cubeFace = cubeFaceFromArgs
          }
          if (roundEventCardsFromArgs.length > 0) {
            this.gamedatas.roundEventCards = roundEventCardsFromArgs
            this.gamedatas.roundEventCard = roundEventCardsFromArgs[0] || null
          }
          if (roundFromArgs) {
            this.gamedatas.round = roundFromArgs
          }
          if (stageNameFromArgs) {
            this.gamedatas.stageName = stageNameFromArgs
          }
          if (phaseNameFromArgs) {
            this.gamedatas.phaseName = phaseNameFromArgs
          }

          // Обновляем отображение
          if (cubeFace) {
            console.log('Updating cube face from RoundEvent state:', cubeFace)
            this._updateCubeFace(cubeFace)
          }

          if (roundEventCards.length > 0) {
            console.log('Rendering round event cards from RoundEvent state:', roundEventCards)
            this._renderEventCards(roundEventCards)
            this._renderRoundEventCards(roundEventCards)
          }

          if (round && stageName) {
            this._renderRoundBanner(round, this.totalRounds, stageName, cubeFace, phaseName)
          }
          break
      }
    },

    // onLeavingState: this method is called each time we are leaving a game state.
    //                 You can use this method to perform some user interface changes at this moment.
    //
    // Мой код для уведомлений
    onLeavingState: function (stateName) {
      console.log('Leaving state: ' + stateName)

      switch (stateName) {
        /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */

        case 'dummy':
          break
      }
    },

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    // Мой код для кнопок действий
    onUpdateActionButtons: function (stateName, args) {
      console.log('onUpdateActionButtons: ' + stateName, args)
      console.log('isCurrentPlayerActive:', this.isCurrentPlayerActive())
      console.log('player_id:', this.player_id)
      console.log('gamedatas.gamestate:', this.gamedatas?.gamestate)

      // Для состояния GameSetup - переход происходит автоматически
      if (stateName === 'GameSetup') {
        this.statusBar.setTitle(_('Подготовка к игре...'))
        return
      }

      if (this.isCurrentPlayerActive()) {
        switch (stateName) {
          case 'PlayerTurn':
            const playableCardsIds = args.playableCardsIds // returned by the argPlayerTurn
            const mustPlaceFounderPlayerTurn = args.mustPlaceFounder === true // Обязательно ли разместить карту основателя

            // Add test action buttons in the action status bar, simulating a card click:
            // Мой код для кнопок действий
            playableCardsIds.forEach((cardId) => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId)))

            this.statusBar.addActionButton(_('Pass'), () => this.bgaPerformAction('actPass'), { color: 'secondary' })

            // Кнопка завершения хода: блокируется, если нужно разместить карту основателя
            const finishTurnButton = this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), {
              primary: true,
              disabled: mustPlaceFounderPlayerTurn,
              tooltip: mustPlaceFounderPlayerTurn ? _('Вы должны разместить карту основателя в один из отделов перед завершением хода') : undefined,
              id: 'finish-turn-button', // ID для обновления состояния кнопки
            })

            // Сохраняем ссылку на кнопку для обновления состояния после размещения карты
            this.finishTurnButton = finishTurnButton
            break
          case 'FounderSelection':
            // В состоянии выбора карты основателя
            console.log('FounderSelection onUpdateActionButtons, args:', args)
            // args может быть null или иметь структуру { args: { ... } }
            const founderSelectionActionArgs = args?.args || args || {}
            console.log('FounderSelection onUpdateActionButtons - Extracted args:', founderSelectionActionArgs)
            const hasSelectedFounder = founderSelectionActionArgs?.hasSelectedFounder === true
            const mustPlaceFounderFounderSelection = founderSelectionActionArgs?.mustPlaceFounder === true
            const founderOptionsFromArgs = founderSelectionActionArgs?.founderOptions || []

            console.log('FounderSelection onUpdateActionButtons:', {
              hasSelectedFounder,
              mustPlaceFounderFounderSelection,
              founderOptionsCount: founderOptionsFromArgs.length,
              founderOptions: founderOptionsFromArgs,
            })

            // Обновляем данные в gamedatas
            if (founderOptionsFromArgs.length > 0) {
              this.gamedatas.founderOptions = founderOptionsFromArgs
              this.gamedatas.activeFounderOptions = founderOptionsFromArgs
              console.log('Updated founderOptions in onUpdateActionButtons')
            }

            // Если карта еще не выбрана и есть опции, отображаем карты
            if (!hasSelectedFounder && founderOptionsFromArgs.length > 0) {
              const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas) || this.player_id
              console.log('✅ Rendering selection cards in onUpdateActionButtons for player:', activePlayerId, 'count:', founderOptionsFromArgs.length)
              setTimeout(() => {
                this._renderFounderSelectionCards(founderOptionsFromArgs, activePlayerId)
              }, 100)
            }

            if (hasSelectedFounder) {
              // Игрок уже выбрал карту - показываем кнопку "Завершить ход"
              console.log('Adding finish turn button')
              this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), {
                primary: true,
                disabled: mustPlaceFounderFounderSelection,
                tooltip: mustPlaceFounderFounderSelection ? _('Вы должны разместить карту основателя в один из отделов перед завершением хода') : undefined,
                id: 'finish-turn-button',
              })
            } else {
              console.log('No selected founder, not showing finish turn button')
            }
            // Карты выбираются кликом, размещение карты тоже через клик
            break
        }
      }
    },

    ///////////////////////////////////////////////////
    //// Utility methods

    /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

    ///////////////////////////////////////////////////
    //// Player's action

    /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */

    // Example:

    onCardClick: function (card_id) {
      console.log('onCardClick', card_id)

      this.bgaPerformAction('actPlayCard', {
        card_id,
      }).then(() => {
        // What to do after the server call if it succeeded
        // (most of the time, nothing, as the game will react to notifs / change of state instead)
      })
    },

    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications

    /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your itarenagame.game.php file.
        
        */
    setupNotifications: function () {
      console.log('notifications subscriptions setup')

      // automatically listen to the notifications, based on the `notif_xxx` function on this class.
      this.bgaSetupPromiseNotifications()
    },

    // TODO: from this point and below, you can write your game notifications handling methods

    // Round updates
    notif_roundStart: async function (args) {
      console.log('notif_roundStart called with args:', args)
      console.log('cubeFace from notification:', args.cubeFace, 'type:', typeof args.cubeFace)

      // Обновляем данные в gamedatas
      if (args.cubeFace !== undefined && args.cubeFace !== null) {
        this.gamedatas.cubeFace = args.cubeFace
      }

      // Обновляем данные о раунде
      if (args.round !== undefined) {
        this.gamedatas.round = args.round
      }
      if (args.stageName !== undefined) {
        this.gamedatas.stageName = args.stageName
      }
      if (args.phaseName !== undefined) {
        this.gamedatas.phaseName = args.phaseName
      }

      // Обновляем карты событий в gamedatas
      const eventCards = args.roundEventCards || (args.eventCard ? [args.eventCard] : [])
      if (eventCards.length > 0) {
        this.gamedatas.roundEventCards = eventCards
        this.gamedatas.roundEventCard = eventCards[0] || null
      }
      console.log('roundStart eventCards', eventCards)

      this._renderRoundBanner(args.round, this.totalRounds, args.stageName, args.cubeFace, args.phaseName)
      // Обновляем отображение кубика
      this._updateCubeFace(args.cubeFace)
      this._renderEventCards(eventCards)
      this._renderRoundEventCards(eventCards)
      if (args.players) {
        // Обновляем деньги игрока
        Object.entries(args.players).forEach(([playerId, data]) => {
          // Обновляем деньги игрока
          if (!this.gamedatas.players[playerId]) {
            // Если игрок не найден, добавляем его
            this.gamedatas.players[playerId] = data
          } else {
            // Если игрок найден, обновляем его данные
            Object.assign(this.gamedatas.players[playerId], data)
          }
        })
        if (args.founders) {
          this.gamedatas.founders = args.founders
          Object.entries(args.founders).forEach(([playerId, founder]) => {
            if (this.gamedatas.players[playerId]) {
              this.gamedatas.players[playerId].founder = founder
            }
          })
        }
        this._applyLocalFounders()
        const activeFromNotif = this._extractActivePlayerId(args) // Идентификатор активного игрока
        if (activeFromNotif !== null) {
          // Если идентификатор активного игрока не равен null
          this.gamedatas.gamestate = this.gamedatas.gamestate || {}
          this.gamedatas.gamestate.active_player = activeFromNotif // Идентификатор активного игрока
        }
        const activeId = activeFromNotif ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id // Идентификатор активного игрока
        this._renderPlayerMoney(this.gamedatas.players, activeId) // Обновляем деньги игрока
        this._renderFounderCard(this.gamedatas.players, activeId)
        this._toggleActivePlayerHand(activeId)
        this._updateHandHighlight(activeId)
      }
    },

    notif_gameSetupStart: async function (args) {
      console.log('=== notif_gameSetupStart CALLED ===')
      console.log('notif_gameSetupStart called with args:', args)

      // После начала подготовки, данные о картах основателей должны быть уже в gamedatas
      // Проверяем и обновляем activeFounderOptions, если нужно
      if (!this.gamedatas.isTutorialMode && this.gamedatas.allPlayersFounderOptions) {
        console.log('gameSetupStart - Checking founder options from allPlayersFounderOptions')
        const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
        if (activePlayerId && this.gamedatas.allPlayersFounderOptions[activePlayerId]) {
          this.gamedatas.activeFounderOptions = this.gamedatas.allPlayersFounderOptions[activePlayerId]
          console.log('gameSetupStart - Updated activeFounderOptions for player ' + activePlayerId)
        }
      }

      // Отображаем начало подготовительного этапа
      const banner = document.getElementById('round-banner')
      if (banner) {
        const stageName = args.stageName || _('Подготовка к игре')
        const content = banner.querySelector('.round-banner__content')
        if (content) {
          content.textContent = _('🔄 ЭТАП 1: ${stageName}').replace('${stageName}', stageName)
        } else {
          banner.textContent = _('🔄 ЭТАП 1: ${stageName}').replace('${stageName}', stageName)
        }
        banner.className = 'round-banner round-banner--setup'
        banner.style.backgroundColor = '#FFA500'
        banner.style.color = '#FFFFFF'
        banner.style.fontSize = '20px'
        banner.style.fontWeight = 'bold'
        banner.style.padding = '10px 0px'
        banner.style.textAlign = 'center'
      }
      this._renderGameSetup()
    },

    notif_gameSetupComplete: async function (args) {
      console.log('=== notif_gameSetupComplete CALLED ===')
      console.log('notif_gameSetupComplete called with args:', args)
      console.log('Current game state:', this.gamedatas?.gamestate?.name)
      console.log('Expected: GameSetup, Next state should be FounderSelection (in main mode)')
      // Обновляем отображение после завершения подготовки
      const banner = document.getElementById('round-banner')
      if (banner) {
        const content = banner.querySelector('.round-banner__content')
        if (content) {
          content.textContent = _('✅ Подготовка завершена! Переход к игре...')
        } else {
          banner.textContent = _('✅ Подготовка завершена! Переход к игре...')
        }
        banner.style.backgroundColor = '#4CAF50'
      }
    },

    notif_playerReadyForGame: async function (args) {
      // Уведомление о готовности игроков
      console.log('notif_playerReadyForGame called with args:', args)
      // Обновляем информацию о готовности игроков
      const readyCount = args.readyCount || 0
      const totalPlayers = args.totalPlayers || 0

      // Обновляем кнопки действий, чтобы скрыть кнопку для игрока, который уже нажал
      const stateName = this.gamedatas?.gamestate?.name || ''
      if (stateName === 'GameSetup') {
        this.statusBar.removeActionButtons()
        this.onUpdateActionButtons(stateName, {
          readyPlayers: args.readyPlayers || [],
          allReady: readyCount === totalPlayers,
          readyCount: readyCount,
          totalPlayers: totalPlayers,
        })
      }
    },

    notif_gameStart: async function (args) {
      console.log('=== notif_gameStart CALLED ===')
      console.log('notif_gameStart called with args:', args)
      console.log('Current game state after gameStart:', this.gamedatas?.gamestate?.name)
      console.log('Expected next state: FounderSelection (in main mode)')

      // Отображаем начало игры
      const banner = document.getElementById('round-banner')
      if (banner) {
        const stageName = args.stageName || _('Начало игры')
        const content = banner.querySelector('.round-banner__content')
        if (content) {
          content.textContent = _('🎮 ЭТАП 2: ${stageName}').replace('${stageName}', stageName)
        } else {
          banner.textContent = _('🎮 ЭТАП 2: ${stageName}').replace('${stageName}', stageName)
        }
        banner.className = 'round-banner round-banner--game-start'
        banner.style.backgroundColor = '#2196F3'
        banner.style.color = '#FFFFFF'
        banner.style.fontSize = '20px'
        banner.style.fontWeight = 'bold'
        banner.style.padding = '10px 0px'
        banner.style.textAlign = 'center'
      }
    },

    notif_gameEnd: async function (args) {
      const el = document.getElementById('round-banner')
      if (el) {
        const content = el.querySelector('.round-banner__content')
        if (content) {
          content.textContent = _('Игра окончена')
        } else {
          el.textContent = _('Игра окончена')
        }
      }
    },

    notif_founderSelected: async function (args) {
      // Обновляем данные о выборе карты основателя
      const playerId = Number(args.player_id || 0)
      const cardId = Number(args.card_id || 0)
      const founder = args.founder || null

      console.log('notif_founderSelected called:', { playerId, cardId, founder })

      if (playerId > 0 && founder) {
        // Обновляем данные в gamedatas
        if (!this.gamedatas.players[playerId]) {
          this.gamedatas.players[playerId] = {}
        }
        this.gamedatas.players[playerId].founder = { ...founder }

        // Обновляем данные в founders
        if (!this.gamedatas.founders) {
          this.gamedatas.founders = {}
        }
        this.gamedatas.founders[playerId] = { ...founder }

        // Удаляем опции выбора для этого игрока
        if (this.gamedatas.founderOptions) {
          this.gamedatas.founderOptions = []
        }
        if (this.gamedatas.activeFounderOptions) {
          this.gamedatas.activeFounderOptions = []
        }
        if (this.gamedatas.allPlayersFounderOptions && this.gamedatas.allPlayersFounderOptions[playerId]) {
          delete this.gamedatas.allPlayersFounderOptions[playerId]
        }

        // Очищаем руку от карт выбора
        const handContainer = document.getElementById('active-player-hand-cards')
        if (handContainer && Number(playerId) === Number(this.player_id)) {
          handContainer.innerHTML = ''
          handContainer.classList.remove('active-player-hand__center--selecting')
        }

        // Обновляем отображение карты основателя
        this._renderFounderCard(this.gamedatas.players, playerId)

        // Если карта универсальная, она остается на руке для размещения
        // Если нет - она автоматически размещена в отделе
        const department = founder.department || 'universal'
        if (department !== 'universal') {
          // Карта автоматически размещена, обновляем отображение
          this._renderFounderCard(this.gamedatas.players, playerId)
        }
      }
    },

    notif_founderPlaced: async function (args) {
      // Обновляем данные о размещении карты основателя (может быть автоматическое или ручное размещение)
      const playerId = Number(args.player_id || 0)
      const department = String(args.department || '')
        .trim()
        .toLowerCase()
      const founder = args.founder || null

      if (playerId > 0 && founder) {
        // Обновляем данные в gamedatas
        if (!this.gamedatas.players[playerId]) {
          this.gamedatas.players[playerId] = {}
        }
        if (!this.gamedatas.players[playerId].founder) {
          this.gamedatas.players[playerId].founder = {}
        }
        // Сначала обновляем данные карты, затем устанавливаем отдел
        Object.assign(this.gamedatas.players[playerId].founder, founder)
        // Устанавливаем отдел после обновления данных, чтобы он не перезаписывался
        this.gamedatas.players[playerId].founder.department = department

        // Обновляем данные в founders
        if (!this.gamedatas.founders) {
          this.gamedatas.founders = {}
        }
        this.gamedatas.founders[playerId] = { ...founder, department: department }

        // Обновляем отображение карты основателя
        this._renderFounderCard(this.gamedatas.players, playerId)

        // Если карта была размещена из руки (была универсальной), удаляем её из руки
        // После размещения карта должна быть в отделе, а не на руке
        const handContainer = document.getElementById('active-player-hand-cards')
        if (handContainer && Number(playerId) === Number(this.player_id)) {
          // Удаляем карту из руки после размещения (она теперь в отделе)
          handContainer.innerHTML = ''
          // Сбрасываем выделение
          this._setDepartmentHighlight(false)
          this._setHandHighlight(false)
        }

        // Обновляем локальные данные
        this.localFounders = this.localFounders || {}
        this.localFounders[playerId] = department

        // Обновляем отображение карты основателя только если это активный игрок
        const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
        if (activePlayerId && Number(activePlayerId) === Number(playerId)) {
          // Это карта активного игрока, обновляем отображение
          this._renderFounderCard(this.gamedatas.players, playerId)
          this._updateHandHighlight(playerId)

          // Обновляем состояние кнопки завершения хода: разблокируем, так как карта размещена
          if (this.finishTurnButton && this.finishTurnButton instanceof HTMLElement) {
            this.finishTurnButton.disabled = false
            this.finishTurnButton.title = ''
            this.finishTurnButton.classList.remove('disabled')
          }
        }
        // Если карта была размещена другим игроком, данные обновлены, но отображение не меняется
        // так как на экране показывается только карта активного игрока
      }
    },

    // Helpers
    _renderRoundBanner: function (round, total, stageName, cubeFace, phaseName) {
      // Текущий раунд, Общее количество раундов, Название этапа, Значение кубика на раунд
      //
      const el = document.getElementById('round-banner')
      if (!el) return
      const title = _('Раунд ${round}/${total}').replace('${round}', String(round)).replace('${total}', String(total))
      const name = stageName || '' // Название этапа
      const phase = phaseName ? ` — ${_('Фаза')}: ${phaseName}` : ''
      const cube = cubeFace ? ` — ${_('Кубик')}: ${cubeFace}` : ''
      const text = (name ? `${title} — ${name}` : title) + phase + cube
      const content = el.querySelector('.round-banner__content')
      if (content) {
        content.textContent = text
      } else {
        el.textContent = text
      }
      this._highlightRoundMarker(round)
    },
    _renderGameSetup: function () {
      // Отображает информацию о подготовке игры
      const banner = document.getElementById('round-banner')
      if (banner) {
        const content = banner.querySelector('.round-banner__content')
        if (content) {
          content.textContent = _('🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ')
        } else {
          banner.textContent = _('🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ')
        }
        banner.className = 'round-banner round-banner--setup'
        banner.style.backgroundColor = '#FFA500'
        banner.style.color = '#FFFFFF'
        banner.style.fontSize = '20px'
        banner.style.fontWeight = 'bold'
        banner.style.padding = '10px 0px'
        banner.style.textAlign = 'center'
      }

      // Отображаем индикаторы игроков на плашете событий
      // Ждем, пока трек раундов будет отрендерен
      setTimeout(() => {
        const roundPanel = document.querySelector('.round-panel__wrapper')
        if (roundPanel) {
          console.log('Calling _renderPlayerIndicators from _renderGameSetup')
          this._renderPlayerIndicators(roundPanel)
        } else {
          console.error('roundPanel not found in _renderGameSetup!')
        }
      }, 300)

      console.log('Game setup in progress...')
    },

    _renderPlayerIndicators: function (container) {
      console.log('_renderPlayerIndicators called', container)

      // Получаем всех игроков
      const players = this.gamedatas?.players || {}
      const playerIds = Object.keys(players)
        .map((id) => parseInt(id))
        .sort((a, b) => a - b)

      console.log('Players:', players, 'PlayerIds:', playerIds)

      // Получаем верхний блок трека навыков (жетоны)
      const tokensRow = container.querySelector('.round-panel__skills-track-row--tokens')
      if (!tokensRow) {
        console.error('tokensRow not found!')
        return
      }

      const tokenColumns = tokensRow.querySelectorAll('.round-panel__skill-token-column')
      if (tokenColumns.length < 5) {
        console.error('Not enough token columns found:', tokenColumns.length)
        return
      }

      // Маппинг игроков на колонки (блоки 1, 2, 4, 5):
      // Игрок 0 -> блок 1 (индекс 0)
      // Игрок 1 -> блок 2 (индекс 1)
      // Игрок 2 -> блок 4 (индекс 3)
      // Игрок 3 -> блок 5 (индекс 4)
      const playerColumnMapping = {
        0: 0, // Первый игрок -> блок 1
        1: 1, // Второй игрок -> блок 2
        2: 3, // Третий игрок -> блок 4
        3: 4, // Четвертый игрок -> блок 5
      }

      // Очищаем все слоты из колонок
      tokenColumns.forEach((column) => {
        const slots = column.querySelectorAll('.round-panel__skill-slot')
        slots.forEach((slot) => {
          slot.remove()
        })
      })

      // Размещаем фишки навыков (skill) игроков в соответствующих колонках
      playerIds.forEach((playerId, playerIndex) => {
        if (playerIndex >= 4) return // Максимум 4 игрока

        const player = players[playerId]
        if (!player) {
          console.warn('Player not found:', playerId)
          return
        }

        const targetColumnIndex = playerColumnMapping[playerIndex]
        if (targetColumnIndex === undefined || !tokenColumns[targetColumnIndex]) {
          console.warn('Target column not found:', targetColumnIndex)
          return
        }

        const targetColumn = tokenColumns[targetColumnIndex]

        // Создаем слот для навыка этого игрока
        const slot = document.createElement('div')
        slot.className = 'round-panel__skill-slot'
        slot.dataset.playerId = playerId
        slot.dataset.columnIndex = targetColumnIndex
        slot.dataset.skillType = 'player-indicator'
        slot.style.position = 'absolute'
        slot.style.left = '50%'
        slot.style.top = '50%'
        slot.style.transform = 'translate(-50%, -50%)'
        slot.style.display = 'flex'
        slot.style.alignItems = 'center'
        slot.style.justifyContent = 'center'
        slot.style.width = '42px'
        slot.style.height = '42px'
        slot.style.zIndex = '11'

        const circle = document.createElement('div')
        circle.className = 'round-panel__skill-circle'
        circle.dataset.playerId = playerId
        let color = String(player.color || '').trim()
        // Если цвет не начинается с #, добавляем его
        if (color && !color.startsWith('#')) {
          color = '#' + color
        }
        // Если цвет пустой, используем белый по умолчанию
        if (!color || color === '#') {
          color = '#ffffff'
        }
        circle.style.backgroundColor = color
        circle.style.width = '34px'
        circle.style.height = '34px'
        circle.style.borderRadius = '50%'
        circle.style.border = '2px solid rgba(255, 255, 255, 0.9)'
        circle.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.4), inset 0 0 4px rgba(255, 255, 255, 0.3)'
        circle.style.display = 'block'
        circle.style.position = 'relative'
        circle.style.zIndex = '12'
        slot.appendChild(circle)
        targetColumn.appendChild(slot)

        console.log(`Created skill indicator for player ${playerId} in column ${targetColumnIndex}`, slot)
      })

      console.log(`Total skill indicators created: ${playerIds.length}`)
    },

    _renderGameModeBanner: function () {
      // Отображает индикатор режима игры
      const el = document.getElementById('game-mode-banner')
      if (!el) return

      const isTutorial = this.isTutorialMode
      const modeText = isTutorial ? _('Режим: Обучающий') : _('Режим: Основной')
      const modeClass = isTutorial ? 'game-mode-banner--tutorial' : 'game-mode-banner--main'
      const modeValue = this.gameMode || 1

      // Добавляем детальную информацию для отладки
      el.textContent = modeText
      el.className = `game-mode-banner ${modeClass}`
      el.title = `Режим игры: ${modeText}\nЗначение: ${modeValue} (1=Обучающий, 2=Основной)\n\n⚠️ Для выбора режима игры создайте новую игру и нажмите "Customize your settings..." при создании.`

      console.log('Game mode banner rendered:', {
        isTutorial: isTutorial,
        gameMode: this.gameMode,
        modeText: modeText,
      })
    },
    /*
        Example:
        
        notif_cardPlayed: async function( args )
        {
            console.log( 'notif_cardPlayed' );
            console.log( args );
            
            // Note: args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
    _renderRoundEventCards: function (roundEventCards) {
      console.log('renderRoundEventCards', roundEventCards)
      const container = document.querySelector('.events-cube-section')
      if (!container) return

      let list = container.querySelector('.round-event-cards')
      if (!list) {
        list = document.createElement('div')
        list.className = 'round-event-cards'
        container.appendChild(list)
      }

      list.innerHTML = ''

      if (!roundEventCards || roundEventCards.length === 0) {
        list.textContent = _('Карта события отсутствует')
        return
      }

      roundEventCards.forEach((card) => {
        const cardDiv = document.createElement('div')
        cardDiv.className = 'round-event-card'
        cardDiv.textContent = card.name || card.card_id || _('Карта события')
        list.appendChild(cardDiv)
      })
    },

    _updateCubeFace: function (cubeFace) {
      console.log('updateCubeFace called', cubeFace, 'type:', typeof cubeFace)
      const display = document.getElementById('cube-face-display')
      if (!display) {
        console.warn('cube-face-display element not found')
        return
      }
      const value = cubeFace ? String(cubeFace).trim() : ''
      console.log('Setting cube face value to:', value)
      display.textContent = value

      // Если значение пустое, показываем предупреждение
      if (!value) {
        console.warn('Cube face value is empty!')
      }
    },

    _renderRoundTrack: function (totalRounds) {
      const roundsTrack = document.querySelector('.round-panel__rounds-track')
      if (!roundsTrack) return

      const columns = roundsTrack.querySelectorAll('.round-track-column')
      columns.forEach((column, index) => {
        const roundNumber = index + 1
        const circleContainer = column.querySelector('.round-track-column__circle')
        if (circleContainer) {
          circleContainer.innerHTML = ''
          if (roundNumber <= totalRounds) {
            const marker = document.createElement('div')
            marker.className = 'round-track__circle'
            marker.dataset.round = String(roundNumber)
            marker.innerHTML = `<span>${roundNumber}</span>`
            circleContainer.appendChild(marker)
          }
        }
      })
      this._highlightRoundMarker(this.gamedatas?.round || 1)
    },

    _highlightRoundMarker: function (round) {
      const roundsTrack = document.querySelector('.round-panel__rounds-track')
      if (!roundsTrack) return
      const markers = roundsTrack.querySelectorAll('.round-track__circle')
      markers.forEach((marker) => {
        if (marker.dataset.round === String(round)) {
          marker.classList.add('round-track__circle--active')
        } else {
          marker.classList.remove('round-track__circle--active')
        }
      })
    },

    _renderEventCards: function (eventCards) {
      const panelBody = document.querySelector('#event-card-panel .event-card-panel__body')
      if (!panelBody) return

      panelBody.innerHTML = ''

      if (!eventCards || eventCards.length === 0) {
        panelBody.textContent = _('Карта события отсутствует')
        return
      }

      const cardsHtml = eventCards
        .map((card, index) => {
          const typeArg = card.card_type_arg || card.card_id
          let cardData = this._getEventCardData(typeArg)
          if (!cardData) {
            cardData = card
          }
          if (typeArg && cardData) {
            this.eventCardsData = this.eventCardsData || {}
            this.eventCardsData[typeArg] = cardData
          }

          const imageUrl = cardData?.image_url ? (cardData.image_url.startsWith('http') ? cardData.image_url : `${g_gamethemeurl}${cardData.image_url}`) : ''
          const powerRound = cardData && typeof cardData.power_round !== 'undefined' ? cardData.power_round : '—'
          const phase = cardData?.phase || '—'
          const effectText = cardData?.effect_description || cardData?.effect || '—'

          return `
            <div class="event-card">
              <div class="event-card__badge">${_('Карта')} ${index + 1}</div>
              ${imageUrl ? `<img src="${imageUrl}" alt="${cardData?.name || ''}" class="event-card__image" />` : ''}
              <div class="event-card__content">
                <div class="event-card__title">${cardData?.name || _('Карта события')}</div>
                <div class="event-card__description">${cardData?.description || ''}</div>
                <div class="event-card__meta">
                  <div class="event-card__meta-item">
                    <span class="event-card__meta-label">${_('Power round')}:</span>
                    <span class="event-card__meta-value">${powerRound}</span>
                  </div>
                  <div class="event-card__meta-item">
                    <span class="event-card__meta-label">${_('Phase')}:</span>
                    <span class="event-card__meta-value">${phase}</span>
                  </div>
                </div>
                <div class="event-card__effect">
                  <span class="event-card__meta-label">${_('Effect')}:</span>
                  <span class="event-card__meta-value">${effectText}</span>
                </div>
              </div>
            </div>
          `
        })
        .join('')

      panelBody.innerHTML = cardsHtml
    },
    _setupCardZoom: function () {
      const container = document.querySelector('.game-layout')
      if (!container) return

      container.addEventListener('click', (event) => {
        const target = event.target
        if (!(target instanceof HTMLElement)) return

        const card = target.closest('.event-card, .founder-card, .employee-card, .badge-card')
        if (!card || !(card instanceof HTMLElement)) return

        card.classList.toggle('card-zoomed')
      })
    },
    _getEventCardData: function (cardTypeArg) {
      if (this.eventCardsData?.[cardTypeArg]) {
        return this.eventCardsData[cardTypeArg]
      }
      if (this.gamedatas?.eventCards?.[cardTypeArg]) {
        return this.gamedatas.eventCards[cardTypeArg]
      }
      return null
    },
    _renderBadgers: function (badgers) {
      const panelBody = document.querySelector('.badgers-panel__body')
      if (!panelBody) return

      this.badgersData = Array.isArray(badgers) ? badgers : []
      panelBody.innerHTML = ''

      if (!this.badgersData.length) {
        panelBody.textContent = _('Монеты отсутствуют')
        return
      }

      const coins = [...this.badgersData].sort((a, b) => (a.value || 0) - (b.value || 0))
      const html = coins
        .map((coin) => {
          const imageUrl = coin.image_url ? (coin.image_url.startsWith('http') ? coin.image_url : `${g_gamethemeurl}${coin.image_url}`) : ''
          const label = coin.display_label || coin.label || coin.name || coin.value || ''
          const available = typeof coin.available_quantity === 'number' ? coin.available_quantity : coin.available_quantity ?? ''
          const initial = typeof coin.initial_quantity === 'number' ? coin.initial_quantity : coin.initial_quantity ?? ''
          const counts = available !== '' && initial !== '' ? `${available}/${initial}` : ''

          return `
            <div class="badgers-panel__coin" data-value="${coin.value ?? ''}">
              ${imageUrl ? `<img src="${imageUrl}" alt="${coin.name || ''}" class="badgers-panel__image" />` : ''}
              <div class="badgers-panel__info">
                <div class="badgers-panel__label">${label}</div>
                ${counts ? `<div class="badgers-panel__counts">${counts}</div>` : ''}
              </div>
            </div>
          `
        })
        .join('')

      panelBody.innerHTML = html
    },
    _renderPlayerMoney: function (players, targetPlayerId) {
      console.log('_renderPlayerMoney called for player:', targetPlayerId)
      console.log('Players data:', players)

      if (players && players[targetPlayerId]) {
        const player = players[targetPlayerId]
        const badgers = player.badgers || 0
        console.log('Player ' + targetPlayerId + ' badgers:', badgers, '(expected: 5)')
        if (badgers !== 5) {
          console.warn('WARNING: Player ' + targetPlayerId + ' has incorrect badgers! Expected: 5, Got: ' + badgers)
        }
      }
      // Обновляем деньги игрока
      const panelBody = document.querySelector('.player-money-panel__body') // Обновляем деньги игрока
      if (!panelBody) return

      const fallbackId = this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
      const playerId = targetPlayerId ?? fallbackId // Идентификатор игрока
      if (!playerId) {
        // Если игрок не найден, очищаем панель
        panelBody.innerHTML = '' // Очищаем панель
        return
      }

      const playerData = this._findPlayerData(players, playerId) // Получаем данные игрока
      if (!playerData) {
        // Если игрок не найден, очищаем панель
        panelBody.innerHTML = ''
        return
      }

      const amount = Number(playerData.badgers ?? 0) || 0 // Количество баджерсов
      const coinData = this._getBestCoinForAmount(amount)
      const imageUrl = coinData?.image_url ? (coinData.image_url.startsWith('http') ? coinData.image_url : `${g_gamethemeurl}${coinData.image_url}`) : `${g_gamethemeurl}img/money/1.png`
      let color = String(playerData.color || '').trim()
      if (color && !color.startsWith('#')) {
        color = `#${color.replace(/^#+/, '')}`
      }
      const panel = panelBody.closest('.player-money-panel')
      if (panel) {
        panel.style.setProperty('--player-money-color', color || 'rgba(255,255,255,0.6)')
        panel.setAttribute('data-player-id', String(playerId))
        const colorBadge = panel.querySelector('.player-money-panel__color-badge')
        if (colorBadge) {
          colorBadge.style.backgroundColor = color || 'rgba(255, 255, 255, 0.4)'
        }
      }

      this._updatePlayerBoardImage(color)

      panelBody.innerHTML = `
        <div class="player-money-panel__balance">
          <img src="${imageUrl}" alt="${coinData?.name || _('Баджерсы')}" class="player-money-panel__icon" />
          <span class="player-money-panel__amount">${amount}</span>
        </div>
      `
      this._renderFounderCard(players, playerId)
    },
    _renderFounderCard: function (players, targetPlayerId) {
      const containers = {
        'sales-department': document.querySelector('.sales-department__body'),
        'back-office': document.querySelector('.back-office__body'),
        'technical-department': document.querySelector('.technical-department__body'),
      }
      const handContainer = document.getElementById('active-player-hand-cards')

      this.pendingFounderMove = null // Сбрасываем ожидание перемещения карты основателя
      this._setDepartmentHighlight(false) // Сбрасываем выделение отдела
      this._setHandHighlight(false)

      const fallbackId = this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
      const playerId = targetPlayerId ?? fallbackId

      Object.values(containers).forEach((container) => {
        if (container) {
          container.innerHTML = ''
        }
      })
      if (handContainer) {
        // Не очищаем руку, если там есть карты для выбора (в состоянии FounderSelection)
        const hasSelectableCards = handContainer.querySelector('.founder-card--selectable')
        const currentState = this.gamedatas?.gamestate?.name
        const isFounderSelection = currentState === 'FounderSelection'
        const isMainMode = !this.isTutorialMode
        const isCurrentPlayer = Number(playerId) === Number(this.player_id)

        // Если это состояние выбора и текущий игрок, не очищаем контейнер
        if (isFounderSelection && isMainMode && isCurrentPlayer && hasSelectableCards) {
          console.log('_renderFounderCard - Skipping hand container clear (has selectable cards)')
          // Не очищаем контейнер, если там есть карты для выбора
        } else if (!hasSelectableCards) {
          handContainer.innerHTML = ''
        }

        // Убираем выделение только если нет карт для выбора
        if (!hasSelectableCards) {
          handContainer.classList.remove('active-player-hand__center--selecting')
        }
      }

      // Проверяем, есть ли карты для выбора (в основном режиме)
      const currentState = this.gamedatas?.gamestate?.name
      const isFounderSelection = currentState === 'FounderSelection'
      const isMainMode = !this.isTutorialMode

      console.log('_renderFounderCard - Checking for selection cards:', {
        currentState,
        isFounderSelection,
        isMainMode,
        playerId,
        currentPlayerId: this.player_id,
        isCurrentPlayer: Number(playerId) === Number(this.player_id),
        founderOptions: this.gamedatas?.founderOptions?.length || 0,
        activeFounderOptions: this.gamedatas?.activeFounderOptions?.length || 0,
      })

      if (isFounderSelection && isMainMode && Number(playerId) === Number(this.player_id)) {
        // Показываем карты для выбора
        const founderOptions = this.gamedatas?.founderOptions || this.gamedatas?.activeFounderOptions || []
        console.log('_renderFounderCard - Found selection state, options count:', founderOptions.length, 'options:', founderOptions)
        if (founderOptions.length > 0) {
          console.log('✅ _renderFounderCard - Rendering selection cards')
          setTimeout(() => {
            this._renderFounderSelectionCards(founderOptions, playerId)
          }, 100)
          return
        } else {
          console.warn('⚠️ _renderFounderCard - No founder options found in selection state!')
          console.warn('gamedatas.founderOptions:', this.gamedatas?.founderOptions)
          console.warn('gamedatas.activeFounderOptions:', this.gamedatas?.activeFounderOptions)
        }
      }

      const playerData = this._findPlayerData(players, playerId)
      if (!playerData || !playerData.founder) {
        if (containers['sales-department']) {
          containers['sales-department'].innerHTML = `<div class="founder-card founder-card--placeholder">${_('Карта основателя не выбрана')}</div>`
        }
        return
      }

      const founder = playerData.founder
      const rawDepartment = String(founder.department || '')
        .trim()
        .toLowerCase()
      let department = rawDepartment
      if (!containers[department]) {
        department = rawDepartment
      }

      const imageUrl = founder.img ? (founder.img.startsWith('http') ? founder.img : `${g_gamethemeurl}${founder.img}`) : ''
      const name = founder.name || ''
      const speciality = founder.speciality || founder.typeName || ''
      const effect = founder.effectDescription || founder.effect || ''
      const effectText = effect || _('Описание отсутствует')

      // Определяем, показывать ли карту или рубашку
      const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
      const isMyTurn = activePlayerId && Number(activePlayerId) === Number(this.player_id) && Number(playerId) === Number(this.player_id)

      // Если карта в отделе, показываем её всегда
      if (rawDepartment !== 'universal') {
        const container = containers[department] || containers['sales-department']
        if (container) {
          const cardMarkup = `
            <div class="founder-card" data-player-id="${playerId}" data-department="${department}">
              ${imageUrl ? `<img src="${imageUrl}" alt="${name}" class="founder-card__image" />` : ''}
            </div>
          `
          container.innerHTML = cardMarkup
        }
        // Убеждаемся, что карта не в руке (она в отделе)
        if (handContainer) {
          handContainer.innerHTML = ''
        }
        return
      }

      // Если карта на руке (universal), проверяем, показывать ли её или рубашку
      if (handContainer) {
        handContainer.dataset.playerId = String(playerId)

        if (isMyTurn) {
          // Это мой ход, показываю свою карту
          const cardMarkup = `
            <div class="founder-card" data-player-id="${playerId}" data-department="${department}">
              ${imageUrl ? `<img src="${imageUrl}" alt="${name}" class="founder-card__image" />` : ''}
            </div>
          `
          handContainer.innerHTML = cardMarkup
        } else {
          // Это ход другого игрока или не мой ход, показываю рубашку
          const backImageUrl = `${g_gamethemeurl}img/back-cards.png`
          const backCardMarkup = `
            <div class="founder-card founder-card--back" data-player-id="${playerId}" data-department="${department}">
              <img src="${backImageUrl}" alt="${_('Рубашка карты')}" class="founder-card__image" />
            </div>
          `
          handContainer.innerHTML = backCardMarkup
        }
      }
    },
    _renderFounderSelectionCards: function (founderOptions, playerId) {
      console.log('🎴 _renderFounderSelectionCards called with:', {
        founderOptions,
        playerId,
        optionsCount: founderOptions?.length,
        options: founderOptions,
      })

      if (!founderOptions || founderOptions.length === 0) {
        console.warn('⚠️ No founder options provided!')
        return
      }

      // Функция для рендеринга карт
      const renderCards = () => {
        const handContainer = document.getElementById('active-player-hand-cards')
        if (!handContainer) {
          console.error('❌ Hand container not found! Trying again...')
          setTimeout(renderCards, 100)
          return
        }

        console.log('✅ Hand container found:', handContainer)
        console.log('Container parent:', handContainer.parentElement)
        console.log('Container computed style:', window.getComputedStyle(handContainer))

        // Убеждаемся, что контейнер видим
        handContainer.style.display = 'flex'
        handContainer.style.visibility = 'visible'
        handContainer.style.opacity = '1'

        // Очищаем контейнер
        handContainer.innerHTML = ''
        handContainer.classList.add('active-player-hand__center--selecting')

        console.log('🎴 Rendering ' + founderOptions.length + ' founder selection cards')

        // Отображаем три карты для выбора
        founderOptions.forEach((founder, index) => {
          const cardId = founder.id || founder.card_id
          const imageUrl = founder.img ? (founder.img.startsWith('http') ? founder.img : `${g_gamethemeurl}${founder.img}`) : ''
          const name = founder.name || _('Неизвестный основатель')

          console.log(`🎴 Creating card ${index + 1}:`, { cardId, name, imageUrl, founder })

          const cardElement = document.createElement('div')
          cardElement.className = 'founder-card founder-card--selectable'
          cardElement.dataset.cardId = cardId
          cardElement.dataset.playerId = playerId
          cardElement.dataset.index = index
          cardElement.title = name
          cardElement.style.cursor = 'pointer'
          cardElement.style.minWidth = '150px'
          cardElement.style.maxWidth = '200px'
          cardElement.style.flex = '0 0 auto'

          if (imageUrl) {
            const img = document.createElement('img')
            img.src = imageUrl
            img.alt = name
            img.className = 'founder-card__image'
            img.style.width = '100%'
            img.style.height = 'auto'
            img.style.display = 'block'
            cardElement.appendChild(img)
          } else {
            const nameDiv = document.createElement('div')
            nameDiv.textContent = name
            nameDiv.style.padding = '10px'
            nameDiv.style.textAlign = 'center'
            cardElement.appendChild(nameDiv)
          }

          // Добавляем обработчик клика
          cardElement.addEventListener('click', () => {
            console.log('🎴 Card clicked:', cardId)
            this._selectFounderCard(cardId)
          })

          handContainer.appendChild(cardElement)
          console.log(`✅ Card ${index + 1} appended to container`)
        })

        console.log('✅✅✅ Rendered ' + founderOptions.length + ' founder selection cards for player ' + playerId)
        console.log('Container children count:', handContainer.children.length)
        console.log('Container innerHTML length:', handContainer.innerHTML.length)

        // Проверяем, что карты действительно добавлены
        const cards = handContainer.querySelectorAll('.founder-card--selectable')
        console.log('Found cards in container:', cards.length)
        if (cards.length === 0) {
          console.error('❌ ERROR: Cards were not added to container!')
        }
      }

      // Пытаемся отобразить сразу, если DOM готов
      renderCards()
    },

    _selectFounderCard: function (cardId) {
      if (!this.checkAction('actSelectFounder')) {
        console.warn('Action actSelectFounder is not available')
        return
      }

      console.log('Selecting founder card:', cardId)
      this.ajaxcall(
        `/itarenagame/itarenagame/actSelectFounder.html`,
        {
          cardId: cardId,
          lock: true,
        },
        this,
        function (result) {
          console.log('Founder card selected successfully:', result)
        },
        function (error) {
          console.error('Error selecting founder card:', error)
          this.showMessage(error, 'error')
        }
      )
    },

    _findPlayerData: function (players, playerId) {
      if (!players) return null
      const stringId = String(playerId)
      return players[stringId] || players[playerId] || null
    },
    _getBestCoinForAmount: function (amount) {
      if (!this.badgersData || !this.badgersData.length || amount <= 0) {
        return null
      }

      const coins = [...this.badgersData]
        .map((coin) => ({ ...coin, value: Number(coin.value || coin.amount || 0) }))
        .filter((coin) => coin.value > 0)
        .sort((a, b) => a.value - b.value)

      for (let i = coins.length - 1; i >= 0; i--) {
        if (amount >= coins[i].value) {
          return coins[i]
        }
      }

      return coins[0] || null
    },

    _renderPenaltyTokens: function (players) {
      // Отображаем жетоны штрафа на планшете игрока
      // Получаем данные текущего игрока
      const currentPlayerId = this.player_id
      const currentPlayer = players[currentPlayerId]

      console.log('_renderPenaltyTokens called', { players, currentPlayerId, currentPlayer })

      const container = document.querySelector('.player-penalty-tokens__container')
      if (!container) {
        console.error('Penalty tokens container not found!')
        return
      }

      console.log('Penalty tokens container found:', container)

      // Очищаем все колонки
      const columns = container.querySelectorAll('.player-penalty-tokens__column')
      columns.forEach((column) => {
        column.innerHTML = ''
      })

      // Получаем жетоны штрафа для текущего игрока
      const penaltyTokens = currentPlayer?.penaltyTokens || []
      console.log('Penalty tokens for player:', penaltyTokens)

      // Маппинг значений штрафа на названия колонок
      const getColumnName = (penaltyValue) => {
        if (penaltyValue === 0) {
          return null // Пустые жетоны размещаются в start-position
        }
        const absValue = Math.abs(penaltyValue)
        if (absValue === 10) {
          return 'penalty-position-10'
        }
        if (absValue >= 1 && absValue <= 5) {
          return `penalty-position-${absValue}`
        }
        return null
      }

      // Размещаем жетоны в соответствующих колонках
      let startPositionIndex = 1 // Индекс для start-position (1 или 2)
      for (let i = 0; i < penaltyTokens.length; i++) {
        const tokenData = penaltyTokens[i]
        const penaltyValue = tokenData?.value || 0

        const token = document.createElement('div')
        token.className = 'player-penalty-token'
        token.dataset.playerId = currentPlayerId
        token.dataset.tokenOrder = i
        token.dataset.tokenId = tokenData?.token_id || ''

        // Если жетон имеет значение штрафа (не пустой), показываем это визуально
        if (penaltyValue !== 0) {
          token.dataset.penaltyValue = penaltyValue
          token.classList.add('player-penalty-token--filled') // Добавляем класс для заполненного жетона
        }

        // Определяем колонку для размещения жетона
        let targetColumn = null
        if (penaltyValue === 0) {
          // Пустой жетон размещаем в start-position
          targetColumn = container.querySelector(`.start-position-${startPositionIndex}`)
          startPositionIndex++
        } else {
          // Жетон со значением размещаем в соответствующей penalty-position
          const columnName = getColumnName(penaltyValue)
          if (columnName) {
            targetColumn = container.querySelector(`.${columnName}`)
          }
        }

        if (targetColumn) {
          targetColumn.appendChild(token)
          console.log('Penalty token created:', { order: i, value: penaltyValue, column: targetColumn.className, token })
        } else {
          console.warn('Target column not found for token:', { order: i, value: penaltyValue })
        }
      }

      console.log('Penalty tokens rendered:', container.children.length)
    },

    _renderProjectTokensOnBoard: function (projectTokens) {
      console.log('_renderProjectTokensOnBoard called', { projectTokens })

      if (!projectTokens || projectTokens.length === 0) {
        console.log('No project tokens to render')
        return
      }

      // Отображаем каждый жетон в соответствующей позиции
      projectTokens.forEach((tokenData) => {
        const boardPosition = tokenData.board_position
        if (!boardPosition) {
          return
        }

        // Находим div с соответствующим data-label
        const rowElement = document.querySelector(`.project-board-panel__row[data-label="${boardPosition}"]`)
        if (!rowElement) {
          console.warn('Row element not found for position:', boardPosition)
          return
        }

        // Очищаем строку от старых жетонов
        rowElement.innerHTML = ''

        // Создаем элемент жетона
        const tokenElement = document.createElement('div')
        tokenElement.className = 'project-token'
        tokenElement.dataset.tokenId = tokenData.token_id
        tokenElement.dataset.position = boardPosition

        // Создаем изображение жетона
        if (tokenData.image_url) {
          const img = document.createElement('img')
          img.src = tokenData.image_url.startsWith('http') ? tokenData.image_url : `${g_gamethemeurl}${tokenData.image_url}`
          img.alt = tokenData.name || 'Project token'
          img.className = 'project-token__image'
          tokenElement.appendChild(img)
        } else {
          // Если нет изображения, создаем текстовый элемент
          const text = document.createElement('div')
          text.className = 'project-token__text'
          text.textContent = tokenData.name || `Token ${tokenData.number}`
          tokenElement.appendChild(text)
        }

        // Добавляем жетон в строку
        rowElement.appendChild(tokenElement)
        console.log('Rendered project token', tokenData.number, 'at position', boardPosition)
      })
    },

    _renderTaskTokens: function (players) {
      // Отображаем жетоны задач в бэклоге на планшете игрока
      const currentPlayerId = this.player_id
      const currentPlayer = players[currentPlayerId]

      console.log('_renderTaskTokens called', { players, currentPlayerId, currentPlayer })

      // Находим колонку бэклога
      const backlogColumn = document.getElementById('sprint-column-backlog')
      if (!backlogColumn) {
        console.error('Backlog column not found!')
        return
      }

      console.log('Backlog column found:', backlogColumn)

      // Очищаем колонку бэклога от старых жетонов
      const existingTokens = backlogColumn.querySelectorAll('.task-token')
      existingTokens.forEach((token) => token.remove())

      // Получаем жетоны задач для текущего игрока, которые находятся в бэклоге
      const taskTokens = currentPlayer?.taskTokens || []
      const backlogTokens = taskTokens.filter((token) => token.location === 'backlog')

      console.log('Task tokens for player:', taskTokens)
      console.log('Backlog tokens:', backlogTokens)

      // Создаем контейнер для жетонов задач в бэклоге, если его еще нет
      let tokensContainer = backlogColumn.querySelector('.task-tokens-container')
      if (!tokensContainer) {
        tokensContainer = document.createElement('div')
        tokensContainer.className = 'task-tokens-container'
        backlogColumn.appendChild(tokensContainer)
      }

      // Очищаем контейнер
      tokensContainer.innerHTML = ''

      // Отображаем жетоны задач
      backlogTokens.forEach((tokenData, index) => {
        const token = document.createElement('div')
        token.className = 'task-token'
        token.dataset.playerId = currentPlayerId
        token.dataset.tokenId = tokenData?.token_id || ''
        token.dataset.color = tokenData?.color || ''
        token.dataset.location = tokenData?.location || 'backlog'

        // Добавляем класс для цвета жетона
        if (tokenData?.color) {
          token.classList.add(`task-token--${tokenData.color}`)
        }

        // Создаем изображение жетона
        const tokenImage = document.createElement('img')
        const colorData = this._getTaskTokenColorData(tokenData?.color)
        if (colorData && colorData.image_url) {
          tokenImage.src = `${g_gamethemeurl}${colorData.image_url}`
          tokenImage.alt = colorData.name || _('Жетон задачи')
          tokenImage.className = 'task-token__image'
        } else {
          // Если нет изображения, используем цветной круг
          token.style.backgroundColor = this._getTaskTokenColorCode(tokenData?.color)
          token.style.borderRadius = '50%'
        }

        if (tokenImage.src) {
          token.appendChild(tokenImage)
        }

        // Позиционируем жетоны вертикально с небольшим отступом
        token.style.position = 'absolute'
        token.style.left = '50%'
        token.style.transform = 'translateX(-50%)'
        token.style.top = `${20 + index * 50}px`

        tokensContainer.appendChild(token)
        console.log('Task token created:', { index, color: tokenData?.color, token })
      })

      console.log('Task tokens rendered:', backlogTokens.length)
    },

    _getTaskTokenColorData: function (colorId) {
      // Маппинг цветов жетонов задач
      const colorMap = {
        cyan: {
          name: _('Голубой'),
          image_url: 'img/task-tokens/cyan.png',
          color_code: '#00CED1',
        },
        pink: {
          name: _('Розовый'),
          image_url: 'img/task-tokens/pink.png',
          color_code: '#FF69B4',
        },
        orange: {
          name: _('Оранжевый'),
          image_url: 'img/task-tokens/orange.png',
          color_code: '#FF8C00',
        },
        purple: {
          name: _('Фиолетовый'),
          image_url: 'img/task-tokens/purple.png',
          color_code: '#9370DB',
        },
      }
      return colorMap[colorId] || null
    },

    _getTaskTokenColorCode: function (colorId) {
      const colorData = this._getTaskTokenColorData(colorId)
      return colorData?.color_code || '#CCCCCC'
    },

    _updatePlayerBoardImage: function (color) {
      const boardImage = document.querySelector('.player-personal-board__image')
      if (!boardImage) return

      const normalized = this._normalizeColor(color)
      const src = this._getBoardImageForColor(normalized) || boardImage.dataset.defaultSrc || boardImage.src
      boardImage.src = src
    },
    _normalizeColor: function (color) {
      if (!color) return ''
      const trimmed = String(color).trim()
      if (!trimmed) return ''
      if (trimmed.startsWith('#')) {
        return `#${trimmed.slice(1).toLowerCase()}`
      }
      return `#${trimmed.replace(/^#+/, '').toLowerCase()}`
    },
    _getBoardImageForColor: function (normalizedColor) {
      if (!normalizedColor) return null

      const map = {
        '#ffd700': 'player-table-yellow.png',
        '#ff0000': 'player-table-red.png',
        '#00ff00': 'player-table-green.png',
        '#008000': 'player-table-green.png',
        '#0000ff': 'player-table-blue.png',
        '#000080': 'player-table-blue.png',
        '#00a000': 'player-table-green.png',
        '#ffa500': 'player-table-yellow.png',
        '#ffff00': 'player-table-yellow.png',
      }

      const fileName = map[normalizedColor]
      return fileName ? `${g_gamethemeurl}img/table/${fileName}` : null
    },
    _toggleActivePlayerHand: function (activePlayerId) {
      const container = document.getElementById('active-player-hand')
      if (!container) return

      // Рука всегда видна, когда есть активный игрок (чтобы все видели рубашки, если это не их ход)
      if (!activePlayerId) {
        container.hidden = true
        this._setDepartmentHighlight(false)
        return
      }

      // Показываем руку активного игрока всем игрокам
      container.hidden = false

      // Убираем подсветку отделов, если это не мой ход
      if (Number(activePlayerId) !== Number(this.player_id)) {
        this._setDepartmentHighlight(false)
      }
    },
    _getActivePlayerIdFromDatas: function (datas) {
      // Идентификатор активного игрока
      if (!datas) return null
      const state = datas.gamestate || datas.gamestateData || {}
      if (typeof state.active_player !== 'undefined' && state.active_player !== null) {
        const value = Number(state.active_player)
        return Number.isNaN(value) ? null : value
      }
      if (typeof datas.active_player !== 'undefined' && datas.active_player !== null) {
        const value = Number(datas.active_player)
        return Number.isNaN(value) ? null : value
      }
      return null
    },
    _extractActivePlayerId: function (args) {
      // Идентификатор активного игрока
      if (!args) return null
      if (typeof args.activePlayerId !== 'undefined' && args.activePlayerId !== null) {
        const value = Number(args.activePlayerId)
        return Number.isNaN(value) ? null : value
      }
      if (typeof args.active_player !== 'undefined' && args.active_player !== null) {
        const value = Number(args.active_player)
        return Number.isNaN(value) ? null : value
      }
      return null
    },
    _setupHandInteractions: function () {
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) {
        return
      }

      // Удаляем старые обработчики, если они есть
      const oldHandler = handContainer._handClickHandler
      if (oldHandler) {
        handContainer.removeEventListener('click', oldHandler)
      }

      // Создаем новый обработчик
      const handClickHandler = (e) => {
        // В обучающем режиме в GameSetup разрешаем управление картами текущему игроку
        const isTutorialGameSetup = this.gamedatas.isTutorialMode && this.gamedatas.gamestate?.name === 'GameSetup'

        if (!isTutorialGameSetup) {
          // В других режимах проверяем активного игрока
          const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
          if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
            return // Только активный игрок может управлять картами
          }
        }

        const card = e.target.closest('.founder-card')
        if (!card) {
          return
        }

        // Проверяем, что это не рубашка карты
        if (card.classList.contains('founder-card--back')) {
          return // Рубашка карты не кликабельна
        }

        // Проверяем, что карта принадлежит текущему игроку
        const cardOwnerId = Number(card.dataset.playerId || handContainer?.dataset.playerId || 0)
        if (cardOwnerId !== Number(this.player_id)) {
          return // Карта не принадлежит текущему игроку
        }

        const isActive = card.classList.toggle('founder-card--active')
        console.log('Card clicked, isActive:', isActive)
        this._setDepartmentHighlight(isActive)
        this._setHandHighlight(isActive)
      }

      // Сохраняем ссылку на обработчик для возможности удаления
      handContainer._handClickHandler = handClickHandler
      handContainer.addEventListener('click', handClickHandler)
      ;['sales-department', 'back-office', 'technical-department'].forEach((department) => {
        // Добавляем обработчики кликов для отделов
        const container = document.querySelector(`.${department}__body`)
        if (!container) {
          return
        }

        // Удаляем старые обработчики, если они есть
        const oldDeptHandler = container._deptClickHandler
        if (oldDeptHandler) {
          container.removeEventListener('click', oldDeptHandler)
        }

        // Создаем новый обработчик
        const deptClickHandler = () => {
          console.log('Department click handler called for:', department)

          // В обучающем режиме в GameSetup разрешаем размещение карт текущему игроку
          const isTutorialGameSetup = this.gamedatas.isTutorialMode && this.gamedatas.gamestate?.name === 'GameSetup'

          if (!isTutorialGameSetup) {
            // В других режимах проверяем активного игрока
            const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
            if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
              console.log('Not active player, cannot place card')
              return // Только активный игрок может размещать карты
            }
          }

          const activeCard = handContainer?.querySelector('.founder-card--active')
          if (!activeCard) {
            console.log('No active card found')
            return
          }

          if (!container.classList.contains('department-highlight')) {
            console.log('Department not highlighted')
            return
          }

          const ownerId = Number(activeCard.dataset.playerId || handContainer?.dataset.playerId || 0)

          // Проверяем, что карта принадлежит текущему игроку
          if (ownerId !== Number(this.player_id)) {
            console.log('Card does not belong to current player')
            return // Карта не принадлежит текущему игроку
          }

          const founderId = Number(activeCard.dataset.founderId || 0)
          console.log('Placing founder card', founderId, 'in department', department)

          // Вызываем серверное действие для размещения карты
          this._setHandHighlight(false)
          this._setDepartmentHighlight(false)
        }

        // Сохраняем ссылку на обработчик для возможности удаления
        container._deptClickHandler = deptClickHandler
        container.addEventListener('click', deptClickHandler)
      })
    },
    _setDepartmentHighlight: function (enabled) {
      console.log('_setDepartmentHighlight called with enabled:', enabled)
      ;['sales-department', 'back-office', 'technical-department'].forEach((department) => {
        const container = document.querySelector(`.${department}__body`)
        if (!container) {
          console.warn('Department container not found:', department)
          return
        }
        if (enabled) {
          container.classList.add('department-highlight')
          container.setAttribute('data-highlight-label', this._getDepartmentLabel(department))
          console.log('Department highlighted:', department)
        } else {
          container.classList.remove('department-highlight')
          container.removeAttribute('data-highlight-label')
          console.log('Department highlight removed:', department)
        }
      })
    },
    _getDepartmentLabel: function (department) {
      return (
        {
          'sales-department': _('Отдел продаж'),
          'back-office': _('Бэк офис'),
          'technical-department': _('Техотдел'),
        }[department] || department
      )
    },
    _setHandHighlight: function (enabled) {
      console.log('_setHandHighlight called with enabled:', enabled)
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) {
        console.warn('Hand container not found')
        return
      }

      if (enabled) {
        handContainer.classList.add('active-player-hand__center--selecting')
        console.log('Hand highlight enabled')
      } else {
        handContainer.classList.remove('active-player-hand__center--selecting')
        const card = handContainer.querySelector('.founder-card--active')
        if (card) {
          card.classList.remove('founder-card--active')
        }
        console.log('Hand highlight disabled')
      }
    },
    _updateHandHighlight: function (playerId) {
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) {
        return
      }

      if (Number(playerId) !== Number(this.player_id)) {
        this._setHandHighlight(false)
        this._setDepartmentHighlight(false)
        return
      }

      const hasCard = !!handContainer.querySelector('.founder-card')
      if (!hasCard) {
        this._setHandHighlight(false)
        this._setDepartmentHighlight(false)
      }
    },
    _applyLocalFounders: function () {
      if (!this.localFounders) {
        return
      }

      Object.entries(this.localFounders).forEach(([playerId, department]) => {
        if (this.gamedatas?.players?.[playerId]?.founder) {
          this.gamedatas.players[playerId].founder.department = department
        }
        if (this.gamedatas?.founders?.[playerId]) {
          this.gamedatas.founders[playerId].department = department
        }
      })
    },
  })
})

function _updateHandSelection(handContainer, enabled) {
  if (!handContainer) return
  if (enabled) {
    handContainer.classList.add('active-player-hand__center--selecting')
  } else {
    handContainer.classList.remove('active-player-hand__center--selecting')
  }
}
