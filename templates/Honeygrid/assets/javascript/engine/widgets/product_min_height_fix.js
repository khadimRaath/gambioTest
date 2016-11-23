'use strict';

/* --------------------------------------------------------------
 product_min_height_fix.js 2016-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that fixes min height of product info content element
 */
gambio.widgets.module('product_min_height_fix', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    defaults = {
		productInfoContent: '.product-info-content' // Selector to apply min height to
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########		

	/**
  * Fix for problem that box overlaps content like cross selling products if product content is too short
  *
  * @private
  */
	var _setProductInfoContentMinHeight = function _setProductInfoContentMinHeight() {
		var minHeight = $this.outerHeight() + parseFloat($this.css('top'));
		$(options.productInfoContent).css('min-height', minHeight + 'px');
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		_setProductInfoContentMinHeight();

		$window.on(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE(), _setProductInfoContentMinHeight);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9taW5faGVpZ2h0X2ZpeC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCJkZWZhdWx0cyIsInByb2R1Y3RJbmZvQ29udGVudCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2V0UHJvZHVjdEluZm9Db250ZW50TWluSGVpZ2h0IiwibWluSGVpZ2h0Iiwib3V0ZXJIZWlnaHQiLCJwYXJzZUZsb2F0IiwiY3NzIiwiaW5pdCIsImRvbmUiLCJvbiIsImpzZSIsImxpYnMiLCJ0ZW1wbGF0ZSIsImV2ZW50cyIsIlNUSUNLWUJPWF9DT05URU5UX0NIQU5HRSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0Msd0JBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLENBSEQsRUFPQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVRCxFQUFFRSxNQUFGLENBRFg7QUFBQSxLQUVDQyxXQUFXO0FBQ1ZDLHNCQUFvQix1QkFEVixDQUNrQztBQURsQyxFQUZaO0FBQUEsS0FLQ0MsVUFBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkwsSUFBN0IsQ0FMWDtBQUFBLEtBTUNGLFNBQVMsRUFOVjs7QUFRQTs7QUFFQTs7Ozs7QUFLQSxLQUFJVyxrQ0FBa0MsU0FBbENBLCtCQUFrQyxHQUFXO0FBQ2hELE1BQUlDLFlBQVlULE1BQU1VLFdBQU4sS0FBc0JDLFdBQVdYLE1BQU1ZLEdBQU4sQ0FBVSxLQUFWLENBQVgsQ0FBdEM7QUFDQVgsSUFBRUssUUFBUUQsa0JBQVYsRUFBOEJPLEdBQTlCLENBQWtDLFlBQWxDLEVBQWdESCxZQUFZLElBQTVEO0FBQ0EsRUFIRDs7QUFLQTs7QUFFQTs7OztBQUlBWixRQUFPZ0IsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1Qk47O0FBRUFOLFVBQVFhLEVBQVIsQ0FBV0MsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxNQUFsQixDQUF5QkMsd0JBQXpCLEVBQVgsRUFBZ0VaLCtCQUFoRTs7QUFFQU07QUFDQSxFQU5EOztBQVFBO0FBQ0EsUUFBT2pCLE1BQVA7QUFDQSxDQWpERiIsImZpbGUiOiJ3aWRnZXRzL3Byb2R1Y3RfbWluX2hlaWdodF9maXguanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gcHJvZHVjdF9taW5faGVpZ2h0X2ZpeC5qcyAyMDE2LTA1LTIzXHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIFdpZGdldCB0aGF0IGZpeGVzIG1pbiBoZWlnaHQgb2YgcHJvZHVjdCBpbmZvIGNvbnRlbnQgZWxlbWVudFxyXG4gKi9cclxuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxyXG5cdCdwcm9kdWN0X21pbl9oZWlnaHRfZml4JyxcclxuXHRcclxuXHRbXHJcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cycsXHJcblx0XSxcclxuXHRcclxuXHRmdW5jdGlvbihkYXRhKSB7XHJcblx0XHRcclxuXHRcdCd1c2Ugc3RyaWN0JztcclxuXHRcdFxyXG5cdFx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXHJcblx0XHRcclxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXHJcblx0XHRcdCR3aW5kb3cgPSAkKHdpbmRvdyksXHJcblx0XHRcdGRlZmF1bHRzID0ge1xyXG5cdFx0XHRcdHByb2R1Y3RJbmZvQ29udGVudDogJy5wcm9kdWN0LWluZm8tY29udGVudCcgLy8gU2VsZWN0b3IgdG8gYXBwbHkgbWluIGhlaWdodCB0b1xyXG5cdFx0XHR9LFxyXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcclxuXHRcdFx0bW9kdWxlID0ge307XHJcblx0XHRcclxuXHRcdC8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXHRcdFxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEZpeCBmb3IgcHJvYmxlbSB0aGF0IGJveCBvdmVybGFwcyBjb250ZW50IGxpa2UgY3Jvc3Mgc2VsbGluZyBwcm9kdWN0cyBpZiBwcm9kdWN0IGNvbnRlbnQgaXMgdG9vIHNob3J0XHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0dmFyIF9zZXRQcm9kdWN0SW5mb0NvbnRlbnRNaW5IZWlnaHQgPSBmdW5jdGlvbigpIHtcclxuXHRcdFx0dmFyIG1pbkhlaWdodCA9ICR0aGlzLm91dGVySGVpZ2h0KCkgKyBwYXJzZUZsb2F0KCR0aGlzLmNzcygndG9wJykpO1xyXG5cdFx0XHQkKG9wdGlvbnMucHJvZHVjdEluZm9Db250ZW50KS5jc3MoJ21pbi1oZWlnaHQnLCBtaW5IZWlnaHQgKyAncHgnKTtcclxuXHRcdH07XHJcblx0XHRcclxuXHRcdC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxyXG5cdFx0ICogQGNvbnN0cnVjdG9yXHJcblx0XHQgKi9cclxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0XHRfc2V0UHJvZHVjdEluZm9Db250ZW50TWluSGVpZ2h0KCk7XHJcblx0XHRcdFxyXG5cdFx0XHQkd2luZG93Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5TVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UoKSwgX3NldFByb2R1Y3RJbmZvQ29udGVudE1pbkhlaWdodCk7XHJcblx0XHRcdFxyXG5cdFx0XHRkb25lKCk7XHJcblx0XHR9O1xyXG5cdFx0XHJcblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXHJcblx0XHRyZXR1cm4gbW9kdWxlO1xyXG5cdH0pO1xyXG4iXX0=
