'use strict';

/* --------------------------------------------------------------
 share_cart.js 2016-04-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('share_cart', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {
		model: {
			lang: jse.core.config.get('appUrl') + '/shop.php?do=JsTranslations&section=shared_shopping_cart'
		}
	};

	var _copyHandler = function _copyHandler() {
		var sharedCartUrl = document.querySelector('.shared_cart_url'),
		    copySupported = document.queryCommandSupported('copy'),
		    $cartResponseWrapper = $('.share-cart-response-wrapper'),
		    error = false,
		    commandSuccessful,
		    txt;

		sharedCartUrl.select();
		try {
			commandSuccessful = document.execCommand('copy');
		} catch (err) {
			jse.core.debug.log('Error occurred when copying!');
			error = true;
		}

		txt = !commandSuccessful || !copySupported || error ? module.model.lang.text_warning : module.model.lang.text_notice;

		$cartResponseWrapper.find('p').first().text(txt);
		$cartResponseWrapper.show();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$this.on('click', _copyHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hhcmVfY2FydC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIm1vZGVsIiwibGFuZyIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJfY29weUhhbmRsZXIiLCJzaGFyZWRDYXJ0VXJsIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiY29weVN1cHBvcnRlZCIsInF1ZXJ5Q29tbWFuZFN1cHBvcnRlZCIsIiRjYXJ0UmVzcG9uc2VXcmFwcGVyIiwiZXJyb3IiLCJjb21tYW5kU3VjY2Vzc2Z1bCIsInR4dCIsInNlbGVjdCIsImV4ZWNDb21tYW5kIiwiZXJyIiwiZGVidWciLCJsb2ciLCJ0ZXh0X3dhcm5pbmciLCJ0ZXh0X25vdGljZSIsImZpbmQiLCJmaXJzdCIsInRleHQiLCJzaG93IiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxZQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFEWjtBQUFBLEtBRUNDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRlg7QUFBQSxLQUdDRCxTQUFTO0FBQ1JPLFNBQU87QUFDTkMsU0FBTUMsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixRQUFwQixJQUFnQztBQURoQztBQURDLEVBSFY7O0FBU0EsS0FBSUMsZUFBZSxTQUFmQSxZQUFlLEdBQVc7QUFDN0IsTUFBSUMsZ0JBQWdCQyxTQUFTQyxhQUFULENBQXVCLGtCQUF2QixDQUFwQjtBQUFBLE1BQ0NDLGdCQUFnQkYsU0FBU0cscUJBQVQsQ0FBK0IsTUFBL0IsQ0FEakI7QUFBQSxNQUVDQyx1QkFBdUJoQixFQUFFLDhCQUFGLENBRnhCO0FBQUEsTUFHQ2lCLFFBQVEsS0FIVDtBQUFBLE1BSUNDLGlCQUpEO0FBQUEsTUFJb0JDLEdBSnBCOztBQU1BUixnQkFBY1MsTUFBZDtBQUNBLE1BQUk7QUFDSEYsdUJBQW9CTixTQUFTUyxXQUFULENBQXFCLE1BQXJCLENBQXBCO0FBQ0EsR0FGRCxDQUVFLE9BQU9DLEdBQVAsRUFBWTtBQUNiaEIsT0FBSUMsSUFBSixDQUFTZ0IsS0FBVCxDQUFlQyxHQUFmLENBQW1CLDhCQUFuQjtBQUNBUCxXQUFRLElBQVI7QUFDQTs7QUFFREUsUUFBTyxDQUFDRCxpQkFBRCxJQUFzQixDQUFDSixhQUF2QixJQUNKRyxLQURHLEdBQ01wQixPQUFPTyxLQUFQLENBQWFDLElBQWIsQ0FBa0JvQixZQUR4QixHQUN1QzVCLE9BQU9PLEtBQVAsQ0FBYUMsSUFBYixDQUFrQnFCLFdBRC9EOztBQUdBVix1QkFBcUJXLElBQXJCLENBQTBCLEdBQTFCLEVBQStCQyxLQUEvQixHQUF1Q0MsSUFBdkMsQ0FBNENWLEdBQTVDO0FBQ0FILHVCQUFxQmMsSUFBckI7QUFDQSxFQXBCRDs7QUF1QkE7O0FBRUE7Ozs7QUFJQWpDLFFBQU9rQyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCakMsUUFBTWtDLEVBQU4sQ0FBUyxPQUFULEVBQWtCdkIsWUFBbEI7O0FBRUFzQjtBQUNBLEVBSkQ7O0FBTUE7QUFDQSxRQUFPbkMsTUFBUDtBQUNBLENBekRGIiwiZmlsZSI6IndpZGdldHMvc2hhcmVfY2FydC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2hhcmVfY2FydC5qcyAyMDE2LTA0LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnc2hhcmVfY2FydCcsXG5cblx0W10sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG5cdFx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge1xuXHRcdFx0XHRtb2RlbDoge1xuXHRcdFx0XHRcdGxhbmc6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9zaG9wLnBocD9kbz1Kc1RyYW5zbGF0aW9ucyZzZWN0aW9uPXNoYXJlZF9zaG9wcGluZ19jYXJ0J1xuXHRcdFx0XHR9XG5cdFx0XHR9O1xuXG5cdFx0dmFyIF9jb3B5SGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHNoYXJlZENhcnRVcmwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuc2hhcmVkX2NhcnRfdXJsJyksXG5cdFx0XHRcdGNvcHlTdXBwb3J0ZWQgPSBkb2N1bWVudC5xdWVyeUNvbW1hbmRTdXBwb3J0ZWQoJ2NvcHknKSxcblx0XHRcdFx0JGNhcnRSZXNwb25zZVdyYXBwZXIgPSAkKCcuc2hhcmUtY2FydC1yZXNwb25zZS13cmFwcGVyJyksXG5cdFx0XHRcdGVycm9yID0gZmFsc2UsXG5cdFx0XHRcdGNvbW1hbmRTdWNjZXNzZnVsLCB0eHQ7XG5cblx0XHRcdHNoYXJlZENhcnRVcmwuc2VsZWN0KCk7XG5cdFx0XHR0cnkge1xuXHRcdFx0XHRjb21tYW5kU3VjY2Vzc2Z1bCA9IGRvY3VtZW50LmV4ZWNDb21tYW5kKCdjb3B5Jyk7XG5cdFx0XHR9IGNhdGNoIChlcnIpIHtcblx0XHRcdFx0anNlLmNvcmUuZGVidWcubG9nKCdFcnJvciBvY2N1cnJlZCB3aGVuIGNvcHlpbmchJyk7XG5cdFx0XHRcdGVycm9yID0gdHJ1ZTtcblx0XHRcdH1cblxuXHRcdFx0dHh0ID0gKCFjb21tYW5kU3VjY2Vzc2Z1bCB8fCAhY29weVN1cHBvcnRlZFxuXHRcdFx0fHwgZXJyb3IpID8gbW9kdWxlLm1vZGVsLmxhbmcudGV4dF93YXJuaW5nIDogbW9kdWxlLm1vZGVsLmxhbmcudGV4dF9ub3RpY2U7XG5cblx0XHRcdCRjYXJ0UmVzcG9uc2VXcmFwcGVyLmZpbmQoJ3AnKS5maXJzdCgpLnRleHQodHh0KTtcblx0XHRcdCRjYXJ0UmVzcG9uc2VXcmFwcGVyLnNob3coKTtcblx0XHR9O1xuXG5cblx0XHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX2NvcHlIYW5kbGVyKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7Il19
