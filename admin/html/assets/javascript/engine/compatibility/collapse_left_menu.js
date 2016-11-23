'use strict';

/* --------------------------------------------------------------
 collapse_left_menu.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Collapse Main Left Menu
 *
 * This module will handle the collapse and expansion of the main left menu of the admin section. The HTML
 * for the collapse button comes from the "html/compatibility/collapse_left_menu.php".
 *
 * @module Compatibility/collapse_left_menu
 */
gx.compatibility.module('collapse_left_menu', ['user_configuration_service'],

/**  @lends module:Compatibility/collapse_left_menu */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// ELEMENTS DEFINITION
	// ------------------------------------------------------------------------

	var $this = $(this),
	    $menu = $('.main-left-menu'),
	    $currentMenuBox = $menu.find('.leftmenu_box.current'),
	    $menuToggleButton = $this.find('.menu-toggle-button'),
	    $menuButtonIndicator = $menuToggleButton.find('#menu-button-indicator'),
	    menuInitState = $menu.data('initState');

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var module = {},
	    initialCssWidth = $menu.css('width'),
	    userConfigurationService = jse.libs.user_configuration_service,
	    userConfig = {
		userId: data.userId,
		configurationKey: 'menuVisibility'
	},
	    stateMap = {
		collapse: {
			next: 'expand',
			button: 'right',
			class: 'collapsed',
			do: function _do(isAnimated) {
				_collapse(isAnimated);
			}
		},
		expand: {
			next: 'expandAll',
			button: 'down',
			class: 'expanded',
			do: function _do(isAnimated) {
				_expand(isAnimated);
			}
		},
		expandAll: {
			next: 'collapse',
			button: 'left',
			class: 'expanded-all',
			do: function _do(isAnimated) {
				_expandAll(isAnimated);
			}
		}
	},
	    currentState;

	// ------------------------------------------------------------------------
	// HELPERS
	// ------------------------------------------------------------------------

	var isMenuVisible = function isMenuVisible() {
		return !$menu.hasClass('collapsed');
	};

	// ------------------------------------------------------------------------
	// STATE CHANGE TRIGGERS
	// ------------------------------------------------------------------------

	var _changeState = function _changeState(state, isAnimated) {
		var saveConfig = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

		currentState = state;
		stateMap[currentState].do(isAnimated);

		if (saveConfig) {
			_saveConfig();
		}

		_changeButton();
	};

	var _changeButton = function _changeButton() {
		var className = 'fa fa-caret-';
		var arrowDirection = stateMap[currentState].button;
		$menuButtonIndicator.removeAttr('class').addClass(className + arrowDirection);
	};

	// ------------------------------------------------------------------------
	// COLLAPSE / EXPAND MENU
	// ------------------------------------------------------------------------

	/**
  * Collapse Left Menu
  * @param {boolean} isAnimated - Animate the hiding?
  * @private
  */
	var _collapse = function _collapse(isAnimated) {

		var currentBox = $this.parent().find('ul.current');

		// Collapse menu
		if (isAnimated) {
			$menu.animate({
				'width': '45px'
			}, 300, 'swing');
		} else {
			$menu.css('width', '45px');
			$('.columnLeft2').css('width', '45px');
		}
		currentBox.hide();

		$(document).trigger('leftmenu:collapse');

		// Fade out heading text
		$menu.find('.leftmenu_head span').fadeOut('fast');

		// Class changes
		$menu.removeClass('expanded-all').addClass('collapsed');

		$menu.find('.current:not(li)').removeClass('current');

		$menu.find('.current-menu-head').addClass('current');

		var interval = setInterval(function () {
			if (currentState === 'collapse') {
				if ($('.leftmenu_head.current').length > 1) {
					$menu.find('.leftmenu_head.current:not(.current-menu-head)').removeClass('current');
					clearInterval(interval);
				}
			} else {
				clearInterval(interval);
			}
		}, 1);
	};

	/**
  * Expand Left Menu
  * @private
  */
	var _expand = function _expand() {

		var currentBox = $this.parent().find('ul.current');

		// Expand menu
		$menu.animate({
			'width': initialCssWidth
		}, 300, 'swing');
		currentBox.show();

		// Fade in heading text
		$menu.find('.leftmenu_head span').fadeIn('slow');

		$(document).trigger('leftmenu:expand');

		// Class changes
		$menu.removeClass('collapsed');
		$currentMenuBox.addClass('current');
	};

	/**
  * Expand all menu items
  * @private
  */
	var _expandAll = function _expandAll(isAnimated) {

		$menu.addClass('expanded-all');

		var $headingBoxes = $menu.find('div.leftmenu_head:not(.current)');

		if (isAnimated) {
			$headingBoxes.addClass('current', 750, 'swing');
		} else {
			$headingBoxes.addClass('current');
		}

		$(document).trigger('leftmenu:expand');

		$menu.find('ul.leftmenu_box:not(.current)').addClass('current');
	};

	// ------------------------------------------------------------------------
	// USER CONFIGURATION HANDLER
	// ------------------------------------------------------------------------

	var _saveConfig = function _saveConfig() {
		userConfigurationService.set({
			data: $.extend(userConfig, {
				configurationValue: currentState
			})
		});
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _onClick = function _onClick(event) {
		if ($menuToggleButton.has(event.target).length) {
			_changeState(stateMap[currentState].next, true);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		$('div.leftmenu_head.current').addClass('current-menu-head');

		if (!isMenuVisible()) {
			$currentMenuBox.removeClass('current');
		}

		currentState = menuInitState;

		if (currentState === '') {
			currentState = 'expand'; // Default value if there is no menuInitState set yet.
		}

		_changeState(currentState, false, false);

		$this.on('click', _onClick);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbGxhcHNlX2xlZnRfbWVudS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJG1lbnUiLCIkY3VycmVudE1lbnVCb3giLCJmaW5kIiwiJG1lbnVUb2dnbGVCdXR0b24iLCIkbWVudUJ1dHRvbkluZGljYXRvciIsIm1lbnVJbml0U3RhdGUiLCJpbml0aWFsQ3NzV2lkdGgiLCJjc3MiLCJ1c2VyQ29uZmlndXJhdGlvblNlcnZpY2UiLCJqc2UiLCJsaWJzIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJ1c2VyQ29uZmlnIiwidXNlcklkIiwiY29uZmlndXJhdGlvbktleSIsInN0YXRlTWFwIiwiY29sbGFwc2UiLCJuZXh0IiwiYnV0dG9uIiwiY2xhc3MiLCJkbyIsImlzQW5pbWF0ZWQiLCJfY29sbGFwc2UiLCJleHBhbmQiLCJfZXhwYW5kIiwiZXhwYW5kQWxsIiwiX2V4cGFuZEFsbCIsImN1cnJlbnRTdGF0ZSIsImlzTWVudVZpc2libGUiLCJoYXNDbGFzcyIsIl9jaGFuZ2VTdGF0ZSIsInN0YXRlIiwic2F2ZUNvbmZpZyIsIl9zYXZlQ29uZmlnIiwiX2NoYW5nZUJ1dHRvbiIsImNsYXNzTmFtZSIsImFycm93RGlyZWN0aW9uIiwicmVtb3ZlQXR0ciIsImFkZENsYXNzIiwiY3VycmVudEJveCIsInBhcmVudCIsImFuaW1hdGUiLCJoaWRlIiwiZG9jdW1lbnQiLCJ0cmlnZ2VyIiwiZmFkZU91dCIsInJlbW92ZUNsYXNzIiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImxlbmd0aCIsImNsZWFySW50ZXJ2YWwiLCJzaG93IiwiZmFkZUluIiwiJGhlYWRpbmdCb3hlcyIsInNldCIsImV4dGVuZCIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsIl9vbkNsaWNrIiwiZXZlbnQiLCJoYXMiLCJ0YXJnZXQiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FB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RUFBc0I7QUFDckIzQixtQkFBZ0I2QyxXQUFoQixDQUE0QixTQUE1QjtBQUNBOztBQUVEbkIsaUJBQWV0QixhQUFmOztBQUVBLE1BQUlzQixpQkFBaUIsRUFBckIsRUFBeUI7QUFDeEJBLGtCQUFlLFFBQWYsQ0FEd0IsQ0FDQztBQUN6Qjs7QUFFREcsZUFBYUgsWUFBYixFQUEyQixLQUEzQixFQUFrQyxLQUFsQzs7QUFFQTdCLFFBQU1pRSxFQUFOLENBQVMsT0FBVCxFQUFrQk4sUUFBbEI7O0FBRUFLO0FBQ0EsRUFuQkQ7O0FBcUJBLFFBQU9sRSxNQUFQO0FBQ0EsQ0E5UEYiLCJmaWxlIjoiY29sbGFwc2VfbGVmdF9tZW51LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjb2xsYXBzZV9sZWZ0X21lbnUuanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQ29sbGFwc2UgTWFpbiBMZWZ0IE1lbnVcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGhhbmRsZSB0aGUgY29sbGFwc2UgYW5kIGV4cGFuc2lvbiBvZiB0aGUgbWFpbiBsZWZ0IG1lbnUgb2YgdGhlIGFkbWluIHNlY3Rpb24uIFRoZSBIVE1MXG4gKiBmb3IgdGhlIGNvbGxhcHNlIGJ1dHRvbiBjb21lcyBmcm9tIHRoZSBcImh0bWwvY29tcGF0aWJpbGl0eS9jb2xsYXBzZV9sZWZ0X21lbnUucGhwXCIuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2NvbGxhcHNlX2xlZnRfbWVudVxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2NvbGxhcHNlX2xlZnRfbWVudScsXG5cdFxuXHRbJ3VzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlJ10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jb2xsYXBzZV9sZWZ0X21lbnUgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRUxFTUVOVFMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkbWVudSA9ICQoJy5tYWluLWxlZnQtbWVudScpLFxuXHRcdFx0JGN1cnJlbnRNZW51Qm94ID0gJG1lbnUuZmluZCgnLmxlZnRtZW51X2JveC5jdXJyZW50JyksXG5cdFx0XHQkbWVudVRvZ2dsZUJ1dHRvbiA9ICR0aGlzLmZpbmQoJy5tZW51LXRvZ2dsZS1idXR0b24nKSxcblx0XHRcdCRtZW51QnV0dG9uSW5kaWNhdG9yID0gJG1lbnVUb2dnbGVCdXR0b24uZmluZCgnI21lbnUtYnV0dG9uLWluZGljYXRvcicpLFxuXHRcdFx0bWVudUluaXRTdGF0ZSA9ICRtZW51LmRhdGEoJ2luaXRTdGF0ZScpO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIG1vZHVsZSA9IHt9LFxuXHRcdFx0XG5cdFx0XHRpbml0aWFsQ3NzV2lkdGggPSAkbWVudS5jc3MoJ3dpZHRoJyksXG5cdFx0XHRcblx0XHRcdHVzZXJDb25maWd1cmF0aW9uU2VydmljZSA9IGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLFxuXHRcdFx0XG5cdFx0XHR1c2VyQ29uZmlnID0ge1xuXHRcdFx0XHR1c2VySWQ6IGRhdGEudXNlcklkLFxuXHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiAnbWVudVZpc2liaWxpdHknXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHRzdGF0ZU1hcCA9IHtcblx0XHRcdFx0Y29sbGFwc2U6IHtcblx0XHRcdFx0XHRuZXh0OiAnZXhwYW5kJyxcblx0XHRcdFx0XHRidXR0b246ICdyaWdodCcsXG5cdFx0XHRcdFx0Y2xhc3M6ICdjb2xsYXBzZWQnLFxuXHRcdFx0XHRcdGRvOiBmdW5jdGlvbihpc0FuaW1hdGVkKSB7XG5cdFx0XHRcdFx0XHRfY29sbGFwc2UoaXNBbmltYXRlZCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9LFxuXHRcdFx0XHRleHBhbmQ6IHtcblx0XHRcdFx0XHRuZXh0OiAnZXhwYW5kQWxsJyxcblx0XHRcdFx0XHRidXR0b246ICdkb3duJyxcblx0XHRcdFx0XHRjbGFzczogJ2V4cGFuZGVkJyxcblx0XHRcdFx0XHRkbzogZnVuY3Rpb24oaXNBbmltYXRlZCkge1xuXHRcdFx0XHRcdFx0X2V4cGFuZChpc0FuaW1hdGVkKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0sXG5cdFx0XHRcdGV4cGFuZEFsbDoge1xuXHRcdFx0XHRcdG5leHQ6ICdjb2xsYXBzZScsXG5cdFx0XHRcdFx0YnV0dG9uOiAnbGVmdCcsXG5cdFx0XHRcdFx0Y2xhc3M6ICdleHBhbmRlZC1hbGwnLFxuXHRcdFx0XHRcdGRvOiBmdW5jdGlvbihpc0FuaW1hdGVkKSB7XG5cdFx0XHRcdFx0XHRfZXhwYW5kQWxsKGlzQW5pbWF0ZWQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0Y3VycmVudFN0YXRlO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEhFTFBFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgaXNNZW51VmlzaWJsZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0cmV0dXJuICEkbWVudS5oYXNDbGFzcygnY29sbGFwc2VkJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBTVEFURSBDSEFOR0UgVFJJR0dFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX2NoYW5nZVN0YXRlID0gZnVuY3Rpb24oc3RhdGUsIGlzQW5pbWF0ZWQsIHNhdmVDb25maWcgPSB0cnVlKSB7XG5cdFx0XHRjdXJyZW50U3RhdGUgPSBzdGF0ZTtcblx0XHRcdHN0YXRlTWFwW2N1cnJlbnRTdGF0ZV0uZG8oaXNBbmltYXRlZCk7XG5cdFx0XHRcblx0XHRcdGlmIChzYXZlQ29uZmlnKSB7XG5cdFx0XHRcdF9zYXZlQ29uZmlnKCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdF9jaGFuZ2VCdXR0b24oKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY2hhbmdlQnV0dG9uID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgY2xhc3NOYW1lID0gJ2ZhIGZhLWNhcmV0LSc7XG5cdFx0XHR2YXIgYXJyb3dEaXJlY3Rpb24gPSBzdGF0ZU1hcFtjdXJyZW50U3RhdGVdLmJ1dHRvbjtcblx0XHRcdCRtZW51QnV0dG9uSW5kaWNhdG9yXG5cdFx0XHRcdC5yZW1vdmVBdHRyKCdjbGFzcycpXG5cdFx0XHRcdC5hZGRDbGFzcyhjbGFzc05hbWUgKyBhcnJvd0RpcmVjdGlvbik7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBDT0xMQVBTRSAvIEVYUEFORCBNRU5VXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ29sbGFwc2UgTGVmdCBNZW51XG5cdFx0ICogQHBhcmFtIHtib29sZWFufSBpc0FuaW1hdGVkIC0gQW5pbWF0ZSB0aGUgaGlkaW5nP1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jb2xsYXBzZSA9IGZ1bmN0aW9uKGlzQW5pbWF0ZWQpIHtcblx0XHRcdFxuXHRcdFx0dmFyIGN1cnJlbnRCb3ggPSAkdGhpcy5wYXJlbnQoKS5maW5kKCd1bC5jdXJyZW50Jyk7XG5cdFx0XHRcblx0XHRcdC8vIENvbGxhcHNlIG1lbnVcblx0XHRcdGlmIChpc0FuaW1hdGVkKSB7XG5cdFx0XHRcdCRtZW51LmFuaW1hdGUoe1xuXHRcdFx0XHRcdCd3aWR0aCc6ICc0NXB4J1xuXHRcdFx0XHR9LCAzMDAsICdzd2luZycpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JG1lbnUuY3NzKCd3aWR0aCcsICc0NXB4Jyk7XG5cdFx0XHRcdCQoJy5jb2x1bW5MZWZ0MicpLmNzcygnd2lkdGgnLCAnNDVweCcpO1xuXHRcdFx0fVxuXHRcdFx0Y3VycmVudEJveC5oaWRlKCk7XG5cdFx0XHRcblx0XHRcdCQoZG9jdW1lbnQpLnRyaWdnZXIoJ2xlZnRtZW51OmNvbGxhcHNlJyk7XG5cdFx0XHRcblx0XHRcdC8vIEZhZGUgb3V0IGhlYWRpbmcgdGV4dFxuXHRcdFx0JG1lbnVcblx0XHRcdFx0LmZpbmQoJy5sZWZ0bWVudV9oZWFkIHNwYW4nKVxuXHRcdFx0XHQuZmFkZU91dCgnZmFzdCcpO1xuXHRcdFx0XG5cdFx0XHQvLyBDbGFzcyBjaGFuZ2VzXG5cdFx0XHQkbWVudVxuXHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2V4cGFuZGVkLWFsbCcpXG5cdFx0XHRcdC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cdFx0XHRcblx0XHRcdCRtZW51XG5cdFx0XHRcdC5maW5kKCcuY3VycmVudDpub3QobGkpJylcblx0XHRcdFx0LnJlbW92ZUNsYXNzKCdjdXJyZW50Jyk7XG5cdFx0XHRcblx0XHRcdCRtZW51XG5cdFx0XHRcdC5maW5kKCcuY3VycmVudC1tZW51LWhlYWQnKVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ2N1cnJlbnQnKTtcblx0XHRcdFxuXHRcdFx0dmFyIGludGVydmFsID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmIChjdXJyZW50U3RhdGUgPT09ICdjb2xsYXBzZScpIHtcblx0XHRcdFx0XHRpZiAoJCgnLmxlZnRtZW51X2hlYWQuY3VycmVudCcpLmxlbmd0aCA+IDEpIHtcblx0XHRcdFx0XHRcdCRtZW51XG5cdFx0XHRcdFx0XHRcdC5maW5kKCcubGVmdG1lbnVfaGVhZC5jdXJyZW50Om5vdCguY3VycmVudC1tZW51LWhlYWQpJylcblx0XHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCdjdXJyZW50Jyk7XG5cdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdH1cblx0XHRcdH0sIDEpO1xuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeHBhbmQgTGVmdCBNZW51XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2V4cGFuZCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XG5cdFx0XHR2YXIgY3VycmVudEJveCA9ICR0aGlzLnBhcmVudCgpLmZpbmQoJ3VsLmN1cnJlbnQnKTtcblx0XHRcdFxuXHRcdFx0Ly8gRXhwYW5kIG1lbnVcblx0XHRcdCRtZW51LmFuaW1hdGUoe1xuXHRcdFx0XHQnd2lkdGgnOiBpbml0aWFsQ3NzV2lkdGhcblx0XHRcdH0sIDMwMCwgJ3N3aW5nJyk7XG5cdFx0XHRjdXJyZW50Qm94LnNob3coKTtcblx0XHRcdFxuXHRcdFx0Ly8gRmFkZSBpbiBoZWFkaW5nIHRleHRcblx0XHRcdCRtZW51LmZpbmQoJy5sZWZ0bWVudV9oZWFkIHNwYW4nKS5mYWRlSW4oJ3Nsb3cnKTtcblx0XHRcdFxuXHRcdFx0JChkb2N1bWVudCkudHJpZ2dlcignbGVmdG1lbnU6ZXhwYW5kJyk7XG5cdFx0XHRcblx0XHRcdC8vIENsYXNzIGNoYW5nZXNcblx0XHRcdCRtZW51LnJlbW92ZUNsYXNzKCdjb2xsYXBzZWQnKTtcblx0XHRcdCRjdXJyZW50TWVudUJveC5hZGRDbGFzcygnY3VycmVudCcpO1xuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeHBhbmQgYWxsIG1lbnUgaXRlbXNcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZXhwYW5kQWxsID0gZnVuY3Rpb24oaXNBbmltYXRlZCkge1xuXHRcdFx0XG5cdFx0XHQkbWVudVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ2V4cGFuZGVkLWFsbCcpO1xuXHRcdFx0XG5cdFx0XHR2YXIgJGhlYWRpbmdCb3hlcyA9ICRtZW51XG5cdFx0XHRcdC5maW5kKCdkaXYubGVmdG1lbnVfaGVhZDpub3QoLmN1cnJlbnQpJyk7XG5cdFx0XHRcblx0XHRcdGlmIChpc0FuaW1hdGVkKSB7XG5cdFx0XHRcdCRoZWFkaW5nQm94ZXMuYWRkQ2xhc3MoJ2N1cnJlbnQnLCA3NTAsICdzd2luZycpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JGhlYWRpbmdCb3hlcy5hZGRDbGFzcygnY3VycmVudCcpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkKGRvY3VtZW50KS50cmlnZ2VyKCdsZWZ0bWVudTpleHBhbmQnKTtcblx0XHRcdFxuXHRcdFx0JG1lbnVcblx0XHRcdFx0LmZpbmQoJ3VsLmxlZnRtZW51X2JveDpub3QoLmN1cnJlbnQpJylcblx0XHRcdFx0LmFkZENsYXNzKCdjdXJyZW50Jyk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBVU0VSIENPTkZJR1VSQVRJT04gSEFORExFUlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfc2F2ZUNvbmZpZyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLnNldCh7XG5cdFx0XHRcdGRhdGE6ICQuZXh0ZW5kKHVzZXJDb25maWcsIHtcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uVmFsdWU6IGN1cnJlbnRTdGF0ZVxuXHRcdFx0XHR9KVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfb25DbGljayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRpZiAoJG1lbnVUb2dnbGVCdXR0b24uaGFzKGV2ZW50LnRhcmdldCkubGVuZ3RoKSB7XG5cdFx0XHRcdF9jaGFuZ2VTdGF0ZShzdGF0ZU1hcFtjdXJyZW50U3RhdGVdLm5leHQsIHRydWUpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdFxuXHRcdFx0JCgnZGl2LmxlZnRtZW51X2hlYWQuY3VycmVudCcpLmFkZENsYXNzKCdjdXJyZW50LW1lbnUtaGVhZCcpO1xuXHRcdFx0XG5cdFx0XHRpZiAoIWlzTWVudVZpc2libGUoKSkge1xuXHRcdFx0XHQkY3VycmVudE1lbnVCb3gucmVtb3ZlQ2xhc3MoJ2N1cnJlbnQnKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Y3VycmVudFN0YXRlID0gbWVudUluaXRTdGF0ZTtcblx0XHRcdFxuXHRcdFx0aWYgKGN1cnJlbnRTdGF0ZSA9PT0gJycpIHtcblx0XHRcdFx0Y3VycmVudFN0YXRlID0gJ2V4cGFuZCc7IC8vIERlZmF1bHQgdmFsdWUgaWYgdGhlcmUgaXMgbm8gbWVudUluaXRTdGF0ZSBzZXQgeWV0LlxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRfY2hhbmdlU3RhdGUoY3VycmVudFN0YXRlLCBmYWxzZSwgZmFsc2UpO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb25DbGljayk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
