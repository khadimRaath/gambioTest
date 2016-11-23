'use strict';

/* --------------------------------------------------------------
 accounting_controller.js 2015-09-24 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

/**
 * ## Accounting Controller Widget
 *
 * This controller will handle the checkboxes in this page.
 *
 * @module Controllers/accounting_controller
 */
gx.controllers.module(
// Module name
'accounting_controller',

// Module dependencies
[],

/** @lends module:Controllers/accounting_controller */

function () {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var $this = $(this),
	    module = {};

	// ------------------------------------------------------------------------
	// ELEMENTS DEFINITION
	// ------------------------------------------------------------------------

	var $mainCheckBox = $this.find('#check_all');
	var $checkboxes = $this.find('input[name="access[]"]');

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _onClick = function _onClick(event) {
		var $target = $(event.target);

		if ($target.is($mainCheckBox)) {
			var checked = $target.is(':checked');

			$checkboxes.each(function (index, element) {
				var $switcher = $(element).parent();

				$(element).attr('checked', checked);

				if (checked) {
					$switcher.addClass('checked');
				} else {
					$switcher.removeClass('checked');
				}
			});
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		$this.on('click', _onClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFjY291bnRpbmcvYWNjb3VudGluZ19jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCIkdGhpcyIsIiQiLCIkbWFpbkNoZWNrQm94IiwiZmluZCIsIiRjaGVja2JveGVzIiwiX29uQ2xpY2siLCJldmVudCIsIiR0YXJnZXQiLCJ0YXJnZXQiLCJpcyIsImNoZWNrZWQiLCJlYWNoIiwiaW5kZXgiLCJlbGVtZW50IiwiJHN3aXRjaGVyIiwicGFyZW50IiwiYXR0ciIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmO0FBQ0M7QUFDQSx1QkFGRDs7QUFJQztBQUNBLEVBTEQ7O0FBT0M7O0FBRUEsWUFBVzs7QUFFVjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDRixTQUFTLEVBRFY7O0FBR0E7QUFDQTtBQUNBOztBQUVBLEtBQUlHLGdCQUFnQkYsTUFBTUcsSUFBTixDQUFXLFlBQVgsQ0FBcEI7QUFDQSxLQUFJQyxjQUFjSixNQUFNRyxJQUFOLENBQVcsd0JBQVgsQ0FBbEI7O0FBRUE7QUFDQTtBQUNBOztBQUVBLEtBQUlFLFdBQVcsU0FBWEEsUUFBVyxDQUFTQyxLQUFULEVBQWdCO0FBQzlCLE1BQUlDLFVBQVVOLEVBQUVLLE1BQU1FLE1BQVIsQ0FBZDs7QUFFQSxNQUFJRCxRQUFRRSxFQUFSLENBQVdQLGFBQVgsQ0FBSixFQUErQjtBQUM5QixPQUFJUSxVQUFVSCxRQUFRRSxFQUFSLENBQVcsVUFBWCxDQUFkOztBQUVBTCxlQUFZTyxJQUFaLENBQWlCLFVBQVNDLEtBQVQsRUFBZ0JDLE9BQWhCLEVBQXlCO0FBQ3pDLFFBQUlDLFlBQVliLEVBQUVZLE9BQUYsRUFBV0UsTUFBWCxFQUFoQjs7QUFFQWQsTUFBRVksT0FBRixFQUFXRyxJQUFYLENBQWdCLFNBQWhCLEVBQTJCTixPQUEzQjs7QUFFQSxRQUFJQSxPQUFKLEVBQWE7QUFDWkksZUFBVUcsUUFBVixDQUFtQixTQUFuQjtBQUNBLEtBRkQsTUFFTztBQUNOSCxlQUFVSSxXQUFWLENBQXNCLFNBQXRCO0FBQ0E7QUFDRCxJQVZEO0FBV0E7QUFDRCxFQWxCRDs7QUFvQkE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQW5CLFFBQU9vQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCcEIsUUFBTXFCLEVBQU4sQ0FBUyxPQUFULEVBQWtCaEIsUUFBbEI7QUFDQWU7QUFDQSxFQUhEOztBQUtBLFFBQU9yQixNQUFQO0FBQ0EsQ0FoRUYiLCJmaWxlIjoiYWNjb3VudGluZy9hY2NvdW50aW5nX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGFjY291bnRpbmdfY29udHJvbGxlci5qcyAyMDE1LTA5LTI0IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEFjY291bnRpbmcgQ29udHJvbGxlciBXaWRnZXRcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIGNoZWNrYm94ZXMgaW4gdGhpcyBwYWdlLlxuICpcbiAqIEBtb2R1bGUgQ29udHJvbGxlcnMvYWNjb3VudGluZ19jb250cm9sbGVyXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0Ly8gTW9kdWxlIG5hbWVcblx0J2FjY291bnRpbmdfY29udHJvbGxlcicsXG5cdFxuXHQvLyBNb2R1bGUgZGVwZW5kZW5jaWVzXG5cdFtdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvYWNjb3VudGluZ19jb250cm9sbGVyICovXG5cdFxuXHRmdW5jdGlvbigpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFTEVNRU5UUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyICRtYWluQ2hlY2tCb3ggPSAkdGhpcy5maW5kKCcjY2hlY2tfYWxsJyk7XG5cdFx0dmFyICRjaGVja2JveGVzID0gJHRoaXMuZmluZCgnaW5wdXRbbmFtZT1cImFjY2Vzc1tdXCJdJyk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX29uQ2xpY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkKGV2ZW50LnRhcmdldCk7XG5cdFx0XHRcblx0XHRcdGlmICgkdGFyZ2V0LmlzKCRtYWluQ2hlY2tCb3gpKSB7XG5cdFx0XHRcdHZhciBjaGVja2VkID0gJHRhcmdldC5pcygnOmNoZWNrZWQnKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRjaGVja2JveGVzLmVhY2goZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnQpIHtcblx0XHRcdFx0XHR2YXIgJHN3aXRjaGVyID0gJChlbGVtZW50KS5wYXJlbnQoKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKGVsZW1lbnQpLmF0dHIoJ2NoZWNrZWQnLCBjaGVja2VkKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoY2hlY2tlZCkge1xuXHRcdFx0XHRcdFx0JHN3aXRjaGVyLmFkZENsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdCRzd2l0Y2hlci5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSBtb2R1bGUsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29uQ2xpY2spO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
