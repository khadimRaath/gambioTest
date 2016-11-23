'use strict';

/* --------------------------------------------------------------
 event_driven_submit.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Event Driven Submit Extension
 *
 * This extension is used along with text_edit.js and ajax_search.js in the Gambio Admin 
 * "Text Edit | Texte Anpassen" page. 
 * 
 * @module Admin/Extensions/event_driven_submit
 * @ignore
 */
gx.extensions.module('event_driven_submit', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Body Element Selector
  *
  * @type {object}
  * 
  * @todo Remove unused variable. 
  */
	$body = $('body'),


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Extension Options
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
	// META INITIALIZE
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {

		$this.on('submitform', function (event, deferred) {
			jse.libs.form.prefillForm($this, deferred, false);
			$this.submit();
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImV2ZW50X2RyaXZlbl9zdWJtaXQuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJvbiIsImV2ZW50IiwiZGVmZXJyZWQiLCJqc2UiLCJsaWJzIiwiZm9ybSIsInByZWZpbGxGb3JtIiwic3VibWl0Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7OztBQVNBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FDQyxxQkFERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7OztBQU9BQyxTQUFRRCxFQUFFLE1BQUYsQ0FmVDs7O0FBaUJDOzs7OztBQUtBRSxZQUFXLEVBdEJaOzs7QUF3QkM7Ozs7O0FBS0FDLFdBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJKLElBQTdCLENBN0JYOzs7QUErQkM7Ozs7O0FBS0FELFVBQVMsRUFwQ1Y7O0FBc0NBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FBLFFBQU9RLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCUCxRQUFNUSxFQUFOLENBQVMsWUFBVCxFQUF1QixVQUFTQyxLQUFULEVBQWdCQyxRQUFoQixFQUEwQjtBQUNoREMsT0FBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFdBQWQsQ0FBMEJkLEtBQTFCLEVBQWlDVSxRQUFqQyxFQUEyQyxLQUEzQztBQUNBVixTQUFNZSxNQUFOO0FBQ0EsR0FIRDs7QUFLQVI7QUFDQSxFQVJEOztBQVVBO0FBQ0EsUUFBT1QsTUFBUDtBQUNBLENBdEVGIiwiZmlsZSI6ImV2ZW50X2RyaXZlbl9zdWJtaXQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGV2ZW50X2RyaXZlbl9zdWJtaXQuanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRXZlbnQgRHJpdmVuIFN1Ym1pdCBFeHRlbnNpb25cbiAqXG4gKiBUaGlzIGV4dGVuc2lvbiBpcyB1c2VkIGFsb25nIHdpdGggdGV4dF9lZGl0LmpzIGFuZCBhamF4X3NlYXJjaC5qcyBpbiB0aGUgR2FtYmlvIEFkbWluIFxuICogXCJUZXh0IEVkaXQgfCBUZXh0ZSBBbnBhc3NlblwiIHBhZ2UuIFxuICogXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvZXZlbnRfZHJpdmVuX3N1Ym1pdFxuICogQGlnbm9yZVxuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J2V2ZW50X2RyaXZlbl9zdWJtaXQnLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBCb2R5IEVsZW1lbnQgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICogXG5cdFx0XHQgKiBAdG9kbyBSZW1vdmUgdW51c2VkIHZhcmlhYmxlLiBcblx0XHRcdCAqL1xuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBFeHRlbnNpb25cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIEV4dGVuc2lvbiBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIE1FVEEgSU5JVElBTElaRVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSBleHRlbnNpb24sIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5vbignc3VibWl0Zm9ybScsIGZ1bmN0aW9uKGV2ZW50LCBkZWZlcnJlZCkge1xuXHRcdFx0XHRqc2UubGlicy5mb3JtLnByZWZpbGxGb3JtKCR0aGlzLCBkZWZlcnJlZCwgZmFsc2UpO1xuXHRcdFx0XHQkdGhpcy5zdWJtaXQoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
