'use strict';

/* --------------------------------------------------------------
 security_page.js 2015-09-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Security Page Controller
 *
 * Changing behavior in the security page.
 * Add readonly-attribute to input elements if captcha_type-dropdown value 'standard' is selected
 *
 * @module Compatibility/security_page
 */
gx.compatibility.module('security_page', [],

/**  @lends module:Compatibility/security_page */

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

	var _disableInputs = function _disableInputs() {
		console.log('change');
		var selectors = ['#GM_RECAPTCHA_PUBLIC_KEY', '#GM_RECAPTCHA_PRIVATE_KEY'];

		var read_only = true;
		if ($('#captcha_type').val() === 'recaptcha') {
			read_only = false;
		}

		$.each(selectors, function () {
			$(this).attr('readonly', read_only);
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_disableInputs();
		$this.on('change', _disableInputs);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNlY3VyaXR5L3NlY3VyaXR5X3BhZ2UuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9kaXNhYmxlSW5wdXRzIiwiY29uc29sZSIsImxvZyIsInNlbGVjdG9ycyIsInJlYWRfb25seSIsInZhbCIsImVhY2giLCJhdHRyIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7OztBQVFBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGVBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSU8saUJBQWlCLFNBQWpCQSxjQUFpQixHQUFXO0FBQy9CQyxVQUFRQyxHQUFSLENBQVksUUFBWjtBQUNBLE1BQUlDLFlBQVksQ0FDZiwwQkFEZSxFQUVmLDJCQUZlLENBQWhCOztBQUtBLE1BQUlDLFlBQVksSUFBaEI7QUFDQSxNQUFJUixFQUFFLGVBQUYsRUFBbUJTLEdBQW5CLE9BQTZCLFdBQWpDLEVBQThDO0FBQzdDRCxlQUFZLEtBQVo7QUFDQTs7QUFFRFIsSUFBRVUsSUFBRixDQUFPSCxTQUFQLEVBQWtCLFlBQVc7QUFDNUJQLEtBQUUsSUFBRixFQUFRVyxJQUFSLENBQWEsVUFBYixFQUF5QkgsU0FBekI7QUFDQSxHQUZEO0FBR0EsRUFmRDs7QUFpQkE7QUFDQTtBQUNBOztBQUVBWCxRQUFPZSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCVDtBQUNBTCxRQUFNZSxFQUFOLENBQVMsUUFBVCxFQUFtQlYsY0FBbkI7QUFDQVM7QUFDQSxFQUpEOztBQU1BLFFBQU9oQixNQUFQO0FBQ0EsQ0E1RUYiLCJmaWxlIjoic2VjdXJpdHkvc2VjdXJpdHlfcGFnZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2VjdXJpdHlfcGFnZS5qcyAyMDE1LTA5LTI4IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBTZWN1cml0eSBQYWdlIENvbnRyb2xsZXJcbiAqXG4gKiBDaGFuZ2luZyBiZWhhdmlvciBpbiB0aGUgc2VjdXJpdHkgcGFnZS5cbiAqIEFkZCByZWFkb25seS1hdHRyaWJ1dGUgdG8gaW5wdXQgZWxlbWVudHMgaWYgY2FwdGNoYV90eXBlLWRyb3Bkb3duIHZhbHVlICdzdGFuZGFyZCcgaXMgc2VsZWN0ZWRcbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvc2VjdXJpdHlfcGFnZVxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J3NlY3VyaXR5X3BhZ2UnLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9zZWN1cml0eV9wYWdlICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9kaXNhYmxlSW5wdXRzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zb2xlLmxvZygnY2hhbmdlJyk7XG5cdFx0XHR2YXIgc2VsZWN0b3JzID0gW1xuXHRcdFx0XHQnI0dNX1JFQ0FQVENIQV9QVUJMSUNfS0VZJyxcblx0XHRcdFx0JyNHTV9SRUNBUFRDSEFfUFJJVkFURV9LRVknXG5cdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHR2YXIgcmVhZF9vbmx5ID0gdHJ1ZTtcblx0XHRcdGlmICgkKCcjY2FwdGNoYV90eXBlJykudmFsKCkgPT09ICdyZWNhcHRjaGEnKSB7XG5cdFx0XHRcdHJlYWRfb25seSA9IGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkLmVhY2goc2VsZWN0b3JzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCh0aGlzKS5hdHRyKCdyZWFkb25seScsIHJlYWRfb25seSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRfZGlzYWJsZUlucHV0cygpO1xuXHRcdFx0JHRoaXMub24oJ2NoYW5nZScsIF9kaXNhYmxlSW5wdXRzKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
