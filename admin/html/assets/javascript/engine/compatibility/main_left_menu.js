'use strict';

/* --------------------------------------------------------------
 main_left_menu.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Compatibility Main Left Menu Handler
 *
 * This module will transform the old menu to the new theme.
 *
 * @module Compatibility/main_left_menu
 */
gx.compatibility.module('main_left_menu', [],

/**  @lends module:Compatibility/main_left_menu */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * setTimeout variable for clearing timeout
  *
  * @var {int}
  */
	timeout = 0,


	/**
  * Delay until submenu opens after entering left menu
  *
  * @var {int}
  */
	initialShowSubmenuDelay = 100,


	/**
  * Delay until submenu appears. Will be set to zero after first submenu was displayed
  * and reset to the initial value after leaving the left menu.
  *
  * @var {int}
  */
	showSubmenuDelay = initialShowSubmenuDelay,


	/**
  * Save mouseDown event for not closing the submenu on dragging an entry into the favs-box
  *
  * @type {boolean}
  */
	mouseDown = false,


	/**
  * Submenu box wherein the mouseDown event was triggered
  *
  * @type {null}
  */
	$mouseDownBox = null,


	/**
  * Mouse X position on mousedown event
  *
  * @type {number}
  */
	mouseX = 0,


	/**
  * Mouse Y position on mousedown event
  *
  * @type {number}
  */
	mouseY = 0,


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	var _showMenuBox = function _showMenuBox($box) {

		var isCurrentBox = $box.hasClass('current');

		if ($box.find('li').length === 0 || isCurrentBox) {
			return;
		}

		if (!$box.is(':visible')) {
			var $menuParent = $box.prev().prev(),
			    isFirstBox = $('.leftmenu_box').index($box) === 0,
			    marginTop = isFirstBox ? -4 : -5,
			    // Fine tuning for the top position
			marginBottom = 10,
			    marginLeft = -10,
			    windowBottomY = $(window).scrollTop() + window.innerHeight,


			// error message box on dashboard page
			headerExtraContentHeight = $('.main-page-content').offset().top - $('.main-top-header').height(),
			    topPosition = $menuParent.offset().top - headerExtraContentHeight + marginTop,
			    bottomPosition = windowBottomY - $box.height() - headerExtraContentHeight + marginTop - marginBottom;

			$box.css({
				'left': $('.main-left-menu').width() + marginLeft
			}); // fine tuning left

			if (topPosition < bottomPosition) {
				$box.css({
					'top': topPosition
				}); // display submenu next to hovered menu item if it fits on screen
			} else {
				$box.css({
					'top': bottomPosition
				}); // else display submenu at the bottom of the screen
			}

			$box.fadeIn(100);
			$box.addClass('floating');
			$menuParent.addClass('active');
		}
	};

	var _hideMenuBox = function _hideMenuBox($box) {
		var isCurrentBox = $box.hasClass('current');

		if ($box.is(':visible') && !isCurrentBox && !mouseDown) {
			$box.fadeOut(100);
			$box.removeClass('floating');
			$box.prev().prev().removeClass('active');
		}
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * On menu head mouse enter menu event handler.
  *
  * @param {object} event
  */
	var _onMenuHeadMouseEnter = function _onMenuHeadMouseEnter(event) {
		$(this).addClass('hover');
		var $that = $(this);

		clearTimeout(timeout);
		timeout = setTimeout(function () {
			_showMenuBox($that.next().next());
			showSubmenuDelay = 0;
		}, showSubmenuDelay);
	};

	/**
  * On menu head mouse leave menu event handler.
  *
  * @param {object} event
  */
	var _onMenuHeadMouseLeave = function _onMenuHeadMouseLeave(event) {
		clearTimeout(timeout);
		$(this).removeClass('hover');
		var $box = $(this).next().next(),
		    $head = $(this);
		setTimeout(function () {
			if (!$box.hasClass('hover') && !$head.hasClass('hover')) {
				_hideMenuBox($box);
			}
		}, 10);
	};

	/**
  * On menu mouse move event handler.
  *
  * Sometimes after multiple hovers the submenus remains hidden and this event handler
  * will ensure that it will not happen while the user hovers the menu item.
  *
  * @param {option} event
  */
	var _onMenuHeadMouseMove = function _onMenuHeadMouseMove(event) {
		if (!$(this).hasClass('hover')) {
			$(this).addClass('hover');
		}

		var $box = $(this).next().next();
	};

	/**
  * On menu box mouse enter menu event handler.
  *
  * @param {object} event
  */
	var _onMenuBoxMouseEnter = function _onMenuBoxMouseEnter(event) {
		$(this).addClass('hover');
	};

	/**
  * On menu box mouse leave menu event handler.
  *
  * @param {object} event
  */
	var _onMenuBoxMouseLeave = function _onMenuBoxMouseLeave(event) {
		$(this).removeClass('hover');

		var $box = $(this),
		    $head = $box.prev().prev();

		setTimeout(function () {
			if (!$box.hasClass('hover') && !$head.hasClass('hover')) {
				_hideMenuBox($box);
			}
		}, 10);
	};

	var _onMenuHeadingDown = function _onMenuHeadingDown(event) {
		mouseX = event.pageX;
		mouseY = event.pageY;
	};

	/**
  * On menu heading click event handler.
  *
  * @param {object} event
  */
	var _onMenuHeadingClick = function _onMenuHeadingClick(event) {

		// do not open link if mouse was moved more than 5px during mousdown event
		if (mouseX > event.pageX + 5 || mouseX < event.pageX - 5 || mouseY > event.pageY + 5 || mouseY < event.pageY - 5) {
			return false;
		}

		// 1 = left click, 2 = middle click
		if (event.which === 1 || event.which === 2) {
			event.preventDefault();
			event.stopPropagation();

			var $heading = $(event.currentTarget);
			var $firstSubItem = $heading.next().next().find('li:first').find('a:first');

			var target = event.which === 1 ? '_self' : '_blank';

			// Open the first sub item's link
			if ($firstSubItem.prop('href')) {
				window.open($firstSubItem.prop('href'), target);
			}
		}
	};

	/**
  * Reset submenu display delay after leaving the left menu
  */
	var _resetShowSubmenuDelay = function _resetShowSubmenuDelay() {
		showSubmenuDelay = initialShowSubmenuDelay;
	};

	/**
  * Save submenu wherein the mouseDown event was triggered
  */
	var _onMenuBoxMouseDown = function _onMenuBoxMouseDown() {
		$mouseDownBox = $(this);
		mouseDown = true;
	};

	/**
  * Hide submenu on mouseUp event after dragging an entry into the favs-box
  */
	var _onMouseUp = function _onMouseUp() {
		mouseDown = false;

		if ($mouseDownBox) {
			_hideMenuBox($mouseDownBox);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZE CONTROLLER
	// ------------------------------------------------------------------------

	/**
  * Initialize controller.
  */
	module.init = function (done) {
		$this.on('mouseenter', '.leftmenu_head', _onMenuHeadMouseEnter).on('mouseleave', '.leftmenu_head', _onMenuHeadMouseLeave).on('mousemove', '.leftmenu_head', _onMenuHeadMouseMove).on('mouseenter', '.leftmenu_box', _onMenuBoxMouseEnter).on('mouseleave', '.leftmenu_box', _onMenuBoxMouseLeave).on('mousedown', '.leftmenu_box', _onMenuBoxMouseDown).on('mousedown', '.leftmenu_head', _onMenuHeadingDown).on('mouseup', '.leftmenu_head', _onMenuHeadingClick).on('mouseleave', _resetShowSubmenuDelay);

		$(document).on('mouseup', _onMouseUp);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW5fbGVmdF9tZW51LmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsInRpbWVvdXQiLCJpbml0aWFsU2hvd1N1Ym1lbnVEZWxheSIsInNob3dTdWJtZW51RGVsYXkiLCJtb3VzZURvd24iLCIkbW91c2VEb3duQm94IiwibW91c2VYIiwibW91c2VZIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zaG93TWVudUJveCIsIiRib3giLCJpc0N1cnJlbnRCb3giLCJoYXNDbGFzcyIsImZpbmQiLCJsZW5ndGgiLCJpcyIsIiRtZW51UGFyZW50IiwicHJldiIsImlzRmlyc3RCb3giLCJpbmRleCIsIm1hcmdpblRvcCIsIm1hcmdpbkJvdHRvbSIsIm1hcmdpbkxlZnQiLCJ3aW5kb3dCb3R0b21ZIiwid2luZG93Iiwic2Nyb2xsVG9wIiwiaW5uZXJIZWlnaHQiLCJoZWFkZXJFeHRyYUNvbnRlbnRIZWlnaHQiLCJvZmZzZXQiLCJ0b3AiLCJoZWlnaHQiLCJ0b3BQb3NpdGlvbiIsImJvdHRvbVBvc2l0aW9uIiwiY3NzIiwid2lkdGgiLCJmYWRlSW4iLCJhZGRDbGFzcyIsIl9oaWRlTWVudUJveCIsImZhZGVPdXQiLCJyZW1vdmVDbGFzcyIsIl9vbk1lbnVIZWFkTW91c2VFbnRlciIsImV2ZW50IiwiJHRoYXQiLCJjbGVhclRpbWVvdXQiLCJzZXRUaW1lb3V0IiwibmV4dCIsIl9vbk1lbnVIZWFkTW91c2VMZWF2ZSIsIiRoZWFkIiwiX29uTWVudUhlYWRNb3VzZU1vdmUiLCJfb25NZW51Qm94TW91c2VFbnRlciIsIl9vbk1lbnVCb3hNb3VzZUxlYXZlIiwiX29uTWVudUhlYWRpbmdEb3duIiwicGFnZVgiLCJwYWdlWSIsIl9vbk1lbnVIZWFkaW5nQ2xpY2siLCJ3aGljaCIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiJGhlYWRpbmciLCJjdXJyZW50VGFyZ2V0IiwiJGZpcnN0U3ViSXRlbSIsInRhcmdldCIsInByb3AiLCJvcGVuIiwiX3Jlc2V0U2hvd1N1Ym1lbnVEZWxheSIsIl9vbk1lbnVCb3hNb3VzZURvd24iLCJfb25Nb3VzZVVwIiwiaW5pdCIsImRvbmUiLCJvbiIsImRvY3VtZW50Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxnQkFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVUsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUMsMkJBQTBCLEdBM0IzQjs7O0FBNkJDOzs7Ozs7QUFNQUMsb0JBQW1CRCx1QkFuQ3BCOzs7QUFxQ0M7Ozs7O0FBS0FFLGFBQVksS0ExQ2I7OztBQTRDQzs7Ozs7QUFLQUMsaUJBQWdCLElBakRqQjs7O0FBbURDOzs7OztBQUtBQyxVQUFTLENBeERWOzs7QUEwREM7Ozs7O0FBS0FDLFVBQVMsQ0EvRFY7OztBQWlFQzs7Ozs7QUFLQUMsV0FBVVQsRUFBRVUsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CVCxRQUFuQixFQUE2QkgsSUFBN0IsQ0F0RVg7OztBQXdFQzs7Ozs7QUFLQUQsVUFBUyxFQTdFVjs7QUErRUE7QUFDQTtBQUNBOztBQUVBLEtBQUljLGVBQWUsU0FBZkEsWUFBZSxDQUFTQyxJQUFULEVBQWU7O0FBRWpDLE1BQUlDLGVBQWVELEtBQUtFLFFBQUwsQ0FBYyxTQUFkLENBQW5COztBQUVBLE1BQUlGLEtBQUtHLElBQUwsQ0FBVSxJQUFWLEVBQWdCQyxNQUFoQixLQUEyQixDQUEzQixJQUFnQ0gsWUFBcEMsRUFBa0Q7QUFDakQ7QUFDQTs7QUFFRCxNQUFJLENBQUNELEtBQUtLLEVBQUwsQ0FBUSxVQUFSLENB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LGNBQVksS0FBWjs7QUFFQSxNQUFJQyxhQUFKLEVBQW1CO0FBQ2xCaUMsZ0JBQWFqQyxhQUFiO0FBQ0E7QUFDRCxFQU5EOztBQVFBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FULFFBQU93RSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCdkUsUUFDRXdFLEVBREYsQ0FDSyxZQURMLEVBQ21CLGdCQURuQixFQUNxQzdCLHFCQURyQyxFQUVFNkIsRUFGRixDQUVLLFlBRkwsRUFFbUIsZ0JBRm5CLEVBRXFDdkIscUJBRnJDLEVBR0V1QixFQUhGLENBR0ssV0FITCxFQUdrQixnQkFIbEIsRUFHb0NyQixvQkFIcEMsRUFJRXFCLEVBSkYsQ0FJSyxZQUpMLEVBSW1CLGVBSm5CLEVBSW9DcEIsb0JBSnBDLEVBS0VvQixFQUxGLENBS0ssWUFMTCxFQUttQixlQUxuQixFQUtvQ25CLG9CQUxwQyxFQU1FbUIsRUFORixDQU1LLFdBTkwsRUFNa0IsZUFObEIsRUFNbUNKLG1CQU5uQyxFQU9FSSxFQVBGLENBT0ssV0FQTCxFQU9rQixnQkFQbEIsRUFPb0NsQixrQkFQcEMsRUFRRWtCLEVBUkYsQ0FRSyxTQVJMLEVBUWdCLGdCQVJoQixFQVFrQ2YsbUJBUmxDLEVBU0VlLEVBVEYsQ0FTSyxZQVRMLEVBU21CTCxzQkFUbkI7O0FBV0FsRSxJQUFFd0UsUUFBRixFQUFZRCxFQUFaLENBQWUsU0FBZixFQUEwQkgsVUFBMUI7O0FBRUFFO0FBQ0EsRUFmRDs7QUFpQkEsUUFBT3pFLE1BQVA7QUFDQSxDQWxVRiIsImZpbGUiOiJtYWluX2xlZnRfbWVudS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbWFpbl9sZWZ0X21lbnUuanMgMjAxNS0wOS0zMCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQ29tcGF0aWJpbGl0eSBNYWluIExlZnQgTWVudSBIYW5kbGVyXG4gKlxuICogVGhpcyBtb2R1bGUgd2lsbCB0cmFuc2Zvcm0gdGhlIG9sZCBtZW51IHRvIHRoZSBuZXcgdGhlbWUuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L21haW5fbGVmdF9tZW51XG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnbWFpbl9sZWZ0X21lbnUnLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9tYWluX2xlZnRfbWVudSAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIHNldFRpbWVvdXQgdmFyaWFibGUgZm9yIGNsZWFyaW5nIHRpbWVvdXRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtpbnR9XG5cdFx0XHQgKi9cblx0XHRcdHRpbWVvdXQgPSAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlbGF5IHVudGlsIHN1Ym1lbnUgb3BlbnMgYWZ0ZXIgZW50ZXJpbmcgbGVmdCBtZW51XG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7aW50fVxuXHRcdFx0ICovXG5cdFx0XHRpbml0aWFsU2hvd1N1Ym1lbnVEZWxheSA9IDEwMCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWxheSB1bnRpbCBzdWJtZW51IGFwcGVhcnMuIFdpbGwgYmUgc2V0IHRvIHplcm8gYWZ0ZXIgZmlyc3Qgc3VibWVudSB3YXMgZGlzcGxheWVkXG5cdFx0XHQgKiBhbmQgcmVzZXQgdG8gdGhlIGluaXRpYWwgdmFsdWUgYWZ0ZXIgbGVhdmluZyB0aGUgbGVmdCBtZW51LlxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge2ludH1cblx0XHRcdCAqL1xuXHRcdFx0c2hvd1N1Ym1lbnVEZWxheSA9IGluaXRpYWxTaG93U3VibWVudURlbGF5LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFNhdmUgbW91c2VEb3duIGV2ZW50IGZvciBub3QgY2xvc2luZyB0aGUgc3VibWVudSBvbiBkcmFnZ2luZyBhbiBlbnRyeSBpbnRvIHRoZSBmYXZzLWJveFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtib29sZWFufVxuXHRcdFx0ICovXG5cdFx0XHRtb3VzZURvd24gPSBmYWxzZSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBTdWJtZW51IGJveCB3aGVyZWluIHRoZSBtb3VzZURvd24gZXZlbnQgd2FzIHRyaWdnZXJlZFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtudWxsfVxuXHRcdFx0ICovXG5cdFx0XHQkbW91c2VEb3duQm94ID0gbnVsbCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb3VzZSBYIHBvc2l0aW9uIG9uIG1vdXNlZG93biBldmVudFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtudW1iZXJ9XG5cdFx0XHQgKi9cblx0XHRcdG1vdXNlWCA9IDAsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW91c2UgWSBwb3NpdGlvbiBvbiBtb3VzZWRvd24gZXZlbnRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7bnVtYmVyfVxuXHRcdFx0ICovXG5cdFx0XHRtb3VzZVkgPSAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3Nob3dNZW51Qm94ID0gZnVuY3Rpb24oJGJveCkge1xuXHRcdFx0XG5cdFx0XHR2YXIgaXNDdXJyZW50Qm94ID0gJGJveC5oYXNDbGFzcygnY3VycmVudCcpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJGJveC5maW5kKCdsaScpLmxlbmd0aCA9PT0gMCB8fCBpc0N1cnJlbnRCb3gpIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoISRib3guaXMoJzp2aXNpYmxlJykpIHtcblx0XHRcdFx0dmFyICRtZW51UGFyZW50ID0gJGJveC5wcmV2KCkucHJldigpLFxuXHRcdFx0XHRcdGlzRmlyc3RCb3ggPSAoJCgnLmxlZnRtZW51X2JveCcpLmluZGV4KCRib3gpID09PSAwKSxcblx0XHRcdFx0XHRtYXJnaW5Ub3AgPSBpc0ZpcnN0Qm94ID8gLTQgOiAtNSwgLy8gRmluZSB0dW5pbmcgZm9yIHRoZSB0b3AgcG9zaXRpb25cblx0XHRcdFx0XHRtYXJnaW5Cb3R0b20gPSAxMCxcblx0XHRcdFx0XHRtYXJnaW5MZWZ0ID0gLTEwLFxuXHRcdFx0XHRcdHdpbmRvd0JvdHRvbVkgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCkgKyB3aW5kb3cuaW5uZXJIZWlnaHQsXG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBlcnJvciBtZXNzYWdlIGJveCBvbiBkYXNoYm9hcmQgcGFnZVxuXHRcdFx0XHRcdGhlYWRlckV4dHJhQ29udGVudEhlaWdodCA9ICQoJy5tYWluLXBhZ2UtY29udGVudCcpLm9mZnNldCgpLnRvcCAtICQoJy5tYWluLXRvcC1oZWFkZXInKS5oZWlnaHQoKSxcblx0XHRcdFx0XHRcblx0XHRcdFx0XHR0b3BQb3NpdGlvbiA9ICRtZW51UGFyZW50Lm9mZnNldCgpLnRvcCAtIGhlYWRlckV4dHJhQ29udGVudEhlaWdodCArIG1hcmdpblRvcCxcblx0XHRcdFx0XHRib3R0b21Qb3NpdGlvbiA9IHdpbmRvd0JvdHRvbVkgLSAkYm94LmhlaWdodCgpIC0gaGVhZGVyRXh0cmFDb250ZW50SGVpZ2h0ICsgbWFyZ2luVG9wIC1cblx0XHRcdFx0XHRcdG1hcmdpbkJvdHRvbTtcblx0XHRcdFx0XG5cdFx0XHRcdCRib3guY3NzKHtcblx0XHRcdFx0XHQnbGVmdCc6ICQoJy5tYWluLWxlZnQtbWVudScpLndpZHRoKCkgKyBtYXJnaW5MZWZ0XG5cdFx0XHRcdH0pOyAvLyBmaW5lIHR1bmluZyBsZWZ0XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAodG9wUG9zaXRpb24gPCBib3R0b21Qb3NpdGlvbikge1xuXHRcdFx0XHRcdCRib3guY3NzKHtcblx0XHRcdFx0XHRcdCd0b3AnOiB0b3BQb3NpdGlvblxuXHRcdFx0XHRcdH0pOyAvLyBkaXNwbGF5IHN1Ym1lbnUgbmV4dCB0byBob3ZlcmVkIG1lbnUgaXRlbSBpZiBpdCBmaXRzIG9uIHNjcmVlblxuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRib3guY3NzKHtcblx0XHRcdFx0XHRcdCd0b3AnOiBib3R0b21Qb3NpdGlvblxuXHRcdFx0XHRcdH0pOyAvLyBlbHNlIGRpc3BsYXkgc3VibWVudSBhdCB0aGUgYm90dG9tIG9mIHRoZSBzY3JlZW5cblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0JGJveC5mYWRlSW4oMTAwKTtcblx0XHRcdFx0JGJveC5hZGRDbGFzcygnZmxvYXRpbmcnKTtcblx0XHRcdFx0JG1lbnVQYXJlbnQuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9oaWRlTWVudUJveCA9IGZ1bmN0aW9uKCRib3gpIHtcblx0XHRcdHZhciBpc0N1cnJlbnRCb3ggPSAkYm94Lmhhc0NsYXNzKCdjdXJyZW50Jyk7XG5cdFx0XHRcblx0XHRcdGlmICgkYm94LmlzKCc6dmlzaWJsZScpICYmICFpc0N1cnJlbnRCb3ggJiYgIW1vdXNlRG93bikge1xuXHRcdFx0XHQkYm94LmZhZGVPdXQoMTAwKTtcblx0XHRcdFx0JGJveC5yZW1vdmVDbGFzcygnZmxvYXRpbmcnKTtcblx0XHRcdFx0JGJveC5wcmV2KCkucHJldigpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogT24gbWVudSBoZWFkIG1vdXNlIGVudGVyIG1lbnUgZXZlbnQgaGFuZGxlci5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudFxuXHRcdCAqL1xuXHRcdHZhciBfb25NZW51SGVhZE1vdXNlRW50ZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnaG92ZXInKTtcblx0XHRcdHZhciAkdGhhdCA9ICQodGhpcyk7XG5cdFx0XHRcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lb3V0KTtcblx0XHRcdHRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfc2hvd01lbnVCb3goJHRoYXQubmV4dCgpLm5leHQoKSk7XG5cdFx0XHRcdHNob3dTdWJtZW51RGVsYXkgPSAwO1xuXHRcdFx0fSwgc2hvd1N1Ym1lbnVEZWxheSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBPbiBtZW51IGhlYWQgbW91c2UgbGVhdmUgbWVudSBldmVudCBoYW5kbGVyLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50XG5cdFx0ICovXG5cdFx0dmFyIF9vbk1lbnVIZWFkTW91c2VMZWF2ZSA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRjbGVhclRpbWVvdXQodGltZW91dCk7XG5cdFx0XHQkKHRoaXMpLnJlbW92ZUNsYXNzKCdob3ZlcicpO1xuXHRcdFx0dmFyICRib3ggPSAkKHRoaXMpLm5leHQoKS5uZXh0KCksXG5cdFx0XHRcdCRoZWFkID0gJCh0aGlzKTtcblx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICghJGJveC5oYXNDbGFzcygnaG92ZXInKSAmJiAhJGhlYWQuaGFzQ2xhc3MoJ2hvdmVyJykpIHtcblx0XHRcdFx0XHRfaGlkZU1lbnVCb3goJGJveCk7XG5cdFx0XHRcdH1cblx0XHRcdH0sIDEwKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE9uIG1lbnUgbW91c2UgbW92ZSBldmVudCBoYW5kbGVyLlxuXHRcdCAqXG5cdFx0ICogU29tZXRpbWVzIGFmdGVyIG11bHRpcGxlIGhvdmVycyB0aGUgc3VibWVudXMgcmVtYWlucyBoaWRkZW4gYW5kIHRoaXMgZXZlbnQgaGFuZGxlclxuXHRcdCAqIHdpbGwgZW5zdXJlIHRoYXQgaXQgd2lsbCBub3QgaGFwcGVuIHdoaWxlIHRoZSB1c2VyIGhvdmVycyB0aGUgbWVudSBpdGVtLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvcHRpb259IGV2ZW50XG5cdFx0ICovXG5cdFx0dmFyIF9vbk1lbnVIZWFkTW91c2VNb3ZlID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGlmICghJCh0aGlzKS5oYXNDbGFzcygnaG92ZXInKSkge1xuXHRcdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdob3ZlcicpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgJGJveCA9ICQodGhpcykubmV4dCgpLm5leHQoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE9uIG1lbnUgYm94IG1vdXNlIGVudGVyIG1lbnUgZXZlbnQgaGFuZGxlci5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudFxuXHRcdCAqL1xuXHRcdHZhciBfb25NZW51Qm94TW91c2VFbnRlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdob3ZlcicpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogT24gbWVudSBib3ggbW91c2UgbGVhdmUgbWVudSBldmVudCBoYW5kbGVyLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50XG5cdFx0ICovXG5cdFx0dmFyIF9vbk1lbnVCb3hNb3VzZUxlYXZlID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCQodGhpcykucmVtb3ZlQ2xhc3MoJ2hvdmVyJyk7XG5cdFx0XHRcblx0XHRcdHZhciAkYm94ID0gJCh0aGlzKSxcblx0XHRcdFx0JGhlYWQgPSAkYm94LnByZXYoKS5wcmV2KCk7XG5cdFx0XHRcblx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICghJGJveC5oYXNDbGFzcygnaG92ZXInKSAmJiAhJGhlYWQuaGFzQ2xhc3MoJ2hvdmVyJykpIHtcblx0XHRcdFx0XHRfaGlkZU1lbnVCb3goJGJveCk7XG5cdFx0XHRcdH1cblx0XHRcdH0sIDEwKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfb25NZW51SGVhZGluZ0Rvd24gPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0bW91c2VYID0gZXZlbnQucGFnZVg7XG5cdFx0XHRtb3VzZVkgPSBldmVudC5wYWdlWTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE9uIG1lbnUgaGVhZGluZyBjbGljayBldmVudCBoYW5kbGVyLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50XG5cdFx0ICovXG5cdFx0dmFyIF9vbk1lbnVIZWFkaW5nQ2xpY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XG5cdFx0XHQvLyBkbyBub3Qgb3BlbiBsaW5rIGlmIG1vdXNlIHdhcyBtb3ZlZCBtb3JlIHRoYW4gNXB4IGR1cmluZyBtb3VzZG93biBldmVudFxuXHRcdFx0aWYgKG1vdXNlWCA+IChldmVudC5wYWdlWCArIDUpIHx8IG1vdXNlWCA8IChldmVudC5wYWdlWCAtIDUpIHx8IG1vdXNlWSA+IChldmVudC5wYWdlWSArIDUpIHx8XG5cdFx0XHRcdG1vdXNlWSA8IChldmVudC5wYWdlWSAtXG5cdFx0XHRcdDUpKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gMSA9IGxlZnQgY2xpY2ssIDIgPSBtaWRkbGUgY2xpY2tcblx0XHRcdGlmIChldmVudC53aGljaCA9PT0gMSB8fCBldmVudC53aGljaCA9PT0gMikge1xuXHRcdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciAkaGVhZGluZyA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG5cdFx0XHRcdHZhciAkZmlyc3RTdWJJdGVtID0gJGhlYWRpbmdcblx0XHRcdFx0XHQubmV4dCgpXG5cdFx0XHRcdFx0Lm5leHQoKVxuXHRcdFx0XHRcdC5maW5kKCdsaTpmaXJzdCcpXG5cdFx0XHRcdFx0LmZpbmQoJ2E6Zmlyc3QnKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciB0YXJnZXQgPSAoZXZlbnQud2hpY2ggPT09IDEpID8gJ19zZWxmJyA6ICdfYmxhbmsnO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gT3BlbiB0aGUgZmlyc3Qgc3ViIGl0ZW0ncyBsaW5rXG5cdFx0XHRcdGlmICgkZmlyc3RTdWJJdGVtLnByb3AoJ2hyZWYnKSkge1xuXHRcdFx0XHRcdHdpbmRvdy5vcGVuKCRmaXJzdFN1Ykl0ZW0ucHJvcCgnaHJlZicpLCB0YXJnZXQpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBSZXNldCBzdWJtZW51IGRpc3BsYXkgZGVsYXkgYWZ0ZXIgbGVhdmluZyB0aGUgbGVmdCBtZW51XG5cdFx0ICovXG5cdFx0dmFyIF9yZXNldFNob3dTdWJtZW51RGVsYXkgPSBmdW5jdGlvbigpIHtcblx0XHRcdHNob3dTdWJtZW51RGVsYXkgPSBpbml0aWFsU2hvd1N1Ym1lbnVEZWxheTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNhdmUgc3VibWVudSB3aGVyZWluIHRoZSBtb3VzZURvd24gZXZlbnQgd2FzIHRyaWdnZXJlZFxuXHRcdCAqL1xuXHRcdHZhciBfb25NZW51Qm94TW91c2VEb3duID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkbW91c2VEb3duQm94ID0gJCh0aGlzKTtcblx0XHRcdG1vdXNlRG93biA9IHRydWU7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIaWRlIHN1Ym1lbnUgb24gbW91c2VVcCBldmVudCBhZnRlciBkcmFnZ2luZyBhbiBlbnRyeSBpbnRvIHRoZSBmYXZzLWJveFxuXHRcdCAqL1xuXHRcdHZhciBfb25Nb3VzZVVwID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRtb3VzZURvd24gPSBmYWxzZTtcblx0XHRcdFxuXHRcdFx0aWYgKCRtb3VzZURvd25Cb3gpIHtcblx0XHRcdFx0X2hpZGVNZW51Qm94KCRtb3VzZURvd25Cb3gpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaRSBDT05UUk9MTEVSXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBjb250cm9sbGVyLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdtb3VzZWVudGVyJywgJy5sZWZ0bWVudV9oZWFkJywgX29uTWVudUhlYWRNb3VzZUVudGVyKVxuXHRcdFx0XHQub24oJ21vdXNlbGVhdmUnLCAnLmxlZnRtZW51X2hlYWQnLCBfb25NZW51SGVhZE1vdXNlTGVhdmUpXG5cdFx0XHRcdC5vbignbW91c2Vtb3ZlJywgJy5sZWZ0bWVudV9oZWFkJywgX29uTWVudUhlYWRNb3VzZU1vdmUpXG5cdFx0XHRcdC5vbignbW91c2VlbnRlcicsICcubGVmdG1lbnVfYm94JywgX29uTWVudUJveE1vdXNlRW50ZXIpXG5cdFx0XHRcdC5vbignbW91c2VsZWF2ZScsICcubGVmdG1lbnVfYm94JywgX29uTWVudUJveE1vdXNlTGVhdmUpXG5cdFx0XHRcdC5vbignbW91c2Vkb3duJywgJy5sZWZ0bWVudV9ib3gnLCBfb25NZW51Qm94TW91c2VEb3duKVxuXHRcdFx0XHQub24oJ21vdXNlZG93bicsICcubGVmdG1lbnVfaGVhZCcsIF9vbk1lbnVIZWFkaW5nRG93bilcblx0XHRcdFx0Lm9uKCdtb3VzZXVwJywgJy5sZWZ0bWVudV9oZWFkJywgX29uTWVudUhlYWRpbmdDbGljaylcblx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlJywgX3Jlc2V0U2hvd1N1Ym1lbnVEZWxheSk7XG5cdFx0XHRcblx0XHRcdCQoZG9jdW1lbnQpLm9uKCdtb3VzZXVwJywgX29uTW91c2VVcCk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
