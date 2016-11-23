'use strict';

/* --------------------------------------------------------------
 cookie_bar.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Cookie-Bar on click or on page change
 */
gambio.widgets.module('cookie_bar', ['xhr'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		closeBtn: '.close-button',
		url: 'shop.php?do=CookieBar'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {},
	    expiry = new Date();

	// ########## EVENT HANDLER ##########


	/**
  * Shows the Cookie-Bar
  * 
  * @private
  */
	var _showCookieBar = function _showCookieBar() {
		$this.css('display', 'table');
	};

	/**
  * Hides the Cookie-Bar, if the hiding cookie is set or if a link or button to close the Cookie-Bar is clicked
  * 
  * @private
  */
	var _hideCookieBar = function _hideCookieBar() {
		$this.hide();
	};

	/**
  * Sets the hiding cookie
  *
  * @private
  */
	var _setCookie = function _setCookie() {
		jse.libs.xhr.get({
			url: options.url
		}, true).done(function () {
			_hideCookieBar();
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		$(options.closeBtn).on('click', _setCookie);

		if (window.localStorage !== undefined) {
			if (localStorage.getItem('cookieBarSeen') === '1') {
				_setCookie();
			} else {
				localStorage.setItem('cookieBarSeen', '1');
				_showCookieBar();
			}
		} else {
			_showCookieBar();
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY29va2llX2Jhci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiY2xvc2VCdG4iLCJ1cmwiLCJvcHRpb25zIiwiZXh0ZW5kIiwiZXhwaXJ5IiwiRGF0ZSIsIl9zaG93Q29va2llQmFyIiwiY3NzIiwiX2hpZGVDb29raWVCYXIiLCJoaWRlIiwiX3NldENvb2tpZSIsImpzZSIsImxpYnMiLCJ4aHIiLCJnZXQiLCJkb25lIiwiaW5pdCIsIm9uIiwid2luZG93IiwibG9jYWxTdG9yYWdlIiwidW5kZWZpbmVkIiwiZ2V0SXRlbSIsInNldEl0ZW0iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFlBREQsRUFHQyxDQUFDLEtBQUQsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVc7QUFDVkMsWUFBVyxlQUREO0FBRVZDLE9BQUs7QUFGSyxFQURaO0FBQUEsS0FLQ0MsVUFBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkgsSUFBN0IsQ0FMWDtBQUFBLEtBTUNELFNBQVMsRUFOVjtBQUFBLEtBT0NTLFNBQVMsSUFBSUMsSUFBSixFQVBWOztBQVVBOzs7QUFHQTs7Ozs7QUFLQSxLQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0JULFFBQU1VLEdBQU4sQ0FBVSxTQUFWLEVBQXFCLE9BQXJCO0FBQ0EsRUFGRDs7QUFLQTs7Ozs7QUFLQSxLQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0JYLFFBQU1ZLElBQU47QUFDQSxFQUZEOztBQUtBOzs7OztBQUtBLEtBQUlDLGFBQWEsU0FBYkEsVUFBYSxHQUFXO0FBQzNCQyxNQUFJQyxJQUFKLENBQVNDLEdBQVQsQ0FBYUMsR0FBYixDQUFpQjtBQUNoQmIsUUFBS0MsUUFBUUQ7QUFERyxHQUFqQixFQUVHLElBRkgsRUFFU2MsSUFGVCxDQUVjLFlBQVc7QUFDeEJQO0FBQ0EsR0FKRDtBQUtBLEVBTkQ7O0FBUUE7O0FBRUE7Ozs7QUFJQWIsUUFBT3FCLElBQVAsR0FBYyxVQUFTRCxJQUFULEVBQWU7QUFDNUJqQixJQUFFSSxRQUFRRixRQUFWLEVBQW9CaUIsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0NQLFVBQWhDOztBQUVBLE1BQUlRLE9BQU9DLFlBQVAsS0FBd0JDLFNBQTVCLEVBQXVDO0FBQ3RDLE9BQUlELGFBQWFFLE9BQWIsQ0FBcUIsZUFBckIsTUFBMEMsR0FBOUMsRUFBbUQ7QUFDbERYO0FBQ0EsSUFGRCxNQUVPO0FBQ05TLGlCQUFhRyxPQUFiLENBQXFCLGVBQXJCLEVBQXNDLEdBQXRDO0FBQ0FoQjtBQUNBO0FBQ0QsR0FQRCxNQU9PO0FBQ05BO0FBQ0E7O0FBRURTO0FBQ0EsRUFmRDs7QUFpQkE7QUFDQSxRQUFPcEIsTUFBUDtBQUNBLENBbEZGIiwiZmlsZSI6IndpZGdldHMvY29va2llX2Jhci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29va2llX2Jhci5qcyAyMDE2LTA2LTE1XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBVc2VkIGZvciBoaWRpbmcgdGhlIENvb2tpZS1CYXIgb24gY2xpY2sgb3Igb24gcGFnZSBjaGFuZ2VcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnY29va2llX2JhcicsXG5cdFxuXHRbJ3hociddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XHRcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGNsb3NlQnRuIDogJy5jbG9zZS1idXR0b24nLFxuXHRcdFx0XHR1cmw6ICdzaG9wLnBocD9kbz1Db29raWVCYXInXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fSxcblx0XHRcdGV4cGlyeSA9IG5ldyBEYXRlKCk7XG5cdFx0XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblx0XHRcblx0XHRcblx0XHQvKipcblx0XHQgKiBTaG93cyB0aGUgQ29va2llLUJhclxuXHRcdCAqIFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zaG93Q29va2llQmFyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkdGhpcy5jc3MoJ2Rpc3BsYXknLCAndGFibGUnKTtcblx0XHR9O1xuXHRcdFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhpZGVzIHRoZSBDb29raWUtQmFyLCBpZiB0aGUgaGlkaW5nIGNvb2tpZSBpcyBzZXQgb3IgaWYgYSBsaW5rIG9yIGJ1dHRvbiB0byBjbG9zZSB0aGUgQ29va2llLUJhciBpcyBjbGlja2VkXG5cdFx0ICogXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2hpZGVDb29raWVCYXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLmhpZGUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFNldHMgdGhlIGhpZGluZyBjb29raWVcblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zZXRDb29raWUgPSBmdW5jdGlvbigpIHtcblx0XHRcdGpzZS5saWJzLnhoci5nZXQoe1xuXHRcdFx0XHR1cmw6IG9wdGlvbnMudXJsXG5cdFx0XHR9LCB0cnVlKS5kb25lKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfaGlkZUNvb2tpZUJhcigpO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCQob3B0aW9ucy5jbG9zZUJ0bikub24oJ2NsaWNrJywgX3NldENvb2tpZSk7XG5cdFx0XHRcblx0XHRcdGlmICh3aW5kb3cubG9jYWxTdG9yYWdlICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0aWYgKGxvY2FsU3RvcmFnZS5nZXRJdGVtKCdjb29raWVCYXJTZWVuJykgPT09ICcxJykge1xuXHRcdFx0XHRcdF9zZXRDb29raWUoKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRsb2NhbFN0b3JhZ2Uuc2V0SXRlbSgnY29va2llQmFyU2VlbicsICcxJyk7XG5cdFx0XHRcdFx0X3Nob3dDb29raWVCYXIoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0X3Nob3dDb29raWVCYXIoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pOyJdfQ==
