'use strict';

/* --------------------------------------------------------------
 resize.js 2016-05-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Resize Layout Controller
 *
 * During the admin layout lifecycle there are events that will change the size of the document (not the window!)
 * and the layout must react to them. This controller will make sure that the layout will remain stable after such
 * changes are marked with the "data-resize-layout" attribute as in the following example.
 *
 * ```html
 * <!-- DataTable Instance -->
 * <table data-gx-widget="datatable" data-resize-layout="draw.dt">
 *   ...
 * </table>
 * ```
 *
 * After a table draw is performed, it is possible that there will be more rows to be displayed and thus the
 * #main-content element gets bigger. Once the datatable "draw.dt" event is executed this module will make
 * sure that the layout remains solid.
 *
 * The event must bubble up to the container this module is bound.
 *
 * ### Dynamic Elements
 *
 * It is possible that during the page lifecycle there will be dynamic elements that will need to register
 * an the "resize-layout" event. In this case apply the "data-resize-layout" attribute in the dynamic
 * element and trigger the "resize:bind" event from that element. The event must bubble up to the layout
 * container which will then register the dynamic elements.
 */
gx.controllers.module('resize', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Marks event listeners.
  *
  * @type {string}
  */

	var ATTRIBUTE_NAME = 'data-resize-layout';

	/**
  * Module Selector
  *
  * @type {jQuery}
  */
	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	/**
  * Main Header Selector
  *
  * @type {jQuery}
  */
	var $mainHeader = $('#main-header');

	/**
  * Main Menu Selector
  *
  * @type {jQuery}
  */
	var $mainMenu = $('#main-menu');

	/**
  * Main Footer Selector
  *
  * @type {jQuery}
  */
	var $mainFooter = $('#main-footer');

	/**
  * Main Footer Info
  *
  * @type {jQuery}
  */
	var $mainFooterInfo = $mainFooter.find('.info');

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Bind resize events.
  */
	function _bindResizeEvents() {
		$this.find('[' + ATTRIBUTE_NAME + ']').each(function () {
			var event = $(this).attr(ATTRIBUTE_NAME);
			$(this).removeAttr(ATTRIBUTE_NAME).on(event, _updateLayoutComponents);
		});
	}

	/**
  * Give initial min height to main menu.
  */
	function _updateLayoutComponents() {
		var mainMenuHeight = window.innerHeight - $mainHeader.outerHeight() - $mainFooterInfo.outerHeight();
		$mainMenu.css('min-height', mainMenuHeight);
		_setFooterInfoPosition();
	}

	/**
  * Calculate the correct footer info position.
  */
	function _setFooterInfoPosition() {
		if ($(document).scrollTop() + window.innerHeight - $mainFooterInfo.outerHeight() < $mainFooter.offset().top) {
			$mainFooter.addClass('fixed');
		} else if ($mainFooterInfo.offset().top + $mainFooterInfo.height() >= $mainFooter.offset().top) {
			$mainFooter.removeClass('fixed');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$(window).on('resize', _updateLayoutComponents).on('JSENGINE_INIT_FINISHED', _updateLayoutComponents).on('scroll', _setFooterInfoPosition).on('register:bind', _bindResizeEvents);

		_bindResizeEvents();

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9yZXNpemUuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCJBVFRSSUJVVEVfTkFNRSIsIiR0aGlzIiwiJCIsIiRtYWluSGVhZGVyIiwiJG1haW5NZW51IiwiJG1haW5Gb290ZXIiLCIkbWFpbkZvb3RlckluZm8iLCJmaW5kIiwiX2JpbmRSZXNpemVFdmVudHMiLCJlYWNoIiwiZXZlbnQiLCJhdHRyIiwicmVtb3ZlQXR0ciIsIm9uIiwiX3VwZGF0ZUxheW91dENvbXBvbmVudHMiLCJtYWluTWVudUhlaWdodCIsIndpbmRvdyIsImlubmVySGVpZ2h0Iiwib3V0ZXJIZWlnaHQiLCJjc3MiLCJfc2V0Rm9vdGVySW5mb1Bvc2l0aW9uIiwiZG9jdW1lbnQiLCJzY3JvbGxUb3AiLCJvZmZzZXQiLCJ0b3AiLCJhZGRDbGFzcyIsImhlaWdodCIsInJlbW92ZUNsYXNzIiwiaW5pdCIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMkJBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsUUFBdEIsRUFBZ0MsRUFBaEMsRUFBb0MsVUFBU0MsSUFBVCxFQUFlOztBQUVsRDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLGlCQUFpQixvQkFBdkI7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUosU0FBUyxFQUFmOztBQUVBOzs7OztBQUtBLEtBQU1LLGNBQWNELEVBQUUsY0FBRixDQUFwQjs7QUFFQTs7Ozs7QUFLQSxLQUFNRSxZQUFZRixFQUFFLFlBQUYsQ0FBbEI7O0FBRUE7Ozs7O0FBS0EsS0FBTUcsY0FBY0gsRUFBRSxjQUFGLENBQXBCOztBQUVBOzs7OztBQUtBLEtBQU1JLGtCQUFrQkQsWUFBWUUsSUFBWixDQUFpQixPQUFqQixDQUF4Qjs7QUFHQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBLFVBQVNDLGlCQUFULEdBQTZCO0FBQzVCUCxRQUFNTSxJQUFOLE9BQWVQLGNBQWYsUUFBa0NTLElBQWxDLENBQXVDLFlBQVc7QUFDakQsT0FBSUMsUUFBUVIsRUFBRSxJQUFGLEVBQVFTLElBQVIsQ0FBYVgsY0FBYixDQUFaO0FBQ0FFLEtBQUUsSUFBRixFQUNFVSxVQURGLENBQ2FaLGNBRGIsRUFFRWEsRUFGRixDQUVLSCxLQUZMLEVBRVlJLHVCQUZaO0FBR0EsR0FMRDtBQU1BOztBQUVEOzs7QUFHQSxVQUFTQSx1QkFBVCxHQUFtQztBQUNsQyxNQUFNQyxpQkFBaUJDLE9BQU9DLFdBQVAsR0FBcUJkLFlBQVllLFdBQVosRUFBckIsR0FBaURaLGdCQUFnQlksV0FBaEIsRUFBeEU7QUFDQWQsWUFBVWUsR0FBVixDQUFjLFlBQWQsRUFBNEJKLGNBQTVCO0FBQ0FLO0FBQ0E7O0FBRUQ7OztBQUdBLFVBQVNBLHNCQUFULEdBQWtDO0FBQ2pDLE1BQUtsQixFQUFFbUIsUUFBRixFQUFZQyxTQUFaLEtBQTBCTixPQUFPQyxXQUFqQyxHQUErQ1gsZ0JBQWdCWSxXQUFoQixFQUFoRCxHQUFpRmIsWUFBWWtCLE1BQVosR0FBcUJDLEdBQTFHLEVBQStHO0FBQzlHbkIsZUFBWW9CLFFBQVosQ0FBcUIsT0FBckI7QUFDQSxHQUZELE1BRU8sSUFBSW5CLGdCQUFnQmlCLE1BQWhCLEdBQXlCQyxHQUF6QixHQUErQmxCLGdCQUFnQm9CLE1BQWhCLEVBQS9CLElBQTJEckIsWUFBWWtCLE1BQVosR0FBcUJDLEdBQXBGLEVBQXlGO0FBQy9GbkIsZUFBWXNCLFdBQVosQ0FBd0IsT0FBeEI7QUFDQTtBQUNEOztBQUVEO0FBQ0E7QUFDQTs7QUFFQTdCLFFBQU84QixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCM0IsSUFBRWMsTUFBRixFQUNFSCxFQURGLENBQ0ssUUFETCxFQUNlQyx1QkFEZixFQUVFRCxFQUZGLENBRUssd0JBRkwsRUFFK0JDLHVCQUYvQixFQUdFRCxFQUhGLENBR0ssUUFITCxFQUdlTyxzQkFIZixFQUlFUCxFQUpGLENBSUssZUFKTCxFQUlzQkwsaUJBSnRCOztBQU1BQTs7QUFFQXFCO0FBQ0EsRUFWRDs7QUFZQSxRQUFPL0IsTUFBUDtBQUNBLENBL0dEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9yZXNpemUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gcmVzaXplLmpzIDIwMTYtMDUtMTJcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogUmVzaXplIExheW91dCBDb250cm9sbGVyXHJcbiAqXHJcbiAqIER1cmluZyB0aGUgYWRtaW4gbGF5b3V0IGxpZmVjeWNsZSB0aGVyZSBhcmUgZXZlbnRzIHRoYXQgd2lsbCBjaGFuZ2UgdGhlIHNpemUgb2YgdGhlIGRvY3VtZW50IChub3QgdGhlIHdpbmRvdyEpXHJcbiAqIGFuZCB0aGUgbGF5b3V0IG11c3QgcmVhY3QgdG8gdGhlbS4gVGhpcyBjb250cm9sbGVyIHdpbGwgbWFrZSBzdXJlIHRoYXQgdGhlIGxheW91dCB3aWxsIHJlbWFpbiBzdGFibGUgYWZ0ZXIgc3VjaFxyXG4gKiBjaGFuZ2VzIGFyZSBtYXJrZWQgd2l0aCB0aGUgXCJkYXRhLXJlc2l6ZS1sYXlvdXRcIiBhdHRyaWJ1dGUgYXMgaW4gdGhlIGZvbGxvd2luZyBleGFtcGxlLlxyXG4gKlxyXG4gKiBgYGBodG1sXHJcbiAqIDwhLS0gRGF0YVRhYmxlIEluc3RhbmNlIC0tPlxyXG4gKiA8dGFibGUgZGF0YS1neC13aWRnZXQ9XCJkYXRhdGFibGVcIiBkYXRhLXJlc2l6ZS1sYXlvdXQ9XCJkcmF3LmR0XCI+XHJcbiAqICAgLi4uXHJcbiAqIDwvdGFibGU+XHJcbiAqIGBgYFxyXG4gKlxyXG4gKiBBZnRlciBhIHRhYmxlIGRyYXcgaXMgcGVyZm9ybWVkLCBpdCBpcyBwb3NzaWJsZSB0aGF0IHRoZXJlIHdpbGwgYmUgbW9yZSByb3dzIHRvIGJlIGRpc3BsYXllZCBhbmQgdGh1cyB0aGVcclxuICogI21haW4tY29udGVudCBlbGVtZW50IGdldHMgYmlnZ2VyLiBPbmNlIHRoZSBkYXRhdGFibGUgXCJkcmF3LmR0XCIgZXZlbnQgaXMgZXhlY3V0ZWQgdGhpcyBtb2R1bGUgd2lsbCBtYWtlXHJcbiAqIHN1cmUgdGhhdCB0aGUgbGF5b3V0IHJlbWFpbnMgc29saWQuXHJcbiAqXHJcbiAqIFRoZSBldmVudCBtdXN0IGJ1YmJsZSB1cCB0byB0aGUgY29udGFpbmVyIHRoaXMgbW9kdWxlIGlzIGJvdW5kLlxyXG4gKlxyXG4gKiAjIyMgRHluYW1pYyBFbGVtZW50c1xyXG4gKlxyXG4gKiBJdCBpcyBwb3NzaWJsZSB0aGF0IGR1cmluZyB0aGUgcGFnZSBsaWZlY3ljbGUgdGhlcmUgd2lsbCBiZSBkeW5hbWljIGVsZW1lbnRzIHRoYXQgd2lsbCBuZWVkIHRvIHJlZ2lzdGVyXHJcbiAqIGFuIHRoZSBcInJlc2l6ZS1sYXlvdXRcIiBldmVudC4gSW4gdGhpcyBjYXNlIGFwcGx5IHRoZSBcImRhdGEtcmVzaXplLWxheW91dFwiIGF0dHJpYnV0ZSBpbiB0aGUgZHluYW1pY1xyXG4gKiBlbGVtZW50IGFuZCB0cmlnZ2VyIHRoZSBcInJlc2l6ZTpiaW5kXCIgZXZlbnQgZnJvbSB0aGF0IGVsZW1lbnQuIFRoZSBldmVudCBtdXN0IGJ1YmJsZSB1cCB0byB0aGUgbGF5b3V0XHJcbiAqIGNvbnRhaW5lciB3aGljaCB3aWxsIHRoZW4gcmVnaXN0ZXIgdGhlIGR5bmFtaWMgZWxlbWVudHMuXHJcbiAqL1xyXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ3Jlc2l6ZScsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1hcmtzIGV2ZW50IGxpc3RlbmVycy5cclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtzdHJpbmd9XHJcblx0ICovXHJcblx0Y29uc3QgQVRUUklCVVRFX05BTUUgPSAnZGF0YS1yZXNpemUtbGF5b3V0JztcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNYWluIEhlYWRlciBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkbWFpbkhlYWRlciA9ICQoJyNtYWluLWhlYWRlcicpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1haW4gTWVudSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkbWFpbk1lbnUgPSAkKCcjbWFpbi1tZW51Jyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTWFpbiBGb290ZXIgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJG1haW5Gb290ZXIgPSAkKCcjbWFpbi1mb290ZXInKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNYWluIEZvb3RlciBJbmZvXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICRtYWluRm9vdGVySW5mbyA9ICRtYWluRm9vdGVyLmZpbmQoJy5pbmZvJyk7XHJcblx0XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogQmluZCByZXNpemUgZXZlbnRzLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9iaW5kUmVzaXplRXZlbnRzKCkge1xyXG5cdFx0JHRoaXMuZmluZChgWyR7QVRUUklCVVRFX05BTUV9XWApLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRcdGxldCBldmVudCA9ICQodGhpcykuYXR0cihBVFRSSUJVVEVfTkFNRSk7XHJcblx0XHRcdCQodGhpcylcclxuXHRcdFx0XHQucmVtb3ZlQXR0cihBVFRSSUJVVEVfTkFNRSlcclxuXHRcdFx0XHQub24oZXZlbnQsIF91cGRhdGVMYXlvdXRDb21wb25lbnRzKTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBHaXZlIGluaXRpYWwgbWluIGhlaWdodCB0byBtYWluIG1lbnUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX3VwZGF0ZUxheW91dENvbXBvbmVudHMoKSB7XHJcblx0XHRjb25zdCBtYWluTWVudUhlaWdodCA9IHdpbmRvdy5pbm5lckhlaWdodCAtICRtYWluSGVhZGVyLm91dGVySGVpZ2h0KCkgLSAkbWFpbkZvb3RlckluZm8ub3V0ZXJIZWlnaHQoKTtcclxuXHRcdCRtYWluTWVudS5jc3MoJ21pbi1oZWlnaHQnLCBtYWluTWVudUhlaWdodCk7XHJcblx0XHRfc2V0Rm9vdGVySW5mb1Bvc2l0aW9uKCk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIENhbGN1bGF0ZSB0aGUgY29ycmVjdCBmb290ZXIgaW5mbyBwb3NpdGlvbi5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfc2V0Rm9vdGVySW5mb1Bvc2l0aW9uKCkge1xyXG5cdFx0aWYgKCgkKGRvY3VtZW50KS5zY3JvbGxUb3AoKSArIHdpbmRvdy5pbm5lckhlaWdodCAtICRtYWluRm9vdGVySW5mby5vdXRlckhlaWdodCgpKSA8ICRtYWluRm9vdGVyLm9mZnNldCgpLnRvcCkge1xyXG5cdFx0XHQkbWFpbkZvb3Rlci5hZGRDbGFzcygnZml4ZWQnKTtcclxuXHRcdH0gZWxzZSBpZiAoJG1haW5Gb290ZXJJbmZvLm9mZnNldCgpLnRvcCArICRtYWluRm9vdGVySW5mby5oZWlnaHQoKSA+PSAkbWFpbkZvb3Rlci5vZmZzZXQoKS50b3ApIHtcclxuXHRcdFx0JG1haW5Gb290ZXIucmVtb3ZlQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkKHdpbmRvdylcclxuXHRcdFx0Lm9uKCdyZXNpemUnLCBfdXBkYXRlTGF5b3V0Q29tcG9uZW50cylcclxuXHRcdFx0Lm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgX3VwZGF0ZUxheW91dENvbXBvbmVudHMpXHJcblx0XHRcdC5vbignc2Nyb2xsJywgX3NldEZvb3RlckluZm9Qb3NpdGlvbilcclxuXHRcdFx0Lm9uKCdyZWdpc3RlcjpiaW5kJywgX2JpbmRSZXNpemVFdmVudHMpO1xyXG5cdFx0XHJcblx0XHRfYmluZFJlc2l6ZUV2ZW50cygpO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG59KTtcclxuIl19
