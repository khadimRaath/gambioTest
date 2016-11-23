'use strict';

/* --------------------------------------------------------------
	ipayment.js 2016-06-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('ipayment', [], function (data) {
	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$('#ipayment_form').submit();
		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaXBheW1lbnQuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsInN1Ym1pdCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsVUFBdEIsRUFBa0MsRUFBbEMsRUFBc0MsVUFBU0MsSUFBVCxFQUFlO0FBQ3BEOztBQUVBOztBQUNBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVyxFQURaO0FBQUEsS0FHQ0MsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FIWDtBQUFBLEtBSUNELFNBQVMsRUFKVjs7QUFNQTs7QUFFQTs7OztBQUlBQSxRQUFPTyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCTCxJQUFFLGdCQUFGLEVBQW9CTSxNQUFwQjtBQUNBRDtBQUNBLEVBSEQ7O0FBS0E7QUFDQSxRQUFPUixNQUFQO0FBQ0EsQ0F2QkQiLCJmaWxlIjoid2lkZ2V0cy9pcGF5bWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdGlwYXltZW50LmpzIDIwMTYtMDYtMjdcblx0R2FtYmlvIEdtYkhcblx0aHR0cDovL3d3dy5nYW1iaW8uZGVcblx0Q29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG5cdFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuXHRbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cblx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnaXBheW1lbnQnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXHQndXNlIHN0cmljdCc7XG5cblx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0ZGVmYXVsdHMgPSB7XG5cdFx0fSxcblx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRtb2R1bGUgPSB7fTtcblxuXHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHQvKipcblx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdCAqIEBjb25zdHJ1Y3RvclxuXHQgKi9cblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0JCgnI2lwYXltZW50X2Zvcm0nKS5zdWJtaXQoKTtcblx0XHRkb25lKCk7XG5cdH07XG5cblx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRyZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=
