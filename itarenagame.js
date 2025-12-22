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
      console.log('🔴🔴🔴 FILE VERSION CHECK - 2024-12-12-v15 🔴🔴🔴')

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
                                ${['red-circle-1',  'red-square', 'red-hex', 'red-circle-2'].map((label) => `<div class="project-board-panel__row" data-label="${label}"></div>`).join('')}
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
                <!-- Модальное окно для выбора специалистов -->
                <div id="specialist-selection-modal" class="specialist-selection-modal">
                  <div class="specialist-selection-modal__content">
                    <div class="specialist-selection-modal__header">
                      <div class="specialist-selection-modal__title" id="specialist-selection-modal-title">Выберите карты сотрудников</div>
                      <div class="specialist-selection-modal__subtitle" id="specialist-selection-modal-subtitle">Выбрано: 0/3</div>
                    </div>
                    <div class="specialist-selection-modal__body" id="specialist-selection-modal-body"></div>
                    <div class="specialist-selection-modal__footer">
                      <button id="specialist-selection-modal-confirm-btn" class="specialist-selection-modal__confirm-btn" disabled>Применить</button>
                    </div>
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
      
      // ВАЖНО: Подписка на уведомления после инициализации gamedatas
      this.setupNotifications()
      this.localFounders = this.localFounders || {}
      this._applyLocalFounders()
      this.eventCardsData = gamedatas.eventCards || {} // Данные о картах событий

      // Проверяем баджерсы игроков (проверка без логирования)
      if (gamedatas.players) {
        Object.values(gamedatas.players).forEach((player) => {
          const badgers = player.badgers || 0
          if (badgers !== 5) {
            // Только предупреждение об ошибке, без обычных логов
            console.warn('WARNING: Player ' + player.id + ' has incorrect badgers count! Expected: 5, Got: ' + badgers)
          }
        })
      }

      // Проверяем карты основателей (без логирования)
      if (!gamedatas.isTutorialMode) {
        // Проверяем, есть ли опции для текущего игрока
        if (gamedatas.founderOptions && gamedatas.founderOptions.length > 0) {
          if (gamedatas.founderOptions.length !== 3) {
            console.warn('⚠️ WARNING: Current player should have 3 options, but got ' + gamedatas.founderOptions.length)
          }
        } else {
          console.error('❌ ERROR: Current player has NO founder options! This should not happen in MAIN mode!')
        }

        // Проверяем активного игрока
        if (!gamedatas.activeFounderOptions || gamedatas.activeFounderOptions.length === 0) {
          console.warn('⚠️ Active player has NO founder options')
        }
      }

      // Режим игры (1 - Обучающий, 2 - Основной)
      this.gameMode = gamedatas.gameMode || 1
      this.isTutorialMode = gamedatas.isTutorialMode !== undefined ? gamedatas.isTutorialMode : this.gameMode === 1
      this._renderRoundTrack(this.totalRounds)
      this._renderRoundBanner(gamedatas.round, this.totalRounds, gamedatas.roundName, gamedatas.cubeFace, gamedatas.phaseName)
      this._renderGameModeBanner()

      // Отображаем индикаторы игроков на плашете событий после рендера трека
      setTimeout(() => {
        const roundPanel = document.querySelector('.round-panel__wrapper')
        if (roundPanel) {
          this._renderPlayerIndicators(roundPanel)
        } else {
          console.error('roundPanel not found in setup!')
        }
      }, 200)

      // Отображаем жетоны проектов на планшете проектов (ВАЖНО: до return!)
      setTimeout(() => {
        this._renderProjectTokensOnBoard(gamedatas.projectTokensOnBoard || [])
      }, 200)

      // Обновляем отображение кубика
      this._updateCubeFace(gamedatas.cubeFace)
      const initialEventCards = gamedatas.roundEventCards || []
      this._renderEventCards(initialEventCards)
      this._renderRoundEventCards(initialEventCards)
      this._renderBadgers(gamedatas.badgers || [])
      const initialActiveId = this._getActivePlayerIdFromDatas(gamedatas) || this.player_id
      this._renderPlayerMoney(gamedatas.players, initialActiveId) // Отображаем деньги игрока
      
      // Сохраняем данные карт специалистов для использования в уведомлениях
      if (gamedatas.specialists) {
        // Преобразуем объект в массив, если это объект
        if (Array.isArray(gamedatas.specialists)) {
          console.log('🎴 Setup - Loaded', gamedatas.specialists.length, 'specialist cards data (array)')
        } else if (typeof gamedatas.specialists === 'object') {
          // Если это объект, преобразуем в массив
          gamedatas.specialists = Object.values(gamedatas.specialists)
          console.log('🎴 Setup - Converted specialists object to array, count:', gamedatas.specialists.length)
        } else {
          console.warn('🎴 Setup - WARNING: gamedatas.specialists has unexpected type:', typeof gamedatas.specialists)
        }
      } else {
        console.warn('🎴 Setup - WARNING: gamedatas.specialists is not loaded!')
      }
      
      // Рендерим сохранённые карты сотрудников (если есть)
      if (gamedatas.playerSpecialists && gamedatas.playerSpecialists.length > 0) {
        console.log('🎴 Setup - Found', gamedatas.playerSpecialists.length, 'saved specialist cards')
        this._renderPlayerSpecialists()
      }

      // Проверяем, нужно ли отобразить карты для выбора (в основном режиме, в состоянии FounderSelection)
      const currentState = gamedatas?.gamestate?.name
      const isFounderSelection = currentState === 'FounderSelection'
      const isMainMode = !gamedatas.isTutorialMode

      console.log('🔍 setup - State check:', {
        currentState,
        isFounderSelection,
        isMainMode,
        initialActiveId,
        currentPlayerId: this.player_id,
        isCurrentPlayer: Number(initialActiveId) === Number(this.player_id),
        allPlayersFounderOptions: gamedatas?.allPlayersFounderOptions,
      })

      // Проверяем, есть ли опции карт для текущего игрока (независимо от активного игрока)
      // ВАЖНО: Показываем карты только в состоянии FounderSelection!
      const currentPlayerOptions = gamedatas?.founderOptions || gamedatas?.allPlayersFounderOptions?.[this.player_id] || []

      if (isFounderSelection && isMainMode && currentPlayerOptions.length > 0) {
        const hasSelectedFounder = gamedatas?.players?.[this.player_id]?.founder !== undefined

        console.log('🔍 setup - Current player has options:', {
          currentPlayerId: this.player_id,
          optionsCount: currentPlayerOptions.length,
          hasSelectedFounder,
          isFounderSelection,
        })

        if (!hasSelectedFounder) {
          console.log('✅ setup - Rendering founder selection cards for current player, count:', currentPlayerOptions.length)
          setTimeout(() => {
            this._renderFounderSelectionCards(currentPlayerOptions, this.player_id)
          }, 200)
          this._toggleActivePlayerHand(this.player_id)
          this._updateHandHighlight(this.player_id)
          return // Не вызываем _renderFounderCard, так как уже отобразили карты
        }
      }

      if (isFounderSelection && isMainMode && Number(initialActiveId) === Number(this.player_id)) {
        // Пробуем получить опции из разных источников (важно для 3+ игроков)
        let founderOptions = gamedatas?.founderOptions || gamedatas?.activeFounderOptions || gamedatas?.allPlayersFounderOptions?.[initialActiveId] || []

        const hasSelectedFounder = gamedatas?.players?.[initialActiveId]?.founder !== undefined

        console.log('setup - FounderSelection check:', {
          isFounderSelection,
          isMainMode,
          isCurrentPlayer: Number(initialActiveId) === Number(this.player_id),
          founderOptionsCount: founderOptions.length,
          hasSelectedFounder,
          founderOptions: founderOptions,
          sources: {
            fromGamedatas: gamedatas?.founderOptions?.length || 0,
            fromActive: gamedatas?.activeFounderOptions?.length || 0,
            fromAllPlayers: gamedatas?.allPlayersFounderOptions?.[initialActiveId]?.length || 0,
          },
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
        console.log('🔄 setup: Calling _renderTaskTokens, players:', gamedatas.players)
        if (gamedatas.players) {
          try {
        this._renderTaskTokens(gamedatas.players)
          } catch (error) {
            console.error('❌ Error in _renderTaskTokens:', error)
          }
        } else {
          console.warn('⚠️ _renderTaskTokens: gamedatas.players is not available')
        }
      }, 200)

      // Рендерим input'ы для выбора задач в parts-of-projects__body
      // Вызываем сразу и с задержкой для надежности
      console.log('🔄 setup: Calling _renderTaskInputs immediately...')
      try {
        this._renderTaskInputs()
      } catch (error) {
        console.error('❌ Error in _renderTaskInputs (immediate):', error)
      }
      
      setTimeout(() => {
        try {
          console.log('🔄 setup: Calling _renderTaskInputs (delayed)...')
          this._renderTaskInputs()
        } catch (error) {
          console.error('❌ Error in _renderTaskInputs (delayed):', error)
        }
      }, 500)

      // TODO: Set up your game interface here, according to "gamedatas"
      // (setupNotifications уже вызван в начале setup)
      
          // Рендерим input'ы для выбора задач - вызываем сразу и с задержкой
          console.log('🔄 setup: Calling _renderTaskInputs immediately...')
          try {
            this._renderTaskInputs()
          } catch (error) {
            console.error('❌ Error in _renderTaskInputs (immediate):', error)
          }

          // Обновляем баннер с текущим этапом игры
          console.log('🏷️ Calling _updateStageBanner from setup...')
          this._updateStageBanner()
      
      // Дополнительно: убеждаемся что баннер виден
      const stageBanner = document.getElementById('round-banner')
      if (stageBanner) {
        stageBanner.style.display = 'block'
        stageBanner.style.visibility = 'visible'
        console.log('🏷️ Stage banner element found and made visible')
      } else {
        console.error('🏷️ Stage banner element NOT FOUND!')
      }

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

          // Рендерим input'ы для выбора задач
          setTimeout(() => {
            this._renderTaskInputs()
          }, 400)

          break
        case 'PlayerTurn':
          if (!this.gamedatas.gamestate) {
            this.gamedatas.gamestate = {}
          }
          const activeId = this._extractActivePlayerId(args) ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
          this.gamedatas.gamestate.active_player = activeId

          // ВАЖНО: Очищаем опции выбора карт при входе в PlayerTurn
          // Это состояние наступает после выбора карты, поэтому карты выбора больше не нужны
          this.gamedatas.founderOptions = null
          this.gamedatas.activeFounderOptions = null
          this.gamedatas.allPlayersFounderOptions = null

          // ВАЖНО: Очищаем отделы от карт предыдущего игрока и отрисовываем карты активного игрока
          // Карты всегда берутся из gamedatas.players[activeId]
          this._clearDepartmentsForNewPlayer(activeId)

          this._renderPlayerMoney(this.gamedatas.players, activeId)
          this._renderFounderCard(this.gamedatas.players, activeId)
          this._toggleActivePlayerHand(activeId)
          this._updateHandHighlight(activeId)
          
          // ВАЖНО: Рендерим сохранённые карты сотрудников на руке
          this._renderPlayerSpecialists()
          
          // Рендерим жетоны задач в панели спринта
          this._renderTaskTokens(this.gamedatas.players)
          
          // Рендерим input'ы для выбора задач
          setTimeout(() => {
            this._renderTaskInputs()
          }, 300)
          
          // Обновляем баннер - теперь ЭТАП 2
          this._updateStageBanner()
          break
        case 'FounderSelection':
          // Состояние выбора карты основателя
          const activeIdFounderSelection = this._extractActivePlayerId(args) ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id

          // ВАЖНО: Сбрасываем флаг выбора карты при входе в новое состояние
          // Это позволяет отрисовать карты для нового игрока
          if (Number(activeIdFounderSelection) === Number(this.player_id)) {
            // Если я активный игрок и у меня ещё нет выбранного основателя - сбрасываем флаг
            if (!this.gamedatas?.players?.[this.player_id]?.founder) {
              this.founderSelectedByCurrentPlayer = false
            }
          }

          // ВАЖНО: Очищаем отделы от карт предыдущих игроков при входе в состояние
          // Каждый игрок должен видеть только свою карту основателя
          this._clearDepartmentsForNewPlayer(activeIdFounderSelection)

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

          // ВАЖНО: Проверяем опции для текущего игрока, а не только для активного
          const currentPlayerOptions = args?.args?.founderOptions || this.gamedatas?.founderOptions || this.gamedatas?.allPlayersFounderOptions?.[this.player_id] || []

          console.log('🔍 onEnteringState - Checking options for current player:', {
            currentPlayerId: this.player_id,
            activePlayerId: activeIdFounderSelection,
            currentPlayerOptionsCount: currentPlayerOptions.length,
            hasOptionsInArgs: args?.args?.founderOptions?.length || 0,
            hasOptionsInGamedatas: this.gamedatas?.founderOptions?.length || 0,
            hasOptionsInAllPlayers: this.gamedatas?.allPlayersFounderOptions?.[this.player_id]?.length || 0,
          })

          // Проверяем, является ли активный игрок текущим игроком
          const isCurrentPlayer = Number(activeIdFounderSelection) === Number(this.player_id)

          console.log('FounderSelection - Player check:', {
            activeIdFounderSelection,
            currentPlayerId: this.player_id,
            isCurrentPlayer,
            argsFounderOptions: args?.args?.founderOptions?.length || 0,
            gamedatasFounderOptions: this.gamedatas?.founderOptions?.length || 0,
            gamedatasActiveFounderOptions: this.gamedatas?.activeFounderOptions?.length || 0,
            allPlayersFounderOptions: this.gamedatas?.allPlayersFounderOptions?.[activeIdFounderSelection]?.length || 0,
          })

          // Если это текущий игрок и есть карты для выбора, отображаем их
          // ИЛИ если у текущего игрока есть опции (независимо от того, активный он или нет)
          if (isCurrentPlayer || currentPlayerOptions.length > 0) {
            // Используем опции текущего игрока, если они есть, иначе опции активного
            let founderOptions =
              currentPlayerOptions.length > 0 ? currentPlayerOptions : args?.args?.founderOptions || this.gamedatas?.founderOptions || this.gamedatas?.activeFounderOptions || this.gamedatas?.allPlayersFounderOptions?.[activeIdFounderSelection] || []

            const targetPlayerId = currentPlayerOptions.length > 0 ? this.player_id : activeIdFounderSelection
            const hasSelectedFounder = args?.args?.hasSelectedFounder === true || this.gamedatas?.players?.[targetPlayerId]?.founder !== undefined

            console.log('Current player in FounderSelection:', {
              founderOptionsCount: founderOptions.length,
              hasSelectedFounder,
              founderOptions: founderOptions,
              sources: {
                fromArgs: args?.args?.founderOptions?.length || 0,
                fromGamedatas: this.gamedatas?.founderOptions?.length || 0,
                fromActive: this.gamedatas?.activeFounderOptions?.length || 0,
                fromAllPlayers: this.gamedatas?.allPlayersFounderOptions?.[activeIdFounderSelection]?.length || 0,
              },
            })

            // В Tutorial режиме карта уже выбрана, нужно проверить нужно ли разместить
            const isTutorial = this.gamedatas.isTutorialMode
            const tutorialHasFounder = isTutorial && this.gamedatas?.players?.[targetPlayerId]?.founder
            const actualHasSelectedFounder = hasSelectedFounder || tutorialHasFounder

            // Если карта еще не выбрана и есть опции, показываем карты для выбора
            if (!actualHasSelectedFounder && founderOptions.length > 0) {
              console.log('✅ Rendering selection cards in onEnteringState, count:', founderOptions.length, 'for player:', targetPlayerId)
              setTimeout(() => {
                this._renderFounderSelectionCards(founderOptions, targetPlayerId)
              }, 100)
            } else if (actualHasSelectedFounder) {
              // Если карта уже выбрана (основной режим или Tutorial), показываем обычное отображение
              // В Tutorial режиме карта уже выбрана, нужно показать её на руке если universal
              const founder = this.gamedatas?.players?.[targetPlayerId]?.founder
              if (isTutorial && founder && founder.department === 'universal' && Number(targetPlayerId) === Number(this.player_id)) {
                // В Tutorial режиме показываем универсальную карту на руке
                this._renderUniversalFounderOnHand(founder, targetPlayerId)
                setTimeout(() => {
                  this._setupHandInteractions()
              }, 100)
            } else {
                this._renderFounderCard(this.gamedatas.players, targetPlayerId)
              }
            } else {
              // Нет опций и карта не выбрана
              console.log('Founder already selected or no options, rendering normal card')
              this._renderFounderCard(this.gamedatas.players, targetPlayerId)
            }
          } else {
            // Для других игроков показываем обычное отображение
            console.log('Not current player, rendering normal card')
            this._renderFounderCard(this.gamedatas.players, activeIdFounderSelection)
          }
          
          // Рендерим жетоны задач в панели спринта (на этапе подготовки)
          setTimeout(() => {
            this._renderTaskTokens(this.gamedatas.players)
          }, 200)
          
          // Рендерим input'ы для выбора задач
          setTimeout(() => {
            console.log('🔄 FounderSelection: Calling _renderTaskInputs...')
            this._renderTaskInputs()
          }, 300)

          this._toggleActivePlayerHand(activeIdFounderSelection)
          this._updateHandHighlight(activeIdFounderSelection)
          
          // Обновляем баннер - ЭТАП 1
          this._updateStageBanner()
          break
        case 'SpecialistSelection':
          // Состояние выбора карт сотрудников
          console.log('=== Entering SpecialistSelection state ===')
          
          const specialistArgs = args?.args || {}
          const specialistActivePlayerId = specialistArgs.activePlayerId || this._getActivePlayerIdFromDatas(this.gamedatas) || this.player_id
          
          console.log('🎴 SpecialistSelection:', {
            activePlayerId: specialistActivePlayerId,
            currentPlayerId: this.player_id,
            isMyTurn: Number(specialistActivePlayerId) === Number(this.player_id),
            handCardsLength: specialistArgs.handCards?.length || 0,
          })
          
          // ВАЖНО: Очищаем карты предыдущего игрока и отрисовываем карты активного игрока
          // Карты основателей и сотрудников хранятся в gamedatas.players[playerId]
          this._clearDepartmentsForNewPlayer(specialistActivePlayerId)
          
          // Отрисовываем карту основателя ТОЛЬКО для активного игрока
          if (this.gamedatas.players && this.gamedatas.players[specialistActivePlayerId]?.founder) {
            this._renderFounderCard(this.gamedatas.players, Number(specialistActivePlayerId))
          }
          
          // Обновляем gamedatas
          if (specialistArgs.handCards && specialistArgs.handCards.length > 0) {
            this.gamedatas.specialistHand = specialistArgs.handCards
          }
          if (specialistArgs.selectedCards) {
            this.gamedatas.selectedSpecialists = specialistArgs.selectedCards
          }
          if (specialistArgs.cardsToKeep) {
            this.gamedatas.cardsToKeep = specialistArgs.cardsToKeep
          }
          
          // Если это мой ход, отображаем карты для выбора
          if (Number(specialistActivePlayerId) === Number(this.player_id)) {
            // ВАЖНО: Используем ТОЛЬКО карты из args, не из кэша
            // specialistArgs.handCards должен содержать 7 карт от сервера
            const handCards = specialistArgs.handCards || []
            const selectedCards = specialistArgs.selectedCards || []
            const cardsToKeep = specialistArgs.cardsToKeep || 3
            
            console.log('🎴 My turn! Rendering', handCards.length, 'cards from args')
            console.log('🎴 specialistArgs.handCards length:', specialistArgs.handCards?.length || 0)
            
            // ВАЖНО: Проверяем, что пришло 7 карт
            if (handCards.length !== 7 && handCards.length > 0) {
              console.error('🎴❌ ERROR: Expected 7 cards for selection, but got', handCards.length, 'from server!')
            }
            
            if (handCards.length > 0) {
              this._openSpecialistSelectionModal()
              this._renderSpecialistSelectionCards(handCards, selectedCards, cardsToKeep)
            } else {
              console.error('🎴❌ No hand cards to render! specialistArgs.handCards:', specialistArgs.handCards)
            }
          } else {
            // Если не мой ход, показываем что другой игрок выбирает
            this._renderWaitingForSpecialistSelection(specialistActivePlayerId)
          }
          
          // Обновляем баннер - всё ещё ЭТАП 1
          this._updateStageBanner()
          break
        // TutorialFounderPlacement удалён - используем FounderSelection с той же логикой
        case 'RoundEvent':
          // Состояние события раунда - обновляем данные кубика и карты событий
          // Приоритет: сначала args (данные из getArgs()), потом gamedatas
          console.log('Entering RoundEvent state, args:', args)
          
          // ВАЖНО: Определяем активного игрока и отрисовываем его карты
          const roundEventActiveId = this._extractActivePlayerId(args) ?? this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
          
          // ВАЖНО: Очищаем отделы от карт предыдущего игрока и отрисовываем карты активного игрока
          // Карты всегда берутся из gamedatas.players[roundEventActiveId]
          this._clearDepartmentsForNewPlayer(roundEventActiveId)
          
          // Отрисовываем карту основателя активного игрока
          if (this.gamedatas.players && this.gamedatas.players[roundEventActiveId]?.founder) {
            this._renderFounderCard(this.gamedatas.players, Number(roundEventActiveId))
          }
          
          // ВАЖНО: Рендерим сохранённые карты сотрудников на руке
          this._renderPlayerSpecialists()
          
          // Рендерим жетоны задач в панели спринта
          this._renderTaskTokens(this.gamedatas.players)

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

          const roundNameFromArgs = args?.args?.roundName
          const roundNameFromGamedatas = this.gamedatas?.roundName
          const roundName = roundNameFromArgs || roundNameFromGamedatas || ''

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
          if (roundNameFromArgs) {
            this.gamedatas.roundName = roundNameFromArgs
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

          if (round && roundName) {
            this._renderRoundBanner(round, this.totalRounds, roundName, cubeFace, phaseName)
          } else {
            // Обновляем баннер - ЭТАП 2
            this._updateStageBanner()
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

      // Для FounderSelection проверяем активного игрока из args, а не только текущего
      const isFounderSelection = stateName === 'FounderSelection'
      const shouldProcessActions = this.isCurrentPlayerActive() || isFounderSelection
      
      if (shouldProcessActions) {
        switch (stateName) {
          case 'PlayerTurn':
            if (!this.isCurrentPlayerActive()) {
              break // PlayerTurn только для активного игрока
            }
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
            // ВАЖНО: Этот блок выполняется для всех игроков, но кнопка показывается только активному
            console.log('FounderSelection onUpdateActionButtons, args:', args)
            // args может быть null или иметь структуру { args: { ... } }
            const founderSelectionActionArgs = args?.args || args || {}
            console.log('FounderSelection onUpdateActionButtons - Extracted args:', founderSelectionActionArgs)
            const hasSelectedFounder = founderSelectionActionArgs?.hasSelectedFounder === true
            const mustPlaceFounderFounderSelection = founderSelectionActionArgs?.mustPlaceFounder === true
            const founderOptionsFromArgs = founderSelectionActionArgs?.founderOptions || []
            const activePlayerIdFromArgs = founderSelectionActionArgs?.activePlayerId || this._getActivePlayerIdFromDatas(this.gamedatas) || this.player_id
            
            // В Tutorial режиме проверяем также через gamedatas для АКТИВНОГО игрока
            const isTutorial = this.gamedatas.isTutorialMode
            const activePlayerId = Number(activePlayerIdFromArgs)
            const tutorialHasFounder = isTutorial && this.gamedatas?.players?.[activePlayerId]?.founder
            const actualHasSelectedFounder = hasSelectedFounder || tutorialHasFounder
            
            // Проверяем, является ли текущий игрок активным
            const isActivePlayer = Number(activePlayerId) === Number(this.player_id)

            console.log('FounderSelection onUpdateActionButtons:', {
              activePlayerId,
              hasSelectedFounder,
              tutorialHasFounder,
              actualHasSelectedFounder,
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

            // Проверяем опции для текущего игрока
            const currentPlayerOptions = founderOptionsFromArgs.length > 0 ? founderOptionsFromArgs : this.gamedatas?.founderOptions || this.gamedatas?.allPlayersFounderOptions?.[this.player_id] || []

            const currentPlayerHasSelected = this.gamedatas?.players?.[this.player_id]?.founder !== undefined

            console.log('🔍 onUpdateActionButtons - Checking options:', {
              currentPlayerId: this.player_id,
              currentPlayerOptionsCount: currentPlayerOptions.length,
              currentPlayerHasSelected,
              hasSelectedFounder,
              founderOptionsFromArgsCount: founderOptionsFromArgs.length,
            })

            // Если карта еще не выбрана и есть опции, отображаем карты для текущего игрока
            if (!currentPlayerHasSelected && currentPlayerOptions.length > 0) {
              console.log('✅ Rendering selection cards in onUpdateActionButtons for current player:', this.player_id, 'count:', currentPlayerOptions.length)
              setTimeout(() => {
                this._renderFounderSelectionCards(currentPlayerOptions, this.player_id)
              }, 100)
            } else if (!hasSelectedFounder && founderOptionsFromArgs.length > 0) {
              // Fallback: если нет опций для текущего игрока, но есть для активного
              const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas) || this.player_id
              console.log('✅ Rendering selection cards in onUpdateActionButtons for active player:', activePlayerId, 'count:', founderOptionsFromArgs.length)
              setTimeout(() => {
                this._renderFounderSelectionCards(founderOptionsFromArgs, activePlayerId)
              }, 100)
            }

            // В Tutorial режиме карта уже выбрана, нужно проверить нужно ли разместить
            // Кнопка показывается только активному игроку
            const shouldShowFinishButton = actualHasSelectedFounder && isActivePlayer
            
            if (shouldShowFinishButton) {
              // Игрок уже выбрал карту - показываем кнопку "Завершить ход"
              // Кнопка блокируется, если карта не размещена в отдел
              // Переход к следующему этапу/игроку происходит только по нажатию кнопки
              console.log('✅ Adding finish turn button for active player:', activePlayerId)
              this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), {
                primary: true,
                disabled: mustPlaceFounderFounderSelection, // Блокируется, если карта не размещена
                tooltip: mustPlaceFounderFounderSelection ? _('Вы должны разместить карту основателя в один из отделов перед завершением хода') : undefined,
                id: 'finish-turn-button',
              })
            } else {
              console.log('❌ Not showing finish button:', {
                actualHasSelectedFounder,
                isActivePlayer,
                activePlayerId,
                currentPlayerId: this.player_id,
              })
            }
            // Карты выбираются кликом, размещение карты тоже через клик
            break

          // TutorialFounderPlacement удалён - используем FounderSelection

          case 'SpecialistSelection':
            // Состояние выбора карт сотрудников
            console.log('🎴 SpecialistSelection onUpdateActionButtons, RAW args:', args)
            console.log('🎴 SpecialistSelection args?.args:', args?.args)
            const specialistActionArgs = args?.args || args || {}
            console.log('🎴 SpecialistSelection EXTRACTED specialistActionArgs:', specialistActionArgs)
            console.log('🎴 SpecialistSelection handCards:', specialistActionArgs.handCards)
            
            const selectedSpecialistsCount = specialistActionArgs.selectedCards?.length || this.gamedatas.selectedSpecialists?.length || 0
            const specialistCardsToKeep = specialistActionArgs.cardsToKeep || 3
            
            console.log('🎴 SpecialistSelection buttons:', {
              selectedCount: selectedSpecialistsCount,
              cardsToKeep: specialistCardsToKeep,
              canConfirm: selectedSpecialistsCount === specialistCardsToKeep,
              handCardsCount: specialistActionArgs.handCards?.length || 0,
            })
            
            // Обновляем UI карт если есть новые данные
            if (specialistActionArgs.handCards && specialistActionArgs.handCards.length > 0) {
              this.gamedatas.specialistHand = specialistActionArgs.handCards
              this.gamedatas.selectedSpecialists = specialistActionArgs.selectedCards || []
              this.gamedatas.cardsToKeep = specialistCardsToKeep
              
              // Открываем модальное окно и рендерим карты
              this._openSpecialistSelectionModal()
              this._renderSpecialistSelectionCards(
                specialistActionArgs.handCards,
                specialistActionArgs.selectedCards || [],
                specialistCardsToKeep
              )
            }
            
            // Кнопка "Применить" теперь в модальном окне, не в статус-баре
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

      // Явная подписка на уведомления
      dojo.subscribe('badgersChanged', this, 'notif_badgersChanged')
      dojo.subscribe('roundStart', this, 'notif_roundStart')
      dojo.subscribe('founderSelected', this, 'notif_founderSelected')
      dojo.subscribe('founderPlaced', this, 'notif_founderPlaced')
      dojo.subscribe('founderCardsDiscarded', this, 'notif_founderCardsDiscarded')
      
      // Уведомления для выбора сотрудников
      dojo.subscribe('specialistToggled', this, 'notif_specialistToggled')
      dojo.subscribe('specialistsConfirmed', this, 'notif_specialistsConfirmed')
      dojo.subscribe('specialistsDealtToHand', this, 'notif_specialistsDealtToHand')
      dojo.subscribe('specialistsDealt', this, 'notif_specialistsDealt')
      dojo.subscribe('founderEffectsApplied', this, 'notif_founderEffectsApplied')
      
      console.log('✅ Notifications subscribed: badgersChanged, roundStart, founderSelected, founderPlaced, founderCardsDiscarded, specialistToggled, specialistsConfirmed, specialistsDealtToHand, specialistsDealt, founderEffectsApplied')
    },

    // TODO: from this point and below, you can write your game notifications handling methods

    // Round updates
    notif_roundStart: async function (notif) {
      console.log('notif_roundStart called with notif:', notif)
      
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      console.log('cubeFace from notification:', args.cubeFace, 'type:', typeof args.cubeFace)

      // Обновляем данные в gamedatas
      if (args.cubeFace !== undefined && args.cubeFace !== null) {
        this.gamedatas.cubeFace = args.cubeFace
      }

      // Обновляем данные о раунде
      if (args.round !== undefined) {
        this.gamedatas.round = args.round
      }
      if (args.roundName !== undefined) {
        this.gamedatas.roundName = args.roundName
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

      this._renderRoundBanner(args.round, this.totalRounds, args.roundName, args.cubeFace, args.phaseName)
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
        
        // ВАЖНО: Очищаем отделы от карт предыдущего игрока и отрисовываем карты активного игрока
        // Карты всегда берутся из gamedatas.players[activeId]
        this._clearDepartmentsForNewPlayer(activeId)
        
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
      console.log('=== 🎮 notif_gameStart CALLED - ПЕРЕХОД К ЭТАПУ 2! ===')
      console.log('notif_gameStart called with args:', args)

      // Отображаем начало ЭТАПА 2
      const banner = document.getElementById('round-banner')
      if (banner) {
        const stageName = args.stageName || _('Начало игры')
        const content = banner.querySelector('.round-banner__content')
        const bannerText = _('🎮 ЭТАП 2: ${stageName}').replace('${stageName}', stageName)
        
        if (content) {
          content.textContent = bannerText
        } else {
          banner.textContent = bannerText
        }
        banner.className = 'round-banner round-banner--game-start'
        banner.style.backgroundColor = '#2196F3'
        banner.style.color = '#FFFFFF'
        banner.style.fontSize = '20px'
        banner.style.fontWeight = 'bold'
        banner.style.padding = '10px 0px'
        banner.style.textAlign = 'center'
        
        console.log('🎮 Banner updated to ЭТАП 2:', bannerText)
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

    notif_founderSelected: async function (notif) {
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      
      const playerId = Number(args.player_id || 0)
      const founder = args.founder || null
      const department = String(args.department || founder?.department || 'universal').trim().toLowerCase()
      const isUniversal = department === 'universal'

      if (playerId > 0 && founder) {
        // Обновляем данные в founders
        if (!this.gamedatas.founders) {
          this.gamedatas.founders = {}
        }
        const founderData = { ...founder }
        founderData.department = department
        this.gamedatas.founders[playerId] = founderData

        // Обновляем данные в players
        if (!this.gamedatas.players[playerId]) {
          this.gamedatas.players[playerId] = {}
        }
        this.gamedatas.players[playerId].founder = founderData

        // Применяем локальные изменения
        this._applyLocalFounders()

        // ВАЖНО: Полностью очищаем опции выбора из всех источников
        this.gamedatas.founderOptions = null
        this.gamedatas.activeFounderOptions = null
        if (this.gamedatas.allPlayersFounderOptions) {
          delete this.gamedatas.allPlayersFounderOptions[playerId]
        }

        const handContainer = document.getElementById('active-player-hand-cards')

        // ВАЖНО: Принудительно удаляем все карты выбора из DOM
        if (handContainer) {
          const selectableCards = handContainer.querySelectorAll('.founder-card--selectable')
          selectableCards.forEach(card => {
            card.remove()
          })
          handContainer.classList.remove('active-player-hand__center--selecting')
        }

        // Если карта универсальная, показываем её на руке для текущего игрока
        if (isUniversal && Number(playerId) === Number(this.player_id)) {
          
          // Очищаем контейнер, но сохраняем карты специалистов, если они есть
          if (handContainer) {
            // Удаляем только карты выбора основателя, но сохраняем карты специалистов
            const selectableCards = handContainer.querySelectorAll('.founder-card--selectable')
            selectableCards.forEach(card => card.remove())
            
            // Если нет карт специалистов, очищаем контейнер полностью
            const specialistCards = handContainer.querySelectorAll('.specialist-card')
            if (specialistCards.length === 0) {
            handContainer.innerHTML = ''
            }
          }

          // Рендерим одну карту на руке (универсальную) напрямую
          this._renderUniversalFounderOnHand(founder, playerId)

          // Переустанавливаем обработчики для возможности размещения
          setTimeout(() => {
            this._setupHandInteractions()
          }, 100)
          
          // ВАЖНО: Добавляем кнопку "Завершить ход" (заблокированную, т.к. нужно разместить карту)
          this._addFinishTurnButton(true)

        } else if (isUniversal && Number(playerId) !== Number(this.player_id)) {
          // Для других игроков показываем рубашку на руке
          
          if (handContainer) {
            handContainer.innerHTML = ''
          }

          // Показываем рубашку для других игроков
          const backImageUrl = `${g_gamethemeurl}img/back-cards.png`
          if (handContainer) {
            handContainer.innerHTML = `
              <div class="founder-card founder-card--back" data-player-id="${playerId}" data-department="universal">
                <img src="${backImageUrl}" alt="${_('Рубашка карты')}" class="founder-card__image" />
              </div>
            `
          }

        } else {
          // Не-универсальная карта - размещена в отдел автоматически
          // ВАЖНО: Рендерим карту в отделе ТОЛЬКО для текущего игрока
          // Для других игроков не рендерим - иначе setTimeout срабатывает после _clearDepartmentsForNewPlayer
          if (Number(playerId) === Number(this.player_id)) {
          // Очищаем руку полностью
          if (handContainer) {
            handContainer.innerHTML = ''
          }

          // Отрисовываем карту в отделе (с небольшой задержкой чтобы DOM обновился)
          setTimeout(() => {
            this._renderFounderCardInDepartment(founder, playerId, department)
          }, 100)
          
            // Добавляем кнопку "Завершить ход" (заблокированную, т.к. нужно применить эффекты)
            // Кнопка разблокируется через уведомление founderEffectsApplied
            this._addFinishTurnButton(true)
          } else {
            console.log('🎉 Skipping render for other player:', playerId)
            // Очищаем руку, т.к. другой игрок сделал выбор
            if (handContainer) {
              handContainer.innerHTML = ''
            }
          }
        }
      }
    },

    // Уведомление об изменении баджерсов (эффект карты основателя)
    notif_badgersChanged: async function (notif) {
      console.log('💰 notif_badgersChanged called:', notif)
      
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      console.log('💰 Extracted args:', args)
      
      const playerId = Number(args.player_id || 0)
      const amount = Number(args.amount || 0)
      const founderName = args.founder_name || 'Основатель'
      const newValue = Number(args.newValue || 0)
      
      console.log('💰 Badgers changed:', { playerId, newValue, amount, founderName })
      
      // Обновляем данные в gamedatas
      if (playerId > 0 && this.gamedatas.players[playerId]) {
        this.gamedatas.players[playerId].badgers = newValue
      }
      
      // Обновляем банк баджерсов, если данные пришли с сервера
      if (args.badgersSupply && Array.isArray(args.badgersSupply)) {
        console.log('💰 Updating badgers supply, count:', args.badgersSupply.length)
        this.gamedatas.badgers = args.badgersSupply
        this._renderBadgers(args.badgersSupply)
      }
      
      // Обновляем отображение денег игрока (передаём оба аргумента!)
      this._renderPlayerMoney(this.gamedatas.players, playerId)
      
      // Визуальная анимация изменения
      if (amount !== 0) {
        const actionText = amount > 0 ? '+' : ''
        this.showMessage(`${founderName}: ${actionText}${amount}Б`, 'info')
      }
    },

    // Очищает отделы от карт других игроков при переходе хода
    // ВАЖНО: Удаляет ВСЕ карты, кроме карт активного игрока
    _clearDepartmentsForNewPlayer: function (activePlayerId) {
      console.log('🧹 _clearDepartmentsForNewPlayer called for player:', activePlayerId)
      
      const departments = ['sales-department', 'back-office', 'technical-department']
      
      departments.forEach(dept => {
        const container = document.querySelector(`.${dept}__body`)
        if (container) {
          // Удаляем ВСЕ карты из отдела (они будут отрисованы заново для активного игрока)
          container.innerHTML = ''
        }
      })
      
      // Также очищаем руку от карт других игроков (но не трогаем карты специалистов в состоянии SpecialistSelection)
      const handContainer = document.getElementById('active-player-hand-cards')
      if (handContainer) {
        const currentState = this.gamedatas?.gamestate?.name
        const isSpecialistSelection = currentState === 'SpecialistSelection'
        
        // В состоянии SpecialistSelection не трогаем контейнер руки (там карты специалистов)
        if (!isSpecialistSelection) {
          // Удаляем только карты основателей, не трогая карты специалистов
          const founderCards = handContainer.querySelectorAll('.founder-card')
          founderCards.forEach(card => {
            const cardPlayerId = card.getAttribute('data-player-id')
            if (cardPlayerId && Number(cardPlayerId) !== Number(activePlayerId)) {
              console.log('🧹 Removing hand card for other player:', cardPlayerId)
              card.remove()
            }
          })
        }
      }
    },

    // Прямая отрисовка карты в конкретном отделе
    _renderFounderCardInDepartment: function (founder, playerId, department) {
      const containers = {
        'sales-department': document.querySelector('.sales-department__body'),
        'back-office': document.querySelector('.back-office__body'),
        'technical-department': document.querySelector('.technical-department__body'),
      }

      const container = containers[department]
      if (!container) {
        console.error('_renderFounderCardInDepartment - Container NOT FOUND for department:', department)
        return
      }

      // В Tutorial режиме очищаем весь контейнер перед размещением карты
      // В основном режиме можно оставить другие карты
      const isTutorial = this.isTutorialMode
      if (isTutorial) {
        container.innerHTML = ''
      } else {
        // Удаляем только карту этого игрока, если она уже есть
        const existingCard = container.querySelector(`[data-player-id="${playerId}"]`)
        if (existingCard) {
          existingCard.remove()
        }
      }

      const imageUrl = founder.img ? (founder.img.startsWith('http') ? founder.img : `${g_gamethemeurl}${founder.img}`) : ''
      const name = founder.name || ''

      const cardMarkup = `
        <div class="founder-card" data-player-id="${playerId}" data-department="${department}">
          ${imageUrl ? `<img src="${imageUrl}" alt="${name}" class="founder-card__image" />` : ''}
        </div>
      `
      container.innerHTML = cardMarkup
    },

    // Вспомогательная функция для отрисовки универсальной карты на руке
    _renderUniversalFounderOnHand: function (founder, playerId) {
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) return

      const imageUrl = founder.img ? (founder.img.startsWith('http') ? founder.img : `${g_gamethemeurl}${founder.img}`) : ''
      const name = founder.name || ''

      // Удаляем только старую карту основателя, если она есть, но сохраняем карты специалистов
      const existingFounderCard = handContainer.querySelector('.founder-card--universal-clickable')
      if (existingFounderCard) {
        existingFounderCard.remove()
      }

      // Создаем карту с классом для клика (обработчик добавляется в _setupHandInteractions)
      const cardDiv = document.createElement('div')
      cardDiv.className = 'founder-card founder-card--universal-clickable'
      cardDiv.setAttribute('data-player-id', playerId)
      cardDiv.setAttribute('data-department', 'universal')
      cardDiv.style.cursor = 'pointer'
      cardDiv.title = _('Кликните, чтобы выбрать отдел для размещения')
      
      if (imageUrl) {
        const img = document.createElement('img')
        img.src = imageUrl
        img.alt = name
        img.className = 'founder-card__image'
        cardDiv.appendChild(img)
      }
      
      // Добавляем карту основателя в начало контейнера (перед картами специалистов)
      handContainer.insertBefore(cardDiv, handContainer.firstChild)
    },

    notif_founderCardsDiscarded: function (notif) {
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      // Карты отправлены в отбой, очищаем руку от карт выбора
      const playerId = Number(args.player_id || 0)
      console.log('notif_founderCardsDiscarded called:', { playerId, discardedCards: args.discarded_cards })

      // Очищаем руку от карт выбора для всех игроков (чтобы все видели, что карты ушли)
      const handContainer = document.getElementById('active-player-hand-cards')
      if (handContainer) {
        handContainer.innerHTML = ''
        handContainer.classList.remove('active-player-hand__center--selecting')
      }
    },

    notif_founderPlaced: async function (notif) {
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
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

        // Применяем локальные изменения (как в notif_roundStart)
        this._applyLocalFounders()

        // ВАЖНО: Кнопка "Завершить ход" разблокируется только после применения всех эффектов
        // Это происходит через уведомление founderEffectsApplied

        // Если карта была размещена из руки (была универсальной), удаляем её из руки
        // После размещения карта должна быть в отделе, а не на руке
        const handContainer = document.getElementById('active-player-hand-cards')
        if (handContainer && Number(playerId) === Number(this.player_id)) {
          // Удаляем только карту основателя из руки, но сохраняем карты специалистов
          // Ищем и удаляем только карту основателя (универсальную)
          const founderCardElement = handContainer.querySelector('.founder-card--universal-clickable')
          if (founderCardElement) {
            founderCardElement.remove()
          }
          
          // ВАЖНО: Карты специалистов от эффекта 'card' рендерятся через notif_specialistsDealtToHand
          // Не нужно дублировать рендеринг здесь, чтобы избежать двойного отображения
          
          // Сбрасываем выделение
          this._setDepartmentHighlight(false)
          this._setHandHighlight(false)
        }
        
        // Обновляем отображение карты основателя
        // В Tutorial режиме отрисовываем только карту текущего игрока, чтобы не показывать карты других игроков
        const isTutorial = this.gamedatas.isTutorialMode
        if (isTutorial) {
          // В Tutorial режиме отрисовываем только карту игрока, который разместил карту
          this._renderFounderCard(this.gamedatas.players, playerId)
        } else {
          // В основном режиме отрисовываем только карту игрока, который разместил карту
          // Не отрисовываем карты всех игроков, чтобы не показывать карты других игроков
          this._renderFounderCard(this.gamedatas.players, playerId)
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

          // ВАЖНО: Кнопка "Завершить ход" разблокируется только после применения всех эффектов
          // Это происходит через уведомление founderEffectsApplied
        }
        // Если карта была размещена другим игроком, данные обновлены, но отображение не меняется
        // так как на экране показывается только карта активного игрока
      }
    },

    // ========================================
    // Уведомления для выбора сотрудников
    // ========================================

    notif_specialistToggled: async function (notif) {
      const args = notif.args || notif
      
      const cardId = Number(args.card_id || 0)
      const action = args.action // 'selected' или 'deselected'
      const selectedCount = Number(args.selected_count || 0)
      const cardsToKeep = Number(args.cards_to_keep || 3)
      
      // Обновляем gamedatas
      if (!this.gamedatas.selectedSpecialists) {
        this.gamedatas.selectedSpecialists = []
      }
      
      if (action === 'selected') {
        if (!this.gamedatas.selectedSpecialists.includes(cardId)) {
          this.gamedatas.selectedSpecialists.push(cardId)
        }
      } else {
        const index = this.gamedatas.selectedSpecialists.indexOf(cardId)
        if (index > -1) {
          this.gamedatas.selectedSpecialists.splice(index, 1)
        }
      }
      
      // Обновляем визуальное состояние карты в модальном окне
      this._updateSpecialistCardSelection(cardId, action === 'selected')
      
      // Обновляем счётчик и кнопку в модальном окне
      this._updateConfirmSpecialistsButton(selectedCount, cardsToKeep)
      
      // Обновляем визуальное состояние карты
      this._updateSpecialistCardSelection(cardId, action === 'selected')
      
      // Обновляем кнопку "Применить"
      this._updateConfirmSpecialistsButton(selectedCount, cardsToKeep)
    },

    notif_specialistsConfirmed: async function (notif) {
      const args = notif.args || notif
      
      const playerId = Number(args.player_id || 0)
      const keptCount = Number(args.kept_count || 0)
      
      // Закрываем модальное окно
      this._closeSpecialistSelectionModal()
      
      // Если это текущий игрок - сохраняем выбранные карты
      if (Number(playerId) === Number(this.player_id)) {
        // ВАЖНО: Получаем существующие карты от эффекта (они уже в playerSpecialists)
        const existingCards = this.gamedatas.playerSpecialists || []
        const existingIds = new Set(existingCards.map(card => card.id))
        
        // Получаем выбранные карты из 7 карт для выбора
        const selectedIds = this.gamedatas.selectedSpecialists || []
        const handCards = this.gamedatas.specialistHand || []
        
        // Фильтруем карты - оставляем только выбранные из 7 карт
        const keptCards = handCards.filter(card => selectedIds.includes(card.id))
        
        // ВАЖНО: Добавляем выбранные карты к существующим (от эффекта), а не перезаписываем!
        const newCards = keptCards.filter(card => !existingIds.has(card.id))
        this.gamedatas.playerSpecialists = [...existingCards, ...newCards]
        
        console.log('🎴 notif_specialistsConfirmed - Existing cards from effect:', existingCards.length)
        console.log('🎴 notif_specialistsConfirmed - Selected cards from 7:', keptCards.length)
        console.log('🎴 notif_specialistsConfirmed - New cards (no duplicates):', newCards.length)
        console.log('🎴 notif_specialistsConfirmed - Total cards now:', this.gamedatas.playerSpecialists.length)
        
        // Очищаем временные данные
        delete this.gamedatas.specialistHand
        delete this.gamedatas.selectedSpecialists
        
        // Рендерим все карты на руке (от эффекта + выбранные)
        this._renderPlayerSpecialists()
      }
    },

    notif_specialistsDealtToHand: async function (notif) {
      console.log('🎴 notif_specialistsDealtToHand received:', notif)
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const cardIds = args.cardIds || []
      
      console.log('🎴 Processing notification:', {
        playerId,
        currentPlayerId: this.player_id,
        cardIds,
        allSpecialistsType: typeof this.gamedatas?.specialists,
        allSpecialistsIsArray: Array.isArray(this.gamedatas?.specialists)
      })
      
      // Если это текущий игрок - обновляем данные и рендерим карты
      if (Number(playerId) === Number(this.player_id)) {
        // Получаем данные карт из SpecialistsData
        let allSpecialists = this.gamedatas.specialists || []
        
        // Преобразуем объект в массив, если это объект
        if (!Array.isArray(allSpecialists) && typeof allSpecialists === 'object') {
          allSpecialists = Object.values(allSpecialists)
          this.gamedatas.specialists = allSpecialists
          console.log('🎴 Converted specialists object to array, count:', allSpecialists.length)
        }
        
        if (!Array.isArray(allSpecialists) || allSpecialists.length === 0) {
          console.error('🎴 ERROR: gamedatas.specialists is not an array or is empty!', {
            type: typeof allSpecialists,
            isArray: Array.isArray(allSpecialists),
            length: allSpecialists?.length
          })
          return
        }
        
        const dealtCards = cardIds.map(cardId => {
          const card = allSpecialists.find(card => Number(card.id) === Number(cardId))
          if (!card) {
            console.warn('🎴 Card not found in specialists data:', cardId, 'Available IDs:', allSpecialists.slice(0, 10).map(c => c.id))
          }
          return card || null
        }).filter(card => card !== null)
        
        console.log('🎴 Dealt cards found:', dealtCards.length, 'out of', cardIds.length)
        
        if (dealtCards.length === 0) {
          console.error('🎴 ERROR: No cards found for IDs:', cardIds)
          return
        }
        
        // ВАЖНО: Эффект 'card' сразу закрепляет карты за игроком (player_specialists_)
        // Эти карты НЕ попадают в specialist_hand_ и НЕ участвуют в выборе из 7 карт
        // Они сразу добавляются в playerSpecialists для отображения на руке
        
        console.log('🎴 notif_specialistsDealtToHand - Cards from founder effect are LOCKED to player (player_specialists_)')
        console.log('🎴 notif_specialistsDealtToHand - These cards do NOT participate in selection from 7 cards')
        
        // Добавляем карты в playerSpecialists (они уже закреплены на сервере)
        const currentSpecialists = this.gamedatas.playerSpecialists || []
        const existingIds = new Set(currentSpecialists.map(card => card.id))
        const newCards = dealtCards.filter(card => !existingIds.has(card.id))
        
        // Добавляем только новые карты (без дубликатов)
        this.gamedatas.playerSpecialists = [...currentSpecialists, ...newCards]
        
        console.log('🎴 notif_specialistsDealtToHand - Dealt cards:', dealtCards.length, 'New cards (no duplicates):', newCards.length)
        console.log('🎴 notif_specialistsDealtToHand - Total player specialists now:', this.gamedatas.playerSpecialists.length)
        
        // Рендерим закреплённые карты на руке
        this._renderPlayerSpecialists()
        
        // Показываем сообщение
        const founderName = args.founder_name || 'Основатель'
        const amount = args.amount || 0
        this.showMessage(`${founderName}: +${amount} карт специалистов`, 'info')
      } else {
        console.log('🎴 Notification is for another player:', playerId, 'current:', this.player_id)
      }
    },

    notif_founderEffectsApplied: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      
      console.log('✅ notif_founderEffectsApplied received for player:', playerId)
      
      // Если это текущий игрок, разблокируем кнопку "Завершить ход"
      if (Number(playerId) === Number(this.player_id)) {
        const finishButton = document.getElementById('finish-turn-button')
        if (finishButton) {
          finishButton.disabled = false
          finishButton.removeAttribute('title') // Убираем tooltip
          console.log('✅ Finish turn button unlocked after all founder effects applied')
        } else {
          // Если кнопки нет, добавляем её (активную)
          this._addFinishTurnButton(false)
        }
      }
    },

    notif_specialistsDealt: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const cardIds = args.cardIds || []
      
      // Если это текущий игрок - обновляем данные и рендерим карты
      if (Number(playerId) === Number(this.player_id)) {
        // Получаем данные карт из SpecialistsData
        let allSpecialists = this.gamedatas.specialists || []
        
        // Преобразуем объект в массив, если это объект
        if (!Array.isArray(allSpecialists) && typeof allSpecialists === 'object') {
          allSpecialists = Object.values(allSpecialists)
          this.gamedatas.specialists = allSpecialists
        }
        
        if (!Array.isArray(allSpecialists)) {
          console.error('🎴 ERROR: gamedatas.specialists is not an array in notif_specialistsDealt!')
          return
        }
        
        const dealtCards = cardIds.map(cardId => {
          return allSpecialists.find(card => Number(card.id) === Number(cardId)) || null
        }).filter(card => card !== null)
        
        // ВАЖНО: Эффект 'task' добавляет карты в player_specialists_ (закрепленные)
        // Проверяем на дубликаты перед добавлением
        const currentSpecialists = this.gamedatas.playerSpecialists || []
        const existingIds = new Set(currentSpecialists.map(card => card.id))
        const newCards = dealtCards.filter(card => !existingIds.has(card.id))
        
        // Добавляем только новые карты (без дубликатов)
        this.gamedatas.playerSpecialists = [...currentSpecialists, ...newCards]
        
        console.log('🎴 notif_specialistsDealt - Dealt cards:', dealtCards.length, 'New cards (no duplicates):', newCards.length)
        console.log('🎴 notif_specialistsDealt - Total player specialists now:', this.gamedatas.playerSpecialists.length)
        
        // Рендерим карты в блоке руки
        this._renderPlayerSpecialists()
        
        // Показываем сообщение
        const founderName = args.founder_name || 'Основатель'
        const amount = args.amount || 0
        this.showMessage(`${founderName}: +${amount} карт специалистов`, 'info')
      }
    },

    // ========================================
    // Методы рендеринга карт сотрудников
    // ========================================

    _openSpecialistSelectionModal: function () {
      const modal = document.getElementById('specialist-selection-modal')
      if (modal) {
        modal.classList.add('active')
        
        // Закрытие при клике вне модального окна
        modal.addEventListener('click', (e) => {
          if (e.target === modal) {
            // Не закрываем при клике вне - пользователь должен выбрать карты
          }
        })
      }
    },

    _closeSpecialistSelectionModal: function () {
      const modal = document.getElementById('specialist-selection-modal')
      if (modal) {
        modal.classList.remove('active')
      }
    },

    _renderSpecialistSelectionCards: function (handCards, selectedCards, cardsToKeep) {
      console.log('🎴 _renderSpecialistSelectionCards called:', {
        handCards: handCards?.length || 0,
        selectedCards: selectedCards?.length || 0,
        cardsToKeep,
        handCardsArray: handCards,
      })
      
      // ВАЖНО: Логируем ID всех карт для отладки
      if (handCards && handCards.length > 0) {
        const cardIds = handCards.map(card => ({
          id: card.id,
          idType: typeof card.id,
          name: card.name || 'Unknown'
        }))
        console.log('🎴 Card IDs from server:', cardIds)
        console.log('🎴 Card IDs (numbers only):', handCards.map(c => Number(c.id)))
      }
      
      // ВАЖНО: Проверяем, что пришло 7 карт, а не 3
      if (handCards && handCards.length !== 7 && handCards.length > 0) {
        console.warn('⚠️ WARNING: Expected 7 cards for selection, but got', handCards.length)
      }
      
      const modalBody = document.getElementById('specialist-selection-modal-body')
      const modalTitle = document.getElementById('specialist-selection-modal-title')
      const modalSubtitle = document.getElementById('specialist-selection-modal-subtitle')
      const confirmBtn = document.getElementById('specialist-selection-modal-confirm-btn')
      
      if (!modalBody || !modalTitle || !modalSubtitle || !confirmBtn) {
        console.error('Modal elements not found!')
        return
      }
      
      // Обновляем заголовок
      modalTitle.textContent = _('Выберите') + ' ' + cardsToKeep + ' ' + _('карты сотрудников')
      modalSubtitle.textContent = _('Выбрано') + ': ' + selectedCards.length + '/' + cardsToKeep
      
      // Очищаем контейнер карт
      modalBody.innerHTML = ''
      
      // Рендерим каждую карту
      handCards.forEach((card) => {
        const isSelected = selectedCards.includes(card.id)
        const cardDiv = this._createSpecialistCard(card, isSelected)
        modalBody.appendChild(cardDiv)
      })
      
      // Обновляем кнопку подтверждения
      this._updateConfirmSpecialistsButton(selectedCards.length, cardsToKeep)
    },

    _createSpecialistCard: function (card, isSelected) {
      const cardDiv = document.createElement('div')
      cardDiv.className = `specialist-card ${isSelected ? 'specialist-card--selected' : ''}`
      
      // Устанавливаем data-атрибут (dataset всегда возвращает строку, но это нормально)
      cardDiv.dataset.cardId = card.id
      cardDiv.dataset.department = card.department || 'unknown'
      
      const imageUrl = card.img ? (card.img.startsWith('http') ? card.img : `${g_gamethemeurl}${card.img}`) : ''
      
      // Убраны подписи (overlay), только изображение и галочка
      cardDiv.innerHTML = `
        <div class="specialist-card__inner">
          ${imageUrl ? `<img src="${imageUrl}" alt="${card.name || ''}" class="specialist-card__image" />` : ''}
          <div class="specialist-card__check">✓</div>
        </div>
      `
      
      // Обработчик клика - просто приводим тип при получении из dataset
      cardDiv.addEventListener('click', (e) => {
        e.stopPropagation()
        // ВАЖНО: dataset всегда возвращает строку, поэтому приводим к числу
        const cardId = Number(cardDiv.dataset.cardId)
        this._toggleSpecialistCard(cardId)
      })
      
      return cardDiv
    },

    _toggleSpecialistCard: function (cardId) {
      // ВАЖНО: Убеждаемся, что cardId - это число
      const numericCardId = Number(cardId)
      
      console.log('🎴 _toggleSpecialistCard called:', {
        cardId: cardId,
        numericCardId: numericCardId,
        type: typeof numericCardId,
        handCards: this.gamedatas.specialistHand?.map(c => ({ id: c.id, type: typeof c.id })) || []
      })
      
      // Отправляем действие на сервер
      this.bgaPerformAction('actToggleSpecialist', { cardId: numericCardId })
        .catch((error) => {
          console.error('❌ Error toggling specialist:', error)
        })
    },

    _updateSpecialistCardSelection: function (cardId, isSelected) {
      const cardDiv = document.querySelector(`.specialist-card[data-card-id="${cardId}"]`)
      if (cardDiv) {
        if (isSelected) {
          cardDiv.classList.add('specialist-card--selected')
        } else {
          cardDiv.classList.remove('specialist-card--selected')
        }
      }
      
      // Обновляем счётчик в модальном окне
      const modalSubtitle = document.getElementById('specialist-selection-modal-subtitle')
      if (modalSubtitle && this.gamedatas.selectedSpecialists !== undefined) {
        const cardsToKeep = this.gamedatas.cardsToKeep || 3
        modalSubtitle.textContent = _('Выбрано') + ': ' + this.gamedatas.selectedSpecialists.length + '/' + cardsToKeep
      }
    },

    _updateConfirmSpecialistsButton: function (selectedCount, cardsToKeep) {
      const confirmBtn = document.getElementById('specialist-selection-modal-confirm-btn')
      if (!confirmBtn) return
      
      if (selectedCount === cardsToKeep) {
        // Можно подтвердить
        confirmBtn.disabled = false
        confirmBtn.classList.remove('specialist-selection-modal__confirm-btn:disabled')
        
        // Удаляем старый обработчик и добавляем новый
        const newBtn = confirmBtn.cloneNode(true)
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn)
        newBtn.addEventListener('click', () => {
          this.bgaPerformAction('actConfirmSpecialists')
            .catch((error) => {
              console.error('Error confirming specialists:', error)
            })
        })
      } else {
        // Нельзя подтвердить
        confirmBtn.disabled = true
        confirmBtn.classList.add('specialist-selection-modal__confirm-btn:disabled')
      }
    },

    _renderWaitingForSpecialistSelection: function (activePlayerId) {
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) return
      
      handContainer.innerHTML = ''
      handContainer.classList.remove('active-player-hand__center--selecting')
      
      // Получаем имя активного игрока
      const playerName = this.gamedatas?.players?.[activePlayerId]?.name || 'Игрок'
      
      handContainer.innerHTML = `
        <div class="waiting-for-selection">
          <div class="waiting-icon">⏳</div>
          <div class="waiting-text">${playerName} ${_('выбирает карты сотрудников...')}</div>
        </div>
      `
    },

    /**
     * Рендерит сохранённые карты сотрудников на руке игрока
     * Вызывается после этапа выбора карт (SpecialistSelection)
     */
    _renderPlayerSpecialists: function () {
      console.log('🎴 _renderPlayerSpecialists called')
      
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) {
        console.error('🎴 Hand container not found!')
        return
      }
      
      // Получаем сохранённые карты сотрудников текущего игрока
      // ВАЖНО: Используем только gamedatas.playerSpecialists, не смешиваем с players[].specialists
      const playerSpecialists = this.gamedatas?.playerSpecialists || []
      
      console.log('🎴 Player specialists:', playerSpecialists.length, 'cards')
      console.log('🎴 Player specialists source: gamedatas.playerSpecialists')
      
      if (!playerSpecialists || playerSpecialists.length === 0) {
        console.log('🎴 No saved specialists to render')
        return
      }
      
      // Очищаем контейнер
      handContainer.innerHTML = ''
      handContainer.classList.remove('active-player-hand__center--selecting')
      handContainer.style.display = 'flex'
      handContainer.style.visibility = 'visible'
      handContainer.style.opacity = '1'
      
      // Контейнер для карт
      const cardsWrapper = document.createElement('div')
      cardsWrapper.className = 'specialist-cards-wrapper specialist-cards-wrapper--saved'
      
      // Рендерим каждую карту (без возможности выбора)
      playerSpecialists.forEach((card) => {
        const cardDiv = this._createSpecialistCardReadonly(card)
        cardsWrapper.appendChild(cardDiv)
      })
      
      handContainer.appendChild(cardsWrapper)
      console.log('🎴 Rendered', playerSpecialists.length, 'saved specialist cards')
    },

    /**
     * Создаёт карту сотрудника только для отображения (без возможности выбора)
     */
    _createSpecialistCardReadonly: function (card) {
      const cardDiv = document.createElement('div')
      cardDiv.className = 'specialist-card specialist-card--saved'
      cardDiv.dataset.cardId = card.id
      cardDiv.dataset.department = card.department || 'unknown'
      
      const imageUrl = card.img ? (card.img.startsWith('http') ? card.img : `${g_gamethemeurl}${card.img}`) : ''
      
      cardDiv.innerHTML = `
        <div class="specialist-card__inner">
          ${imageUrl ? `<img src="${imageUrl}" alt="${card.name || ''}" class="specialist-card__image" />` : ''}
        </div>
      `
      
      return cardDiv
    },

    // Helpers
    _renderRoundBanner: function (round, total, roundName, cubeFace, phaseName) {
      // Текущий раунд, Общее количество раундов, Название раунда, Значение кубика на раунд
      //
      const el = document.getElementById('round-banner')
      if (!el) return
      const title = _('Раунд ${round}/${total}').replace('${round}', String(round)).replace('${total}', String(total))
      const name = roundName || '' // Название раунда
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
      this._updateStageBanner()

      // Отображаем индикаторы игроков на плашете событий
      // Ждем, пока трек раундов будет отрендерен
      setTimeout(() => {
        const roundPanel = document.querySelector('.round-panel__wrapper')
        if (roundPanel) {
          this._renderPlayerIndicators(roundPanel)
        } else {
          console.error('roundPanel not found in _renderGameSetup!')
        }
      }, 300)

      console.log('Game setup in progress...')
    },
    
    // Обновляет баннер с текущим этапом игры
    _updateStageBanner: function () {
      const banner = document.getElementById('round-banner')
      if (!banner) {
        console.error('🏷️ _updateStageBanner: banner element not found!')
        return
      }
      
      const content = banner.querySelector('.round-banner__content')
      const currentState = this.gamedatas?.gamestate?.name
      const roundNumber = this.gamedatas?.round || this.gamedatas?.roundNumber || this.gamedatas?.round_number || 0
      const roundName = this.gamedatas?.roundName || ''
      
      console.log('🏷️ _updateStageBanner called:', { currentState, roundNumber, roundName })
      
      // Определяем текущий этап
      // ЭТАП 1: GameSetup, FounderSelection (выбор карт основателей), SpecialistSelection (выбор карт сотрудников)
      // ЭТАП 2: RoundEvent, PlayerTurn, NextPlayer и т.д.
      const isStage1 = currentState === 'GameSetup' || currentState === 'FounderSelection' || currentState === 'SpecialistSelection'
      
      let bannerText = ''
      let bgColor = ''
      let bannerClass = ''
      
      if (isStage1) {
        bannerText = _('🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ')
        bgColor = '#FFA500' // Оранжевый
        bannerClass = 'round-banner round-banner--setup'
      } else if (roundNumber > 0) {
        // ЭТАП 2 с номером раунда
        bannerText = _('🎮 ЭТАП 2: РАУНД ${round}').replace('${round}', roundNumber)
        bgColor = '#2196F3' // Синий
        bannerClass = 'round-banner round-banner--game-start'
      } else {
        // ЭТАП 2 без данных о раунде
        bannerText = _('🎮 ЭТАП 2: НАЧАЛО ИГРЫ')
        bgColor = '#2196F3' // Синий
        bannerClass = 'round-banner round-banner--game-start'
      }
      
      // Обновляем баннер
      if (content) {
        content.textContent = bannerText
      } else {
        banner.textContent = bannerText
      }
      banner.className = bannerClass
      banner.style.backgroundColor = bgColor
      banner.style.color = '#FFFFFF'
      banner.style.fontSize = '20px'
      banner.style.fontWeight = 'bold'
      banner.style.padding = '10px 0px'
      banner.style.textAlign = 'center'
      banner.style.display = 'block'
      banner.style.visibility = 'visible'
      
      console.log('🏷️ Stage banner updated:', bannerText, 'state:', currentState, 'bgColor:', bgColor)
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
      const display = document.getElementById('cube-face-display')
      if (!display) {
        console.warn('cube-face-display element not found')
        return
      }
      const value = cubeFace ? String(cubeFace).trim() : ''
      display.textContent = value
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
      // Если цвет пустой или только #, используем белый по умолчанию
      if (!color || color === '#') {
        color = '#ffffff'
      }
      const panel = panelBody.closest('.player-money-panel')
      if (panel) {
        panel.style.setProperty('--player-money-color', color)
        panel.setAttribute('data-player-id', String(playerId))
        const colorBadge = panel.querySelector('.player-money-panel__color-badge')
        if (colorBadge) {
          colorBadge.style.backgroundColor = color
        }
      }

      this._updatePlayerBoardImage(color)

      panelBody.innerHTML = `
        <div class="player-money-panel__balance">
          <img src="${imageUrl}" alt="${coinData?.name || _('Баджерсы')}" class="player-money-panel__icon" />
          <span class="player-money-panel__amount">${amount}</span>
        </div>
      `
      // УБРАНО: _renderFounderCard теперь вызывается отдельно, не из _renderPlayerMoney
      // Это исправляет баг, когда карта другого игрока появлялась при обновлении денег
    },
    _renderFounderCard: function (players, targetPlayerId) {
      // Блок "Найм сотрудников" общий для всех игроков
      // Контейнеры отделов находятся в блоке "Найм сотрудников"
      const containers = {
        'sales-department': document.querySelector('.sales-department__body'),
        'back-office': document.querySelector('.back-office__body'),
        'technical-department': document.querySelector('.technical-department__body'),
      }

      const handContainer = document.getElementById('active-player-hand-cards')

      // Контейнеры для отделов (для отладки)
      // const containersFound = { 'sales-department': !!containers['sales-department'], ... }

      // Если контейнеры не найдены, выводим предупреждение
      if (!containers['sales-department'] && !containers['back-office'] && !containers['technical-department']) {
        console.error('_renderFounderCard - ERROR: No containers found! Searching in DOM...')
        const allContainers = document.querySelectorAll('.sales-department__body, .back-office__body, .technical-department__body')
        console.error('_renderFounderCard - Found containers in DOM:', allContainers.length, Array.from(allContainers))
      }

      this.pendingFounderMove = null // Сбрасываем ожидание перемещения карты основателя
      this._setDepartmentHighlight(false) // Сбрасываем выделение отдела
      this._setHandHighlight(false)

      const fallbackId = this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
      const playerId = targetPlayerId ?? fallbackId

      // Проверяем состояние ДО очистки отделов
      const currentState = this.gamedatas?.gamestate?.name
      const isSpecialistSelection = currentState === 'SpecialistSelection'

      // ВАЖНО: В состоянии SpecialistSelection отделы уже очищены в _clearDepartmentsForNewPlayer
      // Здесь просто удаляем старую карту этого игрока (если есть) перед отрисовкой новой
      const isTutorial = this.isTutorialMode
      Object.values(containers).forEach((container) => {
        if (container) {
          // Удаляем только карту этого игрока (если есть)
          const existingCard = container.querySelector(`[data-player-id="${playerId}"]`)
          if (existingCard) {
            existingCard.remove()
          }
        }
      })
      
      if (handContainer) {
        // ВАЖНО: В состоянии SpecialistSelection НЕ трогаем контейнер руки вообще!
        // Карты специалистов должны оставаться на руке, карты основателей - в отделах
        if (!isSpecialistSelection) {
        // Не очищаем руку, если там есть карты для выбора (в состоянии FounderSelection)
        const hasSelectableCards = handContainer.querySelector('.founder-card--selectable')
        const isFounderSelection = currentState === 'FounderSelection'
        const isMainMode = !this.isTutorialMode
        const isCurrentPlayer = Number(playerId) === Number(this.player_id)

          // Если это состояние выбора основателя и текущий игрок, не очищаем контейнер
        if (isFounderSelection && isMainMode && isCurrentPlayer && hasSelectableCards) {
          // Не очищаем контейнер, если там есть карты для выбора
        } else if (!hasSelectableCards) {
          handContainer.innerHTML = ''
        }

        // Убираем выделение только если нет карт для выбора
        if (!hasSelectableCards) {
          handContainer.classList.remove('active-player-hand__center--selecting')
          }
        }
      }

      // Проверяем, есть ли карты для выбора (в основном режиме)
      const isFounderSelection = currentState === 'FounderSelection'
      const isMainMode = !this.isTutorialMode

      if (isFounderSelection && isMainMode && Number(playerId) === Number(this.player_id)) {
        // Показываем карты для выбора (проверяем все возможные источники данных)
        const founderOptions = this.gamedatas?.founderOptions || this.gamedatas?.activeFounderOptions || this.gamedatas?.allPlayersFounderOptions?.[playerId] || []
        if (founderOptions.length > 0) {
          setTimeout(() => {
            this._renderFounderSelectionCards(founderOptions, playerId)
          }, 100)
          return
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

      // Если карта в отделе, показываем её
      if (rawDepartment !== 'universal') {
        const container = containers[department] || containers['sales-department']
        if (container) {
          // В основном режиме удаляем только карту этого игрока, чтобы не затереть карты других игроков
          const isTutorial = this.gamedatas.isTutorialMode
          if (isTutorial) {
            // В Tutorial режиме очищаем весь контейнер
            container.innerHTML = ''
          } else {
            // В основном режиме удаляем только карту этого игрока
            const existingCard = container.querySelector(`[data-player-id="${playerId}"]`)
            if (existingCard) {
              existingCard.remove()
            }
          }
          
          const cardMarkup = `
            <div class="founder-card" data-player-id="${playerId}" data-department="${department}">
              ${imageUrl ? `<img src="${imageUrl}" alt="${name}" class="founder-card__image" />` : ''}
            </div>
          `
          container.innerHTML = cardMarkup
        } else {
          console.error('_renderFounderCard - ❌ Container not found for department:', department)
          console.error(
            '_renderFounderCard - Available containers:',
            Object.keys(containers).map((key) => ({ key, found: !!containers[key], element: containers[key] }))
          )

          // Попробуем найти контейнеры еще раз
          const retryContainers = {
            'sales-department': document.querySelector('.sales-department__body'),
            'back-office': document.querySelector('.back-office__body'),
            'technical-department': document.querySelector('.technical-department__body'),
          }
          console.error('_renderFounderCard - Retry search results:', retryContainers)
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
          
          // Устанавливаем обработчики после рендеринга карты (для основного режима)
          if (!this.gamedatas.isTutorialMode) {
            // Переустанавливаем обработчики, чтобы они работали с новой картой
            setTimeout(() => {
              this._setupHandInteractions()
            }, 100)
          }
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
      
      // ВАЖНО: Проверяем флаг - если карта уже выбрана текущим игроком, не рендерим
      if (this.founderSelectedByCurrentPlayer) {
        console.log('🎴 Founder already selected by current player (flag), skipping render')
        return
      }
      
      // Проверяем, есть ли у текущего игрока уже выбранный основатель в gamedatas
      if (this.gamedatas?.players?.[this.player_id]?.founder) {
        console.log('🎴 Player already has founder in gamedatas, skipping selection cards render')
        return
      }
      
      // Проверяем, очищены ли founderOptions в gamedatas
      if (this.gamedatas.founderOptions === null && this.gamedatas.activeFounderOptions === null) {
        console.log('🎴 founderOptions cleared, skipping render')
        return
      }

      // Определяем, показывать ли карты или рубашку (логика из обучающего режима)
      const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
      const isMyTurn = activePlayerId && Number(activePlayerId) === Number(this.player_id) && Number(playerId) === Number(this.player_id)

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

        // Если это не мой ход, показываем три рубашки карт
        if (!isMyTurn) {
          console.log('🎴 Not my turn, showing 3 card backs for player ' + playerId)
          const backImageUrl = `${g_gamethemeurl}img/back-cards.png`

          // Создаем три рубашки карт
          for (let i = 0; i < 3; i++) {
            const backCardElement = document.createElement('div')
            backCardElement.className = 'founder-card founder-card--back'
            backCardElement.dataset.playerId = playerId
            backCardElement.style.minWidth = '150px'
            backCardElement.style.maxWidth = '200px'
            backCardElement.style.flex = '0 0 auto'

            const img = document.createElement('img')
            img.src = backImageUrl
            img.alt = _('Рубашка карты')
            img.className = 'founder-card__image'
            img.style.width = '100%'
            img.style.height = 'auto'
            img.style.display = 'block'

            backCardElement.appendChild(img)
            handContainer.appendChild(backCardElement)
          }
          return
        }

        console.log('🎴 Rendering ' + founderOptions.length + ' founder selection cards')

        // Отображаем три карты для выбора (только для активного игрока)
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
      console.log('🎯 _selectFounderCard called with cardId:', cardId)
      
      // Находим данные выбранной карты из опций
      const founderOptions = this.gamedatas?.founderOptions || this.gamedatas?.activeFounderOptions || []
      const selectedFounder = founderOptions.find(f => f.id === cardId || f.card_id === cardId)
      
      console.log('🎯 Selected founder:', selectedFounder)

      this.bgaPerformAction('actSelectFounder', {
          cardId: cardId,
      }).then(() => {
        console.log('✅ Founder card selected successfully!')
        
        // Сразу обновляем UI, не дожидаясь уведомления
        if (selectedFounder) {
          const department = selectedFounder.department || 'universal'
          const playerId = this.player_id
          const isUniversal = department === 'universal'
          
          // Обновляем данные в gamedatas
          if (!this.gamedatas.founders) this.gamedatas.founders = {}
          if (!this.gamedatas.players[playerId]) this.gamedatas.players[playerId] = {}
          
          this.gamedatas.founders[playerId] = { ...selectedFounder, department }
          this.gamedatas.players[playerId].founder = { ...selectedFounder, department }
          
          // Очищаем опции выбора
          this.gamedatas.founderOptions = null
          this.gamedatas.activeFounderOptions = null
          
          // ВАЖНО: Устанавливаем флаг что карта выбрана
          this.founderSelectedByCurrentPlayer = true
          
          // Очищаем руку от карт выбора
          const handContainer = document.getElementById('active-player-hand-cards')
          if (handContainer) {
            handContainer.innerHTML = ''
            handContainer.classList.remove('active-player-hand__center--selecting')
          }
          
          // Если карта универсальная - показываем на руке
          if (isUniversal) {
            console.log('🎯 Universal card - rendering on hand')
            this._renderUniversalFounderOnHand(selectedFounder, playerId)
            setTimeout(() => this._setupHandInteractions(), 100)
          } else {
            // Не универсальная - размещаем в отдел
            console.log('🎯 Non-universal card - placing in department:', department)
            this._renderFounderCardInDepartment(selectedFounder, playerId, department)
          }
          
          // ВАЖНО: Добавляем кнопку "Завершить ход" после выбора карты
          this._addFinishTurnButton(isUniversal)
          }
      }).catch((error) => {
        console.error('❌ Error selecting founder card:', error)
      })
    },
    
    // Добавляем кнопку "Завершить ход"
    _addFinishTurnButton: function (isDisabled) {
      // Удаляем старую кнопку если есть
      const existingButton = document.getElementById('finish-turn-button')
      if (existingButton) {
        existingButton.remove()
      }
      
      // Добавляем новую кнопку
      this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), {
        primary: true,
        disabled: isDisabled,
        tooltip: isDisabled ? _('Вы должны разместить карту основателя в один из отделов перед завершением хода') : undefined,
        id: 'finish-turn-button',
      })
      
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
      console.log('=== _renderProjectTokensOnBoard CALLED ===')
      console.log('projectTokens:', projectTokens)
      console.log('projectTokens type:', typeof projectTokens)
      console.log('projectTokens length:', projectTokens?.length || 0)

      if (!projectTokens || projectTokens.length === 0) {
        console.warn('⚠️ No project tokens to render - array is empty or undefined')
        return
      }

      // Проверяем наличие контейнера
      const allRows = document.querySelectorAll('.project-board-panel__row[data-label]')
      console.log('Found project board rows:', allRows.length, Array.from(allRows).map(r => r.dataset.label))

      // Отображаем каждый жетон в соответствующей позиции
      projectTokens.forEach((tokenData) => {
        const boardPosition = tokenData.board_position
        console.log('Processing token:', { 
          token_id: tokenData.token_id, 
          number: tokenData.number, 
          board_position: boardPosition,
          image_url: tokenData.image_url 
        })
        
        if (!boardPosition) {
          console.warn('Token has no board_position:', tokenData)
          return
        }

        // Находим div с соответствующим data-label
        const rowElement = document.querySelector(`.project-board-panel__row[data-label="${boardPosition}"]`)
        if (!rowElement) {
          console.warn('Row element not found for position:', boardPosition, 'Available positions:', Array.from(allRows).map(r => r.dataset.label))
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
          const imageUrl = tokenData.image_url.startsWith('http') ? tokenData.image_url : `${g_gamethemeurl}${tokenData.image_url}`
          img.src = imageUrl
          img.alt = tokenData.name || 'Project token'
          img.className = 'project-token__image'
          img.onerror = () => console.error('Failed to load project token image:', imageUrl)
          tokenElement.appendChild(img)
        } else {
          // Если нет изображения, создаем текстовый элемент
          const text = document.createElement('div')
          text.className = 'project-token__text'
          text.textContent = tokenData.name || `Token ${tokenData.number}`
          tokenElement.appendChild(text)
          console.log('Created project token with text:', text.textContent)
        }

        // Добавляем жетон в строку
        rowElement.appendChild(tokenElement)
        console.log('Rendered project token', tokenData.number, 'at position', boardPosition, 'rowElement:', rowElement)
      })
    },

    _renderTaskTokens: function (players) {
      // Отображаем жетоны задач во всех колонках спринт-панели
      const currentPlayerId = this.player_id
      
      // players может быть объектом или массивом
      let currentPlayer = null
      if (Array.isArray(players)) {
        currentPlayer = players.find(p => Number(p.id) === Number(currentPlayerId))
      } else if (players) {
        // Пробуем разные варианты ключей
        currentPlayer = players[currentPlayerId] || players[String(currentPlayerId)] || players[Number(currentPlayerId)]
      }

      console.log('_renderTaskTokens called', { 
        players, 
        currentPlayerId, 
        currentPlayer, 
        playersType: Array.isArray(players) ? 'array' : typeof players,
        playersKeys: players && !Array.isArray(players) ? Object.keys(players) : 'N/A'
      })

      if (!currentPlayer) {
        console.warn('⚠️ _renderTaskTokens: Current player not found!', { 
          currentPlayerId, 
          playersKeys: players && !Array.isArray(players) ? Object.keys(players) : 'N/A',
          playersIsArray: Array.isArray(players)
        })
        return
      }

      // Получаем жетоны задач для текущего игрока
      const taskTokens = currentPlayer?.taskTokens || []
      console.log('Task tokens for player:', taskTokens, 'count:', taskTokens.length)
      
      if (taskTokens.length === 0) {
        console.log('ℹ️ No task tokens to render for player', currentPlayerId, 'taskTokens:', taskTokens)
      }

      // Маппинг локаций на ID колонок
      const locationToColumnId = {
        'backlog': 'sprint-column-backlog',
        'in-progress': 'sprint-column-in-progress',
        'testing': 'sprint-column-testing',
        'completed': 'sprint-column-completed',
      }

      // Рендерим жетоны для каждой колонки
      Object.keys(locationToColumnId).forEach((location) => {
        const columnId = locationToColumnId[location]
        const column = document.getElementById(columnId)
        
        if (!column) {
          console.warn('Column not found:', columnId)
          return
        }

        // Очищаем колонку от старых жетонов
        const existingTokens = column.querySelectorAll('.task-token')
        existingTokens.forEach((token) => token.remove())

        // Получаем жетоны для этой локации
        const locationTokens = taskTokens.filter((token) => token.location === location)
        console.log(`${location} tokens:`, locationTokens)

        // Создаем контейнер для жетонов, если его еще нет
        let tokensContainer = column.querySelector('.task-tokens-container')
      if (!tokensContainer) {
        tokensContainer = document.createElement('div')
        tokensContainer.className = 'task-tokens-container'
          column.appendChild(tokensContainer)
      }

      // Очищаем контейнер
      tokensContainer.innerHTML = ''

      // Отображаем жетоны задач
        locationTokens.forEach((tokenData, index) => {
        const token = document.createElement('div')
        token.className = 'task-token'
        token.dataset.playerId = currentPlayerId
        token.dataset.tokenId = tokenData?.token_id || ''
        token.dataset.color = tokenData?.color || ''
          token.dataset.location = tokenData?.location || location

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
          console.log('Task token created:', { location, index, color: tokenData?.color, token })
      })

        console.log(`${location} tokens rendered:`, locationTokens.length)
      })
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

    _renderTaskInputs: function () {
      // Рендерим 4 input'а с кнопками для выбора задач в parts-of-projects__body
      console.log('🔄 _renderTaskInputs: Starting...')
      
      // Пробуем разные способы найти контейнер
      let container = document.querySelector('.parts-of-projects__body')
      if (!container) {
        container = dojo.query('.parts-of-projects__body')[0]
      }
      if (!container) {
        const partsOfProjects = document.querySelector('.parts-of-projects')
        if (partsOfProjects) {
          container = partsOfProjects.querySelector('.parts-of-projects__body')
        }
      }
      
      if (!container) {
        console.warn('⚠️ parts-of-projects__body not found, trying again in 500ms...')
        console.log('Available elements:', {
          partsOfProjects: !!document.querySelector('.parts-of-projects'),
          allPartsOfProjects: document.querySelectorAll('.parts-of-projects').length,
          allBodies: document.querySelectorAll('[class*="body"]').length
        })
        setTimeout(() => {
          const retryContainer = document.querySelector('.parts-of-projects__body') || 
                                 dojo.query('.parts-of-projects__body')[0]
          if (retryContainer) {
            console.log('✅ parts-of-projects__body found on retry')
            this._renderTaskInputs()
          } else {
            console.error('❌ parts-of-projects__body still not found after retry')
          }
        }, 500)
        return
      }

      console.log('✅ parts-of-projects__body found, rendering inputs...', container)

      // Очищаем контейнер
      container.innerHTML = ''

      // Массив цветов задач
      const taskColors = ['cyan', 'orange', 'pink', 'purple']

      // Создаем контейнер для всех input'ов
      const inputsContainer = document.createElement('div')
      inputsContainer.className = 'task-inputs-container'

      // Создаем input для каждого цвета
      taskColors.forEach((color) => {
        const colorData = this._getTaskTokenColorData(color)
        if (!colorData) {
          console.warn(`⚠️ Color data not found for: ${color}`)
          return
        }
        console.log(`✅ Creating input for color: ${color}`, colorData)

        // Контейнер для одного input'а
        const inputWrapper = document.createElement('div')
        inputWrapper.className = `task-input-wrapper task-input-wrapper--${color}`
        inputWrapper.dataset.color = color

        // Картинка над input'ом
        const image = document.createElement('img')
        image.src = `${g_gamethemeurl}${colorData.image_url}`
        image.alt = colorData.name || _('Жетон задачи')
        image.className = 'task-input__image'

        // Контейнер для input и кнопок
        const inputGroup = document.createElement('div')
        inputGroup.className = 'task-input-group'

        // Кнопка уменьшения
        const decreaseBtn = document.createElement('button')
        decreaseBtn.type = 'button'
        decreaseBtn.className = 'task-input__button task-input__button--decrease'
        decreaseBtn.textContent = '−'
        decreaseBtn.setAttribute('aria-label', _('Уменьшить'))

        // Input
        const input = document.createElement('input')
        input.type = 'number'
        input.step = 1
        input.max = 7
        input.min = 0
        input.value = 0
        input.className = 'task-input__field'
        input.dataset.color = color
        input.id = `task-input-${color}`

        // Кнопка увеличения
        const increaseBtn = document.createElement('button')
        increaseBtn.type = 'button'
        increaseBtn.className = 'task-input__button task-input__button--increase'
        increaseBtn.textContent = '+'
        increaseBtn.setAttribute('aria-label', _('Увеличить'))

        // Обработчики для кнопок
        decreaseBtn.addEventListener('click', () => {
          const currentValue = parseInt(input.value) || 0
          if (currentValue > input.min) {
            input.value = currentValue - 1
            input.dispatchEvent(new Event('change', { bubbles: true }))
          }
        })

        increaseBtn.addEventListener('click', () => {
          const currentValue = parseInt(input.value) || 0
          if (currentValue < input.max) {
            input.value = currentValue + 1
            input.dispatchEvent(new Event('change', { bubbles: true }))
          }
        })

        // Собираем структуру
        inputGroup.appendChild(decreaseBtn)
        inputGroup.appendChild(input)
        inputGroup.appendChild(increaseBtn)

        inputWrapper.appendChild(image)
        inputWrapper.appendChild(inputGroup)

        inputsContainer.appendChild(inputWrapper)
      })

      container.appendChild(inputsContainer)
      console.log('✅ _renderTaskInputs: Completed, added', taskColors.length, 'inputs')
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
        const currentState = this.gamedatas?.gamestate?.name
        
        // В обучающем режиме разрешаем управление картами текущему игроку
        // Tutorial использует FounderSelection так же как основной режим
        const isTutorialMode = this.gamedatas.isTutorialMode && currentState === 'GameSetup'

        if (!isTutorialMode) {
          // В основном режиме проверяем активного игрока
          const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
          if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
            return // Только активный игрок может управлять картами
          }
          
          // В основном режиме проверяем, что это универсальная карта в руке
          const isFounderSelection = currentState === 'FounderSelection'
          const isPlayerTurn = currentState === 'PlayerTurn'
          
          // Разрешаем только в состояниях, где можно размещать карту
          if (!isFounderSelection && !isPlayerTurn) {
            return
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

        // Проверяем, что это универсальная карта (department='universal')
        const cardDepartment = card.dataset.department || ''
        if (cardDepartment !== 'universal') {
          console.log('Card is not universal, department:', cardDepartment)
          return // Только универсальные карты можно размещать
        }

        // Переключаем активное состояние карты
        const isActive = card.classList.toggle('founder-card--active')
        // Добавляем/убираем увеличение карты в 2 раза
        card.classList.toggle('founder-card--enlarged', isActive)
        
        // Подсвечиваем отделы для выбора
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
          
          const currentState = this.gamedatas?.gamestate?.name

          // В обучающем режиме разрешаем размещение карт текущему игроку
          // Tutorial использует FounderSelection так же как основной режим
          const isTutorialMode = this.gamedatas.isTutorialMode && currentState === 'GameSetup'

          if (!isTutorialMode) {
            // В основном режиме проверяем активного игрока
            const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
            if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
              console.log('Not active player, cannot place card')
              return // Только активный игрок может размещать карты
            }
            
            // В основном режиме проверяем состояние игры
            const isFounderSelection = currentState === 'FounderSelection'
            const isPlayerTurn = currentState === 'PlayerTurn'
            
            // Разрешаем только в состояниях, где можно размещать карту
            if (!isFounderSelection && !isPlayerTurn) {
              console.log('Not in valid state for placing card:', currentState)
              return
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

          // Проверяем, что это универсальная карта
          const cardDepartment = activeCard.dataset.department || ''
          if (cardDepartment !== 'universal') {
            console.log('Card is not universal, cannot place manually')
            return
          }

          // Сразу обновляем UI
          this._setHandHighlight(false)
          this._setDepartmentHighlight(false)
          
          // Перемещаем карту в отдел визуально
          const founder = this.gamedatas?.founders?.[this.player_id] || this.gamedatas?.players?.[this.player_id]?.founder
          if (founder) {
            // Обновляем department в данных
            founder.department = department
            if (this.gamedatas?.players?.[this.player_id]?.founder) {
              this.gamedatas.players[this.player_id].founder.department = department
            }
            if (this.gamedatas?.founders?.[this.player_id]) {
              this.gamedatas.founders[this.player_id].department = department
            }
            
            // Очищаем руку и отрисовываем карту в отделе
            if (handContainer) {
              handContainer.innerHTML = ''
            }
            this._renderFounderCardInDepartment(founder, this.player_id, department)
          }

          // Вызываем серверное действие для размещения карты
          this.bgaPerformAction('actPlaceFounder', {
              department: department,
          }).then(() => {
            // Кнопка "Завершить ход" разблокируется через уведомление founderEffectsApplied
            // после применения всех эффектов карты основателя
          }).catch((error) => {
            console.error('❌ Error placing founder card:', error)
          })
        }

        // Сохраняем ссылку на обработчик для возможности удаления
        container._deptClickHandler = deptClickHandler
        container.addEventListener('click', deptClickHandler)
      })
    },
    _setDepartmentHighlight: function (enabled) {
      ;['sales-department', 'back-office', 'technical-department'].forEach((department) => {
        const container = document.querySelector(`.${department}__body`)
        if (!container) {
          console.warn('Department container not found:', department)
          return
        }
        if (enabled) {
          container.classList.add('department-highlight')
          container.setAttribute('data-highlight-label', this._getDepartmentLabel(department))
        } else {
          container.classList.remove('department-highlight')
          container.removeAttribute('data-highlight-label')
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
      const handContainer = document.getElementById('active-player-hand-cards')
      if (!handContainer) {
        console.warn('Hand container not found')
        return
      }

      if (enabled) {
        handContainer.classList.add('active-player-hand__center--selecting')
      } else {
        handContainer.classList.remove('active-player-hand__center--selecting')
        const card = handContainer.querySelector('.founder-card--active')
        if (card) {
          card.classList.remove('founder-card--active')
        }
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
