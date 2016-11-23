'use strict';

/* --------------------------------------------------------------
 tabs.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tabs Widget
 *
 * This widget is a custom implementation of tabs functionality and must not be confused with
 * jQueryUI's tab widget.
 * 
 * The actual `<div>` which contains the tabs, has to have a CSS-Class named **tab-headline-wrapper**.
 * The tabs will be identified by this CSS-Class. The content of the tabs has to be in a `<div>` which has to have
 * a CSS-Class called **tab-content-wrapper**. The elements inside, have to be in the same order as the tabs.
 *
 * ### Example
 *
 * ```html
 * <div data-gx-widget="tabs">
 *   <!-- Tabs -->
 *   <div class="tab-headline-wrapper">
 *     <a href="#tab1">Tab #1</a>
 *     <a href="#tab2">Tab #2</a>
 *   </div>
 *   
 *   <!-- Content -->
 *   <div class="tab-content-wrapper">
 *     <div>Content of tab #1.</div>
 *     <div>Content of tab #2.</div>
 *   </div>
 * </div>
 * ```
 *
 * @module Admin/Widgets/tabs
 */
gx.widgets.module('tabs', [], function (data) {

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
  * Headline Tags Selector
  *
  * @type {object}
  */
	$headlineTags = null,


	/**
  * Content Tags Selector
  *
  * @type {object}
  */
	$contentTags = null,


	/**
  * Default Options for Widget
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
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Click handler for the tabs onClick the content gets switched.
  *
  * @param {object} event jQuery event object contains information of the event.
  */
	var _clickHandler = function _clickHandler(event) {
		event.preventDefault();
		event.stopPropagation();

		$headlineTags.removeClass('active');

		var index = $(this).addClass('active').index();

		$contentTags.hide().eq(index).show();
	};

	/**
  * Handles external "show" event
  *
  * @param {object} event jQuery event object contains information of the event.
  * @param {number} tab index to show
  */
	var _showHandler = function _showHandler(event, index) {
		event.preventDefault();
		event.stopPropagation();
		$headlineTags.eq(index).trigger('click');
	};

	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		$headlineTags = $this.children('.tab-headline-wrapper').children('a');

		$contentTags = $this.children('.tab-content-wrapper').children('div');

		$this.addClass('ui-tabs');
		$this.on('click', '.tab-headline-wrapper > a', _clickHandler);
		$this.on('show:tab', _showHandler);

		// Set first tab as selected.
		$headlineTags.eq(0).trigger('click');

		done();
	};

	// Return data to module engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRhYnMuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRoZWFkbGluZVRhZ3MiLCIkY29udGVudFRhZ3MiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2xpY2tIYW5kbGVyIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsInJlbW92ZUNsYXNzIiwiaW5kZXgiLCJhZGRDbGFzcyIsImhpZGUiLCJlcSIsInNob3ciLCJfc2hvd0hhbmRsZXIiLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiLCJjaGlsZHJlbiIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQThCQUEsR0FBR0MsT0FBSCxDQUFXQyxNQUFYLENBQ0MsTUFERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsaUJBQWdCLElBYmpCOzs7QUFlQzs7Ozs7QUFLQUMsZ0JBQWUsSUFwQmhCOzs7QUFzQkM7Ozs7O0FBS0FDLFlBQVcsRUEzQlo7OztBQTZCQzs7Ozs7QUFLQUMsV0FBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkwsSUFBN0IsQ0FsQ1g7OztBQW9DQzs7Ozs7QUFLQUQsVUFBUyxFQXpDVjs7QUEyQ0E7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLEtBQUlTLGdCQUFnQixTQUFoQkEsYUFBZ0IsQ0FBU0MsS0FBVCxFQUFnQjtBQUNuQ0EsUUFBTUMsY0FBTjtBQUNBRCxRQUFNRSxlQUFOOztBQUVBUixnQkFBY1MsV0FBZCxDQUEwQixRQUExQjs7QUFFQSxNQUFJQyxRQUFRWCxFQUFFLElBQUYsRUFDVlksUUFEVSxDQUNELFFBREMsRUFFVkQsS0FGVSxFQUFaOztBQUlBVCxlQUNFVyxJQURGLEdBRUVDLEVBRkYsQ0FFS0gsS0FGTCxFQUdFSSxJQUhGO0FBSUEsRUFkRDs7QUFnQkE7Ozs7OztBQU1BLEtBQUlDLGVBQWUsU0FBZkEsWUFBZSxDQUFTVCxLQUFULEVBQWdCSSxLQUFoQixFQUF1QjtBQUN6Q0osUUFBTUMsY0FBTjtBQUNBRCxRQUFNRSxlQUFOO0FBQ0FSLGdCQUFjYSxFQUFkLENBQWlCSCxLQUFqQixFQUF3Qk0sT0FBeEIsQ0FBZ0MsT0FBaEM7QUFDQSxFQUpEOztBQU1BO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FwQixRQUFPcUIsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QmxCLGtCQUFnQkYsTUFDZHFCLFFBRGMsQ0FDTCx1QkFESyxFQUVkQSxRQUZjLENBRUwsR0FGSyxDQUFoQjs7QUFJQWxCLGlCQUFlSCxNQUNicUIsUUFEYSxDQUNKLHNCQURJLEVBRWJBLFFBRmEsQ0FFSixLQUZJLENBQWY7O0FBSUFyQixRQUFNYSxRQUFOLENBQWUsU0FBZjtBQUNBYixRQUFNc0IsRUFBTixDQUFTLE9BQVQsRUFBa0IsMkJBQWxCLEVBQStDZixhQUEvQztBQUNBUCxRQUFNc0IsRUFBTixDQUFTLFVBQVQsRUFBcUJMLFlBQXJCOztBQUVBO0FBQ0FmLGdCQUNFYSxFQURGLENBQ0ssQ0FETCxFQUVFRyxPQUZGLENBRVUsT0FGVjs7QUFJQUU7QUFDQSxFQW5CRDs7QUFxQkE7QUFDQSxRQUFPdEIsTUFBUDtBQUNBLENBM0hGIiwiZmlsZSI6InRhYnMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRhYnMuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgVGFicyBXaWRnZXRcbiAqXG4gKiBUaGlzIHdpZGdldCBpcyBhIGN1c3RvbSBpbXBsZW1lbnRhdGlvbiBvZiB0YWJzIGZ1bmN0aW9uYWxpdHkgYW5kIG11c3Qgbm90IGJlIGNvbmZ1c2VkIHdpdGhcbiAqIGpRdWVyeVVJJ3MgdGFiIHdpZGdldC5cbiAqIFxuICogVGhlIGFjdHVhbCBgPGRpdj5gIHdoaWNoIGNvbnRhaW5zIHRoZSB0YWJzLCBoYXMgdG8gaGF2ZSBhIENTUy1DbGFzcyBuYW1lZCAqKnRhYi1oZWFkbGluZS13cmFwcGVyKiouXG4gKiBUaGUgdGFicyB3aWxsIGJlIGlkZW50aWZpZWQgYnkgdGhpcyBDU1MtQ2xhc3MuIFRoZSBjb250ZW50IG9mIHRoZSB0YWJzIGhhcyB0byBiZSBpbiBhIGA8ZGl2PmAgd2hpY2ggaGFzIHRvIGhhdmVcbiAqIGEgQ1NTLUNsYXNzIGNhbGxlZCAqKnRhYi1jb250ZW50LXdyYXBwZXIqKi4gVGhlIGVsZW1lbnRzIGluc2lkZSwgaGF2ZSB0byBiZSBpbiB0aGUgc2FtZSBvcmRlciBhcyB0aGUgdGFicy5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIGBgYGh0bWxcbiAqIDxkaXYgZGF0YS1neC13aWRnZXQ9XCJ0YWJzXCI+XG4gKiAgIDwhLS0gVGFicyAtLT5cbiAqICAgPGRpdiBjbGFzcz1cInRhYi1oZWFkbGluZS13cmFwcGVyXCI+XG4gKiAgICAgPGEgaHJlZj1cIiN0YWIxXCI+VGFiICMxPC9hPlxuICogICAgIDxhIGhyZWY9XCIjdGFiMlwiPlRhYiAjMjwvYT5cbiAqICAgPC9kaXY+XG4gKiAgIFxuICogICA8IS0tIENvbnRlbnQgLS0+XG4gKiAgIDxkaXYgY2xhc3M9XCJ0YWItY29udGVudC13cmFwcGVyXCI+XG4gKiAgICAgPGRpdj5Db250ZW50IG9mIHRhYiAjMS48L2Rpdj5cbiAqICAgICA8ZGl2PkNvbnRlbnQgb2YgdGFiICMyLjwvZGl2PlxuICogICA8L2Rpdj5cbiAqIDwvZGl2PlxuICogYGBgXG4gKlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL3RhYnNcbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCd0YWJzJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogSGVhZGxpbmUgVGFncyBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRoZWFkbGluZVRhZ3MgPSBudWxsLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIENvbnRlbnQgVGFncyBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRjb250ZW50VGFncyA9IG51bGwsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBXaWRnZXRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2xpY2sgaGFuZGxlciBmb3IgdGhlIHRhYnMgb25DbGljayB0aGUgY29udGVudCBnZXRzIHN3aXRjaGVkLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QgY29udGFpbnMgaW5mb3JtYXRpb24gb2YgdGhlIGV2ZW50LlxuXHRcdCAqL1xuXHRcdHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFxuXHRcdFx0JGhlYWRsaW5lVGFncy5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcblx0XHRcdHZhciBpbmRleCA9ICQodGhpcylcblx0XHRcdFx0LmFkZENsYXNzKCdhY3RpdmUnKVxuXHRcdFx0XHQuaW5kZXgoKTtcblx0XHRcdFxuXHRcdFx0JGNvbnRlbnRUYWdzXG5cdFx0XHRcdC5oaWRlKClcblx0XHRcdFx0LmVxKGluZGV4KVxuXHRcdFx0XHQuc2hvdygpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlcyBleHRlcm5hbCBcInNob3dcIiBldmVudFxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QgY29udGFpbnMgaW5mb3JtYXRpb24gb2YgdGhlIGV2ZW50LlxuXHRcdCAqIEBwYXJhbSB7bnVtYmVyfSB0YWIgaW5kZXggdG8gc2hvd1xuXHRcdCAqL1xuXHRcdHZhciBfc2hvd0hhbmRsZXIgPSBmdW5jdGlvbihldmVudCwgaW5kZXgpIHtcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdCRoZWFkbGluZVRhZ3MuZXEoaW5kZXgpLnRyaWdnZXIoJ2NsaWNrJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpFXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkaGVhZGxpbmVUYWdzID0gJHRoaXNcblx0XHRcdFx0LmNoaWxkcmVuKCcudGFiLWhlYWRsaW5lLXdyYXBwZXInKVxuXHRcdFx0XHQuY2hpbGRyZW4oJ2EnKTtcblx0XHRcdFxuXHRcdFx0JGNvbnRlbnRUYWdzID0gJHRoaXNcblx0XHRcdFx0LmNoaWxkcmVuKCcudGFiLWNvbnRlbnQtd3JhcHBlcicpXG5cdFx0XHRcdC5jaGlsZHJlbignZGl2Jyk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLmFkZENsYXNzKCd1aS10YWJzJyk7XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCAnLnRhYi1oZWFkbGluZS13cmFwcGVyID4gYScsIF9jbGlja0hhbmRsZXIpO1xuXHRcdFx0JHRoaXMub24oJ3Nob3c6dGFiJywgX3Nob3dIYW5kbGVyKTtcblx0XHRcdFxuXHRcdFx0Ly8gU2V0IGZpcnN0IHRhYiBhcyBzZWxlY3RlZC5cblx0XHRcdCRoZWFkbGluZVRhZ3Ncblx0XHRcdFx0LmVxKDApXG5cdFx0XHRcdC50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
