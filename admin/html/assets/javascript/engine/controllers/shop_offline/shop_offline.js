'use strict';

/* --------------------------------------------------------------
 shop_offline.js 2016-06-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * General Controller of Shop
 */
gx.controllers.module('shop_offline', [], function (data) {

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
  * Module Instance 
  * 
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	function _toggleLanguageSelection() {
		var $languagesButtonBar = $('.languages.buttonbar');

		if ($(this).attr('href') === '#status') {
			$languagesButtonBar.css('visibility', 'hidden');
		} else {
			$languagesButtonBar.css('visibility', 'visible');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.find('.tab-headline-wrapper > a').on('click', _toggleLanguageSelection);

		_toggleLanguageSelection.call($('.tab-headline-wrapper a')[0]);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNob3Bfb2ZmbGluZS9zaG9wX29mZmxpbmUuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJfdG9nZ2xlTGFuZ3VhZ2VTZWxlY3Rpb24iLCIkbGFuZ3VhZ2VzQnV0dG9uQmFyIiwiYXR0ciIsImNzcyIsImluaXQiLCJkb25lIiwiZmluZCIsIm9uIiwiY2FsbCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQXNCLGNBQXRCLEVBQXNDLEVBQXRDLEVBQTBDLFVBQVNDLElBQVQsRUFBZTs7QUFFeEQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTLEVBQWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBLFVBQVNJLHdCQUFULEdBQW9DO0FBQ25DLE1BQU1DLHNCQUFzQkYsRUFBRSxzQkFBRixDQUE1Qjs7QUFFQSxNQUFJQSxFQUFFLElBQUYsRUFBUUcsSUFBUixDQUFhLE1BQWIsTUFBeUIsU0FBN0IsRUFBd0M7QUFDdkNELHVCQUFvQkUsR0FBcEIsQ0FBd0IsWUFBeEIsRUFBc0MsUUFBdEM7QUFDQSxHQUZELE1BRU87QUFDTkYsdUJBQW9CRSxHQUFwQixDQUF3QixZQUF4QixFQUFzQyxTQUF0QztBQUNBO0FBQ0Q7O0FBRUQ7QUFDQTtBQUNBOztBQUVBUCxRQUFPUSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCUCxRQUFNUSxJQUFOLENBQVcsMkJBQVgsRUFBd0NDLEVBQXhDLENBQTJDLE9BQTNDLEVBQW9EUCx3QkFBcEQ7O0FBRUFBLDJCQUF5QlEsSUFBekIsQ0FBOEJULEVBQUUseUJBQUYsRUFBNkIsQ0FBN0IsQ0FBOUI7O0FBRUFNO0FBQ0EsRUFORDs7QUFRQSxRQUFPVCxNQUFQO0FBRUEsQ0FsREQiLCJmaWxlIjoic2hvcF9vZmZsaW5lL3Nob3Bfb2ZmbGluZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBzaG9wX29mZmxpbmUuanMgMjAxNi0wNi0yOVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBHZW5lcmFsIENvbnRyb2xsZXIgb2YgU2hvcFxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdzaG9wX29mZmxpbmUnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKiBcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2UgXHJcblx0ICogXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTsgXHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0ZnVuY3Rpb24gX3RvZ2dsZUxhbmd1YWdlU2VsZWN0aW9uKCkge1xyXG5cdFx0Y29uc3QgJGxhbmd1YWdlc0J1dHRvbkJhciA9ICQoJy5sYW5ndWFnZXMuYnV0dG9uYmFyJyk7IFxyXG5cdFx0XHJcblx0XHRpZiAoJCh0aGlzKS5hdHRyKCdocmVmJykgPT09ICcjc3RhdHVzJykge1xyXG5cdFx0XHQkbGFuZ3VhZ2VzQnV0dG9uQmFyLmNzcygndmlzaWJpbGl0eScsICdoaWRkZW4nKTsgXHJcblx0XHR9IGVsc2Uge1xyXG5cdFx0XHQkbGFuZ3VhZ2VzQnV0dG9uQmFyLmNzcygndmlzaWJpbGl0eScsICd2aXNpYmxlJyk7IFxyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMuZmluZCgnLnRhYi1oZWFkbGluZS13cmFwcGVyID4gYScpLm9uKCdjbGljaycsIF90b2dnbGVMYW5ndWFnZVNlbGVjdGlvbik7IFxyXG5cdFx0XHJcblx0XHRfdG9nZ2xlTGFuZ3VhZ2VTZWxlY3Rpb24uY2FsbCgkKCcudGFiLWhlYWRsaW5lLXdyYXBwZXIgYScpWzBdKTtcclxuXHRcdFxyXG5cdFx0ZG9uZSgpOyBcclxuXHR9OyBcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsgIl19
