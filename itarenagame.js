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
                      <div id="round-banner" class="round-banner"></div>
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
                          <div class="round-track"></div>
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
      this._renderRoundTrack(this.totalRounds)
      this._renderRoundBanner(gamedatas.round, this.totalRounds, gamedatas.stageName, gamedatas.cubeFace, gamedatas.phaseName)

      // Обновляем отображение кубика
      this._updateCubeFace(gamedatas.cubeFace)
      const initialEventCards = gamedatas.roundEventCards || []
      this._renderEventCards(initialEventCards)
      this._renderRoundEventCards(initialEventCards)
      this._renderBadgers(gamedatas.badgers || [])
      const initialActiveId = this._getActivePlayerIdFromDatas(gamedatas) || this.player_id
      this._renderPlayerMoney(gamedatas.players, initialActiveId) // Отображаем деньги игрока
      this._renderFounderCard(gamedatas.players, initialActiveId)
      this._toggleActivePlayerHand(initialActiveId)
      this._updateHandHighlight(initialActiveId)

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
      console.log('Entering state: ' + stateName, args)

      switch (stateName) {
        /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */

        case 'dummy':
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
      if (this.isCurrentPlayerActive()) {
        switch (stateName) {
          case 'PlayerTurn':
            const playableCardsIds = args.playableCardsIds // returned by the argPlayerTurn
            const mustPlaceFounder = args.mustPlaceFounder === true // Обязательно ли разместить карту основателя

            // Add test action buttons in the action status bar, simulating a card click:
            // Мой код для кнопок действий
            playableCardsIds.forEach((cardId) => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId)))

            this.statusBar.addActionButton(_('Pass'), () => this.bgaPerformAction('actPass'), { color: 'secondary' })

            // Кнопка завершения хода: блокируется, если нужно разместить карту основателя
            const finishTurnButton = this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), {
              primary: true,
              disabled: mustPlaceFounder,
              tooltip: mustPlaceFounder ? _('Вы должны разместить карту основателя в один из отделов перед завершением хода') : undefined,
              id: 'finish-turn-button', // ID для обновления состояния кнопки
            })

            // Сохраняем ссылку на кнопку для обновления состояния после размещения карты
            this.finishTurnButton = finishTurnButton
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
      this._renderRoundBanner(args.round, this.totalRounds, args.stageName, args.cubeFace, args.phaseName)
      // Обновляем отображение кубика
      this._updateCubeFace(args.cubeFace)
      const eventCards = args.roundEventCards || (args.eventCard ? [args.eventCard] : [])
      console.log('roundStart eventCards', eventCards)
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

    notif_gameEnd: async function (args) {
      const el = document.getElementById('round-banner')
      if (el) el.textContent = _('Игра окончена')
    },

    notif_founderPlaced: async function (args) {
      // Обновляем данные о размещении карты основателя
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
      el.textContent = text
      this._highlightRoundMarker(round)
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
      console.log('updateCubeFace called', cubeFace)
      const display = document.getElementById('cube-face-display')
      if (!display) return
      const value = cubeFace ? String(cubeFace).trim() : ''
      display.textContent = value
    },

    _renderRoundTrack: function (totalRounds) {
      const track = document.querySelector('.round-track')
      if (!track) return
      track.innerHTML = ''
      for (let i = 1; i <= totalRounds; i++) {
        const marker = document.createElement('div')
        marker.className = 'round-track__circle'
        marker.dataset.round = String(i)
        marker.innerHTML = `<span>${i}</span>`
        track.appendChild(marker)
      }
      this._highlightRoundMarker(this.gamedatas?.round || 1)
    },

    _highlightRoundMarker: function (round) {
      const track = document.querySelector('.round-track')
      if (!track) return
      const markers = track.querySelectorAll('.round-track__circle')
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

      Object.values(containers).forEach((container) => {
        if (container) {
          container.innerHTML = ''
        }
      })
      if (handContainer) {
        handContainer.innerHTML = ''
        handContainer.classList.remove('active-player-hand__center--selecting') // Убираем выделение руки игрока
      }
      this.pendingFounderMove = null // Сбрасываем ожидание перемещения карты основателя
      this._setDepartmentHighlight(false) // Сбрасываем выделение отдела
      this._setHandHighlight(false)

      const fallbackId = this._getActivePlayerIdFromDatas(this.gamedatas) ?? this.player_id
      const playerId = targetPlayerId ?? fallbackId

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

      handContainer.addEventListener('click', () => {
        // Проверяем, что это активный игрок
        const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
        if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
          return // Только активный игрок может управлять картами
        }

        const card = handContainer.querySelector('.founder-card')
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
        this._setDepartmentHighlight(isActive)
        this._setHandHighlight(isActive)
      })
      ;['sales-department', 'back-office', 'technical-department'].forEach((department) => {
        // Добавляем обработчики кликов для отделов
        const container = document.querySelector(`.${department}__body`)
        if (!container) {
          return
        }

        container.addEventListener('click', () => {
          // Проверяем, что это активный игрок
          const activePlayerId = this._getActivePlayerIdFromDatas(this.gamedatas)
          if (!activePlayerId || Number(activePlayerId) !== Number(this.player_id)) {
            return // Только активный игрок может размещать карты
          }

          const activeCard = handContainer?.querySelector('.founder-card--active')
          if (!activeCard) {
            return
          }

          if (!container.classList.contains('department-highlight')) {
            return
          }

          const ownerId = Number(activeCard.dataset.playerId || handContainer?.dataset.playerId || 0)

          // Проверяем, что карта принадлежит текущему игроку
          if (ownerId !== Number(this.player_id)) {
            return // Карта не принадлежит текущему игроку
          }

          // Вызываем серверное действие для размещения карты
          this._setHandHighlight(false)
          this._setDepartmentHighlight(false)

          this.bgaPerformAction(
            'actPlaceFounder',
            {
              department: department,
            },
            (result) => {
              // Действие успешно выполнено
              console.log('Founder placed successfully:', result)
            }
          )
        })
      })
    },
    _setDepartmentHighlight: function (enabled) {
      ;['sales-department', 'back-office', 'technical-department'].forEach((department) => {
        const container = document.querySelector(`.${department}__body`)
        if (!container) return
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
