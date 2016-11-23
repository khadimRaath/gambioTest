'use strict';

/* --------------------------------------------------------------
 collapser.js 2016-02-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Collapser Widget
 *
 * This widget expands or collapses the target element. It is not visible when collapsed but visible when expanded.
 *
 * ### Options
 *
 * **Collapsed | `data-collapser-collapsed` | Boolean | Optional**
 *
 * Default state of the collapser. If no value is provided, it defaults to `false`.
 *
 * **Collapsed Icon Class | `data-collapser-collapsed_icon_class` | String | Optional**
 *
 * Default Font Awesome icon when the collapser is collapsed. If no value is provided, it defaults 
 * to **'fa-plus-square-o'**.
 * 
 * **Expanded Icon Class | `data-collapser-expanded_icon_class` | String | Optional**
 *
 * Default Font Awesome icon when the collapser is expanded. If no value is provided, it defaults 
 * to **'fa-minus-square-o'**.
 *
 * **Additional Classes | `data-collapser-additional_classes` | String | Optional**
 *
 * Provide additional CSS-Classes which should be added. If no value is provided, it defaults to **'pull-right'**, 
 * which applies a CSS *'float: right'* style.
 *
 * **Target Selector | `data-collapser-target_selector` | String | Required**
 *
 * Provide the target selector, which is the element thath will be collapsed or expanded. 
 *
 * **Parent Selector | `data-collapser-parent_selector` | String | Optional**
 *
 * Provide a parent selector for the collapser. It's empty by default. 
 * 
 * ### Example
 *
 * When the page loads, the **collapser** widget will be added to the `<div>` element.
 * On click, the target selector will be shown or hidden.
 *
 * ```html
 *  <div class="headline-wrapper"
 *      data-gx-widget="collapser"
 *      data-collapser-target_selector=".content-wrapper"
 *      data-collapser-section="category_base_data"
 *      data-collapser-user_id="1"
 *      data-collapser-collapsed="true">
 *    Click This Headline
 *  </div>
 *  <div class="content-wrapper">
 *    Toggled content
 *  </div>
 * ```
 *
 * @module Admin/Widgets/collapser
 * 
 * @todo Make the styling for this widget (like it is on the products site) more general. Currently, the "div" element
 * has to be wrapped in another "div" with specific CSS-Classes like ".gx-container" and ".frame-head".
 */
gx.widgets.module('collapser', ['user_configuration_service'], function (data) {

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
  * UserConfigurationService Alias
  *
  * @type {object}
  */
	userConfigurationService = jse.libs.user_configuration_service,


	/**
  * Default Options for Widget
  *
  * @type {object}
  */
	defaults = {
		collapsed: false,
		collapsed_icon_class: 'fa-plus-square-o',
		expanded_icon_class: 'fa-minus-square-o',
		additional_classes: 'pull-right',
		parent_selector: ''
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
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Sets the cursor to pointer
  * @private
  */
	var _setMouseCursorPointer = function _setMouseCursorPointer() {
		$this.addClass('cursor-pointer').children().addClass('cursor-pointer');
	};

	/**
  * Sets the initial visibility according to the 'collapsed' value
  * @private
  */
	var _setInitialVisibilityState = function _setInitialVisibilityState() {
		if (options.collapsed) {
			if (options.parent_selector) {
				$this.parents(options.parent_selector).next(options.target_selector).hide();
			} else {
				$this.next(options.target_selector).hide();
			}
		}
	};

	/**
  * Creates the markup for the collapser and adds the click event handler
  * @private
  */
	var _createCollapser = function _createCollapser() {
		$this.append($('<span></span>').addClass('collapser').addClass(options.additional_classes).append($('<i></i>').addClass('fa').addClass(options.collapsed ? options.collapsed_icon_class : options.expanded_icon_class))).on('click', _toggleVisibilityState);
	};

	/**
  * Saves the current visibility state.
  *
  * @private
  */
	var _saveVisibilityState = function _saveVisibilityState() {
		var collapseState = $this.find('.collapser > i.fa').hasClass(options.collapsed_icon_class);

		userConfigurationService.set({
			data: {
				userId: options.user_id,
				configurationKey: options.section + '_collapse',
				configurationValue: collapseState
			}
		});
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Toggles the visibility state and switches between plus and minus icon.
  *
  * @private
  */
	var _toggleVisibilityState = function _toggleVisibilityState() {
		if (options.parent_selector) {
			$this.parents(options.parent_selector).next(options.target_selector).toggle();
		} else {
			$this.next(options.target_selector).toggle();
		}

		$this.find('.collapser > i.fa').toggleClass(options.collapsed_icon_class);
		$this.find('.collapser > i.fa').toggleClass(options.expanded_icon_class);

		_saveVisibilityState();
	};

	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		_setMouseCursorPointer();
		_setInitialVisibilityState();
		_createCollapser();
		done();
	};

	// Return data to module engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbGxhcHNlci5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwidXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIiwianNlIiwibGlicyIsInVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlIiwiZGVmYXVsdHMiLCJjb2xsYXBzZWQiLCJjb2xsYXBzZWRfaWNvbl9jbGFzcyIsImV4cGFuZGVkX2ljb25fY2xhc3MiLCJhZGRpdGlvbmFsX2NsYXNzZXMiLCJwYXJlbnRfc2VsZWN0b3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3NldE1vdXNlQ3Vyc29yUG9pbnRlciIsImFkZENsYXNzIiwiY2hpbGRyZW4iLCJfc2V0SW5pdGlhbFZpc2liaWxpdHlTdGF0ZSIsInBhcmVudHMiLCJuZXh0IiwidGFyZ2V0X3NlbGVjdG9yIiwiaGlkZSIsIl9jcmVhdGVDb2xsYXBzZXIiLCJhcHBlbmQiLCJvbiIsIl90b2dnbGVWaXNpYmlsaXR5U3RhdGUiLCJfc2F2ZVZpc2liaWxpdHlTdGF0ZSIsImNvbGxhcHNlU3RhdGUiLCJmaW5kIiwiaGFzQ2xhc3MiLCJzZXQiLCJ1c2VySWQiLCJ1c2VyX2lkIiwiY29uZmlndXJhdGlvbktleSIsInNlY3Rpb24iLCJjb25maWd1cmF0aW9uVmFsdWUiLCJ0b2dnbGUiLCJ0b2dnbGVDbGFzcyIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUEwREFBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUNDLFdBREQsRUFHQyxDQUFDLDRCQUFELENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsNEJBQTJCQyxJQUFJQyxJ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cHNlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29sbGFwc2VyLmpzIDIwMTYtMDItMTlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENvbGxhcHNlciBXaWRnZXRcbiAqXG4gKiBUaGlzIHdpZGdldCBleHBhbmRzIG9yIGNvbGxhcHNlcyB0aGUgdGFyZ2V0IGVsZW1lbnQuIEl0IGlzIG5vdCB2aXNpYmxlIHdoZW4gY29sbGFwc2VkIGJ1dCB2aXNpYmxlIHdoZW4gZXhwYW5kZWQuXG4gKlxuICogIyMjIE9wdGlvbnNcbiAqXG4gKiAqKkNvbGxhcHNlZCB8IGBkYXRhLWNvbGxhcHNlci1jb2xsYXBzZWRgIHwgQm9vbGVhbiB8IE9wdGlvbmFsKipcbiAqXG4gKiBEZWZhdWx0IHN0YXRlIG9mIHRoZSBjb2xsYXBzZXIuIElmIG5vIHZhbHVlIGlzIHByb3ZpZGVkLCBpdCBkZWZhdWx0cyB0byBgZmFsc2VgLlxuICpcbiAqICoqQ29sbGFwc2VkIEljb24gQ2xhc3MgfCBgZGF0YS1jb2xsYXBzZXItY29sbGFwc2VkX2ljb25fY2xhc3NgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIERlZmF1bHQgRm9udCBBd2Vzb21lIGljb24gd2hlbiB0aGUgY29sbGFwc2VyIGlzIGNvbGxhcHNlZC4gSWYgbm8gdmFsdWUgaXMgcHJvdmlkZWQsIGl0IGRlZmF1bHRzIFxuICogdG8gKionZmEtcGx1cy1zcXVhcmUtbycqKi5cbiAqIFxuICogKipFeHBhbmRlZCBJY29uIENsYXNzIHwgYGRhdGEtY29sbGFwc2VyLWV4cGFuZGVkX2ljb25fY2xhc3NgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIERlZmF1bHQgRm9udCBBd2Vzb21lIGljb24gd2hlbiB0aGUgY29sbGFwc2VyIGlzIGV4cGFuZGVkLiBJZiBubyB2YWx1ZSBpcyBwcm92aWRlZCwgaXQgZGVmYXVsdHMgXG4gKiB0byAqKidmYS1taW51cy1zcXVhcmUtbycqKi5cbiAqXG4gKiAqKkFkZGl0aW9uYWwgQ2xhc3NlcyB8IGBkYXRhLWNvbGxhcHNlci1hZGRpdGlvbmFsX2NsYXNzZXNgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgYWRkaXRpb25hbCBDU1MtQ2xhc3NlcyB3aGljaCBzaG91bGQgYmUgYWRkZWQuIElmIG5vIHZhbHVlIGlzIHByb3ZpZGVkLCBpdCBkZWZhdWx0cyB0byAqKidwdWxsLXJpZ2h0JyoqLCBcbiAqIHdoaWNoIGFwcGxpZXMgYSBDU1MgKidmbG9hdDogcmlnaHQnKiBzdHlsZS5cbiAqXG4gKiAqKlRhcmdldCBTZWxlY3RvciB8IGBkYXRhLWNvbGxhcHNlci10YXJnZXRfc2VsZWN0b3JgIHwgU3RyaW5nIHwgUmVxdWlyZWQqKlxuICpcbiAqIFByb3ZpZGUgdGhlIHRhcmdldCBzZWxlY3Rvciwgd2hpY2ggaXMgdGhlIGVsZW1lbnQgdGhhdGggd2lsbCBiZSBjb2xsYXBzZWQgb3IgZXhwYW5kZWQuIFxuICpcbiAqICoqUGFyZW50IFNlbGVjdG9yIHwgYGRhdGEtY29sbGFwc2VyLXBhcmVudF9zZWxlY3RvcmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogUHJvdmlkZSBhIHBhcmVudCBzZWxlY3RvciBmb3IgdGhlIGNvbGxhcHNlci4gSXQncyBlbXB0eSBieSBkZWZhdWx0LiBcbiAqIFxuICogIyMjIEV4YW1wbGVcbiAqXG4gKiBXaGVuIHRoZSBwYWdlIGxvYWRzLCB0aGUgKipjb2xsYXBzZXIqKiB3aWRnZXQgd2lsbCBiZSBhZGRlZCB0byB0aGUgYDxkaXY+YCBlbGVtZW50LlxuICogT24gY2xpY2ssIHRoZSB0YXJnZXQgc2VsZWN0b3Igd2lsbCBiZSBzaG93biBvciBoaWRkZW4uXG4gKlxuICogYGBgaHRtbFxuICogIDxkaXYgY2xhc3M9XCJoZWFkbGluZS13cmFwcGVyXCJcbiAqICAgICAgZGF0YS1neC13aWRnZXQ9XCJjb2xsYXBzZXJcIlxuICogICAgICBkYXRhLWNvbGxhcHNlci10YXJnZXRfc2VsZWN0b3I9XCIuY29udGVudC13cmFwcGVyXCJcbiAqICAgICAgZGF0YS1jb2xsYXBzZXItc2VjdGlvbj1cImNhdGVnb3J5X2Jhc2VfZGF0YVwiXG4gKiAgICAgIGRhdGEtY29sbGFwc2VyLXVzZXJfaWQ9XCIxXCJcbiAqICAgICAgZGF0YS1jb2xsYXBzZXItY29sbGFwc2VkPVwidHJ1ZVwiPlxuICogICAgQ2xpY2sgVGhpcyBIZWFkbGluZVxuICogIDwvZGl2PlxuICogIDxkaXYgY2xhc3M9XCJjb250ZW50LXdyYXBwZXJcIj5cbiAqICAgIFRvZ2dsZWQgY29udGVudFxuICogIDwvZGl2PlxuICogYGBgXG4gKlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2NvbGxhcHNlclxuICogXG4gKiBAdG9kbyBNYWtlIHRoZSBzdHlsaW5nIGZvciB0aGlzIHdpZGdldCAobGlrZSBpdCBpcyBvbiB0aGUgcHJvZHVjdHMgc2l0ZSkgbW9yZSBnZW5lcmFsLiBDdXJyZW50bHksIHRoZSBcImRpdlwiIGVsZW1lbnRcbiAqIGhhcyB0byBiZSB3cmFwcGVkIGluIGFub3RoZXIgXCJkaXZcIiB3aXRoIHNwZWNpZmljIENTUy1DbGFzc2VzIGxpa2UgXCIuZ3gtY29udGFpbmVyXCIgYW5kIFwiLmZyYW1lLWhlYWRcIi5cbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdjb2xsYXBzZXInLFxuXHRcblx0Wyd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIEFsaWFzXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlID0ganNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBXaWRnZXRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Y29sbGFwc2VkOiBmYWxzZSxcblx0XHRcdFx0Y29sbGFwc2VkX2ljb25fY2xhc3M6ICdmYS1wbHVzLXNxdWFyZS1vJyxcblx0XHRcdFx0ZXhwYW5kZWRfaWNvbl9jbGFzczogJ2ZhLW1pbnVzLXNxdWFyZS1vJyxcblx0XHRcdFx0YWRkaXRpb25hbF9jbGFzc2VzOiAncHVsbC1yaWdodCcsXG5cdFx0XHRcdHBhcmVudF9zZWxlY3RvcjogJydcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU2V0cyB0aGUgY3Vyc29yIHRvIHBvaW50ZXJcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0TW91c2VDdXJzb3JQb2ludGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkdGhpcy5hZGRDbGFzcygnY3Vyc29yLXBvaW50ZXInKS5jaGlsZHJlbigpLmFkZENsYXNzKCdjdXJzb3ItcG9pbnRlcicpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU2V0cyB0aGUgaW5pdGlhbCB2aXNpYmlsaXR5IGFjY29yZGluZyB0byB0aGUgJ2NvbGxhcHNlZCcgdmFsdWVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0SW5pdGlhbFZpc2liaWxpdHlTdGF0ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKG9wdGlvbnMuY29sbGFwc2VkKSB7XG5cdFx0XHRcdGlmIChvcHRpb25zLnBhcmVudF9zZWxlY3Rvcikge1xuXHRcdFx0XHRcdCR0aGlzLnBhcmVudHMob3B0aW9ucy5wYXJlbnRfc2VsZWN0b3IpLm5leHQob3B0aW9ucy50YXJnZXRfc2VsZWN0b3IpLmhpZGUoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlIHtcblx0XHRcdFx0XHQkdGhpcy5uZXh0KG9wdGlvbnMudGFyZ2V0X3NlbGVjdG9yKS5oaWRlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENyZWF0ZXMgdGhlIG1hcmt1cCBmb3IgdGhlIGNvbGxhcHNlciBhbmQgYWRkcyB0aGUgY2xpY2sgZXZlbnQgaGFuZGxlclxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jcmVhdGVDb2xsYXBzZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLmFwcGVuZChcblx0XHRcdFx0JCgnPHNwYW4+PC9zcGFuPicpLmFkZENsYXNzKCdjb2xsYXBzZXInKS5hZGRDbGFzcyhvcHRpb25zLmFkZGl0aW9uYWxfY2xhc3NlcykuYXBwZW5kKFxuXHRcdFx0XHRcdCQoJzxpPjwvaT4nKS5hZGRDbGFzcygnZmEnKS5hZGRDbGFzcyhvcHRpb25zLmNvbGxhcHNlZCA/IG9wdGlvbnMuY29sbGFwc2VkX2ljb25fY2xhc3MgOlxuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvcHRpb25zLmV4cGFuZGVkX2ljb25fY2xhc3MpXG5cdFx0XHRcdClcblx0XHRcdCkub24oJ2NsaWNrJywgX3RvZ2dsZVZpc2liaWxpdHlTdGF0ZSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFNhdmVzIHRoZSBjdXJyZW50IHZpc2liaWxpdHkgc3RhdGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2F2ZVZpc2liaWxpdHlTdGF0ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGNvbGxhcHNlU3RhdGUgPSAkdGhpcy5maW5kKCcuY29sbGFwc2VyID4gaS5mYScpLmhhc0NsYXNzKG9wdGlvbnMuY29sbGFwc2VkX2ljb25fY2xhc3MpO1xuXHRcdFx0XG5cdFx0XHR1c2VyQ29uZmlndXJhdGlvblNlcnZpY2Uuc2V0KHtcblx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdHVzZXJJZDogb3B0aW9ucy51c2VyX2lkLFxuXHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25LZXk6IG9wdGlvbnMuc2VjdGlvbiArICdfY29sbGFwc2UnLFxuXHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25WYWx1ZTogY29sbGFwc2VTdGF0ZVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVG9nZ2xlcyB0aGUgdmlzaWJpbGl0eSBzdGF0ZSBhbmQgc3dpdGNoZXMgYmV0d2VlbiBwbHVzIGFuZCBtaW51cyBpY29uLlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvZ2dsZVZpc2liaWxpdHlTdGF0ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKG9wdGlvbnMucGFyZW50X3NlbGVjdG9yKSB7XG5cdFx0XHRcdCR0aGlzLnBhcmVudHMob3B0aW9ucy5wYXJlbnRfc2VsZWN0b3IpLm5leHQob3B0aW9ucy50YXJnZXRfc2VsZWN0b3IpLnRvZ2dsZSgpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSB7XG5cdFx0XHRcdCR0aGlzLm5leHQob3B0aW9ucy50YXJnZXRfc2VsZWN0b3IpLnRvZ2dsZSgpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkdGhpcy5maW5kKCcuY29sbGFwc2VyID4gaS5mYScpLnRvZ2dsZUNsYXNzKG9wdGlvbnMuY29sbGFwc2VkX2ljb25fY2xhc3MpO1xuXHRcdFx0JHRoaXMuZmluZCgnLmNvbGxhcHNlciA+IGkuZmEnKS50b2dnbGVDbGFzcyhvcHRpb25zLmV4cGFuZGVkX2ljb25fY2xhc3MpO1xuXG5cdFx0XHRcblx0XHRcdF9zYXZlVmlzaWJpbGl0eVN0YXRlKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpFXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRfc2V0TW91c2VDdXJzb3JQb2ludGVyKCk7XG5cdFx0XHRfc2V0SW5pdGlhbFZpc2liaWxpdHlTdGF0ZSgpO1xuXHRcdFx0X2NyZWF0ZUNvbGxhcHNlcigpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
