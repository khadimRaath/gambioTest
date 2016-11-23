'use strict';

/* --------------------------------------------------------------
 tooltip.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tooltip Widget
 *
 * Enables qTip2 tooltips for child elements with a title attribute. You can change the default tooltip 
 * position and other options, if you set a data-tooltip-position attribute to the parent element.
 *
 * **Important:** If you use this widgets on elements inside a modal then it will not work,
 * because the modal elements are reset before they are displayed.
 *
 * ### Example
 *  
 * ```html
 * <form data-gx-widget="tooltip">
 *   <input type="text" title="This is a tooltip widget" />
 * </form>
 * ```
 * 
 * @module Admin/Widgets/tooltip
 * @requires jQuery-qTip2-Plugin
 */
gx.widgets.module('tooltip', [],

/** @lends module:Widgets/tooltip */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Widget Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Widget Options
  *
  * @type {object}
  * @todo This options are not applied anywhere.
  */
	defaults = {
		position: {
			my: 'left+10 center',
			at: 'right center'
		}
	},


	/**
  * Final Widget Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  *
  * @todo Make more configuration possible since qtip2 is pretty flexible.
  */
	module.init = function (done) {
		$this.find('[title]').qtip({
			style: {
				classes: 'qtip-tipsy'
			},
			position: {
				my: 'bottom+200 top center',
				at: 'top center'
			}
		});
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRvb2x0aXAuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwicG9zaXRpb24iLCJteSIsImF0Iiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwiZmluZCIsInF0aXAiLCJzdHlsZSIsImNsYXNzZXMiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFvQkFBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUNDLFNBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7Ozs7QUFNQUMsWUFBVztBQUNWQyxZQUFVO0FBQ1RDLE9BQUksZ0JBREs7QUFFVEMsT0FBSTtBQUZLO0FBREEsRUFkWjs7O0FBcUJDOzs7OztBQUtBQyxXQUFVTCxFQUFFTSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJMLFFBQW5CLEVBQTZCSCxJQUE3QixDQTFCWDs7O0FBNEJDOzs7OztBQUtBRCxVQUFTLEVBakNWOztBQW1DQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0FBLFFBQU9VLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJULFFBQU1VLElBQU4sQ0FBVyxTQUFYLEVBQXNCQyxJQUF0QixDQUEyQjtBQUMxQkMsVUFBTztBQUNOQyxhQUFTO0FBREgsSUFEbUI7QUFJMUJWLGFBQVU7QUFDVEMsUUFBSSx1QkFESztBQUVUQyxRQUFJO0FBRks7QUFKZ0IsR0FBM0I7QUFTQUk7QUFDQSxFQVhEOztBQWFBO0FBQ0EsUUFBT1gsTUFBUDtBQUNBLENBMUVGIiwiZmlsZSI6InRvb2x0aXAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRvb2x0aXAuanMgMjAxNi0wMi0xOSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgVG9vbHRpcCBXaWRnZXRcbiAqXG4gKiBFbmFibGVzIHFUaXAyIHRvb2x0aXBzIGZvciBjaGlsZCBlbGVtZW50cyB3aXRoIGEgdGl0bGUgYXR0cmlidXRlLiBZb3UgY2FuIGNoYW5nZSB0aGUgZGVmYXVsdCB0b29sdGlwIFxuICogcG9zaXRpb24gYW5kIG90aGVyIG9wdGlvbnMsIGlmIHlvdSBzZXQgYSBkYXRhLXRvb2x0aXAtcG9zaXRpb24gYXR0cmlidXRlIHRvIHRoZSBwYXJlbnQgZWxlbWVudC5cbiAqXG4gKiAqKkltcG9ydGFudDoqKiBJZiB5b3UgdXNlIHRoaXMgd2lkZ2V0cyBvbiBlbGVtZW50cyBpbnNpZGUgYSBtb2RhbCB0aGVuIGl0IHdpbGwgbm90IHdvcmssXG4gKiBiZWNhdXNlIHRoZSBtb2RhbCBlbGVtZW50cyBhcmUgcmVzZXQgYmVmb3JlIHRoZXkgYXJlIGRpc3BsYXllZC5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICogIFxuICogYGBgaHRtbFxuICogPGZvcm0gZGF0YS1neC13aWRnZXQ9XCJ0b29sdGlwXCI+XG4gKiAgIDxpbnB1dCB0eXBlPVwidGV4dFwiIHRpdGxlPVwiVGhpcyBpcyBhIHRvb2x0aXAgd2lkZ2V0XCIgLz5cbiAqIDwvZm9ybT5cbiAqIGBgYFxuICogXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvdG9vbHRpcFxuICogQHJlcXVpcmVzIGpRdWVyeS1xVGlwMi1QbHVnaW5cbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCd0b29sdGlwJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6V2lkZ2V0cy90b29sdGlwICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqIEB0b2RvIFRoaXMgb3B0aW9ucyBhcmUgbm90IGFwcGxpZWQgYW55d2hlcmUuXG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRwb3NpdGlvbjoge1xuXHRcdFx0XHRcdG15OiAnbGVmdCsxMCBjZW50ZXInLFxuXHRcdFx0XHRcdGF0OiAncmlnaHQgY2VudGVyJ1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICpcblx0XHQgKiBAdG9kbyBNYWtlIG1vcmUgY29uZmlndXJhdGlvbiBwb3NzaWJsZSBzaW5jZSBxdGlwMiBpcyBwcmV0dHkgZmxleGlibGUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpcy5maW5kKCdbdGl0bGVdJykucXRpcCh7XG5cdFx0XHRcdHN0eWxlOiB7XG5cdFx0XHRcdFx0Y2xhc3NlczogJ3F0aXAtdGlwc3knXG5cdFx0XHRcdH0sXG5cdFx0XHRcdHBvc2l0aW9uOiB7XG5cdFx0XHRcdFx0bXk6ICdib3R0b20rMjAwIHRvcCBjZW50ZXInLFxuXHRcdFx0XHRcdGF0OiAndG9wIGNlbnRlcidcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
