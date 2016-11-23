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
 * @deprecated Since v1.4, will be removed in v1.6. Use the one from JSE/Widgets namespace.
 * 
 * @module Admin/Widgets/datetimepicker
 * @requires jQuery-Datetimepicker-Plugin
 */
gx.widgets.module('datetimepicker', [], function (data) {

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGV0aW1lcGlja2VyLmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImZvcm1hdCIsImxhbmciLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0Iiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwiZm4iLCJkYXRldGltZXBpY2tlciIsInVuZGVmaW5lZCIsIkVycm9yIiwiY29udGFpbmVyIiwiZmluZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBcUNBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxnQkFERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1ZDLFVBQVEsV0FERTtBQUVWQyxRQUFNQyxJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLGNBQXBCLE1BQXdDLElBQXhDLEdBQStDLE9BQS9DLEdBQXlEO0FBRnJELEVBYlo7OztBQWtCQzs7Ozs7QUFLQUMsV0FBVVIsRUFBRVMsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CUixRQUFuQixFQUE2QkgsSUFBN0IsQ0F2Qlg7OztBQXlCQzs7Ozs7QUFLQUQsVUFBUyxFQTlCVjs7QUFnQ0E7Ozs7O0FBS0FBLFFBQU9hLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI7QUFDQSxNQUFJWCxFQUFFWSxFQUFGLENBQUtDLGNBQUwsS0FBd0JDLFNBQTVCLEVBQXVDO0FBQ3RDLFNBQU0sSUFBSUMsS0FBSixDQUFVLGlGQUFWLENBQU47QUFDQTs7QUFFRDtBQUNBLE1BQUlQLFFBQVFRLFNBQVIsS0FBc0JGLFNBQTFCLEVBQXFDO0FBQ3BDZixTQUFNa0IsSUFBTixDQUFXLGlCQUFYLEVBQThCSixjQUE5QixDQUE2Q0wsT0FBN0M7QUFDQSxHQUZELE1BRU87QUFDTlQsU0FBTWMsY0FBTixDQUFxQkwsT0FBckI7QUFDQTs7QUFFREc7QUFDQSxFQWREOztBQWdCQSxRQUFPZCxNQUFQO0FBQ0EsQ0EvREYiLCJmaWxlIjoiZGF0ZXRpbWVwaWNrZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRhdGV0aW1lcGlja2VyLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIERhdGV0aW1lcGlja2VyIFdpZGdldFxuICpcbiAqIFRoaXMgd2lkZ2V0IHdpbGwgY29udmVydCBpdHNlbGYgb3IgbXVsdGlwbGUgZWxlbWVudHMgaW50byBkYXRldGltZXBpY2tlciBpbnN0YW5jZXMuIENoZWNrIHRoZSBkZWZhdWx0cyBvYmplY3QgZm9yIGFcbiAqIGxpc3Qgb2YgYXZhaWxhYmxlIG9wdGlvbnMuXG4gKlxuICogWW91IGNhbiBhbHNvIHNldCB0aGlzIG1vZHVsZSBpbiBhIGNvbnRhaW5lciBlbGVtZW50IGFuZCBwcm92aWRlIHRoZSBcImRhdGEtZGF0ZXRpbWVwaWNrZXItY29udGFpbmVyXCIgYXR0cmlidXRlIGFuZFxuICogdGhpcyBwbHVnaW4gd2lsbCBpbml0aWFsaXplIGFsbCB0aGUgY2hpbGQgZWxlbWVudHMgdGhhdCBoYXZlIHRoZSBcImRhdGV0aW1lcGlja2VyXCIgY2xhc3MgaW50byBkYXRldGltZXBpY2tlciB3aWRnZXRzLlxuICpcbiAqIGpRdWVyeSBEYXRldGltZXBpY2tlciBXZWJzaXRlOiB7QGxpbmsgaHR0cDovL3hkc29mdC5uZXQvanFwbHVnaW5zL2RhdGV0aW1lcGlja2VyfVxuICogXG4gKiAjIyMgT3B0aW9uc1xuICpcbiAqIEluIGFkZGl0aW9uIHRvIHRoZSBvcHRpb25zIHN0YXRlZCBiZWxvdywgeW91IGNvdWxkIGFsc28gYWRkIG1hbnkgbW9yZSBvcHRpb25zIHNob3duIGluIHRoZVxuICogalF1ZXJ5IERhdGV0aW1lcGlja2VyIGRvY3VtZW50YXRpb24uXG4gKlxuICogKipGb3JtYXQgfCBgZGF0YS1kYXRldGltZXBpY2tlci1mb3JtYXRgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgdGhlIGRlZmF1bHQgZGF0ZSBmb3JtYXQuIElmIG5vIHZhbHVlIGlzIHByb3ZpZGVkLCB0aGUgZGVmYXVsdCBmb3JtYXQgd2lsbCBiZSBzZXRcbiAqIHRvIGAnZC5tLlkgSDppJ2AuXG4gKlxuICogKipMYW5nIHwgYGRhdGEtZGF0ZXRpbWVwaWNrZXItbGFuZ2AgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogUHJvdmlkZSB0aGUgZGVmYXVsdCBsYW5ndWFnZSBjb2RlLiBJZiB0aGUgY3VycmVudCBsYW5ndWFnZSBpcyBzZXQgdG8gZW5nbGlzaCwgdGhlIGRlZmF1bHRcbiAqIGxhbmd1YWdlIGNvZGUgd2lsbCBiZSBzZXQgdG8gYCdlbi1HQidgLCBlbHNlIHRoZSBsYW5ndWFnZSBjb2RlIHdpbGwgYmUgc2V0IHRvIGAnZGUnYC5cbiAqXG4gKiAjIyMgRXhhbXBsZXNcbiAqIFxuICogYGBgaHRtbFxuICogPGlucHV0IHR5cGU9XCJ0ZXh0XCIgcGxhY2Vob2xkZXI9XCIjIy4jIy4jIyMjICMjOiMjXCIgZGF0YS1neC13aWRnZXQ9XCJkYXRldGltZXBpY2tlclwiIC8+XG4gKiBgYGBcbiAqXG4gKiBAZGVwcmVjYXRlZCBTaW5jZSB2MS40LCB3aWxsIGJlIHJlbW92ZWQgaW4gdjEuNi4gVXNlIHRoZSBvbmUgZnJvbSBKU0UvV2lkZ2V0cyBuYW1lc3BhY2UuXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9kYXRldGltZXBpY2tlclxuICogQHJlcXVpcmVzIGpRdWVyeS1EYXRldGltZXBpY2tlci1QbHVnaW5cbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdkYXRldGltZXBpY2tlcicsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGZvcm1hdDogJ2QubS5ZIEg6aScsXG5cdFx0XHRcdGxhbmc6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpID09PSAnZW4nID8gJ2VuLUdCJyA6ICdkZSdcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqIFxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIEluc3RhbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBNb2R1bGVcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7ZnVuY3Rpb259IGRvbmUgQ2FsbCB0aGlzIG1ldGhvZCBvbmNlIHRoZSBtb2R1bGUgaXMgaW5pdGlhbGl6ZWQuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBDaGVjayBpZiB0aGUgZGF0ZXRpbWVwaWNrZXIgcGx1Z2luIGlzIGFscmVhZHkgbG9hZGVkLiBcblx0XHRcdGlmICgkLmZuLmRhdGV0aW1lcGlja2VyID09PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdUaGUgJC5mbi5kYXRldGltZXBpY2tlciBwbHVnaW4gbXVzdCBiZSBsb2FkZWQgYmVmb3JlIHRoZSBtb2R1bGUgaXMgaW5pdGlhbGl6ZWQuJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIENoZWNrIGlmIHRoZSBjdXJyZW50IGVsZW1lbnQgaXMgYSBjb250YWluZXIgYW5kIHRodXMgbmVlZCB0byBpbml0aWFsaXplIHRoZSBjaGlsZHJlbiBlbGVtZW50cy4gXG5cdFx0XHRpZiAob3B0aW9ucy5jb250YWluZXIgIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHQkdGhpcy5maW5kKCcuZGF0ZXRpbWVwaWNrZXInKS5kYXRldGltZXBpY2tlcihvcHRpb25zKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdCR0aGlzLmRhdGV0aW1lcGlja2VyKG9wdGlvbnMpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTsgIl19
