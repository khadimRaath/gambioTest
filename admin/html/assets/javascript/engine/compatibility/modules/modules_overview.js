'use strict';

/* --------------------------------------------------------------
 modules_overview.js 2015-09-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Modules Overview Listing Handler
 *
 * This module will handle the listing actions on module pages like payment, shipping or order total
 *
 * @module Compatibility/modules_overview
 */
gx.compatibility.module('modules_overview', [],

/**  @lends module:Compatibility/modules_overview */

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
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	var _toggle = function _toggle(event) {
		var id = $(this).prop('id');

		$('.' + id).toggleClass('hidden');
		$(this).toggleClass('closed');

		$(this).find('i:last-child').toggleClass('fa-plus-square-o');
		$(this).find('i:last-child').toggleClass('fa-minus-square-o');
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		// init method

		$('.module-head').on('click', _toggle);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1vZHVsZXMvbW9kdWxlc19vdmVydmlldy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3RvZ2dsZSIsImV2ZW50IiwiaWQiLCJwcm9wIiwidG9nZ2xlQ2xhc3MiLCJmaW5kIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0Msa0JBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSU8sVUFBVSxTQUFWQSxPQUFVLENBQVNDLEtBQVQsRUFBZ0I7QUFDN0IsTUFBSUMsS0FBS04sRUFBRSxJQUFGLEVBQVFPLElBQVIsQ0FBYSxJQUFiLENBQVQ7O0FBRUFQLElBQUUsTUFBTU0sRUFBUixFQUFZRSxXQUFaLENBQXdCLFFBQXhCO0FBQ0FSLElBQUUsSUFBRixFQUFRUSxXQUFSLENBQW9CLFFBQXBCOztBQUVBUixJQUFFLElBQUYsRUFBUVMsSUFBUixDQUFhLGNBQWIsRUFBNkJELFdBQTdCLENBQXlDLGtCQUF6QztBQUNBUixJQUFFLElBQUYsRUFBUVMsSUFBUixDQUFhLGNBQWIsRUFBNkJELFdBQTdCLENBQXlDLG1CQUF6QztBQUNBLEVBUkQ7O0FBVUE7QUFDQTtBQUNBOztBQUVBWCxRQUFPYSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1Qjs7QUFFQVgsSUFBRSxjQUFGLEVBQWtCWSxFQUFsQixDQUFxQixPQUFyQixFQUE4QlIsT0FBOUI7O0FBRUFPO0FBQ0EsRUFQRDs7QUFTQSxRQUFPZCxNQUFQO0FBQ0EsQ0F4RUYiLCJmaWxlIjoibW9kdWxlcy9tb2R1bGVzX292ZXJ2aWV3LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2R1bGVzX292ZXJ2aWV3LmpzIDIwMTUtMDktMjggZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIE1vZHVsZXMgT3ZlcnZpZXcgTGlzdGluZyBIYW5kbGVyXG4gKlxuICogVGhpcyBtb2R1bGUgd2lsbCBoYW5kbGUgdGhlIGxpc3RpbmcgYWN0aW9ucyBvbiBtb2R1bGUgcGFnZXMgbGlrZSBwYXltZW50LCBzaGlwcGluZyBvciBvcmRlciB0b3RhbFxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9tb2R1bGVzX292ZXJ2aWV3XG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnbW9kdWxlc19vdmVydmlldycsXG5cdFxuXHRbXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L21vZHVsZXNfb3ZlcnZpZXcgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF90b2dnbGUgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIGlkID0gJCh0aGlzKS5wcm9wKCdpZCcpO1xuXHRcdFx0XG5cdFx0XHQkKCcuJyArIGlkKS50b2dnbGVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHQkKHRoaXMpLnRvZ2dsZUNsYXNzKCdjbG9zZWQnKTtcblx0XHRcdFxuXHRcdFx0JCh0aGlzKS5maW5kKCdpOmxhc3QtY2hpbGQnKS50b2dnbGVDbGFzcygnZmEtcGx1cy1zcXVhcmUtbycpO1xuXHRcdFx0JCh0aGlzKS5maW5kKCdpOmxhc3QtY2hpbGQnKS50b2dnbGVDbGFzcygnZmEtbWludXMtc3F1YXJlLW8nKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdC8vIGluaXQgbWV0aG9kXG5cdFx0XHRcblx0XHRcdCQoJy5tb2R1bGUtaGVhZCcpLm9uKCdjbGljaycsIF90b2dnbGUpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
