'use strict';

/* --------------------------------------------------------------
 shop_key.js 2016-03-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Shop Key
 *
 * This module will update the information in the textarea of the shop key page and opens a modal layer for
 * more detailed information of the shop key
 *
 * @module Controllers/shop_key
 */
gx.controllers.module('shop_key', [],

/**  @lends module:Controllers/shop_key */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
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
  * Entering the shop key into the input field updates the content of the textarea containing shop information
  * like the shop key
  *
  * @private
  */
	var _updateTextarea = function _updateTextarea() {
		var $textarea = $this.find('#shop-key-data'),
		    html = $textarea.html().replace(/shop_key=.*?\nlanguage/g, 'shop_key=' + $.trim($(this).val()) + '\nlanguage');

		$textarea.html(html);
	};

	/**
  * Clicking the link for more information about the shop key opens a modal box
  *
  * @param event
  * @private
  */
	var _showInformation = function _showInformation(event) {
		var $information = $('<p class="shop-key-information">' + jse.core.lang.translate('purpose_description', 'shop_key') + '</p>');

		event.preventDefault();

		$information.dialog({
			'title': jse.core.lang.translate('page_title', 'shop_key'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': [{
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function click() {
					$(this).dialog('close');
				}
			}],
			'width': 420
		});
	};

	/**
  * Update action parameter of form to the delete-url if delete button is clicked
  *
  * @param event
  * @private
  */
	var _deleteShopKey = function _deleteShopKey(event) {
		var actionUrl = $this.attr('action');

		event.preventDefault();

		actionUrl = actionUrl.replace('do=ShopKey/Store', 'do=ShopKey/Destroy');
		$this.attr('action', actionUrl);

		$this.submit();
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('change', '#gambio-shop-key', _updateTextarea).on('keyup', '#gambio-shop-key', _updateTextarea).on('click', '.show-shop-key-information', _showInformation).on('click', 'input[name="delete"]', _deleteShopKey);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNob3Bfa2V5L3Nob3Bfa2V5LmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3VwZGF0ZVRleHRhcmVhIiwiJHRleHRhcmVhIiwiZmluZCIsImh0bWwiLCJyZXBsYWNlIiwidHJpbSIsInZhbCIsIl9zaG93SW5mb3JtYXRpb24iLCJldmVudCIsIiRpbmZvcm1hdGlvbiIsImpzZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwicHJldmVudERlZmF1bHQiLCJkaWFsb2ciLCJfZGVsZXRlU2hvcEtleSIsImFjdGlvblVybCIsImF0dHIiLCJzdWJtaXQiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLFVBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQU1BLEtBQUlPLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNoQyxNQUFJQyxZQUFZTixNQUFNTyxJQUFOLENBQVcsZ0JBQVgsQ0FBaEI7QUFBQSxNQUNDQyxPQUFPRixVQUFVRSxJQUFWLEdBQWlCQyxPQUFqQixDQUF5Qix5QkFBekIsRUFBb0QsY0FBY1IsRUFBRVMsSUFBRixDQUFPVCxFQUFFLElBQUYsRUFBUVUsR0FBUixFQUFQLENBQWQsR0FDMUQsWUFETSxDQURSOztBQUlBTCxZQUFVRSxJQUFWLENBQWVBLElBQWY7QUFDQSxFQU5EOztBQVFBOzs7Ozs7QUFNQSxLQUFJSSxtQkFBbUIsU0FBbkJBLGdCQUFtQixDQUFTQyxLQUFULEVBQWdCO0FBQ3RDLE1BQUlDLGVBQWViLEVBQUUscUNBQ3BCYyxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixxQkFBeEIsRUFBK0MsVUFBL0MsQ0FEb0IsR0FFcEIsTUFGa0IsQ0FBbkI7O0FBSUFMLFFBQU1NLGNBQU47O0FBRUFMLGVBQWFNLE1BQWIsQ0FBb0I7QUFDbkIsWUFBU0wsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsWUFBeEIsRUFBc0MsVUFBdEMsQ0FEVTtBQUVuQixZQUFTLElBRlU7QUFHbkIsa0JBQWUsY0FISTtBQUluQixjQUFXLENBQ1Y7QUFDQyxZQUFRSCxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixPQUF4QixFQUFpQyxTQUFqQyxDQURUO0FBRUMsYUFBUyxLQUZWO0FBR0MsYUFBUyxpQkFBVztBQUNuQmpCLE9BQUUsSUFBRixFQUFRbUIsTUFBUixDQUFlLE9BQWY7QUFDQTtBQUxGLElBRFUsQ0FKUTtBQWFuQixZQUFTO0FBYlUsR0FBcEI7QUFlQSxFQXRCRDs7QUF3QkE7Ozs7OztBQU1BLEtBQUlDLGlCQUFpQixTQUFqQkEsY0FBaUIsQ0FBU1IsS0FBVCxFQUFnQjtBQUNwQyxNQUFJUyxZQUFZdEIsTUFBTXVCLElBQU4sQ0FBVyxRQUFYLENBQWhCOztBQUVBVixRQUFNTSxjQUFOOztBQUVBRyxjQUFZQSxVQUFVYixPQUFWLENBQWtCLGtCQUFsQixFQUFzQyxvQkFBdEMsQ0FBWjtBQUNBVCxRQUFNdUIsSUFBTixDQUFXLFFBQVgsRUFBcUJELFNBQXJCOztBQUVBdEIsUUFBTXdCLE1BQU47QUFDQSxFQVREOztBQVdBO0FBQ0E7QUFDQTs7QUFFQTFCLFFBQU8yQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCMUIsUUFDRTJCLEVBREYsQ0FDSyxRQURMLEVBQ2Usa0JBRGYsRUFDbUN0QixlQURuQyxFQUVFc0IsRUFGRixDQUVLLE9BRkwsRUFFYyxrQkFGZCxFQUVrQ3RCLGVBRmxDLEVBR0VzQixFQUhGLENBR0ssT0FITCxFQUdjLDRCQUhkLEVBRzRDZixnQkFINUMsRUFJRWUsRUFKRixDQUlLLE9BSkwsRUFJYyxzQkFKZCxFQUlzQ04sY0FKdEM7O0FBTUFLO0FBQ0EsRUFSRDs7QUFVQSxRQUFPNUIsTUFBUDtBQUNBLENBNUhGIiwiZmlsZSI6InNob3Bfa2V5L3Nob3Bfa2V5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzaG9wX2tleS5qcyAyMDE2LTAzLTE2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBTaG9wIEtleVxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgdXBkYXRlIHRoZSBpbmZvcm1hdGlvbiBpbiB0aGUgdGV4dGFyZWEgb2YgdGhlIHNob3Aga2V5IHBhZ2UgYW5kIG9wZW5zIGEgbW9kYWwgbGF5ZXIgZm9yXG4gKiBtb3JlIGRldGFpbGVkIGluZm9ybWF0aW9uIG9mIHRoZSBzaG9wIGtleVxuICpcbiAqIEBtb2R1bGUgQ29udHJvbGxlcnMvc2hvcF9rZXlcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnc2hvcF9rZXknLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvc2hvcF9rZXkgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBFbnRlcmluZyB0aGUgc2hvcCBrZXkgaW50byB0aGUgaW5wdXQgZmllbGQgdXBkYXRlcyB0aGUgY29udGVudCBvZiB0aGUgdGV4dGFyZWEgY29udGFpbmluZyBzaG9wIGluZm9ybWF0aW9uXG5cdFx0ICogbGlrZSB0aGUgc2hvcCBrZXlcblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVUZXh0YXJlYSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICR0ZXh0YXJlYSA9ICR0aGlzLmZpbmQoJyNzaG9wLWtleS1kYXRhJyksXG5cdFx0XHRcdGh0bWwgPSAkdGV4dGFyZWEuaHRtbCgpLnJlcGxhY2UoL3Nob3Bfa2V5PS4qP1xcbmxhbmd1YWdlL2csICdzaG9wX2tleT0nICsgJC50cmltKCQodGhpcykudmFsKCkpICtcblx0XHRcdFx0XHQnXFxubGFuZ3VhZ2UnKTtcblx0XHRcdFxuXHRcdFx0JHRleHRhcmVhLmh0bWwoaHRtbCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDbGlja2luZyB0aGUgbGluayBmb3IgbW9yZSBpbmZvcm1hdGlvbiBhYm91dCB0aGUgc2hvcCBrZXkgb3BlbnMgYSBtb2RhbCBib3hcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zaG93SW5mb3JtYXRpb24gPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyICRpbmZvcm1hdGlvbiA9ICQoJzxwIGNsYXNzPVwic2hvcC1rZXktaW5mb3JtYXRpb25cIj4nICtcblx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3B1cnBvc2VfZGVzY3JpcHRpb24nLCAnc2hvcF9rZXknKSArXG5cdFx0XHRcdCc8L3A+Jyk7XG5cdFx0XHRcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcblx0XHRcdCRpbmZvcm1hdGlvbi5kaWFsb2coe1xuXHRcdFx0XHQndGl0bGUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncGFnZV90aXRsZScsICdzaG9wX2tleScpLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBbXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdLFxuXHRcdFx0XHQnd2lkdGgnOiA0MjBcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogVXBkYXRlIGFjdGlvbiBwYXJhbWV0ZXIgb2YgZm9ybSB0byB0aGUgZGVsZXRlLXVybCBpZiBkZWxldGUgYnV0dG9uIGlzIGNsaWNrZWRcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9kZWxldGVTaG9wS2V5ID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBhY3Rpb25VcmwgPSAkdGhpcy5hdHRyKCdhY3Rpb24nKTtcblx0XHRcdFxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFxuXHRcdFx0YWN0aW9uVXJsID0gYWN0aW9uVXJsLnJlcGxhY2UoJ2RvPVNob3BLZXkvU3RvcmUnLCAnZG89U2hvcEtleS9EZXN0cm95Jyk7XG5cdFx0XHQkdGhpcy5hdHRyKCdhY3Rpb24nLCBhY3Rpb25VcmwpO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5zdWJtaXQoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NoYW5nZScsICcjZ2FtYmlvLXNob3Ata2V5JywgX3VwZGF0ZVRleHRhcmVhKVxuXHRcdFx0XHQub24oJ2tleXVwJywgJyNnYW1iaW8tc2hvcC1rZXknLCBfdXBkYXRlVGV4dGFyZWEpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLnNob3ctc2hvcC1rZXktaW5mb3JtYXRpb24nLCBfc2hvd0luZm9ybWF0aW9uKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJ2lucHV0W25hbWU9XCJkZWxldGVcIl0nLCBfZGVsZXRlU2hvcEtleSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
