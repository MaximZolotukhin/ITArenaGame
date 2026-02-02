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

      // Example to add a div on the game area
      // Мой код для баннера раунда
      const gameArea = this.getGameAreaElement()
      if (!gameArea) {
        console.error('❌ getGameAreaElement() returned null! Cannot initialize game UI.')
        return
      }
      
      try {
        gameArea.insertAdjacentHTML(
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
                            <div class="round-panel__skills-track-row round-panel__skills-track-row--skills">
                              <div class="round-panel__skills-track-row-inner">
                                <div class="round-panel__skill-column" data-skill="eloquence"></div>
                                <div class="round-panel__skill-column" data-skill="discipline"></div>
                                <div class="round-panel__skill-column" data-skill="intellect"></div>
                                <div class="round-panel__skill-column" data-skill="frugality"></div>
                              </div>
                            </div>
                            <div class="round-panel__skills-track-row round-panel__skills-track-row--tokens">
                              <div class="round-panel__skills-track-row-inner">
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column"></div>
                                <div class="round-panel__skill-token-column"></div>
                              </div>
                            </div>
                          <!-- <div class="round-panel__skill-indicators"></div> -->
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
                                          // Все три колонки имеют одинаковую структуру: 6 ячеек от 1 до 6 сверху вниз
                                          const rowsHtml = Array(6)
                                            .fill(0)
                                            .map((_, j) => {
                                              const rowNum = 6 - j // Нумерация от 1 до 6 снизу вверх (row-6 сверху, row-1 снизу)
                                              const isBottomRow = rowNum === 1 // Нижняя ячейка (row-1)
                                              // Жетон создается только в колонке 1
                                              const shouldHaveToken = isBottomRow && columnNum === 1
                                              return `<div id="player-department-back-office-evolution-column-${columnNum}-row-${rowNum}" class="player-department-back-office-evolution__row" data-row-index="${rowNum}">${
                                                shouldHaveToken ? '<div class="player-department-back-office-evolution__token"></div>' : ''
                                              }</div>`
                                            })
                                            .join('')
                                          return `<div id="player-department-back-office-evolution-column-${columnNum}" class="player-department-back-office-evolution__column">
                                            <div class="player-department-back-office-evolution-column-${columnNum}__rows-wrapper">${rowsHtml}</div>
                                          </div>`
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
      } catch (error) {
        console.error('❌ Error inserting game HTML:', error)
        // Продолжаем выполнение, даже если произошла ошибка
      }
      
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

      // ВАЖНО: Кубик и карты событий НЕ отрисовываются в setup()
      // Они отрисовываются только в RoundEvent (этап 2, round > 0)
      // Сохраняем данные из getAllDatas() в gamedatas для будущего использования
      const currentRound = gamedatas.round || 0
      if (currentRound > 0) {
        // Только если мы уже на этапе 2, сохраняем данные
        if (gamedatas.cubeFace) {
          this.gamedatas.cubeFace = gamedatas.cubeFace
          console.log('🎲 setup: Saved cubeFace to gamedatas (Stage 2):', gamedatas.cubeFace)
        }
        if (gamedatas.roundEventCards && gamedatas.roundEventCards.length > 0) {
          this.gamedatas.roundEventCards = gamedatas.roundEventCards
          this.gamedatas.roundEventCard = gamedatas.roundEventCards[0] || null
          console.log('🎴 setup: Saved roundEventCards to gamedatas (Stage 2):', gamedatas.roundEventCards.length)
        }
      } else {
        // Этап 1 - очищаем данные кубика и карт событий
        this.gamedatas.cubeFace = ''
        this.gamedatas.roundEventCards = []
        this.gamedatas.roundEventCard = null
        console.log('🎲 setup: Stage 1 - Clearing cube and event cards data')
      }
      
      this._renderBadgers(gamedatas.badgers || [])
      const initialActiveId = this._getActivePlayerIdFromDatas(gamedatas) || this.player_id
      this._renderPlayerMoney(gamedatas.players, initialActiveId) // Отображаем деньги игрока
      
      // Сохраняем данные карт специалистов для использования в уведомлениях
      if (gamedatas.specialists && !Array.isArray(gamedatas.specialists) && typeof gamedatas.specialists === 'object') {
        // Преобразуем объект в массив
        gamedatas.specialists = Object.values(gamedatas.specialists)
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

          // ВАЖНО: Проверяем и обновляем кубик и карты событий ТОЛЬКО на этапе 2 (round > 0)
          // Это нужно на случай, если RoundEvent быстро перешел в PlayerTurn
          const currentRound = this.gamedatas?.round || args?.args?.round || 0
          
          if (currentRound > 0) {
            // ПРИОРИТЕТ: сначала args (из getArgs()), потом gamedatas (из getAllDatas()), потом уведомление
            const cubeFaceFromPlayerTurnArgs = args?.args?.cubeFace
            const cubeFaceFromPlayerTurnGamedatas = this.gamedatas?.cubeFace || ''
            const cubeFaceForPlayerTurn = cubeFaceFromPlayerTurnArgs || cubeFaceFromPlayerTurnGamedatas || ''
            
            const eventCardsFromPlayerTurnArgs = args?.args?.roundEventCards || []
            const eventCardsFromPlayerTurnGamedatas = this.gamedatas?.roundEventCards || []
            const eventCardsForPlayerTurn = eventCardsFromPlayerTurnArgs.length > 0 ? eventCardsFromPlayerTurnArgs : eventCardsFromPlayerTurnGamedatas
            
            console.log('🎲 PlayerTurn (Stage 2) - cubeFace sources:', {
              fromArgs: cubeFaceFromPlayerTurnArgs,
              fromGamedatas: cubeFaceFromPlayerTurnGamedatas,
              final: cubeFaceForPlayerTurn
            })
            console.log('🎴 PlayerTurn (Stage 2) - eventCards sources:', {
              fromArgs: eventCardsFromPlayerTurnArgs.length,
              fromGamedatas: eventCardsFromPlayerTurnGamedatas.length,
              final: eventCardsForPlayerTurn.length
            })
            
            // Обновляем gamedatas из args, если они есть
            if (cubeFaceFromPlayerTurnArgs) {
              this.gamedatas.cubeFace = cubeFaceFromPlayerTurnArgs
            }
            if (eventCardsFromPlayerTurnArgs.length > 0) {
              this.gamedatas.roundEventCards = eventCardsFromPlayerTurnArgs
              this.gamedatas.roundEventCard = eventCardsFromPlayerTurnArgs[0] || null
            }
            
            if (cubeFaceForPlayerTurn) {
              console.log('🎲 PlayerTurn: Updating cube face:', cubeFaceForPlayerTurn)
              this._updateCubeFace(cubeFaceForPlayerTurn)
            } else {
              // Если кубик еще не установлен, ждем уведомление
              console.log('🎲 PlayerTurn: cubeFace is empty, waiting for roundStart notification...')
              setTimeout(() => {
                const updatedCubeFace = this.gamedatas?.cubeFace || ''
                if (updatedCubeFace) {
                  console.log('🎲 PlayerTurn: Updating cube face from notification:', updatedCubeFace)
                  this._updateCubeFace(updatedCubeFace)
                } else {
                  console.warn('🎲 PlayerTurn: cubeFace STILL empty after timeout!')
                }
              }, 500)
            }
            
            if (eventCardsForPlayerTurn.length > 0) {
              console.log('🎴 PlayerTurn: Rendering event cards:', eventCardsForPlayerTurn)
              this._renderEventCards(eventCardsForPlayerTurn)
              this._renderRoundEventCards(eventCardsForPlayerTurn)
            } else {
              // Если карты еще не установлены, ждем уведомление
              console.log('🎴 PlayerTurn: event cards are empty, waiting for roundStart notification...')
              setTimeout(() => {
                const updatedCards = this.gamedatas?.roundEventCards || []
                if (updatedCards.length > 0) {
                  console.log('🎴 PlayerTurn: Rendering event cards from notification:', updatedCards)
                  this._renderEventCards(updatedCards)
                  this._renderRoundEventCards(updatedCards)
                } else {
                  console.warn('🎴 PlayerTurn: event cards STILL empty after timeout!')
                }
              }, 500)
            }
          } else {
            // Этап 1 - не отрисовываем кубик и карты событий
            console.log('🎲 PlayerTurn: Stage 1 - skipping cube and event cards rendering')
          }

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
          // ВАЖНО: СНАЧАЛА обновляем кубик и карты событий, ПОТОМ все остальное
          console.log('🎲 Entering RoundEvent state, args:', args)
          
          // Получаем данные из args или gamedatas
          // ВАЖНО: Приоритет - сначала args (из getArgs()), потом gamedatas (из уведомления roundStart)
          const cubeFaceFromArgs = args?.args?.cubeFace
          const cubeFaceFromGamedatas = this.gamedatas?.cubeFace
          // Используем значение из args, если оно есть, иначе из gamedatas (может быть обновлено уведомлением)
          let cubeFace = cubeFaceFromArgs || cubeFaceFromGamedatas || ''
          console.log('🎲 RoundEvent onEnteringState - cubeFace sources:', {
            cubeFaceFromArgs,
            cubeFaceFromGamedatas,
            finalCubeFace: cubeFace
          })

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

          // ВАЖНО: Проверяем, что мы на этапе 2 (round > 0)
          // RoundEvent должен вызываться только на этапе 2
          if (round <= 0) {
            console.warn('🎲 RoundEvent: round <= 0, skipping cube and event cards rendering')
            break
          }

          // Обновляем данные в gamedatas для последующих обновлений
          // ВАЖНО: Обновляем cubeFace в gamedatas, даже если оно из args (может быть пустым, но обновится уведомлением)
          if (cubeFaceFromArgs !== undefined) {
            this.gamedatas.cubeFace = cubeFaceFromArgs
          } else if (cubeFaceFromGamedatas) {
            // Если в args нет, но есть в gamedatas (из уведомления), используем его
            this.gamedatas.cubeFace = cubeFaceFromGamedatas
            cubeFace = cubeFaceFromGamedatas
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

          // ВАЖНО: ВСЕГДА обновляем кубик и карты событий в RoundEvent (это фаза событий раунда)
          // Если данные есть в args - используем их сразу
          // Если нет - ждем уведомление roundStart с небольшой задержкой
          if (cubeFace) {
            console.log('🎲 RoundEvent: Updating cube face from args:', cubeFace)
            this._updateCubeFace(cubeFace)
          } else {
            console.log('🎲 RoundEvent: cubeFace is empty in args, waiting for roundStart notification...')
            // Ждем уведомление с увеличенной задержкой (может прийти после перехода в PlayerTurn)
            setTimeout(() => {
              const updatedCubeFace = this.gamedatas?.cubeFace || ''
              if (updatedCubeFace) {
                console.log('🎲 RoundEvent: Updating cube face from notification:', updatedCubeFace)
                this._updateCubeFace(updatedCubeFace)
              } else {
                console.warn('🎲 RoundEvent: cubeFace still empty after timeout!')
              }
            }, 500)
          }
          
          if (roundEventCards.length > 0) {
            console.log('🎴 RoundEvent: Rendering round event cards from args:', roundEventCards)
            this._renderEventCards(roundEventCards)
            this._renderRoundEventCards(roundEventCards)
          } else {
            console.log('🎴 RoundEvent: No event cards in args, waiting for roundStart notification...')
            // Ждем уведомление с увеличенной задержкой (может прийти после перехода в PlayerTurn)
            setTimeout(() => {
              const updatedCards = this.gamedatas?.roundEventCards || []
              if (updatedCards.length > 0) {
                console.log('🎴 RoundEvent: Rendering event cards from notification:', updatedCards)
                this._renderEventCards(updatedCards)
                this._renderRoundEventCards(updatedCards)
              } else {
                console.warn('🎴 RoundEvent: event cards still empty after timeout!')
              }
            }, 500)
          }

          if (round && roundName) {
            this._renderRoundBanner(round, this.totalRounds, roundName, cubeFace || this.gamedatas?.cubeFace || '', phaseName)
          } else {
            // Обновляем баннер - ЭТАП 2
            this._updateStageBanner()
          }
          
          // ПОТОМ обновляем остальные элементы (карты игроков, жетоны и т.д.)
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
          break

        case 'RoundSkills':
          if (args?.args?.phaseKey) this.gamedatas.phaseKey = args.args.phaseKey
          if (args?.args?.phaseName) this.gamedatas.phaseName = args.args.phaseName
          this.gamedatas.phaseNumber = 2
          this._updateStageBanner()
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

      // Фаза навыков: явный заголовок для всех игроков (активный видит «ваш ход», остальные — «ожидание»)
      if (stateName === 'RoundSkills') {
        const phaseNameSkills = (args?.args?.phaseName) || (typeof _ !== 'undefined' ? _('Навыки') : 'Навыки')
        if (this.isCurrentPlayerActive()) {
          this.statusBar.setTitle((typeof _ !== 'undefined' ? _('Фаза «${phase}» — ваш ход') : 'Фаза «' + phaseNameSkills + '» — ваш ход').replace('${phase}', phaseNameSkills))
        } else {
          const activeId = this.gamedatas?.gamestate?.active_player
          const activeName = activeId && this.gamedatas?.players?.[activeId]?.name ? this.gamedatas.players[activeId].name : (typeof _ !== 'undefined' ? _('Игрок') : 'Игрок')
          this.statusBar.setTitle((typeof _ !== 'undefined' ? _('Фаза «${phase}» — ожидание ${player}') : 'Фаза «' + phaseNameSkills + '» — ожидание ' + activeName).replace('${phase}', phaseNameSkills).replace('${player}', activeName))
        }
      }

      // FounderSelection, NextPlayer (при pendingRoundEvent), RoundEvent — не только активный игрок.
      const isFounderSelection = stateName === 'FounderSelection'
      const a = args?.args || args || {}
      const isNextPlayerWithPending = stateName === 'NextPlayer' && a.pendingRoundEvent
      const shouldProcessActions = this.isCurrentPlayerActive() || isFounderSelection || isNextPlayerWithPending
      
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
          case 'RoundSkills':
            this._updateStageBanner()
            if (this.isCurrentPlayerActive()) {
              this.statusBar.addActionButton(_('Завершить фазу навыков'), () => this.bgaPerformAction('actCompleteSkillsPhase'), { primary: true })
            }
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

          case 'NextPlayer':
            if (a.pendingRoundEvent) {
              this.statusBar.addActionButton(_('Продолжить'), () => this.bgaPerformAction('actStartRoundEvent'), { primary: true })
            }
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

      const actionPromise = this.bgaPerformAction('actPlayCard', {
        card_id,
      })
      if (actionPromise) {
        actionPromise.then(() => {
          // What to do after the server call if it succeeded
          // (most of the time, nothing, as the game will react to notifs / change of state instead)
        })
      }
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
      dojo.subscribe('incomeTrackChanged', this, 'notif_incomeTrackChanged')
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
      dojo.subscribe('taskSelectionRequired', this, 'notif_taskSelectionRequired')
      dojo.subscribe('tasksSelected', this, 'notif_tasksSelected')
      dojo.subscribe('taskMovesRequired', this, 'notif_taskMovesRequired')
      dojo.subscribe('taskMovesCompleted', this, 'notif_taskMovesCompleted')
      dojo.subscribe('debugUpdateTrack', this, 'notif_debugUpdateTrack')
      dojo.subscribe('visualTrackChanged', this, 'notif_visualTrackChanged')
      dojo.subscribe('technicalDevelopmentMovesRequired', this, 'notif_technicalDevelopmentMovesRequired')
      dojo.subscribe('technicalDevelopmentMovesCompleted', this, 'notif_technicalDevelopmentMovesCompleted')
      dojo.subscribe('initialPlayerValues', this, 'notif_initialPlayerValues')
      
      console.log('✅ Notifications subscribed: badgersChanged, incomeTrackChanged, roundStart, founderSelected, founderPlaced, founderCardsDiscarded, specialistToggled, specialistsConfirmed, specialistsDealtToHand, specialistsDealt, founderEffectsApplied, taskSelectionRequired, tasksSelected, taskMovesRequired, taskMovesCompleted, debugUpdateTrack, visualTrackChanged, technicalDevelopmentMovesRequired, technicalDevelopmentMovesCompleted, initialPlayerValues')
    },

    // TODO: from this point and below, you can write your game notifications handling methods

    // Round updates
    notif_roundStart: async function (notif) {
      console.log('🎲🎲🎲 notif_roundStart CALLED!', notif)
      console.log('🎲 Current state:', this.gamedatas?.gamestate?.name)
      
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      console.log('🎲 cubeFace from notification:', args.cubeFace, 'type:', typeof args.cubeFace)
      console.log('🎲 roundEventCards from notification:', args.roundEventCards)
      console.log('🎲 phaseNumber from notification:', args.phaseNumber)
      console.log('🎲 phaseKey from notification:', args.phaseKey)
      
      // ВАЖНО: Сохраняем номер фазы и ключ фазы в gamedatas
      if (args.phaseNumber !== undefined) {
        this.gamedatas.phaseNumber = args.phaseNumber
        console.log('🎲 Saved phaseNumber to gamedatas:', args.phaseNumber)
      }
      if (args.phaseKey !== undefined) {
        this.gamedatas.phaseKey = args.phaseKey
        console.log('🎲 Saved phaseKey to gamedatas:', args.phaseKey)
      }

      // ВАЖНО: Обновляем данные в gamedatas ПЕРЕД обновлением отображения
      // Это гарантирует, что значение будет доступно для последующих вызовов
      if (args.cubeFace !== undefined && args.cubeFace !== null && args.cubeFace !== '') {
        this.gamedatas.cubeFace = args.cubeFace
        console.log('🎲 Updated gamedatas.cubeFace to:', args.cubeFace)
      } else {
        console.warn('🎲 WARNING: cubeFace is empty in roundStart notification!', args.cubeFace)
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
      if (args.phaseNumber !== undefined) {
        this.gamedatas.phaseNumber = args.phaseNumber
        console.log('🎲 Saved phaseNumber to gamedatas:', args.phaseNumber)
      }
      if (args.phaseKey !== undefined) {
        this.gamedatas.phaseKey = args.phaseKey
        console.log('🎲 Saved phaseKey to gamedatas:', args.phaseKey)
      }

      // Обновляем карты событий в gamedatas
      const eventCards = args.roundEventCards || (args.eventCard ? [args.eventCard] : [])
      if (eventCards.length > 0) {
        this.gamedatas.roundEventCards = eventCards
        this.gamedatas.roundEventCard = eventCards[0] || null
      }
      console.log('roundStart eventCards', eventCards)

      // ВАЖНО: Обновляем кубик и карты событий ТОЛЬКО на этапе 2 (round > 0)
      const currentRound = args.round || this.gamedatas?.round || 0
      if (currentRound > 0) {
        // ВАЖНО: СНАЧАЛА обновляем кубик и карты событий СИНХРОННО
        // Это гарантирует, что они отобразятся сразу при получении уведомления
        console.log('🎲 notif_roundStart: Updating cube face (Stage 2):', args.cubeFace)
        if (args.cubeFace) {
          this._updateCubeFace(args.cubeFace)
        }
        
        if (eventCards.length > 0) {
          console.log('🎴 notif_roundStart: Rendering event cards (Stage 2):', eventCards)
          this._renderEventCards(eventCards)
          this._renderRoundEventCards(eventCards)
        }
      } else {
        console.log('🎲 notif_roundStart: Stage 1 - skipping cube and event cards rendering')
      }
      
      // ПОТОМ обновляем баннер
      this._renderRoundBanner(args.round, this.totalRounds, args.roundName, args.cubeFace || '', args.phaseName)
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

    notif_initialPlayerValues: async function (notif) {
      console.log('🔵🔵🔵 notif_initialPlayerValues CALLED!', notif)
      const args = notif.args || notif
      const initialValues = args.initialValues || {}
      
      console.log('=== НАЧАЛЬНЫЕ ЗНАЧЕНИЯ ВСЕХ ИГРОКОВ ПОСЛЕ ИНИЦИАЛИЗАЦИИ ===')
      console.log('🔵 initialValues keys:', Object.keys(initialValues))
      Object.keys(initialValues).forEach((playerId) => {
        const values = initialValues[playerId]
        console.log(`Игрок ${playerId}:`)
        console.log(`  - badgers=${values.badgers}`)
        console.log(`  - incomeTrack=${values.incomeTrack}`)
        console.log(`  - taskTokens: всего=${values.taskTokens.total}, по локациям=`, values.taskTokens.byLocation)
        console.log(`  - projectTokens: всего=${values.projectTokens}`)
        console.log(`  - specialistHand (на руке): всего=${values.specialistHand.count}, IDs=`, values.specialistHand.ids)
        console.log(`  - playerSpecialists (подтвержденные): всего=${values.playerSpecialists.count}, IDs=`, values.playerSpecialists.ids)
        console.log(`  - backOfficeCol1=${values.backOfficeCol1 ?? 'null'}, backOfficeCol2=${values.backOfficeCol2 ?? 'null'}, backOfficeCol3=${values.backOfficeCol3 ?? 'null'}`)
        console.log(`  - techDevCol1=${values.techDevCol1 ?? 'null'}, techDevCol2=${values.techDevCol2 ?? 'null'}, techDevCol3=${values.techDevCol3 ?? 'null'}, techDevCol4=${values.techDevCol4 ?? 'null'}`)
        console.log(`  - skillToken=${values.skillToken ?? 'null'}`)
      })
      console.log('=== КОНЕЦ НАЧАЛЬНЫХ ЗНАЧЕНИЙ ===')
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
      const oldValue = Number(args.oldValue || 0)
      
      console.log('💰 Badgers changed:', { playerId, oldValue, newValue, amount, founderName })
      console.log('💰 Current player:', this.player_id, 'Target player:', playerId)
      
      // ВАЖНО: Обновляем данные в gamedatas только для указанного игрока
      // НО: Только если oldValue совпадает с текущим значением (данные не были изменены getAllDatas())
      if (playerId > 0 && this.gamedatas.players[playerId]) {
        const currentValue = this.gamedatas.players[playerId].badgers || 0
        
        // ВАЖНО: Если oldValue не совпадает с currentValue, это означает, что данные были изменены getAllDatas()
        // В этом случае мы должны использовать newValue из уведомления, так как это актуальное значение с сервера
        if (oldValue !== currentValue) {
          // Данные были изменены getAllDatas() или другим уведомлением
          // Используем newValue из уведомления, так как это актуальное значение с сервера
          this.gamedatas.players[playerId].badgers = newValue
          return
        }
        
        // ВАЖНО: Если currentValue уже равен newValue, значит данные уже актуальны
        // Это может произойти, если уведомление приходит несколько раз (через notify->all)
        if (currentValue === newValue) {
          // Данные уже актуальны, пропускаем обновление
          return
        }
        
        // ВАЖНО: Используем newValue из уведомления, так как oldValue совпадает с currentValue
        // Это означает, что данные не были изменены getAllDatas()
        this.gamedatas.players[playerId].badgers = newValue
      } else {
        console.error('🔴🔴🔴 ERROR: Cannot update badgers for player', playerId, '- player not found in gamedatas.players')
      }
      
      // Обновляем банк баджерсов, если данные пришли с сервера
      if (args.badgersSupply && Array.isArray(args.badgersSupply)) {
        console.log('💰 Updating badgers supply, count:', args.badgersSupply.length)
        this.gamedatas.badgers = args.badgersSupply
        this._renderBadgers(args.badgersSupply)
      }
      
      // ВАЖНО: Проверяем, что данные обновлены только для указанного игрока
      console.log('💰 Verifying badgers for all players after update:')
      Object.keys(this.gamedatas.players).forEach((pid) => {
        const pBadgers = this.gamedatas.players[pid].badgers || 0
        console.log('💰 Player', pid, 'badgers:', pBadgers, pid == playerId ? '(UPDATED)' : '(unchanged)')
      })
      
      // Обновляем отображение денег игрока (передаём оба аргумента!)
      this._renderPlayerMoney(this.gamedatas.players, playerId)
      
      // Визуальная анимация изменения
      if (amount !== 0) {
        const actionText = amount > 0 ? '+' : ''
        this.showMessage(`${founderName}: ${actionText}${amount}Б`, 'info')
      }
    },

    notif_incomeTrackChanged: async function (notif) {
      console.log('📈📈📈 notif_incomeTrackChanged called:', notif)
      
      // BGA передаёт объект notif, данные в notif.args
      const args = notif.args || notif
      console.log('📈 Extracted args:', args)
      
      const playerId = Number(args.player_id || args.playerId || 0)
      const amount = Number(args.amount || 0)
      const founderName = args.founder_name || args.founderName || 'Основатель'
      const newValue = Number(args.newValue || args.new_value || 0)
      const oldValue = Number(args.oldValue || args.old_value || 0)
      
      console.log('📈 Income track changed:', { playerId, oldValue, newValue, amount, founderName })
      
      // Обновляем данные в gamedatas
      if (playerId > 0 && this.gamedatas.players[playerId]) {
        this.gamedatas.players[playerId].energy = newValue
        console.log('📈 Updated gamedatas.players[' + playerId + '].energy to', newValue)
      }
      
      // Обновляем визуальное отображение трека дохода
      console.log('📈 Calling _updateIncomeTrackPosition with playerId:', playerId, 'newValue:', newValue)
      this._updateIncomeTrackPosition(playerId, newValue)
      
      // Визуальная анимация изменения
      if (amount !== 0) {
        const actionText = amount > 0 ? '+' : ''
        this.showMessage(`${founderName}: ${actionText}${amount} трек дохода`, 'info')
      }
    },

    /**
     * Обновляет позицию жетона на треке дохода
     * @param {number} playerId ID игрока
     * @param {number} energyValue Значение энергии (позиция на треке от 1 до 20)
     */
    _updateIncomeTrackPosition: function (playerId, energyValue) {
      console.log('📈 _updateIncomeTrackPosition called:', { playerId, energyValue })
      
      // ВАЖНО: Обновляем данные в gamedatas для правильного игрока
      if (playerId > 0 && this.gamedatas.players[playerId]) {
        this.gamedatas.players[playerId].energy = energyValue
        console.log('📈 Updated gamedatas.players[' + playerId + '].energy to', energyValue)
      } else {
        console.warn('📈 WARNING: Cannot update energy for player', playerId, '- player not found in gamedatas')
      }
      
      // ВАЖНО: Обновляем визуально только если это трек текущего игрока
      // На странице отображается только трек текущего игрока
      const currentPlayerId = Number(this.player_id)
      if (Number(playerId) !== currentPlayerId) {
        console.log('📈 Skipping visual update - track belongs to player', playerId, 'but current player is', currentPlayerId)
        return
      }
      
      // Ограничиваем значение от 1 до 20
      const position = Math.max(1, Math.min(20, energyValue || 1))
      console.log('📈 Target position:', position)
      
      // Трек дохода находится в .player-personal-board, который создается в основном HTML
      // Ищем все треки дохода на странице
      const allIncomeTracks = document.querySelectorAll('.income-track')
      console.log('📈 Found income tracks:', allIncomeTracks.length)
      
      if (allIncomeTracks.length === 0) {
        console.log('📈 _updateIncomeTrackPosition - No income tracks found on page')
        return
      }
      
      // Используем первый найденный трек (для текущего игрока)
      const playerBoard = allIncomeTracks[0]
      
      if (!playerBoard) {
        console.log('📈 _updateIncomeTrackPosition - Income track element not found')
        return
      }
      
      console.log('📈 Found income track element:', playerBoard)
      
      // Находим все секторы на треке
      const sectors = playerBoard.querySelectorAll('.income-track__sector')
      console.log('📈 Found sectors:', sectors.length)
      
      if (!sectors || sectors.length === 0) {
        console.log('📈 _updateIncomeTrackPosition - No sectors found')
        return
      }
      
      // Удаляем жетон из всех секторов
      let removedCount = 0
      sectors.forEach(sector => {
        const token = sector.querySelector('.income-track__token')
        if (token) {
          token.remove()
          removedCount++
          console.log('📈 Removed token from sector:', sector.dataset.value)
        }
      })
      console.log('📈 Removed tokens from', removedCount, 'sectors')
      
      // Находим сектор с нужным значением
      const targetSector = Array.from(sectors).find(sector => {
        const sectorValue = parseInt(sector.dataset.value, 10)
        const matches = sectorValue === position
        if (matches) {
          console.log('📈 Found matching sector:', sectorValue, 'for position:', position)
        }
        return matches
      })
      
      if (targetSector) {
        console.log('📈 Target sector found:', targetSector)
        // Находим контейнер содержимого сектора
        const sectorContent = targetSector.querySelector('.income-track__sector-content')
        if (sectorContent) {
          console.log('📈 Sector content found:', sectorContent)
          // Создаем и добавляем жетон
          const token = document.createElement('div')
          token.className = 'income-track__token'
          sectorContent.appendChild(token)
          console.log('✅ Added token to sector:', position, 'in sectorContent:', sectorContent)
        } else {
          console.log('❌ _updateIncomeTrackPosition - Sector content not found for position:', position)
          console.log('📈 Target sector HTML:', targetSector.outerHTML.substring(0, 200))
        }
      } else {
        console.log('❌ _updateIncomeTrackPosition - Target sector not found for position:', position)
        console.log('📈 Available sector values:', Array.from(sectors).map(s => s.dataset.value).join(', '))
      }
    },

    notif_visualTrackChanged: async function (notif) {
      console.log('🎯 notif_visualTrackChanged called:', notif)
      
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const trackId = args.track_id || args.trackId || ''
      const amount = Number(args.amount || 0)
      const newValue = Number(args.newValue || args.new_value || 0)
      
      console.log('🎯 Visual track changed:', { playerId, trackId, amount, newValue })
      
      // Обрабатываем трек эволюции бэк-офиса (только колонка 1 имеет визуальный жетон)
      if (trackId === 'player-department-back-office-evolution-column-1') {
        console.log('🎯 Processing back-office evolution column 1:', trackId, 'for player:', playerId, 'amount:', amount)
        this._updateBackOfficeEvolutionColumn(playerId, trackId, newValue, amount)
      } else if (trackId === 'player-department-back-office-evolution-column-2' || 
                 trackId === 'player-department-back-office-evolution-column-3') {
        // Колонки 2 и 3 - только сохраняем данные, без визуального обновления
        console.log('🎯 Processing back-office evolution column (no visual):', trackId, 'for player:', playerId, 'amount:', amount)
        const columnMatch = trackId.match(/column-(\d+)/)
        const columnNum = columnMatch ? columnMatch[1] : '1'
        const columnKey = 'column' + columnNum
        if (playerId > 0 && this.gamedatas.players[playerId]) {
          if (!this.gamedatas.players[playerId].backOfficeEvolution) {
            this.gamedatas.players[playerId].backOfficeEvolution = {}
          }
          this.gamedatas.players[playerId].backOfficeEvolution[columnKey] = newValue || (1 + amount)
        }
      } else {
        console.log('🎯 Track', trackId, 'is not a back-office evolution column, skipping')
      }
      
      // Визуальная анимация изменения
      if (amount !== 0) {
        const actionText = amount > 0 ? '+' : ''
        this.showMessage(`${args.founder_name || 'Основатель'}: ${actionText}${amount} ${args.track_name || trackId}`, 'info')
      }
    },

    /**
     * Универсальная функция для обновления позиции жетона в колонке эволюции бэк-офиса
     * @param {number} playerId ID игрока
     * @param {string} trackId ID трека (например, 'player-department-back-office-evolution-column-1')
     * @param {number} newValue Новое значение (не используется, вычисляется из текущей позиции + amount)
     * @param {number} amount Изменение (относительное, прибавляется к текущей позиции)
     */
    _updateBackOfficeEvolutionColumn: function (playerId, trackId, newValue, amount) {
      // Извлекаем номер колонки из trackId
      const columnMatch = trackId.match(/column-(\d+)/)
      const columnNum = columnMatch ? columnMatch[1] : '1'
      const columnKey = 'column' + columnNum
      
      // ВАЖНО: Жетон есть только в колонке 1! Для колонок 2 и 3 только сохраняем данные
      if (columnNum !== '1') {
        // Для колонок 2 и 3 только сохраняем данные в gamedatas, без визуального обновления
        if (playerId > 0 && this.gamedatas.players[playerId]) {
          if (!this.gamedatas.players[playerId].backOfficeEvolution) {
            this.gamedatas.players[playerId].backOfficeEvolution = {}
          }
          this.gamedatas.players[playerId].backOfficeEvolution[columnKey] = newValue || (1 + amount)
        }
        return
      }
      
      // ВАЖНО: Обновляем визуально только если это трек текущего игрока
      const currentPlayerId = Number(this.player_id)
      if (Number(playerId) !== currentPlayerId) {
        // Сохраняем данные в gamedatas для правильного игрока
        if (playerId > 0 && this.gamedatas.players[playerId]) {
          if (!this.gamedatas.players[playerId].backOfficeEvolution) {
            this.gamedatas.players[playerId].backOfficeEvolution = {}
          }
          this.gamedatas.players[playerId].backOfficeEvolution[columnKey] = newValue
        }
        return
      }
      
      // Находим контейнер колонки для текущего игрока
      const columnElement = document.getElementById(trackId)
      if (!columnElement) {
        console.log('🎯 Column element not found for trackId:', trackId)
        return
      }
      
      console.log('🎯 Column element found:', columnElement, 'for trackId:', trackId)
      console.log('🎯 Column element innerHTML length:', columnElement.innerHTML.length)
      
      // Находим wrapper со строками (может быть с классом column-1__rows-wrapper, column-2__rows-wrapper и т.д.)
      const wrapperClass = `player-department-back-office-evolution-column-${columnNum}__rows-wrapper`
      let wrapper = columnElement.querySelector(`.${wrapperClass}`)
      
      if (!wrapper) {
        console.log('🎯 Wrapper not found with class:', wrapperClass, 'trying fallback')
        // Fallback: пробуем найти любой wrapper или используем сам columnElement
        wrapper = columnElement.querySelector('[class*="rows-wrapper"]') || columnElement
        console.log('🎯 Fallback wrapper:', wrapper)
      } else {
        console.log('🎯 Wrapper found with class:', wrapperClass)
      }
      
      // Находим все строки в колонке - пробуем несколько способов
      let rows = wrapper.querySelectorAll('.player-department-back-office-evolution__row')
      console.log('🎯 Found rows in wrapper:', rows.length, 'in column', columnNum, 'for trackId:', trackId)
      
      // Если строки не найдены в wrapper, пробуем найти их напрямую в columnElement
      if (rows.length === 0) {
        console.log('🎯 No rows found in wrapper, trying direct search in columnElement')
        rows = columnElement.querySelectorAll('.player-department-back-office-evolution__row')
        console.log('🎯 Direct rows found in columnElement:', rows.length)
        
        // Если все еще не найдены, пробуем найти по ID
        if (rows.length === 0) {
          console.log('🎯 No rows found by querySelector, trying to find by ID')
          const rowElements = []
          for (let i = 1; i <= 6; i++) {
            const rowId = `player-department-back-office-evolution-column-${columnNum}-row-${i}`
            const row = document.getElementById(rowId)
            if (row) {
              rowElements.push(row)
              console.log('🎯 Found row by ID:', rowId)
            } else {
              console.log('🎯 Row not found by ID:', rowId)
            }
          }
          if (rowElements.length > 0) {
            console.log('🎯 Found', rowElements.length, 'rows by ID, converting to NodeList-like structure')
            // Создаем объект, похожий на NodeList
            rows = {
              length: rowElements.length,
              forEach: (callback) => rowElements.forEach(callback),
              [Symbol.iterator]: function* () {
                for (let i = 0; i < rowElements.length; i++) {
                  yield rowElements[i]
                }
              }
            }
            // Также сохраняем массив для доступа по индексу
            rows._array = rowElements
          }
        }
      }
      
      if (!rows || rows.length === 0) {
        return
      }
      
      console.log('🎯 Successfully found', rows.length, 'rows for column', columnNum)
      
      // Преобразуем rows в массив для единообразной обработки
      const rowsArray = Array.isArray(rows) ? rows : (rows._array || Array.from(rows))
      console.log('🎯 rowsArray length:', rowsArray.length)
      
      // Находим текущую позицию жетона (где он сейчас находится)
      let currentPosition = 1 // По умолчанию позиция 1 (нижняя ячейка)
      rowsArray.forEach(row => {
        const token = row.querySelector('.player-department-back-office-evolution__token')
        if (token) {
          const rowIndex = parseInt(row.dataset.rowIndex, 10)
          currentPosition = rowIndex
          console.log('🎯 Found current token at row:', rowIndex)
        }
      })
      
      console.log('🎯 Current position:', currentPosition, 'amount:', amount)
      
      // Вычисляем новую позицию: текущая позиция + изменение
      const newPosition = Math.max(1, Math.min(6, currentPosition + amount))
      console.log('🎯 New position:', newPosition, '(current:', currentPosition, '+ amount:', amount, ')')
      
      // Удаляем жетон из всех строк
      let removedCount = 0
      rowsArray.forEach(row => {
        const token = row.querySelector('.player-department-back-office-evolution__token')
        if (token) {
          token.remove()
          removedCount++
          console.log('🎯 Removed token from row:', row.dataset.rowIndex)
        }
      })
      console.log('🎯 Removed tokens from', removedCount, 'rows')
      
      // Находим строку с нужной позицией (row-1 снизу, row-6 сверху)
      // newPosition = 1 означает нижнюю ячейку (row-1), newPosition = 6 означает верхнюю ячейку (row-6)
      const targetRow = rowsArray.find(row => {
        const rowIndex = parseInt(row.dataset.rowIndex, 10)
        const matches = rowIndex === newPosition
        if (matches) {
          console.log('🎯 Found matching row:', rowIndex, 'for position:', newPosition)
        }
        return matches
      })
      
      if (targetRow) {
        console.log('🎯 Target row found:', targetRow)
        // Создаем и добавляем жетон
        const token = document.createElement('div')
        token.className = 'player-department-back-office-evolution__token'
        targetRow.appendChild(token)
        console.log('✅ Added token to row:', newPosition, 'in column:', columnNum)
        
        // Сохраняем данные в gamedatas
        if (playerId > 0 && this.gamedatas.players[playerId]) {
          if (!this.gamedatas.players[playerId].backOfficeEvolution) {
            this.gamedatas.players[playerId].backOfficeEvolution = {}
          }
          this.gamedatas.players[playerId].backOfficeEvolution[columnKey] = newPosition
          console.log('🎯 Saved back office evolution data for player', playerId, 'column:', columnKey, 'position:', newPosition)
        }
      } else {
        console.log('❌ _updateBackOfficeEvolutionColumn - Target row not found for position:', newPosition, 'in column:', columnNum)
        console.log('🎯 Available row indices:', Array.from(rows).map(r => r.dataset.rowIndex).join(', '))
      }
    },

    /**
     * Обновляет позицию жетона в колонке 1 эволюции бэк-офиса (для обратной совместимости)
     * @param {number} playerId ID игрока
     * @param {number} newValue Новое значение (не используется, вычисляется из текущей позиции + amount)
     * @param {number} amount Изменение (относительное, прибавляется к текущей позиции)
     */
    _updateBackOfficeEvolutionColumn1: function (playerId, newValue, amount) {
      // Вызываем универсальную функцию для колонки 1
      this._updateBackOfficeEvolutionColumn(playerId, 'player-department-back-office-evolution-column-1', newValue, amount)
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
      
      // ВАЖНО: Обновляем деньги нового игрока при переходе
      // Данные должны быть уже обновлены в gamedatas.players через getAllDatas()
      // Используем setTimeout, чтобы убедиться, что данные обновлены после всех уведомлений
      setTimeout(() => {
        if (this.gamedatas && this.gamedatas.players && this.gamedatas.players[activePlayerId]) {
          const badgers = this.gamedatas.players[activePlayerId].badgers ?? 0
          console.log('💰 _clearDepartmentsForNewPlayer: Updating money for new player:', activePlayerId, 'badgers:', badgers)
          this._renderPlayerMoney(this.gamedatas.players, activePlayerId)
        } else {
          console.warn('⚠️ _clearDepartmentsForNewPlayer: Player data not found in gamedatas.players for player:', activePlayerId)
        }
      }, 100)
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
        
        // ВАЖНО: Дополнительная отрисовка через небольшую задержку, чтобы убедиться, что DOM обновлён
        setTimeout(() => {
          this._renderPlayerSpecialists()
        }, 100)
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
        
        // ВАЖНО: Дополнительная отрисовка через небольшую задержку, чтобы убедиться, что DOM обновлён
        setTimeout(() => {
          this._renderPlayerSpecialists()
        }, 100)
        
        // Показываем сообщение
        const founderName = args.founder_name || args.specialist_name || 'Карта'
        const amount = args.amount || 0
        this.showMessage(`${founderName}: +${amount} карт специалистов`, 'info')
      } else {
        console.log('🎴 Notification is for another player:', playerId, 'current:', this.player_id)
      }
    },

    notif_debugUpdateTrack: async function (notif) {
      const args = notif.args || notif
      console.log('🔵🔵🔵 DEBUG updateTrack:', args)
      console.log('🔵 Card:', args.card_name, 'ID:', args.card_id)
      console.log('🔵 Has updateTrack:', args.has_updateTrack)
      console.log('🔵 updateTrack value:', args.updateTrack_value)
      console.log('🔵 updateTrack value type:', typeof args.updateTrack_value)
      console.log('🔵 updateTrack value is_array:', Array.isArray(args.updateTrack_value))
      console.log('🔵 updateTrack_count (from FoundersData):', args.updateTrack_count)
      if (Array.isArray(args.updateTrack_value)) {
        console.log('🔵 updateTrack value count:', args.updateTrack_value.length)
        args.updateTrack_value.forEach((track, idx) => {
          console.log(`🔵 updateTrack value[${idx}]:`, track)
        })
      }
      console.log('🔵 Applied effects count:', args.applied_effects_count)
      console.log('🔵 Applied effects:', args.applied_effects)
      console.log('🔵 updateTrack_in_applied:', args.updateTrack_in_applied)
      console.log('🔵 tracks_in_applied_count:', args.tracks_in_applied_count)
      console.log('🔵 tracks_in_applied:', args.tracks_in_applied)
      
      if (Array.isArray(args.tracks_in_applied)) {
        console.log('🔵 tracks_in_applied count:', args.tracks_in_applied.length)
        args.tracks_in_applied.forEach((track, idx) => {
          console.log(`🔵 tracks_in_applied[${idx}]:`, track)
          if (track.trackId === 'player-department-technical-development') {
            console.log(`🔧🔧🔧 FOUND technical-development track in applied! column:`, track.column)
          }
        })
      }
      
      // Проверяем, есть ли updateTrack в примененных эффектах
      if (args.applied_effects && Array.isArray(args.applied_effects)) {
        const updateTrackEffect = args.applied_effects.find(e => e.type === 'updateTrack')
        if (updateTrackEffect) {
          console.log('🔵🔵🔵 FOUND updateTrack in applied_effects!', updateTrackEffect)
          if (updateTrackEffect.tracks && Array.isArray(updateTrackEffect.tracks)) {
            const incomeTrack = updateTrackEffect.tracks.find(t => t.trackId === 'income-track')
            if (incomeTrack) {
              console.log('🔵🔵🔵 FOUND income-track in tracks!', incomeTrack)
            } else {
              console.log('🔴🔴🔴 income-track NOT FOUND in tracks!')
            }
          }
        } else {
          console.log('🔴🔴🔴 updateTrack NOT FOUND in applied_effects!')
        }
      }
    },

    notif_founderEffectsApplied: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      
      console.log('✅ notif_founderEffectsApplied received for player:', playerId)
      
      // Если это текущий игрок, разблокируем кнопку "Завершить ход"
      // НО только если нет ожидающего выбора задач или перемещений
      if (Number(playerId) === Number(this.player_id)) {
        // Проверяем, есть ли ожидающий выбор задач или перемещений
        const hasPendingTaskSelection = this.gamedatas?.pendingTaskSelection || false
        const hasPendingTaskMoves = this.gamedatas?.pendingTaskMoves || false
        const hasPendingTaskMovesJson = this.gamedatas?.pendingTaskMovesJson || false
        const hasPendingTechnicalDevelopmentMoves = this.gamedatas?.pendingTechnicalDevelopmentMoves || false
        
        if (!hasPendingTaskSelection && !hasPendingTaskMoves && !hasPendingTaskMovesJson && !hasPendingTechnicalDevelopmentMoves) {
          const finishButton = document.getElementById('finish-turn-button')
          if (finishButton) {
            finishButton.disabled = false
            finishButton.removeAttribute('title') // Убираем tooltip
            console.log('✅ Finish turn button unlocked after all founder effects applied')
          } else {
            // Если кнопки нет, добавляем её (активную)
            this._addFinishTurnButton(false)
          }
        } else {
          console.log('⏳ Finish turn button remains disabled - waiting for task selection/moves/technical development')
        }
      }
    },

    notif_taskSelectionRequired: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const amount = Number(args.amount || 0)
      const founderName = args.founder_name || ''
      
      console.log('🎯 notif_taskSelectionRequired received for player:', playerId, 'amount:', amount)
      
      // Если это текущий игрок, активируем выбор задач
      if (Number(playerId) === Number(this.player_id)) {
        // Сохраняем информацию о ожидающем выборе
        this.gamedatas.pendingTaskSelection = {
          amount: amount,
          founderName: founderName
        }
        
        // Проверяем, есть ли также эффект move_task (для карты Дмитрий)
        const pendingMovesJson = this.gamedatas?.pendingTaskMovesJson
        if (pendingMovesJson) {
          // Показываем подсказку о последовательности действий
          this._showFounderEffectSequenceHint(founderName, amount, JSON.parse(pendingMovesJson))
        }
        
        // Активируем input'ы для выбора задач
        this._activateTaskSelectionForFounder(amount)
      }
    },

    notif_taskMovesRequired: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const moveCount = Number(args.move_count || 0)
      const moveColor = args.move_color || 'any'
      const founderName = args.founder_name || ''
      
      console.log('🎯🎯🎯 notif_taskMovesRequired received for player:', playerId, 'moveCount:', moveCount, 'moveColor:', moveColor, 'currentPlayer:', this.player_id)
      
      // Если это текущий игрок, активируем режим перемещения задач
      if (Number(playerId) === Number(this.player_id)) {
        console.log('✅ This is current player, activating task move mode')
        
        // Сохраняем информацию о ожидающем перемещении
        this.gamedatas.pendingTaskMoves = {
          moveCount: moveCount,
          moveColor: moveColor,
          usedMoves: 0,
          moves: [], // Массив перемещений [{tokenId, fromLocation, toLocation, blocks}]
          fromEffect: true, // Флаг, что это эффект карты (не учитывать техотдел)
          moveSource: 'founder_effect' // Источник перемещения: 'founder_effect' или 'sprint_phase'
        }
        
        // Сохраняем JSON для проверки в taskSelectionRequired
        this.gamedatas.pendingTaskMovesJson = JSON.stringify({
          moveCount: moveCount,
          moveColor: moveColor,
          founderName: founderName
        })
        
        console.log('✅ pendingTaskMoves set:', this.gamedatas.pendingTaskMoves)
        console.log('✅ pendingTaskMovesJson set:', this.gamedatas.pendingTaskMovesJson)
        
        // Проверяем, завершен ли выбор задач
        if (!this.gamedatas.pendingTaskSelection) {
          console.log('✅ No pending task selection, activating move mode immediately')
          // Если выбор задач уже завершен, сразу активируем режим перемещения
          this._activateTaskMoveMode(moveCount, moveColor)
        } else {
          console.log('⏳ Task selection still pending, showing hint')
          // Если выбор задач еще не завершен, показываем подсказку
          this._showFounderEffectSequenceHint(founderName, this.gamedatas.pendingTaskSelection.amount, {
            moveCount: moveCount,
            moveColor: moveColor
          })
        }
      } else {
        console.log('⏭️ This is not current player, skipping')
      }
    },

    notif_taskMovesCompleted: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      
      console.log('✅ notif_taskMovesCompleted received for player:', playerId)
      
      // Обновляем данные игрока
      if (this.gamedatas?.players?.[playerId]) {
        // Перезагружаем данные игры для обновления задач
        this._renderTaskTokens(this.gamedatas.players)
      }
      
      // Если это текущий игрок, деактивируем режим перемещения
      if (Number(playerId) === Number(this.player_id)) {
        this.gamedatas.pendingTaskMoves = null
        this._deactivateTaskMoveMode()
        
        // Убираем подсказку о последовательности действий
        this._hideFounderEffectSequenceHint()
        
        // Теперь можно разблокировать кнопку "Завершить ход"
        const finishButton = document.getElementById('finish-turn-button')
        if (finishButton) {
          finishButton.disabled = false
          finishButton.removeAttribute('title')
        }
      }
    },

    notif_technicalDevelopmentMovesRequired: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const moveCount = Number(args.move_count || 0)
      const founderName = args.founder_name || 'Основатель'
      
      console.log('🔧🔧🔧 notif_technicalDevelopmentMovesRequired received for player:', playerId, 'moveCount:', moveCount, 'founderName:', founderName)
      
      // Если это текущий игрок, активируем режим выбора колонок
      if (Number(playerId) === Number(this.player_id)) {
        console.log('✅ This is current player, activating technical development move mode')
        this._activateTechnicalDevelopmentMoveMode(moveCount, founderName)
      } else {
        console.log('⏭️ This is not current player, skipping')
      }
    },

    notif_technicalDevelopmentMovesCompleted: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      
      console.log('✅ notif_technicalDevelopmentMovesCompleted received for player:', playerId)
      
      // Если это текущий игрок, деактивируем режим выбора
      if (Number(playerId) === Number(this.player_id)) {
        this._deactivateTechnicalDevelopmentMoveMode()
        
        // Теперь можно разблокировать кнопку "Завершить ход"
        const finishButton = document.getElementById('finish-turn-button')
        if (finishButton) {
          finishButton.disabled = false
          finishButton.removeAttribute('title')
        }
      }
    },

    notif_tasksSelected: async function (notif) {
      const args = notif.args || notif
      const playerId = Number(args.player_id || 0)
      const selectedTasks = args.selected_tasks || []
      const addedTokens = args.added_tokens || []
      
      console.log('✅ notif_tasksSelected received for player:', playerId, 'tasks:', selectedTasks)
      console.log('🔍🔍🔍 notif_tasksSelected - BEFORE processing:')
      console.log('  → this.gamedatas.pendingTaskMoves:', this.gamedatas.pendingTaskMoves)
      console.log('  → this.gamedatas.pendingTaskMovesJson:', this.gamedatas.pendingTaskMovesJson)
      console.log('  → args.pending_task_moves:', args.pending_task_moves)
      console.log('  → Was notif_taskMovesRequired called? Check logs above for "🎯🎯🎯 notif_taskMovesRequired"')
      
      // Обновляем данные игрока - добавляем задачи в backlog
      if (this.gamedatas?.players?.[playerId]) {
        if (!this.gamedatas.players[playerId].taskTokens) {
          this.gamedatas.players[playerId].taskTokens = []
        }
        
        // Добавляем новые задачи в backlog
        addedTokens.forEach((token) => {
          this.gamedatas.players[playerId].taskTokens.push({
            token_id: token.token_id,
            color: token.color,
            location: 'backlog',
            row_index: null
          })
        })
        
        // Перерисовываем жетоны задач
        this._renderTaskTokens(this.gamedatas.players)
      }
      
      // Если это текущий игрок, деактивируем выбор задач
      if (Number(playerId) === Number(this.player_id)) {
        this.gamedatas.pendingTaskSelection = null
        this._deactivateTaskSelection()
        
        // Убираем подсказку о выборе задач
        const hint = document.getElementById('founder-effect-sequence-hint')
        if (hint) {
          const step1 = hint.querySelector('.founder-effect-sequence-hint__step:first-child')
          if (step1) {
            step1.classList.remove('founder-effect-sequence-hint__step--active')
            step1.classList.add('founder-effect-sequence-hint__step--completed')
          }
        }
        
        // Проверяем, есть ли ожидающее перемещение задач (эффект move_task)
        // Проверяем два источника: pendingTaskMovesJson (из уведомления) и данные из args
        console.log('🔍 notif_tasksSelected - Checking for pendingTaskMovesJson:', this.gamedatas.pendingTaskMovesJson)
        console.log('🔍 notif_tasksSelected - Checking args for pendingTaskMoves:', args.pending_task_moves)
        console.log('🔍 notif_tasksSelected - Full args keys:', Object.keys(args))
        console.log('🔍 notif_tasksSelected - Full args:', JSON.stringify(args, null, 2))
        
        let movesData = null
        
        // ВАЖНО: Проверяем args.pending_task_moves ПЕРВЫМ, так как это данные напрямую с сервера
        // Это более надежный источник, чем pendingTaskMovesJson (который может потеряться)
        if (args.pending_task_moves) {
          try {
            movesData = typeof args.pending_task_moves === 'string' 
              ? JSON.parse(args.pending_task_moves) 
              : args.pending_task_moves
            console.log('✅✅✅ notif_tasksSelected - Found pending_task_moves in args (PRIORITY), activating move mode:', movesData)
            
            // Преобразуем данные в формат, который ожидает клиент
            if (movesData && (movesData.move_count || movesData.moveCount)) {
              // Сохраняем в pendingTaskMoves для использования в _handleTaskTokenClick
              // ВАЖНО: Сохраняем существующие moves и usedMoves, если они есть
              const existingMoves = this.gamedatas.pendingTaskMoves?.moves || []
              const existingUsedMoves = this.gamedatas.pendingTaskMoves?.usedMoves || 0
              
              this.gamedatas.pendingTaskMoves = {
                moveCount: movesData.move_count || movesData.moveCount,
                moveColor: movesData.move_color || movesData.moveColor || 'any',
                usedMoves: existingUsedMoves || movesData.used_moves || movesData.usedMoves || 0, // Сохраняем существующие или используем из args
                moves: [...existingMoves], // ВАЖНО: Сохраняем существующие перемещения!
                fromEffect: true, // ВАЖНО: это эффект карты
                moveSource: 'founder_effect', // ВАЖНО: источник - эффект карты
                founderName: movesData.founder_name || movesData.founderName || ''
              }
              console.log('✅✅✅ notif_tasksSelected - pendingTaskMoves set from args (PRIORITY, preserving moves):', {
                ...this.gamedatas.pendingTaskMoves,
                moves: this.gamedatas.pendingTaskMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation }))
              })
              
              // Также сохраняем в movesData для дальнейшей обработки
              movesData.moveCount = this.gamedatas.pendingTaskMoves.moveCount
              movesData.moveColor = this.gamedatas.pendingTaskMoves.moveColor
            }
          } catch (e) {
            console.error('❌ Error parsing pending_task_moves from args:', e)
          }
        }
        // Если в args нет, проверяем pendingTaskMovesJson (из уведомления taskMovesRequired)
        else if (this.gamedatas.pendingTaskMovesJson) {
          movesData = JSON.parse(this.gamedatas.pendingTaskMovesJson)
          console.log('✅ Found pendingTaskMovesJson (fallback), activating move mode:', movesData)
          delete this.gamedatas.pendingTaskMovesJson
        }
        // РЕЗЕРВНАЯ ЛОГИКА: Если карта "Дмитрий" (ID=1), но данных нет, создаем их на основе известного эффекта
        else if (args.founder_name === 'Дмитрий' || args.founder_name === 'Dmitry') {
          console.warn('⚠️⚠️⚠️ notif_tasksSelected - Founder is Дмитрий but no pending_task_moves found! Creating from known effect.')
          // Карта "Дмитрий" имеет эффект move_task: {move_count: 3, move_color: 'any'}
          movesData = {
            move_count: 3,
            moveColor: 'any',
            moveCount: 3,
            move_color: 'any',
            founder_name: args.founder_name || 'Дмитрий'
          }
          console.log('✅✅✅ notif_tasksSelected - Created movesData from known Дмитрий effect:', movesData)
        }
        
        // Если movesData найдено и pendingTaskMoves еще не установлен, устанавливаем его
        if (movesData && (movesData.move_count > 0 || movesData.moveCount > 0)) {
          console.log('🔍 notif_tasksSelected - movesData found:', movesData)
          console.log('🔍 notif_tasksSelected - current pendingTaskMoves:', this.gamedatas.pendingTaskMoves)
          
          if (!this.gamedatas.pendingTaskMoves) {
            console.log('⚠️ notif_tasksSelected - pendingTaskMoves is NOT set, setting it now from movesData')
            // Активируем режим перемещения задач после выбора задач
            // ВАЖНО: Сохраняем существующие moves и usedMoves, если они есть
            const existingMoves = this.gamedatas.pendingTaskMoves?.moves || []
            const existingUsedMoves = this.gamedatas.pendingTaskMoves?.usedMoves || 0
            
            this.gamedatas.pendingTaskMoves = {
              moveCount: movesData.move_count || movesData.moveCount,
              moveColor: movesData.move_color || movesData.moveColor || 'any',
              usedMoves: existingUsedMoves, // Сохраняем существующие использованные ходы
              moves: [...existingMoves], // ВАЖНО: Сохраняем существующие перемещения!
              fromEffect: true, // Флаг, что это эффект карты
              moveSource: 'founder_effect', // Источник перемещения: 'founder_effect' или 'sprint_phase'
              founderName: movesData.founder_name || movesData.founderName || ''
            }
            console.log('✅ pendingTaskMoves set from movesData (preserving existing moves):', {
              ...this.gamedatas.pendingTaskMoves,
              moves: this.gamedatas.pendingTaskMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation }))
            })
          } else {
            console.log('⚠️ notif_tasksSelected - pendingTaskMoves already exists, ensuring fromEffect and moveSource are set')
            // ВАЖНО: Гарантируем, что fromEffect и moveSource установлены правильно
            if (this.gamedatas.pendingTaskMoves.fromEffect !== true) {
              console.log('⚠️⚠️⚠️ fromEffect is not true, setting it to true')
              this.gamedatas.pendingTaskMoves.fromEffect = true
            }
            if (this.gamedatas.pendingTaskMoves.moveSource !== 'founder_effect') {
              console.log('⚠️⚠️⚠️ moveSource is not founder_effect, setting it to founder_effect')
              this.gamedatas.pendingTaskMoves.moveSource = 'founder_effect'
            }
            console.log('✅ pendingTaskMoves after ensuring flags:', this.gamedatas.pendingTaskMoves)
          }
          
          if (this.gamedatas.pendingTaskMoves) {
            const moveCount = this.gamedatas.pendingTaskMoves.moveCount
            const moveColor = this.gamedatas.pendingTaskMoves.moveColor
            console.log('🎯 notif_tasksSelected - Activating task move mode:', { moveCount, moveColor, pendingTaskMoves: this.gamedatas.pendingTaskMoves })
            this._activateTaskMoveMode(moveCount, moveColor)
          } else {
            console.error('❌❌❌ notif_tasksSelected - pendingTaskMoves is NOT set after processing movesData!')
          }
          
          // Обновляем подсказку - активируем шаг 2
          if (hint) {
            const step2 = hint.querySelector('.founder-effect-sequence-hint__step:last-child')
            if (step2) {
              step2.classList.add('founder-effect-sequence-hint__step--active')
            }
          }
        } else {
          // Убираем подсказку, если нет перемещений
          this._hideFounderEffectSequenceHint()
          
          // Теперь можно разблокировать кнопку "Завершить ход"
          const finishButton = document.getElementById('finish-turn-button')
          if (finishButton) {
            finishButton.disabled = false
            finishButton.removeAttribute('title')
          }
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
        
        // ВАЖНО: Дополнительная отрисовка через небольшую задержку, чтобы убедиться, что DOM обновлён
        setTimeout(() => {
          this._renderPlayerSpecialists()
        }, 100)
        
        // Показываем сообщение
        const founderName = args.founder_name || args.specialist_name || 'Карта'
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
      const actionPromise = this.bgaPerformAction('actToggleSpecialist', { cardId: numericCardId })
      if (actionPromise) {
        actionPromise.catch((error) => {
          console.error('❌ Error toggling specialist:', error)
        })
      }
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
          newBtn.disabled = true
          const actionPromise = this.bgaPerformAction('actConfirmSpecialists')
          if (actionPromise) {
            actionPromise.catch((error) => {
              const msg = (error && (error.message || error.responseText || error.status)) || error
              console.error('Error confirming specialists:', msg)
              newBtn.disabled = false
            })
          } else {
            newBtn.disabled = false
          }
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
      
      // ВАЖНО: Получаем номер фазы из gamedatas (приходит с сервера)
      // Сервер отправляет phaseNumber в уведомлениях и в getAllDatas
      const currentState = this.gamedatas?.gamestate?.name
      let phaseNumber = this.gamedatas?.phaseNumber || null
      
      // Если phaseNumber не пришел, пытаемся найти по phaseKey или currentState
      if (phaseNumber === null) {
        const phaseKey = this.gamedatas?.phaseKey || ''
        const roundPhases = this.gamedatas?.roundPhases || []
        
        // Ищем фазу по ключу в массиве фаз
        if (phaseKey && roundPhases.length > 0) {
          const phase = roundPhases.find(p => p.key === phaseKey)
          if (phase) {
            phaseNumber = phase.number
          }
        }
        
        // Если не нашли, определяем по состоянию (fallback)
        if (phaseNumber === null && currentState === 'RoundEvent') {
          phaseNumber = 1
        }
      }
      
      // Преобразуем в строку для отображения
      phaseNumber = phaseNumber !== null ? String(phaseNumber) : null
      
      console.log('🎴 _renderRoundBanner - Phase data:', {
        phaseNumber,
        phaseName,
        phaseKey: this.gamedatas?.phaseKey,
        roundPhases: this.gamedatas?.roundPhases,
        currentState
      })
      
      // Формируем текст в формате: ЭТАП 2: РАУНД X — Название раунда — ФАЗА№ : Название фазы
      let parts = []
      parts.push('🎮 ЭТАП 2: РАУНД ' + round)
      
      if (roundName) {
        parts.push(roundName)
      }
      
      if (phaseName) {
        // Формат: ФАЗА№ : Название фазы
        if (phaseNumber) {
          parts.push('ФАЗА№' + phaseNumber + ' : ' + phaseName)
        } else {
          parts.push('ФАЗА : ' + phaseName)
        }
      }
      
      const text = parts.join(' — ')
      
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
      try {
        const banner = document.getElementById('round-banner')
        if (!banner) {
          console.error('🏷️ _updateStageBanner: banner element not found!')
          return
        }
        
        const content = banner.querySelector('.round-banner__content')
        const currentState = this.gamedatas?.gamestate?.name
        const roundNumber = this.gamedatas?.round || this.gamedatas?.roundNumber || this.gamedatas?.round_number || 0
        const roundName = this.gamedatas?.roundName || ''
        
        // ВАЖНО: Получаем номер фазы из gamedatas (приходит с сервера)
        // Сервер отправляет phaseNumber в уведомлениях и в getAllDatas
        const roundPhases = this.gamedatas?.roundPhases || []
        let phaseNumber = this.gamedatas?.phaseNumber ?? null
        let phaseKey = this.gamedatas?.phaseKey || ''
        let phaseNameFromState = this.gamedatas?.phaseName || ''
        
        // В состоянии RoundSkills баннер должен показывать фазу «Навыки», а не «Событие»
        if (currentState === 'RoundSkills') {
          phaseKey = 'skills'
          const skillsPhase = roundPhases.find(p => p.key === 'skills')
          phaseNumber = skillsPhase ? skillsPhase.number : 2
          phaseNameFromState = skillsPhase ? (skillsPhase.name || (typeof _ !== 'undefined' ? _('Навыки') : 'Навыки')) : (typeof _ !== 'undefined' ? _('Навыки') : 'Навыки')
        }
        
        // Если phaseNumber не пришел, пытаемся найти по phaseKey или currentState
        if (phaseNumber === null) {
          if (phaseKey && roundPhases.length > 0) {
            const phase = roundPhases.find(p => p.key === phaseKey)
            if (phase) {
              phaseNumber = phase.number
              if (!phaseNameFromState) phaseNameFromState = phase.name || ''
            }
          }
          if (phaseNumber === null && currentState === 'RoundEvent') {
            phaseNumber = 1
          }
        }
        
        // Преобразуем в строку для отображения
        phaseNumber = phaseNumber !== null ? String(phaseNumber) : null
        const phaseName = phaseNameFromState
        
        console.log('🏷️ _updateStageBanner called:', { 
          currentState, 
          roundNumber, 
          roundName, 
          phaseName, 
          phaseNumber,
          phaseKey: this.gamedatas?.phaseKey,
          roundPhases: this.gamedatas?.roundPhases
        })
        
        // Определяем текущий этап
        // ЭТАП 1: GameSetup, FounderSelection (выбор карт основателей), SpecialistSelection (выбор карт сотрудников)
        // ЭТАП 2: RoundEvent, PlayerTurn, NextPlayer и т.д.
        const isStage1 = currentState === 'GameSetup' || currentState === 'FounderSelection' || currentState === 'SpecialistSelection'
        
        let bannerText = ''
        let bgColor = ''
        let bannerClass = ''
        
        try {
          if (isStage1) {
            bannerText = typeof _ !== 'undefined' ? _('🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ') : '🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ'
            bgColor = '#FFA500' // Оранжевый
            bannerClass = 'round-banner round-banner--setup'
          } else if (roundNumber > 0) {
            // ЭТАП 2 с номером раунда, названием раунда и названием фазы с номером
            let parts = []
            parts.push('🎮 ЭТАП 2: РАУНД ' + roundNumber)
            
            if (roundName) {
              parts.push(roundName)
            }
            
            if (phaseName) {
              // Формат: ФАЗА№ : Название фазы
              if (phaseNumber) {
                parts.push('ФАЗА№' + phaseNumber + ' : ' + phaseName)
              } else {
                parts.push('ФАЗА : ' + phaseName)
              }
            }
            
            bannerText = parts.join(' — ')
            bgColor = '#2196F3' // Синий
            bannerClass = 'round-banner round-banner--game-start'
          } else {
            // ЭТАП 2 без данных о раунде
            bannerText = typeof _ !== 'undefined' ? _('🎮 ЭТАП 2: НАЧАЛО ИГРЫ') : '🎮 ЭТАП 2: НАЧАЛО ИГРЫ'
            bgColor = '#2196F3' // Синий
            bannerClass = 'round-banner round-banner--game-start'
          }
        } catch (e) {
          console.error('🏷️ Error in banner text generation:', e)
          bannerText = '🔄 ЭТАП 1: ПОДГОТОВКА К ИГРЕ'
          bgColor = '#FFA500'
          bannerClass = 'round-banner round-banner--setup'
        }
        
        // Обновляем баннер
        try {
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
        } catch (e) {
          console.error('🏷️ Error updating banner styles:', e)
        }
        
        console.log('🏷️ Stage banner updated:', bannerText, 'state:', currentState, 'bgColor:', bgColor)
      } catch (e) {
        console.error('🏷️ Error in _updateStageBanner:', e)
      }
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
      if (tokenColumns.length < 4) {
        console.error('Not enough token columns found:', tokenColumns.length)
        return
      }

      // Маппинг игроков на колонки (4 колонки по центру, прижаты друг к другу):
      const playerColumnMapping = {
        0: 0,
        1: 1,
        2: 2,
        3: 3,
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
        console.warn('⚠️ _updateCubeFace: cube-face-display element not found')
        return
      }
      const value = cubeFace ? String(cubeFace).trim() : ''
      display.textContent = value
      console.log('✅ _updateCubeFace: Updated cube-face-display with value:', value)
      
      // Визуальная индикация обновления (для отладки)
      if (value) {
        display.style.opacity = '1'
      } else {
        display.style.opacity = '0.5'
      }
    },

    _updateDebugCubeDisplay: function () {
      try {
        const debugBlock = document.getElementById('debug-cube-display')
        if (!debugBlock) {
          // Элемент еще не создан, это нормально на ранних этапах
          return
        }
        
        // Определяем, на каком этапе мы находимся
        const currentState = this.gamedatas?.gamestate?.name || ''
        const currentRound = this.gamedatas?.round || 0
        
        // ЭТАП 1: Подготовка к игре - FounderSelection, SpecialistSelection, GameSetup, NextPlayer (когда round === 0)
        // ЭТАП 2: Игра - RoundEvent, PlayerTurn (когда round > 0)
        const isStage1 = currentState === 'FounderSelection' || 
                        currentState === 'SpecialistSelection' || 
                        currentState === 'GameSetup' ||
                        (currentState === 'NextPlayer' && currentRound === 0) ||
                        currentRound === 0
        
        // ВАЖНО: На этапе 1 полностью скрываем debug панель и НЕ обновляем её
        // На этапе 1 НЕТ раундов и фаз - это подготовка к игре!
        if (isStage1) {
          if (debugBlock && debugBlock.style) {
            debugBlock.style.display = 'none'
          }
          // ВАЖНО: Выходим ДО обновления любых полей - на этапе 1 нет раундов и фаз!
          return
        }
        
        // ЭТАП 2: Показываем debug панель только если round > 0
        // Дополнительная проверка - если round === 0, но мы не в этапе 1 - что-то не так
        if (currentRound <= 0) {
          if (debugBlock && debugBlock.style) {
            debugBlock.style.display = 'none'
          }
          return
        }
        
        // ЭТАП 2: round > 0 - показываем debug панель
        if (debugBlock && debugBlock.style) {
          debugBlock.style.display = 'block'
        }
        
        const roundEl = document.getElementById('debug-round')
        const roundNameEl = document.getElementById('debug-round-name')
        const phaseEl = document.getElementById('debug-phase')
        const cubeFaceEl = document.getElementById('debug-cube-face')
        const eventCardsEl = document.getElementById('debug-event-cards')
        const roundStartCalledEl = document.getElementById('debug-roundstart-called')
        const currentStateEl = document.getElementById('debug-current-state')
        const gamedatasCubeFaceEl = document.getElementById('debug-gamedatas-cubeface')
        const gamedatasCardsEl = document.getElementById('debug-gamedatas-cards')
        
        // Обновляем данные о раунде
        if (roundEl) {
          const round = this.gamedatas?.round || 0
          roundEl.textContent = round > 0 ? round : '(этап 1)'
          if (roundEl.style) {
            roundEl.style.color = round > 0 ? '#0f0' : '#ff0'
          }
        }
        
        if (roundNameEl) {
          // Берем название раунда из gamedatas (устанавливается из args или уведомлений)
          const roundName = this.gamedatas?.roundName || ''
          roundNameEl.textContent = roundName || '(не установлено)'
          if (roundNameEl.style) {
            roundNameEl.style.color = roundName ? '#0f0' : '#f00'
          }
        }
        
        // ВАЖНО: Определяем фазу ТОЛЬКО для ЭТАПА 2 (round > 0)
        // На этапе 1 (round === 0) фазы нет вообще!
        // RoundEvent = Фаза 1 (Событие), PlayerTurn = Фаза 2 (Ход игрока)
        let phaseNumber = '-'
        let phaseName = this.gamedatas?.phaseName || ''
        
        // Логируем для отладки
        console.log('🔍 _updateDebugCubeDisplay - ЭТАП 2: currentState:', currentState, 'round:', currentRound, 'phaseName from gamedatas:', phaseName)
        
        // Определяем фазу только для состояний этапа 2
        if (currentState === 'RoundEvent') {
          // ФАЗА 1: Событие раунда (первая фаза каждого раунда)
          phaseNumber = '1'
          if (!phaseName) {
            phaseName = 'Событие'
          }
          console.log('🔍 RoundEvent detected - ФАЗА 1: Событие')
        } else if (currentState === 'PlayerTurn') {
          // ФАЗА 2: Ход игрока (вторая фаза каждого раунда)
          phaseNumber = '2'
          if (!phaseName) {
            phaseName = 'Ход игрока'
          }
          console.log('🔍 PlayerTurn detected - ФАЗА 2: Ход игрока')
        } else {
          // Другие состояния этапа 2 (NextPlayer и т.д.) - не определяем фазу
          console.log('🔍 ЭТАП 2, но не RoundEvent/PlayerTurn:', currentState, '- фаза не определена')
        }
        
        // Объединяем номер и название фазы в формат "Фаза №{номер}: {название}"
        if (phaseEl) {
          let phaseText = '-'
          if (phaseNumber !== '-') {
            if (phaseName) {
              phaseText = `Фаза №${phaseNumber}: ${phaseName}`
            } else {
              phaseText = `Фаза №${phaseNumber}`
            }
          } else if (phaseName) {
            phaseText = phaseName
          }
          
          phaseEl.textContent = phaseText
          if (phaseEl.style) {
            phaseEl.style.color = phaseNumber !== '-' || phaseName ? '#0f0' : '#f00'
          }
        }
        
        if (cubeFaceEl && cubeFaceEl.style) {
          const cubeFace = this.gamedatas?.cubeFace || ''
          cubeFaceEl.textContent = cubeFace || '(пусто)'
          cubeFaceEl.style.color = cubeFace ? '#0f0' : '#f00'
        }
        
        if (eventCardsEl && eventCardsEl.style) {
          const cards = this.gamedatas?.roundEventCards || []
          eventCardsEl.textContent = cards.length > 0 ? `${cards.length} карт: ${JSON.stringify(cards.map(c => c.card_id || c.card_type_arg || '?'))}` : '(пусто)'
          eventCardsEl.style.color = cards.length > 0 ? '#0f0' : '#f00'
        }
        
        if (gamedatasCubeFaceEl && gamedatasCubeFaceEl.style) {
          const cubeFace = this.gamedatas?.cubeFace || ''
          gamedatasCubeFaceEl.textContent = cubeFace || '(пусто)'
          gamedatasCubeFaceEl.style.color = cubeFace ? '#0f0' : '#f00'
        }
        
        if (gamedatasCardsEl && gamedatasCardsEl.style) {
          const cards = this.gamedatas?.roundEventCards || []
          gamedatasCardsEl.textContent = cards.length > 0 ? `${cards.length} карт` : '(пусто)'
          gamedatasCardsEl.style.color = cards.length > 0 ? '#0f0' : '#f00'
        }
        
        if (currentStateEl) {
          currentStateEl.textContent = currentState
        }
        
        if (roundStartCalledEl) {
          // Обновляем статус вызова roundStart (устанавливается в notif_roundStart)
          const wasCalled = roundStartCalledEl.textContent === 'ДА ✅'
          if (!wasCalled) {
            roundStartCalledEl.textContent = 'нет'
            if (roundStartCalledEl.style) {
              roundStartCalledEl.style.color = '#f00'
            }
          }
        }
      } catch (error) {
        console.error('Error in _updateDebugCubeDisplay:', error)
        // Игнорируем ошибки в debug функции, чтобы не ломать игру
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
      // Обновляем деньги игрока
      const panelBody = document.querySelector('.player-money-panel__body') // Обновляем деньги игрока
      if (!panelBody) {
        console.warn('⚠️ _renderPlayerMoney: panelBody not found')
        return
      }

      const fallbackId = this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
      const playerId = targetPlayerId ?? fallbackId // Идентификатор игрока
      if (!playerId) {
        // Если игрок не найден, очищаем панель
        console.warn('⚠️ _renderPlayerMoney: playerId not found, clearing panel')
        panelBody.innerHTML = '' // Очищаем панель
        return
      }

      // ВАЖНО: Обновляем данные из gamedatas.players, если они есть
      // Это гарантирует, что мы используем актуальные данные
      let playerData = this._findPlayerData(players, playerId) // Получаем данные игрока
      
      // Если данных нет в переданном объекте players, пробуем взять из gamedatas
      if (!playerData && this.gamedatas && this.gamedatas.players && this.gamedatas.players[playerId]) {
        playerData = this.gamedatas.players[playerId]
        console.log('💰 _renderPlayerMoney: Using data from gamedatas.players for player:', playerId)
      }
      
      if (!playerData) {
        // Если игрок не найден, очищаем панель
        console.warn('⚠️ _renderPlayerMoney: playerData not found for player:', playerId)
        panelBody.innerHTML = ''
        return
      }

      const amount = Number(playerData.badgers ?? 0) || 0 // Количество баджерсов
      console.log('💰 _renderPlayerMoney: Rendering money for player:', playerId, 'amount:', amount)
      
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

      // ВАЖНО: Полностью заменяем содержимое, чтобы убрать старые данные
      panelBody.innerHTML = `
        <div class="player-money-panel__balance">
          <img src="${imageUrl}" alt="${coinData?.name || _('Баджерсы')}" class="player-money-panel__icon" />
          <span class="player-money-panel__amount">${amount}</span>
        </div>
      `
      console.log('✅ _renderPlayerMoney: Updated balance to', amount, 'for player', playerId)
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
        this._selectingFounder = false
      }).catch((error) => {
        console.error('❌ Error selecting founder card:', error)
        this._selectingFounder = false
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

        // Делаем жетоны кликабельными в backlog или в режиме move_task
        // ВАЖНО: Всегда берем актуальное значение из gamedatas
        const pendingMoves = this.gamedatas?.pendingTaskMoves
        const isMoveMode = pendingMoves !== null && pendingMoves !== undefined
        
        // Дополнительная проверка для отладки
        if (isMoveMode && pendingMoves && pendingMoves.moves) {
          console.log('🔍 _renderTaskTokens - pendingMoves check:', {
            movesCount: pendingMoves.moves.length,
            moves: pendingMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation, blocks: m.blocks }))
          })
        }
        
        // Проверяем, есть ли уже перемещение для этого жетона (делает его неактивным)
        const tokenIdNum = parseInt(tokenData?.token_id, 10)
        const tokenIdStr = String(tokenData?.token_id)
        
        // ВАЖНО: Берем актуальное значение из gamedatas, а не из замыкания
        const currentPendingMoves = this.gamedatas?.pendingTaskMoves
        const currentMoves = currentPendingMoves?.moves || []
        
        const hasExistingMove = isMoveMode && currentPendingMoves && Array.isArray(currentMoves) && currentMoves.length > 0 && currentMoves.some(m => {
          if (!m || !m.tokenId) return false
          const moveTokenIdNum = parseInt(m.tokenId, 10)
          const moveTokenIdStr = String(m.tokenId)
          // Проверяем все возможные варианты сравнения
          const matches = moveTokenIdNum === tokenIdNum || 
                         moveTokenIdStr === tokenIdStr || 
                         m.tokenId == tokenData?.token_id || 
                         m.tokenId === tokenData?.token_id ||
                         String(m.tokenId) === String(tokenData?.token_id)
          if (matches) {
            console.log('🔒 Found existing move for token during render:', { 
              tokenId: tokenData?.token_id, 
              tokenIdNum: tokenIdNum,
              tokenIdStr: tokenIdStr,
              move: m,
              moveTokenId: m.tokenId,
              moveTokenIdNum: moveTokenIdNum,
              moveTokenIdStr: moveTokenIdStr
            })
          }
          return matches
        })
        
        console.log('🔍 Token render check:', {
          tokenId: tokenData?.token_id,
          tokenIdNum: tokenIdNum,
          tokenIdStr: tokenIdStr,
          hasExistingMove: hasExistingMove,
          isMoveMode: isMoveMode,
          pendingMovesExists: !!currentPendingMoves,
          movesCount: currentMoves.length,
          moves: currentMoves.map(m => ({ 
            tokenId: m.tokenId, 
            tokenIdType: typeof m.tokenId,
            toLocation: m.toLocation 
          }))
        })
        
        if (location === 'backlog' || isMoveMode) {
          // Если жетон уже перемещен - делаем его неактивным
          if (hasExistingMove) {
            token.classList.add('task-token--inactive')
            token.style.cursor = 'not-allowed'
            console.log('🔒 Token is inactive (has existing move):', { tokenId: tokenData?.token_id, location: location })
          } else {
            token.style.cursor = 'pointer'
            token.classList.remove('task-token--inactive')
            console.log('✅ Token is active (no existing move):', { tokenId: tokenData?.token_id, location: location })
          }
          
          token.style.pointerEvents = 'auto'
          token.classList.add('task-token--clickable')
          
          // Удаляем предыдущий обработчик, если есть
          if (token._clickHandler) {
            token.removeEventListener('click', token._clickHandler)
          }
          
          // Добавляем новый обработчик
          const clickHandler = (e) => {
            e.stopPropagation()
            
            // ВАЖНО: Проверяем актуальное состояние при каждом клике
            const currentPendingMoves = this.gamedatas?.pendingTaskMoves
            const currentTokenId = tokenData?.token_id
            const currentTokenIdNum = parseInt(currentTokenId, 10)
            const currentlyHasMove = currentPendingMoves && currentPendingMoves.moves && currentPendingMoves.moves.some(m => {
              const moveTokenId = parseInt(m.tokenId, 10)
              return moveTokenId === currentTokenIdNum || m.tokenId == currentTokenId || m.tokenId === currentTokenId
            })
            
            // Если жетон неактивен и имеет перемещение - отменяем перемещение
            if (currentlyHasMove && token.classList.contains('task-token--inactive')) {
              console.log('🔄 Activating inactive token on click - canceling move:', { tokenId: currentTokenId })
              
              // ВАЖНО: Удаляем перемещение из pendingMoves, чтобы активировать жетон
              const moveIndex = currentPendingMoves.moves.findIndex(m => {
                const moveTokenId = parseInt(m.tokenId, 10)
                return moveTokenId === currentTokenIdNum || m.tokenId == currentTokenId || m.tokenId === currentTokenId
              })
              
              if (moveIndex !== -1) {
                const canceledMove = currentPendingMoves.moves[moveIndex]
                currentPendingMoves.usedMoves -= canceledMove.blocks
                currentPendingMoves.moves.splice(moveIndex, 1)
                
                // Обновляем gamedatas
                if (this.gamedatas.pendingTaskMoves) {
                  this.gamedatas.pendingTaskMoves.moves = [...currentPendingMoves.moves]
                  this.gamedatas.pendingTaskMoves.usedMoves = currentPendingMoves.usedMoves
                  console.log('✅ Updated gamedatas.pendingTaskMoves after canceling move on token click:', {
                    movesCount: this.gamedatas.pendingTaskMoves.moves.length,
                    usedMoves: this.gamedatas.pendingTaskMoves.usedMoves
                  })
                }
                
                // Возвращаем жетон в исходное положение
                const currentPlayer = this.gamedatas.players[this.player_id]
                if (currentPlayer && currentPlayer.taskTokens) {
                  const tokenDataInGamedatas = currentPlayer.taskTokens.find(t => t.token_id == currentTokenId)
                  if (tokenDataInGamedatas) {
                    tokenDataInGamedatas.location = canceledMove.fromLocation
                    console.log('✅ Reverted token to original location:', { 
                      tokenId: currentTokenId, 
                      newLocation: canceledMove.fromLocation 
                    })
                  }
                }
                
                // Перерисовываем жетоны
                this._renderTaskTokens(this.gamedatas.players)
                this._updateTaskMoveModeUI()
                
                // Скрываем кнопку подтверждения, если не все ходы использованы
                if (currentPendingMoves.usedMoves < currentPendingMoves.moveCount) {
                  this._hideTaskMovesConfirmButton()
                }
                
                // ВАЖНО: Очищаем подсветку колонок, чтобы можно было выбрать другой жетон
                this._clearColumnHighlight()
                console.log('✅ Cleared column highlight after canceling move - token remains inactive')
              }
              
              // НЕ активируем жетон после отмены перемещения - он остается неактивным
              // Это позволяет выбрать другой жетон для перемещения
              return // Выходим, не обрабатываем дальше
            }
            
            // Если жетон неактивен, но НЕ имеет перемещения - активируем его
            // Это может произойти после отмены перемещения, когда жетон остался неактивным
            if (!currentlyHasMove && token.classList.contains('task-token--inactive')) {
              console.log('🔄 Activating inactive token (no move) on click:', { tokenId: currentTokenId })
              token.classList.remove('task-token--inactive')
              token.style.cursor = 'pointer'
              // Продолжаем обработку - вызовем _handleTaskTokenClick ниже
            }
            
            // Если жетон активен, но имеет перемещение - это не должно происходить, но на всякий случай проверяем
            if (currentlyHasMove && !token.classList.contains('task-token--inactive')) {
              console.warn('⚠️ Active token has existing move - this should not happen:', { tokenId: currentTokenId })
            }
            
            // Обычный клик на активный жетон
            this._handleTaskTokenClick(token, tokenData)
          }
          token.addEventListener('click', clickHandler)
          token._clickHandler = clickHandler // Сохраняем для удаления
          
          // В режиме move_task добавляем специальный класс
          if (isMoveMode) {
            const tokenColor = tokenData?.color
            if (pendingMoves.moveColor === 'any' || tokenColor === pendingMoves.moveColor) {
              token.classList.add('task-token--move-mode')
            }
          }
        }

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

    /**
     * Получает позицию жетона в техотделе для указанного цвета
     * @param {string} color Цвет задачи (pink, orange, cyan, purple)
     * @returns {number} Позиция жетона (количество блоков для перемещения)
     */
    _getTechDepartmentPosition: function (color) {
      // Маппинг цветов на колонки техотдела
      const colorToColumn = {
        'pink': 1,
        'orange': 2,
        'cyan': 3, // blue в HTML
        'purple': 4
      }

      const columnNum = colorToColumn[color]
      if (!columnNum) {
        console.warn('Unknown color for tech department:', color)
        return 0
      }

      // Ищем жетон в колонке техотдела
      const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
      if (!column) {
        console.warn('Tech department column not found:', columnNum)
        return 0
      }

      // Ищем жетон (токен) в колонке
      const token = column.querySelector('.player-department-technical-development__token')
      if (!token) {
        console.warn('Tech department token not found in column:', columnNum)
        return 0
      }

      // Получаем родительскую строку жетона
      const row = token.closest('.player-department-technical-development__row')
      if (!row) {
        console.warn('Tech department row not found for token')
        return 0
      }

      // Получаем индекс строки из data-row-index
      const rowIndex = parseInt(row.dataset.rowIndex || '0', 10)
      
      // Для колонок 1 и 3: позиция = rowIndex (1-5), где 1 = нижняя позиция
      // Для колонок 2 и 4: позиция = rowIndex (0-5), где 0 = нижняя позиция
      // Возвращаем количество блоков для перемещения (позиция жетона)
      if (columnNum === 1 || columnNum === 3) {
        // Для колонок 1 и 3: rowIndex от 1 до 5, где 1 = нижняя позиция (0 блоков), 5 = верхняя (4 блока)
        return rowIndex // Позиция = количество блоков
      } else {
        // Для колонок 2 и 4: rowIndex от 0 до 5, где 0 = нижняя позиция (0 блоков), 5 = верхняя (5 блоков)
        return rowIndex // Позиция = количество блоков
      }
    },

    /**
     * Обрабатывает клик на жетон задачи в backlog
     * @param {HTMLElement} tokenElement Элемент жетона
     * @param {Object} tokenData Данные жетона
     */
    _handleTaskTokenClick: function (tokenElement, tokenData) {
      const color = tokenData?.color
      const tokenId = tokenData?.token_id
      
      // ВАЖНО: Получаем актуальную локацию из gamedatas, а не из tokenData
      // tokenData может содержать устаревшую информацию
      let location = tokenData?.location || 'backlog'
      const currentPlayer = this.gamedatas.players[this.player_id]
      if (currentPlayer && currentPlayer.taskTokens) {
        const token = currentPlayer.taskTokens.find(t => t.token_id == tokenId)
        if (token && token.location) {
          location = token.location
          console.log('✅ Using actual location from gamedatas:', { tokenId, location, tokenDataLocation: tokenData?.location })
        }
      }

      if (!color || !tokenId) {
        console.error('Invalid token data:', tokenData)
        return
      }

      // Проверяем, находимся ли мы в режиме перемещения задач (эффект move_task)
      const pendingMoves = this.gamedatas?.pendingTaskMoves
      
      console.log('🔍🔍🔍 _handleTaskTokenClick - Checking mode:', {
        hasPendingMoves: !!pendingMoves,
        pendingMoves: pendingMoves,
        fromEffect: pendingMoves?.fromEffect,
        fromEffectType: typeof pendingMoves?.fromEffect,
        fromEffectStrict: pendingMoves?.fromEffect === true,
        moveSource: pendingMoves?.moveSource,
        moveSourceValue: pendingMoves?.moveSource === 'founder_effect',
        moveCount: pendingMoves?.moveCount,
        usedMoves: pendingMoves?.usedMoves,
        availableMoves: pendingMoves ? (pendingMoves.moveCount - pendingMoves.usedMoves) : null,
        color,
        tokenId,
        location,
        gamedatasKeys: Object.keys(this.gamedatas || {}),
        pendingTaskMovesJson: this.gamedatas?.pendingTaskMovesJson,
        fullGamedatasPendingTaskMoves: JSON.stringify(this.gamedatas?.pendingTaskMoves, null, 2)
      })
      
      console.log('🔍🔍🔍 _handleTaskTokenClick - STEP 1: Checking if pendingMoves exists')
      console.log('  → pendingMoves:', pendingMoves)
      console.log('  → !!pendingMoves:', !!pendingMoves)
      
      if (pendingMoves) {
        console.log('✅✅✅ _handleTaskTokenClick - STEP 2: pendingMoves EXISTS, entering effect mode branch')
        
        // ВАЖНО: Проверяем, есть ли уже перемещение для этого жетона
        // Если есть - подсвечиваем все доступные колонки (включая бэклог и все промежуточные)
        const tokenIdNum = parseInt(tokenId, 10)
        const existingMoveIndex = pendingMoves.moves.findIndex(m => {
          const moveTokenId = parseInt(m.tokenId, 10)
          return moveTokenId === tokenIdNum || m.tokenId == tokenId || m.tokenId === tokenId
        })
        
        if (existingMoveIndex !== -1) {
          // Найдено существующее перемещение - активируем жетон и подсвечиваем все доступные колонки
          const existingMove = pendingMoves.moves[existingMoveIndex]
          console.log('🔄🔄🔄 Found existing move for token, activating and allowing re-move:', {
            tokenId: tokenId,
            existingMove: existingMove,
            currentLocation: location,
            fromLocation: existingMove.fromLocation
          })
          
          // Активируем жетон визуально (убираем неактивное состояние)
          const tokenElement = document.querySelector(`[data-token-id="${tokenId}"]`)
          if (tokenElement) {
            tokenElement.classList.remove('task-token--inactive')
            tokenElement.style.cursor = 'pointer'
            console.log('✅ Token activated visually:', { tokenId })
          }
          
          // Подсвечиваем все доступные колонки, включая бэклог и все промежуточные
          // Используем исходную локацию для подсчета доступных колонок
          const availableMoves = pendingMoves.moveCount - pendingMoves.usedMoves + existingMove.blocks
          const maxBlocks = isEffectMode ? availableMoves : Math.min(this._getTechDepartmentPosition(color), availableMoves)
          
          console.log('🔄 Re-highlighting columns for moved token:', {
            tokenId: tokenId,
            currentLocation: location,
            fromLocation: existingMove.fromLocation,
            availableMoves: availableMoves,
            maxBlocks: maxBlocks
          })
          
          // Подсвечиваем колонки, начиная с исходной локации (включая бэклог и все промежуточные)
          this._highlightAvailableColumns(maxBlocks, color, tokenId, existingMove.fromLocation, pendingMoves)
          
          // Выходим из функции - подсветка выполнена
          return
        }
        
        // Режим перемещения задач от эффекта карты
        // Проверяем цвет, если указан
        if (pendingMoves.moveColor !== 'any' && color !== pendingMoves.moveColor) {
          this.showMessage(_('Можно перемещать только задачи указанного цвета'), 'error')
          return
        }

        // Если это эффект карты (fromEffect = true), НЕ учитываем позицию в техотделе
        // Используем только move_count из эффекта
        const availableMoves = pendingMoves.moveCount - pendingMoves.usedMoves

        if (availableMoves === 0) {
          this.showMessage(_('Нет доступных ходов для перемещения'), 'error')
          return
        }

        // В режиме эффекта карты можно переместить на любое количество блоков до availableMoves
        // НЕ учитываем позицию в техотделе - используем только availableMoves
        // Проверяем и fromEffect, и moveSource для надежности
        console.log('🔍🔍🔍 _handleTaskTokenClick - STEP 3: Checking isEffectMode condition')
        console.log('  → pendingMoves.fromEffect:', pendingMoves.fromEffect, '(type:', typeof pendingMoves.fromEffect, ')')
        console.log('  → pendingMoves.fromEffect === true:', pendingMoves.fromEffect === true)
        console.log('  → pendingMoves.moveSource:', pendingMoves.moveSource)
        console.log('  → pendingMoves.moveSource === "founder_effect":', pendingMoves.moveSource === 'founder_effect')
        
        const check1 = pendingMoves.fromEffect === true
        const check2 = pendingMoves.moveSource === 'founder_effect'
        const isEffectMode = check1 || check2
        
        console.log('  → check1 (fromEffect === true):', check1)
        console.log('  → check2 (moveSource === "founder_effect"):', check2)
        console.log('  → isEffectMode (check1 || check2):', isEffectMode)
        
        const techDeptPos = this._getTechDepartmentPosition(color)
        const maxBlocks = isEffectMode ? availableMoves : Math.min(techDeptPos, availableMoves)
        
        console.log('🔍🔍🔍 _handleTaskTokenClick - STEP 4: Calculating maxBlocks')
        console.log('  → availableMoves:', availableMoves)
        console.log('  → techDeptPosition:', techDeptPos)
        console.log('  → isEffectMode:', isEffectMode)
        console.log('  → maxBlocks calculation:', isEffectMode ? `availableMoves (${availableMoves})` : `Math.min(techDeptPos (${techDeptPos}), availableMoves (${availableMoves}))`)
        console.log('  → FINAL maxBlocks:', maxBlocks)
        
        console.log('🔍🔍🔍 _handleTaskTokenClick - Mode check SUMMARY:', {
          fromEffect: pendingMoves.fromEffect,
          fromEffectType: typeof pendingMoves.fromEffect,
          fromEffectStrict: pendingMoves.fromEffect === true,
          moveSource: pendingMoves.moveSource,
          moveSourceStrict: pendingMoves.moveSource === 'founder_effect',
          check1: check1,
          check2: check2,
          isEffectMode: isEffectMode,
          availableMoves: availableMoves,
          techDeptPosition: techDeptPos,
          maxBlocks: maxBlocks
        })

        if (maxBlocks === 0) {
          this.showMessage(_('Нет доступных ходов для перемещения'), 'error')
          return
        }

        console.log('✅ Task token clicked (move mode from effect):', { 
          color, 
          tokenId, 
          maxBlocks, 
          availableMoves, 
          fromEffect: pendingMoves.fromEffect,
          location,
          moveCount: pendingMoves.moveCount,
          usedMoves: pendingMoves.usedMoves,
          pendingMoves: pendingMoves
        })

        // Подсвечиваем доступные колонки с учетом доступных ходов (без ограничения техотделом)
        // В режиме эффекта карты maxBlocks = availableMoves, поэтому подсветятся все колонки в пределах доступных ходов
        // ВАЖНО: Передаем pendingMoves, чтобы функция знала, что это режим эффекта карты
        this._highlightAvailableColumns(maxBlocks, color, tokenId, location, pendingMoves)
      } else {
        console.log('❌❌❌ _handleTaskTokenClick - STEP 2: pendingMoves DOES NOT EXIST, entering normal mode branch')
        console.log('  → This means: NO effect mode, using tech department logic')
        
        // Обычный режим (фаза Спринт) - используем стандартную логику
        // Получаем максимальное количество блоков для перемещения из техотдела
        const maxBlocks = this._getTechDepartmentPosition(color)
        
        console.log('🔍🔍🔍 _handleTaskTokenClick - Normal mode calculation:')
        console.log('  → techDeptPosition:', maxBlocks)
        
        if (maxBlocks === 0) {
          this.showMessage(_('Невозможно переместить задачу: позиция в техотделе не найдена'), 'error')
          return
        }

        console.log('Task token clicked (normal mode - Sprint phase):', { color, tokenId, maxBlocks, location })

        // В обычном режиме создаем pendingMoves с moveSource = 'sprint_phase'
        // чтобы функция _highlightAvailableColumns знала, что это обычный режим
        const sprintPhaseMoves = {
          moveCount: maxBlocks,
          moveColor: 'any',
          usedMoves: 0,
          moves: [],
          fromEffect: false, // НЕ эффект карты
          moveSource: 'sprint_phase' // Источник: фаза Спринт
        }

        // Подсвечиваем доступные колонки с информацией о режиме
        this._highlightAvailableColumns(maxBlocks, color, tokenId, location, sprintPhaseMoves)
      }
    },

    /**
     * Подсвечивает доступные колонки для перемещения жетона
     * @param {number} maxBlocks Максимальное количество блоков для перемещения
     * @param {string} color Цвет задачи
     * @param {number} tokenId ID жетона
     * @param {string} fromLocation Текущая локация жетона
     * @param {Object} pendingMoves Данные о ожидающих перемещениях (для режима эффекта карты)
     */
    _highlightAvailableColumns: function (maxBlocks, color, tokenId, fromLocation = 'backlog', pendingMoves = null) {
      console.log('🎯🎯🎯 _highlightAvailableColumns CALLED:', {
        maxBlocks: maxBlocks,
        color: color,
        tokenId: tokenId,
        fromLocation: fromLocation,
        pendingMoves: pendingMoves
      })
      
      // Убираем предыдущую подсветку
      this._clearColumnHighlight()

      // Колонки в порядке: backlog -> in-progress -> testing -> completed
      const locationOrder = ['backlog', 'in-progress', 'testing', 'completed']
      const fromIndex = locationOrder.indexOf(fromLocation)
      
      console.log('🔍 Location check:', {
        fromLocation: fromLocation,
        fromIndex: fromIndex,
        locationOrder: locationOrder
      })
      
      if (fromIndex === -1) {
        console.error('Invalid fromLocation:', fromLocation)
        return
      }
      
      // Проверяем, есть ли уже перемещение для этого жетона (для возможности возврата в бэклог)
      const tokenIdNum = parseInt(tokenId, 10)
      const hasExistingMove = pendingMoves && pendingMoves.moves && pendingMoves.moves.some(m => {
        const moveTokenId = parseInt(m.tokenId, 10)
        return moveTokenId === tokenIdNum || m.tokenId == tokenId || m.tokenId === tokenId
      })
      
      console.log('🔍 Existing move check:', {
        tokenId: tokenId,
        tokenIdNum: tokenIdNum,
        hasExistingMove: hasExistingMove,
        moves: pendingMoves?.moves
      })
      
      // ВАЖНО: Добавляем бэклог в список колонок для возможности возврата
      const columns = [
        { id: 'sprint-column-backlog', location: 'backlog' },
        { id: 'sprint-column-in-progress', location: 'in-progress' },
        { id: 'sprint-column-testing', location: 'testing' },
        { id: 'sprint-column-completed', location: 'completed' }
      ]
      

      // Проверяем, находимся ли мы в режиме перемещения от эффекта карты
      // Если pendingMoves не передан, берем из gamedatas
      if (!pendingMoves) {
        pendingMoves = this.gamedatas?.pendingTaskMoves
      }
      
      // Определяем режим: эффект карты (fromEffect=true) или фаза Спринт (moveSource='sprint_phase')
      console.log('🔍🔍🔍 _highlightAvailableColumns - STEP 1: Determining mode')
      console.log('  → pendingMoves:', pendingMoves)
      console.log('  → pendingMoves?.fromEffect:', pendingMoves?.fromEffect, '(type:', typeof pendingMoves?.fromEffect, ')')
      console.log('  → pendingMoves?.fromEffect === true:', pendingMoves?.fromEffect === true)
      console.log('  → pendingMoves?.moveSource:', pendingMoves?.moveSource)
      console.log('  → pendingMoves?.moveSource === "founder_effect":', pendingMoves?.moveSource === 'founder_effect')
      
      let check1 = pendingMoves && pendingMoves.fromEffect === true
      let check2 = pendingMoves && pendingMoves.moveSource === 'founder_effect'
      let isEffectMode = check1 || check2
      const isSprintPhase = pendingMoves && pendingMoves.moveSource === 'sprint_phase'
      
      console.log('  → check1 (fromEffect === true):', check1)
      console.log('  → check2 (moveSource === "founder_effect"):', check2)
      console.log('  → isEffectMode (check1 || check2):', isEffectMode)
      console.log('  → isSprintPhase:', isSprintPhase)
      
      // Если pendingMoves существует, но fromEffect и moveSource не установлены, 
      // но есть moveCount > 0, то это скорее всего эффект карты
      if (pendingMoves && !isEffectMode && !isSprintPhase && pendingMoves.moveCount > 0) {
        console.warn('⚠️⚠️⚠️ _highlightAvailableColumns - pendingMoves exists but fromEffect/moveSource not set! Assuming founder_effect mode.')
        console.warn('  → BEFORE fix: fromEffect=', pendingMoves.fromEffect, ', moveSource=', pendingMoves.moveSource)
        pendingMoves.fromEffect = true
        pendingMoves.moveSource = 'founder_effect'
        // Обновляем в gamedatas тоже
        if (this.gamedatas.pendingTaskMoves) {
          this.gamedatas.pendingTaskMoves.fromEffect = true
          this.gamedatas.pendingTaskMoves.moveSource = 'founder_effect'
        }
        // Пересчитываем isEffectMode после обновления
        isEffectMode = true
        console.warn('  → AFTER fix: fromEffect=', pendingMoves.fromEffect, ', moveSource=', pendingMoves.moveSource, ', isEffectMode=', isEffectMode)
      }
      
      console.log('🎯🎯🎯 _highlightAvailableColumns:', {
        maxBlocks,
        fromLocation,
        isEffectMode,
        isSprintPhase,
        pendingMoves: pendingMoves,
        fromEffect: pendingMoves?.fromEffect,
        moveSource: pendingMoves?.moveSource,
        availableMoves: isEffectMode ? (pendingMoves.moveCount - pendingMoves.usedMoves) : null,
        moveCount: pendingMoves?.moveCount,
        usedMoves: pendingMoves?.usedMoves,
        techDeptPosition: isEffectMode ? 'IGNORED (founder effect)' : (this._getTechDepartmentPosition ? this._getTechDepartmentPosition(color || '') : 'N/A')
      })

      // Подсвечиваем доступные колонки
      console.log('🔍🔍🔍 Starting to highlight columns:', {
        columnsCount: columns.length,
        columns: columns.map(c => ({ id: c.id, location: c.location })),
        fromLocation: fromLocation,
        fromIndex: fromIndex,
        hasBacklog: columns.some(c => c.location === 'backlog')
      })
      
      columns.forEach((col) => {
        const toIndex = locationOrder.indexOf(col.location)
        if (toIndex === -1) {
          console.log(`⚠️ Column ${col.location} not found in locationOrder`)
          return
        }
        
        const blocksNeeded = toIndex - fromIndex
        
        // ВАЖНО: Разрешаем перемещение в любую колонку (включая назад в бэклог и промежуточные)
        // Если перемещаем назад (blocksNeeded < 0) - это возврат, вычитаем ходы
        // Если перемещаем вперед (blocksNeeded > 0) - это обычное перемещение
        const canMoveBackward = blocksNeeded < 0 // Перемещение назад (в бэклог или промежуточные колонки)
        const canMoveForward = blocksNeeded > 0 && blocksNeeded <= maxBlocks // Перемещение вперед
        
        if (canMoveBackward || canMoveForward) {
          // Дополнительная проверка для режима эффекта карты (только для перемещения вперед)
          if (isEffectMode && canMoveForward) {
            // В режиме эффекта карты проверяем, что у нас достаточно доступных ходов
            // ИГНОРИРУЕМ техотдел - используем только moveCount из эффекта
            const availableMoves = pendingMoves.moveCount - pendingMoves.usedMoves
            console.log(`🔍 Column ${col.location}: blocksNeeded=${blocksNeeded}, availableMoves=${availableMoves}, maxBlocks=${maxBlocks}`)
            if (blocksNeeded > availableMoves) {
              console.log(`⏭️ Skipping column ${col.location}: blocksNeeded (${blocksNeeded}) > availableMoves (${availableMoves})`)
              return // Пропускаем, если не хватает ходов
            }
            console.log(`✅ Will highlight column ${col.location} (founder effect mode, ignoring tech dept)`)
          } else if (canMoveBackward) {
            console.log(`✅ Will highlight column ${col.location} (moving backward, blocksNeeded=${blocksNeeded})`)
          }
        } else if (isSprintPhase) {
          // В режиме фазы Спринт используем ограничение из техотдела (maxBlocks уже установлен)
          console.log(`✅ Will highlight column ${col.location} (sprint phase mode, maxBlocks=${maxBlocks} from tech dept)`)
        } else {
          // Обычный режим (без pendingMoves) - используем maxBlocks
          console.log(`✅ Will highlight column ${col.location} (normal mode, maxBlocks=${maxBlocks})`)
        }
        
        const columnElement = document.getElementById(col.id)
        if (columnElement) {
          console.log(`✅ Found column element: ${col.id}`, { 
              element: columnElement, 
              id: columnElement.id,
              classes: columnElement.className,
              hasHandler: !!columnElement._taskMoveHandler
            })
            
            columnElement.classList.add('sprint-column--available')
            columnElement.dataset.targetLocation = col.location
            columnElement.dataset.targetTokenId = tokenId
            columnElement.dataset.targetColor = color
            columnElement.dataset.blocksNeeded = blocksNeeded // Сохраняем количество блоков
            columnElement.style.cursor = 'pointer'
            columnElement.style.pointerEvents = 'auto' // Убеждаемся, что элемент кликабелен
            
            // Добавляем обработчик клика на колонку
            // ВАЖНО: Удаляем предыдущий обработчик, если есть
            if (columnElement._taskMoveHandler) {
              console.log(`⚠️ Removing old handler from column ${col.id}`)
              columnElement.removeEventListener('click', columnElement._taskMoveHandler, true)
            }
            
            const clickHandler = (e) => {
              console.log('🎯🎯🎯 Column click handler FIRED!', { 
                columnId: col.id, 
                location: col.location, 
                tokenId, 
                color,
                target: e.target,
                currentTarget: e.currentTarget,
                eventPhase: e.eventPhase,
                bubbles: e.bubbles,
                cancelable: e.cancelable
              })
              
              // ВАЖНО: Проверяем, активен ли жетон перед обработкой клика на колонку
              const currentPendingMoves = this.gamedatas?.pendingTaskMoves
              const currentTokenIdNum = parseInt(tokenId, 10)
              const currentlyHasMove = currentPendingMoves && currentPendingMoves.moves && currentPendingMoves.moves.some(m => {
                const moveTokenId = parseInt(m.tokenId, 10)
                return moveTokenId === currentTokenIdNum || m.tokenId == tokenId || m.tokenId === tokenId
              })
              
              // Находим элемент жетона в DOM
              const tokenElement = document.querySelector(`[data-token-id="${tokenId}"]`)
              const isTokenInactive = tokenElement && tokenElement.classList.contains('task-token--inactive')
              
              // Если жетон неактивен - игнорируем клик на колонку
              if (currentlyHasMove || isTokenInactive) {
                console.log('🔒 Ignoring column click - token is inactive:', { 
                  tokenId, 
                  currentlyHasMove, 
                  isTokenInactive,
                  tokenElement: !!tokenElement
                })
                e.stopPropagation()
                e.stopImmediatePropagation()
                e.preventDefault()
                return // Выходим, не обрабатываем клик
              }
              
              // Останавливаем распространение события, чтобы обработчик отмены не сработал
              e.stopPropagation()
              e.stopImmediatePropagation()
              e.preventDefault()
              
              console.log('🎯🎯🎯 Column clicked:', { columnId: col.id, location: col.location, tokenId, color })
              
              // Удаляем обработчик отмены, если он есть
              if (this._columnClickCancelHandler) {
                console.log('✅ Removing cancel handler')
                document.removeEventListener('click', this._columnClickCancelHandler)
                this._columnClickCancelHandler = null
              }
              
              // Вызываем перемещение задачи
              console.log('🚀 Calling _moveTaskTokenToColumn...')
              this._moveTaskTokenToColumn(tokenId, col.location, color)
              console.log('✅ _moveTaskTokenToColumn called')
              
              // НЕ очищаем подсветку сразу - пусть она останется для возможных дополнительных перемещений
              // this._clearColumnHighlight()
            }
            
            // НЕ используем { once: true }, чтобы можно было перемещать несколько задач
            // Используем capture phase (true), чтобы обработчик сработал раньше других обработчиков
            // Это гарантирует, что клик на колонку обработается до обработчика отмены на document
            columnElement.addEventListener('click', clickHandler, true)
            columnElement._taskMoveHandler = clickHandler // Сохраняем для удаления
            
            console.log('✅✅✅ Added click handler to column:', { 
              columnId: col.id, 
              location: col.location, 
              tokenId, 
              color,
              element: columnElement,
              hasHandler: !!columnElement._taskMoveHandler,
              handlerType: typeof columnElement._taskMoveHandler
            })
          } else {
            console.error(`❌❌❌ Column element NOT FOUND: ${col.id}`)
          }
      })

      // Сохраняем информацию о текущем выборе
      this._currentTaskSelection = {
        tokenId: tokenId,
        color: color,
        maxBlocks: maxBlocks
      }

      // Добавляем обработчик клика вне области для отмены выбора
      // ВАЖНО: Сохраняем ссылку на обработчик, чтобы можно было удалить его при клике на колонку
      this._columnClickCancelHandler = (e) => {
        console.log('🔍🔍🔍 Cancel handler FIRED!', { 
          target: e.target, 
          targetId: e.target.id,
          targetClasses: e.target.className,
          targetTag: e.target.tagName,
          currentTarget: e.currentTarget
        })
        
        // КРИТИЧЕСКИ ВАЖНО: Проверяем, был ли клик на подсвеченной колонке
        // Проверяем по ID колонок напрямую - это самый надежный способ
        const target = e.target
        let isColumnClick = false
        
        // Проверяем все подсвеченные колонки по их ID
        const columnIds = ['sprint-column-in-progress', 'sprint-column-testing', 'sprint-column-completed']
        for (const colId of columnIds) {
          const col = document.getElementById(colId)
          if (col) {
            const hasAvailableClass = col.classList.contains('sprint-column--available')
            console.log(`🔍 Checking column ${colId}:`, { 
              exists: !!col, 
              hasAvailableClass: hasAvailableClass,
              targetIsColumn: target === col,
              columnContainsTarget: col.contains(target)
            })
            
            if (hasAvailableClass) {
              // Проверяем, является ли target самой колонкой или её дочерним элементом
              if (target === col || col.contains(target)) {
                isColumnClick = true
                console.log('✅✅✅ Cancel handler detected column click!', { colId, target: target, targetId: target.id })
                break
              }
            }
          } else {
            console.log(`⚠️ Column ${colId} not found in DOM`)
          }
        }
        
        // Также проверяем через closest для надежности
        if (!isColumnClick) {
          const clickedColumn = target.closest('.sprint-column--available')
          if (clickedColumn && clickedColumn.classList.contains('sprint-column--available')) {
            isColumnClick = true
            console.log('✅✅✅ Cancel handler detected column click via closest!', { target: target, clickedColumn: clickedColumn })
          }
        }
        
        const clickedToken = target.closest('.task-token--clickable')
        // ВАЖНО: Игнорируем неактивные жетоны - они не должны обрабатываться при клике на другой жетон
        const isInactiveToken = clickedToken && clickedToken.classList.contains('task-token--inactive')
        
        // Проверяем, является ли клик на активный жетон (не неактивный)
        const isActiveToken = clickedToken && !isInactiveToken
        
        // Если клик на активный жетон - всегда очищаем подсветку и обрабатываем клик
        // Это позволяет выбрать другой жетон после перемещения первого
        if (isActiveToken) {
          console.log('🔄 Click on active token, clearing highlight and processing click', { 
            clickedToken: !!clickedToken,
            clickedTokenId: clickedToken?.dataset?.tokenId,
            currentSelectionTokenId: this._currentTaskSelection?.tokenId,
            isColumnClick: isColumnClick
          })
          this._clearColumnHighlight()
          document.removeEventListener('click', this._columnClickCancelHandler)
          this._columnClickCancelHandler = null
          
          // Обрабатываем клик на активный жетон
          console.log('🔄 Processing click on active token')
          // Даем время на очистку, затем обрабатываем клик
          setTimeout(() => {
            clickedToken.click()
          }, 50)
          return // Выходим, не обрабатываем дальше
        }
        
        // Если клик вне области или на неактивный жетон - очищаем подсветку
        if (!isColumnClick && (!clickedToken || isInactiveToken)) {
          console.log('🔒 Click outside highlighted area or on inactive token, clearing highlight', { 
            clickedToken: !!clickedToken,
            isInactiveToken: isInactiveToken,
            isColumnClick: isColumnClick,
            target: target,
            targetId: target.id,
            targetClasses: target.className,
            targetTag: target.tagName
          })
          this._clearColumnHighlight()
          document.removeEventListener('click', this._columnClickCancelHandler)
          this._columnClickCancelHandler = null
        } else {
          console.log('✅ Click on highlighted area, keeping highlight (or column click detected)', { 
            isColumnClick: isColumnClick, 
            clickedToken: !!clickedToken,
            target: target,
            targetId: target.id
          })
          // Если это клик по колонке, НЕ очищаем подсветку - пусть обработчик на колонке обработает клик
          // НО: если обработчик на колонке не сработал, нужно дать ему время
          if (isColumnClick) {
            // Даем обработчику на колонке время сработать
            setTimeout(() => {
              // Если обработчик на колонке не сработал, он должен был вызвать _moveTaskTokenToColumn
              // Проверяем, не нужно ли очистить подсветку
              if (this._columnClickCancelHandler) {
                console.log('⚠️ Column click detected but column handler may not have fired, keeping highlight for now')
              }
            }, 100)
          }
        }
      }
      
      // Добавляем обработчик с задержкой и БЕЗ capture phase, чтобы он сработал ПОСЛЕ обработчика на колонке
      // Используем requestAnimationFrame + setTimeout для гарантии, что обработчик сработает после обработчика на колонке
      requestAnimationFrame(() => {
        setTimeout(() => {
          if (this._columnClickCancelHandler) {
            // Используем capture: false, чтобы обработчик сработал ПОСЛЕ обработчика на колонке (который использует capture: true)
            document.addEventListener('click', this._columnClickCancelHandler, { once: true, capture: false })
            console.log('✅ Added cancel handler to document (will fire AFTER column click handler, delay: 300ms)')
          }
        }, 300) // Увеличиваем задержку еще больше
      })
    },

    /**
     * Убирает подсветку с колонок
     */
    _clearColumnHighlight: function () {
      const columns = [
        'sprint-column-backlog',
        'sprint-column-in-progress',
        'sprint-column-testing',
        'sprint-column-completed'
      ]

      columns.forEach((colId) => {
        const column = document.getElementById(colId)
        if (column) {
          column.classList.remove('sprint-column--available')
          column.style.cursor = ''
          delete column.dataset.targetLocation
          delete column.dataset.targetTokenId
          delete column.dataset.targetColor
          
          // Удаляем обработчик клика, если есть
          if (column._taskMoveHandler) {
            column.removeEventListener('click', column._taskMoveHandler, true) // Удаляем с capture phase
            delete column._taskMoveHandler
          }
        }
      })
      
      // Также удаляем обработчик отмены, если есть
      if (this._columnClickCancelHandler) {
        document.removeEventListener('click', this._columnClickCancelHandler)
        this._columnClickCancelHandler = null
      }

      delete this._currentTaskSelection
      
      console.log('✅ Column highlight cleared, handlers removed')
    },

    /**
     * Перемещает жетон задачи в указанную колонку
     * @param {number} tokenId ID жетона
     * @param {string} newLocation Новая локация (in-progress, testing, completed)
     * @param {string} color Цвет задачи
     */
    _moveTaskTokenToColumn: function (tokenId, newLocation, color) {
      console.log('🎯🎯🎯 _moveTaskTokenToColumn called:', { tokenId, newLocation, color })

      // Проверяем, находимся ли мы в режиме перемещения задач (эффект move_task)
      const pendingMoves = this.gamedatas?.pendingTaskMoves
      
      console.log('🔍🔍🔍 _moveTaskTokenToColumn - Checking mode:', {
        hasPendingMoves: !!pendingMoves,
        pendingMoves: pendingMoves,
        color,
        tokenId,
        newLocation: newLocation,
        fromEffect: pendingMoves?.fromEffect,
        moveSource: pendingMoves?.moveSource,
        gamedatasPendingTaskMoves: this.gamedatas?.pendingTaskMoves
      })
      
      if (pendingMoves) {
        console.log('✅✅✅ _moveTaskTokenToColumn - In effect mode, processing move')
        
        // ВАЖНО: Проверяем, не было ли уже перемещение этой задачи
        // Если было, отменяем предыдущее перемещение и пересчитываем usedMoves
        let actualFromLocation = this._getTokenCurrentLocation(tokenId)
        
        // ВАЖНО: Преобразуем tokenId в число для корректного сравнения
        const tokenIdNum = parseInt(tokenId, 10)
        
        console.log('🔍🔍🔍 Checking for existing moves:', {
          tokenId: tokenId,
          tokenIdNum: tokenIdNum,
          tokenIdType: typeof tokenId,
          currentLocation: actualFromLocation,
          allMoves: pendingMoves.moves,
          movesCount: pendingMoves.moves.length,
          movesTokenIds: pendingMoves.moves.map(m => ({ 
            tokenId: m.tokenId, 
            type: typeof m.tokenId,
            tokenIdNum: parseInt(m.tokenId, 10)
          }))
        })
        
        // Ищем существующее перемещение, сравнивая как числа и как строки
        const existingMoveIndex = pendingMoves.moves.findIndex(m => {
          const moveTokenId = parseInt(m.tokenId, 10)
          const match = moveTokenId === tokenIdNum || m.tokenId == tokenId || m.tokenId === tokenId
          if (match) {
            console.log('✅ Found matching move:', { 
              moveTokenId: m.tokenId, 
              searchTokenId: tokenId,
              move: m 
            })
          }
          return match
        })
        
        if (existingMoveIndex !== -1) {
          const existingMove = pendingMoves.moves[existingMoveIndex]
          console.log('⚠️⚠️⚠️ Found existing move for this token, canceling it:', {
            existingMove: existingMove,
            existingMoveIndex: existingMoveIndex,
            oldUsedMoves: pendingMoves.usedMoves,
            currentLocation: actualFromLocation,
            existingMoveFromLocation: existingMove.fromLocation,
            existingMoveToLocation: existingMove.toLocation,
            newLocation: newLocation
          })
          
          // ВАЖНО: Используем исходную локацию из отмененного перемещения для нового расчета
          actualFromLocation = existingMove.fromLocation
          
          // Проверяем, пытается ли пользователь переместить жетон обратно в исходное положение (отмена без нового перемещения)
          // Отмена происходит, если пользователь кликает на колонку, где жетон был ДО перемещения
          const isCancelingMove = (newLocation === existingMove.fromLocation)
          
          // Также проверяем, не пытается ли пользователь переместить жетон в то же место, где он уже находится
          // ВАЖНО: Используем existingMove.toLocation, так как это место, куда жетон был перемещен
          const isMovingToSameLocation = (newLocation === existingMove.toLocation)
          
          const blocks = this._calculateBlocksBetween(actualFromLocation, newLocation)
          
          console.log('🔍🔍🔍 Checking if canceling move:', {
            newLocation: newLocation,
            existingMoveFromLocation: existingMove.fromLocation,
            existingMoveToLocation: existingMove.toLocation,
            isCancelingMove: isCancelingMove,
            isMovingToSameLocation: isMovingToSameLocation,
            blocks: blocks
          })
          
          // ВАЖНО: Сначала проверяем попытку переместить в то же место - игнорируем
          if (isMovingToSameLocation) {
            console.log('⚠️ User is trying to move token to the same location - ignoring')
            this.showMessage(_('Жетон уже находится в этой колонке'), 'info')
            return
          }
          
          // Если пользователь кликает на исходную колонку - это отмена
          if (isCancelingMove) {
            // Пользователь перемещает жетон обратно в исходное положение - просто отменяем перемещение
            console.log('✅✅✅ User is moving token back to original location - canceling move only')
            
            // Отменяем предыдущее перемещение - вычитаем использованные блоки
            const oldUsedMoves = pendingMoves.usedMoves
            pendingMoves.usedMoves -= existingMove.blocks
            
            console.log('✅✅✅ Canceled move blocks:', {
              oldUsedMoves: oldUsedMoves,
              blocksToSubtract: existingMove.blocks,
              newUsedMoves: pendingMoves.usedMoves
            })
            
            // Удаляем предыдущее перемещение из списка
            pendingMoves.moves.splice(existingMoveIndex, 1)
            
            // ВАЖНО: Обновляем gamedatas.pendingTaskMoves после отмены
            if (this.gamedatas.pendingTaskMoves) {
              // ВАЖНО: Создаем новый массив, чтобы гарантировать обновление ссылки
              this.gamedatas.pendingTaskMoves.moves = [...pendingMoves.moves]
              this.gamedatas.pendingTaskMoves.usedMoves = pendingMoves.usedMoves
              console.log('✅ Updated gamedatas.pendingTaskMoves after cancel (back to original):', {
                movesCount: this.gamedatas.pendingTaskMoves.moves.length,
                usedMoves: this.gamedatas.pendingTaskMoves.usedMoves,
                moves: this.gamedatas.pendingTaskMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation }))
              })
            } else {
              console.error('❌❌❌ CRITICAL: gamedatas.pendingTaskMoves is null/undefined after cancel!')
            }
            
            // Возвращаем жетон в исходное положение
            const currentPlayer = this.gamedatas.players[this.player_id]
            if (currentPlayer && currentPlayer.taskTokens) {
              const token = currentPlayer.taskTokens.find(t => t.token_id == tokenId)
              if (token) {
                const oldTokenLocation = token.location
                token.location = existingMove.fromLocation
                console.log('✅ Reverted token to original location:', { 
                  tokenId, 
                  oldLocation: oldTokenLocation,
                  newLocation: existingMove.fromLocation 
                })
              } else {
                console.warn('⚠️ Token not found in gamedatas for reversion:', tokenId)
              }
            }
            
            // Перерисовываем жетоны после отмены
            this._renderTaskTokens(this.gamedatas.players)
            
            // Обновляем UI после отмены
            this._updateTaskMoveModeUI()
            
            // ВАЖНО: Скрываем кнопку подтверждения, если не все ходы использованы
            if (pendingMoves.usedMoves < pendingMoves.moveCount) {
              this._hideTaskMovesConfirmButton()
              console.log('✅ Hidden confirm button after canceling move')
            } else {
              console.warn('⚠️ Current player or taskTokens not found for reversion')
            }
            
            // Перерисовываем жетоны
            this._renderTaskTokens(this.gamedatas.players)
            
            // Обновляем UI после отмены
            this._updateTaskMoveModeUI()
            
            // ВАЖНО: Скрываем кнопку подтверждения, если не все ходы использованы
            if (pendingMoves.usedMoves < pendingMoves.moveCount) {
              const confirmButton = document.getElementById('task-moves-confirm-button')
              if (confirmButton) {
                confirmButton.remove()
                console.log('✅ Hidden confirm button after canceling move')
              }
            }
            
            console.log('✅ After canceling previous move:', {
              usedMoves: pendingMoves.usedMoves,
              movesCount: pendingMoves.moves.length,
              availableMoves: pendingMoves.moveCount - pendingMoves.usedMoves,
              actualFromLocation: actualFromLocation,
              remainingMoves: pendingMoves.moves
            })
            
            // Выходим из функции, так как это была только отмена без нового перемещения
            return
          } else {
            // Пользователь перемещает жетон в другое место - отменяем старое и добавляем новое
            console.log('✅✅✅ User is moving token to different location - canceling old and adding new')
            
            // Отменяем предыдущее перемещение - вычитаем использованные блоки
            const oldUsedMoves = pendingMoves.usedMoves
            pendingMoves.usedMoves -= existingMove.blocks
            
            console.log('✅✅✅ Canceled move blocks:', {
              oldUsedMoves: oldUsedMoves,
              blocksToSubtract: existingMove.blocks,
              newUsedMoves: pendingMoves.usedMoves
            })
            
            // Удаляем предыдущее перемещение из списка
            pendingMoves.moves.splice(existingMoveIndex, 1)
            
            // ВАЖНО: Обновляем gamedatas.pendingTaskMoves после отмены
            if (this.gamedatas.pendingTaskMoves) {
              // ВАЖНО: Создаем новый массив, чтобы гарантировать обновление ссылки
              this.gamedatas.pendingTaskMoves.moves = [...pendingMoves.moves]
              this.gamedatas.pendingTaskMoves.usedMoves = pendingMoves.usedMoves
              console.log('✅ Updated gamedatas.pendingTaskMoves after cancel:', {
                movesCount: this.gamedatas.pendingTaskMoves.moves.length,
                usedMoves: this.gamedatas.pendingTaskMoves.usedMoves,
                moves: this.gamedatas.pendingTaskMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation }))
              })
            } else {
              console.error('❌❌❌ CRITICAL: gamedatas.pendingTaskMoves is null/undefined after cancel!')
            }
            
            // Возвращаем жетон в исходное положение (временно, перед новым перемещением)
            const currentPlayer = this.gamedatas.players[this.player_id]
            if (currentPlayer && currentPlayer.taskTokens) {
              const token = currentPlayer.taskTokens.find(t => t.token_id == tokenId)
              if (token) {
                token.location = existingMove.fromLocation
                console.log('✅ Reverted token to original location before new move:', { 
                  tokenId, 
                  newLocation: existingMove.fromLocation 
                })
              }
            }
            
            // Перерисовываем жетоны перед новым перемещением
            this._renderTaskTokens(this.gamedatas.players)
            
            // Обновляем UI после отмены
            this._updateTaskMoveModeUI()
            
            // ВАЖНО: Скрываем кнопку подтверждения, если не все ходы использованы
            if (pendingMoves.usedMoves < pendingMoves.moveCount) {
              const confirmButton = document.getElementById('task-moves-confirm-button')
              if (confirmButton) {
                confirmButton.remove()
                console.log('✅ Hidden confirm button after canceling move')
              }
            }
            
            console.log('✅ After canceling previous move (before new move):', {
              usedMoves: pendingMoves.usedMoves,
              movesCount: pendingMoves.moves.length,
              availableMoves: pendingMoves.moveCount - pendingMoves.usedMoves,
              actualFromLocation: actualFromLocation,
              remainingMoves: pendingMoves.moves
            })
            
            // Продолжаем выполнение для добавления нового перемещения
          }
        } else {
          console.log('✅ No existing move found for this token, proceeding with new move')
          
          // Проверяем, не пытается ли пользователь переместить жетон в то же место, где он уже находится
          const currentTokenLocation = this._getTokenCurrentLocation(tokenId)
          if (newLocation === currentTokenLocation) {
            console.log('⚠️ User is trying to move token to the same location - ignoring')
            this.showMessage(_('Жетон уже находится в этой колонке'), 'info')
            return
          }
        }
        
        // Режим перемещения задач - добавляем в список перемещений
        // Используем actualFromLocation (может быть исходной, если было отменено предыдущее перемещение)
        const blocks = this._calculateBlocksBetween(actualFromLocation, newLocation)
        
        // ВАЖНО: Если перемещаем назад (blocks < 0), это возврат - вычитаем ходы
        // Если перемещаем вперед (blocks > 0), это обычное перемещение - добавляем ходы
        const isMovingBackward = blocks < 0
        const blocksToUse = Math.abs(blocks) // Используем абсолютное значение для подсчета
        
        console.log('🔍🔍🔍 _moveTaskTokenToColumn - Move calculation:', {
          fromLocation: actualFromLocation,
          toLocation: newLocation,
          blocks: blocks,
          blocksToUse: blocksToUse,
          isMovingBackward: isMovingBackward,
          usedMoves: pendingMoves.usedMoves,
          moveCount: pendingMoves.moveCount,
          availableMoves: pendingMoves.moveCount - pendingMoves.usedMoves,
          existingMoves: pendingMoves.moves
        })
        
        // Проверяем, достаточно ли ходов для нового перемещения (только для перемещения вперед)
        if (!isMovingBackward && pendingMoves.usedMoves + blocksToUse > pendingMoves.moveCount) {
          console.warn('❌ Not enough moves:', {
            used: pendingMoves.usedMoves,
            needed: blocksToUse,
            total: pendingMoves.moveCount,
            available: pendingMoves.moveCount - pendingMoves.usedMoves
          })
          this.showMessage(_('Недостаточно ходов для перемещения'), 'error')
          return
        }
        
        // Проверяем цвет, если указан
        if (pendingMoves.moveColor !== 'any') {
          const tokenColor = this._getTokenColor(tokenId)
          if (tokenColor !== pendingMoves.moveColor) {
            console.warn('❌ Color mismatch:', { tokenColor, requiredColor: pendingMoves.moveColor })
            this.showMessage(_('Можно перемещать только задачи указанного цвета'), 'error')
            return
          }
        }
        
        // Добавляем новое перемещение в список
        // ВАЖНО: Сохраняем tokenId как строку для единообразия
        const normalizedTokenId = String(tokenId)
        pendingMoves.moves.push({
          tokenId: normalizedTokenId, // ВАЖНО: Сохраняем как строку для единообразия
          fromLocation: actualFromLocation, // Используем исходную локацию
          toLocation: newLocation,
          blocks: blocksToUse, // Сохраняем абсолютное значение
          color: color
        })
        console.log('✅ Added move to pendingMoves.moves:', {
          tokenId: normalizedTokenId,
          tokenIdType: typeof normalizedTokenId,
          fromLocation: actualFromLocation,
          toLocation: newLocation,
          blocks: blocksToUse,
          totalMoves: pendingMoves.moves.length
        })
        
        // ВАЖНО: Если перемещаем назад - вычитаем ходы, если вперед - добавляем
        if (isMovingBackward) {
          pendingMoves.usedMoves -= blocksToUse
          console.log('✅ Moving backward - subtracting moves:', {
            blocksToUse: blocksToUse,
            oldUsedMoves: pendingMoves.usedMoves + blocksToUse,
            newUsedMoves: pendingMoves.usedMoves
          })
        } else {
          pendingMoves.usedMoves += blocksToUse
          console.log('✅ Moving forward - adding moves:', {
            blocksToUse: blocksToUse,
            oldUsedMoves: pendingMoves.usedMoves - blocksToUse,
            newUsedMoves: pendingMoves.usedMoves
          })
        }
        
        // ВАЖНО: Обновляем gamedatas.pendingTaskMoves, чтобы при следующем рендеринге жетон был неактивным
        // pendingMoves - это ссылка на this.gamedatas.pendingTaskMoves, но на всякий случай обновляем явно
        if (this.gamedatas.pendingTaskMoves) {
          // ВАЖНО: Создаем новый массив, чтобы гарантировать обновление ссылки
          this.gamedatas.pendingTaskMoves.moves = [...pendingMoves.moves]
          this.gamedatas.pendingTaskMoves.usedMoves = pendingMoves.usedMoves
          console.log('✅ Updated gamedatas.pendingTaskMoves:', {
            movesCount: this.gamedatas.pendingTaskMoves.moves.length,
            usedMoves: this.gamedatas.pendingTaskMoves.usedMoves,
            moves: this.gamedatas.pendingTaskMoves.moves.map(m => ({ tokenId: m.tokenId, toLocation: m.toLocation }))
          })
        } else {
          console.error('❌❌❌ CRITICAL: gamedatas.pendingTaskMoves is null/undefined after move!')
        }
        
        console.log('✅✅✅ _moveTaskTokenToColumn - Move added:', {
          moves: pendingMoves.moves,
          movesCount: pendingMoves.moves.length,
          usedMoves: pendingMoves.usedMoves,
          moveCount: pendingMoves.moveCount,
          availableMoves: pendingMoves.moveCount - pendingMoves.usedMoves
        })
        
        // Обновляем UI (обновит счетчик)
        this._updateTaskMoveModeUI()
        console.log('✅ UI updated, counter should show:', `${pendingMoves.usedMoves} / ${pendingMoves.moveCount}`)
        
        // Временно перемещаем жетон визуально (обновляем данные)
        const currentPlayer = this.gamedatas.players[this.player_id]
        if (currentPlayer && currentPlayer.taskTokens) {
          const token = currentPlayer.taskTokens.find(t => t.token_id == tokenId)
          if (token) {
            console.log('✅ Updating token location in gamedatas:', { tokenId, oldLocation: token.location, newLocation })
            const oldLocation = token.location
            token.location = newLocation
            
            // Также обновляем в DOM сразу для мгновенной визуальной обратной связи
            const tokenElement = document.querySelector(`[data-token-id="${tokenId}"]`)
            if (tokenElement) {
              tokenElement.dataset.location = newLocation
              console.log('✅ Updated token location in DOM:', { tokenId, newLocation })
            } else {
              console.warn('⚠️ Token element not found in DOM:', tokenId)
            }
          } else {
            console.warn('⚠️ Token not found in gamedatas:', tokenId)
          }
        } else {
          console.warn('⚠️ Current player or taskTokens not found')
        }
        
        // Перерисовываем жетоны - это переместит жетон в правильную колонку и сделает его неактивным
        console.log('🔄 Rendering task tokens after move...')
        this._renderTaskTokens(this.gamedatas.players)
        console.log('✅ Task tokens rendered (moved token is now inactive)')
        
        // ВАЖНО: Очищаем подсветку колонок после перемещения, чтобы можно было выбрать другой жетон
        this._clearColumnHighlight()
        console.log('✅ Cleared column highlight after move - ready for next token selection')
        
        // Если все ходы использованы, показываем кнопку подтверждения
        if (pendingMoves.usedMoves >= pendingMoves.moveCount) {
          console.log('✅ All moves used, showing confirm button')
          this._showTaskMovesConfirmButton()
        } else {
          console.log('✅ Moves remaining:', pendingMoves.moveCount - pendingMoves.usedMoves)
        }
      } else {
        // Обычный режим - сразу отправляем на сервер
        this._updateTaskLocation(tokenId, newLocation, null, (result) => {
          if (result && result.success !== false) {
            console.log('✅ Task token moved successfully')
            // Обновляем отображение
            this._renderTaskTokens(this.gamedatas.players)
          } else {
            console.error('❌ Failed to move task token:', result)
            this.showMessage(_('Ошибка при перемещении задачи'), 'error')
          }
        })
      }
    },

    /**
     * Активирует режим перемещения задач (эффект move_task)
     * @param {number} moveCount Количество доступных ходов
     * @param {string} moveColor Цвет задач ('any' для любого цвета)
     */
    _activateTaskMoveMode: function (moveCount, moveColor) {
      console.log('🎯🎯🎯 _activateTaskMoveMode called:', { moveCount, moveColor, pendingTaskMoves: this.gamedatas?.pendingTaskMoves })
      
      // Убеждаемся, что pendingTaskMoves установлен правильно
      if (!this.gamedatas.pendingTaskMoves) {
        console.warn('⚠️⚠️⚠️ pendingTaskMoves is not set, creating it now')
        this.gamedatas.pendingTaskMoves = {
          moveCount: moveCount,
          moveColor: moveColor,
          usedMoves: 0,
          moves: [],
          fromEffect: true, // Флаг, что это эффект карты
          moveSource: 'founder_effect'
        }
        console.log('✅✅✅ Created pendingTaskMoves:', this.gamedatas.pendingTaskMoves)
      } else {
        // Обновляем флаг fromEffect, если он не установлен
        console.log('🔍 pendingTaskMoves exists, checking fromEffect:', this.gamedatas.pendingTaskMoves.fromEffect)
        if (!this.gamedatas.pendingTaskMoves.fromEffect) {
          console.log('⚠️⚠️⚠️ fromEffect is false, setting it to true')
          this.gamedatas.pendingTaskMoves.fromEffect = true
          this.gamedatas.pendingTaskMoves.moveSource = 'founder_effect'
        } else {
          console.log('✅✅✅ fromEffect is already true, moveSource:', this.gamedatas.pendingTaskMoves.moveSource)
        }
      }
      
      console.log('✅ pendingTaskMoves after activation:', this.gamedatas.pendingTaskMoves)
      
      // Делаем все жетоны во всех колонках кликабельными (кроме completed)
      const columns = [
        'sprint-column-backlog',
        'sprint-column-in-progress',
        'sprint-column-testing'
      ]
      
      columns.forEach((columnId) => {
        const column = document.getElementById(columnId)
        if (column) {
          const tokens = column.querySelectorAll('.task-token')
          tokens.forEach((token) => {
            const tokenColor = token.dataset.color
            
            // Проверяем цвет, если указан
            if (moveColor !== 'any' && tokenColor !== moveColor) {
              return // Пропускаем жетоны другого цвета
            }
            
            token.classList.add('task-token--move-mode')
            token.style.cursor = 'pointer'
            token.style.pointerEvents = 'auto'
          })
        }
      })
      
      // Показываем индикатор режима перемещения
      this._showTaskMoveModeIndicator(moveCount, moveColor)
      
      // Перерисовываем жетоны, чтобы применить изменения
      setTimeout(() => {
        this._renderTaskTokens(this.gamedatas.players)
      }, 100)
    },

    /**
     * Деактивирует режим перемещения задач
     */
    _activateTechnicalDevelopmentMoveMode: function (moveCount, founderName) {
      console.log('🔧🔧🔧 _activateTechnicalDevelopmentMoveMode called:', { moveCount, founderName })
      
      // Сохраняем данные о режиме выбора
      this.gamedatas.pendingTechnicalDevelopmentMoves = {
        moveCount: moveCount, // Общее количество очков для распределения (2)
        usedMoves: 0, // Использовано очков
        moves: [], // Массив перемещений: [{column: 1, amount: 1}, {column: 2, amount: 1}] или [{column: 1, amount: 2}]
        founderName: founderName,
        fromEffect: true,
        moveSource: 'founder_effect'
      }
      
      console.log('✅ pendingTechnicalDevelopmentMoves set:', this.gamedatas.pendingTechnicalDevelopmentMoves)
      
      // Делаем строки техотдела кликабельными (как в треке задач)
      // В техотделе 4 колонки: column-1, column-2, column-3, column-4
      const columns = [1, 2, 3, 4]
      let tokensFound = 0
      
      columns.forEach((columnNum) => {
        const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
        console.log('🔧 Looking for column:', columnNum, 'found:', !!column)
        
        if (column) {
          // Ищем все строки в колонке (могут быть в wrapper или напрямую)
          const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
          const container = wrapper || column
          const rows = container.querySelectorAll('.player-department-technical-development__row')
          
          console.log('🔧 Found rows in column', columnNum, ':', rows.length)
          
          // Находим текущую позицию жетона в этой колонке
          let currentTokenRowIndex = null
          rows.forEach((row) => {
            const token = row.querySelector('.player-department-technical-development__token')
            if (token) {
              tokensFound++
              currentTokenRowIndex = parseInt(row.dataset.rowIndex, 10)
              console.log('🔧 Found token in column', columnNum, 'row', currentTokenRowIndex)
              
              // НЕ подсвечиваем жетон сразу - только после клика на строку
            }
          })
          
          // Обновляем кликабельные строки для этой колонки (с учетом доступных очков)
          if (currentTokenRowIndex !== null) {
            // Используем функцию обновления, которая учитывает доступные очки
            this._updateClickableRowsForColumn(columnNum)
          }
        } else {
          console.warn('⚠️ Column not found:', columnNum)
        }
      })
      
      console.log('🔧 Total tokens found and activated:', tokensFound)
      
      if (tokensFound === 0) {
        console.error('❌ No tokens found in technical development columns!')
        // Не показываем ошибку, возможно жетоны появятся позже
        console.warn('⚠️ No tokens found, but continuing anyway')
      }
      
      // Показываем подсказку
      this.showMessage(_('Выберите колонки техотдела для улучшения (всего ${count} очков)').replace('${count}', moveCount), 'info')
      
      // Добавляем кнопку подтверждения
      this._addTechnicalDevelopmentConfirmButton()
      
      // Блокируем кнопку завершения хода
      this._updateFinishTurnButtonForTechnicalDevelopment()
    },

    _deactivateTechnicalDevelopmentMoveMode: function () {
      console.log('🔒 Deactivating technical development move mode')
      
      // Убираем классы с жетонов
      const tokens = document.querySelectorAll('.technical-development-token--move-mode')
      tokens.forEach((token) => {
        token.classList.remove('technical-development-token--move-mode')
      })
      
      // Убираем классы и обработчики со строк
      const rows = document.querySelectorAll('.technical-development-row--clickable')
      rows.forEach((row) => {
        row.classList.remove('technical-development-row--clickable')
        row.style.cursor = ''
        row.style.position = ''
        row.removeAttribute('data-clickable')
      })
      
      // Удаляем обработчики делегирования с контейнеров
      const columns = [1, 2, 3, 4]
      columns.forEach((columnNum) => {
        const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
        if (!column) return
        
        const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
        const container = wrapper || column
        
        if (container._technicalDevClickHandler) {
          container.removeEventListener('click', container._technicalDevClickHandler, true)
          container._technicalDevClickHandler = null
        }
      })
      
      // Убираем кнопку подтверждения
      const confirmButton = document.getElementById('technical-development-moves-confirm-button')
      if (confirmButton) {
        confirmButton.remove()
      }
      
      // Очищаем данные
      this.gamedatas.pendingTechnicalDevelopmentMoves = null
      
      // Разблокируем кнопку завершения хода
      this._updateFinishTurnButtonForTechnicalDevelopment()
    },

    _handleTechnicalDevelopmentRowClick: function (columnNum, fromRowIndex, toRowIndex) {
      console.log('🔧 Technical development row clicked:', { columnNum, fromRowIndex, toRowIndex })
      
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (!pendingMoves) {
        console.warn('⚠️ No pending technical development moves')
        return
      }
      
      // Находим текущую реальную позицию жетона (может измениться после предыдущих кликов)
      const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
      if (!column) {
        console.error('❌ Column not found:', columnNum)
        return
      }
      
      // Ищем строки в wrapper или напрямую в колонке
      const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
      const container = wrapper || column
      const rows = container.querySelectorAll('.player-department-technical-development__row')
      
      // Находим строку, где сейчас находится жетон
      let actualCurrentRowIndex = fromRowIndex
      rows.forEach(row => {
        const token = row.querySelector('.player-department-technical-development__token')
        if (token) {
          actualCurrentRowIndex = parseInt(row.dataset.rowIndex, 10)
        }
      })
      
      console.log('🔧 Actual current row index:', actualCurrentRowIndex, 'target row:', toRowIndex)
      
      // Проверяем, есть ли уже перемещение для этой колонки
      const existingMove = pendingMoves.moves.find(m => m.column === columnNum)
      
      // Если кликнули на исходную позицию (для отмены перемещения)
      if (existingMove && existingMove.amount > 0 && toRowIndex === existingMove.fromRowIndex) {
        // Возвращаем жетон в исходную позицию
        const originalRowIndex = existingMove.fromRowIndex
        const currentAmount = existingMove.amount
        
        console.log('🔧 Canceling move: returning token from row', actualCurrentRowIndex, 'to original row', originalRowIndex, 'amount:', -currentAmount)
        
        this._moveTechnicalDevelopmentToken(columnNum, actualCurrentRowIndex, originalRowIndex, -currentAmount)
        
        // Обновляем подсветку жетонов после отмены
        this._updateTechnicalDevelopmentTokenHighlights()
        
        // Обновляем кликабельные строки после отмены
        this._updateClickableRowsForColumn(columnNum)
        return
      }
      
      // Если кликнули на строку, где уже находится жетон или ниже (но не исходная), ничего не делаем
      if (toRowIndex <= actualCurrentRowIndex) {
        return
      }
      
      // Вычисляем количество позиций для перемещения
      const moveAmount = toRowIndex - actualCurrentRowIndex
      
      // Проверяем, достаточно ли очков
      const availableMoves = pendingMoves.moveCount - pendingMoves.usedMoves
      if (moveAmount > availableMoves) {
        this.showMessage(_('Недостаточно очков для перемещения на ${amount} позиций').replace('${amount}', moveAmount), 'error')
        return
      }
      
      // Если есть существующее перемещение, сначала отменяем его
      if (existingMove && existingMove.amount > 0) {
        const originalRowIndex = existingMove.fromRowIndex
        const currentAmount = existingMove.amount
        // Отменяем предыдущее перемещение
        this._moveTechnicalDevelopmentToken(columnNum, actualCurrentRowIndex, originalRowIndex, -currentAmount)
        // Обновляем actualCurrentRowIndex после отмены
        actualCurrentRowIndex = originalRowIndex
      }
      
      // Перемещаем жетон в выбранную строку
      this._moveTechnicalDevelopmentToken(columnNum, actualCurrentRowIndex, toRowIndex, moveAmount)
      
      // Обновляем подсветку жетонов после перемещения
      this._updateTechnicalDevelopmentTokenHighlights()
    },

    _moveTechnicalDevelopmentToken: function (columnNum, fromRowIndex, toRowIndex, amount) {
      console.log('🔧 Moving technical development token:', { columnNum, fromRowIndex, toRowIndex, amount })
      
      const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
      if (!column) {
        console.error('❌ Column not found:', columnNum)
        return
      }
      
      // Ищем строки в wrapper или напрямую в колонке
      const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
      const container = wrapper || column
      
      // ВАЖНО: Всегда находим реальную текущую позицию жетона, а не используем fromRowIndex
      // fromRowIndex используется только для сохранения исходной позиции в данных
      // Ищем ТОЛЬКО строки (не жетоны!)
      const rows = container.querySelectorAll('.player-department-technical-development__row')
      let currentRowIndex = null
      let currentRow = null
      let token = null
      
      console.log('🔧 Searching for token in', rows.length, 'rows')
      rows.forEach(row => {
        // Проверяем, что это действительно строка, а не жетон
        if (!row.classList.contains('player-department-technical-development__row')) {
          return
        }
        const rowToken = row.querySelector('.player-department-technical-development__token')
        if (rowToken) {
          currentRowIndex = parseInt(row.dataset.rowIndex, 10)
          currentRow = row
          token = rowToken
          console.log('🔧 Found token at row:', currentRowIndex)
        }
      })
      
      // Если жетон не найден, это ошибка - жетон должен быть на поле
      if (!currentRow || !token) {
        console.error('❌ Token not found in any row! This should not happen.')
        console.error('❌ Available rows:', Array.from(rows).map(r => ({ id: r.id, rowIndex: r.dataset.rowIndex, className: r.className })))
        return
      }
      
      // Находим целевую строку (ВАЖНО: ищем только строки, не жетоны!)
      let toRow = null
      // Сначала ищем по ID строки
      toRow = document.getElementById(`player-department-technical-development-column-${columnNum}-row-${toRowIndex}`)
      // Проверяем, что это действительно строка
      if (toRow && !toRow.classList.contains('player-department-technical-development__row')) {
        console.warn('⚠️ Element found by ID is not a row, searching again')
        toRow = null
      }
      
      // Если не нашли, ищем среди строк по data-row-index
      if (!toRow) {
        rows.forEach(row => {
          // Проверяем, что это действительно строка, а не жетон
          if (!row.classList.contains('player-department-technical-development__row')) {
            return
          }
          const rowIndex = parseInt(row.dataset.rowIndex, 10)
          if (rowIndex === toRowIndex) {
            toRow = row
          }
        })
      }
      
      // Финальная проверка: убеждаемся, что toRow - это действительно строка
      if (toRow && !toRow.classList.contains('player-department-technical-development__row')) {
        console.error('❌ Target element is not a row!', { toRow, className: toRow.className, id: toRow.id })
        toRow = null
      }
      
      if (!currentRow || !token) {
        console.error('❌ Current row or token not found:', { currentRowIndex, fromRowIndex, toRowIndex, columnNum })
        console.error('❌ Available rows:', Array.from(rows).map(r => ({ id: r.id, rowIndex: r.dataset.rowIndex, className: r.className })))
        return
      }
      
      if (!toRow) {
        console.error('❌ Target row not found:', { toRowIndex, columnNum })
        console.error('❌ Available rows:', Array.from(rows).map(r => ({ id: r.id, rowIndex: r.dataset.rowIndex, className: r.className })))
        return
      }
      
      // Финальная проверка: toRow должен быть строкой, а не жетоном
      if (!toRow.classList.contains('player-department-technical-development__row')) {
        console.error('❌ Target element is not a row! It is:', { 
          element: toRow, 
          className: toRow.className, 
          id: toRow.id,
          tagName: toRow.tagName 
        })
        return
      }
      
      // Проверяем, что мы не пытаемся переместить жетон в ту же строку
      if (currentRowIndex === toRowIndex) {
        console.warn('⚠️ Token is already at target row:', toRowIndex)
        return
      }
      
      // Проверяем, что toRow и currentRow - это разные элементы
      if (currentRow === toRow) {
        console.warn('⚠️ Current row and target row are the same element!', { currentRowIndex, toRowIndex })
        return
      }
      
      console.log('✅ Moving token from row', currentRowIndex, 'to row', toRowIndex)
      console.log('✅ Current row element:', currentRow.id || currentRow.className)
      console.log('✅ Target row element:', toRow.id || toRow.className)
      
      // Перемещаем жетон
      // Сначала удаляем из текущей строки
      try {
        if (token.parentNode) {
          token.parentNode.removeChild(token)
        } else {
          token.remove()
        }
        
        // Затем добавляем в целевую строку
        toRow.appendChild(token)
        console.log('✅ Token successfully moved')
      } catch (error) {
        console.error('❌ Error moving token:', error)
        console.error('❌ Token:', token)
        console.error('❌ Current row:', currentRow)
        console.error('❌ Target row:', toRow)
        return
      }
      
      // Обновляем данные о перемещении
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (pendingMoves) {
        // Ищем существующее перемещение для этой колонки
        const existingMove = pendingMoves.moves.find(m => m.column === columnNum)
        if (existingMove) {
          // Если перемещение уже есть, изменяем amount и обновляем toRowIndex
          existingMove.amount += amount
          
          // Если amount стал 0 или меньше, удаляем перемещение и возвращаем toRowIndex в исходную позицию
          if (existingMove.amount <= 0) {
            existingMove.toRowIndex = existingMove.fromRowIndex // Возвращаем в исходную позицию
            const index = pendingMoves.moves.indexOf(existingMove)
            pendingMoves.moves.splice(index, 1)
          } else {
            // Обновляем целевую позицию только если amount > 0
            existingMove.toRowIndex = toRowIndex
          }
        } else {
          // Создаем новое перемещение только если amount > 0
          if (amount > 0) {
            pendingMoves.moves.push({
              column: columnNum,
              fromRowIndex: fromRowIndex, // Исходная позиция
              toRowIndex: toRowIndex, // Текущая позиция после перемещения
              amount: amount
            })
          }
        }
        
        pendingMoves.usedMoves += amount
        
        // Проверяем, что usedMoves не стал отрицательным
        if (pendingMoves.usedMoves < 0) {
          pendingMoves.usedMoves = 0
        }
        
        console.log('✅ Token moved, pendingMoves:', pendingMoves)
        
        // Обновляем подсветку жетонов (только в колонках с активными перемещениями)
        this._updateTechnicalDevelopmentTokenHighlights()
        
        // Обновляем кликабельные строки для этой колонки
        this._updateClickableRowsForColumn(columnNum)
        
        // Обновляем кнопку подтверждения
        this._updateTechnicalDevelopmentConfirmButton()
        
        // Блокируем кнопку завершения, пока есть ожидающие перемещения
        this._updateFinishTurnButtonForTechnicalDevelopment()
      }
    },
    
    _updateTechnicalDevelopmentTokenHighlights: function () {
      // Убираем подсветку со всех жетонов
      const allTokens = document.querySelectorAll('.technical-development-token--move-mode')
      allTokens.forEach(token => {
        token.classList.remove('technical-development-token--move-mode')
      })
      
      // Подсвечиваем только жетоны в колонках с активными перемещениями
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (!pendingMoves) return
      
      pendingMoves.moves.forEach(move => {
        const columnNum = move.column
        const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
        if (!column) return
        
        const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
        const container = wrapper || column
        const rows = container.querySelectorAll('.player-department-technical-development__row')
        
        rows.forEach(row => {
          const token = row.querySelector('.player-department-technical-development__token')
          if (token) {
            token.classList.add('technical-development-token--move-mode')
          }
        })
      })
    },
    
    _updateActiveTechnicalDevelopmentTokenHighlight: function (token) {
      token.classList.add('technical-development-token--move-mode')
    },
    
    _removeActiveTechnicalDevelopmentTokenHighlight: function (token) {
      token.classList.remove('technical-development-token--move-mode')
    },
    
    _updateClickableRowsForColumn: function (columnNum) {
      // Проверяем, что режим перемещения активирован
      if (!this.gamedatas.pendingTechnicalDevelopmentMoves) {
        return
      }
      
      const column = document.getElementById(`player-department-technical-development-column-${columnNum}`)
      if (!column) return
      
      const wrapper = column.querySelector(`.player-department-technical-development-column-${columnNum}__rows-wrapper`)
      const container = wrapper || column
      const rows = container.querySelectorAll('.player-department-technical-development__row')
      
      // Удаляем старый обработчик делегирования, если есть
      if (container._technicalDevClickHandler) {
        container.removeEventListener('click', container._technicalDevClickHandler, true)
        container._technicalDevClickHandler = null
      }
      
      // Находим текущую позицию жетона (ВАЖНО: вычисляем заново каждый раз)
      let currentTokenRowIndex = null
      rows.forEach((row) => {
        const token = row.querySelector('.player-department-technical-development__token')
        if (token) {
          currentTokenRowIndex = parseInt(row.dataset.rowIndex, 10)
        }
      })
      
      if (currentTokenRowIndex === null) return
      
      // Обновляем кликабельность строк
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (!pendingMoves) return
      
      const availableMoves = pendingMoves.moveCount - pendingMoves.usedMoves
      
      // Сохраняем текущий индекс для использования в замыкании
      const currentRowIndex = currentTokenRowIndex
      const self = this
      
      // Проверяем, есть ли активное перемещение для этой колонки (для отмены)
      const existingMove = pendingMoves.moves.find(m => m.column === columnNum)
      const originalRowIndex = existingMove ? existingMove.fromRowIndex : null
      
      // Обновляем визуальное состояние строк
      rows.forEach((row) => {
        const rowIndex = parseInt(row.dataset.rowIndex, 10)
        const moveAmount = rowIndex - currentRowIndex
        
        // Строка кликабельна, если:
        // 1. Она выше текущей позиции и в пределах доступных очков (для перемещения вверх)
        // 2. ИЛИ это исходная позиция и есть активное перемещение (для отмены)
        const isClickableForMove = rowIndex > currentRowIndex && moveAmount <= availableMoves
        const isClickableForUndo = originalRowIndex !== null && rowIndex === originalRowIndex && existingMove && existingMove.amount > 0
        
        if (isClickableForMove || isClickableForUndo) {
          row.classList.add('technical-development-row--clickable')
          row.style.cursor = 'pointer'
          row.style.position = 'relative'
          row.style.pointerEvents = 'auto'
          row.style.zIndex = '10'
          row.setAttribute('data-clickable', 'true')
          if (isClickableForUndo) {
            row.setAttribute('data-undo', 'true') // Помечаем строку для отмены
          }
        } else {
          row.classList.remove('technical-development-row--clickable')
          row.style.cursor = ''
          row.style.position = ''
          row.style.pointerEvents = ''
          row.style.zIndex = ''
          row.removeAttribute('data-clickable')
          row.removeAttribute('data-undo')
        }
      })
      
      const clickableCount = container.querySelectorAll('[data-clickable="true"]').length
      console.log('🔴 Total clickable rows in column', columnNum, ':', clickableCount)
      
      // Обработчик клика
      const clickHandler = function(e) {
        console.log('🔴🔴🔴 CLICK HANDLER CALLED!', e.target, e.type)
        
        // Находим строку, на которую кликнули
        let clickedRow = e.target.closest('.player-department-technical-development__row')
        console.log('🔴 clickedRow:', clickedRow)
        
        if (!clickedRow) {
          console.log('🔴 No row found')
          return
        }
        
        console.log('🔴 Row found, checking clickable:', clickedRow.hasAttribute('data-clickable'), 'rowIndex:', clickedRow.dataset.rowIndex)
        
        // Проверяем, что строка кликабельна
        if (!clickedRow.hasAttribute('data-clickable')) {
          console.log('🔴 Row is not clickable')
          return
        }
        
        e.stopPropagation()
        e.preventDefault()
        
        const targetRowIndex = parseInt(clickedRow.dataset.rowIndex, 10)
        console.log('🔴✅ CLICK PROCESSED! column:', columnNum, 'targetRow:', targetRowIndex)
        
        // Вычисляем текущую позицию жетона заново при клике
        const currentRows = container.querySelectorAll('.player-department-technical-development__row')
        let actualCurrentRowIndex = null
        
        // Убираем подсветку со всех жетонов и находим текущую позицию
        currentRows.forEach((r) => {
          const t = r.querySelector('.player-department-technical-development__token')
          if (t) {
            t.classList.remove('technical-development-token--move-mode')
            actualCurrentRowIndex = parseInt(r.dataset.rowIndex, 10)
          }
        })
        
        // Подсвечиваем жетон в текущей позиции
        if (actualCurrentRowIndex !== null) {
          currentRows.forEach((r) => {
            const t = r.querySelector('.player-department-technical-development__token')
            if (t && parseInt(r.dataset.rowIndex, 10) === actualCurrentRowIndex) {
              t.classList.add('technical-development-token--move-mode')
            }
          })
          
          // Обрабатываем клик
          self._handleTechnicalDevelopmentRowClick(columnNum, actualCurrentRowIndex, targetRowIndex)
        }
      }
      
      // Добавляем обработчики на разные события для надежности
      container.addEventListener('mousedown', function(e) {
        console.log('🔴 MOUSEDOWN on container, target:', e.target)
        const clickedRow = e.target.closest('.player-department-technical-development__row')
        if (clickedRow && clickedRow.hasAttribute('data-clickable')) {
          console.log('🔴✅ MOUSEDOWN on clickable row!')
          e.preventDefault()
          clickHandler(e)
        }
      }, true)
      
      container.addEventListener('click', clickHandler, true)
      container._technicalDevClickHandler = clickHandler
      
      console.log('🔴✅ Event listeners added to container:', container, 'column:', columnNum)
    },

    _addTechnicalDevelopmentConfirmButton: function () {
      // Удаляем существующую кнопку, если есть
      const existingButton = document.getElementById('technical-development-moves-confirm-button')
      if (existingButton) {
        existingButton.remove()
      }
      
      // Создаем кнопку
      const button = document.createElement('button')
      button.id = 'technical-development-moves-confirm-button'
      button.className = 'technical-development-moves-confirm-button'
      button.textContent = _('Подтвердить улучшения')
      button.disabled = true
      
      // Добавляем обработчик
      button.onclick = () => {
        this._confirmTechnicalDevelopmentMoves()
      }
      
      // Добавляем кнопку в интерфейс (вверху экрана, по центру)
      // Используем фиксированное позиционирование вверху экрана по центру
      button.style.position = 'fixed'
      button.style.top = '80px'
      button.style.left = '50%'
      button.style.transform = 'translateX(-50%)'
      button.style.zIndex = '1000'
      document.body.appendChild(button)
      
      this._updateTechnicalDevelopmentConfirmButton()
    },

    _updateTechnicalDevelopmentConfirmButton: function () {
      const button = document.getElementById('technical-development-moves-confirm-button')
      if (!button) return
      
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (!pendingMoves) {
        button.disabled = true
        return
      }
      
      const usedMoves = pendingMoves.usedMoves
      const totalMoves = pendingMoves.moveCount
      const remaining = totalMoves - usedMoves
      
      // Кнопка активна, если использованы все очки
      button.disabled = usedMoves !== totalMoves
      
      if (remaining > 0) {
        button.textContent = _('Подтвердить улучшения (осталось ${count} очков)').replace('${count}', remaining)
      } else {
        button.textContent = _('Подтвердить улучшения')
      }
    },

    _updateFinishTurnButtonForTechnicalDevelopment: function () {
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      const finishButton = document.getElementById('finish-turn-button')
      
      if (!finishButton) return
      
      if (pendingMoves && pendingMoves.usedMoves > 0) {
        // Блокируем кнопку завершения, пока есть ожидающие перемещения
        finishButton.disabled = true
        finishButton.setAttribute('title', _('Завершите улучшение техотдела'))
        console.log('🔒 Finish turn button disabled - technical development moves pending')
      } else if (!pendingMoves) {
        // Если нет ожидающих перемещений, проверяем другие условия
        const hasPendingTaskSelection = this.gamedatas?.pendingTaskSelection || false
        const hasPendingTaskMoves = this.gamedatas?.pendingTaskMoves || false
        const hasPendingTaskMovesJson = this.gamedatas?.pendingTaskMovesJson || false
        
        if (!hasPendingTaskSelection && !hasPendingTaskMoves && !hasPendingTaskMovesJson) {
          finishButton.disabled = false
          finishButton.removeAttribute('title')
          console.log('✅ Finish turn button enabled - no pending moves')
        }
      }
    },

    _confirmTechnicalDevelopmentMoves: function () {
      const pendingMoves = this.gamedatas.pendingTechnicalDevelopmentMoves
      if (!pendingMoves) {
        console.error('❌ No pending technical development moves')
        return
      }
      
      if (pendingMoves.usedMoves !== pendingMoves.moveCount) {
        this.showMessage(_('Используйте все доступные очки'), 'error')
        return
      }
      
      console.log('🔧 Confirming technical development moves:', pendingMoves.moves)
      
      // Отправляем данные на сервер (преобразуем массив в JSON строку)
      this.ajaxcall('/' + this.game_name + '/' + this.game_name + '/actConfirmTechnicalDevelopmentMoves.html', {
        movesJson: JSON.stringify(pendingMoves.moves)
      }, this, (result) => {
        if (result && !result.error) {
          console.log('✅ Technical development moves confirmed')
          this._deactivateTechnicalDevelopmentMoveMode()
          
          // Разблокируем кнопку завершения
          this._updateFinishTurnButtonForTechnicalDevelopment()
        } else {
          console.error('❌ Failed to confirm technical development moves:', result)
          this.showMessage(result?.error || _('Ошибка при подтверждении'), 'error')
        }
      })
    },

    _deactivateTaskMoveMode: function () {
      console.log('🔒 Deactivating task move mode')
      
      // Убираем классы с жетонов
      const tokens = document.querySelectorAll('.task-token--move-mode')
      tokens.forEach((token) => {
        token.classList.remove('task-token--move-mode')
        if (token.dataset.location === 'backlog') {
          token.style.cursor = 'pointer'
          token.style.pointerEvents = 'auto'
        } else {
          token.style.cursor = ''
          token.style.pointerEvents = 'none'
        }
      })
      
      // Убираем индикатор
      this._hideTaskMoveModeIndicator()
      
      // Убираем кнопку подтверждения
      const confirmButton = document.getElementById('task-moves-confirm-button')
      if (confirmButton) {
        confirmButton.remove()
      }
    },

    /**
     * Показывает индикатор режима перемещения задач
     */
    _showTaskMoveModeIndicator: function (moveCount, moveColor) {
      // Убираем предыдущий индикатор, если есть
      this._hideTaskMoveModeIndicator()
      
      const indicator = document.createElement('div')
      indicator.id = 'task-move-mode-indicator'
      indicator.className = 'task-move-mode-indicator'
      indicator.innerHTML = `
        <div class="task-move-mode-indicator__content">
          <span class="task-move-mode-indicator__text">
            ${_('Режим перемещения задач')}: ${moveCount} ${_('ходов')}
            ${moveColor !== 'any' ? `(${_('только')} ${this._getTaskTokenColorData(moveColor)?.name || moveColor})` : ''}
          </span>
          <span class="task-move-mode-indicator__used" id="task-move-mode-used">0 / ${moveCount}</span>
        </div>
      `
      
      document.body.appendChild(indicator)
      
      // Позиционируем окно под окном "Эффект карты" с отступом 10px
      this._positionTaskMoveIndicator()
    },
    
    /**
     * Позиционирует индикатор режима перемещения задач под окном эффекта карты
     */
    _positionTaskMoveIndicator: function () {
      const founderHint = document.getElementById('founder-effect-sequence-hint')
      const taskIndicator = document.getElementById('task-move-mode-indicator')
      
      if (!taskIndicator) {
        return
      }
      
      if (!founderHint) {
        // Если окно эффекта карты не найдено, используем стандартное позиционирование по центру
        taskIndicator.style.position = 'fixed'
        taskIndicator.style.top = '420px'
        taskIndicator.style.left = '50%'
        taskIndicator.style.transform = 'translateX(-50%)'
        taskIndicator.style.width = 'auto'
        return
      }
      
      // Получаем позицию и размеры окна эффекта карты
      const founderRect = founderHint.getBoundingClientRect()
      
      // Устанавливаем позицию окна "Режим перемещения задач" под окном эффекта карты
      taskIndicator.style.position = 'fixed'
      taskIndicator.style.top = (founderRect.bottom + 10) + 'px' // 10px отступ
      taskIndicator.style.left = founderRect.left + 'px' // Выравниваем по левому краю
      taskIndicator.style.transform = 'none' // Убираем transform, так как позиционируем напрямую
      taskIndicator.style.width = founderRect.width + 'px' // Ширина такая же, как у окна эффекта карты
    },

    /**
     * Скрывает индикатор режима перемещения задач
     */
    _hideTaskMoveModeIndicator: function () {
      const indicator = document.getElementById('task-move-mode-indicator')
      if (indicator) {
        indicator.remove()
      }
    },

    /**
     * Обновляет UI режима перемещения задач
     */
    _updateTaskMoveModeUI: function () {
      const pendingMoves = this.gamedatas?.pendingTaskMoves
      if (!pendingMoves) {
        console.warn('⚠️ _updateTaskMoveModeUI: No pendingMoves found')
        return
      }
      
      const usedElement = document.getElementById('task-move-mode-used')
      if (usedElement) {
        const newText = `${pendingMoves.usedMoves} / ${pendingMoves.moveCount}`
        usedElement.textContent = newText
        console.log('✅✅✅ _updateTaskMoveModeUI - Counter updated:', {
          usedMoves: pendingMoves.usedMoves,
          moveCount: pendingMoves.moveCount,
          newText: newText,
          element: usedElement
        })
      } else {
        console.warn('⚠️ _updateTaskMoveModeUI: Element task-move-mode-used not found')
      }
    },

    /**
     * Показывает кнопку подтверждения перемещений
     */
    _showTaskMovesConfirmButton: function () {
      // Убираем предыдущую кнопку, если есть
      const existingButton = document.getElementById('task-moves-confirm-button')
      if (existingButton) {
        existingButton.remove()
      }
      
      const indicator = document.getElementById('task-move-mode-indicator')
      if (!indicator) return
      
      const button = document.createElement('button')
      button.id = 'task-moves-confirm-button'
      button.className = 'task-moves-confirm-button'
      button.textContent = _('Подтвердить перемещения')
      
      button.addEventListener('click', () => {
        this._confirmTaskMoves()
      })
      
      indicator.appendChild(button)
    },

    /**
     * Скрывает кнопку подтверждения перемещений
     */
    _hideTaskMovesConfirmButton: function () {
      const confirmButton = document.getElementById('task-moves-confirm-button')
      if (confirmButton) {
        confirmButton.remove()
      }
    },

    /**
     * Подтверждает перемещения задач и отправляет на сервер
     */
    _confirmTaskMoves: function () {
      const pendingMoves = this.gamedatas?.pendingTaskMoves
      if (!pendingMoves) {
        console.error('❌ No pendingMoves found!')
        this.showMessage(_('Нет перемещений для подтверждения'), 'error')
        return
      }
      
      // Проверяем, что есть перемещения для отправки
      if (!pendingMoves.moves || pendingMoves.moves.length === 0) {
        console.error('❌ No moves to confirm!', { pendingMoves })
        this.showMessage(_('Нет перемещений для подтверждения'), 'error')
        return
      }
      
      // ВАЖНО: Требуем использования всех ходов
      if (pendingMoves.usedMoves !== pendingMoves.moveCount) {
        console.warn('❌ Not all moves used:', {
          usedMoves: pendingMoves.usedMoves,
          moveCount: pendingMoves.moveCount,
          remaining: pendingMoves.moveCount - pendingMoves.usedMoves
        })
        this.showMessage(_('Вы должны использовать все доступные ходы (${used}/${total})', {
          used: pendingMoves.usedMoves,
          total: pendingMoves.moveCount
        }), 'error')
        return
      }
      
      console.log('✅ Confirming task moves:', {
        moves: pendingMoves.moves,
        movesCount: pendingMoves.moves.length,
        usedMoves: pendingMoves.usedMoves,
        moveCount: pendingMoves.moveCount,
        movesJson: JSON.stringify(pendingMoves.moves)
      })
      
      // Отправляем на сервер
      // ВАЖНО: сервер ожидает параметр movesJson, а не moves
      const movesJson = JSON.stringify(pendingMoves.moves)
      if (!movesJson || movesJson === '[]') {
        console.error('❌ Empty movesJson!')
        this.showMessage(_('Нет перемещений для подтверждения'), 'error')
        return
      }
      
      // ВАЖНО: Блокируем кнопку, чтобы предотвратить повторные нажатия
      const confirmButton = document.getElementById('task-moves-confirm-button')
      if (confirmButton) {
        confirmButton.disabled = true
        confirmButton.textContent = _('Отправка...')
      }
      
      this.bgaPerformAction('actConfirmTaskMoves', {
        movesJson: movesJson
      }, (result) => {
        if (result && result.success !== false) {
          console.log('✅ Task moves confirmed successfully')
          
          // ВАЖНО: Сразу очищаем состояние и скрываем UI после успешной отправки
          // Не ждем уведомления, так как оно может прийти с задержкой
          this.gamedatas.pendingTaskMoves = null
          this._deactivateTaskMoveMode()
          
          // Скрываем кнопку подтверждения
          if (confirmButton) {
            confirmButton.remove()
          }
          
          // Скрываем индикатор режима перемещения
          this._hideTaskMoveModeIndicator()
          
          console.log('✅ Task move mode deactivated after confirmation')
        } else {
          console.error('❌ Failed to confirm task moves:', result)
          
          // Разблокируем кнопку при ошибке
          if (confirmButton) {
            confirmButton.disabled = false
            confirmButton.textContent = _('Подтвердить перемещения')
          }
          
          // Показываем сообщение об ошибке только если это не стандартная ошибка валидации
          if (result && result.error) {
            this.showMessage(result.error, 'error')
          } else {
            this.showMessage(_('Ошибка при подтверждении перемещений'), 'error')
          }
        }
      }).catch((error) => {
        console.error('❌ Exception during task moves confirmation:', error)
        
        // Разблокируем кнопку при ошибке
        if (confirmButton) {
          confirmButton.disabled = false
          confirmButton.textContent = _('Подтвердить перемещения')
        }
        
        // Показываем сообщение об ошибке
        const errorMessage = error?.message || _('Ошибка при подтверждении перемещений')
        this.showMessage(errorMessage, 'error')
      })
    },

    /**
     * Получает текущую локацию жетона
     */
    _getTokenCurrentLocation: function (tokenId) {
      // Сначала проверяем в gamedatas (более надежный источник)
      const currentPlayer = this.gamedatas.players[this.player_id]
      if (currentPlayer && currentPlayer.taskTokens) {
        const token = currentPlayer.taskTokens.find(t => t.token_id == tokenId)
        if (token && token.location) {
          console.log('🔍 _getTokenCurrentLocation from gamedatas:', { tokenId, location: token.location })
          return token.location
        }
      }
      
      // Если не нашли в gamedatas, проверяем DOM
      const token = document.querySelector(`[data-token-id="${tokenId}"]`)
      const location = token?.dataset.location || 'backlog'
      console.log('🔍 _getTokenCurrentLocation from DOM:', { tokenId, location })
      return location
    },

    /**
     * Получает цвет жетона
     */
    _getTokenColor: function (tokenId) {
      const token = document.querySelector(`[data-token-id="${tokenId}"]`)
      return token?.dataset.color || ''
    },

    /**
     * Вычисляет количество блоков между двумя локациями
     */
    _calculateBlocksBetween: function (fromLocation, toLocation) {
      const locationOrder = ['backlog', 'in-progress', 'testing', 'completed']
      const fromIndex = locationOrder.indexOf(fromLocation)
      const toIndex = locationOrder.indexOf(toLocation)
      
      if (fromIndex === -1 || toIndex === -1) {
        return 0
      }
      
      return Math.max(0, toIndex - fromIndex)
    },

    /**
     * Показывает подсказку о последовательности действий для эффекта карты основателя
     * @param {string} founderName Имя основателя
     * @param {number} taskAmount Количество задач для выбора
     * @param {Object} movesData Данные о перемещении {moveCount, moveColor}
     */
    _showFounderEffectSequenceHint: function (founderName, taskAmount, movesData) {
      // Убираем предыдущую подсказку, если есть
      this._hideFounderEffectSequenceHint()
      
      const hint = document.createElement('div')
      hint.id = 'founder-effect-sequence-hint'
      hint.className = 'founder-effect-sequence-hint'
      hint.innerHTML = `
        <div class="founder-effect-sequence-hint__content">
          <div class="founder-effect-sequence-hint__title">
            ${_('Эффект карты')} "${founderName}"
          </div>
          <div class="founder-effect-sequence-hint__steps">
            <div class="founder-effect-sequence-hint__step ${this.gamedatas.pendingTaskSelection ? 'founder-effect-sequence-hint__step--active' : 'founder-effect-sequence-hint__step--completed'}">
              <span class="founder-effect-sequence-hint__step-number">1</span>
              <span class="founder-effect-sequence-hint__step-text">${_('Выберите')} ${taskAmount} ${_('задач')}</span>
            </div>
            <div class="founder-effect-sequence-hint__step ${this.gamedatas.pendingTaskMoves ? 'founder-effect-sequence-hint__step--active' : ''}">
              <span class="founder-effect-sequence-hint__step-number">2</span>
              <span class="founder-effect-sequence-hint__step-text">${_('Передвиньте задачи на')} ${movesData.moveCount} ${_('блока')}</span>
            </div>
          </div>
        </div>
      `
      
      document.body.appendChild(hint)
      
      // Обновляем позицию окна "Режим перемещения задач", если оно уже отображается
      setTimeout(() => {
        this._positionTaskMoveIndicator()
      }, 0)
    },

    /**
     * Скрывает подсказку о последовательности действий
     */
    _hideFounderEffectSequenceHint: function () {
      const hint = document.getElementById('founder-effect-sequence-hint')
      if (hint) {
        hint.remove()
      }
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

    /**
     * Получает выбранные задачи из input'ов
     * @returns {Array} Массив задач в формате [{color: 'cyan', quantity: 2}, ...]
     */
    _getSelectedTasks: function () {
      const taskColors = ['cyan', 'orange', 'pink', 'purple']
      const selectedTasks = []
      
      taskColors.forEach((color) => {
        const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
        if (input) {
          const quantity = parseInt(input.value) || 0
          if (quantity > 0) {
            selectedTasks.push({
              color: color,
              quantity: quantity
            })
          }
        }
      })
      
      return selectedTasks
    },

    /**
     * Отправляет задачи на сервер для добавления игроку
     * @param {number} playerId ID игрока
     * @param {Array} tasks Массив задач [{color: 'cyan', quantity: 2}, ...]
     * @param {string} location Локация задач (по умолчанию 'backlog')
     * @param {Function} callback Функция обратного вызова
     */
    _addTasksToPlayer: function (playerId, tasks, location = 'backlog', callback) {
      if (!tasks || tasks.length === 0) {
        if (callback) callback()
        return
      }

      this.bgaPerformAction('actAddTaskTokens', {
        player_id: playerId,
        tasks: tasks,
        location: location
      }, (result) => {
        if (result && result.success !== false) {
          console.log('✅ Tasks added to player', playerId, ':', tasks)
          if (callback) callback(result)
        } else {
          console.error('❌ Failed to add tasks:', result)
          if (callback) callback(result)
        }
      })
    },

    /**
     * Отправляет задачи на сервер для удаления у игрока
     * @param {number} playerId ID игрока
     * @param {Array} tasks Массив задач [{color: 'cyan', quantity: 2}, ...]
     * @param {string|null} location Локация (если указана, удаляет только из этой локации)
     * @param {Function} callback Функция обратного вызова
     */
    _removeTasksFromPlayer: function (playerId, tasks, location = null, callback) {
      if (!tasks || tasks.length === 0) {
        if (callback) callback()
        return
      }

      this.bgaPerformAction('actRemoveTaskTokens', {
        player_id: playerId,
        tasks: tasks,
        location: location
      }, (result) => {
        if (result && result.success !== false) {
          console.log('✅ Tasks removed from player', playerId, ':', tasks)
          if (callback) callback(result)
        } else {
          console.error('❌ Failed to remove tasks:', result)
          if (callback) callback(result)
        }
      })
    },

    /**
     * Обновляет локацию задачи
     * @param {number} tokenId ID токена задачи
     * @param {string} location Новая локация
     * @param {number|null} rowIndex Новый индекс строки
     * @param {Function} callback Функция обратного вызова
     */
    _updateTaskLocation: function (tokenId, location, rowIndex = null, callback) {
      this.bgaPerformAction('actUpdateTaskTokenLocation', {
        token_id: tokenId,
        location: location,
        row_index: rowIndex
      }, (result) => {
        if (result && result.success !== false) {
          console.log('✅ Task location updated:', tokenId, '->', location)
          if (callback) callback(result)
        } else {
          console.error('❌ Failed to update task location:', result)
          if (callback) callback(result)
        }
      })
    },

    /**
     * Активирует выбор задач для эффекта карты основателя
     * @param {number} maxTasks Максимальное количество задач для выбора
     */
    _activateTaskSelectionForFounder: function (maxTasks) {
      console.log('🎯 _activateTaskSelectionForFounder: maxTasks =', maxTasks)
      
      const taskColors = ['cyan', 'orange', 'pink', 'purple']
      
      // Активируем все input'ы и устанавливаем максимальное значение
      taskColors.forEach((color) => {
        const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
        if (input) {
          input.disabled = false
          input.max = maxTasks
          input.value = 0
          input.classList.remove('task-input__field--disabled')
        }
        
        // Активируем кнопки
        const decreaseBtn = input?.parentElement?.querySelector('.task-input__button--decrease')
        const increaseBtn = input?.parentElement?.querySelector('.task-input__button--increase')
        if (decreaseBtn) decreaseBtn.disabled = false
        if (increaseBtn) increaseBtn.disabled = false
      })
      
      // Добавляем кнопку "Готово"
      this._addTaskSelectionConfirmButton(maxTasks)
      
      // Добавляем обработчик изменения input'ов для проверки суммы
      this._setupTaskSelectionValidation(maxTasks)
    },

    /**
     * Деактивирует выбор задач
     */
    _deactivateTaskSelection: function () {
      console.log('🔒 _deactivateTaskSelection: deactivating task selection')
      
      const taskColors = ['cyan', 'orange', 'pink', 'purple']
      
      // Деактивируем все input'ы
      taskColors.forEach((color) => {
        const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
        if (input) {
          input.disabled = true
          input.max = 0
          input.value = 0
          input.classList.add('task-input__field--disabled')
        }
        
        // Деактивируем кнопки
        const decreaseBtn = input?.parentElement?.querySelector('.task-input__button--decrease')
        const increaseBtn = input?.parentElement?.querySelector('.task-input__button--increase')
        if (decreaseBtn) decreaseBtn.disabled = true
        if (increaseBtn) increaseBtn.disabled = true
      })
      
      // Удаляем кнопку "Готово"
      const confirmButton = document.getElementById('task-selection-confirm-button')
      if (confirmButton) {
        confirmButton.remove()
      }
    },

    /**
     * Добавляет кнопку "Готово" для подтверждения выбора задач
     * @param {number} maxTasks Максимальное количество задач
     */
    _addTaskSelectionConfirmButton: function (maxTasks) {
      // Удаляем существующую кнопку, если есть
      const existingButton = document.getElementById('task-selection-confirm-button')
      if (existingButton) {
        existingButton.remove()
      }
      
      // Находим контейнер с input'ами
      const container = document.querySelector('.task-inputs-container')
      if (!container) {
        console.error('❌ task-inputs-container not found')
        return
      }
      
      // Создаем кнопку
      const button = document.createElement('button')
      button.id = 'task-selection-confirm-button'
      button.className = 'task-selection-confirm-button'
      button.textContent = _('Готово')
      button.disabled = true // По умолчанию отключена
      
      // Обработчик клика
      button.addEventListener('click', () => {
        this._confirmTaskSelection(maxTasks)
      })
      
      // Добавляем кнопку в контейнер
      container.appendChild(button)
      
      console.log('✅ Task selection confirm button added')
    },

    /**
     * Настраивает валидацию выбора задач (проверка суммы)
     * @param {number} maxTasks Максимальное количество задач
     */
    _setupTaskSelectionValidation: function (maxTasks) {
      const taskColors = ['cyan', 'orange', 'pink', 'purple']
      const confirmButton = document.getElementById('task-selection-confirm-button')
      
      const validateSelection = () => {
        let total = 0
        taskColors.forEach((color) => {
          const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
          if (input && !input.disabled) {
            total += parseInt(input.value) || 0
          }
        })
        
        // Обновляем состояние кнопки "Готово"
        if (confirmButton) {
          confirmButton.disabled = total !== maxTasks
          if (total > maxTasks) {
            confirmButton.title = _('Выбрано слишком много задач')
          } else if (total < maxTasks) {
            confirmButton.title = _('Выберите ровно ${amount} задач', { amount: maxTasks })
          } else {
            confirmButton.title = ''
          }
        }
        
        // Обновляем максимальное значение для каждого input'а
        taskColors.forEach((color) => {
          const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
          if (input && !input.disabled) {
            const currentValue = parseInt(input.value) || 0
            const otherTotal = total - currentValue
            const remaining = maxTasks - otherTotal
            input.max = Math.max(0, remaining)
            
            // Если текущее значение превышает доступное, уменьшаем его
            if (currentValue > remaining) {
              input.value = remaining
            }
          }
        })
      }
      
      // Добавляем обработчики на все input'ы
      taskColors.forEach((color) => {
        const input = document.querySelector(`.task-input__field[data-color="${color}"]`)
        if (input) {
          input.addEventListener('input', validateSelection)
          input.addEventListener('change', validateSelection)
        }
      })
      
      // Первоначальная валидация
      validateSelection()
    },

    /**
     * Подтверждает выбор задач и отправляет на сервер
     * @param {number} maxTasks Максимальное количество задач
     */
    _confirmTaskSelection: function (maxTasks) {
      const selectedTasks = this._getSelectedTasks()
      const total = selectedTasks.reduce((sum, task) => sum + task.quantity, 0)
      
      if (total !== maxTasks) {
        this.showMessage(_('Вы должны выбрать ровно ${amount} задач', { amount: maxTasks }), 'error')
        return
      }
      
      console.log('✅ Confirming task selection:', selectedTasks)
      
      // Отправляем на сервер (преобразуем массив в JSON строку)
      this.bgaPerformAction('actConfirmTaskSelection', {
        selectedTasksJson: JSON.stringify(selectedTasks)
      }, (result) => {
        if (result && result.success !== false) {
          console.log('✅ Task selection confirmed successfully')
        } else {
          console.error('❌ Failed to confirm task selection:', result)
          this.showMessage(_('Ошибка при подтверждении выбора задач'), 'error')
        }
      })
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

          // Проверяем, не выполняется ли уже действие
          if (this._placingFounder) {
            console.warn('⚠️ actPlaceFounder already in progress, ignoring duplicate call')
            return
          }
          
          // Устанавливаем флаг, что действие выполняется
          this._placingFounder = true
          
          // Вызываем серверное действие для размещения карты
          const promise = this.bgaPerformAction('actPlaceFounder', { department: department })
          if (promise) {
            promise.then(() => {
              // Кнопка "Завершить ход" разблокируется через уведомление founderEffectsApplied
              this._placingFounder = false
            }).catch((error) => {
              console.error('❌ Error placing founder card:', error)
              this._placingFounder = false
            })
          } else {
            // checkAction не прошёл (например, "An action is already in progress") — сбрасываем флаг
            this._placingFounder = false
          }
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
    }
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
