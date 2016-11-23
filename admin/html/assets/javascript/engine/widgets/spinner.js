'use strict';

/* --------------------------------------------------------------
 spinner.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Spinner Widget
 *
 * Converts a simple text input element to a value spinner.
 *
 * jQueryUI Spinner API: {@link http://api.jqueryui.com/slider}
 * 
 * ### Options
 *
 * **Min | `data-spinner-min` | Number | Optional**
 *
 * The minimum value of the spinner. If no value is provided, no minimum limit is set.
 *
 * **Max | `data-spinner-max` | Number | Optional**
 *
 * The maximum value of the spinner. If no value is provided, no maximum limit is set.
 *
 * ### Example
 *
 * ```html
 * <input type="text" data-gx-widget="spinner" data-spinner-min="1" data-spinner-max="10" />
 * ```
 *
 * @module Admin/Widgets/spinner
 * @requires jQueryUI-Library
 */
gx.widgets.module('spinner', [], function (data) {

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
  */
	defaults = {},


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
  */
	module.init = function (done) {
		$this.spinner(options);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNwaW5uZXIuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwic3Bpbm5lciJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTBCQUEsR0FBR0MsT0FBSCxDQUFXQyxNQUFYLENBQ0MsU0FERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUQsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUEsUUFBT08sSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1Qk4sUUFBTU8sT0FBTixDQUFjSixPQUFkO0FBQ0FHO0FBQ0EsRUFIRDs7QUFLQTtBQUNBLFFBQU9SLE1BQVA7QUFDQSxDQXhERiIsImZpbGUiOiJzcGlubmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzcGlubmVyLmpzIDIwMTYtMDItMTkgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFNwaW5uZXIgV2lkZ2V0XG4gKlxuICogQ29udmVydHMgYSBzaW1wbGUgdGV4dCBpbnB1dCBlbGVtZW50IHRvIGEgdmFsdWUgc3Bpbm5lci5cbiAqXG4gKiBqUXVlcnlVSSBTcGlubmVyIEFQSToge0BsaW5rIGh0dHA6Ly9hcGkuanF1ZXJ5dWkuY29tL3NsaWRlcn1cbiAqIFxuICogIyMjIE9wdGlvbnNcbiAqXG4gKiAqKk1pbiB8IGBkYXRhLXNwaW5uZXItbWluYCB8IE51bWJlciB8IE9wdGlvbmFsKipcbiAqXG4gKiBUaGUgbWluaW11bSB2YWx1ZSBvZiB0aGUgc3Bpbm5lci4gSWYgbm8gdmFsdWUgaXMgcHJvdmlkZWQsIG5vIG1pbmltdW0gbGltaXQgaXMgc2V0LlxuICpcbiAqICoqTWF4IHwgYGRhdGEtc3Bpbm5lci1tYXhgIHwgTnVtYmVyIHwgT3B0aW9uYWwqKlxuICpcbiAqIFRoZSBtYXhpbXVtIHZhbHVlIG9mIHRoZSBzcGlubmVyLiBJZiBubyB2YWx1ZSBpcyBwcm92aWRlZCwgbm8gbWF4aW11bSBsaW1pdCBpcyBzZXQuXG4gKlxuICogIyMjIEV4YW1wbGVcbiAqXG4gKiBgYGBodG1sXG4gKiA8aW5wdXQgdHlwZT1cInRleHRcIiBkYXRhLWd4LXdpZGdldD1cInNwaW5uZXJcIiBkYXRhLXNwaW5uZXItbWluPVwiMVwiIGRhdGEtc3Bpbm5lci1tYXg9XCIxMFwiIC8+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvc3Bpbm5lclxuICogQHJlcXVpcmVzIGpRdWVyeVVJLUxpYnJhcnlcbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdzcGlubmVyJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzLnNwaW5uZXIob3B0aW9ucyk7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
