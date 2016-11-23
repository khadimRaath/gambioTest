'use strict';

/* --------------------------------------------------------------
 gift.js 2016-02-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('gift', ['xhr', 'form'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    url = null,
	    defaults = {},
	    selectorMapping = {
		giftContent: '.gift-cart-content-wrapper'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	var _submitHandler = function _submitHandler(e) {
		e.preventDefault();
		e.stopPropagation();

		var dataset = jse.libs.form.getData($this);

		jse.libs.xhr.ajax({ url: url, data: dataset }, true).done(function (result) {
			jse.libs.template.helpers.fill(result.content, $this, selectorMapping);
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		url = $this.attr('action');

		$this.on('submit', _submitHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZ2lmdC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsInVybCIsImRlZmF1bHRzIiwic2VsZWN0b3JNYXBwaW5nIiwiZ2lmdENvbnRlbnQiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3N1Ym1pdEhhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJkYXRhc2V0IiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwieGhyIiwiYWpheCIsImRvbmUiLCJyZXN1bHQiLCJ0ZW1wbGF0ZSIsImhlbHBlcnMiLCJmaWxsIiwiY29udGVudCIsImluaXQiLCJhdHRyIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsTUFERCxFQUdDLENBQUMsS0FBRCxFQUFRLE1BQVIsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLE1BQU0sSUFEUDtBQUFBLEtBRUNDLFdBQVcsRUFGWjtBQUFBLEtBR0NDLGtCQUFrQjtBQUNqQkMsZUFBYTtBQURJLEVBSG5CO0FBQUEsS0FNQ0MsVUFBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkosSUFBN0IsQ0FOWDtBQUFBLEtBT0NELFNBQVMsRUFQVjs7QUFVQSxLQUFJVSxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVNDLENBQVQsRUFBWTtBQUNoQ0EsSUFBRUMsY0FBRjtBQUNBRCxJQUFFRSxlQUFGOztBQUVBLE1BQUlDLFVBQVVDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxPQUFkLENBQXNCaEIsS0FBdEIsQ0FBZDs7QUFFQWEsTUFBSUMsSUFBSixDQUFTRyxHQUFULENBQWFDLElBQWIsQ0FBa0IsRUFBQ2hCLEtBQUtBLEdBQU4sRUFBV0gsTUFBTWEsT0FBakIsRUFBbEIsRUFBNkMsSUFBN0MsRUFBbURPLElBQW5ELENBQXdELFVBQVNDLE1BQVQsRUFBaUI7QUFDeEVQLE9BQUlDLElBQUosQ0FBU08sUUFBVCxDQUFrQkMsT0FBbEIsQ0FBMEJDLElBQTFCLENBQStCSCxPQUFPSSxPQUF0QyxFQUErQ3hCLEtBQS9DLEVBQXNESSxlQUF0RDtBQUNBLEdBRkQ7QUFJQSxFQVZEOztBQWFGOztBQUVFOzs7O0FBSUFOLFFBQU8yQixJQUFQLEdBQWMsVUFBU04sSUFBVCxFQUFlOztBQUU1QmpCLFFBQU1GLE1BQU0wQixJQUFOLENBQVcsUUFBWCxDQUFOOztBQUVBMUIsUUFBTTJCLEVBQU4sQ0FBUyxRQUFULEVBQW1CbkIsY0FBbkI7O0FBRUFXO0FBQ0EsRUFQRDs7QUFTQTtBQUNBLFFBQU9yQixNQUFQO0FBQ0EsQ0FuREYiLCJmaWxlIjoid2lkZ2V0cy9naWZ0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBnaWZ0LmpzIDIwMTYtMDItMTVcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdnaWZ0JyxcblxuXHRbJ3hocicsICdmb3JtJ10sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHR1cmwgPSBudWxsLFxuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdHNlbGVjdG9yTWFwcGluZyA9IHtcblx0XHRcdFx0Z2lmdENvbnRlbnQ6ICcuZ2lmdC1jYXJ0LWNvbnRlbnQtd3JhcHBlcidcblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cblx0XHR2YXIgX3N1Ym1pdEhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHR2YXIgZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcyk7XG5cblx0XHRcdGpzZS5saWJzLnhoci5hamF4KHt1cmw6IHVybCwgZGF0YTogZGF0YXNldH0sIHRydWUpLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdGpzZS5saWJzLnRlbXBsYXRlLmhlbHBlcnMuZmlsbChyZXN1bHQuY29udGVudCwgJHRoaXMsIHNlbGVjdG9yTWFwcGluZyk7XG5cdFx0XHR9KTtcblxuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdFx0dXJsID0gJHRoaXMuYXR0cignYWN0aW9uJyk7XG5cblx0XHRcdCR0aGlzLm9uKCdzdWJtaXQnLCBfc3VibWl0SGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pOyJdfQ==
