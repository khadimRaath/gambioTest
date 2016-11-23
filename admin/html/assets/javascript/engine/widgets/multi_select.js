'use strict';

/* --------------------------------------------------------------
 multi_select.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Multi Select Widget
 *
 * This module serves as a wrapper of SumoSelect, a jQuery plugin that provides enhanced select-box functionality.
 * Bind this widget to a parent container and mark each child select-box element with the `data-multi_select-instance`
 * attribute.
 *
 * After the initialization of the widget all the marked elements will be converted into SumoSelect instances.
 *
 *
 * ### Options
 *
 * **Options Source | `data-multi_select-source` | String | Optional**
 *
 * Provide a URL that will be used to fetch the options of the select box. The widget will perform a GET request to
 * the provided destination and expects a JSON array with the options:
 *
 * [
 *   {
 *     "value": "1", 
 *     "text": "Option #1"
 *   },
 *   {
 *     "value": "2", 
 *     "text": "Option #2"
 *   }
 * ]
 *
 * You can also pass other configuration directly in the parent element which will be used for every child instance.
 *
 *
 * ### Methods
 *
 * **Reload Options [AJAX]**
 *
 * You can use this method to refresh the options from the already provided data-multi_select-source or by providing
 * a new URL which will also be set as the data-source of the element. If the multi select has no URL then it will just
 * sync its values with the select element.
 *
 * * ```js
 * $('#my-multi-select').multi_select('reload', 'http://shop.de/options/source/url');
 * ```
 *
 * **Refresh Options**
 *
 * Update the multi-select widget with the state of the original select element. This method is useful after performing
 * changes in the original element and need to display them in the multi-select widget.
 *
 * ```js
 * $('#my-multi-select').multi_select('refresh');
 * ```
 *
 * ### Events
 * ```javascript
 * // Triggered when the multi-select widget has performed a "reload" method (after the AJAX call).
 * $('#my-multi-select').on('reload', function(event) {});
 * ```
 *
 * ### Example
 *
 * ```html
 * <form data-gx-widget="multi_select">
 *   <select data-multi_select-instance data-multi_select-source="http://shop.de/options-source-url"></select>
 * </form>
 * ```
 *
 * {@link http://hemantnegi.github.io/jquery.sumoselect}
 *
 * @module Admin/Widgets/multi_select
 * @requires jQuery-SumoSelect
 */
gx.widgets.module('multi_select', [], function (data) {

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
		selectAll: true,
		csvDispCount: 2,
		captionFormat: '{0} ' + jse.core.lang.translate('selected', 'admin_labels'),
		locale: ['OK', jse.core.lang.translate('CANCEL', 'general'), jse.core.lang.translate('SELECT_ALL', 'general')]
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
  * Add the "multi_select" method to the jQuery prototype.
  */
	function _addPublicMethod() {
		if ($.fn.multi_select) {
			return;
		}

		$.fn.extend({
			multi_select: function multi_select(action) {
				for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
					args[_key - 1] = arguments[_key];
				}

				if (!$(this).is('select')) {
					throw new Error('Called the "multi_select" method on an invalid object (select box expected).');
				}

				$.each(this, function () {
					switch (action) {
						case 'reload':
							_reload.apply(undefined, [$(this)].concat(args));
							break;

						case 'refresh':
							_refresh(this);
					}
				});
			}
		});
	}

	/**
  * Fill a select box with the provided options.
  *
  * @param {jQuery} $select The select box to be filled.
  * @param {Object} options Array with { value: "...", "text": "..." } entries.
  */
	function _fillSelect($select, options) {
		$select.empty();
		$.each(options, function (index, option) {
			$select.append(new Option(option.text, option.value));
		});
	}

	/**
  * Reload the options from the source (data property) or the provided URL,
  *
  * @param {string} url Optional, if provided it will be used as the source of the data and will also update the
  * data-source property of the element.
  */
	function _reload($select, url) {
		url = url || $select.data('source');

		if (!url) {
			throw new Error('Multi Select Reload: Neither URL nor data-source contain a URL value.');
		}

		$select.data('source', url);

		$.getJSON(url).done(function (response) {
			_fillSelect($select, response);
			$select[0].sumo.reload();
			$select.trigger('reload');
		}).fail(function (jqxhr, textStatus, errorThrown) {
			jse.core.debug.error('Multi Select AJAX Error: ', jqxhr, textStatus, errorThrown);
		});
	}

	/**
  * Refresh the multi select instance depending the state of the original select element.
  *
  * @param {Node} select The DOM element to be refreshed.
  */
	function _refresh(select) {
		if (select.sumo === undefined) {
			throw new Error('Multi Select Refresh: The provided select element is not an instance of SumoSelect.', select);
		}

		select.sumo.reload();

		// Update the caption by simulating a click in an ".opt" element.  
		_overrideSelectAllCaption.apply($(select.parentNode).find('.opt')[0]);
	}

	/**
  * Override the multi select caption when all elements are selected.
  *
  * This callback will override the caption because SumoSelect does not provide a setting for this text.
  */
	function _overrideSelectAllCaption() {
		var $optWrapper = $(this).parents('.optWrapper');
		var allCheckboxesChecked = $optWrapper.find('.opt.selected').length === $optWrapper.find('.opt').length;
		var atLeastOneCheckboxChecked = $optWrapper.find('.opt.selected').length > 0;
		var $selectAllCheckbox = $optWrapper.find('.select-all');

		$selectAllCheckbox.removeClass('partial-select');

		if (allCheckboxesChecked) {
			$optWrapper.siblings('.CaptionCont').children('span').text(jse.core.lang.translate('all_selected', 'admin_labels'));
		} else if (atLeastOneCheckboxChecked) {
			$selectAllCheckbox.addClass('partial-select');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Add public module method.  
		_addPublicMethod();

		// Initialize the elements. 
		$this.find('[data-multi_select-instance]').each(function () {
			var $select = $(this);

			$select.removeAttr('data-multi_select-instance');

			// Instantiate the widget without an AJAX request.
			$select.SumoSelect(options);

			if ($select.data('multi_selectSource') !== undefined) {
				// Remove the data attribute and store the value internally with the 'source' key. 
				$select.data('source', $select.data('multi_selectSource'));
				$select.removeAttr('data-multi_select-source');

				// Fetch the options with an AJAX request.
				$.getJSON($select.data('multi_selectSource')).done(function (response) {
					_fillSelect($select, response);
					$select[0].sumo.unload();
					$select.SumoSelect(options);
					$select.trigger('reload');
				}).fail(function (jqxhr, textStatus, errorThrown) {
					jse.core.debug.error('Multi Select AJAX Error: ', jqxhr, textStatus, errorThrown);
				});
			}
		});

		done();
	};

	// When the user clicks on the "Select All" option update the text with a custom translations. This has to 
	// be done manually because there is no option for this text in SumoSelect. 
	$this.on('click', '.select-all, .opt', _overrideSelectAllCaption);

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm11bHRpX3NlbGVjdC5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJzZWxlY3RBbGwiLCJjc3ZEaXNwQ291bnQiLCJjYXB0aW9uRm9ybWF0IiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJsb2NhbGUiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX2FkZFB1YmxpY01ldGhvZCIsImZuIiwibXVsdGlfc2VsZWN0IiwiYWN0aW9uIiwiYXJncyIsImlzIiwiRXJyb3IiLCJlYWNoIiwiX3JlbG9hZCIsIl9yZWZyZXNoIiwiX2ZpbGxTZWxlY3QiLCIkc2VsZWN0IiwiZW1wdHkiLCJpbmRleCIsIm9wdGlvbiIsImFwcGVuZCIsIk9wdGlvbiIsInRleHQiLCJ2YWx1ZSIsInVybCIsImdldEpTT04iLCJkb25lIiwicmVzcG9uc2UiLCJzdW1vIiwicmVsb2FkIiwidHJpZ2dlciIsImZhaWwiLCJqcXhociIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsImRlYnVnIiwiZXJyb3IiLCJzZWxlY3QiLCJ1bmRlZmluZWQiLCJfb3ZlcnJpZGVTZWxlY3RBbGxDYXB0aW9uIiwiYXBwbHkiLCJwYXJlbnROb2RlIiwiZmluZCIsIiRvcHRXcmFwcGVyIiwicGFyZW50cyIsImFsbENoZWNrYm94ZXNDaGVja2VkIiwibGVuZ3RoIiwiYXRMZWFzdE9uZUNoZWNrYm94Q2hlY2tlZCIsIiRzZWxlY3RBbGxDaGVja2JveCIsInJlbW92ZUNsYXNzIiwic2libGluZ3MiLCJjaGlsZHJlbiIsImFkZENsYXNzIiwiaW5pdCIsInJlbW92ZUF0dHIiLCJTdW1vU2VsZWN0IiwidW5sb2FkIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF1RUFBLEdBQUdDLE9B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O0FBRUEsQ0F6TUQiLCJmaWxlIjoibXVsdGlfc2VsZWN0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIG11bHRpX3NlbGVjdC5qcyAyMDE2LTA3LTEzXHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqICMjIE11bHRpIFNlbGVjdCBXaWRnZXRcclxuICpcclxuICogVGhpcyBtb2R1bGUgc2VydmVzIGFzIGEgd3JhcHBlciBvZiBTdW1vU2VsZWN0LCBhIGpRdWVyeSBwbHVnaW4gdGhhdCBwcm92aWRlcyBlbmhhbmNlZCBzZWxlY3QtYm94IGZ1bmN0aW9uYWxpdHkuXHJcbiAqIEJpbmQgdGhpcyB3aWRnZXQgdG8gYSBwYXJlbnQgY29udGFpbmVyIGFuZCBtYXJrIGVhY2ggY2hpbGQgc2VsZWN0LWJveCBlbGVtZW50IHdpdGggdGhlIGBkYXRhLW11bHRpX3NlbGVjdC1pbnN0YW5jZWBcclxuICogYXR0cmlidXRlLlxyXG4gKlxyXG4gKiBBZnRlciB0aGUgaW5pdGlhbGl6YXRpb24gb2YgdGhlIHdpZGdldCBhbGwgdGhlIG1hcmtlZCBlbGVtZW50cyB3aWxsIGJlIGNvbnZlcnRlZCBpbnRvIFN1bW9TZWxlY3QgaW5zdGFuY2VzLlxyXG4gKlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9uc1xyXG4gKlxyXG4gKiAqKk9wdGlvbnMgU291cmNlIHwgYGRhdGEtbXVsdGlfc2VsZWN0LXNvdXJjZWAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqXHJcbiAqIFByb3ZpZGUgYSBVUkwgdGhhdCB3aWxsIGJlIHVzZWQgdG8gZmV0Y2ggdGhlIG9wdGlvbnMgb2YgdGhlIHNlbGVjdCBib3guIFRoZSB3aWRnZXQgd2lsbCBwZXJmb3JtIGEgR0VUIHJlcXVlc3QgdG9cclxuICogdGhlIHByb3ZpZGVkIGRlc3RpbmF0aW9uIGFuZCBleHBlY3RzIGEgSlNPTiBhcnJheSB3aXRoIHRoZSBvcHRpb25zOlxyXG4gKlxyXG4gKiBbXHJcbiAqICAge1xyXG4gKiAgICAgXCJ2YWx1ZVwiOiBcIjFcIiwgXHJcbiAqICAgICBcInRleHRcIjogXCJPcHRpb24gIzFcIlxyXG4gKiAgIH0sXHJcbiAqICAge1xyXG4gKiAgICAgXCJ2YWx1ZVwiOiBcIjJcIiwgXHJcbiAqICAgICBcInRleHRcIjogXCJPcHRpb24gIzJcIlxyXG4gKiAgIH1cclxuICogXVxyXG4gKlxyXG4gKiBZb3UgY2FuIGFsc28gcGFzcyBvdGhlciBjb25maWd1cmF0aW9uIGRpcmVjdGx5IGluIHRoZSBwYXJlbnQgZWxlbWVudCB3aGljaCB3aWxsIGJlIHVzZWQgZm9yIGV2ZXJ5IGNoaWxkIGluc3RhbmNlLlxyXG4gKlxyXG4gKlxyXG4gKiAjIyMgTWV0aG9kc1xyXG4gKlxyXG4gKiAqKlJlbG9hZCBPcHRpb25zIFtBSkFYXSoqXHJcbiAqXHJcbiAqIFlvdSBjYW4gdXNlIHRoaXMgbWV0aG9kIHRvIHJlZnJlc2ggdGhlIG9wdGlvbnMgZnJvbSB0aGUgYWxyZWFkeSBwcm92aWRlZCBkYXRhLW11bHRpX3NlbGVjdC1zb3VyY2Ugb3IgYnkgcHJvdmlkaW5nXHJcbiAqIGEgbmV3IFVSTCB3aGljaCB3aWxsIGFsc28gYmUgc2V0IGFzIHRoZSBkYXRhLXNvdXJjZSBvZiB0aGUgZWxlbWVudC4gSWYgdGhlIG11bHRpIHNlbGVjdCBoYXMgbm8gVVJMIHRoZW4gaXQgd2lsbCBqdXN0XHJcbiAqIHN5bmMgaXRzIHZhbHVlcyB3aXRoIHRoZSBzZWxlY3QgZWxlbWVudC5cclxuICpcclxuICogKiBgYGBqc1xyXG4gKiAkKCcjbXktbXVsdGktc2VsZWN0JykubXVsdGlfc2VsZWN0KCdyZWxvYWQnLCAnaHR0cDovL3Nob3AuZGUvb3B0aW9ucy9zb3VyY2UvdXJsJyk7XHJcbiAqIGBgYFxyXG4gKlxyXG4gKiAqKlJlZnJlc2ggT3B0aW9ucyoqXHJcbiAqXHJcbiAqIFVwZGF0ZSB0aGUgbXVsdGktc2VsZWN0IHdpZGdldCB3aXRoIHRoZSBzdGF0ZSBvZiB0aGUgb3JpZ2luYWwgc2VsZWN0IGVsZW1lbnQuIFRoaXMgbWV0aG9kIGlzIHVzZWZ1bCBhZnRlciBwZXJmb3JtaW5nXHJcbiAqIGNoYW5nZXMgaW4gdGhlIG9yaWdpbmFsIGVsZW1lbnQgYW5kIG5lZWQgdG8gZGlzcGxheSB0aGVtIGluIHRoZSBtdWx0aS1zZWxlY3Qgd2lkZ2V0LlxyXG4gKlxyXG4gKiBgYGBqc1xyXG4gKiAkKCcjbXktbXVsdGktc2VsZWN0JykubXVsdGlfc2VsZWN0KCdyZWZyZXNoJyk7XHJcbiAqIGBgYFxyXG4gKlxyXG4gKiAjIyMgRXZlbnRzXHJcbiAqIGBgYGphdmFzY3JpcHRcclxuICogLy8gVHJpZ2dlcmVkIHdoZW4gdGhlIG11bHRpLXNlbGVjdCB3aWRnZXQgaGFzIHBlcmZvcm1lZCBhIFwicmVsb2FkXCIgbWV0aG9kIChhZnRlciB0aGUgQUpBWCBjYWxsKS5cclxuICogJCgnI215LW11bHRpLXNlbGVjdCcpLm9uKCdyZWxvYWQnLCBmdW5jdGlvbihldmVudCkge30pO1xyXG4gKiBgYGBcclxuICpcclxuICogIyMjIEV4YW1wbGVcclxuICpcclxuICogYGBgaHRtbFxyXG4gKiA8Zm9ybSBkYXRhLWd4LXdpZGdldD1cIm11bHRpX3NlbGVjdFwiPlxyXG4gKiAgIDxzZWxlY3QgZGF0YS1tdWx0aV9zZWxlY3QtaW5zdGFuY2UgZGF0YS1tdWx0aV9zZWxlY3Qtc291cmNlPVwiaHR0cDovL3Nob3AuZGUvb3B0aW9ucy1zb3VyY2UtdXJsXCI+PC9zZWxlY3Q+XHJcbiAqIDwvZm9ybT5cclxuICogYGBgXHJcbiAqXHJcbiAqIHtAbGluayBodHRwOi8vaGVtYW50bmVnaS5naXRodWIuaW8vanF1ZXJ5LnN1bW9zZWxlY3R9XHJcbiAqXHJcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9tdWx0aV9zZWxlY3RcclxuICogQHJlcXVpcmVzIGpRdWVyeS1TdW1vU2VsZWN0XHJcbiAqL1xyXG5neC53aWRnZXRzLm1vZHVsZSgnbXVsdGlfc2VsZWN0JywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBEZWZhdWx0IE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgZGVmYXVsdHMgPSB7XHJcblx0XHRzZWxlY3RBbGw6IHRydWUsXHJcblx0XHRjc3ZEaXNwQ291bnQ6IDIsXHJcblx0XHRjYXB0aW9uRm9ybWF0OiBgezB9ICR7anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3NlbGVjdGVkJywgJ2FkbWluX2xhYmVscycpfWAsXHJcblx0XHRsb2NhbGU6IFtcclxuXHRcdFx0J09LJyxcclxuXHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0NBTkNFTCcsICdnZW5lcmFsJyksXHJcblx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTRUxFQ1RfQUxMJywgJ2dlbmVyYWwnKVxyXG5cdFx0XVxyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogRmluYWwgT3B0aW9uc1xyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogQWRkIHRoZSBcIm11bHRpX3NlbGVjdFwiIG1ldGhvZCB0byB0aGUgalF1ZXJ5IHByb3RvdHlwZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfYWRkUHVibGljTWV0aG9kKCkge1xyXG5cdFx0aWYgKCQuZm4ubXVsdGlfc2VsZWN0KSB7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0JC5mbi5leHRlbmQoe1xyXG5cdFx0XHRtdWx0aV9zZWxlY3Q6IGZ1bmN0aW9uKGFjdGlvbiwgLi4uYXJncykge1xyXG5cdFx0XHRcdGlmICghJCh0aGlzKS5pcygnc2VsZWN0JykpIHtcclxuXHRcdFx0XHRcdHRocm93IG5ldyBFcnJvcignQ2FsbGVkIHRoZSBcIm11bHRpX3NlbGVjdFwiIG1ldGhvZCBvbiBhbiBpbnZhbGlkIG9iamVjdCAoc2VsZWN0IGJveCBleHBlY3RlZCkuJyk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdCQuZWFjaCh0aGlzLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRcdHN3aXRjaCAoYWN0aW9uKSB7XHJcblx0XHRcdFx0XHRcdGNhc2UgJ3JlbG9hZCc6XHJcblx0XHRcdFx0XHRcdFx0X3JlbG9hZCgkKHRoaXMpLCAuLi5hcmdzKTtcclxuXHRcdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdFx0XHJcblx0XHRcdFx0XHRcdGNhc2UgJ3JlZnJlc2gnOlxyXG5cdFx0XHRcdFx0XHRcdF9yZWZyZXNoKHRoaXMpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogRmlsbCBhIHNlbGVjdCBib3ggd2l0aCB0aGUgcHJvdmlkZWQgb3B0aW9ucy5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5fSAkc2VsZWN0IFRoZSBzZWxlY3QgYm94IHRvIGJlIGZpbGxlZC5cclxuXHQgKiBAcGFyYW0ge09iamVjdH0gb3B0aW9ucyBBcnJheSB3aXRoIHsgdmFsdWU6IFwiLi4uXCIsIFwidGV4dFwiOiBcIi4uLlwiIH0gZW50cmllcy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfZmlsbFNlbGVjdCgkc2VsZWN0LCBvcHRpb25zKSB7XHJcblx0XHQkc2VsZWN0LmVtcHR5KCk7XHJcblx0XHQkLmVhY2gob3B0aW9ucywgKGluZGV4LCBvcHRpb24pID0+IHtcclxuXHRcdFx0JHNlbGVjdC5hcHBlbmQobmV3IE9wdGlvbihvcHRpb24udGV4dCwgb3B0aW9uLnZhbHVlKSk7XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogUmVsb2FkIHRoZSBvcHRpb25zIGZyb20gdGhlIHNvdXJjZSAoZGF0YSBwcm9wZXJ0eSkgb3IgdGhlIHByb3ZpZGVkIFVSTCxcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB1cmwgT3B0aW9uYWwsIGlmIHByb3ZpZGVkIGl0IHdpbGwgYmUgdXNlZCBhcyB0aGUgc291cmNlIG9mIHRoZSBkYXRhIGFuZCB3aWxsIGFsc28gdXBkYXRlIHRoZVxyXG5cdCAqIGRhdGEtc291cmNlIHByb3BlcnR5IG9mIHRoZSBlbGVtZW50LlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9yZWxvYWQoJHNlbGVjdCwgdXJsKSB7XHJcblx0XHR1cmwgPSB1cmwgfHwgJHNlbGVjdC5kYXRhKCdzb3VyY2UnKTtcclxuXHRcdFxyXG5cdFx0aWYgKCF1cmwpIHtcclxuXHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNdWx0aSBTZWxlY3QgUmVsb2FkOiBOZWl0aGVyIFVSTCBub3IgZGF0YS1zb3VyY2UgY29udGFpbiBhIFVSTCB2YWx1ZS4nKTtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0JHNlbGVjdC5kYXRhKCdzb3VyY2UnLCB1cmwpO1xyXG5cdFx0XHJcblx0XHQkLmdldEpTT04odXJsKVxyXG5cdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xyXG5cdFx0XHRcdF9maWxsU2VsZWN0KCRzZWxlY3QsIHJlc3BvbnNlKTtcclxuXHRcdFx0XHQkc2VsZWN0WzBdLnN1bW8ucmVsb2FkKCk7XHJcblx0XHRcdFx0JHNlbGVjdC50cmlnZ2VyKCdyZWxvYWQnKTtcclxuXHRcdFx0fSlcclxuXHRcdFx0LmZhaWwoZnVuY3Rpb24oanF4aHIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XHJcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ011bHRpIFNlbGVjdCBBSkFYIEVycm9yOiAnLCBqcXhociwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pO1xyXG5cdFx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogUmVmcmVzaCB0aGUgbXVsdGkgc2VsZWN0IGluc3RhbmNlIGRlcGVuZGluZyB0aGUgc3RhdGUgb2YgdGhlIG9yaWdpbmFsIHNlbGVjdCBlbGVtZW50LlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtOb2RlfSBzZWxlY3QgVGhlIERPTSBlbGVtZW50IHRvIGJlIHJlZnJlc2hlZC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfcmVmcmVzaChzZWxlY3QpIHtcclxuXHRcdGlmIChzZWxlY3Quc3VtbyA9PT0gdW5kZWZpbmVkKSB7XHJcblx0XHRcdHRocm93IG5ldyBFcnJvcignTXVsdGkgU2VsZWN0IFJlZnJlc2g6IFRoZSBwcm92aWRlZCBzZWxlY3QgZWxlbWVudCBpcyBub3QgYW4gaW5zdGFuY2Ugb2YgU3Vtb1NlbGVjdC4nLCBcclxuXHRcdFx0XHRzZWxlY3QpO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRzZWxlY3Quc3Vtby5yZWxvYWQoKTtcclxuXHRcdFxyXG5cdFx0Ly8gVXBkYXRlIHRoZSBjYXB0aW9uIGJ5IHNpbXVsYXRpbmcgYSBjbGljayBpbiBhbiBcIi5vcHRcIiBlbGVtZW50LiAgXHJcblx0XHRfb3ZlcnJpZGVTZWxlY3RBbGxDYXB0aW9uLmFwcGx5KCQoc2VsZWN0LnBhcmVudE5vZGUpLmZpbmQoJy5vcHQnKVswXSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE92ZXJyaWRlIHRoZSBtdWx0aSBzZWxlY3QgY2FwdGlvbiB3aGVuIGFsbCBlbGVtZW50cyBhcmUgc2VsZWN0ZWQuXHJcblx0ICpcclxuXHQgKiBUaGlzIGNhbGxiYWNrIHdpbGwgb3ZlcnJpZGUgdGhlIGNhcHRpb24gYmVjYXVzZSBTdW1vU2VsZWN0IGRvZXMgbm90IHByb3ZpZGUgYSBzZXR0aW5nIGZvciB0aGlzIHRleHQuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX292ZXJyaWRlU2VsZWN0QWxsQ2FwdGlvbigpIHtcclxuXHRcdGNvbnN0ICRvcHRXcmFwcGVyID0gJCh0aGlzKS5wYXJlbnRzKCcub3B0V3JhcHBlcicpO1xyXG5cdFx0Y29uc3QgYWxsQ2hlY2tib3hlc0NoZWNrZWQgPSAkb3B0V3JhcHBlci5maW5kKCcub3B0LnNlbGVjdGVkJykubGVuZ3RoID09PSAkb3B0V3JhcHBlci5maW5kKCcub3B0JykubGVuZ3RoO1xyXG5cdFx0Y29uc3QgYXRMZWFzdE9uZUNoZWNrYm94Q2hlY2tlZCA9ICRvcHRXcmFwcGVyLmZpbmQoJy5vcHQuc2VsZWN0ZWQnKS5sZW5ndGggPiAwO1xyXG5cdFx0Y29uc3QgJHNlbGVjdEFsbENoZWNrYm94ID0gJG9wdFdyYXBwZXIuZmluZCgnLnNlbGVjdC1hbGwnKTtcclxuXHRcdFxyXG5cdFx0JHNlbGVjdEFsbENoZWNrYm94LnJlbW92ZUNsYXNzKCdwYXJ0aWFsLXNlbGVjdCcpO1xyXG5cdFx0XHJcblx0XHRpZiAoYWxsQ2hlY2tib3hlc0NoZWNrZWQpIHtcclxuXHRcdFx0JG9wdFdyYXBwZXJcclxuXHRcdFx0XHQuc2libGluZ3MoJy5DYXB0aW9uQ29udCcpXHJcblx0XHRcdFx0LmNoaWxkcmVuKCdzcGFuJylcclxuXHRcdFx0XHQudGV4dChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnYWxsX3NlbGVjdGVkJywgJ2FkbWluX2xhYmVscycpKTtcclxuXHRcdH0gZWxzZSBpZiAoYXRMZWFzdE9uZUNoZWNrYm94Q2hlY2tlZCkge1xyXG5cdFx0XHQkc2VsZWN0QWxsQ2hlY2tib3guYWRkQ2xhc3MoJ3BhcnRpYWwtc2VsZWN0Jyk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQvLyBBZGQgcHVibGljIG1vZHVsZSBtZXRob2QuICBcclxuXHRcdF9hZGRQdWJsaWNNZXRob2QoKTtcclxuXHRcdFxyXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgZWxlbWVudHMuIFxyXG5cdFx0JHRoaXMuZmluZCgnW2RhdGEtbXVsdGlfc2VsZWN0LWluc3RhbmNlXScpLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdGNvbnN0ICRzZWxlY3QgPSAkKHRoaXMpO1xyXG5cdFx0XHRcclxuXHRcdFx0JHNlbGVjdC5yZW1vdmVBdHRyKCdkYXRhLW11bHRpX3NlbGVjdC1pbnN0YW5jZScpO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gSW5zdGFudGlhdGUgdGhlIHdpZGdldCB3aXRob3V0IGFuIEFKQVggcmVxdWVzdC5cclxuXHRcdFx0JHNlbGVjdC5TdW1vU2VsZWN0KG9wdGlvbnMpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKCRzZWxlY3QuZGF0YSgnbXVsdGlfc2VsZWN0U291cmNlJykgIT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdC8vIFJlbW92ZSB0aGUgZGF0YSBhdHRyaWJ1dGUgYW5kIHN0b3JlIHRoZSB2YWx1ZSBpbnRlcm5hbGx5IHdpdGggdGhlICdzb3VyY2UnIGtleS4gXHJcblx0XHRcdFx0JHNlbGVjdC5kYXRhKCdzb3VyY2UnLCAkc2VsZWN0LmRhdGEoJ211bHRpX3NlbGVjdFNvdXJjZScpKTtcclxuXHRcdFx0XHQkc2VsZWN0LnJlbW92ZUF0dHIoJ2RhdGEtbXVsdGlfc2VsZWN0LXNvdXJjZScpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIEZldGNoIHRoZSBvcHRpb25zIHdpdGggYW4gQUpBWCByZXF1ZXN0LlxyXG5cdFx0XHRcdCQuZ2V0SlNPTigkc2VsZWN0LmRhdGEoJ211bHRpX3NlbGVjdFNvdXJjZScpKVxyXG5cdFx0XHRcdFx0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcclxuXHRcdFx0XHRcdFx0X2ZpbGxTZWxlY3QoJHNlbGVjdCwgcmVzcG9uc2UpO1xyXG5cdFx0XHRcdFx0XHQkc2VsZWN0WzBdLnN1bW8udW5sb2FkKCk7XHJcblx0XHRcdFx0XHRcdCRzZWxlY3QuU3Vtb1NlbGVjdChvcHRpb25zKTtcclxuXHRcdFx0XHRcdFx0JHNlbGVjdC50cmlnZ2VyKCdyZWxvYWQnKTtcclxuXHRcdFx0XHRcdH0pXHJcblx0XHRcdFx0XHQuZmFpbChmdW5jdGlvbihqcXhociwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pIHtcclxuXHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ011bHRpIFNlbGVjdCBBSkFYIEVycm9yOiAnLCBqcXhociwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pO1xyXG5cdFx0XHRcdFx0fSk7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHQvLyBXaGVuIHRoZSB1c2VyIGNsaWNrcyBvbiB0aGUgXCJTZWxlY3QgQWxsXCIgb3B0aW9uIHVwZGF0ZSB0aGUgdGV4dCB3aXRoIGEgY3VzdG9tIHRyYW5zbGF0aW9ucy4gVGhpcyBoYXMgdG8gXHJcblx0Ly8gYmUgZG9uZSBtYW51YWxseSBiZWNhdXNlIHRoZXJlIGlzIG5vIG9wdGlvbiBmb3IgdGhpcyB0ZXh0IGluIFN1bW9TZWxlY3QuIFxyXG5cdCR0aGlzLm9uKCdjbGljaycsICcuc2VsZWN0LWFsbCwgLm9wdCcsIF9vdmVycmlkZVNlbGVjdEFsbENhcHRpb24pO1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyAiXX0=
