/* --------------------------------------------------------------
 stickybox.js 2016-07-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that keeps an element between the two elements in view
 * 
 * @todo Refactor the animation technique so that it works correctly in tablet devices ("landscape" orientation).
 */
gambio.widgets.module(
	'stickybox',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$window = $(window),
			$header = null,
			$footer = null,
			$outerWrapper = null,
			bottom = null,
			top = null,
			elementHeight = null,
			elementWidth = null,
			elementOffset = null,
			fixedTopPosition = null,
			documentHeight = null,
			headerFixed = null,
			css = null,
			timer = null,
			initialOffset = null,
			initialTop = null,
			initialHeader = null,
			initialMarginTop = null,
			skipped = 0,
			checkFit = true,
			lastFit = null,
			defaults = {
				// The breakpoint, since which this script calculates the position
				breakpoint: 60,
				// Selector to set the header's margin top
				outerWrapper: '#outer-wrapper',
				// Selector to set the header height
				header: 'header',
				// Selector to set the footer height
				footer: '.product-info-listings, footer',
				// Reference selector to set the top position of the sticky box
				offsetTopReferenceSelector: '#breadcrumb_navi, .product-info',
				// Add a space between header/footer and content container
				marginTop: 15,
				// Add a space between header/footer and content container
				marginBottom: 0,
				// Sets the z-index in fixed mode
				zIndex: 1000,
				// If set to true, the number of events in "smoothness" gets skipped
				cpuOptimization: false,
				// The higher the value, the more scroll events gets skipped
				smoothness: 10,
				// The delay after the last scroll event the cpu optimization fires an recalculate event
				smoothnessDelay: 150,
				// Selector to set teaser slider height
				stage: '#stage',
				// Selector to set error box height
				errorBox: 'table.box-error, table.box-warning' 
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

// ########## HELPER FUNCTIONS ##########

		/**
		 * Calculates all necessary positions,
		 * offsets and dimensions
		 * @private
		 */
		var _calculateDimensions = function() {
			top = $header.outerHeight();
			bottom = $footer.offset().top;
			top += options.marginTop;
			bottom -= options.marginBottom;

			elementHeight = $this.outerHeight();
			elementWidth = $this.outerWidth();
			elementOffset = elementOffset || $this.offset();

			documentHeight = $(document).height();
			
			var cssTop = options.marginTop; 
			if (headerFixed) {
				cssTop = top;
			}
			
			css = {
				'position': 'fixed',
				'top': cssTop + 'px',
				'left': elementOffset.left + 'px',
				'z-index': options.zIndex,
				'width': elementWidth
			};
		};

		/**
		 * Checks if the available space between
		 * the header & footer is enough to set
		 * the container sticky
		 * @return         {boolean}           If true, there is enough space to set it sticky
		 * @private
		 */
		var _fitInView = function() {

			if (checkFit) {
				checkFit = false;

				_resetPosition();

				window.setTimeout(function() {
					checkFit = true;
				}, 100);

				lastFit = documentHeight - Math.abs(bottom - documentHeight) - top;

			}

			return lastFit > elementHeight;

		};

		/**
		 * Helper function that gets called on scroll. In case
		 * the content could be displayed without being sticky,
		 * the sticky-styles were removed, else a check is
		 * performed if the top of the element needs to be
		 * adjusted in case that it would overlap with the
		 * footer otherwise.
		 * @param       {number}     scrollPosition      Current scroll position of the page
		 * @private
		 */
		var _calcPosition = function(scrollPosition) {
			var newTop = initialTop - (initialHeader - top) + scrollPosition;
			
			if (headerFixed) {
				var elementBottom = scrollPosition + top + elementHeight + options.marginBottom,
					overlapping = elementBottom - bottom,
					currentTop = parseFloat($this.css('top'));
				
				newTop = (newTop < initialTop) ? initialTop : newTop;
				newTop -= overlapping - top;
				
				if (top + scrollPosition <= elementOffset.top) {
					_resetPosition();
				} else if (overlapping > 0) {
					if (bottom - scrollPosition < elementHeight + initialHeader - initialTop) {
						newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
						_resetPosition();
						$this.css({top: newTop + 'px'});
					} else if (Math.abs(currentTop - newTop) >= 0.5) {
						_resetPosition();
						$this.css({top: newTop + 'px'});
					}
				} else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
					$this.css(css);
				}
			} else {
				if (scrollPosition <= elementOffset.top - options.marginTop) {
					_resetPosition();
				} else if (bottom - scrollPosition + options.marginTop < elementHeight 
						- initialTop - options.marginTop) {
					newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
					_resetPosition();
					$this.css({top: newTop + 'px'});
				} else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
					$this.css(css);
				}
			}

		};

		/**
		 * In case that the CPU optimization
		 * is enabled, skipp a certain count
		 * of scroll events before recalculating
		 * the position.
		 * @return     {boolean}           True if this event shall be processed
		 * @private
		 */
		var _cpuOptimization = function() {
			skipped += 1;
			clearTimeout(timer);
			if (skipped < options.smoothness) {
				timer = setTimeout(function() {
					$window.trigger('scroll.stickybox', true);
				}, options.smoothnessDelay);
				return false;
			}
			skipped = 0;
			return true;
		};
		
		/**
		 * Set the initial top position of the sticky box. A correction is necessary, if the breadcrumb is longer than 
		 * one line.
		 * 
		 * @private
		 */
		var _fixInitialTopPosition = function() {
			var offsetTop = $this.offset().top,
				targetOffsetTop = $(options.offsetTopReferenceSelector).first().offset().top,
				offsetDifference = offsetTop - targetOffsetTop,
				topPosition = parseFloat($this.css('top'));
			
			fixedTopPosition = topPosition - offsetDifference;
			
			_resetPosition();
		};
		
		/**
		 * Restore initial position of the sticky box by removing its style attribute and setting the fixed 
		 * top position.
		 * 
		 * @private
		 */
		var _resetPosition = function() {
			$this.removeAttr('style');
			
			if (jse.libs.template.responsive.breakpoint().name === 'md'
				|| jse.libs.template.responsive.breakpoint().name === 'lg') {
				$this.css('top', fixedTopPosition + 'px');
			}
		};


// ########## EVENT HANDLER ##########

		/**
		 * Event handler for the scroll event. It gets the
		 * upper border of the content element and calls
		 * individual methods depending on the sticky state.
		 * To perform better on low end CPUs it checks if
		 * scroll events shall be skipped.
		 * @private
		 */
		var _checkPosition = function(e, d) {

			if (options.cpuOptimization && !d && !_cpuOptimization()) {
				return true;
			}

			if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {
				_calculateDimensions();
				var scrollPosition = $window.scrollTop(),
					fit = _fitInView();

				if (fit) {
					_calcPosition(scrollPosition);
				}
			}
		};

		/**
		 * Handler for the resize event. On browser
		 * resize it is resetting the state to calculate
		 * a new position
		 * @private
		 */
		var _resizeHandler = function() {
			_resetPosition();
			elementOffset = null;
			skipped = 0;
			initialOffset = $this.offset().top;

			_checkPosition();
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			var sliderHeight = 0, 
				errorBoxHeight = 0,
				marginTop = 0,
				marginBottom = 0;
			
			$outerWrapper = $(options.outerWrapper);
			$header = $(options.header);
			$footer = $(options.footer);
			
			if ($(options.stage).length > 0) {
				sliderHeight = $(options.stage).outerHeight();
			}
			
			$(options.errorBox).each(function() {
				marginTop    = parseInt($(this).css('margin-top'), 10);
				marginBottom = parseInt($(this).css('margin-bottom'), 10);
				
				errorBoxHeight += $(this).outerHeight();
				errorBoxHeight += marginTop;
				errorBoxHeight += marginBottom;
			});
			
			var errorBoxElements = $(options.errorBox).length;
			
			if (errorBoxElements >= 2) {
				errorBoxHeight = errorBoxHeight - (marginTop * (errorBoxElements - 1));
			}
			
			_fixInitialTopPosition();
			
			initialOffset = $this.offset().top;
			initialTop = parseFloat($this.css('top'));
			initialHeader = $header.outerHeight() + options.marginTop + sliderHeight + errorBoxHeight;
			initialMarginTop = parseFloat($outerWrapper.css('margin-top').replace(/[^\d]/, ''));
			headerFixed = $header.css('position') === 'fixed';
			
			if (jse.core.config.get('mobile')) {
				return done();
			}
			
			_checkPosition();

			$window
				.on('resize', _resizeHandler)
				.on('scroll.stickybox', _checkPosition)
				.on(jse.libs.template.events.REPOSITIONS_STICKYBOX(), _resizeHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});
