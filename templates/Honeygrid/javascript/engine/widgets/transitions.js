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
gambio.widgets.module(
	'transitions',

	[
		gambio.source + '/libs/events'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			timer = [],
			defaults = {
				duration: 0.5,        // Default transition duration in seconds
				open: true,       // Is it a open or a close animation (needed to determine the correct classes)
				classClose: '',         // Class added during close transition
				classOpen: ''          // Class added during open animation
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
		var _getTransitionDuration = function($element) {

			var duration = options.duration;

			$element
				.add($element.children())
				.each(function() {
					var time = ($element.css('transition-duration') !== undefined)
						? $element.css('transition-duration')
						: ($element.css('-webkit-transtion-duration') !== undefined)
						           ? $element.css('-webkit-transtion-duration')
						           : ($element.css('-moz-transtion-duration') !== undefined)
							  ? $element.css('-moz-transtion-duration')
							  : ($element.css('-ms-transtion-duration') !== undefined)
							             ? $element.css('-ms-transtion-duration')
							             : ($element.css('-o-transtion-duration') !== undefined)
								    ? $element.css('-o-transtion-duration') : -1;

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
		var _transitionHandler = function(e, d) {

			var $self = $(e.target),
				$clone = $self.clone(), // Avoid hiding the original element, use a clone as a helper.
				dataset = $.extend({}, $self.data().transition || {}, d),
				removeClass = (dataset.open) ? dataset.classClose : dataset.classOpen,
				addClass = (dataset.open) ? dataset.classOpen : dataset.classClose,
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
			
			$self
				.removeAttr('style')
				.removeClass('transition ' + removeClass)
				.addClass(addClass);

			height = $self.outerHeight();
			width = $self.outerWidth();

			// Check if the container height needs to be set
			if (dataset.calcHeight) {
				// Setup the transition by setting the initial
				// values BEFORE adding the transition classes.
				// After setting the transition classes, set the
				// final sizes
				$self
					.removeClass(addClass)
					.css({
						     height: initialHeight + 'px',
						     width: initialWidth + 'px',
						     visibility: 'initial',
						     display: 'initial'
					     })
					.addClass('transition ' + addClass)
					.css({
						     'height': height + 'px',
						     'width': width + 'px'
					     });
			} else {
				// Setup the transition by setting the transition classes.
				$self
					.removeClass(addClass)
					.addClass('transition ' + addClass);
			}

			// Add an event listener to remove all unnecessary
			// classes and style attributes
			var duration = _getTransitionDuration($self);
			timer[dataset.uid] = setTimeout(function() {

				$self
					.removeAttr('style')
					.removeClass('transition')
					.removeData('transition')
					.triggerHandler(jse.libs.template.events.TRANSITION_FINISHED());

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
		var _stopTransition = function() {
			var $self = $(this),
				dataset = $self.data('transition') || {};

			if (!$.isEmptyObject(dataset)) {

				timer[dataset.uid] = (timer[dataset.uid]) ? clearTimeout(timer[dataset.uid]) : null;

				$self
					.removeAttr('style')
					.removeClass('transition')
					.removeData('transition')
					.triggerHandler(jse.libs.template.events.TRANSITION_FINISHED());

			}
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$this
				.on(jse.libs.template.events.TRANSITION(), _transitionHandler)
				.on(jse.libs.template.events.TRANSITION_STOP(), _stopTransition);

			done();
		};

		// Return data to widget engine
		return module;
	});
