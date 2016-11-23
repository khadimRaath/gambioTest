'use strict';

/* --------------------------------------------------------------
 shipcloud_config_controller.js 2016-01-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module(
// Module name
'shipcloud_config_controller',
// Module dependencies
[], function () {
	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var $this = $(this),
	    module = {};

	// ------------------------------------------------------------------------

	var _initCarrierCheckboxes = function _initCarrierCheckboxes() {
		$('input[name="preselected_carriers[]"]').on('change', function (e) {
			if ($(this).get(0).checked === true) {
				$('input[name="checked_carriers[]"]', $(this).closest('tr')).removeAttr('disabled');
			} else {
				$('input[name="checked_carriers[]"]', $(this).closest('tr')).attr('disabled', 'disabled');
			}
		});
		$('input[name="preselected_carriers[]"]').trigger('change');
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		_initCarrierCheckboxes();
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNoaXBjbG91ZC9zaGlwY2xvdWRfY29uZmlnX2NvbnRyb2xsZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsIiR0aGlzIiwiJCIsIl9pbml0Q2FycmllckNoZWNrYm94ZXMiLCJvbiIsImUiLCJnZXQiLCJjaGVja2VkIiwiY2xvc2VzdCIsInJlbW92ZUF0dHIiLCJhdHRyIiwidHJpZ2dlciIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZjtBQUNDO0FBQ0EsNkJBRkQ7QUFHQztBQUNBLEVBSkQsRUFLQyxZQUFXO0FBQ1Y7O0FBRUE7QUFDQTtBQUNBOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0YsU0FBUyxFQURWOztBQUdBOztBQUVBLEtBQUlHLHlCQUF5QixTQUF6QkEsc0JBQXlCLEdBQVc7QUFDdkNELElBQUUsc0NBQUYsRUFBMENFLEVBQTFDLENBQTZDLFFBQTdDLEVBQXVELFVBQVNDLENBQVQsRUFBWTtBQUNsRSxPQUFJSCxFQUFFLElBQUYsRUFBUUksR0FBUixDQUFZLENBQVosRUFBZUMsT0FBZixLQUEyQixJQUEvQixFQUFxQztBQUNwQ0wsTUFBRSxrQ0FBRixFQUFzQ0EsRUFBRSxJQUFGLEVBQVFNLE9BQVIsQ0FBZ0IsSUFBaEIsQ0FBdEMsRUFBNkRDLFVBQTdELENBQXdFLFVBQXhFO0FBQ0EsSUFGRCxNQUdLO0FBQ0pQLE1BQUUsa0NBQUYsRUFBc0NBLEVBQUUsSUFBRixFQUFRTSxPQUFSLENBQWdCLElBQWhCLENBQXRDLEVBQTZERSxJQUE3RCxDQUFrRSxVQUFsRSxFQUE4RSxVQUE5RTtBQUNBO0FBQ0QsR0FQRDtBQVFBUixJQUFFLHNDQUFGLEVBQTBDUyxPQUExQyxDQUFrRCxRQUFsRDtBQUNBLEVBVkQ7O0FBWUE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQVgsUUFBT1ksSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QlY7QUFDQVU7QUFDQSxFQUhEOztBQUtBLFFBQU9iLE1BQVA7QUFDQSxDQTFDRiIsImZpbGUiOiJzaGlwY2xvdWQvc2hpcGNsb3VkX2NvbmZpZ19jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzaGlwY2xvdWRfY29uZmlnX2NvbnRyb2xsZXIuanMgMjAxNi0wMS0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0Ly8gTW9kdWxlIG5hbWVcblx0J3NoaXBjbG91ZF9jb25maWdfY29udHJvbGxlcicsXG5cdC8vIE1vZHVsZSBkZXBlbmRlbmNpZXNcblx0W10sXG5cdGZ1bmN0aW9uKCkge1xuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfaW5pdENhcnJpZXJDaGVja2JveGVzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwicHJlc2VsZWN0ZWRfY2FycmllcnNbXVwiXScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdGlmICgkKHRoaXMpLmdldCgwKS5jaGVja2VkID09PSB0cnVlKSB7XG5cdFx0XHRcdFx0JCgnaW5wdXRbbmFtZT1cImNoZWNrZWRfY2FycmllcnNbXVwiXScsICQodGhpcykuY2xvc2VzdCgndHInKSkucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlIHtcblx0XHRcdFx0XHQkKCdpbnB1dFtuYW1lPVwiY2hlY2tlZF9jYXJyaWVyc1tdXCJdJywgJCh0aGlzKS5jbG9zZXN0KCd0cicpKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJwcmVzZWxlY3RlZF9jYXJyaWVyc1tdXCJdJykudHJpZ2dlcignY2hhbmdlJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSBtb2R1bGUsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0X2luaXRDYXJyaWVyQ2hlY2tib3hlcygpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fVxuKTtcbiJdfQ==
