'use strict';

/* --------------------------------------------------------------
 view_change.js 2016-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## View Change Extension
 *
 * Use this extension to show or hide elements depending the state of a input-checkbox element. The extension
 * needs to be bound directly on the checkbox element. It requires two jQuery selector parameters that point
 * the elements that will be displayed when the checkbox is checked and when it isn't.
 * 
 * ### Options 
 * 
 * **On State Selector | `data-view_change-on` | String | Required** 
 * 
 * Define a jQuery selector that selects the elements to be displayed when the checkbox is checked.
 * 
 * **Off State Selector | `data-view_change-off` | String | Required**
 *
 * Define a jQuery selector that selects the elements to be displayed when the checkbox is unchecked (required).
 * 
 * **Closest Parent Selector | `data-view_change-closest` | String | Optional**
 *
 * Use this jQuery selector to specify which "closest" element will be the parent of the element search. This 
 * option can be useful for shrinking the search scope within a single parent container and not the whole page 
 * body.
 * 
 * ### Example 
 *
 * In the following example only the labels that reside inside the div.container element will be affected by the 
 * checkbox state. The label outside the container will always be visible.
 * 
 * ```html 
 * <div class="container">
 *   <input type="checkbox" data-gx-extension="view_change"
 *     data-view_change-on=".label-primary"
 *     data-view_change-off=".label-secondary"
 *     data-view_change-closest=".container" />
 *   <label class="label-primary">Test Label - Primary</label>
 *   <label class="label-secondary">Test Label - Secondary</label>  
 * </div>
 * 
 * <label class="label-primary">Test Label - Primary</label>  
 * ```
 * 
 * @module Admin/Extensions/view_change
 */
gx.extensions.module('view_change', [], function (data) {

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
  * Parent Selector (default body)
  *
  * @type {object}
  */
	$parent = $('body'),


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {
		// @todo Rename this option to activeSelector
		on: null, // Selector for the elements that are shown if the checkbox is set
		// @todo Rename this option to inactiveSelector
		off: null, // Selector for the elements that are shown if the checkbox is not set
		// @todo Rename this option to parentSelector
		closest: null // Got to the closest X-element and search inside it for the views
	},


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
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Shows or hides elements corresponding to the checkbox state.
  */
	var _changeHandler = function _changeHandler() {
		if ($this.prop('checked')) {
			$parent.find(options.on).show();
			$parent.find(options.off).hide();
			$this.attr('checked', 'checked');
		} else {
			$parent.find(options.on).hide();
			$parent.find(options.off).show();
			$this.removeAttr('checked');
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {
		if (options.closest) {
			$parent = $this.closest(options.closest);
		}
		$this.on('change checkbox:change', _changeHandler);
		_changeHandler();

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInZpZXdfY2hhbmdlLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkcGFyZW50IiwiZGVmYXVsdHMiLCJvbiIsIm9mZiIsImNsb3Nlc3QiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX2NoYW5nZUhhbmRsZXIiLCJwcm9wIiwiZmluZCIsInNob3ciLCJoaWRlIiwiYXR0ciIsInJlbW92ZUF0dHIiLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMkNBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FDQyxhQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxXQUFVRCxFQUFFLE1BQUYsQ0FiWDs7O0FBZUM7Ozs7O0FBS0FFLFlBQVc7QUFDVjtBQUNBQyxNQUFJLElBRk0sRUFFQTtBQUNWO0FBQ0FDLE9BQUssSUFKSyxFQUlDO0FBQ1g7QUFDQUMsV0FBUyxJQU5DLENBTUk7QUFOSixFQXBCWjs7O0FBNkJDOzs7OztBQUtBQyxXQUFVTixFQUFFTyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJMLFFBQW5CLEVBQTZCSixJQUE3QixDQWxDWDs7O0FBb0NDOzs7OztBQUtBRCxVQUFTLEVBekNWOztBQTJDQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBLEtBQUlXLGlCQUFpQixTQUFqQkEsY0FBaUIsR0FBVztBQUMvQixNQUFJVCxNQUFNVSxJQUFOLENBQVcsU0FBWCxDQUFKLEVBQTJCO0FBQzFCUixXQUFRUyxJQUFSLENBQWFKLFFBQVFILEVBQXJCLEVBQXlCUSxJQUF6QjtBQUNBVixXQUFRUyxJQUFSLENBQWFKLFFBQVFGLEdBQXJCLEVBQTBCUSxJQUExQjtBQUNBYixTQUFNYyxJQUFOLENBQVcsU0FBWCxFQUFzQixTQUF0QjtBQUNBLEdBSkQsTUFJTztBQUNOWixXQUFRUyxJQUFSLENBQWFKLFFBQVFILEVBQXJCLEVBQXlCUyxJQUF6QjtBQUNBWCxXQUFRUyxJQUFSLENBQWFKLFFBQVFGLEdBQXJCLEVBQTBCTyxJQUExQjtBQUNBWixTQUFNZSxVQUFOLENBQWlCLFNBQWpCO0FBQ0E7QUFFRCxFQVhEOztBQWFBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FqQixRQUFPa0IsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixNQUFJVixRQUFRRCxPQUFaLEVBQXFCO0FBQ3BCSixhQUFVRixNQUFNTSxPQUFOLENBQWNDLFFBQVFELE9BQXRCLENBQVY7QUFDQTtBQUNETixRQUFNSSxFQUFOLENBQVMsd0JBQVQsRUFBbUNLLGNBQW5DO0FBQ0FBOztBQUVBUTtBQUNBLEVBUkQ7O0FBVUE7QUFDQSxRQUFPbkIsTUFBUDtBQUNBLENBL0ZGIiwiZmlsZSI6InZpZXdfY2hhbmdlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB2aWV3X2NoYW5nZS5qcyAyMDE2LTA2LTAxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBWaWV3IENoYW5nZSBFeHRlbnNpb25cbiAqXG4gKiBVc2UgdGhpcyBleHRlbnNpb24gdG8gc2hvdyBvciBoaWRlIGVsZW1lbnRzIGRlcGVuZGluZyB0aGUgc3RhdGUgb2YgYSBpbnB1dC1jaGVja2JveCBlbGVtZW50LiBUaGUgZXh0ZW5zaW9uXG4gKiBuZWVkcyB0byBiZSBib3VuZCBkaXJlY3RseSBvbiB0aGUgY2hlY2tib3ggZWxlbWVudC4gSXQgcmVxdWlyZXMgdHdvIGpRdWVyeSBzZWxlY3RvciBwYXJhbWV0ZXJzIHRoYXQgcG9pbnRcbiAqIHRoZSBlbGVtZW50cyB0aGF0IHdpbGwgYmUgZGlzcGxheWVkIHdoZW4gdGhlIGNoZWNrYm94IGlzIGNoZWNrZWQgYW5kIHdoZW4gaXQgaXNuJ3QuXG4gKiBcbiAqICMjIyBPcHRpb25zIFxuICogXG4gKiAqKk9uIFN0YXRlIFNlbGVjdG9yIHwgYGRhdGEtdmlld19jaGFuZ2Utb25gIHwgU3RyaW5nIHwgUmVxdWlyZWQqKiBcbiAqIFxuICogRGVmaW5lIGEgalF1ZXJ5IHNlbGVjdG9yIHRoYXQgc2VsZWN0cyB0aGUgZWxlbWVudHMgdG8gYmUgZGlzcGxheWVkIHdoZW4gdGhlIGNoZWNrYm94IGlzIGNoZWNrZWQuXG4gKiBcbiAqICoqT2ZmIFN0YXRlIFNlbGVjdG9yIHwgYGRhdGEtdmlld19jaGFuZ2Utb2ZmYCB8IFN0cmluZyB8IFJlcXVpcmVkKipcbiAqXG4gKiBEZWZpbmUgYSBqUXVlcnkgc2VsZWN0b3IgdGhhdCBzZWxlY3RzIHRoZSBlbGVtZW50cyB0byBiZSBkaXNwbGF5ZWQgd2hlbiB0aGUgY2hlY2tib3ggaXMgdW5jaGVja2VkIChyZXF1aXJlZCkuXG4gKiBcbiAqICoqQ2xvc2VzdCBQYXJlbnQgU2VsZWN0b3IgfCBgZGF0YS12aWV3X2NoYW5nZS1jbG9zZXN0YCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqXG4gKiBVc2UgdGhpcyBqUXVlcnkgc2VsZWN0b3IgdG8gc3BlY2lmeSB3aGljaCBcImNsb3Nlc3RcIiBlbGVtZW50IHdpbGwgYmUgdGhlIHBhcmVudCBvZiB0aGUgZWxlbWVudCBzZWFyY2guIFRoaXMgXG4gKiBvcHRpb24gY2FuIGJlIHVzZWZ1bCBmb3Igc2hyaW5raW5nIHRoZSBzZWFyY2ggc2NvcGUgd2l0aGluIGEgc2luZ2xlIHBhcmVudCBjb250YWluZXIgYW5kIG5vdCB0aGUgd2hvbGUgcGFnZSBcbiAqIGJvZHkuXG4gKiBcbiAqICMjIyBFeGFtcGxlIFxuICpcbiAqIEluIHRoZSBmb2xsb3dpbmcgZXhhbXBsZSBvbmx5IHRoZSBsYWJlbHMgdGhhdCByZXNpZGUgaW5zaWRlIHRoZSBkaXYuY29udGFpbmVyIGVsZW1lbnQgd2lsbCBiZSBhZmZlY3RlZCBieSB0aGUgXG4gKiBjaGVja2JveCBzdGF0ZS4gVGhlIGxhYmVsIG91dHNpZGUgdGhlIGNvbnRhaW5lciB3aWxsIGFsd2F5cyBiZSB2aXNpYmxlLlxuICogXG4gKiBgYGBodG1sIFxuICogPGRpdiBjbGFzcz1cImNvbnRhaW5lclwiPlxuICogICA8aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgZGF0YS1neC1leHRlbnNpb249XCJ2aWV3X2NoYW5nZVwiXG4gKiAgICAgZGF0YS12aWV3X2NoYW5nZS1vbj1cIi5sYWJlbC1wcmltYXJ5XCJcbiAqICAgICBkYXRhLXZpZXdfY2hhbmdlLW9mZj1cIi5sYWJlbC1zZWNvbmRhcnlcIlxuICogICAgIGRhdGEtdmlld19jaGFuZ2UtY2xvc2VzdD1cIi5jb250YWluZXJcIiAvPlxuICogICA8bGFiZWwgY2xhc3M9XCJsYWJlbC1wcmltYXJ5XCI+VGVzdCBMYWJlbCAtIFByaW1hcnk8L2xhYmVsPlxuICogICA8bGFiZWwgY2xhc3M9XCJsYWJlbC1zZWNvbmRhcnlcIj5UZXN0IExhYmVsIC0gU2Vjb25kYXJ5PC9sYWJlbD4gIFxuICogPC9kaXY+XG4gKiBcbiAqIDxsYWJlbCBjbGFzcz1cImxhYmVsLXByaW1hcnlcIj5UZXN0IExhYmVsIC0gUHJpbWFyeTwvbGFiZWw+ICBcbiAqIGBgYFxuICogXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvdmlld19jaGFuZ2VcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCd2aWV3X2NoYW5nZScsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBFeHRlbnNpb24gUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFBhcmVudCBTZWxlY3RvciAoZGVmYXVsdCBib2R5KVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRwYXJlbnQgPSAkKCdib2R5JyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBFeHRlbnNpb25cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Ly8gQHRvZG8gUmVuYW1lIHRoaXMgb3B0aW9uIHRvIGFjdGl2ZVNlbGVjdG9yXG5cdFx0XHRcdG9uOiBudWxsLCAvLyBTZWxlY3RvciBmb3IgdGhlIGVsZW1lbnRzIHRoYXQgYXJlIHNob3duIGlmIHRoZSBjaGVja2JveCBpcyBzZXRcblx0XHRcdFx0Ly8gQHRvZG8gUmVuYW1lIHRoaXMgb3B0aW9uIHRvIGluYWN0aXZlU2VsZWN0b3Jcblx0XHRcdFx0b2ZmOiBudWxsLCAvLyBTZWxlY3RvciBmb3IgdGhlIGVsZW1lbnRzIHRoYXQgYXJlIHNob3duIGlmIHRoZSBjaGVja2JveCBpcyBub3Qgc2V0XG5cdFx0XHRcdC8vIEB0b2RvIFJlbmFtZSB0aGlzIG9wdGlvbiB0byBwYXJlbnRTZWxlY3RvclxuXHRcdFx0XHRjbG9zZXN0OiBudWxsIC8vIEdvdCB0byB0aGUgY2xvc2VzdCBYLWVsZW1lbnQgYW5kIHNlYXJjaCBpbnNpZGUgaXQgZm9yIHRoZSB2aWV3c1xuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU2hvd3Mgb3IgaGlkZXMgZWxlbWVudHMgY29ycmVzcG9uZGluZyB0byB0aGUgY2hlY2tib3ggc3RhdGUuXG5cdFx0ICovXG5cdFx0dmFyIF9jaGFuZ2VIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJHRoaXMucHJvcCgnY2hlY2tlZCcpKSB7XG5cdFx0XHRcdCRwYXJlbnQuZmluZChvcHRpb25zLm9uKS5zaG93KCk7XG5cdFx0XHRcdCRwYXJlbnQuZmluZChvcHRpb25zLm9mZikuaGlkZSgpO1xuXHRcdFx0XHQkdGhpcy5hdHRyKCdjaGVja2VkJywgJ2NoZWNrZWQnKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdCRwYXJlbnQuZmluZChvcHRpb25zLm9uKS5oaWRlKCk7XG5cdFx0XHRcdCRwYXJlbnQuZmluZChvcHRpb25zLm9mZikuc2hvdygpO1xuXHRcdFx0XHQkdGhpcy5yZW1vdmVBdHRyKCdjaGVja2VkJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRpZiAob3B0aW9ucy5jbG9zZXN0KSB7XG5cdFx0XHRcdCRwYXJlbnQgPSAkdGhpcy5jbG9zZXN0KG9wdGlvbnMuY2xvc2VzdCk7XG5cdFx0XHR9XG5cdFx0XHQkdGhpcy5vbignY2hhbmdlIGNoZWNrYm94OmNoYW5nZScsIF9jaGFuZ2VIYW5kbGVyKTtcblx0XHRcdF9jaGFuZ2VIYW5kbGVyKCk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
