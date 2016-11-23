'use strict';

/* --------------------------------------------------------------
 transitions.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that helps on applying css3 transitions on
 * elements. This component listens on events triggered on
 * objects that needs to be animated and calculates the
 * dimensions for the element before and after animation
 */
gambio.widgets.module('transitions', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    timer = [],
	    defaults = {
		duration: 0.5, // Default transition duration in seconds
		open: true, // Is it a open or a close animation (needed to determine the correct classes)
		classClose: '', // Class added during close transition
		classOpen: '' // Class added during open animation
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTION ##########

	/**
  * Helper function that gets the current transition
  * duration from the given element (in ms). If the
  * current object hasn't an transition duration check
  * all child elements for a duration and stop after
  * finding the first one
  * @param       {object}    $element    jQuery selection of the animated element
  * @return     {integer}               Animation duration in ms
  * @private
  */
	var _getTransitionDuration = function _getTransitionDuration($element) {

		var duration = options.duration;

		$element.add($element.children()).each(function () {
			var time = $element.css('transition-duration') !== undefined ? $element.css('transition-duration') : $element.css('-webkit-transtion-duration') !== undefined ? $element.css('-webkit-transtion-duration') : $element.css('-moz-transtion-duration') !== undefined ? $element.css('-moz-transtion-duration') : $element.css('-ms-transtion-duration') !== undefined ? $element.css('-ms-transtion-duration') : $element.css('-o-transtion-duration') !== undefined ? $element.css('-o-transtion-duration') : -1;

			if (time >= 0) {
				duration = time;
				return false;
			}
		});

		duration = Math.round(parseFloat(duration) * 1000);
		return duration;
	};

	// ########## EVENT HANDLER ##########

	/**
  * Function that sets the classes and dimensions to an object
  * that needs to be animated. After the animation duration it
  * cleans up all unnecessary classes and style attributes
  * @param       {object}        e           jQuery event object
  * @param       {object}        d           JSON that contains the configuration
  * @private
  */
	var _transitionHandler = function _transitionHandler(e, d) {

		var $self = $(e.target),
		    $clone = $self.clone(),
		    // Avoid hiding the original element, use a clone as a helper.
		dataset = $.extend({}, $self.data().transition || {}, d),
		    removeClass = dataset.open ? dataset.classClose : dataset.classOpen,
		    addClass = dataset.open ? dataset.classOpen : dataset.classClose,
		    initialHeight = null,
		    initialWidth = null,
		    height = null,
		    width = null;

		dataset.uid = dataset.uid || parseInt(Math.random() * 100000, 10);
		removeClass = removeClass || '';
		addClass = addClass || '';

		// Stop current animation timers
		if (timer[dataset.uid]) {
			clearTimeout(timer[dataset.uid]);
		}

		$clone.appendTo($self.parent());

		// Get initial and final dimensions of the target
		// by getting the current width and height values
		// and the ones with the final classes appended to
		// the target
		$clone.css({
			visibility: 'hidden',
			display: 'initial'
		});

		initialHeight = $clone.outerHeight();
		initialWidth = $clone.outerWidth();

		$self.removeAttr('style').removeClass('transition ' + removeClass).addClass(addClass);

		height = $self.outerHeight();
		width = $self.outerWidth();

		// Check if the container height needs to be set
		if (dataset.calcHeight) {
			// Setup the transition by setting the initial
			// values BEFORE adding the transition classes.
			// After setting the transition classes, set the
			// final sizes
			$self.removeClass(addClass).css({
				height: initialHeight + 'px',
				width: initialWidth + 'px',
				visibility: 'initial',
				display: 'initial'
			}).addClass('transition ' + addClass).css({
				'height': height + 'px',
				'width': width + 'px'
			});
		} else {
			// Setup the transition by setting the transition classes.
			$self.removeClass(addClass).addClass('transition ' + addClass);
		}

		// Add an event listener to remove all unnecessary
		// classes and style attributes
		var duration = _getTransitionDuration($self);
		timer[dataset.uid] = setTimeout(function () {

			$self.removeAttr('style').removeClass('transition').removeData('transition').triggerHandler(jse.libs.template.events.TRANSITION_FINISHED());
		}, duration);

		// Store the configuration data to the target object
		$self.data('transition', dataset);
		$clone.remove();
	};

	/**
  * Event handler that stops a transition timer set
  * by the _transitionHandler function.
  * @private
  */
	var _stopTransition = function _stopTransition() {
		var $self = $(this),
		    dataset = $self.data('transition') || {};

		if (!$.isEmptyObject(dataset)) {

			timer[dataset.uid] = timer[dataset.uid] ? clearTimeout(timer[dataset.uid]) : null;

			$self.removeAttr('style').removeClass('transition').removeData('transition').triggerHandler(jse.libs.template.events.TRANSITION_FINISHED());
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on(jse.libs.template.events.TRANSITION(), _transitionHandler).on(jse.libs.template.events.TRANSITION_STOP(), _stopTransition);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdHJhbnNpdGlvbnMuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJ0aW1lciIsImRlZmF1bHRzIiwiZHVyYXRpb24iLCJvcGVuIiwiY2xhc3NDbG9zZSIsImNsYXNzT3BlbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0VHJhbnNpdGlvbkR1cmF0aW9uIiwiJGVsZW1lbnQiLCJhZGQiLCJjaGlsZHJlbiIsImVhY2giLCJ0aW1lIiwiY3NzIiwidW5kZWZpbmVkIiwiTWF0aCIsInJvdW5kIiwicGFyc2VGbG9hdCIsIl90cmFuc2l0aW9uSGFuZGxlciIsImUiLCJkIiwiJHNlbGYiLCJ0YXJnZXQiLCIkY2xvbmUiLCJjbG9uZSIsImRhdGFzZXQiLCJ0cmFuc2l0aW9uIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImluaXRpYWxIZWlnaHQiLCJpbml0aWFsV2lkdGgiLCJoZWlnaHQiLCJ3aWR0aCIsInVpZCIsInBhcnNlSW50IiwicmFuZG9tIiwiY2xlYXJUaW1lb3V0IiwiYXBwZW5kVG8iLCJwYXJlbnQiLCJ2aXNpYmlsaXR5IiwiZGlzcGxheSIsIm91dGVySGVpZ2h0Iiwib3V0ZXJXaWR0aCIsInJlbW92ZUF0dHIiLCJjYWxjSGVpZ2h0Iiwic2V0VGltZW91dCIsInJlbW92ZURhdGEiLCJ0cmlnZ2VySGFuZGxlciIsImpzZSIsImxpYnMiLCJ0ZW1wbGF0ZSIsImV2ZW50cyIsIlRSQU5TSVRJT05fRklOSVNIRUQiLCJyZW1vdmUiLCJfc3RvcFRyYW5zaXRpb24iLCJpc0VtcHR5T2JqZWN0IiwiaW5pdCIsImRvbmUiLCJvbiIsIlRSQU5TSVRJT04iLCJUUkFOU0lUSU9OX1NUT1AiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7O0FBTUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGFBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdC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OlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0cmFuc2l0aW9ucy5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgdGhhdCBoZWxwcyBvbiBhcHBseWluZyBjc3MzIHRyYW5zaXRpb25zIG9uXG4gKiBlbGVtZW50cy4gVGhpcyBjb21wb25lbnQgbGlzdGVucyBvbiBldmVudHMgdHJpZ2dlcmVkIG9uXG4gKiBvYmplY3RzIHRoYXQgbmVlZHMgdG8gYmUgYW5pbWF0ZWQgYW5kIGNhbGN1bGF0ZXMgdGhlXG4gKiBkaW1lbnNpb25zIGZvciB0aGUgZWxlbWVudCBiZWZvcmUgYW5kIGFmdGVyIGFuaW1hdGlvblxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCd0cmFuc2l0aW9ucycsXG5cblx0W1xuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0dGltZXIgPSBbXSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRkdXJhdGlvbjogMC41LCAgICAgICAgLy8gRGVmYXVsdCB0cmFuc2l0aW9uIGR1cmF0aW9uIGluIHNlY29uZHNcblx0XHRcdFx0b3BlbjogdHJ1ZSwgICAgICAgLy8gSXMgaXQgYSBvcGVuIG9yIGEgY2xvc2UgYW5pbWF0aW9uIChuZWVkZWQgdG8gZGV0ZXJtaW5lIHRoZSBjb3JyZWN0IGNsYXNzZXMpXG5cdFx0XHRcdGNsYXNzQ2xvc2U6ICcnLCAgICAgICAgIC8vIENsYXNzIGFkZGVkIGR1cmluZyBjbG9zZSB0cmFuc2l0aW9uXG5cdFx0XHRcdGNsYXNzT3BlbjogJycgICAgICAgICAgLy8gQ2xhc3MgYWRkZWQgZHVyaW5nIG9wZW4gYW5pbWF0aW9uXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBnZXRzIHRoZSBjdXJyZW50IHRyYW5zaXRpb25cblx0XHQgKiBkdXJhdGlvbiBmcm9tIHRoZSBnaXZlbiBlbGVtZW50IChpbiBtcykuIElmIHRoZVxuXHRcdCAqIGN1cnJlbnQgb2JqZWN0IGhhc24ndCBhbiB0cmFuc2l0aW9uIGR1cmF0aW9uIGNoZWNrXG5cdFx0ICogYWxsIGNoaWxkIGVsZW1lbnRzIGZvciBhIGR1cmF0aW9uIGFuZCBzdG9wIGFmdGVyXG5cdFx0ICogZmluZGluZyB0aGUgZmlyc3Qgb25lXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRlbGVtZW50ICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGFuaW1hdGVkIGVsZW1lbnRcblx0XHQgKiBAcmV0dXJuICAgICB7aW50ZWdlcn0gICAgICAgICAgICAgICBBbmltYXRpb24gZHVyYXRpb24gaW4gbXNcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0VHJhbnNpdGlvbkR1cmF0aW9uID0gZnVuY3Rpb24oJGVsZW1lbnQpIHtcblxuXHRcdFx0dmFyIGR1cmF0aW9uID0gb3B0aW9ucy5kdXJhdGlvbjtcblxuXHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0LmFkZCgkZWxlbWVudC5jaGlsZHJlbigpKVxuXHRcdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHR2YXIgdGltZSA9ICgkZWxlbWVudC5jc3MoJ3RyYW5zaXRpb24tZHVyYXRpb24nKSAhPT0gdW5kZWZpbmVkKVxuXHRcdFx0XHRcdFx0PyAkZWxlbWVudC5jc3MoJ3RyYW5zaXRpb24tZHVyYXRpb24nKVxuXHRcdFx0XHRcdFx0OiAoJGVsZW1lbnQuY3NzKCctd2Via2l0LXRyYW5zdGlvbi1kdXJhdGlvbicpICE9PSB1bmRlZmluZWQpXG5cdFx0XHRcdFx0XHQgICAgICAgICAgID8gJGVsZW1lbnQuY3NzKCctd2Via2l0LXRyYW5zdGlvbi1kdXJhdGlvbicpXG5cdFx0XHRcdFx0XHQgICAgICAgICAgIDogKCRlbGVtZW50LmNzcygnLW1vei10cmFuc3Rpb24tZHVyYXRpb24nKSAhPT0gdW5kZWZpbmVkKVxuXHRcdFx0XHRcdFx0XHQgID8gJGVsZW1lbnQuY3NzKCctbW96LXRyYW5zdGlvbi1kdXJhdGlvbicpXG5cdFx0XHRcdFx0XHRcdCAgOiAoJGVsZW1lbnQuY3NzKCctbXMtdHJhbnN0aW9uLWR1cmF0aW9uJykgIT09IHVuZGVmaW5lZClcblx0XHRcdFx0XHRcdFx0ICAgICAgICAgICAgID8gJGVsZW1lbnQuY3NzKCctbXMtdHJhbnN0aW9uLWR1cmF0aW9uJylcblx0XHRcdFx0XHRcdFx0ICAgICAgICAgICAgIDogKCRlbGVtZW50LmNzcygnLW8tdHJhbnN0aW9uLWR1cmF0aW9uJykgIT09IHVuZGVmaW5lZClcblx0XHRcdFx0XHRcdFx0XHQgICAgPyAkZWxlbWVudC5jc3MoJy1vLXRyYW5zdGlvbi1kdXJhdGlvbicpIDogLTE7XG5cblx0XHRcdFx0XHRpZiAodGltZSA+PSAwKSB7XG5cdFx0XHRcdFx0XHRkdXJhdGlvbiA9IHRpbWU7XG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblxuXHRcdFx0ZHVyYXRpb24gPSBNYXRoLnJvdW5kKHBhcnNlRmxvYXQoZHVyYXRpb24pICogMTAwMCk7XG5cdFx0XHRyZXR1cm4gZHVyYXRpb247XG5cblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBGdW5jdGlvbiB0aGF0IHNldHMgdGhlIGNsYXNzZXMgYW5kIGRpbWVuc2lvbnMgdG8gYW4gb2JqZWN0XG5cdFx0ICogdGhhdCBuZWVkcyB0byBiZSBhbmltYXRlZC4gQWZ0ZXIgdGhlIGFuaW1hdGlvbiBkdXJhdGlvbiBpdFxuXHRcdCAqIGNsZWFucyB1cCBhbGwgdW5uZWNlc3NhcnkgY2xhc3NlcyBhbmQgc3R5bGUgYXR0cmlidXRlc1xuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZCAgICAgICAgICAgSlNPTiB0aGF0IGNvbnRhaW5zIHRoZSBjb25maWd1cmF0aW9uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RyYW5zaXRpb25IYW5kbGVyID0gZnVuY3Rpb24oZSwgZCkge1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKGUudGFyZ2V0KSxcblx0XHRcdFx0JGNsb25lID0gJHNlbGYuY2xvbmUoKSwgLy8gQXZvaWQgaGlkaW5nIHRoZSBvcmlnaW5hbCBlbGVtZW50LCB1c2UgYSBjbG9uZSBhcyBhIGhlbHBlci5cblx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCAkc2VsZi5kYXRhKCkudHJhbnNpdGlvbiB8fCB7fSwgZCksXG5cdFx0XHRcdHJlbW92ZUNsYXNzID0gKGRhdGFzZXQub3BlbikgPyBkYXRhc2V0LmNsYXNzQ2xvc2UgOiBkYXRhc2V0LmNsYXNzT3Blbixcblx0XHRcdFx0YWRkQ2xhc3MgPSAoZGF0YXNldC5vcGVuKSA/IGRhdGFzZXQuY2xhc3NPcGVuIDogZGF0YXNldC5jbGFzc0Nsb3NlLFxuXHRcdFx0XHRpbml0aWFsSGVpZ2h0ID0gbnVsbCxcblx0XHRcdFx0aW5pdGlhbFdpZHRoID0gbnVsbCxcblx0XHRcdFx0aGVpZ2h0ID0gbnVsbCxcblx0XHRcdFx0d2lkdGggPSBudWxsO1xuXG5cdFx0XHRkYXRhc2V0LnVpZCA9IGRhdGFzZXQudWlkIHx8IHBhcnNlSW50KE1hdGgucmFuZG9tKCkgKiAxMDAwMDAsIDEwKTtcblx0XHRcdHJlbW92ZUNsYXNzID0gcmVtb3ZlQ2xhc3MgfHwgJyc7XG5cdFx0XHRhZGRDbGFzcyA9IGFkZENsYXNzIHx8ICcnO1xuXG5cdFx0XHQvLyBTdG9wIGN1cnJlbnQgYW5pbWF0aW9uIHRpbWVyc1xuXHRcdFx0aWYgKHRpbWVyW2RhdGFzZXQudWlkXSkge1xuXHRcdFx0XHRjbGVhclRpbWVvdXQodGltZXJbZGF0YXNldC51aWRdKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JGNsb25lLmFwcGVuZFRvKCRzZWxmLnBhcmVudCgpKTsgXG5cblxuXHRcdFx0Ly8gR2V0IGluaXRpYWwgYW5kIGZpbmFsIGRpbWVuc2lvbnMgb2YgdGhlIHRhcmdldFxuXHRcdFx0Ly8gYnkgZ2V0dGluZyB0aGUgY3VycmVudCB3aWR0aCBhbmQgaGVpZ2h0IHZhbHVlc1xuXHRcdFx0Ly8gYW5kIHRoZSBvbmVzIHdpdGggdGhlIGZpbmFsIGNsYXNzZXMgYXBwZW5kZWQgdG9cblx0XHRcdC8vIHRoZSB0YXJnZXRcblx0XHRcdCRjbG9uZS5jc3Moe1xuXHRcdFx0XHQgICAgICAgICAgdmlzaWJpbGl0eTogJ2hpZGRlbicsXG5cdFx0XHRcdCAgICAgICAgICBkaXNwbGF5OiAnaW5pdGlhbCdcblx0XHRcdCAgICAgICAgICB9KTtcblxuXHRcdFx0aW5pdGlhbEhlaWdodCA9ICRjbG9uZS5vdXRlckhlaWdodCgpO1xuXHRcdFx0aW5pdGlhbFdpZHRoID0gJGNsb25lLm91dGVyV2lkdGgoKTtcblx0XHRcdFxuXHRcdFx0JHNlbGZcblx0XHRcdFx0LnJlbW92ZUF0dHIoJ3N0eWxlJylcblx0XHRcdFx0LnJlbW92ZUNsYXNzKCd0cmFuc2l0aW9uICcgKyByZW1vdmVDbGFzcylcblx0XHRcdFx0LmFkZENsYXNzKGFkZENsYXNzKTtcblxuXHRcdFx0aGVpZ2h0ID0gJHNlbGYub3V0ZXJIZWlnaHQoKTtcblx0XHRcdHdpZHRoID0gJHNlbGYub3V0ZXJXaWR0aCgpO1xuXG5cdFx0XHQvLyBDaGVjayBpZiB0aGUgY29udGFpbmVyIGhlaWdodCBuZWVkcyB0byBiZSBzZXRcblx0XHRcdGlmIChkYXRhc2V0LmNhbGNIZWlnaHQpIHtcblx0XHRcdFx0Ly8gU2V0dXAgdGhlIHRyYW5zaXRpb24gYnkgc2V0dGluZyB0aGUgaW5pdGlhbFxuXHRcdFx0XHQvLyB2YWx1ZXMgQkVGT1JFIGFkZGluZyB0aGUgdHJhbnNpdGlvbiBjbGFzc2VzLlxuXHRcdFx0XHQvLyBBZnRlciBzZXR0aW5nIHRoZSB0cmFuc2l0aW9uIGNsYXNzZXMsIHNldCB0aGVcblx0XHRcdFx0Ly8gZmluYWwgc2l6ZXNcblx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoYWRkQ2xhc3MpXG5cdFx0XHRcdFx0LmNzcyh7XG5cdFx0XHRcdFx0XHQgICAgIGhlaWdodDogaW5pdGlhbEhlaWdodCArICdweCcsXG5cdFx0XHRcdFx0XHQgICAgIHdpZHRoOiBpbml0aWFsV2lkdGggKyAncHgnLFxuXHRcdFx0XHRcdFx0ICAgICB2aXNpYmlsaXR5OiAnaW5pdGlhbCcsXG5cdFx0XHRcdFx0XHQgICAgIGRpc3BsYXk6ICdpbml0aWFsJ1xuXHRcdFx0XHRcdCAgICAgfSlcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJ3RyYW5zaXRpb24gJyArIGFkZENsYXNzKVxuXHRcdFx0XHRcdC5jc3Moe1xuXHRcdFx0XHRcdFx0ICAgICAnaGVpZ2h0JzogaGVpZ2h0ICsgJ3B4Jyxcblx0XHRcdFx0XHRcdCAgICAgJ3dpZHRoJzogd2lkdGggKyAncHgnXG5cdFx0XHRcdFx0ICAgICB9KTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdC8vIFNldHVwIHRoZSB0cmFuc2l0aW9uIGJ5IHNldHRpbmcgdGhlIHRyYW5zaXRpb24gY2xhc3Nlcy5cblx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoYWRkQ2xhc3MpXG5cdFx0XHRcdFx0LmFkZENsYXNzKCd0cmFuc2l0aW9uICcgKyBhZGRDbGFzcyk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIEFkZCBhbiBldmVudCBsaXN0ZW5lciB0byByZW1vdmUgYWxsIHVubmVjZXNzYXJ5XG5cdFx0XHQvLyBjbGFzc2VzIGFuZCBzdHlsZSBhdHRyaWJ1dGVzXG5cdFx0XHR2YXIgZHVyYXRpb24gPSBfZ2V0VHJhbnNpdGlvbkR1cmF0aW9uKCRzZWxmKTtcblx0XHRcdHRpbWVyW2RhdGFzZXQudWlkXSA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHQucmVtb3ZlQXR0cignc3R5bGUnKVxuXHRcdFx0XHRcdC5yZW1vdmVDbGFzcygndHJhbnNpdGlvbicpXG5cdFx0XHRcdFx0LnJlbW92ZURhdGEoJ3RyYW5zaXRpb24nKVxuXHRcdFx0XHRcdC50cmlnZ2VySGFuZGxlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTl9GSU5JU0hFRCgpKTtcblxuXHRcdFx0fSwgZHVyYXRpb24pO1xuXG5cdFx0XHQvLyBTdG9yZSB0aGUgY29uZmlndXJhdGlvbiBkYXRhIHRvIHRoZSB0YXJnZXQgb2JqZWN0XG5cdFx0XHQkc2VsZi5kYXRhKCd0cmFuc2l0aW9uJywgZGF0YXNldCk7XG5cdFx0XHQkY2xvbmUucmVtb3ZlKCk7XG5cdFx0fTtcblxuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciB0aGF0IHN0b3BzIGEgdHJhbnNpdGlvbiB0aW1lciBzZXRcblx0XHQgKiBieSB0aGUgX3RyYW5zaXRpb25IYW5kbGVyIGZ1bmN0aW9uLlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zdG9wVHJhbnNpdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0ZGF0YXNldCA9ICRzZWxmLmRhdGEoJ3RyYW5zaXRpb24nKSB8fCB7fTtcblxuXHRcdFx0aWYgKCEkLmlzRW1wdHlPYmplY3QoZGF0YXNldCkpIHtcblxuXHRcdFx0XHR0aW1lcltkYXRhc2V0LnVpZF0gPSAodGltZXJbZGF0YXNldC51aWRdKSA/IGNsZWFyVGltZW91dCh0aW1lcltkYXRhc2V0LnVpZF0pIDogbnVsbDtcblxuXHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdC5yZW1vdmVBdHRyKCdzdHlsZScpXG5cdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCd0cmFuc2l0aW9uJylcblx0XHRcdFx0XHQucmVtb3ZlRGF0YSgndHJhbnNpdGlvbicpXG5cdFx0XHRcdFx0LnRyaWdnZXJIYW5kbGVyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCkpO1xuXG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgX3RyYW5zaXRpb25IYW5kbGVyKVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fU1RPUCgpLCBfc3RvcFRyYW5zaXRpb24pO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
