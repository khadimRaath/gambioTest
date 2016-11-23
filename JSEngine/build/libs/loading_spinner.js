'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 loading_spinner.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.loading_spinner = jse.libs.loading_spinner || {};

/**
 * ## Loading Spinner Library
 *
 * This library provides an easy and simple way to display a loading spinner inside any container
 * element to provide a smooth "loading" experience to the UI. If no container is specified then
 * the whole page will be taken for the display. The loading spinner comes from the Font Awesome
 * "fa-spinner" class. You can load this library as a dependency to existing modules.
 *
 * The following usage example will show you how to display and hide the spinner inside an element.
 *
 * ```javascript
 * // Create a selector variable for the target element.
 * var $targetElement = $('#my-div');
 *
 * // The $targetElement will be overlayed by the spinner.
 * var $spinner = window.jse.libs.loading_spinner.show($targetElement);
 *
 * // Do some stuff ...
 *
 * // Hide the spinner when the job is done.
 * window.jse.loading_spinner.hide($spinner);
 * ```
 *
 * @module JSE/Libs/loading_spinner
 * @exports jse.libs.loading_spinner
 */
(function (exports) {

	'use strict';

	/**
  * Contains a list of the active spinners so that they can be validated
  * before they are destroyed.
  *
  * @type {Array}
  */

	var instances = [];

	/**
  * Show the loading spinner to the target element.
  *
  * @param {jQuery} $targetElement (optional) The target element will be overlayed by the spinner. If no
  * argument is provided then the spinner will overlay the whole page.
  * @param {String} zIndex Optional ('auto'), give a specific z-index value to the loading spinner. 
  *
  * @return {jQuery} Returns the selector of the spinner div element. You can further manipulate the spinner
  * if required, but you have to provide this selector as a parameter to the "hide" method below.
  */
	exports.show = function ($targetElement) {
		var zIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'auto';

		if ($targetElement !== undefined && (typeof $targetElement === 'undefined' ? 'undefined' : _typeof($targetElement)) !== 'object') {
			throw new Error('Invalid argument provided for the "show" method: ' + (typeof $targetElement === 'undefined' ? 'undefined' : _typeof($targetElement)));
		}

		if ($targetElement.length === 0) {
			return; // No element matches the provided selector. 
		}

		$targetElement = $targetElement || $('body'); // set default value

		var $spinner = $('<div class="loading-spinner"></div>');
		var fontSize = 80;

		$spinner.html('<i class="fa fa-spinner fa-spin"></i>').css({
			width: $targetElement.innerWidth() + 'px',
			height: $targetElement.innerHeight() + 'px',
			boxSizing: 'border-box',
			background: '#FFF',
			opacity: '0.8',
			position: 'absolute',
			top: $targetElement.offset().top,
			left: $targetElement.offset().left,
			fontSize: fontSize + 'px',
			color: '#2196F3', // primary color
			zIndex: zIndex
		}).appendTo('body');

		$spinner.find('i').css({
			position: 'absolute',
			left: $spinner.width() / 2 - fontSize / 2,
			top: $spinner.height() / 2 - fontSize / 2
		});

		instances.push($spinner);

		return $spinner;
	};

	/**
  * Hide an existing spinner.
  *
  * This method will hide and remove the loading spinner markup from the document entirely.
  *
  * @param {jQuery} $spinner Must be the selector provided from the "show" method. If the selector
  * is invalid or no elements were found then an exception will be thrown.
  *
  * @return {jQuery.Promise} Returns a promise object that will be resolved once the spinner is removed.
  *
  * @throws Error If the $spinner selector was not found in the spinner instances.
  */
	exports.hide = function ($spinner) {
		var index = instances.indexOf($spinner);
		var deferred = $.Deferred();

		if (index === -1) {
			throw new Error('The provided spinner instance does not exist.');
		}

		instances.splice(index, 1);

		$spinner.fadeOut(400, function () {
			$spinner.remove();
			deferred.resolve();
		});

		return deferred.promise();
	};
})(jse.libs.loading_spinner);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxvYWRpbmdfc3Bpbm5lci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwibG9hZGluZ19zcGlubmVyIiwiZXhwb3J0cyIsImluc3RhbmNlcyIsInNob3ciLCIkdGFyZ2V0RWxlbWVudCIsInpJbmRleCIsInVuZGVmaW5lZCIsIkVycm9yIiwibGVuZ3RoIiwiJCIsIiRzcGlubmVyIiwiZm9udFNpemUiLCJodG1sIiwiY3NzIiwid2lkdGgiLCJpbm5lcldpZHRoIiwiaGVpZ2h0IiwiaW5uZXJIZWlnaHQiLCJib3hTaXppbmciLCJiYWNrZ3JvdW5kIiwib3BhY2l0eSIsInBvc2l0aW9uIiwidG9wIiwib2Zmc2V0IiwibGVmdCIsImNvbG9yIiwiYXBwZW5kVG8iLCJmaW5kIiwicHVzaCIsImhpZGUiLCJpbmRleCIsImluZGV4T2YiLCJkZWZlcnJlZCIsIkRlZmVycmVkIiwic3BsaWNlIiwiZmFkZU91dCIsInJlbW92ZSIsInJlc29sdmUiLCJwcm9taXNlIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxlQUFULEdBQTJCRixJQUFJQyxJQUFKLENBQVNDLGVBQVQsSUFBNEIsRUFBdkQ7O0FBRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMEJBLENBQUMsVUFBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7Ozs7Ozs7QUFNQSxLQUFNQyxZQUFZLEVBQWxCOztBQUVBOzs7Ozs7Ozs7O0FBVUFELFNBQVFFLElBQVIsR0FBZSxVQUFTQyxjQUFULEVBQTBDO0FBQUEsTUFBakJDLE1BQWlCLHVFQUFSLE1BQVE7O0FBQ3hELE1BQUlELG1CQUFtQkUsU0FBbkIsSUFBZ0MsUUFBT0YsY0FBUCx5Q0FBT0EsY0FBUCxPQUEwQixRQUE5RCxFQUF3RTtBQUN2RSxTQUFNLElBQUlHLEtBQUosQ0FBVSw4REFBNkRILGNBQTdELHlDQUE2REEsY0FBN0QsRUFBVixDQUFOO0FBQ0E7O0FBRUQsTUFBSUEsZUFBZUksTUFBZixLQUEwQixDQUE5QixFQUFpQztBQUNoQyxVQURnQyxDQUN4QjtBQUNSOztBQUVESixtQkFBaUJBLGtCQUFrQkssRUFBRSxNQUFGLENBQW5DLENBVHdELENBU1Y7O0FBRTlDLE1BQU1DLFdBQVdELEVBQUUscUNBQUYsQ0FBakI7QUFDQSxNQUFNRSxXQUFXLEVBQWpCOztBQUVBRCxXQUNFRSxJQURGLENBQ08sdUNBRFAsRUFFRUMsR0FGRixDQUVNO0FBQ0pDLFVBQU9WLGVBQWVXLFVBQWYsS0FBOEIsSUFEakM7QUFFSkMsV0FBUVosZUFBZWEsV0FBZixLQUErQixJQUZuQztBQUdKQyxjQUFXLFlBSFA7QUFJSkMsZUFBWSxNQUpSO0FBS0pDLFlBQVMsS0FMTDtBQU1KQyxhQUFVLFVBTk47QUFPSkMsUUFBS2xCLGVBQWVtQixNQUFmLEdBQXdCRCxHQVB6QjtBQVFKRSxTQUFNcEIsZUFBZW1CLE1BQWYsR0FBd0JDLElBUjFCO0FBU0piLGFBQVVBLFdBQVcsSUFUakI7QUFVSmMsVUFBTyxTQVZILEVBVWM7QUFDbEJwQixXQUFRQTtBQVhKLEdBRk4sRUFlRXFCLFFBZkYsQ0FlVyxNQWZYOztBQWlCQWhCLFdBQVNpQixJQUFULENBQWMsR0FBZCxFQUFtQmQsR0FBbkIsQ0FBdUI7QUFDdEJRLGFBQVUsVUFEWTtBQUV0QkcsU0FBTWQsU0FBU0ksS0FBVCxLQUFtQixDQUFuQixHQUF1QkgsV0FBVyxDQUZsQjtBQUd0QlcsUUFBS1osU0FBU00sTUFBVCxLQUFvQixDQUFwQixHQUF3QkwsV0FBVztBQUhsQixHQUF2Qjs7QUFNQVQsWUFBVTBCLElBQVYsQ0FBZWxCLFFBQWY7O0FBRUEsU0FBT0EsUUFBUDtBQUNBLEVBeENEOztBQTBDQTs7Ozs7Ozs7Ozs7O0FBWUFULFNBQVE0QixJQUFSLEdBQWUsVUFBU25CLFFBQVQsRUFBbUI7QUFDakMsTUFBTW9CLFFBQVE1QixVQUFVNkIsT0FBVixDQUFrQnJCLFFBQWxCLENBQWQ7QUFDQSxNQUFNc0IsV0FBV3ZCLEVBQUV3QixRQUFGLEVBQWpCOztBQUVBLE1BQUlILFVBQVUsQ0FBQyxDQUFmLEVBQWtCO0FBQ2pCLFNBQU0sSUFBSXZCLEtBQUosQ0FBVSwrQ0FBVixDQUFOO0FBQ0E7O0FBRURMLFlBQVVnQyxNQUFWLENBQWlCSixLQUFqQixFQUF3QixDQUF4Qjs7QUFFQXBCLFdBQVN5QixPQUFULENBQWlCLEdBQWpCLEVBQXNCLFlBQVc7QUFDaEN6QixZQUFTMEIsTUFBVDtBQUNBSixZQUFTSyxPQUFUO0FBQ0EsR0FIRDs7QUFLQSxTQUFPTCxTQUFTTSxPQUFULEVBQVA7QUFDQSxFQWhCRDtBQWtCQSxDQTlGRCxFQThGR3hDLElBQUlDLElBQUosQ0FBU0MsZUE5RloiLCJmaWxlIjoibG9hZGluZ19zcGlubmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBsb2FkaW5nX3NwaW5uZXIuanMgMjAxNi0wNi0xNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lciB8fCB7fTtcblxuLyoqXG4gKiAjIyBMb2FkaW5nIFNwaW5uZXIgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBwcm92aWRlcyBhbiBlYXN5IGFuZCBzaW1wbGUgd2F5IHRvIGRpc3BsYXkgYSBsb2FkaW5nIHNwaW5uZXIgaW5zaWRlIGFueSBjb250YWluZXJcbiAqIGVsZW1lbnQgdG8gcHJvdmlkZSBhIHNtb290aCBcImxvYWRpbmdcIiBleHBlcmllbmNlIHRvIHRoZSBVSS4gSWYgbm8gY29udGFpbmVyIGlzIHNwZWNpZmllZCB0aGVuXG4gKiB0aGUgd2hvbGUgcGFnZSB3aWxsIGJlIHRha2VuIGZvciB0aGUgZGlzcGxheS4gVGhlIGxvYWRpbmcgc3Bpbm5lciBjb21lcyBmcm9tIHRoZSBGb250IEF3ZXNvbWVcbiAqIFwiZmEtc3Bpbm5lclwiIGNsYXNzLiBZb3UgY2FuIGxvYWQgdGhpcyBsaWJyYXJ5IGFzIGEgZGVwZW5kZW5jeSB0byBleGlzdGluZyBtb2R1bGVzLlxuICpcbiAqIFRoZSBmb2xsb3dpbmcgdXNhZ2UgZXhhbXBsZSB3aWxsIHNob3cgeW91IGhvdyB0byBkaXNwbGF5IGFuZCBoaWRlIHRoZSBzcGlubmVyIGluc2lkZSBhbiBlbGVtZW50LlxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIC8vIENyZWF0ZSBhIHNlbGVjdG9yIHZhcmlhYmxlIGZvciB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gKiB2YXIgJHRhcmdldEVsZW1lbnQgPSAkKCcjbXktZGl2Jyk7XG4gKlxuICogLy8gVGhlICR0YXJnZXRFbGVtZW50IHdpbGwgYmUgb3ZlcmxheWVkIGJ5IHRoZSBzcGlubmVyLlxuICogdmFyICRzcGlubmVyID0gd2luZG93LmpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCR0YXJnZXRFbGVtZW50KTtcbiAqXG4gKiAvLyBEbyBzb21lIHN0dWZmIC4uLlxuICpcbiAqIC8vIEhpZGUgdGhlIHNwaW5uZXIgd2hlbiB0aGUgam9iIGlzIGRvbmUuXG4gKiB3aW5kb3cuanNlLmxvYWRpbmdfc3Bpbm5lci5oaWRlKCRzcGlubmVyKTtcbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvbG9hZGluZ19zcGlubmVyXG4gKiBAZXhwb3J0cyBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXJcbiAqL1xuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBDb250YWlucyBhIGxpc3Qgb2YgdGhlIGFjdGl2ZSBzcGlubmVycyBzbyB0aGF0IHRoZXkgY2FuIGJlIHZhbGlkYXRlZFxuXHQgKiBiZWZvcmUgdGhleSBhcmUgZGVzdHJveWVkLlxuXHQgKlxuXHQgKiBAdHlwZSB7QXJyYXl9XG5cdCAqL1xuXHRjb25zdCBpbnN0YW5jZXMgPSBbXTtcblx0XG5cdC8qKlxuXHQgKiBTaG93IHRoZSBsb2FkaW5nIHNwaW5uZXIgdG8gdGhlIHRhcmdldCBlbGVtZW50LlxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldEVsZW1lbnQgKG9wdGlvbmFsKSBUaGUgdGFyZ2V0IGVsZW1lbnQgd2lsbCBiZSBvdmVybGF5ZWQgYnkgdGhlIHNwaW5uZXIuIElmIG5vXG5cdCAqIGFyZ3VtZW50IGlzIHByb3ZpZGVkIHRoZW4gdGhlIHNwaW5uZXIgd2lsbCBvdmVybGF5IHRoZSB3aG9sZSBwYWdlLlxuXHQgKiBAcGFyYW0ge1N0cmluZ30gekluZGV4IE9wdGlvbmFsICgnYXV0bycpLCBnaXZlIGEgc3BlY2lmaWMgei1pbmRleCB2YWx1ZSB0byB0aGUgbG9hZGluZyBzcGlubmVyLiBcblx0ICpcblx0ICogQHJldHVybiB7alF1ZXJ5fSBSZXR1cm5zIHRoZSBzZWxlY3RvciBvZiB0aGUgc3Bpbm5lciBkaXYgZWxlbWVudC4gWW91IGNhbiBmdXJ0aGVyIG1hbmlwdWxhdGUgdGhlIHNwaW5uZXJcblx0ICogaWYgcmVxdWlyZWQsIGJ1dCB5b3UgaGF2ZSB0byBwcm92aWRlIHRoaXMgc2VsZWN0b3IgYXMgYSBwYXJhbWV0ZXIgdG8gdGhlIFwiaGlkZVwiIG1ldGhvZCBiZWxvdy5cblx0ICovXG5cdGV4cG9ydHMuc2hvdyA9IGZ1bmN0aW9uKCR0YXJnZXRFbGVtZW50LCB6SW5kZXggPSAnYXV0bycpIHtcblx0XHRpZiAoJHRhcmdldEVsZW1lbnQgIT09IHVuZGVmaW5lZCAmJiB0eXBlb2YgJHRhcmdldEVsZW1lbnQgIT09ICdvYmplY3QnKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgYXJndW1lbnQgcHJvdmlkZWQgZm9yIHRoZSBcInNob3dcIiBtZXRob2Q6ICcgKyB0eXBlb2YgJHRhcmdldEVsZW1lbnQpO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoJHRhcmdldEVsZW1lbnQubGVuZ3RoID09PSAwKSB7XG5cdFx0XHRyZXR1cm47IC8vIE5vIGVsZW1lbnQgbWF0Y2hlcyB0aGUgcHJvdmlkZWQgc2VsZWN0b3IuIFxuXHRcdH1cblx0XHRcblx0XHQkdGFyZ2V0RWxlbWVudCA9ICR0YXJnZXRFbGVtZW50IHx8ICQoJ2JvZHknKTsgLy8gc2V0IGRlZmF1bHQgdmFsdWVcblx0XHRcblx0XHRjb25zdCAkc3Bpbm5lciA9ICQoJzxkaXYgY2xhc3M9XCJsb2FkaW5nLXNwaW5uZXJcIj48L2Rpdj4nKTtcblx0XHRjb25zdCBmb250U2l6ZSA9IDgwO1xuXHRcdFxuXHRcdCRzcGlubmVyXG5cdFx0XHQuaHRtbCgnPGkgY2xhc3M9XCJmYSBmYS1zcGlubmVyIGZhLXNwaW5cIj48L2k+Jylcblx0XHRcdC5jc3Moe1xuXHRcdFx0XHR3aWR0aDogJHRhcmdldEVsZW1lbnQuaW5uZXJXaWR0aCgpICsgJ3B4Jyxcblx0XHRcdFx0aGVpZ2h0OiAkdGFyZ2V0RWxlbWVudC5pbm5lckhlaWdodCgpICsgJ3B4Jyxcblx0XHRcdFx0Ym94U2l6aW5nOiAnYm9yZGVyLWJveCcsXG5cdFx0XHRcdGJhY2tncm91bmQ6ICcjRkZGJyxcblx0XHRcdFx0b3BhY2l0eTogJzAuOCcsXG5cdFx0XHRcdHBvc2l0aW9uOiAnYWJzb2x1dGUnLFxuXHRcdFx0XHR0b3A6ICR0YXJnZXRFbGVtZW50Lm9mZnNldCgpLnRvcCxcblx0XHRcdFx0bGVmdDogJHRhcmdldEVsZW1lbnQub2Zmc2V0KCkubGVmdCxcblx0XHRcdFx0Zm9udFNpemU6IGZvbnRTaXplICsgJ3B4Jyxcblx0XHRcdFx0Y29sb3I6ICcjMjE5NkYzJywgLy8gcHJpbWFyeSBjb2xvclxuXHRcdFx0XHR6SW5kZXg6IHpJbmRleFxuXHRcdFx0fSlcblx0XHRcdC5hcHBlbmRUbygnYm9keScpO1xuXHRcdFxuXHRcdCRzcGlubmVyLmZpbmQoJ2knKS5jc3Moe1xuXHRcdFx0cG9zaXRpb246ICdhYnNvbHV0ZScsXG5cdFx0XHRsZWZ0OiAkc3Bpbm5lci53aWR0aCgpIC8gMiAtIGZvbnRTaXplIC8gMixcblx0XHRcdHRvcDogJHNwaW5uZXIuaGVpZ2h0KCkgLyAyIC0gZm9udFNpemUgLyAyXG5cdFx0fSk7XG5cdFx0XG5cdFx0aW5zdGFuY2VzLnB1c2goJHNwaW5uZXIpO1xuXHRcdFxuXHRcdHJldHVybiAkc3Bpbm5lcjtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBIaWRlIGFuIGV4aXN0aW5nIHNwaW5uZXIuXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgaGlkZSBhbmQgcmVtb3ZlIHRoZSBsb2FkaW5nIHNwaW5uZXIgbWFya3VwIGZyb20gdGhlIGRvY3VtZW50IGVudGlyZWx5LlxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHNwaW5uZXIgTXVzdCBiZSB0aGUgc2VsZWN0b3IgcHJvdmlkZWQgZnJvbSB0aGUgXCJzaG93XCIgbWV0aG9kLiBJZiB0aGUgc2VsZWN0b3Jcblx0ICogaXMgaW52YWxpZCBvciBubyBlbGVtZW50cyB3ZXJlIGZvdW5kIHRoZW4gYW4gZXhjZXB0aW9uIHdpbGwgYmUgdGhyb3duLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtqUXVlcnkuUHJvbWlzZX0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0IHRoYXQgd2lsbCBiZSByZXNvbHZlZCBvbmNlIHRoZSBzcGlubmVyIGlzIHJlbW92ZWQuXG5cdCAqXG5cdCAqIEB0aHJvd3MgRXJyb3IgSWYgdGhlICRzcGlubmVyIHNlbGVjdG9yIHdhcyBub3QgZm91bmQgaW4gdGhlIHNwaW5uZXIgaW5zdGFuY2VzLlxuXHQgKi9cblx0ZXhwb3J0cy5oaWRlID0gZnVuY3Rpb24oJHNwaW5uZXIpIHtcblx0XHRjb25zdCBpbmRleCA9IGluc3RhbmNlcy5pbmRleE9mKCRzcGlubmVyKTsgXG5cdFx0Y29uc3QgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XG5cdFx0aWYgKGluZGV4ID09PSAtMSkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKCdUaGUgcHJvdmlkZWQgc3Bpbm5lciBpbnN0YW5jZSBkb2VzIG5vdCBleGlzdC4nKTtcblx0XHR9XG5cdFx0XG5cdFx0aW5zdGFuY2VzLnNwbGljZShpbmRleCwgMSk7XG5cdFx0XG5cdFx0JHNwaW5uZXIuZmFkZU91dCg0MDAsIGZ1bmN0aW9uKCkge1xuXHRcdFx0JHNwaW5uZXIucmVtb3ZlKCk7XG5cdFx0XHRkZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0cmV0dXJuIGRlZmVycmVkLnByb21pc2UoKTtcblx0fTtcblx0XG59KShqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIpO1xuIl19
