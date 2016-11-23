/* --------------------------------------------------------------
 swiper.js 2016-08-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Swiper */

/**
 * Widget that binds the swiper plugin (third party) to a DOM element
 *
 * @todo Remove the try - catch blocks and and correct the swiper issues.
 */
gambio.widgets.module(
	'swiper',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			$slides = null,
			$controls = null,
			$target = null,
			$template = null,
			init = true,
			swiper = null,
			sliderOptions = null,
			hasThumbnails = true,
			mode = null,
			breakpointDataset = null,
			duplicates = false,
			preventSlideStart = false,
			sliderDefaults = {                                   // Default configuration for the swiper
				pagination: '.swiper-pagination',
				nextButton: '.swiper-button-next',
				prevButton: '.swiper-button-prev',
				paginationClickable: true,
				loop: true,
				autoplay: 3,
				autoplayDisableOnInteraction: false
			},
			defaults = {
				// JSON that gets merged with the sliderDefaults and is passed to "swiper" directly.
				sliderOptions: null,
				// If this instance is a "main" swiper, the given selector selects the "control" swiper.
				controls: null,
				// If this instance is a "control" swiper, the given selector selects the "main" swiper.
				target: null,
				// Sets the initial slide (needed to prevent different init slides in main/controller slider).
				initSlide: null,
				// Detect if a swiper is needed for the breakpoint. If not, turn it off
				autoOff: false,
				// The translucence fix enables support for a fade effect between images with different aspect ratio,
				// but causing a delay between the change
				disableTranslucenceFix: false,
				breakpoints: [
					{
						// Until which breakpoint this settings is available
						breakpoint: 40,
						// If true, the paging bullets will be replaced with the preview images.
						usePreviewBullets: false,
						// This and all other settings belonging to the swiper plugin.
						slidesPerView: 2,
						// If true, the current slide gets centered in view (most usefull with an even slidesPerView
						// count).
						centeredSlides: true
					},
					{
						breakpoint: 60,
						usePreviewBullets: true,
						slidesPerView: 3
					},
					{
						breakpoint: 80,
						usePreviewBullets: true,
						slidesPerView: 3
					},
					{
						breakpoint: 100,
						usePreviewBullets: true,
						slidesPerView: 5
					}
				]
			},
			options = $.extend({}, defaults, data),
			module = {};


// ########## HELPER FUNCTIONS ##########

		/**
		 * Function that generates the markup for
		 * the preview bullets
		 * @param       {integer}       index           Index of the slide
		 * @param       {string}        className       The classname that must be add to the markup
		 * @return     {string}                        The preview image html string
		 * @private
		 */
		var _generatePreviewButtons = function(index, className) {
			var $currentSlide = $slides.eq(index),
				$image = $currentSlide.find('img'),
				altTxt = $image.attr('alt'),
				thumbImage = $currentSlide.data('thumbImage');
			
			if (thumbImage) {
				return '<img src="' + thumbImage + '" alt="' + altTxt + '" class="' + className + '" />';
			}

			return '';
		};

		/**
		 * Helper function to get the index of the
		 * active slide
		 * @return     {integer}                       The index of the active slide
		 * @private
		 */
		var _getIndex = function() {
			var index = $this
				.find('.swiper-slide-active')
				.index();

			// If there are duplicate slides (generated
			// by the swiper) recalculate the index
			index = duplicates ? index - 1 : index;
			index = index || 0;

			return index;
		};

		/**
		 * Helper function to add the active
		 * class to the active slide
		 * @param       {integer}           index       The index of the active slide
		 * @private
		 */
		var _setActive = function(index) {
			$slides = $this.find('.swiper-slide:not(.swiper-slide-duplicate)');
			index = duplicates ? index + 1 : index;
			$slides
				.removeClass('active')
				.eq(index)
				.addClass('active');
		};


// ########## EVENT HANDLER ##########

		/**
		 * Event handler for the mouseenter event.
		 * It disables the autoplay
		 * @private
		 */
		var _mouseEnterHandler = function() {
			try {
				if (swiper) {
					swiper.stopAutoplay();
				}
			} catch (e) {
				// Do not log the error
			}
		};

		/**
		 * Event handler for the mouseleave event.
		 * It enables the autoplay
		 * @private
		 */
		var _mouseLeaveHandler = function() {
			try {
				if (swiper) {
					swiper.startAutoplay();
				}
			} catch (e) {
				// Do not log the error
			}
		};

		/**
		 * Event handler for the goto event.
		 * It switches the current slide to the given index
		 * and adds the active class to the new active slide
		 * @param       {object}    e       jQuery event object
		 * @param       {number}    d       Index of the slide to show
		 * @private
		 */
		var _gotoHandler = function(e, d) {
			e.stopPropagation();

			// Set the active slide
			_setActive(d);

			// Temporary deactivate the onSlideChangeStart event
			// to prevent looping through the goto / changeStart
			// events
			preventSlideStart = true;

			// Remove the autoplay after a goto event
			$this.off('mouseleave.swiper');
			swiper.stopAutoplay();

			// Try to correct the index between sliders
			// with and without duplicates
			var index = duplicates ? d + 1 : d;
			if (index > $slides.length - 1) {
				index = 0;
			}

			// Goto the desired slide
			swiper.slideTo(index);

			// Reactivate the onSlideChangeEvent
			preventSlideStart = false;
		};

		/**
		 * Click event handler that triggers a
		 * "goto" event to the target swiper
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _clickHandler = function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $self = $(this),
				index = $self.index();

			index = duplicates ? index - 1 : index;

			// Set the active slide
			_setActive(index);

			// Inform the main swiper
			$target.trigger(jse.libs.template.events.SWIPER_GOTO(), index);
		};

		/**
		 * Event that gets triggered on slideChange.
		 * If the slide gets changed, the controls
		 * will follow the current slide in position
		 * @private
		 */
		var _triggerSlideChange = function() {
			if (!preventSlideStart) {
				var index = _getIndex(),
					lastIndex = $slides.length - 2;


				// Recalculate index if duplicate slides are inside the slider
				if (index < 0) {
					index = $slides.length - 3;
				} else {
					index = (duplicates && index === lastIndex) ? index - lastIndex : index;
				}

				// Set the active slide
				_setActive(index);

				// Inform the controls
				$controls.trigger(jse.libs.template.events.SWIPER_GOTO(), index);
			}
		};


		/**
		 * Workaround for the translucence issue
		 * that happens on small screens with enabled
		 * fade effect. Maybe it can be removed, if the
		 * swiper gets updated itself
		 * @private
		 */
		var _translucenceWorkaround = function() {
			if (!options.disableTranslucenceFix && sliderOptions && sliderOptions.effect === 'fade') {
				$this.find('.swiper-slide')
					.filter(':not(.swiper-slide-active)')
					.fadeTo(300, 0, function() {
						$(this).css('visibility', 'hidden');
					});
				
				$this.find('.swiper-slide')
					.filter('.swiper-slide-active')
					.fadeTo(300, 1, function() {
						$(this).css('visibility', '');
					});
			}
		};

		/**
		 * The breakpoint handler initializes the swiper
		 * with the settings for the current breakpoint.
		 * Therefore it uses the default slider options,
		 * the custom slider options given by the options
		 * object and the breakpoint options object also
		 * given by the options (in this order)
		 * @private
		 */
		var _breakpointHandler = function() {

			// Get the current viewmode
			var oldMode = mode || {},
				newMode = jse.libs.template.responsive.breakpoint(),
				extendOptions = options.breakpoints[0] || {},
				newBreakpointDataset = null;

			// Only do something if the view was changed
			if (newMode.id !== oldMode.id) {

				// Store the new viewmode
				mode = $.extend({}, newMode);

				// Iterate through the breakpoints object to detect
				// the correct settings for the current breakpoint
				$.each(options.breakpoints, function(i, v) {
					if (v.breakpoint > newMode.id) {
						return false;
					}
					newBreakpointDataset = i;
					extendOptions = v;
				});
				
				if (options.sliderOptions && options.sliderOptions.breakpoints) {
					$.each(options.sliderOptions.breakpoints, function(i, v) {
						if (v.breakpoint === newMode.id) {
							extendOptions = v;
							return false;
						}
					});
				}

				// Only do something if the settings change due browser
				// resize or if it's the first time run
				if (newBreakpointDataset !== breakpointDataset || init) {
					// Combine the settings
					sliderOptions = $.extend({}, sliderDefaults, options.sliderOptions || {}, extendOptions);

					// Add the preview image bullets function to the options object
					if (sliderOptions.usePreviewBullets && hasThumbnails) {
						sliderOptions.paginationBulletRender = _generatePreviewButtons;
					}

					// Add the autoplay interval to the options object
					sliderOptions.autoplay = (sliderOptions.autoplay) ? (sliderOptions.autoplay * 1000) : 0;

					// If an swiper exists, get the current
					// slide no. and remove the old swiper
					if (swiper) {
						sliderOptions.initialSlide = _getIndex();
						try {
							swiper.destroy(true, true);
						} catch (ignore) {
							swiper = null;
						}

					} else {
						sliderOptions.initialSlide = options.initSlide || sliderOptions.initialSlide || 0;
					}

					var $duplicate = $this.find('.swiper-slide:not(.swiper-slide-duplicate)');

					if (!options.autoOff || ($duplicate.length > sliderOptions.slidesPerView && options.autoOff)) {
						$this
							.addClass('swiper-is-active')
							.removeClass('swiper-is-not-active');

						// Initialize the swiper
						try {
							swiper = new Swiper($this, sliderOptions);
						} catch (e) {
							// Swiper might throw an error upon initialization that should not halt the 
							// script execution.
							return; 
						}

						swiper
							.off('onTransitionEnd onSlideChangeStart')
							.on('onTransitionEnd', _translucenceWorkaround);

						// If this is a "main" swiper and has external controls, an
						// goto event is triggered if the current slide is changed
						if ($controls.length) {
							swiper.on('onSlideChangeStart', _triggerSlideChange);
						}

						// Add the event handler
						$this
							.off('mouseenter.swiper mouseleave.swiper ' + jse.libs.template.events.SWIPER_GOTO() + ' '
							     + jse.libs.template.events.SLIDES_UPDATE())
							.on('mouseenter.swiper', _mouseEnterHandler)
							.on('mouseleave.swiper', _mouseLeaveHandler)
							.on(jse.libs.template.events.SWIPER_GOTO(), _gotoHandler)
							.on(jse.libs.template.events.SLIDES_UPDATE(), _updateSlides);

						if (init) {
							// Check if there are duplicates slides (generated by the swiper)
							// after the first time init of the swiper
							duplicates = !!$this.find('.swiper-slide-duplicate').length;
						}

						// Set the active slide
						var index = (init && options.initSlide) ? options.initSlide : _getIndex();
						_setActive(index);

						// Inform the controls that the main swiper has changed
						// In case that the other slider isn't initialized yet,
						// set an data attribute to the markup element to inform
						// it on init
						if ($controls.length) {
							$controls.attr('data-swiper-init-slide', index);
							_triggerSlideChange();
						}

						// Unset the init flag
						init = false;

					} else {
						// Disable the swiper buttons
						$this
							.removeClass('swiper-is-active')
							.addClass('swiper-is-not-active');
						init = true;
					}
				}

			}

		};

		/**
		 * Event handler that adds & removes slides from the
		 * swiper. After the slides were processed, the first
		 * slide is shown
		 * @param       {object}    e       jQuery event object
		 * @param       {object}    d       JSON object that contains the categories / images
		 * @private
		 */
		var _updateSlides = function(e, d) {

			// Loops through each category inside the images array
			$.each(d, function(category, dataset) {
				var catName = category + '-category',
					add = [],
					remove = [],
					markup = $template.html();

				// Get all indexes from the slides
				// of the same category and remove
				// them from the slider
				$slides
					.filter('.' + catName)
					.each(function() {
						var $self = $(this),
							index = $self.data().swiperSlideIndex;

						index = index === undefined ? $self.index() : index;
						remove.push(index);
					});
				swiper.removeSlide(remove);

				// Generate the markup for the new slides
				// and add them to the slider
				$.each(dataset || [], function(i, v) {
					v.className = catName;
					v.srcattr = 'src="' + v.src + '"';
					add.push(Mustache.render(markup, v));
				});
				swiper.appendSlide(add);

			});

			$slides = $this.find('.swiper-slide');

			// To prevent an inconsistent state
			// in control / main slider combinations
			// slide to the first slide
			_setActive(0);
			var index = duplicates ? 1 : 0;
			swiper.slideTo(index, 0);

		};
		
		/**
		 * Prevent text selection by clicking on swiper buttons
		 * @private
		 */
		var _preventTextSelection = function() {
			$(options.sliderOptions.nextButton).on('selectstart', function() {
				return false;
			});
			$(options.sliderOptions.prevButton).on('selectstart', function() {
				return false;
			});
		};

// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$slides = $this.find('.swiper-slide');
			$controls = $(options.controls);
			$target = $(options.target);
			$template = $this.find('template');

			// Check if all images inside the swiper have
			// thumbnail image given
			$slides.each(function() {
				if (!$(this).data().thumbImage) {
					hasThumbnails = false;
					return false;
				}
			});

			// Add the breakpoint handler ty dynamically
			// set the options corresponding to the browser size
			$body.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);
			_breakpointHandler();

			// If this instance is a "control" swiper the target is the main swiper
			// which will be updated on a click inside this control swiper
			if (options.target) {
				$this.on('click.swiper', '.swiper-slide', _clickHandler);
			}
			
			$(document).ready(function() {
				$('.swiper-vertical .swiper-slide[data-index]').css('display', 'inline-block');
				$('.product-info-image .swiper-slide[data-index]').css('z-index', 'inherit');
				$('.product-info-image .swiper-slide[data-index] .swiper-slide-inside img.img-responsive').fadeIn(1000);
			});
			
			_translucenceWorkaround();
			_preventTextSelection();

			done();
		};

		// Return data to widget engine
		return module;
	});
