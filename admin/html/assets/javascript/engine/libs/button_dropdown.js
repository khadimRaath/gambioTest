'use strict';

/* --------------------------------------------------------------
 button_dropdown.js 2016-06-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.button_dropdown = jse.libs.button_dropdown || {};

/**
 * ## Button Dropdown Library
 *
 * This library contains helper functions that make the manipulation of a button dropdown
 * widget easier.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 *
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *
 *   [
 *      gx.source + '/libs/button_dropdown'
 *   ],
 *
 *   function(data) {
 *      // Module code ... 
 *   });
 *```
 *
 * ### Example
 *
 * ```javascript
 * var $buttonDropdown = $('#my.js-button-dropdown');
 *
 * // Map an action to a dropdown item.
 * jse.libs.button_dropdown.mapAction($buttonDropdown, action, section, callback, $targetRecentButton);
 *
 * // Change recent button.
 * jse.libs.button_dropdown.changeDefualtButton($buttonDropdown, text, callback, $targetRecentButton);
 *
 * // Add a separator in a dropdown list.
 * jse.libs.button_dropdown.addDropdownSeperator($buttonDropdown);
 * ```
 *
 * @todo Further improve the code and the comments of this library.
 *
 * @module Admin/Libs/button_dropdown
 * @exports jse.libs.button_dropdown
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Triggers a specific event from an element.
  *
  * Some situations require a different approach than just using the "trigger" method.
  *
  * @param {object} $element Destination element to be triggered.
  * @param {object} event Event options can be used for creating new conditions.
  *
  * @private
  */

	var _triggerEvent = function _triggerEvent($element, event) {
		if ($element.prop('tagName') === 'A' && event.type === 'click') {
			$element.get(0).click();
		} else {
			$element.trigger(event.type);
		}
	};

	/**
  * Binds the event to a new dropdown action item.
  *
  * @param {object} options See bind documentation.
  *
  * @private
  */
	var _bindEvent = function _bindEvent(options) {
		var $dropdown = options.$dropdown,
		    action = options.action,
		    $target = options.$target,
		    eventName = options.event,
		    callback = options.callback || false,
		    title = options.title || (options.$target.length ? options.$target.text() : '<No Action Title Provided>'),
		    $li = $('<li></li>');

		$li.html('<span data-value="' + action + '">' + title + '</span>');
		$dropdown.find('ul').append($li);

		$li.find('span').on(eventName, function (event) {
			if (callback !== false) {
				//event.preventDefault();
				//event.stopPropagation();
				callback.call($li.find('span'), event);
			} else {
				_triggerEvent($target, event);
			}
		});
	};

	/**
  * Initializes the default button.
  *
  * @param {object} $dropdown The affected button dropdown selector.
  * @param {object} configValue Configuration value that comes from the UserConfigurationService.
  * @param {object} title The caption of the default action button.
  * @param {object} callback (optional) Callback function for the new action.
  * @param {object} $targetDefaultButton (optional) Selector for the default button.
  */
	var _initDefaultAction = function _initDefaultAction($dropdown, configValue, title, callback, $targetDefaultButton) {
		var interval = setInterval(function () {
			if (typeof $dropdown.attr('data-configuration_value') !== 'undefined') {
				// Sets the recent action button loaded from database.
				if ($dropdown.attr('data-configuration_value') === configValue) {
					exports.changeDefaultAction($dropdown, title, callback, $targetDefaultButton);
				}

				clearInterval(interval);
			}
		}, 300);
	};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
  * Adds a new item to the dropdown.
  *
  * @param {string} translationPhrase Translation phrase key.
  * @param {string} translationSection Translation section of the phrase.
  * @param {function} customCallback Define a custom callback.
  * @param {object} $targetDefaultButton (optional) A custom selector which dropdown buttons should be changed.
  */
	exports.mapAction = function ($dropdown, translationPhrase, translationSection, customCallback, $targetDefaultButton) {
		var $target = $targetDefaultButton || $dropdown,
		    title = translationSection !== '' ? jse.core.lang.translate(translationPhrase, translationSection) : translationPhrase;

		// Sets the first action as recent action button, if no recent action has benn set so far.
		if (!$dropdown.find('ul li').length && $dropdown.find('button:first').text().trim() === '') {
			exports.changeDefaultAction($dropdown, title, customCallback, $target);
		}

		_initDefaultAction($dropdown, translationPhrase, title, customCallback, $target);

		var options = {
			action: translationPhrase,
			$dropdown: $dropdown,
			title: title,
			event: 'perform:action',
			callback: function callback(event) {
				customCallback(event);
				exports.changeDefaultAction($(this), title, customCallback, $target);
			}
		};

		_bindEvent(options);
	};

	/**
  * Changes the default action of the button.
  *
  * @param {object} $button The affected button dropdown widget.
  * @param {string} title Text of the new button.
  * @param {string} callback The callback
  * @param {object} $targetDefaultButton A custom element for which button should be changed.
  */
	exports.changeDefaultAction = function ($dropdown, title, callback, $targetDefaultButton) {
		var $target = $targetDefaultButton || $dropdown,
		    icon = $target.data('icon');

		if (title.length) {
			$target.find('button:first').off('perform:action').on('perform:action', callback);
		}

		$target.find('button:first').text(title);

		$target.find('button:first').prop('title', title.trim());

		if (typeof icon !== 'undefined') {
			$target.find('button:first').prepend($('<i class="fa fa-' + icon + ' btn-icon"></i>'));
		}
	};

	/**
  * Add button-dropdown action.
  *
  * This method works with the Bootstrap markup button-dropdowns and enables you to add actions with callbacks
  * existing button dropdown elements.
  *
  * The action object can have the following attributes and default values:
  *
  * {
  *   text: '{Undefined}', // The text to be displayed. 
  *   href: '#', // URL for the <a> element.  
  *   target: '', // Target attribute for <a> element. 
  *   class: '', // Add custom classes to the <a> element.
  *   data: {}, // Add data to the <a> element. 
  *   isDefault: false, // Whether the action is the default action. 
  *   callback: function(e) {} // Callback for click event of the <a> element. 
  * }
  *
  * @param {object} $dropdown The jQuery selector of the button dropdown wrapper.
  * @param {object} action An object containing the action information.
  */
	exports.addAction = function ($dropdown, action) {
		var $li = $('<li/>'),
		    $a = $('<a />');

		$a.text(action.text || '{Undefined}').attr('href', action.href || '#').attr('target', action.target || '').addClass(action.class || '').data(action.data).appendTo($li);

		if (action.isDefault) {
			exports.setDefaultAction($dropdown, $a);
		}

		if (action.callback) {
			$a.on('click', action.callback);
		}

		$li.appendTo($dropdown.find('ul'));
	};

	/**
  * Adds a separator to the dropdown list.
  *
  * The separator will be added at the end of the list.
  *
  * @param {object} $dropdown The jQuery selector of the button dropdown wrapper.
  * @param {bool} compatibilityMarkup (optional) Whether to use the compatibility markup.
  */
	exports.addSeparator = function ($dropdown) {
		var compatibilityMarkup = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

		var html = !compatibilityMarkup ? '<li role="separator" class="divider"></li>' : '<li><hr></li>';
		$dropdown.find('ul').append(html);
	};

	/**
  * Bind button dropdown default action. 
  * 
  * This method will update the default action of the dropdown upon click. This is useful for storing the 
  * default actions and then using them to display the default one with every new instance of the button 
  * dropdown. 
  * 
  * Important: The <a> elements need to have the "configurationValue" data property that defines a unique string
  * for the selected action. 
  * 
  * @param {object} $dropdowns The jQuery selector of the button dropdowns wrapper.
  * @param {number} userId The ID of the current user. 
  * @param {string} configurationKey The configuration key to be saved. 
  * @param {object} userConfigurationService The user_configuration_service module (needs to be passed explicitly).  
  */
	exports.bindDefaultAction = function ($dropdowns, userId, configurationKey, userConfigurationService) {
		$dropdowns.on('click', 'a', function () {
			var params = {
				data: {
					userId: userId,
					configurationKey: configurationKey,
					configurationValue: $(this).data('configurationValue')
				}
			};

			userConfigurationService.set(params);

			exports.setDefaultAction($dropdowns, $(this));
		});
	};

	/**
  * Set the default action item for button dropdowns.
  *
  * @param {object} $dropdowns jQuery selector for the button dropdowns.
  * @param {object} $actionLink jQuery selector for the action link to be set as default.
  */
	exports.setDefaultAction = function ($dropdowns, $actionLink) {
		$dropdowns.each(function (index, dropdown) {
			var $dropdownButton = $(dropdown).children('button:first');

			$dropdownButton.text($actionLink.text()).off('click').on('click', function () {
				// Do nothing when the dropdown is grayed out.
				if ($dropdownButton.hasClass('disabled')) {
					return;
				}
				$(dropdown).find('li:eq(' + $actionLink.parent().index() + ') a')[0].click();
			});
		});
	};
})(jse.libs.button_dropdown);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJ1dHRvbl9kcm9wZG93bi5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwiZXhwb3J0cyIsIl90cmlnZ2VyRXZlbnQiLCIkZWxlbWVudCIsImV2ZW50IiwicHJvcCIsInR5cGUiLCJnZXQiLCJjbGljayIsInRyaWdnZXIiLCJfYmluZEV2ZW50Iiwib3B0aW9ucyIsIiRkcm9wZG93biIsImFjdGlvbiIsIiR0YXJnZXQiLCJldmVudE5hbWUiLCJjYWxsYmFjayIsInRpdGxlIiwibGVuZ3RoIiwidGV4dCIsIiRsaSIsIiQiLCJodG1sIiwiZmluZCIsImFwcGVuZCIsIm9uIiwiY2FsbCIsIl9pbml0RGVmYXVsdEFjdGlvbiIsImNvbmZpZ1ZhbHVlIiwiJHRhcmdldERlZmF1bHRCdXR0b24iLCJpbnRlcnZhbCIsInNldEludGVydmFsIiwiYXR0ciIsImNoYW5nZURlZmF1bHRBY3Rpb24iLCJjbGVhckludGVydmFsIiwibWFwQWN0aW9uIiwidHJhbnNsYXRpb25QaHJhc2UiLCJ0cmFuc2xhdGlvblNlY3Rpb24iLCJjdXN0b21DYWxsYmFjayIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwidHJpbSIsImljb24iLCJkYXRhIiwib2ZmIiwicHJlcGVuZCIsImFkZEFjdGlvbiIsIiRhIiwiaHJlZiIsInRhcmdldCIsImFkZENsYXNzIiwiY2xhc3MiLCJhcHBlbmRUbyIsImlzRGVmYXVsdCIsInNldERlZmF1bHRBY3Rpb24iLCJhZGRTZXBhcmF0b3IiLCJjb21wYXRpYmlsaXR5TWFya3VwIiwiYmluZERlZmF1bHRBY3Rpb24iLCIkZHJvcGRvd25zIiwidXNlcklkIiwiY29uZmlndXJhdGlvbktleSIsInVzZXJDb25maWd1cmF0aW9uU2VydmljZSIsInBhcmFtcyIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsInNldCIsIiRhY3Rpb25MaW5rIiwiZWFjaCIsImluZGV4IiwiZHJvcGRvd24iLCIkZHJvcGRvd25CdXR0b24iLCJjaGlsZHJlbiIs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cEIsRUFIRixDQUdLLE9BSEwsRUFHYyxZQUFNO0FBQ2xCO0FBQ0EsUUFBSTZDLGdCQUFnQkUsUUFBaEIsQ0FBeUIsVUFBekIsQ0FBSixFQUEwQztBQUN6QztBQUNBO0FBQ0RuRCxNQUFFZ0QsUUFBRixFQUNFOUMsSUFERixZQUNnQjJDLFlBQVlPLE1BQVosR0FBcUJMLEtBQXJCLEVBRGhCLFVBQ21ELENBRG5ELEVBRUU1RCxLQUZGO0FBR0EsSUFYRjtBQVlBLEdBZkQ7QUFnQkEsRUFqQkQ7QUFtQkEsQ0F6UUQsRUF5UUdWLElBQUlDLElBQUosQ0FBU0MsZUF6UVoiLCJmaWxlIjoiYnV0dG9uX2Ryb3Bkb3duLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBidXR0b25fZHJvcGRvd24uanMgMjAxNi0wNi0xMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmJ1dHRvbl9kcm9wZG93biA9IGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93biB8fCB7fTtcblxuLyoqXG4gKiAjIyBCdXR0b24gRHJvcGRvd24gTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBjb250YWlucyBoZWxwZXIgZnVuY3Rpb25zIHRoYXQgbWFrZSB0aGUgbWFuaXB1bGF0aW9uIG9mIGEgYnV0dG9uIGRyb3Bkb3duXG4gKiB3aWRnZXQgZWFzaWVyLlxuICpcbiAqIFlvdSB3aWxsIG5lZWQgdG8gcHJvdmlkZSB0aGUgZnVsbCBVUkwgaW4gb3JkZXIgdG8gbG9hZCB0aGlzIGxpYnJhcnkgYXMgYSBkZXBlbmRlbmN5IHRvIGEgbW9kdWxlOlxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIGd4LmNvbnRyb2xsZXIubW9kdWxlKFxuICogICAnbXlfY3VzdG9tX3BhZ2UnLFxuICpcbiAqICAgW1xuICogICAgICBneC5zb3VyY2UgKyAnL2xpYnMvYnV0dG9uX2Ryb3Bkb3duJ1xuICogICBdLFxuICpcbiAqICAgZnVuY3Rpb24oZGF0YSkge1xuICogICAgICAvLyBNb2R1bGUgY29kZSAuLi4gXG4gKiAgIH0pO1xuICpgYGBcbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIHZhciAkYnV0dG9uRHJvcGRvd24gPSAkKCcjbXkuanMtYnV0dG9uLWRyb3Bkb3duJyk7XG4gKlxuICogLy8gTWFwIGFuIGFjdGlvbiB0byBhIGRyb3Bkb3duIGl0ZW0uXG4gKiBqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCRidXR0b25Ecm9wZG93biwgYWN0aW9uLCBzZWN0aW9uLCBjYWxsYmFjaywgJHRhcmdldFJlY2VudEJ1dHRvbik7XG4gKlxuICogLy8gQ2hhbmdlIHJlY2VudCBidXR0b24uXG4gKiBqc2UubGlicy5idXR0b25fZHJvcGRvd24uY2hhbmdlRGVmdWFsdEJ1dHRvbigkYnV0dG9uRHJvcGRvd24sIHRleHQsIGNhbGxiYWNrLCAkdGFyZ2V0UmVjZW50QnV0dG9uKTtcbiAqXG4gKiAvLyBBZGQgYSBzZXBhcmF0b3IgaW4gYSBkcm9wZG93biBsaXN0LlxuICoganNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZERyb3Bkb3duU2VwZXJhdG9yKCRidXR0b25Ecm9wZG93bik7XG4gKiBgYGBcbiAqXG4gKiBAdG9kbyBGdXJ0aGVyIGltcHJvdmUgdGhlIGNvZGUgYW5kIHRoZSBjb21tZW50cyBvZiB0aGlzIGxpYnJhcnkuXG4gKlxuICogQG1vZHVsZSBBZG1pbi9MaWJzL2J1dHRvbl9kcm9wZG93blxuICogQGV4cG9ydHMganNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFRyaWdnZXJzIGEgc3BlY2lmaWMgZXZlbnQgZnJvbSBhbiBlbGVtZW50LlxuXHQgKlxuXHQgKiBTb21lIHNpdHVhdGlvbnMgcmVxdWlyZSBhIGRpZmZlcmVudCBhcHByb2FjaCB0aGFuIGp1c3QgdXNpbmcgdGhlIFwidHJpZ2dlclwiIG1ldGhvZC5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IERlc3RpbmF0aW9uIGVsZW1lbnQgdG8gYmUgdHJpZ2dlcmVkLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgRXZlbnQgb3B0aW9ucyBjYW4gYmUgdXNlZCBmb3IgY3JlYXRpbmcgbmV3IGNvbmRpdGlvbnMuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX3RyaWdnZXJFdmVudCA9IGZ1bmN0aW9uKCRlbGVtZW50LCBldmVudCkge1xuXHRcdGlmICgkZWxlbWVudC5wcm9wKCd0YWdOYW1lJykgPT09ICdBJyAmJiBldmVudC50eXBlID09PSAnY2xpY2snKSB7XG5cdFx0XHQkZWxlbWVudC5nZXQoMCkuY2xpY2soKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JGVsZW1lbnQudHJpZ2dlcihldmVudC50eXBlKTtcblx0XHR9XG5cdH07XG5cdFxuXHQvKipcblx0ICogQmluZHMgdGhlIGV2ZW50IHRvIGEgbmV3IGRyb3Bkb3duIGFjdGlvbiBpdGVtLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBTZWUgYmluZCBkb2N1bWVudGF0aW9uLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9iaW5kRXZlbnQgPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0dmFyICRkcm9wZG93biA9IG9wdGlvbnMuJGRyb3Bkb3duLFxuXHRcdFx0YWN0aW9uID0gb3B0aW9ucy5hY3Rpb24sXG5cdFx0XHQkdGFyZ2V0ID0gb3B0aW9ucy4kdGFyZ2V0LFxuXHRcdFx0ZXZlbnROYW1lID0gb3B0aW9ucy5ldmVudCxcblx0XHRcdGNhbGxiYWNrID0gb3B0aW9ucy5jYWxsYmFjayB8fCBmYWxzZSxcblx0XHRcdHRpdGxlID0gb3B0aW9ucy50aXRsZSB8fCAob3B0aW9ucy4kdGFyZ2V0Lmxlbmd0aCA/IG9wdGlvbnMuJHRhcmdldC50ZXh0KCkgOiAnPE5vIEFjdGlvbiBUaXRsZSBQcm92aWRlZD4nKSxcblx0XHRcdCRsaSA9ICQoJzxsaT48L2xpPicpO1xuXHRcdFxuXHRcdCRsaS5odG1sKCc8c3BhbiBkYXRhLXZhbHVlPVwiJyArIGFjdGlvbiArICdcIj4nICsgdGl0bGUgKyAnPC9zcGFuPicpO1xuXHRcdCRkcm9wZG93bi5maW5kKCd1bCcpLmFwcGVuZCgkbGkpO1xuXHRcdFxuXHRcdCRsaS5maW5kKCdzcGFuJykub24oZXZlbnROYW1lLCBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0aWYgKGNhbGxiYWNrICE9PSBmYWxzZSkge1xuXHRcdFx0XHQvL2V2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdC8vZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcdGNhbGxiYWNrLmNhbGwoJGxpLmZpbmQoJ3NwYW4nKSwgZXZlbnQpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0X3RyaWdnZXJFdmVudCgkdGFyZ2V0LCBldmVudCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZXMgdGhlIGRlZmF1bHQgYnV0dG9uLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGRyb3Bkb3duIFRoZSBhZmZlY3RlZCBidXR0b24gZHJvcGRvd24gc2VsZWN0b3IuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBjb25maWdWYWx1ZSBDb25maWd1cmF0aW9uIHZhbHVlIHRoYXQgY29tZXMgZnJvbSB0aGUgVXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gdGl0bGUgVGhlIGNhcHRpb24gb2YgdGhlIGRlZmF1bHQgYWN0aW9uIGJ1dHRvbi5cblx0ICogQHBhcmFtIHtvYmplY3R9IGNhbGxiYWNrIChvcHRpb25hbCkgQ2FsbGJhY2sgZnVuY3Rpb24gZm9yIHRoZSBuZXcgYWN0aW9uLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJHRhcmdldERlZmF1bHRCdXR0b24gKG9wdGlvbmFsKSBTZWxlY3RvciBmb3IgdGhlIGRlZmF1bHQgYnV0dG9uLlxuXHQgKi9cblx0dmFyIF9pbml0RGVmYXVsdEFjdGlvbiA9IGZ1bmN0aW9uKCRkcm9wZG93biwgY29uZmlnVmFsdWUsIHRpdGxlLCBjYWxsYmFjaywgJHRhcmdldERlZmF1bHRCdXR0b24pIHtcblx0XHR2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcblx0XHRcdGlmICh0eXBlb2YgJGRyb3Bkb3duLmF0dHIoJ2RhdGEtY29uZmlndXJhdGlvbl92YWx1ZScpICE9PSAndW5kZWZpbmVkJykge1xuXHRcdFx0XHQvLyBTZXRzIHRoZSByZWNlbnQgYWN0aW9uIGJ1dHRvbiBsb2FkZWQgZnJvbSBkYXRhYmFzZS5cblx0XHRcdFx0aWYgKCRkcm9wZG93bi5hdHRyKCdkYXRhLWNvbmZpZ3VyYXRpb25fdmFsdWUnKSA9PT0gY29uZmlnVmFsdWUpIHtcblx0XHRcdFx0XHRleHBvcnRzLmNoYW5nZURlZmF1bHRBY3Rpb24oJGRyb3Bkb3duLCB0aXRsZSwgY2FsbGJhY2ssICR0YXJnZXREZWZhdWx0QnV0dG9uKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHR9XG5cdFx0fSwgMzAwKTtcblx0fTtcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBQVUJMSUMgTUVUSE9EU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdC8qKlxuXHQgKiBBZGRzIGEgbmV3IGl0ZW0gdG8gdGhlIGRyb3Bkb3duLlxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdHJhbnNsYXRpb25QaHJhc2UgVHJhbnNsYXRpb24gcGhyYXNlIGtleS5cblx0ICogQHBhcmFtIHtzdHJpbmd9IHRyYW5zbGF0aW9uU2VjdGlvbiBUcmFuc2xhdGlvbiBzZWN0aW9uIG9mIHRoZSBwaHJhc2UuXG5cdCAqIEBwYXJhbSB7ZnVuY3Rpb259IGN1c3RvbUNhbGxiYWNrIERlZmluZSBhIGN1c3RvbSBjYWxsYmFjay5cblx0ICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXREZWZhdWx0QnV0dG9uIChvcHRpb25hbCkgQSBjdXN0b20gc2VsZWN0b3Igd2hpY2ggZHJvcGRvd24gYnV0dG9ucyBzaG91bGQgYmUgY2hhbmdlZC5cblx0ICovXG5cdGV4cG9ydHMubWFwQWN0aW9uID1cblx0XHRmdW5jdGlvbigkZHJvcGRvd24sIHRyYW5zbGF0aW9uUGhyYXNlLCB0cmFuc2xhdGlvblNlY3Rpb24sIGN1c3RvbUNhbGxiYWNrLCAkdGFyZ2V0RGVmYXVsdEJ1dHRvbikge1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkdGFyZ2V0RGVmYXVsdEJ1dHRvbiB8fCAkZHJvcGRvd24sXG5cdFx0XHRcdHRpdGxlID0gKHRyYW5zbGF0aW9uU2VjdGlvbiAhPT0gJycpXG5cdFx0XHRcdFx0PyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSh0cmFuc2xhdGlvblBocmFzZSwgdHJhbnNsYXRpb25TZWN0aW9uKVxuXHRcdFx0XHRcdDogdHJhbnNsYXRpb25QaHJhc2U7XG5cdFx0XHRcblx0XHRcdC8vIFNldHMgdGhlIGZpcnN0IGFjdGlvbiBhcyByZWNlbnQgYWN0aW9uIGJ1dHRvbiwgaWYgbm8gcmVjZW50IGFjdGlvbiBoYXMgYmVubiBzZXQgc28gZmFyLlxuXHRcdFx0aWYgKCEkZHJvcGRvd24uZmluZCgndWwgbGknKS5sZW5ndGggJiYgJGRyb3Bkb3duLmZpbmQoJ2J1dHRvbjpmaXJzdCcpLnRleHQoKS50cmltKCkgPT09ICcnKSB7XG5cdFx0XHRcdGV4cG9ydHMuY2hhbmdlRGVmYXVsdEFjdGlvbigkZHJvcGRvd24sIHRpdGxlLCBjdXN0b21DYWxsYmFjaywgJHRhcmdldCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdF9pbml0RGVmYXVsdEFjdGlvbigkZHJvcGRvd24sIHRyYW5zbGF0aW9uUGhyYXNlLCB0aXRsZSwgY3VzdG9tQ2FsbGJhY2ssICR0YXJnZXQpO1xuXHRcdFx0XG5cdFx0XHR2YXIgb3B0aW9ucyA9IHtcblx0XHRcdFx0YWN0aW9uOiB0cmFuc2xhdGlvblBocmFzZSxcblx0XHRcdFx0JGRyb3Bkb3duOiAkZHJvcGRvd24sXG5cdFx0XHRcdHRpdGxlOiB0aXRsZSxcblx0XHRcdFx0ZXZlbnQ6ICdwZXJmb3JtOmFjdGlvbicsXG5cdFx0XHRcdGNhbGxiYWNrOiBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XHRcdGN1c3RvbUNhbGxiYWNrKGV2ZW50KTtcblx0XHRcdFx0XHRleHBvcnRzLmNoYW5nZURlZmF1bHRBY3Rpb24oJCh0aGlzKSwgdGl0bGUsIGN1c3RvbUNhbGxiYWNrLCAkdGFyZ2V0KTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblx0XHRcdFxuXHRcdFx0X2JpbmRFdmVudChvcHRpb25zKTtcblx0XHR9O1xuXHRcblx0LyoqXG5cdCAqIENoYW5nZXMgdGhlIGRlZmF1bHQgYWN0aW9uIG9mIHRoZSBidXR0b24uXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkYnV0dG9uIFRoZSBhZmZlY3RlZCBidXR0b24gZHJvcGRvd24gd2lkZ2V0LlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdGl0bGUgVGV4dCBvZiB0aGUgbmV3IGJ1dHRvbi5cblx0ICogQHBhcmFtIHtzdHJpbmd9IGNhbGxiYWNrIFRoZSBjYWxsYmFja1xuXHQgKiBAcGFyYW0ge29iamVjdH0gJHRhcmdldERlZmF1bHRCdXR0b24gQSBjdXN0b20gZWxlbWVudCBmb3Igd2hpY2ggYnV0dG9uIHNob3VsZCBiZSBjaGFuZ2VkLlxuXHQgKi9cblx0ZXhwb3J0cy5jaGFuZ2VEZWZhdWx0QWN0aW9uID0gZnVuY3Rpb24oJGRyb3Bkb3duLCB0aXRsZSwgY2FsbGJhY2ssICR0YXJnZXREZWZhdWx0QnV0dG9uKSB7XG5cdFx0dmFyICR0YXJnZXQgPSAkdGFyZ2V0RGVmYXVsdEJ1dHRvbiB8fCAkZHJvcGRvd24sXG5cdFx0XHRpY29uID0gJHRhcmdldC5kYXRhKCdpY29uJyk7XG5cdFx0XG5cdFx0aWYgKHRpdGxlLmxlbmd0aCkge1xuXHRcdFx0JHRhcmdldFxuXHRcdFx0XHQuZmluZCgnYnV0dG9uOmZpcnN0Jylcblx0XHRcdFx0Lm9mZigncGVyZm9ybTphY3Rpb24nKVxuXHRcdFx0XHQub24oJ3BlcmZvcm06YWN0aW9uJywgY2FsbGJhY2spO1xuXHRcdH1cblx0XHRcblx0XHQkdGFyZ2V0XG5cdFx0XHQuZmluZCgnYnV0dG9uOmZpcnN0Jylcblx0XHRcdC50ZXh0KHRpdGxlKTtcblx0XHRcblx0XHQkdGFyZ2V0XG5cdFx0XHQuZmluZCgnYnV0dG9uOmZpcnN0Jylcblx0XHRcdC5wcm9wKCd0aXRsZScsIHRpdGxlLnRyaW0oKSk7XG5cdFx0XG5cdFx0aWYgKHR5cGVvZiBpY29uICE9PSAndW5kZWZpbmVkJykge1xuXHRcdFx0JHRhcmdldFxuXHRcdFx0XHQuZmluZCgnYnV0dG9uOmZpcnN0Jylcblx0XHRcdFx0LnByZXBlbmQoJCgnPGkgY2xhc3M9XCJmYSBmYS0nICsgaWNvbiArICcgYnRuLWljb25cIj48L2k+JykpO1xuXHRcdH1cblx0fTtcblx0XG5cdC8qKlxuXHQgKiBBZGQgYnV0dG9uLWRyb3Bkb3duIGFjdGlvbi5cblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd29ya3Mgd2l0aCB0aGUgQm9vdHN0cmFwIG1hcmt1cCBidXR0b24tZHJvcGRvd25zIGFuZCBlbmFibGVzIHlvdSB0byBhZGQgYWN0aW9ucyB3aXRoIGNhbGxiYWNrc1xuXHQgKiBleGlzdGluZyBidXR0b24gZHJvcGRvd24gZWxlbWVudHMuXG5cdCAqXG5cdCAqIFRoZSBhY3Rpb24gb2JqZWN0IGNhbiBoYXZlIHRoZSBmb2xsb3dpbmcgYXR0cmlidXRlcyBhbmQgZGVmYXVsdCB2YWx1ZXM6XG5cdCAqXG5cdCAqIHtcblx0ICogICB0ZXh0OiAne1VuZGVmaW5lZH0nLCAvLyBUaGUgdGV4dCB0byBiZSBkaXNwbGF5ZWQuIFxuXHQgKiAgIGhyZWY6ICcjJywgLy8gVVJMIGZvciB0aGUgPGE+IGVsZW1lbnQuICBcblx0ICogICB0YXJnZXQ6ICcnLCAvLyBUYXJnZXQgYXR0cmlidXRlIGZvciA8YT4gZWxlbWVudC4gXG5cdCAqICAgY2xhc3M6ICcnLCAvLyBBZGQgY3VzdG9tIGNsYXNzZXMgdG8gdGhlIDxhPiBlbGVtZW50LlxuXHQgKiAgIGRhdGE6IHt9LCAvLyBBZGQgZGF0YSB0byB0aGUgPGE+IGVsZW1lbnQuIFxuXHQgKiAgIGlzRGVmYXVsdDogZmFsc2UsIC8vIFdoZXRoZXIgdGhlIGFjdGlvbiBpcyB0aGUgZGVmYXVsdCBhY3Rpb24uIFxuXHQgKiAgIGNhbGxiYWNrOiBmdW5jdGlvbihlKSB7fSAvLyBDYWxsYmFjayBmb3IgY2xpY2sgZXZlbnQgb2YgdGhlIDxhPiBlbGVtZW50LiBcblx0ICogfVxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGRyb3Bkb3duIFRoZSBqUXVlcnkgc2VsZWN0b3Igb2YgdGhlIGJ1dHRvbiBkcm9wZG93biB3cmFwcGVyLlxuXHQgKiBAcGFyYW0ge29iamVjdH0gYWN0aW9uIEFuIG9iamVjdCBjb250YWluaW5nIHRoZSBhY3Rpb24gaW5mb3JtYXRpb24uXG5cdCAqL1xuXHRleHBvcnRzLmFkZEFjdGlvbiA9IGZ1bmN0aW9uKCRkcm9wZG93biwgYWN0aW9uKSB7XG5cdFx0bGV0ICRsaSA9ICQoJzxsaS8+JyksXG5cdFx0XHQkYSA9ICQoJzxhIC8+Jyk7XG5cdFx0XG5cdFx0JGFcblx0XHRcdC50ZXh0KGFjdGlvbi50ZXh0IHx8ICd7VW5kZWZpbmVkfScpXG5cdFx0XHQuYXR0cignaHJlZicsIGFjdGlvbi5ocmVmIHx8ICcjJylcblx0XHRcdC5hdHRyKCd0YXJnZXQnLCBhY3Rpb24udGFyZ2V0IHx8ICcnKVxuXHRcdFx0LmFkZENsYXNzKGFjdGlvbi5jbGFzcyB8fCAnJylcblx0XHRcdC5kYXRhKGFjdGlvbi5kYXRhKVxuXHRcdFx0LmFwcGVuZFRvKCRsaSk7XG5cdFx0XG5cdFx0aWYgKGFjdGlvbi5pc0RlZmF1bHQpIHtcblx0XHRcdGV4cG9ydHMuc2V0RGVmYXVsdEFjdGlvbigkZHJvcGRvd24sICRhKTsgXG5cdFx0fVxuXHRcdFxuXHRcdGlmIChhY3Rpb24uY2FsbGJhY2spIHtcblx0XHRcdCRhLm9uKCdjbGljaycsIGFjdGlvbi5jYWxsYmFjayk7XG5cdFx0fVxuXHRcdFxuXHRcdCRsaS5hcHBlbmRUbygkZHJvcGRvd24uZmluZCgndWwnKSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogQWRkcyBhIHNlcGFyYXRvciB0byB0aGUgZHJvcGRvd24gbGlzdC5cblx0ICpcblx0ICogVGhlIHNlcGFyYXRvciB3aWxsIGJlIGFkZGVkIGF0IHRoZSBlbmQgb2YgdGhlIGxpc3QuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSAkZHJvcGRvd24gVGhlIGpRdWVyeSBzZWxlY3RvciBvZiB0aGUgYnV0dG9uIGRyb3Bkb3duIHdyYXBwZXIuXG5cdCAqIEBwYXJhbSB7Ym9vbH0gY29tcGF0aWJpbGl0eU1hcmt1cCAob3B0aW9uYWwpIFdoZXRoZXIgdG8gdXNlIHRoZSBjb21wYXRpYmlsaXR5IG1hcmt1cC5cblx0ICovXG5cdGV4cG9ydHMuYWRkU2VwYXJhdG9yID0gZnVuY3Rpb24oJGRyb3Bkb3duLCBjb21wYXRpYmlsaXR5TWFya3VwID0gZmFsc2UpIHtcblx0XHRsZXQgaHRtbCA9ICFjb21wYXRpYmlsaXR5TWFya3VwID8gJzxsaSByb2xlPVwic2VwYXJhdG9yXCIgY2xhc3M9XCJkaXZpZGVyXCI+PC9saT4nIDogJzxsaT48aHI+PC9saT4nO1xuXHRcdCRkcm9wZG93bi5maW5kKCd1bCcpLmFwcGVuZChodG1sKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBCaW5kIGJ1dHRvbiBkcm9wZG93biBkZWZhdWx0IGFjdGlvbi4gXG5cdCAqIFxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIHVwZGF0ZSB0aGUgZGVmYXVsdCBhY3Rpb24gb2YgdGhlIGRyb3Bkb3duIHVwb24gY2xpY2suIFRoaXMgaXMgdXNlZnVsIGZvciBzdG9yaW5nIHRoZSBcblx0ICogZGVmYXVsdCBhY3Rpb25zIGFuZCB0aGVuIHVzaW5nIHRoZW0gdG8gZGlzcGxheSB0aGUgZGVmYXVsdCBvbmUgd2l0aCBldmVyeSBuZXcgaW5zdGFuY2Ugb2YgdGhlIGJ1dHRvbiBcblx0ICogZHJvcGRvd24uIFxuXHQgKiBcblx0ICogSW1wb3J0YW50OiBUaGUgPGE+IGVsZW1lbnRzIG5lZWQgdG8gaGF2ZSB0aGUgXCJjb25maWd1cmF0aW9uVmFsdWVcIiBkYXRhIHByb3BlcnR5IHRoYXQgZGVmaW5lcyBhIHVuaXF1ZSBzdHJpbmdcblx0ICogZm9yIHRoZSBzZWxlY3RlZCBhY3Rpb24uIFxuXHQgKiBcblx0ICogQHBhcmFtIHtvYmplY3R9ICRkcm9wZG93bnMgVGhlIGpRdWVyeSBzZWxlY3RvciBvZiB0aGUgYnV0dG9uIGRyb3Bkb3ducyB3cmFwcGVyLlxuXHQgKiBAcGFyYW0ge251bWJlcn0gdXNlcklkIFRoZSBJRCBvZiB0aGUgY3VycmVudCB1c2VyLiBcblx0ICogQHBhcmFtIHtzdHJpbmd9IGNvbmZpZ3VyYXRpb25LZXkgVGhlIGNvbmZpZ3VyYXRpb24ga2V5IHRvIGJlIHNhdmVkLiBcblx0ICogQHBhcmFtIHtvYmplY3R9IHVzZXJDb25maWd1cmF0aW9uU2VydmljZSBUaGUgdXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UgbW9kdWxlIChuZWVkcyB0byBiZSBwYXNzZWQgZXhwbGljaXRseSkuICBcblx0ICovXG5cdGV4cG9ydHMuYmluZERlZmF1bHRBY3Rpb24gPSBmdW5jdGlvbigkZHJvcGRvd25zLCB1c2VySWQsIGNvbmZpZ3VyYXRpb25LZXksICB1c2VyQ29uZmlndXJhdGlvblNlcnZpY2UpIHtcblx0XHQkZHJvcGRvd25zLm9uKCdjbGljaycsICdhJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zdCBwYXJhbXMgPSB7XG5cdFx0XHRcdGRhdGE6IHtcblx0XHRcdFx0XHR1c2VySWQsXG5cdFx0XHRcdFx0Y29uZmlndXJhdGlvbktleSwgXG5cdFx0XHRcdFx0Y29uZmlndXJhdGlvblZhbHVlOiAkKHRoaXMpLmRhdGEoJ2NvbmZpZ3VyYXRpb25WYWx1ZScpXG5cdFx0XHRcdH1cblx0XHRcdH07IFxuXHRcdFx0XG5cdFx0XHR1c2VyQ29uZmlndXJhdGlvblNlcnZpY2Uuc2V0KHBhcmFtcyk7XG5cdFx0XHRcblx0XHRcdGV4cG9ydHMuc2V0RGVmYXVsdEFjdGlvbigkZHJvcGRvd25zLCAkKHRoaXMpKTsgXG5cdFx0fSk7IFxuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFNldCB0aGUgZGVmYXVsdCBhY3Rpb24gaXRlbSBmb3IgYnV0dG9uIGRyb3Bkb3ducy5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRkcm9wZG93bnMgalF1ZXJ5IHNlbGVjdG9yIGZvciB0aGUgYnV0dG9uIGRyb3Bkb3ducy5cblx0ICogQHBhcmFtIHtvYmplY3R9ICRhY3Rpb25MaW5rIGpRdWVyeSBzZWxlY3RvciBmb3IgdGhlIGFjdGlvbiBsaW5rIHRvIGJlIHNldCBhcyBkZWZhdWx0LlxuXHQgKi9cblx0ZXhwb3J0cy5zZXREZWZhdWx0QWN0aW9uID0gZnVuY3Rpb24oJGRyb3Bkb3ducywgJGFjdGlvbkxpbmspIHtcblx0XHQkZHJvcGRvd25zLmVhY2goKGluZGV4LCBkcm9wZG93bikgPT4ge1xuXHRcdFx0Y29uc3QgJGRyb3Bkb3duQnV0dG9uID0gJChkcm9wZG93bikuY2hpbGRyZW4oJ2J1dHRvbjpmaXJzdCcpO1xuXHRcdFx0XG5cdFx0XHQkZHJvcGRvd25CdXR0b25cblx0XHRcdFx0LnRleHQoJGFjdGlvbkxpbmsudGV4dCgpKVxuXHRcdFx0XHQub2ZmKCdjbGljaycpXG5cdFx0XHRcdC5vbignY2xpY2snLCAoKSA9PiB7XG5cdFx0XHRcdFx0Ly8gRG8gbm90aGluZyB3aGVuIHRoZSBkcm9wZG93biBpcyBncmF5ZWQgb3V0LlxuXHRcdFx0XHRcdGlmICgkZHJvcGRvd25CdXR0b24uaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0JChkcm9wZG93bilcblx0XHRcdFx0XHRcdC5maW5kKGBsaTplcSgkeyRhY3Rpb25MaW5rLnBhcmVudCgpLmluZGV4KCl9KSBhYClbMF1cblx0XHRcdFx0XHRcdC5jbGljaygpO1xuXHRcdFx0XHR9KTtcblx0XHR9KTtcblx0fTtcblx0XG59KShqc2UubGlicy5idXR0b25fZHJvcGRvd24pO1xuIl19
