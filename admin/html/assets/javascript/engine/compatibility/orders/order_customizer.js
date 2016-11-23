'use strict';

/* --------------------------------------------------------------
 order_customizer.js 2015-08-10 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Modal Layer Module
 *
 * This module will open a modal layer for order actions like deleting or changing the oder status.
 *
 * @module Compatibility/order_customizer
 */
gx.compatibility.module('order_customizer', [],

/**  @lends module:Compatibility/order_customizer */

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
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _openCustomizerSet = function _openCustomizerSet(event) {

		var $customizer = $(options.selector);

		event.preventDefault();
		event.stopPropagation();
		$customizer.dialog({
			'title': jse.core.lang.translate('HEADING_GX_CUSTOMIZER', 'orders'),
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

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', _openCustomizerSet);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcl9jdXN0b21pemVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfb3BlbkN1c3RvbWl6ZXJTZXQiLCJldmVudCIsIiRjdXN0b21pemVyIiwic2VsZWN0b3IiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsImRpYWxvZyIsImpzZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0Msa0JBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSU8scUJBQXFCLFNBQXJCQSxrQkFBcUIsQ0FBU0MsS0FBVCxFQUFnQjs7QUFFeEMsTUFBSUMsY0FBY04sRUFBRUUsUUFBUUssUUFBVixDQUFsQjs7QUFFQUYsUUFBTUcsY0FBTjtBQUNBSCxRQUFNSSxlQUFOO0FBQ0FILGNBQVlJLE1BQVosQ0FBbUI7QUFDbEIsWUFBU0MsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsdUJBQXhCLEVBQWlELFFBQWpELENBRFM7QUFFbEIsWUFBUyxJQUZTO0FBR2xCLGtCQUFlLGNBSEc7QUFJbEIsY0FBVyxDQUNWO0FBQ0MsWUFBUUgsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsT0FBeEIsRUFBaUMsU0FBakMsQ0FEVDtBQUVDLGFBQVMsS0FGVjtBQUdDLGFBQVMsaUJBQVc7QUFDbkJkLE9BQUUsSUFBRixFQUFRVSxNQUFSLENBQWUsT0FBZjtBQUNBO0FBTEYsSUFEVSxDQUpPO0FBYWxCLFlBQVM7QUFiUyxHQUFuQjtBQWdCQSxFQXRCRDs7QUF3QkE7QUFDQTtBQUNBOztBQUVBYixRQUFPa0IsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QmpCLFFBQU1rQixFQUFOLENBQVMsT0FBVCxFQUFrQmIsa0JBQWxCO0FBQ0FZO0FBQ0EsRUFIRDs7QUFLQSxRQUFPbkIsTUFBUDtBQUNBLENBbEZGIiwiZmlsZSI6Im9yZGVycy9vcmRlcl9jdXN0b21pemVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvcmRlcl9jdXN0b21pemVyLmpzIDIwMTUtMDgtMTAgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIE9yZGVycyBNb2RhbCBMYXllciBNb2R1bGVcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIG9wZW4gYSBtb2RhbCBsYXllciBmb3Igb3JkZXIgYWN0aW9ucyBsaWtlIGRlbGV0aW5nIG9yIGNoYW5naW5nIHRoZSBvZGVyIHN0YXR1cy5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvb3JkZXJfY3VzdG9taXplclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J29yZGVyX2N1c3RvbWl6ZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9vcmRlcl9jdXN0b21pemVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgRlVOQ1RJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9vcGVuQ3VzdG9taXplclNldCA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcblx0XHRcdHZhciAkY3VzdG9taXplciA9ICQob3B0aW9ucy5zZWxlY3Rvcik7XG5cdFx0XHRcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdCRjdXN0b21pemVyLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdIRUFESU5HX0dYX0NVU1RPTUlaRVInLCAnb3JkZXJzJyksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF0sXG5cdFx0XHRcdCd3aWR0aCc6IDQyMFxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb3BlbkN1c3RvbWl6ZXJTZXQpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
