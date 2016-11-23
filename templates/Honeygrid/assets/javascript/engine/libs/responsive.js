'use strict';

/* --------------------------------------------------------------
 responsive.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.responsive = jse.libs.template.responsive || {};

/**
 * ## Honeygrid Responsive Utilities Library
 *
 * Library to make the template responsive. This function depends on jQuery.
 *
 * @module Honeygrid/Libs/responsive
 * @exports jse.libs.template.responsive
 */
(function (exports) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $body = $('body'),
	    current = null,
	    timer = null,
	    breakpoints = [{
		id: 20,
		name: 'too small',
		width: 480
	}, {
		id: 40,
		name: 'xs',
		width: 768
	}, {
		id: 60,
		name: 'sm',
		width: 992
	}, {
		id: 80,
		name: 'md',
		width: 1200
	}, {
		id: 100,
		name: 'lg',
		width: null
	}];

	// ########## EVENT HANDLER ##########

	/**
  * Returns the breakpoint of the current page, 
  * false if no breakpoint could be identified.
  *
  * @return Breakpoint
  */
	var _getBreakpoint = function _getBreakpoint() {
		var width = window.innerWidth,
		    result = null;

		// check if page is loaded inside an iframe and, if appropriate, set the iframe's width
		if (window.self !== window.top) {
			document.body.style.overflow = 'hidden';
			width = document.body.clientWidth;
			document.body.style.overflow = 'visible';
		}

		if (width === 0) {
			timer = setTimeout(function () {
				_getBreakpoint();
			}, 10);
			current = $.extend({}, breakpoints[0]); // set default breakpoint value
			return false;
		}

		$.each(breakpoints, function (i, v) {
			if (!v.width || width < v.width) {

				result = $.extend({}, v);
				return false;
			}
		});

		if (result && (!current || current.id !== result.id)) {
			current = $.extend({}, result);
			clearTimeout(timer);
			timer = setTimeout(function () {
				// @todo This lib depends on the existence of the events lib (both are loaded asynchronously).
				if (jse.libs.template.events !== undefined) {
					$body.trigger(jse.libs.template.events.BREAKPOINT(), current);
				}
			}, 10);
		}
	};

	// ########## INITIALIZATION ##########

	_getBreakpoint();

	$(window).on('resize', _getBreakpoint);

	/**
  * @todo rename method to "getBreakpoint".
  */
	exports.breakpoint = function () {
		return current;
	};
})(jse.libs.template.responsive);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvcmVzcG9uc2l2ZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJyZXNwb25zaXZlIiwiZXhwb3J0cyIsIiRib2R5IiwiJCIsImN1cnJlbnQiLCJ0aW1lciIsImJyZWFrcG9pbnRzIiwiaWQiLCJuYW1lIiwid2lkdGgiLCJfZ2V0QnJlYWtwb2ludCIsIndpbmRvdyIsImlubmVyV2lkdGgiLCJyZXN1bHQiLCJzZWxmIiwidG9wIiwiZG9jdW1lbnQiLCJib2R5Iiwic3R5bGUiLCJvdmVyZmxvdyIsImNsaWVudFdpZHRoIiwic2V0VGltZW91dCIsImV4dGVuZCIsImVhY2giLCJpIiwidiIsImNsZWFyVGltZW91dCIsImV2ZW50cyIsInVuZGVmaW5lZCIsInRyaWdnZXIiLCJCUkVBS1BPSU5UIiwib24iLCJicmVha3BvaW50Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsVUFBbEIsR0FBK0JILElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsVUFBbEIsSUFBZ0MsRUFBL0Q7O0FBRUE7Ozs7Ozs7O0FBUUMsV0FBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxNQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVLElBRFg7QUFBQSxLQUVDQyxRQUFRLElBRlQ7QUFBQSxLQUdDQyxjQUFjLENBQ2I7QUFDQ0MsTUFBSSxFQURMO0FBRUNDLFFBQU0sV0FGUDtBQUdDQyxTQUFPO0FBSFIsRUFEYSxFQU1iO0FBQ0NGLE1BQUksRUFETDtBQUVDQyxRQUFNLElBRlA7QUFHQ0MsU0FBTztBQUhSLEVBTmEsRUFXYjtBQUNDRixNQUFJLEVBREw7QUFFQ0MsUUFBTSxJQUZQO0FBR0NDLFNBQU87QUFIUixFQVhhLEVBZ0JiO0FBQ0NGLE1BQUksRUFETDtBQUVDQyxRQUFNLElBRlA7QUFHQ0MsU0FBTztBQUhSLEVBaEJhLEVBcUJiO0FBQ0NGLE1BQUksR0FETDtBQUVDQyxRQUFNLElBRlA7QUFHQ0MsU0FBTztBQUhSLEVBckJhLENBSGY7O0FBZ0NBOztBQUVBOzs7Ozs7QUFNQSxLQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0IsTUFBSUQsUUFBUUUsT0FBT0MsVUFBbkI7QUFBQSxNQUNDQyxTQUFTLElBRFY7O0FBR0E7QUFDQSxNQUFJRixPQUFPRyxJQUFQLEtBQWdCSCxPQUFPSSxHQUEzQixFQUFnQztBQUMvQkMsWUFBU0MsSUFBVCxDQUFjQyxLQUFkLENBQW9CQyxRQUFwQixHQUErQixRQUEvQjtBQUNBVixXQUFRTyxTQUFTQyxJQUFULENBQWNHLFdBQXRCO0FBQ0FKLFlBQVNDLElBQVQsQ0FBY0MsS0FBZCxDQUFvQkMsUUFBcEIsR0FBK0IsU0FBL0I7QUFDQTs7QUFFRCxNQUFJVixVQUFVLENBQWQsRUFBaUI7QUFDaEJKLFdBQVFnQixXQUFXLFlBQVc7QUFDN0JYO0FBQ0EsSUFGTyxFQUVMLEVBRkssQ0FBUjtBQUdBTixhQUFVRCxFQUFFbUIsTUFBRixDQUFTLEVBQVQsRUFBYWhCLFlBQVksQ0FBWixDQUFiLENBQVYsQ0FKZ0IsQ0FJd0I7QUFDeEMsVUFBTyxLQUFQO0FBQ0E7O0FBRURILElBQUVvQixJQUFGLENBQU9qQixXQUFQLEVBQW9CLFVBQVNrQixDQUFULEVBQVlDLENBQVosRUFBZTtBQUNsQyxPQUFJLENBQUNBLEVBQUVoQixLQUFILElBQVlBLFFBQVFnQixFQUFFaEIsS0FBMUIsRUFBaUM7O0FBRWhDSSxhQUFTVixFQUFFbUIsTUFBRixDQUFTLEVBQVQsRUFBYUcsQ0FBYixDQUFUO0FBQ0EsV0FBTyxLQUFQO0FBQ0E7QUFDRCxHQU5EOztBQVNBLE1BQUlaLFdBQVcsQ0FBQ1QsT0FBRCxJQUFZQSxRQUFRRyxFQUFSLEtBQWVNLE9BQU9OLEVBQTdDLENBQUosRUFBc0Q7QUFDckRILGFBQVVELEVBQUVtQixNQUFGLENBQVMsRUFBVCxFQUFhVCxNQUFiLENBQVY7QUFDQWEsZ0JBQWFyQixLQUFiO0FBQ0FBLFdBQVFnQixXQUFXLFlBQVc7QUFDN0I7QUFDQSxRQUFJeEIsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCNEIsTUFBbEIsS0FBNkJDLFNBQWpDLEVBQTRDO0FBQzNDMUIsV0FBTTJCLE9BQU4sQ0FBY2hDLElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQjRCLE1BQWxCLENBQXlCRyxVQUF6QixFQUFkLEVBQXFEMUIsT0FBckQ7QUFDQTtBQUNELElBTE8sRUFLTCxFQUxLLENBQVI7QUFNQTtBQUNELEVBdENEOztBQXlDQTs7QUFFQU07O0FBRUFQLEdBQUVRLE1BQUYsRUFBVW9CLEVBQVYsQ0FBYSxRQUFiLEVBQXVCckIsY0FBdkI7O0FBRUE7OztBQUdBVCxTQUFRK0IsVUFBUixHQUFxQixZQUFXO0FBQy9CLFNBQU81QixPQUFQO0FBQ0EsRUFGRDtBQUlBLENBcEdBLEVBb0dDUCxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLFVBcEduQixDQUFEIiwiZmlsZSI6ImxpYnMvcmVzcG9uc2l2ZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcmVzcG9uc2l2ZS5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudGVtcGxhdGUucmVzcG9uc2l2ZSA9IGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUgfHwge307XG5cbi8qKlxuICogIyMgSG9uZXlncmlkIFJlc3BvbnNpdmUgVXRpbGl0aWVzIExpYnJhcnlcbiAqXG4gKiBMaWJyYXJ5IHRvIG1ha2UgdGhlIHRlbXBsYXRlIHJlc3BvbnNpdmUuIFRoaXMgZnVuY3Rpb24gZGVwZW5kcyBvbiBqUXVlcnkuXG4gKlxuICogQG1vZHVsZSBIb25leWdyaWQvTGlicy9yZXNwb25zaXZlXG4gKiBAZXhwb3J0cyBqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdHZhciAkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRjdXJyZW50ID0gbnVsbCxcblx0XHR0aW1lciA9IG51bGwsXG5cdFx0YnJlYWtwb2ludHMgPSBbXG5cdFx0XHR7XG5cdFx0XHRcdGlkOiAyMCxcblx0XHRcdFx0bmFtZTogJ3RvbyBzbWFsbCcsXG5cdFx0XHRcdHdpZHRoOiA0ODBcblx0XHRcdH0sXG5cdFx0XHR7XG5cdFx0XHRcdGlkOiA0MCxcblx0XHRcdFx0bmFtZTogJ3hzJyxcblx0XHRcdFx0d2lkdGg6IDc2OFxuXHRcdFx0fSxcblx0XHRcdHtcblx0XHRcdFx0aWQ6IDYwLFxuXHRcdFx0XHRuYW1lOiAnc20nLFxuXHRcdFx0XHR3aWR0aDogOTkyXG5cdFx0XHR9LFxuXHRcdFx0e1xuXHRcdFx0XHRpZDogODAsXG5cdFx0XHRcdG5hbWU6ICdtZCcsXG5cdFx0XHRcdHdpZHRoOiAxMjAwXG5cdFx0XHR9LFxuXHRcdFx0e1xuXHRcdFx0XHRpZDogMTAwLFxuXHRcdFx0XHRuYW1lOiAnbGcnLFxuXHRcdFx0XHR3aWR0aDogbnVsbFxuXHRcdFx0fVxuXHRcdF07XG5cblxuXHQvLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBSZXR1cm5zIHRoZSBicmVha3BvaW50IG9mIHRoZSBjdXJyZW50IHBhZ2UsIFxuXHQgKiBmYWxzZSBpZiBubyBicmVha3BvaW50IGNvdWxkIGJlIGlkZW50aWZpZWQuXG5cdCAqXG5cdCAqIEByZXR1cm4gQnJlYWtwb2ludFxuXHQgKi9cblx0dmFyIF9nZXRCcmVha3BvaW50ID0gZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHdpZHRoID0gd2luZG93LmlubmVyV2lkdGgsXG5cdFx0XHRyZXN1bHQgPSBudWxsO1xuXG5cdFx0Ly8gY2hlY2sgaWYgcGFnZSBpcyBsb2FkZWQgaW5zaWRlIGFuIGlmcmFtZSBhbmQsIGlmIGFwcHJvcHJpYXRlLCBzZXQgdGhlIGlmcmFtZSdzIHdpZHRoXG5cdFx0aWYgKHdpbmRvdy5zZWxmICE9PSB3aW5kb3cudG9wKSB7XG5cdFx0XHRkb2N1bWVudC5ib2R5LnN0eWxlLm92ZXJmbG93ID0gJ2hpZGRlbic7XG5cdFx0XHR3aWR0aCA9IGRvY3VtZW50LmJvZHkuY2xpZW50V2lkdGg7XG5cdFx0XHRkb2N1bWVudC5ib2R5LnN0eWxlLm92ZXJmbG93ID0gJ3Zpc2libGUnO1xuXHRcdH1cblx0XHRcblx0XHRpZiAod2lkdGggPT09IDApIHtcblx0XHRcdHRpbWVyID0gc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0X2dldEJyZWFrcG9pbnQoKTtcblx0XHRcdH0sIDEwKTtcblx0XHRcdGN1cnJlbnQgPSAkLmV4dGVuZCh7fSwgYnJlYWtwb2ludHNbMF0pOyAvLyBzZXQgZGVmYXVsdCBicmVha3BvaW50IHZhbHVlXG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fVxuXG5cdFx0JC5lYWNoKGJyZWFrcG9pbnRzLCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRpZiAoIXYud2lkdGggfHwgd2lkdGggPCB2LndpZHRoKSB7XG5cblx0XHRcdFx0cmVzdWx0ID0gJC5leHRlbmQoe30sIHYpO1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fSk7XG5cblxuXHRcdGlmIChyZXN1bHQgJiYgKCFjdXJyZW50IHx8IGN1cnJlbnQuaWQgIT09IHJlc3VsdC5pZCkpIHtcblx0XHRcdGN1cnJlbnQgPSAkLmV4dGVuZCh7fSwgcmVzdWx0KTtcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lcik7XG5cdFx0XHR0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdC8vIEB0b2RvIFRoaXMgbGliIGRlcGVuZHMgb24gdGhlIGV4aXN0ZW5jZSBvZiB0aGUgZXZlbnRzIGxpYiAoYm90aCBhcmUgbG9hZGVkIGFzeW5jaHJvbm91c2x5KS5cblx0XHRcdFx0aWYgKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cyAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0JGJvZHkudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBjdXJyZW50KTtcblx0XHRcdFx0fVxuXHRcdFx0fSwgMTApO1xuXHRcdH1cblx0fTtcblxuXG5cdC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdF9nZXRCcmVha3BvaW50KCk7XG5cblx0JCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfZ2V0QnJlYWtwb2ludCk7XG5cblx0LyoqXG5cdCAqIEB0b2RvIHJlbmFtZSBtZXRob2QgdG8gXCJnZXRCcmVha3BvaW50XCIuXG5cdCAqL1xuXHRleHBvcnRzLmJyZWFrcG9pbnQgPSBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4gY3VycmVudDtcblx0fTtcblxufShqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlKSk7Il19
