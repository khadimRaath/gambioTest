/* --------------------------------------------------------------
 info_box.js 2016-08-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Infobox Controller
 */
gx.controllers.module(
	'info_box',

	[
		'loading_spinner',
		`${gx.source}/libs/info_box`
	],

	function (data) {

		'use strict';

		// --------------------------------------------------------------------
		// VARIABLES
		// --------------------------------------------------------------------

		/**
		 * Module Instance
		 *
		 * @type {Object}
		 */
		const  module = {};

		// --------------------------------------------------------------------
		// FUNCTIONS
		// --------------------------------------------------------------------

		/**
		 * Class representing a controller for the admin info box.
		 *
		 * Passed element will have an event listener 'show:popover' to call the popover.
		 */
		class InfoBoxController {
			/**
			 * Creates a new info box controller.
			 *
			 * @param  {Function} done           Module finish callback function.
			 * @param  {jQuery}   $element       Trigger element.
			 * @param  {Object}   ServiceLibrary Info box service library.
			 * @param  {Object}   LoadingSpinner Loading spinner library.
			 * @param  {Object}   Translator     JS-Engine translation library.
			 */
			constructor(done, $element, ServiceLibrary, LoadingSpinner, Translator) {
				// Popover animation time values (in ms)
				this.CLOSE_DELAY = 5000;
				this.FADEOUT_DURATION = 650;

				// CSS classes.
				this.HIDDEN_CLASS = 'hidden';
				this.ACTIVE_CLASS = 'active';

				// Default open mode on links.
				this.OPEN_MODE = '_self';

				// Message constants
				this.STATUS_NEW = 'new';
				this.STATUS_READ = 'read';
				this.STATUS_HIDDEN = 'hidden';
				this.STATUS_DELETED = 'deleted';
				this.TYPE_INFO = 'info';
				this.TYPE_WARNING = 'warning';
				this.TYPE_SUCCESS = 'success';
				this.VISIBILITY_ALWAYS_ON = 'alwayson';
				this.VISIBILITY_HIDEABLE = 'hideable';
				this.VISIBILITY_REMOVABLE = 'removable';

				// Elements
				this.$element = $element;
				this.$messageCount = $element.find('.notification-count');

				// Libraries
				this.service = ServiceLibrary;
				this.loadingSpinner = LoadingSpinner;
				this.translator = Translator;

				// Selector strings
				this.messageListSelector = '.info-box-popover-content';
				this.messageListCheckboxSelector = '.visibility-checkbox';
				this.messageItemSelector = '.message';
				this.messageItemHiddenSelector = '[data-status="hidden"]';
				this.messageItemButtonSelector = '.message-button';
				this.messageItemActionSelector = '.message-action';
				this.popoverSelector = '.info-box-popover';
				this.popoverArrowSelector = 'div.arrow';

				// Admin action success message identifier prefix.
				this.successMessageIdentifierPrefix = 'adminActionSuccess-';

				// Bind popover to element.
				this._initPopover();

				// Call module finish callback.
				done();
			}

			/**
			 * Checks for (new) messages and sets the message count.
			 *
			 * This method is called if the page is loaded.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @public
			 */
			checkInitial() {
				// Messages iterator.
				const handleMessages = messages => {
					// Message counter.
					let messageCount = 0;

					// Flag to indicate if there are new messages?
					let hasNewMessages = false;

					// Iterate over each message.
					for (const message of messages) {
						// Find a new message.
						if (message.status === this.STATUS_NEW) {
							hasNewMessages = true;
						}

						// Count the message excluding the success messages.
						if (message.identifier.search(this.successMessageIdentifierPrefix) === -1) {
							messageCount++;
						}
					}

					// Set message count.
					this._setMessageCount(messageCount);

					// Open info box if there are new messages.
					if (hasNewMessages) {
						this._showPopover();
						setTimeout(() => this._hidePopover(), this.CLOSE_DELAY);
					}
				};

				// Get messages and call messages iterator function.
				this.service
					.getMessages()
					.then(messages => handleMessages(messages));

				return this;
			}

			/**
			 * Shows the popover.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_showPopover() {
				// Trigger popover show event.
				this.$element.popover('show');
				return this;
			}

			/**
			 * Binds the bootstrap popover to the element.
			 *
			 * {@link http://getbootstrap.com/javascript/#popovers}
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_initPopover() {
				// Popover initialization options.
				const popoverOptions = {
					animation: false,
					placement: 'bottom',
					content: ' ',
					trigger: 'manual',
					template: InfoBoxController._createPopoverTemplateElement()
				};

				// Initialize popover and attach event handlers.
				this.$element
					.popover(popoverOptions)
					.on('click', event => this._onButtonClick(event))
					.on('shown.bs.popover', event => this._onPopoverShown(event))
					.on('show:popover', event => this._showPopover())
					.on('refresh:messages', event => this.checkInitial());

				// Attach event listeners to the window.
				$(window)
					.on('resize', () => this._fixPopoverPosition())
					.on('click', event => this._onWindowClick(event));

				return this;
			}

			/**
			 * Fixes the position of the popover depending on the window size.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_fixPopoverPosition() {
				// Offset correction values.
				const ARROW_OFFSET = 240;
				const POPOVER_OFFSET = 250;

				const $popover = $(this.popoverSelector);
				const $arrow = $popover.find(this.popoverArrowSelector);

				// Fix the offset for the affected elements, if popover is open.
				if ($popover.length) {
					const arrowOffset = $popover.offset().left + ARROW_OFFSET;
					const popoverOffset = this.$element.offset().left - POPOVER_OFFSET + (this.$element.width() / 2);

					$arrow.offset({left: arrowOffset});
					$popover.offset({left: popoverOffset});
				}

				return this;
			}

			/**
			 * Handles event for button click action.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onButtonClick() {
				const $popover = $(this.popoverSelector);

				// Toggle popover, based on the visibility of the element.
				if ($popover.length) {
					this._hidePopover();
				} else {
					this._showPopover();
				}

				return this;
			}

			/**
			 * Handles event for shown popover action.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onPopoverShown() {
				const $messageList = $(this.messageListSelector);
				const $popover = $(this.popoverSelector);

				// Hide the popover on the top.
				// This is needed to handle the popover animation, handled by CSS.
				$popover
					.addClass(this.ACTIVE_CLASS)
					.css({top: $popover.height() * -1});

				// Fix the popover position, fetch and show the messages and mark shown messages as read.
				this
					._fixPopoverPosition()
					.service.getMessages()
					.then(messages => this._fillPopoverContent(messages)._markMessagesAsRead());

				// Attach event handlers to popover.
				$messageList
					.off('click change')
					.on('click', this.messageItemButtonSelector, event => this._onMessageButtonClick(event))
					.on('click', this.messageItemActionSelector, event => this._onMessageActionClick(event))
					.on('change', this.messageListCheckboxSelector, event => this._onVisibilityCheckboxChange(event));

				return this;
			}

			/**
			 * Hides the popover.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_hidePopover() {
				// Remove active class to start animation.
				$(this.popoverSelector).removeClass(this.ACTIVE_CLASS);

				// Deferred fire of the hide event to be sure that the animation is complete.
				setTimeout(() => this.$element.popover('hide'), this.FADEOUT_DURATION);

				return this;
			}

			/**
			 * Handles event for clicked message buttons.
			 *
			 * @param {jQuery.Event} event Fired event.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onMessageButtonClick(event) {
				// Link value from button.
				const href = $(event.target).attr('href');

				event.preventDefault();
				event.stopPropagation();

				// Open link if exists.
				if (href && href.trim().length) {
					window.open(href, this.OPEN_MODE);
				}

				return this;
			}

			/**
			 * Handles event for clicked message action trigger elements.
			 *
			 * @param {jQuery.Event} event Fired event.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onMessageActionClick(event) {
				const actionRemoveClass = 'message-action-remove';

				const $element = $(event.target);

				// Check if the clicked target indicates a message removal.
				const doRemove = $element.hasClass(actionRemoveClass);

				// ID of the message taken from the message item element.
				const id = $element.parents(this.messageItemSelector).data('id');

				// Delete/hide message depending on the clicked target.
				if (doRemove) {
					this._deleteMessage(id);
				} else {
					this._hideMessage(id);
				}

				return this;
			}

			/**
			 * Handles event for click action inside the window.
			 *
			 * @param {jQuery.Event} event Fired event.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onWindowClick(event) {
				const $target = $(event.target);
				const $popover = $(this.popoverSelector);

				const isClickedOnButton = this.$element.has($target).length || this.$element.is($target).length;
				const isPopoverShown = $popover.length;
				const isClickedOutsideOfPopover = !$popover.has($target).length;

				// Only hide dropdown, if clicked target is not within the popover area.
				if (isClickedOutsideOfPopover && isPopoverShown && !isClickedOnButton) {
					this._hidePopover();
				}

				return this;
			}

			/**
			 * Handles event for checkbox change.
			 *
			 * @param {jQuery.Event} event Fired event.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_onVisibilityCheckboxChange(event) {
				const isCheckboxChecked = $(event.target).is(':checked');

				// Toggle hidden messages and mark shown messages as read.
				this
					._toggleHiddenMessages(isCheckboxChecked)
					._markMessagesAsRead();

				return this;
			}

			/**
			 * Shows/hides the messages declared as hidden via the data attribute.
			 *
			 * @param {Boolean} doShow Show the hidden messages?
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_toggleHiddenMessages(doShow) {
				// Get all hidden message elements.
				const $hiddenMessages = $(this.messageListSelector).find(this.messageItemHiddenSelector);

				if (doShow) {
					$hiddenMessages.removeClass(this.HIDDEN_CLASS);
				} else {
					$hiddenMessages.addClass(this.HIDDEN_CLASS);
				}

				return this;
			}

			/**
			 * Sets the amount of messages into the icon.
			 *
			 * @param {Number} amount Message count.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_setMessageCount(amount) {
				// If no amount has been passed, the notification count will be hidden.
				if (amount) {
					this.$messageCount
						.removeClass(this.HIDDEN_CLASS)
						.text(amount);
				} else {
					this.$messageCount.addClass(this.HIDDEN_CLASS);
				}

				return this;
			}

			/**
			 * Fills the content element with all messages provided.
			 *
			 * @param {Object} messages Messages.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 *
			 * @private
			 */
			_fillPopoverContent(messages) {
				// Message list element.
				const $messageList = $(this.messageListSelector);

				// Popover element.
				const $popover = $(this.popoverSelector);

				// Show loading spinner.
				const $spinner = this.loadingSpinner.show($popover);

				// Fix for the loading spinner.
				$spinner.css({'z-index': 9999});

				// Message counter.
				let messageCount = 0;

				// Clear message list.
				$messageList.empty();

				// Show info, if there are no messages.
				// Else fill the message list with message items.
				if (!messages.length) {
					const message = {
						message: this.translator.translate('NO_MESSAGES', 'admin_info_boxes'),
						visibility: this.VISIBILITY_ALWAYS_ON,
						status: this.STATUS_READ,
						headline: '',
						type: ''
					};

					$messageList.append(this._createMessageElement(message));
				} else {
					let doesExistSuccessMessage = false;
					let doesExistHiddenMessages = false;

					// Iterate through messages and check if there are hidden ones.
					for (const message of messages) {
						const isSuccessMessage = message.identifier.search(this.successMessageIdentifierPrefix) !== -1;

						$messageList.append(this._createMessageElement(message));

						if (message.status === this.STATUS_HIDDEN) {
							doesExistHiddenMessages = true;
						}

						// Count the message excluding the success messages.
						if (isSuccessMessage) {
							doesExistSuccessMessage = true;
						} else {
							messageCount++;
						}
					}

					// Set the message count.
					this._setMessageCount(messageCount);

					// Hide messages declared as hidden and add visibility checkbox.
					if (doesExistHiddenMessages && !doesExistSuccessMessage) {
						$messageList.append(this._createVisibilityCheckboxElement());
						this._toggleHiddenMessages(false);
					}

					// Remove all other messages if a success message is present.
					if (doesExistSuccessMessage) {
						$(this.messageListSelector)
							.find(this.messageItemSelector)
							.not(`[data-identifier*=${this.successMessageIdentifierPrefix}]`)
							.remove();
					}
				}

				// Do some fade animations for smooth displaying of message items.
				$messageList
					.children()
					.each((index, element) => $(element).hide().fadeIn());

				// Hide loading spinner.
				this.loadingSpinner.hide($spinner);

				return this;
			}

			/**
			 * Shows a loading spinner and reloads the messages.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 */
			_refreshPopover() {
				const $popover = $(this.popoverSelector);
				const $spinner = this.loadingSpinner.show($popover);

				// Fix for the loading spinner.
				$spinner.css({'z-index': 9999});

				// Retrieve messages, fill the message list and kill the loading spinner.
				this
					.service.getMessages()
					.then(messages => this._fillPopoverContent(messages)._markMessagesAsRead())
					.then(() => this.loadingSpinner.hide($spinner));

				return this;
			}

			/**
			 * Marks all visible messages as read.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 */
			_markMessagesAsRead() {
				// Get message item elements.
				const $messages = $(this.messageListSelector).find(this.messageItemSelector);

				// Set status as read for visible elements only.
				const messageIterator = (index, element) => {
					const $message = $(element);
					const data = $message.data();
					const isHidden = $message.hasClass(this.STATUS_HIDDEN);

					// Delete by ID if existent.
					if (!isHidden && data.id) {
						this.service.setStatus(data.id, this.STATUS_READ);
						$message.data('status', this.STATUS_READ);
					}

					// Delete success messages.
					if (data.identifier && data.identifier.search(this.successMessageIdentifierPrefix) !== -1) {
						this.service.deleteByIdentifier(data.identifier);
					}
				};

				// Iterate over each message.
				$messages.each(messageIterator);

				return this;
			}

			/**
			 * Deletes a message and refreshes the message item list.
			 *
			 * @param {Number} id Message ID.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 */
			_deleteMessage(id) {
				this.service
					.deleteById(id)
					.then(() => this._refreshPopover());

				return this;
			}

			/**
			 * Hides a message and refreshes the message item list.
			 *
			 * @param {Number} id Message ID.
			 *
			 * @return {InfoBoxController} Same instance for method chaining.
			 */
			_hideMessage(id) {
				this.service
					.setStatus(id, this.STATUS_HIDDEN)
					.then(() => this._refreshPopover());

				return this;
			}

			/**
			 * Creates and returns a new container with a checkbox and label.
			 *
			 * @return {jQuery} Composed element.
			 *
			 * @private
			 */
			_createVisibilityCheckboxElement() {
				const $container = $('<div/>', {class: 'visibility-checkbox-container'});
				const $label = $('<label/>', {
					class: 'visibility-checkbox-label',
					text: this.translator.translate('SHOW_ALL', 'admin_info_boxes')
				});
				const $checkbox = $('<input/>', {type: 'checkbox', class: 'visibility-checkbox'});

				$container.append($checkbox, $label);

				return $container;
			}

			/**
			 * Creates and returns a new HTML element containing the message contents.
			 *
			 * Element represents a message item.
			 *
			 * @param {Object} message Message object.
			 *
			 * @return {jQuery} Composed element.
			 *
			 * @private
			 */
			_createMessageElement(message) {
				// Template elements.
				const $template = $('<div/>', {class: `message ${message.type}`});
				const $headline = $('<p/>', {class: 'message-headline', html: message.headline});
				const $message = $('<p/>', {class: 'message-body', html: message.message});
				const $actionContainer = $('<div/>', {class: 'message-action-container'});
				const $hideAction = $('<span/>', {class: 'message-action message-action-hide fa fa-minus'});
				const $removeAction = $('<span/>', {class: 'message-action message-action-remove fa fa-times'});

				// Is the message a success message?
				const isSuccessMessage = message.identifier ?
				                         message.identifier.search(this.successMessageIdentifierPrefix) !== -1 :
				                         false;
				
				// Show remove/hide button, depending on the visibility value and kind of message.
				if (!isSuccessMessage && message.visibility === this.VISIBILITY_REMOVABLE) {
					$actionContainer
						.append($hideAction, $removeAction)
						.appendTo($template);
				} else if (!isSuccessMessage && message.visibility === this.VISIBILITY_HIDEABLE) {
					$actionContainer
						.append($hideAction)
						.appendTo($template);
				} else if (isSuccessMessage) {
					$actionContainer
						.append($removeAction)
						.appendTo($template);
				}
				
				// Put message data to the message item element as data attributes and append text elements.
				$template
					.attr('data-status', message.status)
					.attr('data-id', message.id)
					.attr('data-visibility', message.visibility)
					.attr('data-identifier', message.identifier)
					.append($headline, $message);

				// Append button, if a button label is defined.
				if (message.buttonLabel) {
					const $button = $('<a/>', {
						class: 'btn message-button',
						text: message.buttonLabel,
						href: message.buttonLink
					});

					$template.append($button);
				}

				return $template;
			}

			/**
			 * Creates and returns a new HTML element containg the popover template.
			 *
			 * @return {jQuery} Composed element.
			 *
			 * @private
			 *
			 * @static
			 */
			static _createPopoverTemplateElement() {
				const $popover = $('<div/>', {class: 'popover info-box-popover', role: 'tooltip'});
				const $arrow = $('<div/>', {class: 'arrow'});
				const $title = $('<div/>', {class: 'popover-title'});
				const $content = $('<div/>', {class: 'popover-content info-box-popover-content'});

				$popover.append($arrow, $title, $content);

				return $popover;
			}
		}

		// --------------------------------------------------------------------
		// INITIALIZATION
		// --------------------------------------------------------------------

		module.init = (done) => {
			// Dependencies.
			const $element = $(this).find('.info-box');
			const ServiceLibrary = jse.libs.info_box.service;
			const LoadingSpinner = jse.libs.loading_spinner;
			const Translator = jse.core.lang;

			// Create a new InfoBox controller instance and set message count.
			const InfoBox = new InfoBoxController(done, $element, ServiceLibrary, LoadingSpinner, Translator);
			InfoBox.checkInitial();
		};

		return module;
	});
