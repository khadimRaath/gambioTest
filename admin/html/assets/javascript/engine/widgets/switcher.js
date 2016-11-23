'use strict';

/* --------------------------------------------------------------
 switcher.js 2016-10-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Switcher Widget
 *
 * This widget originates from the "switcher" mode of the existing checkbox widget. Because of the increased
 * complexity of the old widget code, the switcher mode is now server by this file. Apply the widget in a parent
 * container and it will search and convert all the checkbox/radio instances into switchers.
 *
 * ### Options
 *
 * **On State | `data-switcher-on-state` | String | Optional**
 *
 * Define the content of the "on" state.
 *
 * **Off State | `data-switcher-off-state` | String | Optional**
 *
 * Define the content of the "off" state.
 * 
 * **Selector | `data-switcher-selector` | String | Optional**
 *
 * Set the selector of the checkboxes to be converted to switcher instances. It defaults to **input:checkbox**.
 *
 * ### Examples
 *
 * In the following example the checkbox element will be converted into a single-checkbox instance.
 *
 * ```html
 * <div class="wrapper" data-gx-widget="switcher">
 *   <input type="checkbox" />
 * </div>
 * ```
 *
 * @todo Add method for disabling the switcher widget (e.g. $('#my-switcher').switcher('disabled', true));
 *
 * @module Admin/Widgets/switcher
 */
gx.widgets.module('switcher', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options
  *
  * @type {Object}
  */
	var defaults = {
		onState: '<span class="fa fa-check"></span>',
		offState: '<span class="fa fa-times"></span>',
		selector: 'input:checkbox'
	};

	/**
  * Final Options
  *
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On Switcher Click Event
  *
  * Delegate the click event to the checkbox elements which will update the DOM accordingly.
  *
  * @param {object} event
  */
	function _onSwitcherClick(event) {
		event.stopPropagation();

		if ($(this).hasClass('disabled')) {
			return false; // The switcher is disabled.
		}

		var $checkbox = $(this).find('input:checkbox');

		$checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
	}

	/**
  * On Checkbox Change
  *
  * This callback will update the display of the widget. It will perform the required animations and set the
  * respective state classes.
  */
	function _onCheckboxChange() {
		var $checkbox = $(this);
		var $switcher = $checkbox.parent();

		if (!$switcher.hasClass('checked') && $checkbox.prop('checked')) {
			$switcher.addClass('checked');
		} else if ($switcher.hasClass('checked') && !$checkbox.prop('checked')) {
			$switcher.removeClass('checked');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.find(options.selector).each(function () {
			var $checkbox = $(this);
			var title = $checkbox.prop('title') ? 'title="' + $checkbox.prop('title') + '"' : '';

			$checkbox.wrap('<div class="switcher" ' + title + '></div>').parent().append('\n\t\t\t\t\t<div class="switcher-toggler"></div>\n\t\t\t\t\t<div class="switcher-inner">\n\t\t\t\t\t\t<div class="switcher-state-on">' + options.onState + '</div>\n\t\t\t\t\t\t<div class="switcher-state-off">' + options.offState + '</div>\n\t\t\t\t\t</div>\n\t\t\t\t');

			// Bind the switcher event handlers.  
			$checkbox.parent().on('click', _onSwitcherClick).on('change', 'input:checkbox', _onCheckboxChange);

			// Trigger the change event to update the checkbox display.
			$checkbox.trigger('change');
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN3aXRjaGVyLmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9uU3RhdGUiLCJvZmZTdGF0ZSIsInNlbGVjdG9yIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vblN3aXRjaGVyQ2xpY2siLCJldmVudCIsInN0b3BQcm9wYWdhdGlvbiIsImhhc0NsYXNzIiwiJGNoZWNrYm94IiwiZmluZCIsInByb3AiLCJ0cmlnZ2VyIiwiX29uQ2hlY2tib3hDaGFuZ2UiLCIkc3dpdGNoZXIiLCJwYXJlbnQiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiaW5pdCIsImRvbmUiLCJlYWNoIiwidGl0bGUiLCJ3cmFwIiwiYXBwZW5kIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFtQ0FBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUFrQixVQUFsQixFQUE4QixFQUE5QixFQUFrQyxVQUFTQyxJQUFULEVBQWU7O0FBRWhEOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7O0FBS0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsV0FBVztBQUNoQkMsV0FBUyxtQ0FETztBQUVoQkMsWUFBVSxtQ0FGTTtBQUdoQkMsWUFBVTtBQUhNLEVBQWpCOztBQU1BOzs7OztBQUtBLEtBQU1DLFVBQVVMLEVBQUVNLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkwsUUFBbkIsRUFBNkJILElBQTdCLENBQWhCOztBQUVBOzs7OztBQUtBLEtBQU1ELFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7Ozs7QUFPQSxVQUFTVSxnQkFBVCxDQUEwQkMsS0FBMUIsRUFBaUM7QUFDaENBLFFBQU1DLGVBQU47O0FBRUEsTUFBSVQsRUFBRSxJQUFGLEVBQVFVLFFBQVIsQ0FBaUIsVUFBakIsQ0FBSixFQUFrQztBQUNqQyxVQUFPLEtBQVAsQ0FEaUMsQ0FDbkI7QUFDZDs7QUFFRCxNQUFNQyxZQUFZWCxFQUFFLElBQUYsRUFBUVksSUFBUixDQUFhLGdCQUFiLENBQWxCOztBQUVBRCxZQUNFRSxJQURGLENBQ08sU0FEUCxFQUNrQixDQUFDRixVQUFVRSxJQUFWLENBQWUsU0FBZixDQURuQixFQUVFQyxPQUZGLENBRVUsUUFGVjtBQUdBOztBQUVEOzs7Ozs7QUFNQSxVQUFTQyxpQkFBVCxHQUE2QjtBQUM1QixNQUFNSixZQUFZWCxFQUFFLElBQUYsQ0FBbEI7QUFDQSxNQUFNZ0IsWUFBWUwsVUFBVU0sTUFBVixFQUFsQjs7QUFFQSxNQUFJLENBQUNELFVBQVVOLFFBQVYsQ0FBbUIsU0FBbkIsQ0FBRCxJQUFrQ0MsVUFBVUUsSUFBVixDQUFlLFNBQWYsQ0FBdEMsRUFBaUU7QUFDaEVHLGFBQVVFLFFBQVYsQ0FBbUIsU0FBbkI7QUFDQSxHQUZELE1BRU8sSUFBSUYsVUFBVU4sUUFBVixDQUFtQixTQUFuQixLQUFpQyxDQUFDQyxVQUFVRSxJQUFWLENBQWUsU0FBZixDQUF0QyxFQUFpRTtBQUN2RUcsYUFBVUcsV0FBVixDQUFzQixTQUF0QjtBQUNBO0FBQ0Q7O0FBRUQ7QUFDQTtBQUNBOztBQUVBdEIsUUFBT3VCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJ0QixRQUFNYSxJQUFOLENBQVdQLFFBQVFELFFBQW5CLEVBQTZCa0IsSUFBN0IsQ0FBa0MsWUFBVztBQUM1QyxPQUFNWCxZQUFZWCxFQUFFLElBQUYsQ0FBbEI7QUFDQSxPQUFNdUIsUUFBUVosVUFBVUUsSUFBVixDQUFlLE9BQWYsZ0JBQW9DRixVQUFVRSxJQUFWLENBQWUsT0FBZixDQUFwQyxTQUFpRSxFQUEvRTs7QUFFQUYsYUFDRWEsSUFERiw0QkFDZ0NELEtBRGhDLGNBRUVOLE1BRkYsR0FHRVEsTUFIRiwySUFNb0NwQixRQUFRSCxPQU41Qyw0REFPcUNHLFFBQVFGLFFBUDdDOztBQVdBO0FBQ0FRLGFBQ0VNLE1BREYsR0FFRVMsRUFGRixDQUVLLE9BRkwsRUFFY25CLGdCQUZkLEVBR0VtQixFQUhGLENBR0ssUUFITCxFQUdlLGdCQUhmLEVBR2lDWCxpQkFIakM7O0FBS0E7QUFDQUosYUFBVUcsT0FBVixDQUFrQixRQUFsQjtBQUNBLEdBdkJEOztBQXlCQU87QUFDQSxFQTNCRDs7QUE2QkEsUUFBT3hCLE1BQVA7QUFFQSxDQXJIRCIsImZpbGUiOiJzd2l0Y2hlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBzd2l0Y2hlci5qcyAyMDE2LTEwLTE3XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqICMjIFN3aXRjaGVyIFdpZGdldFxyXG4gKlxyXG4gKiBUaGlzIHdpZGdldCBvcmlnaW5hdGVzIGZyb20gdGhlIFwic3dpdGNoZXJcIiBtb2RlIG9mIHRoZSBleGlzdGluZyBjaGVja2JveCB3aWRnZXQuIEJlY2F1c2Ugb2YgdGhlIGluY3JlYXNlZFxyXG4gKiBjb21wbGV4aXR5IG9mIHRoZSBvbGQgd2lkZ2V0IGNvZGUsIHRoZSBzd2l0Y2hlciBtb2RlIGlzIG5vdyBzZXJ2ZXIgYnkgdGhpcyBmaWxlLiBBcHBseSB0aGUgd2lkZ2V0IGluIGEgcGFyZW50XHJcbiAqIGNvbnRhaW5lciBhbmQgaXQgd2lsbCBzZWFyY2ggYW5kIGNvbnZlcnQgYWxsIHRoZSBjaGVja2JveC9yYWRpbyBpbnN0YW5jZXMgaW50byBzd2l0Y2hlcnMuXHJcbiAqXHJcbiAqICMjIyBPcHRpb25zXHJcbiAqXHJcbiAqICoqT24gU3RhdGUgfCBgZGF0YS1zd2l0Y2hlci1vbi1zdGF0ZWAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqXHJcbiAqIERlZmluZSB0aGUgY29udGVudCBvZiB0aGUgXCJvblwiIHN0YXRlLlxyXG4gKlxyXG4gKiAqKk9mZiBTdGF0ZSB8IGBkYXRhLXN3aXRjaGVyLW9mZi1zdGF0ZWAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqXHJcbiAqIERlZmluZSB0aGUgY29udGVudCBvZiB0aGUgXCJvZmZcIiBzdGF0ZS5cclxuICogXHJcbiAqICoqU2VsZWN0b3IgfCBgZGF0YS1zd2l0Y2hlci1zZWxlY3RvcmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqXHJcbiAqIFNldCB0aGUgc2VsZWN0b3Igb2YgdGhlIGNoZWNrYm94ZXMgdG8gYmUgY29udmVydGVkIHRvIHN3aXRjaGVyIGluc3RhbmNlcy4gSXQgZGVmYXVsdHMgdG8gKippbnB1dDpjaGVja2JveCoqLlxyXG4gKlxyXG4gKiAjIyMgRXhhbXBsZXNcclxuICpcclxuICogSW4gdGhlIGZvbGxvd2luZyBleGFtcGxlIHRoZSBjaGVja2JveCBlbGVtZW50IHdpbGwgYmUgY29udmVydGVkIGludG8gYSBzaW5nbGUtY2hlY2tib3ggaW5zdGFuY2UuXHJcbiAqXHJcbiAqIGBgYGh0bWxcclxuICogPGRpdiBjbGFzcz1cIndyYXBwZXJcIiBkYXRhLWd4LXdpZGdldD1cInN3aXRjaGVyXCI+XHJcbiAqICAgPGlucHV0IHR5cGU9XCJjaGVja2JveFwiIC8+XHJcbiAqIDwvZGl2PlxyXG4gKiBgYGBcclxuICpcclxuICogQHRvZG8gQWRkIG1ldGhvZCBmb3IgZGlzYWJsaW5nIHRoZSBzd2l0Y2hlciB3aWRnZXQgKGUuZy4gJCgnI215LXN3aXRjaGVyJykuc3dpdGNoZXIoJ2Rpc2FibGVkJywgdHJ1ZSkpO1xyXG4gKlxyXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvc3dpdGNoZXJcclxuICovXHJcbmd4LndpZGdldHMubW9kdWxlKCdzd2l0Y2hlcicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogRGVmYXVsdCBPcHRpb25zXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IGRlZmF1bHRzID0ge1xyXG5cdFx0b25TdGF0ZTogJzxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+JyxcclxuXHRcdG9mZlN0YXRlOiAnPHNwYW4gY2xhc3M9XCJmYSBmYS10aW1lc1wiPjwvc3Bhbj4nLFxyXG5cdFx0c2VsZWN0b3I6ICdpbnB1dDpjaGVja2JveCdcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZpbmFsIE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFN3aXRjaGVyIENsaWNrIEV2ZW50XHJcblx0ICpcclxuXHQgKiBEZWxlZ2F0ZSB0aGUgY2xpY2sgZXZlbnQgdG8gdGhlIGNoZWNrYm94IGVsZW1lbnRzIHdoaWNoIHdpbGwgdXBkYXRlIHRoZSBET00gYWNjb3JkaW5nbHkuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25Td2l0Y2hlckNsaWNrKGV2ZW50KSB7XHJcblx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuXHRcdFxyXG5cdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcclxuXHRcdFx0cmV0dXJuIGZhbHNlOyAvLyBUaGUgc3dpdGNoZXIgaXMgZGlzYWJsZWQuXHJcblx0XHR9XHJcblx0XHRcclxuXHRcdGNvbnN0ICRjaGVja2JveCA9ICQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKTtcclxuXHRcdFxyXG5cdFx0JGNoZWNrYm94XHJcblx0XHRcdC5wcm9wKCdjaGVja2VkJywgISRjaGVja2JveC5wcm9wKCdjaGVja2VkJykpXHJcblx0XHRcdC50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gQ2hlY2tib3ggQ2hhbmdlXHJcblx0ICpcclxuXHQgKiBUaGlzIGNhbGxiYWNrIHdpbGwgdXBkYXRlIHRoZSBkaXNwbGF5IG9mIHRoZSB3aWRnZXQuIEl0IHdpbGwgcGVyZm9ybSB0aGUgcmVxdWlyZWQgYW5pbWF0aW9ucyBhbmQgc2V0IHRoZVxyXG5cdCAqIHJlc3BlY3RpdmUgc3RhdGUgY2xhc3Nlcy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25DaGVja2JveENoYW5nZSgpIHtcclxuXHRcdGNvbnN0ICRjaGVja2JveCA9ICQodGhpcyk7XHJcblx0XHRjb25zdCAkc3dpdGNoZXIgPSAkY2hlY2tib3gucGFyZW50KCk7XHJcblx0XHRcclxuXHRcdGlmICghJHN3aXRjaGVyLmhhc0NsYXNzKCdjaGVja2VkJykgJiYgJGNoZWNrYm94LnByb3AoJ2NoZWNrZWQnKSkge1xyXG5cdFx0XHQkc3dpdGNoZXIuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcclxuXHRcdH0gZWxzZSBpZiAoJHN3aXRjaGVyLmhhc0NsYXNzKCdjaGVja2VkJykgJiYgISRjaGVja2JveC5wcm9wKCdjaGVja2VkJykpIHtcclxuXHRcdFx0JHN3aXRjaGVyLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkdGhpcy5maW5kKG9wdGlvbnMuc2VsZWN0b3IpLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdGNvbnN0ICRjaGVja2JveCA9ICQodGhpcyk7XHJcblx0XHRcdGNvbnN0IHRpdGxlID0gJGNoZWNrYm94LnByb3AoJ3RpdGxlJykgPyBgdGl0bGU9XCIkeyRjaGVja2JveC5wcm9wKCd0aXRsZScpfVwiYCA6ICcnO1xyXG5cdFx0XHRcclxuXHRcdFx0JGNoZWNrYm94XHJcblx0XHRcdFx0LndyYXAoYDxkaXYgY2xhc3M9XCJzd2l0Y2hlclwiICR7dGl0bGV9PjwvZGl2PmApXHJcblx0XHRcdFx0LnBhcmVudCgpXHJcblx0XHRcdFx0LmFwcGVuZChgXHJcblx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwic3dpdGNoZXItdG9nZ2xlclwiPjwvZGl2PlxyXG5cdFx0XHRcdFx0PGRpdiBjbGFzcz1cInN3aXRjaGVyLWlubmVyXCI+XHJcblx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiPiR7b3B0aW9ucy5vblN0YXRlfTwvZGl2PlxyXG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCI+JHtvcHRpb25zLm9mZlN0YXRlfTwvZGl2PlxyXG5cdFx0XHRcdFx0PC9kaXY+XHJcblx0XHRcdFx0YCk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBCaW5kIHRoZSBzd2l0Y2hlciBldmVudCBoYW5kbGVycy4gIFxyXG5cdFx0XHQkY2hlY2tib3hcclxuXHRcdFx0XHQucGFyZW50KClcclxuXHRcdFx0XHQub24oJ2NsaWNrJywgX29uU3dpdGNoZXJDbGljaylcclxuXHRcdFx0XHQub24oJ2NoYW5nZScsICdpbnB1dDpjaGVja2JveCcsIF9vbkNoZWNrYm94Q2hhbmdlKTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCB0byB1cGRhdGUgdGhlIGNoZWNrYm94IGRpc3BsYXkuXHJcblx0XHRcdCRjaGVja2JveC50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsgIl19
