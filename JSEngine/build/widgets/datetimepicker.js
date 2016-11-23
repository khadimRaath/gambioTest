'use strict';

/* --------------------------------------------------------------
 datetimepicker.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Datetimepicker Widget
 *
 * This widget will convert itself or multiple elements into datetimepicker instances. Check the defaults object for a
 * list of available options.
 *
 * You can also set this module in a container element and provide the "data-datetimepicker-container" attribute and
 * this plugin will initialize all the child elements that have the "datetimepicker" class into datetimepicker widgets.
 *
 * jQuery Datetimepicker Website: {@link http://xdsoft.net/jqplugins/datetimepicker}
 * 
 * ### Options
 *
 * In addition to the options stated below, you could also add many more options shown in the
 * jQuery Datetimepicker documentation.
 *
 * **Format | `data-datetimepicker-format` | String | Optional**
 *
 * Provide the default date format. If no value is provided, the default format will be set
 * to `'d.m.Y H:i'`.
 *
 * **Lang | `data-datetimepicker-lang` | String | Optional**
 *
 * Provide the default language code. If the current language is set to english, the default
 * language code will be set to `'en-GB'`, else the language code will be set to `'de'`.
 *
 * ### Examples
 * 
 * ```html
 * <input type="text" placeholder="##.##.#### ##:##" data-gx-widget="datetimepicker" />
 * ```
 *
 * @module Admin/Widgets/datetimepicker
 * @requires jQuery-Datetimepicker-Plugin
 */
jse.widgets.module('datetimepicker', [], function (data) {

	'use strict';

	var
	/**
  * Module Selector
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Module Options
  *
  * @type {object}
  */
	defaults = {
		format: 'd.m.Y H:i',
		lang: jse.core.config.get('languageCode') === 'en' ? 'en-GB' : 'de'
	},


	/**
  * Final Module Options
  * 
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Instance
  *
  * @type {object}
  */
	module = {};

	/**
  * Initialize Module
  *
  * @param {function} done Call this method once the module is initialized.
  */
	module.init = function (done) {
		// Check if the datetimepicker plugin is already loaded. 
		if ($.fn.datetimepicker === undefined) {
			throw new Error('The $.fn.datetimepicker plugin must be loaded before the module is initialized.');
		}

		// Check if the current element is a container and thus need to initialize the children elements. 
		if (options.container !== undefined) {
			$this.find('.datetimepicker').datetimepicker(options);
		} else {
			$this.datetimepicker(options);
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGV0aW1lcGlja2VyLmpzIl0sIm5hbWVzIjpbImpzZSIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJmb3JtYXQiLCJsYW5nIiwiY29yZSIsImNvbmZpZyIsImdldCIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsImZuIiwiZGF0ZXRpbWVwaWNrZXIiLCJ1bmRlZmluZWQiLCJFcnJvciIsImNvbnRhaW5lciIsImZpbmQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFtQ0FBLElBQUlDLE9BQUosQ0FBWUMsTUFBWixDQUNDLGdCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVkMsVUFBUSxXQURFO0FBRVZDLFFBQU1SLElBQUlTLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsY0FBcEIsTUFBd0MsSUFBeEMsR0FBK0MsT0FBL0MsR0FBeUQ7QUFGckQsRUFiWjs7O0FBa0JDOzs7OztBQUtBQyxXQUFVUCxFQUFFUSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJQLFFBQW5CLEVBQTZCSCxJQUE3QixDQXZCWDs7O0FBeUJDOzs7OztBQUtBRCxVQUFTLEVBOUJWOztBQWdDQTs7Ozs7QUFLQUEsUUFBT1ksSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjtBQUNBLE1BQUlWLEVBQUVXLEVBQUYsQ0FBS0MsY0FBTCxLQUF3QkMsU0FBNUIsRUFBdUM7QUFDdEMsU0FBTSxJQUFJQyxLQUFKLENBQVUsaUZBQVYsQ0FBTjtBQUNBOztBQUVEO0FBQ0EsTUFBSVAsUUFBUVEsU0FBUixLQUFzQkYsU0FBMUIsRUFBcUM7QUFDcENkLFNBQU1pQixJQUFOLENBQVcsaUJBQVgsRUFBOEJKLGNBQTlCLENBQTZDTCxPQUE3QztBQUNBLEdBRkQsTUFFTztBQUNOUixTQUFNYSxjQUFOLENBQXFCTCxPQUFyQjtBQUNBOztBQUVERztBQUNBLEVBZEQ7O0FBZ0JBLFFBQU9iLE1BQVA7QUFDQSxDQS9ERiIsImZpbGUiOiJkYXRldGltZXBpY2tlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGF0ZXRpbWVwaWNrZXIuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRGF0ZXRpbWVwaWNrZXIgV2lkZ2V0XG4gKlxuICogVGhpcyB3aWRnZXQgd2lsbCBjb252ZXJ0IGl0c2VsZiBvciBtdWx0aXBsZSBlbGVtZW50cyBpbnRvIGRhdGV0aW1lcGlja2VyIGluc3RhbmNlcy4gQ2hlY2sgdGhlIGRlZmF1bHRzIG9iamVjdCBmb3IgYVxuICogbGlzdCBvZiBhdmFpbGFibGUgb3B0aW9ucy5cbiAqXG4gKiBZb3UgY2FuIGFsc28gc2V0IHRoaXMgbW9kdWxlIGluIGEgY29udGFpbmVyIGVsZW1lbnQgYW5kIHByb3ZpZGUgdGhlIFwiZGF0YS1kYXRldGltZXBpY2tlci1jb250YWluZXJcIiBhdHRyaWJ1dGUgYW5kXG4gKiB0aGlzIHBsdWdpbiB3aWxsIGluaXRpYWxpemUgYWxsIHRoZSBjaGlsZCBlbGVtZW50cyB0aGF0IGhhdmUgdGhlIFwiZGF0ZXRpbWVwaWNrZXJcIiBjbGFzcyBpbnRvIGRhdGV0aW1lcGlja2VyIHdpZGdldHMuXG4gKlxuICogalF1ZXJ5IERhdGV0aW1lcGlja2VyIFdlYnNpdGU6IHtAbGluayBodHRwOi8veGRzb2Z0Lm5ldC9qcXBsdWdpbnMvZGF0ZXRpbWVwaWNrZXJ9XG4gKiBcbiAqICMjIyBPcHRpb25zXG4gKlxuICogSW4gYWRkaXRpb24gdG8gdGhlIG9wdGlvbnMgc3RhdGVkIGJlbG93LCB5b3UgY291bGQgYWxzbyBhZGQgbWFueSBtb3JlIG9wdGlvbnMgc2hvd24gaW4gdGhlXG4gKiBqUXVlcnkgRGF0ZXRpbWVwaWNrZXIgZG9jdW1lbnRhdGlvbi5cbiAqXG4gKiAqKkZvcm1hdCB8IGBkYXRhLWRhdGV0aW1lcGlja2VyLWZvcm1hdGAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogUHJvdmlkZSB0aGUgZGVmYXVsdCBkYXRlIGZvcm1hdC4gSWYgbm8gdmFsdWUgaXMgcHJvdmlkZWQsIHRoZSBkZWZhdWx0IGZvcm1hdCB3aWxsIGJlIHNldFxuICogdG8gYCdkLm0uWSBIOmknYC5cbiAqXG4gKiAqKkxhbmcgfCBgZGF0YS1kYXRldGltZXBpY2tlci1sYW5nYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqXG4gKiBQcm92aWRlIHRoZSBkZWZhdWx0IGxhbmd1YWdlIGNvZGUuIElmIHRoZSBjdXJyZW50IGxhbmd1YWdlIGlzIHNldCB0byBlbmdsaXNoLCB0aGUgZGVmYXVsdFxuICogbGFuZ3VhZ2UgY29kZSB3aWxsIGJlIHNldCB0byBgJ2VuLUdCJ2AsIGVsc2UgdGhlIGxhbmd1YWdlIGNvZGUgd2lsbCBiZSBzZXQgdG8gYCdkZSdgLlxuICpcbiAqICMjIyBFeGFtcGxlc1xuICogXG4gKiBgYGBodG1sXG4gKiA8aW5wdXQgdHlwZT1cInRleHRcIiBwbGFjZWhvbGRlcj1cIiMjLiMjLiMjIyMgIyM6IyNcIiBkYXRhLWd4LXdpZGdldD1cImRhdGV0aW1lcGlja2VyXCIgLz5cbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9kYXRldGltZXBpY2tlclxuICogQHJlcXVpcmVzIGpRdWVyeS1EYXRldGltZXBpY2tlci1QbHVnaW5cbiAqL1xuanNlLndpZGdldHMubW9kdWxlKFxuXHQnZGF0ZXRpbWVwaWNrZXInLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBNb2R1bGUgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRmb3JtYXQ6ICdkLm0uWSBIOmknLFxuXHRcdFx0XHRsYW5nOiBqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSA9PT0gJ2VuJyA/ICdlbi1HQicgOiAnZGUnXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBJbnN0YW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgTW9kdWxlXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge2Z1bmN0aW9ufSBkb25lIENhbGwgdGhpcyBtZXRob2Qgb25jZSB0aGUgbW9kdWxlIGlzIGluaXRpYWxpemVkLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gQ2hlY2sgaWYgdGhlIGRhdGV0aW1lcGlja2VyIHBsdWdpbiBpcyBhbHJlYWR5IGxvYWRlZC4gXG5cdFx0XHRpZiAoJC5mbi5kYXRldGltZXBpY2tlciA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignVGhlICQuZm4uZGF0ZXRpbWVwaWNrZXIgcGx1Z2luIG11c3QgYmUgbG9hZGVkIGJlZm9yZSB0aGUgbW9kdWxlIGlzIGluaXRpYWxpemVkLicpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBDaGVjayBpZiB0aGUgY3VycmVudCBlbGVtZW50IGlzIGEgY29udGFpbmVyIGFuZCB0aHVzIG5lZWQgdG8gaW5pdGlhbGl6ZSB0aGUgY2hpbGRyZW4gZWxlbWVudHMuIFxuXHRcdFx0aWYgKG9wdGlvbnMuY29udGFpbmVyICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0JHRoaXMuZmluZCgnLmRhdGV0aW1lcGlja2VyJykuZGF0ZXRpbWVwaWNrZXIob3B0aW9ucyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkdGhpcy5kYXRldGltZXBpY2tlcihvcHRpb25zKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7ICJdfQ==
