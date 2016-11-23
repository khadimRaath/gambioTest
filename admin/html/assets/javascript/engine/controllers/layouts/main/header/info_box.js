'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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
gx.controllers.module('info_box', ['loading_spinner', gx.source + '/libs/info_box'], function (data) {

	'use strict';

	// --------------------------------------------------------------------
	// VARIABLES
	// --------------------------------------------------------------------

	/**
  * Module Instance
  *
  * @type {Object}
  */

	var _this9 = this;

	var module = {};

	// --------------------------------------------------------------------
	// FUNCTIONS
	// --------------------------------------------------------------------

	/**
  * Class representing a controller for the admin info box.
  *
  * Passed element will have an event listener 'show:popover' to call the popover.
  */

	var InfoBoxController = function () {
		/**
   * Creates a new info box controller.
   *
   * @param  {Function} done           Module finish callback function.
   * @param  {jQuery}   $element       Trigger element.
   * @param  {Object}   ServiceLibrary Info box service library.
   * @param  {Object}   LoadingSpinner Loading spinner library.
   * @param  {Object}   Translator     JS-Engine translation library.
   */
		function InfoBoxController(done, $element, ServiceLibrary, LoadingSpinner, Translator) {
			_classCallCheck(this, InfoBoxController);

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


		_createClass(InfoBoxController, [{
			key: 'checkInitial',
			value: function checkInitial() {
				var _this = this;

				// Messages iterator.
				var handleMessages = function handleMessages(messages) {
					// Message counter.
					var messageCount = 0;

					// Flag to indicate if there are new messages?
					var hasNewMessages = false;

					// Iterate over each message.
					var _iteratorNormalCompletion = true;
					var _didIteratorError = false;
					var _iteratorError = undefined;

					try {
						for (var _iterator = messages[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
							var message = _step.value;

							// Find a new message.
							if (message.status === _this.STATUS_NEW) {
								hasNewMessages = true;
							}

							// Count the message excluding the success messages.
							if (message.identifier.search(_this.successMessageIdentifierPrefix) === -1) {
								messageCount++;
							}
						}

						// Set message count.
					} catch (err) {
						_didIteratorError = true;
						_iteratorError = err;
					} finally {
						try {
							if (!_iteratorNormalCompletion && _iterator.return) {
								_iterator.return();
							}
						} finally {
							if (_didIteratorError) {
								throw _iteratorError;
							}
						}
					}

					_this._setMessageCount(messageCount);

					// Open info box if there are new messages.
					if (hasNewMessages) {
						_this._showPopover();
						setTimeout(function () {
							return _this._hidePopover();
						}, _this.CLOSE_DELAY);
					}
				};

				// Get messages and call messages iterator function.
				this.service.getMessages().then(function (messages) {
					return handleMessages(messages);
				});

				return this;
			}

			/**
    * Shows the popover.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_showPopover',
			value: function _showPopover() {
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

		}, {
			key: '_initPopover',
			value: function _initPopover() {
				var _this2 = this;

				// Popover initialization options.
				var popoverOptions = {
					animation: false,
					placement: 'bottom',
					content: ' ',
					trigger: 'manual',
					template: InfoBoxController._createPopoverTemplateElement()
				};

				// Initialize popover and attach event handlers.
				this.$element.popover(popoverOptions).on('click', function (event) {
					return _this2._onButtonClick(event);
				}).on('shown.bs.popover', function (event) {
					return _this2._onPopoverShown(event);
				}).on('show:popover', function (event) {
					return _this2._showPopover();
				}).on('refresh:messages', function (event) {
					return _this2.checkInitial();
				});

				// Attach event listeners to the window.
				$(window).on('resize', function () {
					return _this2._fixPopoverPosition();
				}).on('click', function (event) {
					return _this2._onWindowClick(event);
				});

				return this;
			}

			/**
    * Fixes the position of the popover depending on the window size.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_fixPopoverPosition',
			value: function _fixPopoverPosition() {
				// Offset correction values.
				var ARROW_OFFSET = 240;
				var POPOVER_OFFSET = 250;

				var $popover = $(this.popoverSelector);
				var $arrow = $popover.find(this.popoverArrowSelector);

				// Fix the offset for the affected elements, if popover is open.
				if ($popover.length) {
					var arrowOffset = $popover.offset().left + ARROW_OFFSET;
					var popoverOffset = this.$element.offset().left - POPOVER_OFFSET + this.$element.width() / 2;

					$arrow.offset({ left: arrowOffset });
					$popover.offset({ left: popoverOffset });
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

		}, {
			key: '_onButtonClick',
			value: function _onButtonClick() {
				var $popover = $(this.popoverSelector);

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

		}, {
			key: '_onPopoverShown',
			value: function _onPopoverShown() {
				var _this3 = this;

				var $messageList = $(this.messageListSelector);
				var $popover = $(this.popoverSelector);

				// Hide the popover on the top.
				// This is needed to handle the popover animation, handled by CSS.
				$popover.addClass(this.ACTIVE_CLASS).css({ top: $popover.height() * -1 });

				// Fix the popover position, fetch and show the messages and mark shown messages as read.
				this._fixPopoverPosition().service.getMessages().then(function (messages) {
					return _this3._fillPopoverContent(messages)._markMessagesAsRead();
				});

				// Attach event handlers to popover.
				$messageList.off('click change').on('click', this.messageItemButtonSelector, function (event) {
					return _this3._onMessageButtonClick(event);
				}).on('click', this.messageItemActionSelector, function (event) {
					return _this3._onMessageActionClick(event);
				}).on('change', this.messageListCheckboxSelector, function (event) {
					return _this3._onVisibilityCheckboxChange(event);
				});

				return this;
			}

			/**
    * Hides the popover.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_hidePopover',
			value: function _hidePopover() {
				var _this4 = this;

				// Remove active class to start animation.
				$(this.popoverSelector).removeClass(this.ACTIVE_CLASS);

				// Deferred fire of the hide event to be sure that the animation is complete.
				setTimeout(function () {
					return _this4.$element.popover('hide');
				}, this.FADEOUT_DURATION);

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

		}, {
			key: '_onMessageButtonClick',
			value: function _onMessageButtonClick(event) {
				// Link value from button.
				var href = $(event.target).attr('href');

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

		}, {
			key: '_onMessageActionClick',
			value: function _onMessageActionClick(event) {
				var actionRemoveClass = 'message-action-remove';

				var $element = $(event.target);

				// Check if the clicked target indicates a message removal.
				var doRemove = $element.hasClass(actionRemoveClass);

				// ID of the message taken from the message item element.
				var id = $element.parents(this.messageItemSelector).data('id');

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

		}, {
			key: '_onWindowClick',
			value: function _onWindowClick(event) {
				var $target = $(event.target);
				var $popover = $(this.popoverSelector);

				var isClickedOnButton = this.$element.has($target).length || this.$element.is($target).length;
				var isPopoverShown = $popover.length;
				var isClickedOutsideOfPopover = !$popover.has($target).length;

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

		}, {
			key: '_onVisibilityCheckboxChange',
			value: function _onVisibilityCheckboxChange(event) {
				var isCheckboxChecked = $(event.target).is(':checked');

				// Toggle hidden messages and mark shown messages as read.
				this._toggleHiddenMessages(isCheckboxChecked)._markMessagesAsRead();

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

		}, {
			key: '_toggleHiddenMessages',
			value: function _toggleHiddenMessages(doShow) {
				// Get all hidden message elements.
				var $hiddenMessages = $(this.messageListSelector).find(this.messageItemHiddenSelector);

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

		}, {
			key: '_setMessageCount',
			value: function _setMessageCount(amount) {
				// If no amount has been passed, the notification count will be hidden.
				if (amount) {
					this.$messageCount.removeClass(this.HIDDEN_CLASS).text(amount);
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

		}, {
			key: '_fillPopoverContent',
			value: function _fillPopoverContent(messages) {
				// Message list element.
				var $messageList = $(this.messageListSelector);

				// Popover element.
				var $popover = $(this.popoverSelector);

				// Show loading spinner.
				var $spinner = this.loadingSpinner.show($popover);

				// Fix for the loading spinner.
				$spinner.css({ 'z-index': 9999 });

				// Message counter.
				var messageCount = 0;

				// Clear message list.
				$messageList.empty();

				// Show info, if there are no messages.
				// Else fill the message list with message items.
				if (!messages.length) {
					var message = {
						message: this.translator.translate('NO_MESSAGES', 'admin_info_boxes'),
						visibility: this.VISIBILITY_ALWAYS_ON,
						status: this.STATUS_READ,
						headline: '',
						type: ''
					};

					$messageList.append(this._createMessageElement(message));
				} else {
					var doesExistSuccessMessage = false;
					var doesExistHiddenMessages = false;

					// Iterate through messages and check if there are hidden ones.
					var _iteratorNormalCompletion2 = true;
					var _didIteratorError2 = false;
					var _iteratorError2 = undefined;

					try {
						for (var _iterator2 = messages[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
							var _message = _step2.value;

							var isSuccessMessage = _message.identifier.search(this.successMessageIdentifierPrefix) !== -1;

							$messageList.append(this._createMessageElement(_message));

							if (_message.status === this.STATUS_HIDDEN) {
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
					} catch (err) {
						_didIteratorError2 = true;
						_iteratorError2 = err;
					} finally {
						try {
							if (!_iteratorNormalCompletion2 && _iterator2.return) {
								_iterator2.return();
							}
						} finally {
							if (_didIteratorError2) {
								throw _iteratorError2;
							}
						}
					}

					this._setMessageCount(messageCount);

					// Hide messages declared as hidden and add visibility checkbox.
					if (doesExistHiddenMessages && !doesExistSuccessMessage) {
						$messageList.append(this._createVisibilityCheckboxElement());
						this._toggleHiddenMessages(false);
					}

					// Remove all other messages if a success message is present.
					if (doesExistSuccessMessage) {
						$(this.messageListSelector).find(this.messageItemSelector).not('[data-identifier*=' + this.successMessageIdentifierPrefix + ']').remove();
					}
				}

				// Do some fade animations for smooth displaying of message items.
				$messageList.children().each(function (index, element) {
					return $(element).hide().fadeIn();
				});

				// Hide loading spinner.
				this.loadingSpinner.hide($spinner);

				return this;
			}

			/**
    * Shows a loading spinner and reloads the messages.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    */

		}, {
			key: '_refreshPopover',
			value: function _refreshPopover() {
				var _this5 = this;

				var $popover = $(this.popoverSelector);
				var $spinner = this.loadingSpinner.show($popover);

				// Fix for the loading spinner.
				$spinner.css({ 'z-index': 9999 });

				// Retrieve messages, fill the message list and kill the loading spinner.
				this.service.getMessages().then(function (messages) {
					return _this5._fillPopoverContent(messages)._markMessagesAsRead();
				}).then(function () {
					return _this5.loadingSpinner.hide($spinner);
				});

				return this;
			}

			/**
    * Marks all visible messages as read.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    */

		}, {
			key: '_markMessagesAsRead',
			value: function _markMessagesAsRead() {
				var _this6 = this;

				// Get message item elements.
				var $messages = $(this.messageListSelector).find(this.messageItemSelector);

				// Set status as read for visible elements only.
				var messageIterator = function messageIterator(index, element) {
					var $message = $(element);
					var data = $message.data();
					var isHidden = $message.hasClass(_this6.STATUS_HIDDEN);

					// Delete by ID if existent.
					if (!isHidden && data.id) {
						_this6.service.setStatus(data.id, _this6.STATUS_READ);
						$message.data('status', _this6.STATUS_READ);
					}

					// Delete success messages.
					if (data.identifier && data.identifier.search(_this6.successMessageIdentifierPrefix) !== -1) {
						_this6.service.deleteByIdentifier(data.identifier);
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

		}, {
			key: '_deleteMessage',
			value: function _deleteMessage(id) {
				var _this7 = this;

				this.service.deleteById(id).then(function () {
					return _this7._refreshPopover();
				});

				return this;
			}

			/**
    * Hides a message and refreshes the message item list.
    *
    * @param {Number} id Message ID.
    *
    * @return {InfoBoxController} Same instance for method chaining.
    */

		}, {
			key: '_hideMessage',
			value: function _hideMessage(id) {
				var _this8 = this;

				this.service.setStatus(id, this.STATUS_HIDDEN).then(function () {
					return _this8._refreshPopover();
				});

				return this;
			}

			/**
    * Creates and returns a new container with a checkbox and label.
    *
    * @return {jQuery} Composed element.
    *
    * @private
    */

		}, {
			key: '_createVisibilityCheckboxElement',
			value: function _createVisibilityCheckboxElement() {
				var $container = $('<div/>', { class: 'visibility-checkbox-container' });
				var $label = $('<label/>', {
					class: 'visibility-checkbox-label',
					text: this.translator.translate('SHOW_ALL', 'admin_info_boxes')
				});
				var $checkbox = $('<input/>', { type: 'checkbox', class: 'visibility-checkbox' });

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

		}, {
			key: '_createMessageElement',
			value: function _createMessageElement(message) {
				// Template elements.
				var $template = $('<div/>', { class: 'message ' + message.type });
				var $headline = $('<p/>', { class: 'message-headline', html: message.headline });
				var $message = $('<p/>', { class: 'message-body', html: message.message });
				var $actionContainer = $('<div/>', { class: 'message-action-container' });
				var $hideAction = $('<span/>', { class: 'message-action message-action-hide fa fa-minus' });
				var $removeAction = $('<span/>', { class: 'message-action message-action-remove fa fa-times' });

				// Is the message a success message?
				var isSuccessMessage = message.identifier ? message.identifier.search(this.successMessageIdentifierPrefix) !== -1 : false;

				// Show remove/hide button, depending on the visibility value and kind of message.
				if (!isSuccessMessage && message.visibility === this.VISIBILITY_REMOVABLE) {
					$actionContainer.append($hideAction, $removeAction).appendTo($template);
				} else if (!isSuccessMessage && message.visibility === this.VISIBILITY_HIDEABLE) {
					$actionContainer.append($hideAction).appendTo($template);
				} else if (isSuccessMessage) {
					$actionContainer.append($removeAction).appendTo($template);
				}

				// Put message data to the message item element as data attributes and append text elements.
				$template.attr('data-status', message.status).attr('data-id', message.id).attr('data-visibility', message.visibility).attr('data-identifier', message.identifier).append($headline, $message);

				// Append button, if a button label is defined.
				if (message.buttonLabel) {
					var $button = $('<a/>', {
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

		}], [{
			key: '_createPopoverTemplateElement',
			value: function _createPopoverTemplateElement() {
				var $popover = $('<div/>', { class: 'popover info-box-popover', role: 'tooltip' });
				var $arrow = $('<div/>', { class: 'arrow' });
				var $title = $('<div/>', { class: 'popover-title' });
				var $content = $('<div/>', { class: 'popover-content info-box-popover-content' });

				$popover.append($arrow, $title, $content);

				return $popover;
			}
		}]);

		return InfoBoxController;
	}();

	// --------------------------------------------------------------------
	// INITIALIZATION
	// --------------------------------------------------------------------

	module.init = function (done) {
		// Dependencies.
		var $element = $(_this9).find('.info-box');
		var ServiceLibrary = jse.libs.info_box.service;
		var LoadingSpinner = jse.libs.loading_spinner;
		var Translator = jse.core.lang;

		// Create a new InfoBox controller instance and set message count.
		var InfoBox = new InfoBoxController(done, $element, ServiceLibrary, LoadingSpinner, Translator);
		InfoBox.checkInitial();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9oZWFkZXIvaW5mb19ib3guanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCJJbmZvQm94Q29udHJvbGxlciIsImRvbmUiLCIkZWxlbWVudCIsIlNlcnZpY2VMaWJyYXJ5IiwiTG9hZGluZ1NwaW5uZXIiLCJUcmFuc2xhdG9yIiwiQ0xPU0VfREVMQVkiLCJGQURFT1VUX0RVUkFUSU9OIiwiSElEREVOX0NMQVNTIiwiQUNUSVZFX0NMQVNTIiwiT1BFTl9NT0RFIiwiU1RBVFVTX05FVyIsIlNUQVRVU19SRUFEIiwiU1RBVFVTX0hJRERFTiIsIlNUQVRVU19ERUxFVEVEIiwiVFlQRV9JTkZPIiwiVFlQRV9XQVJOSU5HIiwiVFlQRV9TVUNDRVNTIiwiVklTSUJJTElUWV9BTFdBWVNfT04iLCJWSVNJQklMSVRZX0hJREVBQkxFIiwiVklTSUJJTElUWV9SRU1PVkFCTEUiLCIkbWVzc2FnZUNvdW50IiwiZmluZCIsInNlcnZpY2UiLCJsb2FkaW5nU3Bpbm5lciIsInRyYW5zbGF0b3IiLCJtZXNzYWdlTGlzdFNlbGVjdG9yIiwibWVzc2FnZUxpc3RDaGVja2JveFNlbGVjdG9yIiwibWVzc2FnZUl0ZW1TZWxlY3RvciIsIm1lc3NhZ2VJdGVtSGlkZGVuU2VsZWN0b3IiLCJtZXNzYWdlSXRlbUJ1dHRvblNlbGVjdG9yIiwibWVzc2FnZUl0ZW1BY3Rpb25TZWxlY3RvciIsInBvcG92ZXJTZWxlY3RvciIsInBvcG92ZXJBcnJvd1NlbGVjdG9yIiwic3VjY2Vzc01lc3NhZ2VJZGVudGlmaWVyUHJlZml4IiwiX2luaXRQb3BvdmVyIiwiaGFuZGxlTWVzc2FnZXMiLCJtZXNzYWdlQ291bnQiLCJoYXNOZXdNZXNzYWdlcyIsIm1lc3NhZ2VzIiwibWVzc2FnZSIsInN0YXR1cyIsImlkZW50aWZpZXIiLCJzZWFyY2giLCJfc2V0TWVzc2FnZUNvdW50IiwiX3Nob3dQb3BvdmVyIiwic2V0VGltZW91dCIsIl9oaWRlUG9wb3ZlciIsImdldE1lc3NhZ2VzIiwidGhlbiIsInBvcG92ZXIiLCJwb3BvdmVyT3B0aW9ucyIsImFuaW1hdGlvbiIsInBsYWNlbWVudCIsImNvbnRlbnQiLCJ0cmlnZ2VyIiwidGVtcGxhdGUiLCJfY3JlYXRlUG9wb3ZlclRlbXBsYXRlRWxlbWVudCIsIm9uIiwiX29uQnV0dG9uQ2xpY2siLCJldmVudCIsIl9vblBvcG92ZXJTaG93biIsImNoZWNrSW5pdGlhbCIsIiQiLCJ3aW5kb3ciLCJfZml4UG9wb3ZlclBvc2l0aW9uIiwiX29uV2luZG93Q2xpY2siLCJBUlJPV19PRkZTRVQiLCJQT1BPVkVSX09GRlNFVCIsIiRwb3BvdmVyIiwiJGFycm93IiwibGVuZ3RoIiwiYXJyb3dPZmZzZXQiLCJvZmZzZXQiLCJsZWZ0IiwicG9wb3Zlck9mZnNldCIsIndpZHRoIiwiJG1lc3NhZ2VMaXN0IiwiYWRkQ2xhc3MiLCJjc3MiLCJ0b3AiLCJoZWlnaHQiLCJfZmlsbFBvcG92ZXJDb250ZW50IiwiX21hcmtNZXNzYWdlc0FzUmVhZCIsIm9mZiIsIl9vbk1lc3NhZ2VCdXR0b25DbGljayIsIl9vbk1lc3NhZ2VBY3Rpb25DbGljayIsIl9vblZpc2liaWxpdHlDaGVja2JveENoYW5nZSIsInJlbW92ZUNsYXNzIiwiaHJlZiIsInRhcmdldCIsImF0dHIiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsInRyaW0iLCJvcGVuIiwiYWN0aW9uUmVtb3ZlQ2xhc3MiLCJkb1JlbW92ZSIsImhhc0NsYXNzIiwiaWQiLCJwYXJlbnRzIiwiX2RlbGV0ZU1lc3NhZ2UiLCJfaGlkZU1lc3NhZ2UiLCIkdGFyZ2V0IiwiaXNDbGlja2VkT25CdXR0b24iLCJoYXMiLCJpcyIsImlzUG9wb3ZlclNob3duIiwiaXNDbGlja2VkT3V0c2lkZU9mUG9wb3ZlciIsImlzQ2hlY2tib3hDaGVja2VkIiwiX3RvZ2dsZUhpZGRlbk1lc3NhZ2VzIiwiZG9TaG93IiwiJGhpZGRlbk1lc3NhZ2VzIiwiYW1vdW50IiwidGV4dCIsIiRzcGlubmVyIiwic2hvdyIsImVtcHR5IiwidHJhbnNsYXRlIiwidmlzaWJpbGl0eSIsImhlYWRsaW5lIiwidHlwZSIsImFwcGVuZCIsIl9jcmVhdGVNZXNzYWdlRWxlbWVudCIsImRvZXNFeGlzdFN1Y2Nlc3NNZXNzYWdlIiwiZG9lc0V4aXN0SGlkZGVuTWVzc2FnZXMiLCJpc1N1Y2Nlc3NNZXNzYWdlIiwiX2NyZWF0ZVZpc2liaWxpdHlDaGVja2JveEVsZW1lbnQiLCJub3QiLCJyZW1vdmUiLCJjaGlsZHJlbiIsImVhY2giLCJpbmRleCIsImVsZW1lbnQiLCJoaWRlIiwiZmFkZUluIiwiJG1lc3NhZ2VzIiwibWVzc2FnZUl0ZXJhdG9yIiwiJG1lc3NhZ2UiLCJpc0hpZGRlbiIsInNldFN0YXR1cyIsImRlbGV0ZUJ5SWRlbnRpZmllciIsImRlbGV0ZUJ5SWQiLCJfcmVmcmVzaFBvcG92ZXIiLCIkY29udGFpbmVyIiwiY2xhc3MiLCIkbGFiZWwiLCIkY2hlY2tib3giLCIkdGVtcGxhdGUiLCIkaGVhZGxpbmUiLCJodG1sIiwiJGFjdGlvbkNvbnRhaW5lciIsIiRoaWRlQWN0aW9uIiwiJHJlbW92ZUFjdGlvbiIsImFwcGVuZFRvIiwiYnV0dG9uTGFiZWwiLCIkYnV0dG9uIiwiYnV0dG9uTGluayIsInJvbGUiLCIkdGl0bGUiLCIkY29udGVudCIsImluaXQiLCJqc2UiLCJsaWJzIiwiaW5mb19ib3giLCJsb2FkaW5nX3NwaW5uZXIiLCJjb3JlIiwibGFuZyIsIkluZm9Cb3giXSwibWFwcGluZ3MiOiI7Ozs7OztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FDQyxVQURELEVBR0MsQ0FDQyxpQkFERCxFQUVJRixHQUFHRyxNQUZQLG9CQUhELEVBUUMsVUFBVUMsSUFBVixFQUFnQjs7QUFFZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQVJlOztBQWFmLEtBQU9GLFNBQVMsRUFBaEI7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFuQmUsS0F3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SUFBSXhLLGlCQUFKLENBQXNCQyxJQUF0QixFQUE0QkMsUUFBNUIsRUFBc0NDLGNBQXRDLEVBQXNEQyxjQUF0RCxFQUFzRUMsVUFBdEUsQ0FBaEI7QUFDQW1LLFVBQVExRyxZQUFSO0FBQ0EsRUFWRDs7QUFZQSxRQUFPakUsTUFBUDtBQUNBLENBdnRCRiIsImZpbGUiOiJsYXlvdXRzL21haW4vaGVhZGVyL2luZm9fYm94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbmZvX2JveC5qcyAyMDE2LTA4LTI2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBJbmZvYm94IENvbnRyb2xsZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnaW5mb19ib3gnLFxuXG5cdFtcblx0XHQnbG9hZGluZ19zcGlubmVyJyxcblx0XHRgJHtneC5zb3VyY2V9L2xpYnMvaW5mb19ib3hgXG5cdF0sXG5cblx0ZnVuY3Rpb24gKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHRcdC8qKlxuXHRcdCAqIE1vZHVsZSBJbnN0YW5jZVxuXHRcdCAqXG5cdFx0ICogQHR5cGUge09iamVjdH1cblx0XHQgKi9cblx0XHRjb25zdCAgbW9kdWxlID0ge307XG5cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEZVTkNUSU9OU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBDbGFzcyByZXByZXNlbnRpbmcgYSBjb250cm9sbGVyIGZvciB0aGUgYWRtaW4gaW5mbyBib3guXG5cdFx0ICpcblx0XHQgKiBQYXNzZWQgZWxlbWVudCB3aWxsIGhhdmUgYW4gZXZlbnQgbGlzdGVuZXIgJ3Nob3c6cG9wb3ZlcicgdG8gY2FsbCB0aGUgcG9wb3Zlci5cblx0XHQgKi9cblx0XHRjbGFzcyBJbmZvQm94Q29udHJvbGxlciB7XG5cdFx0XHQvKipcblx0XHRcdCAqIENyZWF0ZXMgYSBuZXcgaW5mbyBib3ggY29udHJvbGxlci5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0gIHtGdW5jdGlvbn0gZG9uZSAgICAgICAgICAgTW9kdWxlIGZpbmlzaCBjYWxsYmFjayBmdW5jdGlvbi5cblx0XHRcdCAqIEBwYXJhbSAge2pRdWVyeX0gICAkZWxlbWVudCAgICAgICBUcmlnZ2VyIGVsZW1lbnQuXG5cdFx0XHQgKiBAcGFyYW0gIHtPYmplY3R9ICAgU2VydmljZUxpYnJhcnkgSW5mbyBib3ggc2VydmljZSBsaWJyYXJ5LlxuXHRcdFx0ICogQHBhcmFtICB7T2JqZWN0fSAgIExvYWRpbmdTcGlubmVyIExvYWRpbmcgc3Bpbm5lciBsaWJyYXJ5LlxuXHRcdFx0ICogQHBhcmFtICB7T2JqZWN0fSAgIFRyYW5zbGF0b3IgICAgIEpTLUVuZ2luZSB0cmFuc2xhdGlvbiBsaWJyYXJ5LlxuXHRcdFx0ICovXG5cdFx0XHRjb25zdHJ1Y3Rvcihkb25lLCAkZWxlbWVudCwgU2VydmljZUxpYnJhcnksIExvYWRpbmdTcGlubmVyLCBUcmFuc2xhdG9yKSB7XG5cdFx0XHRcdC8vIFBvcG92ZXIgYW5pbWF0aW9uIHRpbWUgdmFsdWVzIChpbiBtcylcblx0XHRcdFx0dGhpcy5DTE9TRV9ERUxBWSA9IDUwMDA7XG5cdFx0XHRcdHRoaXMuRkFERU9VVF9EVVJBVElPTiA9IDY1MDtcblxuXHRcdFx0XHQvLyBDU1MgY2xhc3Nlcy5cblx0XHRcdFx0dGhpcy5ISURERU5fQ0xBU1MgPSAnaGlkZGVuJztcblx0XHRcdFx0dGhpcy5BQ1RJVkVfQ0xBU1MgPSAnYWN0aXZlJztcblxuXHRcdFx0XHQvLyBEZWZhdWx0IG9wZW4gbW9kZSBvbiBsaW5rcy5cblx0XHRcdFx0dGhpcy5PUEVOX01PREUgPSAnX3NlbGYnO1xuXG5cdFx0XHRcdC8vIE1lc3NhZ2UgY29uc3RhbnRzXG5cdFx0XHRcdHRoaXMuU1RBVFVTX05FVyA9ICduZXcnO1xuXHRcdFx0XHR0aGlzLlNUQVRVU19SRUFEID0gJ3JlYWQnO1xuXHRcdFx0XHR0aGlzLlNUQVRVU19ISURERU4gPSAnaGlkZGVuJztcblx0XHRcdFx0dGhpcy5TVEFUVVNfREVMRVRFRCA9ICdkZWxldGVkJztcblx0XHRcdFx0dGhpcy5UWVBFX0lORk8gPSAnaW5mbyc7XG5cdFx0XHRcdHRoaXMuVFlQRV9XQVJOSU5HID0gJ3dhcm5pbmcnO1xuXHRcdFx0XHR0aGlzLlRZUEVfU1VDQ0VTUyA9ICdzdWNjZXNzJztcblx0XHRcdFx0dGhpcy5WSVNJQklMSVRZX0FMV0FZU19PTiA9ICdhbHdheXNvbic7XG5cdFx0XHRcdHRoaXMuVklTSUJJTElUWV9ISURFQUJMRSA9ICdoaWRlYWJsZSc7XG5cdFx0XHRcdHRoaXMuVklTSUJJTElUWV9SRU1PVkFCTEUgPSAncmVtb3ZhYmxlJztcblxuXHRcdFx0XHQvLyBFbGVtZW50c1xuXHRcdFx0XHR0aGlzLiRlbGVtZW50ID0gJGVsZW1lbnQ7XG5cdFx0XHRcdHRoaXMuJG1lc3NhZ2VDb3VudCA9ICRlbGVtZW50LmZpbmQoJy5ub3RpZmljYXRpb24tY291bnQnKTtcblxuXHRcdFx0XHQvLyBMaWJyYXJpZXNcblx0XHRcdFx0dGhpcy5zZXJ2aWNlID0gU2VydmljZUxpYnJhcnk7XG5cdFx0XHRcdHRoaXMubG9hZGluZ1NwaW5uZXIgPSBMb2FkaW5nU3Bpbm5lcjtcblx0XHRcdFx0dGhpcy50cmFuc2xhdG9yID0gVHJhbnNsYXRvcjtcblxuXHRcdFx0XHQvLyBTZWxlY3RvciBzdHJpbmdzXG5cdFx0XHRcdHRoaXMubWVzc2FnZUxpc3RTZWxlY3RvciA9ICcuaW5mby1ib3gtcG9wb3Zlci1jb250ZW50Jztcblx0XHRcdFx0dGhpcy5tZXNzYWdlTGlzdENoZWNrYm94U2VsZWN0b3IgPSAnLnZpc2liaWxpdHktY2hlY2tib3gnO1xuXHRcdFx0XHR0aGlzLm1lc3NhZ2VJdGVtU2VsZWN0b3IgPSAnLm1lc3NhZ2UnO1xuXHRcdFx0XHR0aGlzLm1lc3NhZ2VJdGVtSGlkZGVuU2VsZWN0b3IgPSAnW2RhdGEtc3RhdHVzPVwiaGlkZGVuXCJdJztcblx0XHRcdFx0dGhpcy5tZXNzYWdlSXRlbUJ1dHRvblNlbGVjdG9yID0gJy5tZXNzYWdlLWJ1dHRvbic7XG5cdFx0XHRcdHRoaXMubWVzc2FnZUl0ZW1BY3Rpb25TZWxlY3RvciA9ICcubWVzc2FnZS1hY3Rpb24nO1xuXHRcdFx0XHR0aGlzLnBvcG92ZXJTZWxlY3RvciA9ICcuaW5mby1ib3gtcG9wb3Zlcic7XG5cdFx0XHRcdHRoaXMucG9wb3ZlckFycm93U2VsZWN0b3IgPSAnZGl2LmFycm93JztcblxuXHRcdFx0XHQvLyBBZG1pbiBhY3Rpb24gc3VjY2VzcyBtZXNzYWdlIGlkZW50aWZpZXIgcHJlZml4LlxuXHRcdFx0XHR0aGlzLnN1Y2Nlc3NNZXNzYWdlSWRlbnRpZmllclByZWZpeCA9ICdhZG1pbkFjdGlvblN1Y2Nlc3MtJztcblxuXHRcdFx0XHQvLyBCaW5kIHBvcG92ZXIgdG8gZWxlbWVudC5cblx0XHRcdFx0dGhpcy5faW5pdFBvcG92ZXIoKTtcblxuXHRcdFx0XHQvLyBDYWxsIG1vZHVsZSBmaW5pc2ggY2FsbGJhY2suXG5cdFx0XHRcdGRvbmUoKTtcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDaGVja3MgZm9yIChuZXcpIG1lc3NhZ2VzIGFuZCBzZXRzIHRoZSBtZXNzYWdlIGNvdW50LlxuXHRcdFx0ICpcblx0XHRcdCAqIFRoaXMgbWV0aG9kIGlzIGNhbGxlZCBpZiB0aGUgcGFnZSBpcyBsb2FkZWQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7SW5mb0JveENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHVibGljXG5cdFx0XHQgKi9cblx0XHRcdGNoZWNrSW5pdGlhbCgpIHtcblx0XHRcdFx0Ly8gTWVzc2FnZXMgaXRlcmF0b3IuXG5cdFx0XHRcdGNvbnN0IGhhbmRsZU1lc3NhZ2VzID0gbWVzc2FnZXMgPT4ge1xuXHRcdFx0XHRcdC8vIE1lc3NhZ2UgY291bnRlci5cblx0XHRcdFx0XHRsZXQgbWVzc2FnZUNvdW50ID0gMDtcblxuXHRcdFx0XHRcdC8vIEZsYWcgdG8gaW5kaWNhdGUgaWYgdGhlcmUgYXJlIG5ldyBtZXNzYWdlcz9cblx0XHRcdFx0XHRsZXQgaGFzTmV3TWVzc2FnZXMgPSBmYWxzZTtcblxuXHRcdFx0XHRcdC8vIEl0ZXJhdGUgb3ZlciBlYWNoIG1lc3NhZ2UuXG5cdFx0XHRcdFx0Zm9yIChjb25zdCBtZXNzYWdlIG9mIG1lc3NhZ2VzKSB7XG5cdFx0XHRcdFx0XHQvLyBGaW5kIGEgbmV3IG1lc3NhZ2UuXG5cdFx0XHRcdFx0XHRpZiAobWVzc2FnZS5zdGF0dXMgPT09IHRoaXMuU1RBVFVTX05FVykge1xuXHRcdFx0XHRcdFx0XHRoYXNOZXdNZXNzYWdlcyA9IHRydWU7XG5cdFx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRcdC8vIENvdW50IHRoZSBtZXNzYWdlIGV4Y2x1ZGluZyB0aGUgc3VjY2VzcyBtZXNzYWdlcy5cblx0XHRcdFx0XHRcdGlmIChtZXNzYWdlLmlkZW50aWZpZXIuc2VhcmNoKHRoaXMuc3VjY2Vzc01lc3NhZ2VJZGVudGlmaWVyUHJlZml4KSA9PT0gLTEpIHtcblx0XHRcdFx0XHRcdFx0bWVzc2FnZUNvdW50Kys7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gU2V0IG1lc3NhZ2UgY291bnQuXG5cdFx0XHRcdFx0dGhpcy5fc2V0TWVzc2FnZUNvdW50KG1lc3NhZ2VDb3VudCk7XG5cblx0XHRcdFx0XHQvLyBPcGVuIGluZm8gYm94IGlmIHRoZXJlIGFyZSBuZXcgbWVzc2FnZXMuXG5cdFx0XHRcdFx0aWYgKGhhc05ld01lc3NhZ2VzKSB7XG5cdFx0XHRcdFx0XHR0aGlzLl9zaG93UG9wb3ZlcigpO1xuXHRcdFx0XHRcdFx0c2V0VGltZW91dCgoKSA9PiB0aGlzLl9oaWRlUG9wb3ZlcigpLCB0aGlzLkNMT1NFX0RFTEFZKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH07XG5cblx0XHRcdFx0Ly8gR2V0IG1lc3NhZ2VzIGFuZCBjYWxsIG1lc3NhZ2VzIGl0ZXJhdG9yIGZ1bmN0aW9uLlxuXHRcdFx0XHR0aGlzLnNlcnZpY2Vcblx0XHRcdFx0XHQuZ2V0TWVzc2FnZXMoKVxuXHRcdFx0XHRcdC50aGVuKG1lc3NhZ2VzID0+IGhhbmRsZU1lc3NhZ2VzKG1lc3NhZ2VzKSk7XG5cblx0XHRcdFx0cmV0dXJuIHRoaXM7XG5cdFx0XHR9XG5cblx0XHRcdC8qKlxuXHRcdFx0ICogU2hvd3MgdGhlIHBvcG92ZXIuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7SW5mb0JveENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfc2hvd1BvcG92ZXIoKSB7XG5cdFx0XHRcdC8vIFRyaWdnZXIgcG9wb3ZlciBzaG93IGV2ZW50LlxuXHRcdFx0XHR0aGlzLiRlbGVtZW50LnBvcG92ZXIoJ3Nob3cnKTtcblx0XHRcdFx0cmV0dXJuIHRoaXM7XG5cdFx0XHR9XG5cblx0XHRcdC8qKlxuXHRcdFx0ICogQmluZHMgdGhlIGJvb3RzdHJhcCBwb3BvdmVyIHRvIHRoZSBlbGVtZW50LlxuXHRcdFx0ICpcblx0XHRcdCAqIHtAbGluayBodHRwOi8vZ2V0Ym9vdHN0cmFwLmNvbS9qYXZhc2NyaXB0LyNwb3BvdmVyc31cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9pbml0UG9wb3ZlcigpIHtcblx0XHRcdFx0Ly8gUG9wb3ZlciBpbml0aWFsaXphdGlvbiBvcHRpb25zLlxuXHRcdFx0XHRjb25zdCBwb3BvdmVyT3B0aW9ucyA9IHtcblx0XHRcdFx0XHRhbmltYXRpb246IGZhbHNlLFxuXHRcdFx0XHRcdHBsYWNlbWVudDogJ2JvdHRvbScsXG5cdFx0XHRcdFx0Y29udGVudDogJyAnLFxuXHRcdFx0XHRcdHRyaWdnZXI6ICdtYW51YWwnLFxuXHRcdFx0XHRcdHRlbXBsYXRlOiBJbmZvQm94Q29udHJvbGxlci5fY3JlYXRlUG9wb3ZlclRlbXBsYXRlRWxlbWVudCgpXG5cdFx0XHRcdH07XG5cblx0XHRcdFx0Ly8gSW5pdGlhbGl6ZSBwb3BvdmVyIGFuZCBhdHRhY2ggZXZlbnQgaGFuZGxlcnMuXG5cdFx0XHRcdHRoaXMuJGVsZW1lbnRcblx0XHRcdFx0XHQucG9wb3Zlcihwb3BvdmVyT3B0aW9ucylcblx0XHRcdFx0XHQub24oJ2NsaWNrJywgZXZlbnQgPT4gdGhpcy5fb25CdXR0b25DbGljayhldmVudCkpXG5cdFx0XHRcdFx0Lm9uKCdzaG93bi5icy5wb3BvdmVyJywgZXZlbnQgPT4gdGhpcy5fb25Qb3BvdmVyU2hvd24oZXZlbnQpKVxuXHRcdFx0XHRcdC5vbignc2hvdzpwb3BvdmVyJywgZXZlbnQgPT4gdGhpcy5fc2hvd1BvcG92ZXIoKSlcblx0XHRcdFx0XHQub24oJ3JlZnJlc2g6bWVzc2FnZXMnLCBldmVudCA9PiB0aGlzLmNoZWNrSW5pdGlhbCgpKTtcblxuXHRcdFx0XHQvLyBBdHRhY2ggZXZlbnQgbGlzdGVuZXJzIHRvIHRoZSB3aW5kb3cuXG5cdFx0XHRcdCQod2luZG93KVxuXHRcdFx0XHRcdC5vbigncmVzaXplJywgKCkgPT4gdGhpcy5fZml4UG9wb3ZlclBvc2l0aW9uKCkpXG5cdFx0XHRcdFx0Lm9uKCdjbGljaycsIGV2ZW50ID0+IHRoaXMuX29uV2luZG93Q2xpY2soZXZlbnQpKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaXhlcyB0aGUgcG9zaXRpb24gb2YgdGhlIHBvcG92ZXIgZGVwZW5kaW5nIG9uIHRoZSB3aW5kb3cgc2l6ZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9maXhQb3BvdmVyUG9zaXRpb24oKSB7XG5cdFx0XHRcdC8vIE9mZnNldCBjb3JyZWN0aW9uIHZhbHVlcy5cblx0XHRcdFx0Y29uc3QgQVJST1dfT0ZGU0VUID0gMjQwO1xuXHRcdFx0XHRjb25zdCBQT1BPVkVSX09GRlNFVCA9IDI1MDtcblxuXHRcdFx0XHRjb25zdCAkcG9wb3ZlciA9ICQodGhpcy5wb3BvdmVyU2VsZWN0b3IpO1xuXHRcdFx0XHRjb25zdCAkYXJyb3cgPSAkcG9wb3Zlci5maW5kKHRoaXMucG9wb3ZlckFycm93U2VsZWN0b3IpO1xuXG5cdFx0XHRcdC8vIEZpeCB0aGUgb2Zmc2V0IGZvciB0aGUgYWZmZWN0ZWQgZWxlbWVudHMsIGlmIHBvcG92ZXIgaXMgb3Blbi5cblx0XHRcdFx0aWYgKCRwb3BvdmVyLmxlbmd0aCkge1xuXHRcdFx0XHRcdGNvbnN0IGFycm93T2Zmc2V0ID0gJHBvcG92ZXIub2Zmc2V0KCkubGVmdCArIEFSUk9XX09GRlNFVDtcblx0XHRcdFx0XHRjb25zdCBwb3BvdmVyT2Zmc2V0ID0gdGhpcy4kZWxlbWVudC5vZmZzZXQoKS5sZWZ0IC0gUE9QT1ZFUl9PRkZTRVQgKyAodGhpcy4kZWxlbWVudC53aWR0aCgpIC8gMik7XG5cblx0XHRcdFx0XHQkYXJyb3cub2Zmc2V0KHtsZWZ0OiBhcnJvd09mZnNldH0pO1xuXHRcdFx0XHRcdCRwb3BvdmVyLm9mZnNldCh7bGVmdDogcG9wb3Zlck9mZnNldH0pO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0cmV0dXJuIHRoaXM7XG5cdFx0XHR9XG5cblx0XHRcdC8qKlxuXHRcdFx0ICogSGFuZGxlcyBldmVudCBmb3IgYnV0dG9uIGNsaWNrIGFjdGlvbi5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9vbkJ1dHRvbkNsaWNrKCkge1xuXHRcdFx0XHRjb25zdCAkcG9wb3ZlciA9ICQodGhpcy5wb3BvdmVyU2VsZWN0b3IpO1xuXG5cdFx0XHRcdC8vIFRvZ2dsZSBwb3BvdmVyLCBiYXNlZCBvbiB0aGUgdmlzaWJpbGl0eSBvZiB0aGUgZWxlbWVudC5cblx0XHRcdFx0aWYgKCRwb3BvdmVyLmxlbmd0aCkge1xuXHRcdFx0XHRcdHRoaXMuX2hpZGVQb3BvdmVyKCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0dGhpcy5fc2hvd1BvcG92ZXIoKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEhhbmRsZXMgZXZlbnQgZm9yIHNob3duIHBvcG92ZXIgYWN0aW9uLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X29uUG9wb3ZlclNob3duKCkge1xuXHRcdFx0XHRjb25zdCAkbWVzc2FnZUxpc3QgPSAkKHRoaXMubWVzc2FnZUxpc3RTZWxlY3Rvcik7XG5cdFx0XHRcdGNvbnN0ICRwb3BvdmVyID0gJCh0aGlzLnBvcG92ZXJTZWxlY3Rvcik7XG5cblx0XHRcdFx0Ly8gSGlkZSB0aGUgcG9wb3ZlciBvbiB0aGUgdG9wLlxuXHRcdFx0XHQvLyBUaGlzIGlzIG5lZWRlZCB0byBoYW5kbGUgdGhlIHBvcG92ZXIgYW5pbWF0aW9uLCBoYW5kbGVkIGJ5IENTUy5cblx0XHRcdFx0JHBvcG92ZXJcblx0XHRcdFx0XHQuYWRkQ2xhc3ModGhpcy5BQ1RJVkVfQ0xBU1MpXG5cdFx0XHRcdFx0LmNzcyh7dG9wOiAkcG9wb3Zlci5oZWlnaHQoKSAqIC0xfSk7XG5cblx0XHRcdFx0Ly8gRml4IHRoZSBwb3BvdmVyIHBvc2l0aW9uLCBmZXRjaCBhbmQgc2hvdyB0aGUgbWVzc2FnZXMgYW5kIG1hcmsgc2hvd24gbWVzc2FnZXMgYXMgcmVhZC5cblx0XHRcdFx0dGhpc1xuXHRcdFx0XHRcdC5fZml4UG9wb3ZlclBvc2l0aW9uKClcblx0XHRcdFx0XHQuc2VydmljZS5nZXRNZXNzYWdlcygpXG5cdFx0XHRcdFx0LnRoZW4obWVzc2FnZXMgPT4gdGhpcy5fZmlsbFBvcG92ZXJDb250ZW50KG1lc3NhZ2VzKS5fbWFya01lc3NhZ2VzQXNSZWFkKCkpO1xuXG5cdFx0XHRcdC8vIEF0dGFjaCBldmVudCBoYW5kbGVycyB0byBwb3BvdmVyLlxuXHRcdFx0XHQkbWVzc2FnZUxpc3Rcblx0XHRcdFx0XHQub2ZmKCdjbGljayBjaGFuZ2UnKVxuXHRcdFx0XHRcdC5vbignY2xpY2snLCB0aGlzLm1lc3NhZ2VJdGVtQnV0dG9uU2VsZWN0b3IsIGV2ZW50ID0+IHRoaXMuX29uTWVzc2FnZUJ1dHRvbkNsaWNrKGV2ZW50KSlcblx0XHRcdFx0XHQub24oJ2NsaWNrJywgdGhpcy5tZXNzYWdlSXRlbUFjdGlvblNlbGVjdG9yLCBldmVudCA9PiB0aGlzLl9vbk1lc3NhZ2VBY3Rpb25DbGljayhldmVudCkpXG5cdFx0XHRcdFx0Lm9uKCdjaGFuZ2UnLCB0aGlzLm1lc3NhZ2VMaXN0Q2hlY2tib3hTZWxlY3RvciwgZXZlbnQgPT4gdGhpcy5fb25WaXNpYmlsaXR5Q2hlY2tib3hDaGFuZ2UoZXZlbnQpKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIaWRlcyB0aGUgcG9wb3Zlci5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9oaWRlUG9wb3ZlcigpIHtcblx0XHRcdFx0Ly8gUmVtb3ZlIGFjdGl2ZSBjbGFzcyB0byBzdGFydCBhbmltYXRpb24uXG5cdFx0XHRcdCQodGhpcy5wb3BvdmVyU2VsZWN0b3IpLnJlbW92ZUNsYXNzKHRoaXMuQUNUSVZFX0NMQVNTKTtcblxuXHRcdFx0XHQvLyBEZWZlcnJlZCBmaXJlIG9mIHRoZSBoaWRlIGV2ZW50IHRvIGJlIHN1cmUgdGhhdCB0aGUgYW5pbWF0aW9uIGlzIGNvbXBsZXRlLlxuXHRcdFx0XHRzZXRUaW1lb3V0KCgpID0+IHRoaXMuJGVsZW1lbnQucG9wb3ZlcignaGlkZScpLCB0aGlzLkZBREVPVVRfRFVSQVRJT04pO1xuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEhhbmRsZXMgZXZlbnQgZm9yIGNsaWNrZWQgbWVzc2FnZSBidXR0b25zLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBGaXJlZCBldmVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9vbk1lc3NhZ2VCdXR0b25DbGljayhldmVudCkge1xuXHRcdFx0XHQvLyBMaW5rIHZhbHVlIGZyb20gYnV0dG9uLlxuXHRcdFx0XHRjb25zdCBocmVmID0gJChldmVudC50YXJnZXQpLmF0dHIoJ2hyZWYnKTtcblxuXHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0XHQvLyBPcGVuIGxpbmsgaWYgZXhpc3RzLlxuXHRcdFx0XHRpZiAoaHJlZiAmJiBocmVmLnRyaW0oKS5sZW5ndGgpIHtcblx0XHRcdFx0XHR3aW5kb3cub3BlbihocmVmLCB0aGlzLk9QRU5fTU9ERSk7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIYW5kbGVzIGV2ZW50IGZvciBjbGlja2VkIG1lc3NhZ2UgYWN0aW9uIHRyaWdnZXIgZWxlbWVudHMuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IEZpcmVkIGV2ZW50LlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X29uTWVzc2FnZUFjdGlvbkNsaWNrKGV2ZW50KSB7XG5cdFx0XHRcdGNvbnN0IGFjdGlvblJlbW92ZUNsYXNzID0gJ21lc3NhZ2UtYWN0aW9uLXJlbW92ZSc7XG5cblx0XHRcdFx0Y29uc3QgJGVsZW1lbnQgPSAkKGV2ZW50LnRhcmdldCk7XG5cblx0XHRcdFx0Ly8gQ2hlY2sgaWYgdGhlIGNsaWNrZWQgdGFyZ2V0IGluZGljYXRlcyBhIG1lc3NhZ2UgcmVtb3ZhbC5cblx0XHRcdFx0Y29uc3QgZG9SZW1vdmUgPSAkZWxlbWVudC5oYXNDbGFzcyhhY3Rpb25SZW1vdmVDbGFzcyk7XG5cblx0XHRcdFx0Ly8gSUQgb2YgdGhlIG1lc3NhZ2UgdGFrZW4gZnJvbSB0aGUgbWVzc2FnZSBpdGVtIGVsZW1lbnQuXG5cdFx0XHRcdGNvbnN0IGlkID0gJGVsZW1lbnQucGFyZW50cyh0aGlzLm1lc3NhZ2VJdGVtU2VsZWN0b3IpLmRhdGEoJ2lkJyk7XG5cblx0XHRcdFx0Ly8gRGVsZXRlL2hpZGUgbWVzc2FnZSBkZXBlbmRpbmcgb24gdGhlIGNsaWNrZWQgdGFyZ2V0LlxuXHRcdFx0XHRpZiAoZG9SZW1vdmUpIHtcblx0XHRcdFx0XHR0aGlzLl9kZWxldGVNZXNzYWdlKGlkKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHR0aGlzLl9oaWRlTWVzc2FnZShpZCk7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIYW5kbGVzIGV2ZW50IGZvciBjbGljayBhY3Rpb24gaW5zaWRlIHRoZSB3aW5kb3cuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IEZpcmVkIGV2ZW50LlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X29uV2luZG93Q2xpY2soZXZlbnQpIHtcblx0XHRcdFx0Y29uc3QgJHRhcmdldCA9ICQoZXZlbnQudGFyZ2V0KTtcblx0XHRcdFx0Y29uc3QgJHBvcG92ZXIgPSAkKHRoaXMucG9wb3ZlclNlbGVjdG9yKTtcblxuXHRcdFx0XHRjb25zdCBpc0NsaWNrZWRPbkJ1dHRvbiA9IHRoaXMuJGVsZW1lbnQuaGFzKCR0YXJnZXQpLmxlbmd0aCB8fCB0aGlzLiRlbGVtZW50LmlzKCR0YXJnZXQpLmxlbmd0aDtcblx0XHRcdFx0Y29uc3QgaXNQb3BvdmVyU2hvd24gPSAkcG9wb3Zlci5sZW5ndGg7XG5cdFx0XHRcdGNvbnN0IGlzQ2xpY2tlZE91dHNpZGVPZlBvcG92ZXIgPSAhJHBvcG92ZXIuaGFzKCR0YXJnZXQpLmxlbmd0aDtcblxuXHRcdFx0XHQvLyBPbmx5IGhpZGUgZHJvcGRvd24sIGlmIGNsaWNrZWQgdGFyZ2V0IGlzIG5vdCB3aXRoaW4gdGhlIHBvcG92ZXIgYXJlYS5cblx0XHRcdFx0aWYgKGlzQ2xpY2tlZE91dHNpZGVPZlBvcG92ZXIgJiYgaXNQb3BvdmVyU2hvd24gJiYgIWlzQ2xpY2tlZE9uQnV0dG9uKSB7XG5cdFx0XHRcdFx0dGhpcy5faGlkZVBvcG92ZXIoKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEhhbmRsZXMgZXZlbnQgZm9yIGNoZWNrYm94IGNoYW5nZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7SW5mb0JveENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfb25WaXNpYmlsaXR5Q2hlY2tib3hDaGFuZ2UoZXZlbnQpIHtcblx0XHRcdFx0Y29uc3QgaXNDaGVja2JveENoZWNrZWQgPSAkKGV2ZW50LnRhcmdldCkuaXMoJzpjaGVja2VkJyk7XG5cblx0XHRcdFx0Ly8gVG9nZ2xlIGhpZGRlbiBtZXNzYWdlcyBhbmQgbWFyayBzaG93biBtZXNzYWdlcyBhcyByZWFkLlxuXHRcdFx0XHR0aGlzXG5cdFx0XHRcdFx0Ll90b2dnbGVIaWRkZW5NZXNzYWdlcyhpc0NoZWNrYm94Q2hlY2tlZClcblx0XHRcdFx0XHQuX21hcmtNZXNzYWdlc0FzUmVhZCgpO1xuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIFNob3dzL2hpZGVzIHRoZSBtZXNzYWdlcyBkZWNsYXJlZCBhcyBoaWRkZW4gdmlhIHRoZSBkYXRhIGF0dHJpYnV0ZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge0Jvb2xlYW59IGRvU2hvdyBTaG93IHRoZSBoaWRkZW4gbWVzc2FnZXM/XG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7SW5mb0JveENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfdG9nZ2xlSGlkZGVuTWVzc2FnZXMoZG9TaG93KSB7XG5cdFx0XHRcdC8vIEdldCBhbGwgaGlkZGVuIG1lc3NhZ2UgZWxlbWVudHMuXG5cdFx0XHRcdGNvbnN0ICRoaWRkZW5NZXNzYWdlcyA9ICQodGhpcy5tZXNzYWdlTGlzdFNlbGVjdG9yKS5maW5kKHRoaXMubWVzc2FnZUl0ZW1IaWRkZW5TZWxlY3Rvcik7XG5cblx0XHRcdFx0aWYgKGRvU2hvdykge1xuXHRcdFx0XHRcdCRoaWRkZW5NZXNzYWdlcy5yZW1vdmVDbGFzcyh0aGlzLkhJRERFTl9DTEFTUyk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGhpZGRlbk1lc3NhZ2VzLmFkZENsYXNzKHRoaXMuSElEREVOX0NMQVNTKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIFNldHMgdGhlIGFtb3VudCBvZiBtZXNzYWdlcyBpbnRvIHRoZSBpY29uLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7TnVtYmVyfSBhbW91bnQgTWVzc2FnZSBjb3VudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9zZXRNZXNzYWdlQ291bnQoYW1vdW50KSB7XG5cdFx0XHRcdC8vIElmIG5vIGFtb3VudCBoYXMgYmVlbiBwYXNzZWQsIHRoZSBub3RpZmljYXRpb24gY291bnQgd2lsbCBiZSBoaWRkZW4uXG5cdFx0XHRcdGlmIChhbW91bnQpIHtcblx0XHRcdFx0XHR0aGlzLiRtZXNzYWdlQ291bnRcblx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcyh0aGlzLkhJRERFTl9DTEFTUylcblx0XHRcdFx0XHRcdC50ZXh0KGFtb3VudCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0dGhpcy4kbWVzc2FnZUNvdW50LmFkZENsYXNzKHRoaXMuSElEREVOX0NMQVNTKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbGxzIHRoZSBjb250ZW50IGVsZW1lbnQgd2l0aCBhbGwgbWVzc2FnZXMgcHJvdmlkZWQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHBhcmFtIHtPYmplY3R9IG1lc3NhZ2VzIE1lc3NhZ2VzLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0X2ZpbGxQb3BvdmVyQ29udGVudChtZXNzYWdlcykge1xuXHRcdFx0XHQvLyBNZXNzYWdlIGxpc3QgZWxlbWVudC5cblx0XHRcdFx0Y29uc3QgJG1lc3NhZ2VMaXN0ID0gJCh0aGlzLm1lc3NhZ2VMaXN0U2VsZWN0b3IpO1xuXG5cdFx0XHRcdC8vIFBvcG92ZXIgZWxlbWVudC5cblx0XHRcdFx0Y29uc3QgJHBvcG92ZXIgPSAkKHRoaXMucG9wb3ZlclNlbGVjdG9yKTtcblxuXHRcdFx0XHQvLyBTaG93IGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdFx0Y29uc3QgJHNwaW5uZXIgPSB0aGlzLmxvYWRpbmdTcGlubmVyLnNob3coJHBvcG92ZXIpO1xuXG5cdFx0XHRcdC8vIEZpeCBmb3IgdGhlIGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdFx0JHNwaW5uZXIuY3NzKHsnei1pbmRleCc6IDk5OTl9KTtcblxuXHRcdFx0XHQvLyBNZXNzYWdlIGNvdW50ZXIuXG5cdFx0XHRcdGxldCBtZXNzYWdlQ291bnQgPSAwO1xuXG5cdFx0XHRcdC8vIENsZWFyIG1lc3NhZ2UgbGlzdC5cblx0XHRcdFx0JG1lc3NhZ2VMaXN0LmVtcHR5KCk7XG5cblx0XHRcdFx0Ly8gU2hvdyBpbmZvLCBpZiB0aGVyZSBhcmUgbm8gbWVzc2FnZXMuXG5cdFx0XHRcdC8vIEVsc2UgZmlsbCB0aGUgbWVzc2FnZSBsaXN0IHdpdGggbWVzc2FnZSBpdGVtcy5cblx0XHRcdFx0aWYgKCFtZXNzYWdlcy5sZW5ndGgpIHtcblx0XHRcdFx0XHRjb25zdCBtZXNzYWdlID0ge1xuXHRcdFx0XHRcdFx0bWVzc2FnZTogdGhpcy50cmFuc2xhdG9yLnRyYW5zbGF0ZSgnTk9fTUVTU0FHRVMnLCAnYWRtaW5faW5mb19ib3hlcycpLFxuXHRcdFx0XHRcdFx0dmlzaWJpbGl0eTogdGhpcy5WSVNJQklMSVRZX0FMV0FZU19PTixcblx0XHRcdFx0XHRcdHN0YXR1czogdGhpcy5TVEFUVVNfUkVBRCxcblx0XHRcdFx0XHRcdGhlYWRsaW5lOiAnJyxcblx0XHRcdFx0XHRcdHR5cGU6ICcnXG5cdFx0XHRcdFx0fTtcblxuXHRcdFx0XHRcdCRtZXNzYWdlTGlzdC5hcHBlbmQodGhpcy5fY3JlYXRlTWVzc2FnZUVsZW1lbnQobWVzc2FnZSkpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdGxldCBkb2VzRXhpc3RTdWNjZXNzTWVzc2FnZSA9IGZhbHNlO1xuXHRcdFx0XHRcdGxldCBkb2VzRXhpc3RIaWRkZW5NZXNzYWdlcyA9IGZhbHNlO1xuXG5cdFx0XHRcdFx0Ly8gSXRlcmF0ZSB0aHJvdWdoIG1lc3NhZ2VzIGFuZCBjaGVjayBpZiB0aGVyZSBhcmUgaGlkZGVuIG9uZXMuXG5cdFx0XHRcdFx0Zm9yIChjb25zdCBtZXNzYWdlIG9mIG1lc3NhZ2VzKSB7XG5cdFx0XHRcdFx0XHRjb25zdCBpc1N1Y2Nlc3NNZXNzYWdlID0gbWVzc2FnZS5pZGVudGlmaWVyLnNlYXJjaCh0aGlzLnN1Y2Nlc3NNZXNzYWdlSWRlbnRpZmllclByZWZpeCkgIT09IC0xO1xuXG5cdFx0XHRcdFx0XHQkbWVzc2FnZUxpc3QuYXBwZW5kKHRoaXMuX2NyZWF0ZU1lc3NhZ2VFbGVtZW50KG1lc3NhZ2UpKTtcblxuXHRcdFx0XHRcdFx0aWYgKG1lc3NhZ2Uuc3RhdHVzID09PSB0aGlzLlNUQVRVU19ISURERU4pIHtcblx0XHRcdFx0XHRcdFx0ZG9lc0V4aXN0SGlkZGVuTWVzc2FnZXMgPSB0cnVlO1xuXHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHQvLyBDb3VudCB0aGUgbWVzc2FnZSBleGNsdWRpbmcgdGhlIHN1Y2Nlc3MgbWVzc2FnZXMuXG5cdFx0XHRcdFx0XHRpZiAoaXNTdWNjZXNzTWVzc2FnZSkge1xuXHRcdFx0XHRcdFx0XHRkb2VzRXhpc3RTdWNjZXNzTWVzc2FnZSA9IHRydWU7XG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRtZXNzYWdlQ291bnQrKztcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvLyBTZXQgdGhlIG1lc3NhZ2UgY291bnQuXG5cdFx0XHRcdFx0dGhpcy5fc2V0TWVzc2FnZUNvdW50KG1lc3NhZ2VDb3VudCk7XG5cblx0XHRcdFx0XHQvLyBIaWRlIG1lc3NhZ2VzIGRlY2xhcmVkIGFzIGhpZGRlbiBhbmQgYWRkIHZpc2liaWxpdHkgY2hlY2tib3guXG5cdFx0XHRcdFx0aWYgKGRvZXNFeGlzdEhpZGRlbk1lc3NhZ2VzICYmICFkb2VzRXhpc3RTdWNjZXNzTWVzc2FnZSkge1xuXHRcdFx0XHRcdFx0JG1lc3NhZ2VMaXN0LmFwcGVuZCh0aGlzLl9jcmVhdGVWaXNpYmlsaXR5Q2hlY2tib3hFbGVtZW50KCkpO1xuXHRcdFx0XHRcdFx0dGhpcy5fdG9nZ2xlSGlkZGVuTWVzc2FnZXMoZmFsc2UpO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIFJlbW92ZSBhbGwgb3RoZXIgbWVzc2FnZXMgaWYgYSBzdWNjZXNzIG1lc3NhZ2UgaXMgcHJlc2VudC5cblx0XHRcdFx0XHRpZiAoZG9lc0V4aXN0U3VjY2Vzc01lc3NhZ2UpIHtcblx0XHRcdFx0XHRcdCQodGhpcy5tZXNzYWdlTGlzdFNlbGVjdG9yKVxuXHRcdFx0XHRcdFx0XHQuZmluZCh0aGlzLm1lc3NhZ2VJdGVtU2VsZWN0b3IpXG5cdFx0XHRcdFx0XHRcdC5ub3QoYFtkYXRhLWlkZW50aWZpZXIqPSR7dGhpcy5zdWNjZXNzTWVzc2FnZUlkZW50aWZpZXJQcmVmaXh9XWApXG5cdFx0XHRcdFx0XHRcdC5yZW1vdmUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblxuXHRcdFx0XHQvLyBEbyBzb21lIGZhZGUgYW5pbWF0aW9ucyBmb3Igc21vb3RoIGRpc3BsYXlpbmcgb2YgbWVzc2FnZSBpdGVtcy5cblx0XHRcdFx0JG1lc3NhZ2VMaXN0XG5cdFx0XHRcdFx0LmNoaWxkcmVuKClcblx0XHRcdFx0XHQuZWFjaCgoaW5kZXgsIGVsZW1lbnQpID0+ICQoZWxlbWVudCkuaGlkZSgpLmZhZGVJbigpKTtcblxuXHRcdFx0XHQvLyBIaWRlIGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdFx0dGhpcy5sb2FkaW5nU3Bpbm5lci5oaWRlKCRzcGlubmVyKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTaG93cyBhIGxvYWRpbmcgc3Bpbm5lciBhbmQgcmVsb2FkcyB0aGUgbWVzc2FnZXMuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7SW5mb0JveENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cblx0XHRcdCAqL1xuXHRcdFx0X3JlZnJlc2hQb3BvdmVyKCkge1xuXHRcdFx0XHRjb25zdCAkcG9wb3ZlciA9ICQodGhpcy5wb3BvdmVyU2VsZWN0b3IpO1xuXHRcdFx0XHRjb25zdCAkc3Bpbm5lciA9IHRoaXMubG9hZGluZ1NwaW5uZXIuc2hvdygkcG9wb3Zlcik7XG5cblx0XHRcdFx0Ly8gRml4IGZvciB0aGUgbG9hZGluZyBzcGlubmVyLlxuXHRcdFx0XHQkc3Bpbm5lci5jc3Moeyd6LWluZGV4JzogOTk5OX0pO1xuXG5cdFx0XHRcdC8vIFJldHJpZXZlIG1lc3NhZ2VzLCBmaWxsIHRoZSBtZXNzYWdlIGxpc3QgYW5kIGtpbGwgdGhlIGxvYWRpbmcgc3Bpbm5lci5cblx0XHRcdFx0dGhpc1xuXHRcdFx0XHRcdC5zZXJ2aWNlLmdldE1lc3NhZ2VzKClcblx0XHRcdFx0XHQudGhlbihtZXNzYWdlcyA9PiB0aGlzLl9maWxsUG9wb3ZlckNvbnRlbnQobWVzc2FnZXMpLl9tYXJrTWVzc2FnZXNBc1JlYWQoKSlcblx0XHRcdFx0XHQudGhlbigoKSA9PiB0aGlzLmxvYWRpbmdTcGlubmVyLmhpZGUoJHNwaW5uZXIpKTtcblxuXHRcdFx0XHRyZXR1cm4gdGhpcztcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNYXJrcyBhbGwgdmlzaWJsZSBtZXNzYWdlcyBhcyByZWFkLlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKi9cblx0XHRcdF9tYXJrTWVzc2FnZXNBc1JlYWQoKSB7XG5cdFx0XHRcdC8vIEdldCBtZXNzYWdlIGl0ZW0gZWxlbWVudHMuXG5cdFx0XHRcdGNvbnN0ICRtZXNzYWdlcyA9ICQodGhpcy5tZXNzYWdlTGlzdFNlbGVjdG9yKS5maW5kKHRoaXMubWVzc2FnZUl0ZW1TZWxlY3Rvcik7XG5cblx0XHRcdFx0Ly8gU2V0IHN0YXR1cyBhcyByZWFkIGZvciB2aXNpYmxlIGVsZW1lbnRzIG9ubHkuXG5cdFx0XHRcdGNvbnN0IG1lc3NhZ2VJdGVyYXRvciA9IChpbmRleCwgZWxlbWVudCkgPT4ge1xuXHRcdFx0XHRcdGNvbnN0ICRtZXNzYWdlID0gJChlbGVtZW50KTtcblx0XHRcdFx0XHRjb25zdCBkYXRhID0gJG1lc3NhZ2UuZGF0YSgpO1xuXHRcdFx0XHRcdGNvbnN0IGlzSGlkZGVuID0gJG1lc3NhZ2UuaGFzQ2xhc3ModGhpcy5TVEFUVVNfSElEREVOKTtcblxuXHRcdFx0XHRcdC8vIERlbGV0ZSBieSBJRCBpZiBleGlzdGVudC5cblx0XHRcdFx0XHRpZiAoIWlzSGlkZGVuICYmIGRhdGEuaWQpIHtcblx0XHRcdFx0XHRcdHRoaXMuc2VydmljZS5zZXRTdGF0dXMoZGF0YS5pZCwgdGhpcy5TVEFUVVNfUkVBRCk7XG5cdFx0XHRcdFx0XHQkbWVzc2FnZS5kYXRhKCdzdGF0dXMnLCB0aGlzLlNUQVRVU19SRUFEKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHQvLyBEZWxldGUgc3VjY2VzcyBtZXNzYWdlcy5cblx0XHRcdFx0XHRpZiAoZGF0YS5pZGVudGlmaWVyICYmIGRhdGEuaWRlbnRpZmllci5zZWFyY2godGhpcy5zdWNjZXNzTWVzc2FnZUlkZW50aWZpZXJQcmVmaXgpICE9PSAtMSkge1xuXHRcdFx0XHRcdFx0dGhpcy5zZXJ2aWNlLmRlbGV0ZUJ5SWRlbnRpZmllcihkYXRhLmlkZW50aWZpZXIpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fTtcblxuXHRcdFx0XHQvLyBJdGVyYXRlIG92ZXIgZWFjaCBtZXNzYWdlLlxuXHRcdFx0XHQkbWVzc2FnZXMuZWFjaChtZXNzYWdlSXRlcmF0b3IpO1xuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIERlbGV0ZXMgYSBtZXNzYWdlIGFuZCByZWZyZXNoZXMgdGhlIG1lc3NhZ2UgaXRlbSBsaXN0LlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7TnVtYmVyfSBpZCBNZXNzYWdlIElELlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge0luZm9Cb3hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXG5cdFx0XHQgKi9cblx0XHRcdF9kZWxldGVNZXNzYWdlKGlkKSB7XG5cdFx0XHRcdHRoaXMuc2VydmljZVxuXHRcdFx0XHRcdC5kZWxldGVCeUlkKGlkKVxuXHRcdFx0XHRcdC50aGVuKCgpID0+IHRoaXMuX3JlZnJlc2hQb3BvdmVyKCkpO1xuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIEhpZGVzIGEgbWVzc2FnZSBhbmQgcmVmcmVzaGVzIHRoZSBtZXNzYWdlIGl0ZW0gbGlzdC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcGFyYW0ge051bWJlcn0gaWQgTWVzc2FnZSBJRC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtJbmZvQm94Q29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxuXHRcdFx0ICovXG5cdFx0XHRfaGlkZU1lc3NhZ2UoaWQpIHtcblx0XHRcdFx0dGhpcy5zZXJ2aWNlXG5cdFx0XHRcdFx0LnNldFN0YXR1cyhpZCwgdGhpcy5TVEFUVVNfSElEREVOKVxuXHRcdFx0XHRcdC50aGVuKCgpID0+IHRoaXMuX3JlZnJlc2hQb3BvdmVyKCkpO1xuXG5cdFx0XHRcdHJldHVybiB0aGlzO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIENyZWF0ZXMgYW5kIHJldHVybnMgYSBuZXcgY29udGFpbmVyIHdpdGggYSBjaGVja2JveCBhbmQgbGFiZWwuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHJldHVybiB7alF1ZXJ5fSBDb21wb3NlZCBlbGVtZW50LlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdF9jcmVhdGVWaXNpYmlsaXR5Q2hlY2tib3hFbGVtZW50KCkge1xuXHRcdFx0XHRjb25zdCAkY29udGFpbmVyID0gJCgnPGRpdi8+Jywge2NsYXNzOiAndmlzaWJpbGl0eS1jaGVja2JveC1jb250YWluZXInfSk7XG5cdFx0XHRcdGNvbnN0ICRsYWJlbCA9ICQoJzxsYWJlbC8+Jywge1xuXHRcdFx0XHRcdGNsYXNzOiAndmlzaWJpbGl0eS1jaGVja2JveC1sYWJlbCcsXG5cdFx0XHRcdFx0dGV4dDogdGhpcy50cmFuc2xhdG9yLnRyYW5zbGF0ZSgnU0hPV19BTEwnLCAnYWRtaW5faW5mb19ib3hlcycpXG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRjb25zdCAkY2hlY2tib3ggPSAkKCc8aW5wdXQvPicsIHt0eXBlOiAnY2hlY2tib3gnLCBjbGFzczogJ3Zpc2liaWxpdHktY2hlY2tib3gnfSk7XG5cblx0XHRcdFx0JGNvbnRhaW5lci5hcHBlbmQoJGNoZWNrYm94LCAkbGFiZWwpO1xuXG5cdFx0XHRcdHJldHVybiAkY29udGFpbmVyO1xuXHRcdFx0fVxuXG5cdFx0XHQvKipcblx0XHRcdCAqIENyZWF0ZXMgYW5kIHJldHVybnMgYSBuZXcgSFRNTCBlbGVtZW50IGNvbnRhaW5pbmcgdGhlIG1lc3NhZ2UgY29udGVudHMuXG5cdFx0XHQgKlxuXHRcdFx0ICogRWxlbWVudCByZXByZXNlbnRzIGEgbWVzc2FnZSBpdGVtLlxuXHRcdFx0ICpcblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBtZXNzYWdlIE1lc3NhZ2Ugb2JqZWN0LlxuXHRcdFx0ICpcblx0XHRcdCAqIEByZXR1cm4ge2pRdWVyeX0gQ29tcG9zZWQgZWxlbWVudC5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHRfY3JlYXRlTWVzc2FnZUVsZW1lbnQobWVzc2FnZSkge1xuXHRcdFx0XHQvLyBUZW1wbGF0ZSBlbGVtZW50cy5cblx0XHRcdFx0Y29uc3QgJHRlbXBsYXRlID0gJCgnPGRpdi8+Jywge2NsYXNzOiBgbWVzc2FnZSAke21lc3NhZ2UudHlwZX1gfSk7XG5cdFx0XHRcdGNvbnN0ICRoZWFkbGluZSA9ICQoJzxwLz4nLCB7Y2xhc3M6ICdtZXNzYWdlLWhlYWRsaW5lJywgaHRtbDogbWVzc2FnZS5oZWFkbGluZX0pO1xuXHRcdFx0XHRjb25zdCAkbWVzc2FnZSA9ICQoJzxwLz4nLCB7Y2xhc3M6ICdtZXNzYWdlLWJvZHknLCBodG1sOiBtZXNzYWdlLm1lc3NhZ2V9KTtcblx0XHRcdFx0Y29uc3QgJGFjdGlvbkNvbnRhaW5lciA9ICQoJzxkaXYvPicsIHtjbGFzczogJ21lc3NhZ2UtYWN0aW9uLWNvbnRhaW5lcid9KTtcblx0XHRcdFx0Y29uc3QgJGhpZGVBY3Rpb24gPSAkKCc8c3Bhbi8+Jywge2NsYXNzOiAnbWVzc2FnZS1hY3Rpb24gbWVzc2FnZS1hY3Rpb24taGlkZSBmYSBmYS1taW51cyd9KTtcblx0XHRcdFx0Y29uc3QgJHJlbW92ZUFjdGlvbiA9ICQoJzxzcGFuLz4nLCB7Y2xhc3M6ICdtZXNzYWdlLWFjdGlvbiBtZXNzYWdlLWFjdGlvbi1yZW1vdmUgZmEgZmEtdGltZXMnfSk7XG5cblx0XHRcdFx0Ly8gSXMgdGhlIG1lc3NhZ2UgYSBzdWNjZXNzIG1lc3NhZ2U/XG5cdFx0XHRcdGNvbnN0IGlzU3VjY2Vzc01lc3NhZ2UgPSBtZXNzYWdlLmlkZW50aWZpZXIgP1xuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZS5pZGVudGlmaWVyLnNlYXJjaCh0aGlzLnN1Y2Nlc3NNZXNzYWdlSWRlbnRpZmllclByZWZpeCkgIT09IC0xIDpcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgIGZhbHNlO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU2hvdyByZW1vdmUvaGlkZSBidXR0b24sIGRlcGVuZGluZyBvbiB0aGUgdmlzaWJpbGl0eSB2YWx1ZSBhbmQga2luZCBvZiBtZXNzYWdlLlxuXHRcdFx0XHRpZiAoIWlzU3VjY2Vzc01lc3NhZ2UgJiYgbWVzc2FnZS52aXNpYmlsaXR5ID09PSB0aGlzLlZJU0lCSUxJVFlfUkVNT1ZBQkxFKSB7XG5cdFx0XHRcdFx0JGFjdGlvbkNvbnRhaW5lclxuXHRcdFx0XHRcdFx0LmFwcGVuZCgkaGlkZUFjdGlvbiwgJHJlbW92ZUFjdGlvbilcblx0XHRcdFx0XHRcdC5hcHBlbmRUbygkdGVtcGxhdGUpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKCFpc1N1Y2Nlc3NNZXNzYWdlICYmIG1lc3NhZ2UudmlzaWJpbGl0eSA9PT0gdGhpcy5WSVNJQklMSVRZX0hJREVBQkxFKSB7XG5cdFx0XHRcdFx0JGFjdGlvbkNvbnRhaW5lclxuXHRcdFx0XHRcdFx0LmFwcGVuZCgkaGlkZUFjdGlvbilcblx0XHRcdFx0XHRcdC5hcHBlbmRUbygkdGVtcGxhdGUpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKGlzU3VjY2Vzc01lc3NhZ2UpIHtcblx0XHRcdFx0XHQkYWN0aW9uQ29udGFpbmVyXG5cdFx0XHRcdFx0XHQuYXBwZW5kKCRyZW1vdmVBY3Rpb24pXG5cdFx0XHRcdFx0XHQuYXBwZW5kVG8oJHRlbXBsYXRlKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUHV0IG1lc3NhZ2UgZGF0YSB0byB0aGUgbWVzc2FnZSBpdGVtIGVsZW1lbnQgYXMgZGF0YSBhdHRyaWJ1dGVzIGFuZCBhcHBlbmQgdGV4dCBlbGVtZW50cy5cblx0XHRcdFx0JHRlbXBsYXRlXG5cdFx0XHRcdFx0LmF0dHIoJ2RhdGEtc3RhdHVzJywgbWVzc2FnZS5zdGF0dXMpXG5cdFx0XHRcdFx0LmF0dHIoJ2RhdGEtaWQnLCBtZXNzYWdlLmlkKVxuXHRcdFx0XHRcdC5hdHRyKCdkYXRhLXZpc2liaWxpdHknLCBtZXNzYWdlLnZpc2liaWxpdHkpXG5cdFx0XHRcdFx0LmF0dHIoJ2RhdGEtaWRlbnRpZmllcicsIG1lc3NhZ2UuaWRlbnRpZmllcilcblx0XHRcdFx0XHQuYXBwZW5kKCRoZWFkbGluZSwgJG1lc3NhZ2UpO1xuXG5cdFx0XHRcdC8vIEFwcGVuZCBidXR0b24sIGlmIGEgYnV0dG9uIGxhYmVsIGlzIGRlZmluZWQuXG5cdFx0XHRcdGlmIChtZXNzYWdlLmJ1dHRvbkxhYmVsKSB7XG5cdFx0XHRcdFx0Y29uc3QgJGJ1dHRvbiA9ICQoJzxhLz4nLCB7XG5cdFx0XHRcdFx0XHRjbGFzczogJ2J0biBtZXNzYWdlLWJ1dHRvbicsXG5cdFx0XHRcdFx0XHR0ZXh0OiBtZXNzYWdlLmJ1dHRvbkxhYmVsLFxuXHRcdFx0XHRcdFx0aHJlZjogbWVzc2FnZS5idXR0b25MaW5rXG5cdFx0XHRcdFx0fSk7XG5cblx0XHRcdFx0XHQkdGVtcGxhdGUuYXBwZW5kKCRidXR0b24pO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0cmV0dXJuICR0ZW1wbGF0ZTtcblx0XHRcdH1cblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDcmVhdGVzIGFuZCByZXR1cm5zIGEgbmV3IEhUTUwgZWxlbWVudCBjb250YWluZyB0aGUgcG9wb3ZlciB0ZW1wbGF0ZS5cblx0XHRcdCAqXG5cdFx0XHQgKiBAcmV0dXJuIHtqUXVlcnl9IENvbXBvc2VkIGVsZW1lbnQuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqXG5cdFx0XHQgKiBAc3RhdGljXG5cdFx0XHQgKi9cblx0XHRcdHN0YXRpYyBfY3JlYXRlUG9wb3ZlclRlbXBsYXRlRWxlbWVudCgpIHtcblx0XHRcdFx0Y29uc3QgJHBvcG92ZXIgPSAkKCc8ZGl2Lz4nLCB7Y2xhc3M6ICdwb3BvdmVyIGluZm8tYm94LXBvcG92ZXInLCByb2xlOiAndG9vbHRpcCd9KTtcblx0XHRcdFx0Y29uc3QgJGFycm93ID0gJCgnPGRpdi8+Jywge2NsYXNzOiAnYXJyb3cnfSk7XG5cdFx0XHRcdGNvbnN0ICR0aXRsZSA9ICQoJzxkaXYvPicsIHtjbGFzczogJ3BvcG92ZXItdGl0bGUnfSk7XG5cdFx0XHRcdGNvbnN0ICRjb250ZW50ID0gJCgnPGRpdi8+Jywge2NsYXNzOiAncG9wb3Zlci1jb250ZW50IGluZm8tYm94LXBvcG92ZXItY29udGVudCd9KTtcblxuXHRcdFx0XHQkcG9wb3Zlci5hcHBlbmQoJGFycm93LCAkdGl0bGUsICRjb250ZW50KTtcblxuXHRcdFx0XHRyZXR1cm4gJHBvcG92ZXI7XG5cdFx0XHR9XG5cdFx0fVxuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHRtb2R1bGUuaW5pdCA9IChkb25lKSA9PiB7XG5cdFx0XHQvLyBEZXBlbmRlbmNpZXMuXG5cdFx0XHRjb25zdCAkZWxlbWVudCA9ICQodGhpcykuZmluZCgnLmluZm8tYm94Jyk7XG5cdFx0XHRjb25zdCBTZXJ2aWNlTGlicmFyeSA9IGpzZS5saWJzLmluZm9fYm94LnNlcnZpY2U7XG5cdFx0XHRjb25zdCBMb2FkaW5nU3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lcjtcblx0XHRcdGNvbnN0IFRyYW5zbGF0b3IgPSBqc2UuY29yZS5sYW5nO1xuXG5cdFx0XHQvLyBDcmVhdGUgYSBuZXcgSW5mb0JveCBjb250cm9sbGVyIGluc3RhbmNlIGFuZCBzZXQgbWVzc2FnZSBjb3VudC5cblx0XHRcdGNvbnN0IEluZm9Cb3ggPSBuZXcgSW5mb0JveENvbnRyb2xsZXIoZG9uZSwgJGVsZW1lbnQsIFNlcnZpY2VMaWJyYXJ5LCBMb2FkaW5nU3Bpbm5lciwgVHJhbnNsYXRvcik7XG5cdFx0XHRJbmZvQm94LmNoZWNrSW5pdGlhbCgpO1xuXHRcdH07XG5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
