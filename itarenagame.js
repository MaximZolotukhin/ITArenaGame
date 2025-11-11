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
      this.gamedatas = gamedatas
      this.eventCardsData = gamedatas.eventCards || {}
      this._renderRoundTrack(this.totalRounds)
      this._renderRoundBanner(gamedatas.round, this.totalRounds, gamedatas.stageName, gamedatas.cubeFace, gamedatas.phaseName)

      // Обновляем отображение кубика
      this._updateCubeFace(gamedatas.cubeFace)
      const initialEventCards = gamedatas.roundEventCards || []
      this._renderEventCards(initialEventCards)
      this._renderRoundEventCards(initialEventCards)
      this._renderBadgers(gamedatas.badgers || [])
      this._renderPlayerMoney(gamedatas.players) // Отображаем деньги игрока

      // TODO: Set up your game interface here, according to "gamedatas"

      // Setup game notifications to handle (see "setupNotifications" method below)
      // Мой код для уведомлений
      this.setupNotifications()

      console.log('Ending game setup')
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

            // Add test action buttons in the action status bar, simulating a card click:
            // Мой код для кнопок действий
            playableCardsIds.forEach((cardId) => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId)))

            this.statusBar.addActionButton(_('Pass'), () => this.bgaPerformAction('actPass'), { color: 'secondary' })
            this.statusBar.addActionButton(_('Завершить ход'), () => this.bgaPerformAction('actFinishTurn'), { primary: true })
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
        this._renderPlayerMoney(this.gamedatas.players) // Обновляем деньги игрока
      }
    },

    notif_gameEnd: async function (args) {
      const el = document.getElementById('round-banner')
      if (el) el.textContent = _('Игра окончена')
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
    _renderPlayerMoney: function (players) {
      // Обновляем деньги игрока
      const panelBody = document.querySelector('.player-money-panel__body') // Обновляем деньги игрока
      if (!panelBody) return

      const playerId = this.player_id // Идентификатор игрока
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

      panelBody.innerHTML = `
        <div class="player-money-panel__balance">
          <img src="${imageUrl}" alt="${coinData?.name || _('Баджерсы')}" class="player-money-panel__icon" />
          <span class="player-money-panel__amount">${amount}</span>
        </div>
      `
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
  })
})
