'use strict';

/* --------------------------------------------------------------
 datatable_fixed_header.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Fixed DataTable Header
 *
 * The table header will remain in the viewport as the user scrolls down the page. The style change of this
 * module is a bit tricky because we need to remove the thead from the normal flow, something that breaks the
 * display of the table. Therefore a helper clone of the thead is used to maintain the table formatting.
 *
 * **Notice #1**: The .table-fixed-header class is styled by the _tables.scss and is part of this solution.
 *
 * **Notice #2**: This method will take into concern the .content-header element which shouldn't overlap the
 * table header.
 *
 * @module Admin/Extensions/datatable_fixed_header
 */
gx.extensions.module('datatable_fixed_header', [], function (data) {

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
  * Table Header Selector
  *
  * @type {jQuery}
  */
	var $thead = $this.children('thead');

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	/**
  * Marks the end of the table.
  *
  * This value is used to stop the fixed header when the user reaches the end of the table.
  *
  * @type {Number}
  */
	var tableOffsetBottom = void 0;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On DataTable Draw Event
  *
  * Re-calculate the table bottom offset value.
  */
	function _onDataTableDraw() {
		tableOffsetBottom = $this.offset().top + $this.height() - $thead.height();
	}

	/**
  * On DataTable Initialization
  *
  * Modify the table HTML and set the required event handling for the fixed header functionality.
  */
	function _onDataTableInit() {
		var $mainHeader = $('#main-header');
		var $contentHeader = $('.content-header');
		var $clone = $thead.clone();
		var originalTop = $thead.offset().top;
		var isFixed = false;
		var rollingAnimationInterval = null;

		$clone.hide().addClass('table-fixed-header-helper').prependTo($this);

		$(window).on('scroll', function () {
			var scrollTop = $(window).scrollTop();

			if (!isFixed && scrollTop + $mainHeader.outerHeight() > originalTop) {
				$this.addClass('table-fixed-header');
				$thead.outerWidth($this.outerWidth()).addClass('fixed');
				$clone.show();
				isFixed = true;
			} else if (isFixed && scrollTop + $mainHeader.outerHeight() < originalTop) {
				$this.removeClass('table-fixed-header');
				$thead.outerWidth('').removeClass('fixed');
				$clone.hide();
				isFixed = false;
			}

			if (scrollTop >= tableOffsetBottom) {
				$thead.removeClass('fixed');
			} else if ($(window).scrollTop() < tableOffsetBottom && !$thead.hasClass('fixed')) {
				$thead.addClass('fixed');
			}
		}).on('content_header:roll_in', function () {
			rollingAnimationInterval = setInterval(function () {
				$thead.css('top', $contentHeader.position().top + $contentHeader.outerHeight());
				if ($contentHeader.hasClass('fixed')) {
					clearInterval(rollingAnimationInterval);
				}
			}, 1);
		}).on('content_header:roll_out', function () {
			clearInterval(rollingAnimationInterval);
			$thead.css('top', $mainHeader.outerHeight());
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('draw.dt', _onDataTableDraw).on('init.dt', _onDataTableInit);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9maXhlZF9oZWFkZXIuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0aGVhZCIsImNoaWxkcmVuIiwidGFibGVPZmZzZXRCb3R0b20iLCJfb25EYXRhVGFibGVEcmF3Iiwib2Zmc2V0IiwidG9wIiwiaGVpZ2h0IiwiX29uRGF0YVRhYmxlSW5pdCIsIiRtYWluSGVhZGVyIiwiJGNvbnRlbnRIZWFkZXIiLCIkY2xvbmUiLCJjbG9uZSIsIm9yaWdpbmFsVG9wIiwiaXNGaXhlZCIsInJvbGxpbmdBbmltYXRpb25JbnRlcnZhbCIsImhpZGUiLCJhZGRDbGFzcyIsInByZXBlbmRUbyIsIndpbmRvdyIsIm9uIiwic2Nyb2xsVG9wIiwib3V0ZXJIZWlnaHQiLCJvdXRlcldpZHRoIiwic2hvdyIsInJlbW92ZUNsYXNzIiwiaGFzQ2xhc3MiLCJzZXRJbnRlcnZhbCIsImNzcyIsInBvc2l0aW9uIiwiY2xlYXJJbnRlcnZhbCIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7O0FBY0FBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUFxQix3QkFBckIsRUFBK0MsRUFBL0MsRUFBbUQsVUFBU0MsSUFBVCxFQUFlOztBQUVqRTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1DLFNBQVNGLE1BQU1HLFFBQU4sQ0FBZSxPQUFmLENBQWY7O0FBRUE7Ozs7O0FBS0EsS0FBTUwsU0FBUyxFQUFmOztBQUVBOzs7Ozs7O0FBT0EsS0FBSU0sMEJBQUo7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNDLGdCQUFULEdBQTRCO0FBQzNCRCxzQkFBb0JKLE1BQU1NLE1BQU4sR0FBZUMsR0FBZixHQUFxQlAsTUFBTVEsTUFBTixFQUFyQixHQUFzQ04sT0FBT00sTUFBUCxFQUExRDtBQUNBOztBQUVEOzs7OztBQUtBLFVBQVNDLGdCQUFULEdBQTRCO0FBQzNCLE1BQU1DLGNBQWNULEVBQUUsY0FBRixDQUFwQjtBQUNBLE1BQU1VLGlCQUFpQlYsRUFBRSxpQkFBRixDQUF2QjtBQUNBLE1BQU1XLFNBQVNWLE9BQU9XLEtBQVAsRUFBZjtBQUNBLE1BQU1DLGNBQWNaLE9BQU9JLE1BQVAsR0FBZ0JDLEdBQXBDO0FBQ0EsTUFBSVEsVUFBVSxLQUFkO0FBQ0EsTUFBSUMsMkJBQTJCLElBQS9COztBQUVBSixTQUNFSyxJQURGLEdBRUVDLFFBRkYsQ0FFVywyQkFGWCxFQUdFQyxTQUhGLENBR1luQixLQUhaOztBQUtBQyxJQUFFbUIsTUFBRixFQUNFQyxFQURGLENBQ0ssUUFETCxFQUNlLFlBQVc7QUFDeEIsT0FBTUMsWUFBWXJCLEVBQUVtQixNQUFGLEVBQVVFLFNBQVYsRUFBbEI7O0FBRUEsT0FBSSxDQUFDUCxPQUFELElBQVlPLFlBQVlaLFlBQVlhLFdBQVosRUFBWixHQUF3Q1QsV0FBeEQsRUFBcUU7QUFDcEVkLFVBQU1rQixRQUFOLENBQWUsb0JBQWY7QUFDQWhCLFdBQ0VzQixVQURGLENBQ2F4QixNQUFNd0IsVUFBTixFQURiLEVBRUVOLFFBRkYsQ0FFVyxPQUZYO0FBR0FOLFdBQU9hLElBQVA7QUFDQVYsY0FBVSxJQUFWO0FBQ0EsSUFQRCxNQU9PLElBQUlBLFdBQVdPLFlBQVlaLFlBQVlhLFdBQVosRUFBWixHQUF3Q1QsV0FBdkQsRUFBb0U7QUFDMUVkLFVBQU0wQixXQUFOLENBQWtCLG9CQUFsQjtBQUNBeEIsV0FDRXNCLFVBREYsQ0FDYSxFQURiLEVBRUVFLFdBRkYsQ0FFYyxPQUZkO0FBR0FkLFdBQU9LLElBQVA7QUFDQUYsY0FBVSxLQUFWO0FBQ0E7O0FBRUQsT0FBSU8sYUFBYWxCLGlCQUFqQixFQUFvQztBQUNuQ0YsV0FBT3dCLFdBQVAsQ0FBbUIsT0FBbkI7QUFDQSxJQUZELE1BRU8sSUFBSXpCLEVBQUVtQixNQUFGLEVBQVVFLFNBQVYsS0FBd0JsQixpQkFBeEIsSUFBNkMsQ0FBQ0YsT0FBT3lCLFFBQVAsQ0FBZ0IsT0FBaEIsQ0FBbEQsRUFBNEU7QUFDbEZ6QixXQUFPZ0IsUUFBUCxDQUFnQixPQUFoQjtBQUNBO0FBQ0QsR0F6QkYsRUEwQkVHLEVBMUJGLENBMEJLLHdCQTFCTCxFQTBCK0IsWUFBVztBQUN4Q0wsOEJBQTJCWSxZQUFZLFlBQU07QUFDNUMxQixXQUFPMkIsR0FBUCxDQUFXLEtBQVgsRUFBa0JsQixlQUFlbUIsUUFBZixHQUEwQnZCLEdBQTFCLEdBQWdDSSxlQUFlWSxXQUFmLEVBQWxEO0FBQ0EsUUFBSVosZUFBZWdCLFFBQWYsQ0FBd0IsT0FBeEIsQ0FBSixFQUFzQztBQUNyQ0ksbUJBQWNmLHdCQUFkO0FBQ0E7QUFDRCxJQUwwQixFQUt4QixDQUx3QixDQUEzQjtBQU1BLEdBakNGLEVBa0NFSyxFQWxDRixDQWtDSyx5QkFsQ0wsRUFrQ2dDLFlBQVc7QUFDekNVLGlCQUFjZix3QkFBZDtBQUNBZCxVQUFPMkIsR0FBUCxDQUFXLEtBQVgsRUFBa0JuQixZQUFZYSxXQUFaLEVBQWxCO0FBQ0EsR0FyQ0Y7QUFzQ0E7O0FBR0Q7QUFDQTtBQUNBOztBQUVBekIsUUFBT2tDLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJqQyxRQUNFcUIsRUFERixDQUNLLFNBREwsRUFDZ0JoQixnQkFEaEIsRUFFRWdCLEVBRkYsQ0FFSyxTQUZMLEVBRWdCWixnQkFGaEI7O0FBSUF3QjtBQUNBLEVBTkQ7O0FBUUEsUUFBT25DLE1BQVA7QUFFQSxDQTVIRCIsImZpbGUiOiJkYXRhdGFibGVfZml4ZWRfaGVhZGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGRhdGF0YWJsZV9maXhlZF9oZWFkZXIuanMgMjAxNi0wNy0xM1xyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiAjIyBFbmFibGUgRml4ZWQgRGF0YVRhYmxlIEhlYWRlclxyXG4gKlxyXG4gKiBUaGUgdGFibGUgaGVhZGVyIHdpbGwgcmVtYWluIGluIHRoZSB2aWV3cG9ydCBhcyB0aGUgdXNlciBzY3JvbGxzIGRvd24gdGhlIHBhZ2UuIFRoZSBzdHlsZSBjaGFuZ2Ugb2YgdGhpc1xyXG4gKiBtb2R1bGUgaXMgYSBiaXQgdHJpY2t5IGJlY2F1c2Ugd2UgbmVlZCB0byByZW1vdmUgdGhlIHRoZWFkIGZyb20gdGhlIG5vcm1hbCBmbG93LCBzb21ldGhpbmcgdGhhdCBicmVha3MgdGhlXHJcbiAqIGRpc3BsYXkgb2YgdGhlIHRhYmxlLiBUaGVyZWZvcmUgYSBoZWxwZXIgY2xvbmUgb2YgdGhlIHRoZWFkIGlzIHVzZWQgdG8gbWFpbnRhaW4gdGhlIHRhYmxlIGZvcm1hdHRpbmcuXHJcbiAqXHJcbiAqICoqTm90aWNlICMxKio6IFRoZSAudGFibGUtZml4ZWQtaGVhZGVyIGNsYXNzIGlzIHN0eWxlZCBieSB0aGUgX3RhYmxlcy5zY3NzIGFuZCBpcyBwYXJ0IG9mIHRoaXMgc29sdXRpb24uXHJcbiAqXHJcbiAqICoqTm90aWNlICMyKio6IFRoaXMgbWV0aG9kIHdpbGwgdGFrZSBpbnRvIGNvbmNlcm4gdGhlIC5jb250ZW50LWhlYWRlciBlbGVtZW50IHdoaWNoIHNob3VsZG4ndCBvdmVybGFwIHRoZVxyXG4gKiB0YWJsZSBoZWFkZXIuXHJcbiAqXHJcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy9kYXRhdGFibGVfZml4ZWRfaGVhZGVyXHJcbiAqL1xyXG5neC5leHRlbnNpb25zLm1vZHVsZSgnZGF0YXRhYmxlX2ZpeGVkX2hlYWRlcicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogVGFibGUgSGVhZGVyIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGVhZCA9ICR0aGlzLmNoaWxkcmVuKCd0aGVhZCcpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNYXJrcyB0aGUgZW5kIG9mIHRoZSB0YWJsZS5cclxuXHQgKlxyXG5cdCAqIFRoaXMgdmFsdWUgaXMgdXNlZCB0byBzdG9wIHRoZSBmaXhlZCBoZWFkZXIgd2hlbiB0aGUgdXNlciByZWFjaGVzIHRoZSBlbmQgb2YgdGhlIHRhYmxlLlxyXG5cdCAqXHJcblx0ICogQHR5cGUge051bWJlcn1cclxuXHQgKi9cclxuXHRsZXQgdGFibGVPZmZzZXRCb3R0b207XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gRGF0YVRhYmxlIERyYXcgRXZlbnRcclxuXHQgKlxyXG5cdCAqIFJlLWNhbGN1bGF0ZSB0aGUgdGFibGUgYm90dG9tIG9mZnNldCB2YWx1ZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25EYXRhVGFibGVEcmF3KCkge1xyXG5cdFx0dGFibGVPZmZzZXRCb3R0b20gPSAkdGhpcy5vZmZzZXQoKS50b3AgKyAkdGhpcy5oZWlnaHQoKSAtICR0aGVhZC5oZWlnaHQoKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gRGF0YVRhYmxlIEluaXRpYWxpemF0aW9uXHJcblx0ICpcclxuXHQgKiBNb2RpZnkgdGhlIHRhYmxlIEhUTUwgYW5kIHNldCB0aGUgcmVxdWlyZWQgZXZlbnQgaGFuZGxpbmcgZm9yIHRoZSBmaXhlZCBoZWFkZXIgZnVuY3Rpb25hbGl0eS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25EYXRhVGFibGVJbml0KCkge1xyXG5cdFx0Y29uc3QgJG1haW5IZWFkZXIgPSAkKCcjbWFpbi1oZWFkZXInKTtcclxuXHRcdGNvbnN0ICRjb250ZW50SGVhZGVyID0gJCgnLmNvbnRlbnQtaGVhZGVyJyk7XHJcblx0XHRjb25zdCAkY2xvbmUgPSAkdGhlYWQuY2xvbmUoKTtcclxuXHRcdGNvbnN0IG9yaWdpbmFsVG9wID0gJHRoZWFkLm9mZnNldCgpLnRvcDtcclxuXHRcdGxldCBpc0ZpeGVkID0gZmFsc2U7XHJcblx0XHRsZXQgcm9sbGluZ0FuaW1hdGlvbkludGVydmFsID0gbnVsbDtcclxuXHRcdFxyXG5cdFx0JGNsb25lXHJcblx0XHRcdC5oaWRlKClcclxuXHRcdFx0LmFkZENsYXNzKCd0YWJsZS1maXhlZC1oZWFkZXItaGVscGVyJylcclxuXHRcdFx0LnByZXBlbmRUbygkdGhpcyk7XHJcblx0XHRcclxuXHRcdCQod2luZG93KVxyXG5cdFx0XHQub24oJ3Njcm9sbCcsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdGNvbnN0IHNjcm9sbFRvcCA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRpZiAoIWlzRml4ZWQgJiYgc2Nyb2xsVG9wICsgJG1haW5IZWFkZXIub3V0ZXJIZWlnaHQoKSA+IG9yaWdpbmFsVG9wKSB7XHJcblx0XHRcdFx0XHQkdGhpcy5hZGRDbGFzcygndGFibGUtZml4ZWQtaGVhZGVyJyk7XHJcblx0XHRcdFx0XHQkdGhlYWRcclxuXHRcdFx0XHRcdFx0Lm91dGVyV2lkdGgoJHRoaXMub3V0ZXJXaWR0aCgpKVxyXG5cdFx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHRcdFx0XHQkY2xvbmUuc2hvdygpO1xyXG5cdFx0XHRcdFx0aXNGaXhlZCA9IHRydWU7XHJcblx0XHRcdFx0fSBlbHNlIGlmIChpc0ZpeGVkICYmIHNjcm9sbFRvcCArICRtYWluSGVhZGVyLm91dGVySGVpZ2h0KCkgPCBvcmlnaW5hbFRvcCkge1xyXG5cdFx0XHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ3RhYmxlLWZpeGVkLWhlYWRlcicpO1xyXG5cdFx0XHRcdFx0JHRoZWFkXHJcblx0XHRcdFx0XHRcdC5vdXRlcldpZHRoKCcnKVxyXG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHRcdFx0XHQkY2xvbmUuaGlkZSgpO1xyXG5cdFx0XHRcdFx0aXNGaXhlZCA9IGZhbHNlO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRcclxuXHRcdFx0XHRpZiAoc2Nyb2xsVG9wID49IHRhYmxlT2Zmc2V0Qm90dG9tKSB7XHJcblx0XHRcdFx0XHQkdGhlYWQucmVtb3ZlQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHRcdFx0fSBlbHNlIGlmICgkKHdpbmRvdykuc2Nyb2xsVG9wKCkgPCB0YWJsZU9mZnNldEJvdHRvbSAmJiAhJHRoZWFkLmhhc0NsYXNzKCdmaXhlZCcpKSB7XHJcblx0XHRcdFx0XHQkdGhlYWQuYWRkQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9KVxyXG5cdFx0XHQub24oJ2NvbnRlbnRfaGVhZGVyOnJvbGxfaW4nLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRyb2xsaW5nQW5pbWF0aW9uSW50ZXJ2YWwgPSBzZXRJbnRlcnZhbCgoKSA9PiB7XHJcblx0XHRcdFx0XHQkdGhlYWQuY3NzKCd0b3AnLCAkY29udGVudEhlYWRlci5wb3NpdGlvbigpLnRvcCArICRjb250ZW50SGVhZGVyLm91dGVySGVpZ2h0KCkpO1xyXG5cdFx0XHRcdFx0aWYgKCRjb250ZW50SGVhZGVyLmhhc0NsYXNzKCdmaXhlZCcpKSB7XHJcblx0XHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwocm9sbGluZ0FuaW1hdGlvbkludGVydmFsKTtcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9LCAxKTtcclxuXHRcdFx0fSlcclxuXHRcdFx0Lm9uKCdjb250ZW50X2hlYWRlcjpyb2xsX291dCcsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdGNsZWFySW50ZXJ2YWwocm9sbGluZ0FuaW1hdGlvbkludGVydmFsKTtcclxuXHRcdFx0XHQkdGhlYWQuY3NzKCd0b3AnLCAkbWFpbkhlYWRlci5vdXRlckhlaWdodCgpKTtcclxuXHRcdFx0fSk7XHJcblx0fVxyXG5cdFxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkdGhpc1xyXG5cdFx0XHQub24oJ2RyYXcuZHQnLCBfb25EYXRhVGFibGVEcmF3KVxyXG5cdFx0XHQub24oJ2luaXQuZHQnLCBfb25EYXRhVGFibGVJbml0KTtcclxuXHRcdFxyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxuXHRcclxufSk7Il19
