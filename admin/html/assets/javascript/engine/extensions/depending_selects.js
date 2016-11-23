'use strict';

/* --------------------------------------------------------------
 depending_selects.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Depending Selects Extension
 *
 * Extension that fills other dropdowns with data that relate with the value of the
 * dropdown the listener is bound on.
 *
 * @module Admin/Extensions/depending_selects
 * 
 * @deprecated Since JS Engine v1.3
 * 
 * @ignore
 */
gx.extensions.module('depending_selects', ['form', 'fallback'], function (data) {

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
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		'cache': false, // Cache requested data, so that an ajax is only called once
		'requestOnInit': true // Update the values on init
	},


	/**
  * Cache Object
  *
  * @type {object}
  */
	cache = {},


	/**
  * Final Options
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
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Generate Options
  *
  * Function that generates the option fields for the "other" dropdowns
  *
  * @param {object} dataset Data given by the AJAX-call.
  */
	var _generateOptions = function _generateOptions(dataset) {
		$.each(dataset, function (index, value) {
			var $select = $this.find(index);
			$select.empty();
			jse.libs.form.createOptions($select, value, false, false);
		});
	};

	/**
  * Change Handler
  *
  * Event handler for the change-event on the main dropdown.
  */
	var _changeHandler = function _changeHandler() {
		var $self = $(this),
		    $option = $self.children(':selected'),
		    dataset = jse.libs.fallback._data($option, 'depending_selects');

		if (cache[dataset.url]) {
			// Use cached data if available
			_generateOptions(cache[dataset.url]);
		} else if (dataset.url) {
			// If an URL is given, request the data via an AJAX-call.
			$.get(dataset.url).done(function (result) {
				if (result.success) {
					if (options.cache) {
						// Cache the data if the option is given
						cache[dataset.url] = result.data;
					}
					_generateOptions(result.data);
				}
			});
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize function of the extension, called by the engine.
  */
	module.init = function (done) {
		// Display Deprecation Mark
		jse.core.debug.warn('The "depending_selects" extension is deprecated as of v1.3.0, do not use it ' + 'on new pages.');

		// Bind the change handler on the main dropdown object.
		var $source = $this.find(options.target);
		$source.on('change', _changeHandler);

		// Sets the values of the other dropdowns.
		if (options.requestOnInit) {
			$source.trigger('change', []);
		}

		done();
	};

	// Return data to module engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRlcGVuZGluZ19zZWxlY3RzLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImNhY2hlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9nZW5lcmF0ZU9wdGlvbnMiLCJkYXRhc2V0IiwiZWFjaCIsImluZGV4IiwidmFsdWUiLCIkc2VsZWN0IiwiZmluZCIsImVtcHR5IiwianNlIiwibGlicyIsImZvcm0iLCJjcmVhdGVPcHRpb25zIiwiX2NoYW5nZUhhbmRsZXIiLCIkc2VsZiIsIiRvcHRpb24iLCJjaGlsZHJlbiIsImZhbGxiYWNrIiwiX2RhdGEiLCJ1cmwiLCJnZXQiLCJkb25lIiwicmVzdWx0Iiwic3VjY2VzcyIsImluaXQiLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiJHNvdXJjZSIsInRhcmdldCIsIm9uIiwicmVxdWVzdE9uSW5pdCIsInRyaWdnZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7O0FBWUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLG1CQURELEVBR0MsQ0FBQyxNQUFELEVBQVMsVUFBVCxDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVixXQUFTLEtBREMsRUFDTTtBQUNoQixtQkFBaUIsSUFGUCxDQUVZO0FBRlosRUFiWjs7O0FBa0JDOzs7OztBQUtBQyxTQUFRLEVBdkJUOzs7QUF5QkM7Ozs7O0FBS0FDLFdBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJILElBQTdCLENBOUJYOzs7QUFnQ0M7Ozs7O0FBS0FELFVBQVMsRUFyQ1Y7O0FBdUNBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7OztBQU9BLEtBQUlRLG1CQUFtQixTQUFuQkEsZ0JBQW1CLENBQVNDLE9BQVQsRUFBa0I7QUFDeENOLElBQUVPLElBQUYsQ0FBT0QsT0FBUCxFQUFnQixVQUFTRSxLQUFULEVBQWdCQyxLQUFoQixFQUF1QjtBQUN0QyxPQUFJQyxVQUFVWCxNQUFNWSxJQUFOLENBQVdILEtBQVgsQ0FBZDtBQUNBRSxXQUFRRSxLQUFSO0FBQ0FDLE9BQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxhQUFkLENBQTRCTixPQUE1QixFQUFxQ0QsS0FBckMsRUFBNEMsS0FBNUMsRUFBbUQsS0FBbkQ7QUFDQSxHQUpEO0FBS0EsRUFORDs7QUFRQTs7Ozs7QUFLQSxLQUFJUSxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0IsTUFBSUMsUUFBUWxCLEVBQUUsSUFBRixDQUFaO0FBQUEsTUFDQ21CLFVBQVVELE1BQU1FLFFBQU4sQ0FBZSxXQUFmLENBRFg7QUFBQSxNQUVDZCxVQUFVTyxJQUFJQyxJQUFKLENBQVNPLFFBQVQsQ0FBa0JDLEtBQWxCLENBQXdCSCxPQUF4QixFQUFpQyxtQkFBakMsQ0FGWDs7QUFJQSxNQUFJakIsTUFBTUksUUFBUWlCLEdBQWQsQ0FBSixFQUF3QjtBQUN2QjtBQUNBbEIsb0JBQWlCSCxNQUFNSSxRQUFRaUIsR0FBZCxDQUFqQjtBQUNBLEdBSEQsTUFHTyxJQUFJakIsUUFBUWlCLEdBQVosRUFBaUI7QUFDdkI7QUFDQXZCLEtBQUV3QixHQUFGLENBQU1sQixRQUFRaUIsR0FBZCxFQUFtQkUsSUFBbkIsQ0FBd0IsVUFBU0MsTUFBVCxFQUFpQjtBQUN4QyxRQUFJQSxPQUFPQyxPQUFYLEVBQW9CO0FBQ25CLFNBQUl4QixRQUFRRCxLQUFaLEVBQW1CO0FBQ2xCO0FBQ0FBLFlBQU1JLFFBQVFpQixHQUFkLElBQXFCRyxPQUFPNUIsSUFBNUI7QUFDQTtBQUNETyxzQkFBaUJxQixPQUFPNUIsSUFBeEI7QUFDQTtBQUNELElBUkQ7QUFTQTtBQUNELEVBcEJEOztBQXNCQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBRCxRQUFPK0IsSUFBUCxHQUFjLFVBQVNILElBQVQsRUFBZTtBQUM1QjtBQUNBWixNQUFJZ0IsSUFBSixDQUFTQyxLQUFULENBQWVDLElBQWYsQ0FBb0IsaUZBQ2pCLGVBREg7O0FBR0E7QUFDQSxNQUFJQyxVQUFVakMsTUFBTVksSUFBTixDQUFXUixRQUFROEIsTUFBbkIsQ0FBZDtBQUNBRCxVQUFRRSxFQUFSLENBQVcsUUFBWCxFQUFxQmpCLGNBQXJCOztBQUVBO0FBQ0EsTUFBSWQsUUFBUWdDLGFBQVosRUFBMkI7QUFDMUJILFdBQVFJLE9BQVIsQ0FBZ0IsUUFBaEIsRUFBMEIsRUFBMUI7QUFDQTs7QUFFRFg7QUFDQSxFQWZEOztBQWlCQTtBQUNBLFFBQU81QixNQUFQO0FBQ0EsQ0E1SEYiLCJmaWxlIjoiZGVwZW5kaW5nX3NlbGVjdHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRlcGVuZGluZ19zZWxlY3RzLmpzIDIwMTUtMTAtMTUgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIERlcGVuZGluZyBTZWxlY3RzIEV4dGVuc2lvblxuICpcbiAqIEV4dGVuc2lvbiB0aGF0IGZpbGxzIG90aGVyIGRyb3Bkb3ducyB3aXRoIGRhdGEgdGhhdCByZWxhdGUgd2l0aCB0aGUgdmFsdWUgb2YgdGhlXG4gKiBkcm9wZG93biB0aGUgbGlzdGVuZXIgaXMgYm91bmQgb24uXG4gKlxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2RlcGVuZGluZ19zZWxlY3RzXG4gKiBcbiAqIEBkZXByZWNhdGVkIFNpbmNlIEpTIEVuZ2luZSB2MS4zXG4gKiBcbiAqIEBpZ25vcmVcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCdkZXBlbmRpbmdfc2VsZWN0cycsXG5cdFxuXHRbJ2Zvcm0nLCAnZmFsbGJhY2snXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBFeHRlbnNpb24gUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQnY2FjaGUnOiBmYWxzZSwgLy8gQ2FjaGUgcmVxdWVzdGVkIGRhdGEsIHNvIHRoYXQgYW4gYWpheCBpcyBvbmx5IGNhbGxlZCBvbmNlXG5cdFx0XHRcdCdyZXF1ZXN0T25Jbml0JzogdHJ1ZSAvLyBVcGRhdGUgdGhlIHZhbHVlcyBvbiBpbml0XG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIENhY2hlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGNhY2hlID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogR2VuZXJhdGUgT3B0aW9uc1xuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBnZW5lcmF0ZXMgdGhlIG9wdGlvbiBmaWVsZHMgZm9yIHRoZSBcIm90aGVyXCIgZHJvcGRvd25zXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZGF0YXNldCBEYXRhIGdpdmVuIGJ5IHRoZSBBSkFYLWNhbGwuXG5cdFx0ICovXG5cdFx0dmFyIF9nZW5lcmF0ZU9wdGlvbnMgPSBmdW5jdGlvbihkYXRhc2V0KSB7XG5cdFx0XHQkLmVhY2goZGF0YXNldCwgZnVuY3Rpb24oaW5kZXgsIHZhbHVlKSB7XG5cdFx0XHRcdHZhciAkc2VsZWN0ID0gJHRoaXMuZmluZChpbmRleCk7XG5cdFx0XHRcdCRzZWxlY3QuZW1wdHkoKTtcblx0XHRcdFx0anNlLmxpYnMuZm9ybS5jcmVhdGVPcHRpb25zKCRzZWxlY3QsIHZhbHVlLCBmYWxzZSwgZmFsc2UpO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGFuZ2UgSGFuZGxlclxuXHRcdCAqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGNoYW5nZS1ldmVudCBvbiB0aGUgbWFpbiBkcm9wZG93bi5cblx0XHQgKi9cblx0XHR2YXIgX2NoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRvcHRpb24gPSAkc2VsZi5jaGlsZHJlbignOnNlbGVjdGVkJyksXG5cdFx0XHRcdGRhdGFzZXQgPSBqc2UubGlicy5mYWxsYmFjay5fZGF0YSgkb3B0aW9uLCAnZGVwZW5kaW5nX3NlbGVjdHMnKTtcblx0XHRcdFxuXHRcdFx0aWYgKGNhY2hlW2RhdGFzZXQudXJsXSkge1xuXHRcdFx0XHQvLyBVc2UgY2FjaGVkIGRhdGEgaWYgYXZhaWxhYmxlXG5cdFx0XHRcdF9nZW5lcmF0ZU9wdGlvbnMoY2FjaGVbZGF0YXNldC51cmxdKTtcblx0XHRcdH0gZWxzZSBpZiAoZGF0YXNldC51cmwpIHtcblx0XHRcdFx0Ly8gSWYgYW4gVVJMIGlzIGdpdmVuLCByZXF1ZXN0IHRoZSBkYXRhIHZpYSBhbiBBSkFYLWNhbGwuXG5cdFx0XHRcdCQuZ2V0KGRhdGFzZXQudXJsKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0aWYgKG9wdGlvbnMuY2FjaGUpIHtcblx0XHRcdFx0XHRcdFx0Ly8gQ2FjaGUgdGhlIGRhdGEgaWYgdGhlIG9wdGlvbiBpcyBnaXZlblxuXHRcdFx0XHRcdFx0XHRjYWNoZVtkYXRhc2V0LnVybF0gPSByZXN1bHQuZGF0YTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdF9nZW5lcmF0ZU9wdGlvbnMocmVzdWx0LmRhdGEpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgZnVuY3Rpb24gb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBEaXNwbGF5IERlcHJlY2F0aW9uIE1hcmtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBcImRlcGVuZGluZ19zZWxlY3RzXCIgZXh0ZW5zaW9uIGlzIGRlcHJlY2F0ZWQgYXMgb2YgdjEuMy4wLCBkbyBub3QgdXNlIGl0ICdcblx0XHRcdFx0KyAnb24gbmV3IHBhZ2VzLicpO1xuXHRcdFx0XG5cdFx0XHQvLyBCaW5kIHRoZSBjaGFuZ2UgaGFuZGxlciBvbiB0aGUgbWFpbiBkcm9wZG93biBvYmplY3QuXG5cdFx0XHR2YXIgJHNvdXJjZSA9ICR0aGlzLmZpbmQob3B0aW9ucy50YXJnZXQpO1xuXHRcdFx0JHNvdXJjZS5vbignY2hhbmdlJywgX2NoYW5nZUhhbmRsZXIpO1xuXHRcdFx0XG5cdFx0XHQvLyBTZXRzIHRoZSB2YWx1ZXMgb2YgdGhlIG90aGVyIGRyb3Bkb3ducy5cblx0XHRcdGlmIChvcHRpb25zLnJlcXVlc3RPbkluaXQpIHtcblx0XHRcdFx0JHNvdXJjZS50cmlnZ2VyKCdjaGFuZ2UnLCBbXSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
