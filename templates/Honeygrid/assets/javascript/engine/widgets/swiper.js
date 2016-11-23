'use strict';

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
gambio.widgets.module('swiper', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

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
	    sliderDefaults = { // Default configuration for the swiper
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
		breakpoints: [{
			// Until which breakpoint this settings is available
			breakpoint: 40,
			// If true, the paging bullets will be replaced with the preview images.
			usePreviewBullets: false,
			// This and all other settings belonging to the swiper plugin.
			slidesPerView: 2,
			// If true, the current slide gets centered in view (most usefull with an even slidesPerView
			// count).
			centeredSlides: true
		}, {
			breakpoint: 60,
			usePreviewBullets: true,
			slidesPerView: 3
		}, {
			breakpoint: 80,
			usePreviewBullets: true,
			slidesPerView: 3
		}, {
			breakpoint: 100,
			usePreviewBullets: true,
			slidesPerView: 5
		}]
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
	var _generatePreviewButtons = function _generatePreviewButtons(index, className) {
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
	var _getIndex = function _getIndex() {
		var index = $this.find('.swiper-slide-active').index();

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
	var _setActive = function _setActive(index) {
		$slides = $this.find('.swiper-slide:not(.swiper-slide-duplicate)');
		index = duplicates ? index + 1 : index;
		$slides.removeClass('active').eq(index).addClass('active');
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the mouseenter event.
  * It disables the autoplay
  * @private
  */
	var _mouseEnterHandler = function _mouseEnterHandler() {
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
	var _mouseLeaveHandler = function _mouseLeaveHandler() {
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
	var _gotoHandler = function _gotoHandler(e, d) {
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
	var _clickHandler = function _clickHandler(e) {
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
	var _triggerSlideChange = function _triggerSlideChange() {
		if (!preventSlideStart) {
			var index = _getIndex(),
			    lastIndex = $slides.length - 2;

			// Recalculate index if duplicate slides are inside the slider
			if (index < 0) {
				index = $slides.length - 3;
			} else {
				index = duplicates && index === lastIndex ? index - lastIndex : index;
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
	var _translucenceWorkaround = function _translucenceWorkaround() {
		if (!options.disableTranslucenceFix && sliderOptions && sliderOptions.effect === 'fade') {
			$this.find('.swiper-slide').filter(':not(.swiper-slide-active)').fadeTo(300, 0, function () {
				$(this).css('visibility', 'hidden');
			});

			$this.find('.swiper-slide').filter('.swiper-slide-active').fadeTo(300, 1, function () {
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
	var _breakpointHandler = function _breakpointHandler() {

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
			$.each(options.breakpoints, function (i, v) {
				if (v.breakpoint > newMode.id) {
					return false;
				}
				newBreakpointDataset = i;
				extendOptions = v;
			});

			if (options.sliderOptions && options.sliderOptions.breakpoints) {
				$.each(options.sliderOptions.breakpoints, function (i, v) {
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
				sliderOptions.autoplay = sliderOptions.autoplay ? sliderOptions.autoplay * 1000 : 0;

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

				if (!options.autoOff || $duplicate.length > sliderOptions.slidesPerView && options.autoOff) {
					$this.addClass('swiper-is-active').removeClass('swiper-is-not-active');

					// Initialize the swiper
					try {
						swiper = new Swiper($this, sliderOptions);
					} catch (e) {
						// Swiper might throw an error upon initialization that should not halt the 
						// script execution.
						return;
					}

					swiper.off('onTransitionEnd onSlideChangeStart').on('onTransitionEnd', _translucenceWorkaround);

					// If this is a "main" swiper and has external controls, an
					// goto event is triggered if the current slide is changed
					if ($controls.length) {
						swiper.on('onSlideChangeStart', _triggerSlideChange);
					}

					// Add the event handler
					$this.off('mouseenter.swiper mouseleave.swiper ' + jse.libs.template.events.SWIPER_GOTO() + ' ' + jse.libs.template.events.SLIDES_UPDATE()).on('mouseenter.swiper', _mouseEnterHandler).on('mouseleave.swiper', _mouseLeaveHandler).on(jse.libs.template.events.SWIPER_GOTO(), _gotoHandler).on(jse.libs.template.events.SLIDES_UPDATE(), _updateSlides);

					if (init) {
						// Check if there are duplicates slides (generated by the swiper)
						// after the first time init of the swiper
						duplicates = !!$this.find('.swiper-slide-duplicate').length;
					}

					// Set the active slide
					var index = init && options.initSlide ? options.initSlide : _getIndex();
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
					$this.removeClass('swiper-is-active').addClass('swiper-is-not-active');
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
	var _updateSlides = function _updateSlides(e, d) {

		// Loops through each category inside the images array
		$.each(d, function (category, dataset) {
			var catName = category + '-category',
			    add = [],
			    remove = [],
			    markup = $template.html();

			// Get all indexes from the slides
			// of the same category and remove
			// them from the slider
			$slides.filter('.' + catName).each(function () {
				var $self = $(this),
				    index = $self.data().swiperSlideIndex;

				index = index === undefined ? $self.index() : index;
				remove.push(index);
			});
			swiper.removeSlide(remove);

			// Generate the markup for the new slides
			// and add them to the slider
			$.each(dataset || [], function (i, v) {
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
	var _preventTextSelection = function _preventTextSelection() {
		$(options.sliderOptions.nextButton).on('selectstart', function () {
			return false;
		});
		$(options.sliderOptions.prevButton).on('selectstart', function () {
			return false;
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$slides = $this.find('.swiper-slide');
		$controls = $(options.controls);
		$target = $(options.target);
		$template = $this.find('template');

		// Check if all images inside the swiper have
		// thumbnail image given
		$slides.each(function () {
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

		$(document).ready(function () {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc3dpcGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkc2xpZGVzIiwiJGNvbnRyb2xzIiwiJHRhcmdldCIsIiR0ZW1wbGF0ZSIsImluaXQiLCJzd2lwZXIiLCJzbGlkZXJPcHRpb25zIiwiaGFzVGh1bWJuYWlscyIsIm1vZGUiLCJicmVha3BvaW50RGF0YXNldCIsImR1cGxpY2F0ZXMiLCJwcmV2ZW50U2xpZGVTdGFydCIsInNsaWRlckRlZmF1bHRzIiwicGFnaW5hdGlvbiIsIm5leHRCdXR0b24iLCJwcmV2QnV0dG9uIiwicGFnaW5hdGlvbkNsaWNrYWJsZSIsImxvb3AiLCJhdXRvcGxheSIsImF1dG9wbGF5RGlzYWJsZU9uSW50ZXJhY3Rpb24iLCJkZWZhdWx0cyIsImNvbnRyb2xzIiwidGFyZ2V0IiwiaW5pdFNsaWRlIiwiYXV0b09mZiIsImRpc2FibGVUcmFuc2x1Y2VuY2VGaXgiLCJicmVha3BvaW50cyIsImJyZWFrcG9pbnQiLCJ1c2VQcmV2aWV3QnVsbGV0cyIsInNsaWRlc1BlclZpZXciLCJjZW50ZXJlZFNsaWRlcyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2VuZXJhdGVQcmV2aWV3QnV0dG9ucyIsImluZGV4IiwiY2xhc3NOYW1lIiwiJGN1cnJlbnRTbGlkZSIsImVxIiwiJGltYWdlIiwiZmluZCIsImFsdFR4dCIsImF0dHIiLCJ0aHVtYkltYWdlIiwiX2dldEluZGV4IiwiX3NldEFjdGl2ZSIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJfbW91c2VFbnRlckhhbmRsZXIiLCJzdG9wQXV0b3BsYXkiLCJlIiwiX21vdXNlTGVhdmVIYW5kbGVyIiwic3RhcnRBdXRvcGxheSIsIl9nb3RvSGFuZGxlciIsImQiLCJzdG9wUHJvcGFnYXRpb24iLCJvZmYiLCJsZW5ndGgiLCJzbGlkZVRvIiwiX2NsaWNrSGFuZGxlciIsInByZXZlbnREZWZhdWx0IiwiJHNlbGYiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwiZXZlbnRzIiwiU1dJUEVSX0dPVE8iLCJfdHJpZ2dlclNsaWRlQ2hhbmdlIiwibGFzdEluZGV4IiwiX3RyYW5zbHVjZW5jZVdvcmthcm91bmQiLCJlZmZlY3QiLCJmaWx0ZXIiLCJmYWRlVG8iLCJjc3MiLCJfYnJlYWtwb2ludEhhbmRsZXIiLCJvbGRNb2RlIiwibmV3TW9kZSIsInJlc3BvbnNpdmUiLCJleHRlbmRPcHRpb25zIiwibmV3QnJlYWtwb2ludERhdGFzZXQiLCJpZCIsImVhY2giLCJpIiwidiIsInBhZ2luYXRpb25CdWxsZXRSZW5kZXIiLCJpbml0aWFsU2xpZGUiLCJkZXN0cm95IiwiaWdub3JlIiwiJGR1cGxpY2F0ZSIsIlN3aXBlciIsIm9uIiwiU0xJREVTX1VQREFURSIsIl91cGRhdGVTbGlkZXMiLCJjYXRlZ29yeSIsImRhdGFzZXQiLCJjYXROYW1lIiwiYWRkIiwicmVtb3ZlIiwibWFya3VwIiwiaHRtbCIsInN3aXBlclNsaWRlSW5kZXgiLCJ1bmRlZmluZWQiLCJwdXNoIiwicmVtb3ZlU2xpZGUiLCJzcmNhdHRyIiwic3JjIiwiTXVzdGFjaGUiLCJyZW5kZXIiLCJhcHBlbmRTbGlkZSIsIl9wcmV2ZW50VGV4dFNlbGVjdGlvbiIsImRvbmUiLCJCUkVBS1BPSU5UIiwiZG9jdW1lbnQiLCJyZWFkeSIsImZhZGVJbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOztBQUVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxRQURELEVBR0MsQ0FDQ0YsT0FBT0csTUFBUCxHQUFnQixjQURqQixFQUVDSCxPQUFPRyxNQUFQLEdBQWdCLGtCQUZqQixDQUhELEVBUUMsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVGOztBQUVFLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsUUFBUUQsRUFBRSxNQUFGLENBRFQ7QUFBQSxLQUVDRSxVQUFVLElB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QSxXQUFPLEtBQVA7QUFDQTtBQUNELEdBTEQ7O0FBT0E7QUFDQTtBQUNBUixRQUFNMkYsRUFBTixDQUFTNUIsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxNQUFsQixDQUF5QjhDLFVBQXpCLEVBQVQsRUFBZ0RyQyxrQkFBaEQ7QUFDQUE7O0FBRUE7QUFDQTtBQUNBLE1BQUkzQyxRQUFRVCxNQUFaLEVBQW9CO0FBQ25CekIsU0FBTTZGLEVBQU4sQ0FBUyxjQUFULEVBQXlCLGVBQXpCLEVBQTBDaEMsYUFBMUM7QUFDQTs7QUFFRDVELElBQUVrSCxRQUFGLEVBQVlDLEtBQVosQ0FBa0IsWUFBVztBQUM1Qm5ILEtBQUUsNENBQUYsRUFBZ0QyRSxHQUFoRCxDQUFvRCxTQUFwRCxFQUErRCxjQUEvRDtBQUNBM0UsS0FBRSwrQ0FBRixFQUFtRDJFLEdBQW5ELENBQXVELFNBQXZELEVBQWtFLFNBQWxFO0FBQ0EzRSxLQUFFLHVGQUFGLEVBQTJGb0gsTUFBM0YsQ0FBa0csSUFBbEc7QUFDQSxHQUpEOztBQU1BN0M7QUFDQXdDOztBQUVBQztBQUNBLEVBckNEOztBQXVDQTtBQUNBLFFBQU9wSCxNQUFQO0FBQ0EsQ0F6aEJGIiwiZmlsZSI6IndpZGdldHMvc3dpcGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzd2lwZXIuanMgMjAxNi0wOC0zMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgU3dpcGVyICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQgYmluZHMgdGhlIHN3aXBlciBwbHVnaW4gKHRoaXJkIHBhcnR5KSB0byBhIERPTSBlbGVtZW50XG4gKlxuICogQHRvZG8gUmVtb3ZlIHRoZSB0cnkgLSBjYXRjaCBibG9ja3MgYW5kIGFuZCBjb3JyZWN0IHRoZSBzd2lwZXIgaXNzdWVzLlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdzd2lwZXInLFxuXG5cdFtcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cycsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkc2xpZGVzID0gbnVsbCxcblx0XHRcdCRjb250cm9scyA9IG51bGwsXG5cdFx0XHQkdGFyZ2V0ID0gbnVsbCxcblx0XHRcdCR0ZW1wbGF0ZSA9IG51bGwsXG5cdFx0XHRpbml0ID0gdHJ1ZSxcblx0XHRcdHN3aXBlciA9IG51bGwsXG5cdFx0XHRzbGlkZXJPcHRpb25zID0gbnVsbCxcblx0XHRcdGhhc1RodW1ibmFpbHMgPSB0cnVlLFxuXHRcdFx0bW9kZSA9IG51bGwsXG5cdFx0XHRicmVha3BvaW50RGF0YXNldCA9IG51bGwsXG5cdFx0XHRkdXBsaWNhdGVzID0gZmFsc2UsXG5cdFx0XHRwcmV2ZW50U2xpZGVTdGFydCA9IGZhbHNlLFxuXHRcdFx0c2xpZGVyRGVmYXVsdHMgPSB7ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBEZWZhdWx0IGNvbmZpZ3VyYXRpb24gZm9yIHRoZSBzd2lwZXJcblx0XHRcdFx0cGFnaW5hdGlvbjogJy5zd2lwZXItcGFnaW5hdGlvbicsXG5cdFx0XHRcdG5leHRCdXR0b246ICcuc3dpcGVyLWJ1dHRvbi1uZXh0Jyxcblx0XHRcdFx0cHJldkJ1dHRvbjogJy5zd2lwZXItYnV0dG9uLXByZXYnLFxuXHRcdFx0XHRwYWdpbmF0aW9uQ2xpY2thYmxlOiB0cnVlLFxuXHRcdFx0XHRsb29wOiB0cnVlLFxuXHRcdFx0XHRhdXRvcGxheTogMyxcblx0XHRcdFx0YXV0b3BsYXlEaXNhYmxlT25JbnRlcmFjdGlvbjogZmFsc2Vcblx0XHRcdH0sXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Ly8gSlNPTiB0aGF0IGdldHMgbWVyZ2VkIHdpdGggdGhlIHNsaWRlckRlZmF1bHRzIGFuZCBpcyBwYXNzZWQgdG8gXCJzd2lwZXJcIiBkaXJlY3RseS5cblx0XHRcdFx0c2xpZGVyT3B0aW9uczogbnVsbCxcblx0XHRcdFx0Ly8gSWYgdGhpcyBpbnN0YW5jZSBpcyBhIFwibWFpblwiIHN3aXBlciwgdGhlIGdpdmVuIHNlbGVjdG9yIHNlbGVjdHMgdGhlIFwiY29udHJvbFwiIHN3aXBlci5cblx0XHRcdFx0Y29udHJvbHM6IG51bGwsXG5cdFx0XHRcdC8vIElmIHRoaXMgaW5zdGFuY2UgaXMgYSBcImNvbnRyb2xcIiBzd2lwZXIsIHRoZSBnaXZlbiBzZWxlY3RvciBzZWxlY3RzIHRoZSBcIm1haW5cIiBzd2lwZXIuXG5cdFx0XHRcdHRhcmdldDogbnVsbCxcblx0XHRcdFx0Ly8gU2V0cyB0aGUgaW5pdGlhbCBzbGlkZSAobmVlZGVkIHRvIHByZXZlbnQgZGlmZmVyZW50IGluaXQgc2xpZGVzIGluIG1haW4vY29udHJvbGxlciBzbGlkZXIpLlxuXHRcdFx0XHRpbml0U2xpZGU6IG51bGwsXG5cdFx0XHRcdC8vIERldGVjdCBpZiBhIHN3aXBlciBpcyBuZWVkZWQgZm9yIHRoZSBicmVha3BvaW50LiBJZiBub3QsIHR1cm4gaXQgb2ZmXG5cdFx0XHRcdGF1dG9PZmY6IGZhbHNlLFxuXHRcdFx0XHQvLyBUaGUgdHJhbnNsdWNlbmNlIGZpeCBlbmFibGVzIHN1cHBvcnQgZm9yIGEgZmFkZSBlZmZlY3QgYmV0d2VlbiBpbWFnZXMgd2l0aCBkaWZmZXJlbnQgYXNwZWN0IHJhdGlvLFxuXHRcdFx0XHQvLyBidXQgY2F1c2luZyBhIGRlbGF5IGJldHdlZW4gdGhlIGNoYW5nZVxuXHRcdFx0XHRkaXNhYmxlVHJhbnNsdWNlbmNlRml4OiBmYWxzZSxcblx0XHRcdFx0YnJlYWtwb2ludHM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQvLyBVbnRpbCB3aGljaCBicmVha3BvaW50IHRoaXMgc2V0dGluZ3MgaXMgYXZhaWxhYmxlXG5cdFx0XHRcdFx0XHRicmVha3BvaW50OiA0MCxcblx0XHRcdFx0XHRcdC8vIElmIHRydWUsIHRoZSBwYWdpbmcgYnVsbGV0cyB3aWxsIGJlIHJlcGxhY2VkIHdpdGggdGhlIHByZXZpZXcgaW1hZ2VzLlxuXHRcdFx0XHRcdFx0dXNlUHJldmlld0J1bGxldHM6IGZhbHNlLFxuXHRcdFx0XHRcdFx0Ly8gVGhpcyBhbmQgYWxsIG90aGVyIHNldHRpbmdzIGJlbG9uZ2luZyB0byB0aGUgc3dpcGVyIHBsdWdpbi5cblx0XHRcdFx0XHRcdHNsaWRlc1BlclZpZXc6IDIsXG5cdFx0XHRcdFx0XHQvLyBJZiB0cnVlLCB0aGUgY3VycmVudCBzbGlkZSBnZXRzIGNlbnRlcmVkIGluIHZpZXcgKG1vc3QgdXNlZnVsbCB3aXRoIGFuIGV2ZW4gc2xpZGVzUGVyVmlld1xuXHRcdFx0XHRcdFx0Ly8gY291bnQpLlxuXHRcdFx0XHRcdFx0Y2VudGVyZWRTbGlkZXM6IHRydWVcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGJyZWFrcG9pbnQ6IDYwLFxuXHRcdFx0XHRcdFx0dXNlUHJldmlld0J1bGxldHM6IHRydWUsXG5cdFx0XHRcdFx0XHRzbGlkZXNQZXJWaWV3OiAzXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRicmVha3BvaW50OiA4MCxcblx0XHRcdFx0XHRcdHVzZVByZXZpZXdCdWxsZXRzOiB0cnVlLFxuXHRcdFx0XHRcdFx0c2xpZGVzUGVyVmlldzogM1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0YnJlYWtwb2ludDogMTAwLFxuXHRcdFx0XHRcdFx0dXNlUHJldmlld0J1bGxldHM6IHRydWUsXG5cdFx0XHRcdFx0XHRzbGlkZXNQZXJWaWV3OiA1XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBnZW5lcmF0ZXMgdGhlIG1hcmt1cCBmb3Jcblx0XHQgKiB0aGUgcHJldmlldyBidWxsZXRzXG5cdFx0ICogQHBhcmFtICAgICAgIHtpbnRlZ2VyfSAgICAgICBpbmRleCAgICAgICAgICAgSW5kZXggb2YgdGhlIHNsaWRlXG5cdFx0ICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgICAgICBjbGFzc05hbWUgICAgICAgVGhlIGNsYXNzbmFtZSB0aGF0IG11c3QgYmUgYWRkIHRvIHRoZSBtYXJrdXBcblx0XHQgKiBAcmV0dXJuICAgICB7c3RyaW5nfSAgICAgICAgICAgICAgICAgICAgICAgIFRoZSBwcmV2aWV3IGltYWdlIGh0bWwgc3RyaW5nXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2dlbmVyYXRlUHJldmlld0J1dHRvbnMgPSBmdW5jdGlvbihpbmRleCwgY2xhc3NOYW1lKSB7XG5cdFx0XHR2YXIgJGN1cnJlbnRTbGlkZSA9ICRzbGlkZXMuZXEoaW5kZXgpLFxuXHRcdFx0XHQkaW1hZ2UgPSAkY3VycmVudFNsaWRlLmZpbmQoJ2ltZycpLFxuXHRcdFx0XHRhbHRUeHQgPSAkaW1hZ2UuYXR0cignYWx0JyksXG5cdFx0XHRcdHRodW1iSW1hZ2UgPSAkY3VycmVudFNsaWRlLmRhdGEoJ3RodW1iSW1hZ2UnKTtcblx0XHRcdFxuXHRcdFx0aWYgKHRodW1iSW1hZ2UpIHtcblx0XHRcdFx0cmV0dXJuICc8aW1nIHNyYz1cIicgKyB0aHVtYkltYWdlICsgJ1wiIGFsdD1cIicgKyBhbHRUeHQgKyAnXCIgY2xhc3M9XCInICsgY2xhc3NOYW1lICsgJ1wiIC8+Jztcblx0XHRcdH1cblxuXHRcdFx0cmV0dXJuICcnO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gZ2V0IHRoZSBpbmRleCBvZiB0aGVcblx0XHQgKiBhY3RpdmUgc2xpZGVcblx0XHQgKiBAcmV0dXJuICAgICB7aW50ZWdlcn0gICAgICAgICAgICAgICAgICAgICAgIFRoZSBpbmRleCBvZiB0aGUgYWN0aXZlIHNsaWRlXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2dldEluZGV4ID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgaW5kZXggPSAkdGhpc1xuXHRcdFx0XHQuZmluZCgnLnN3aXBlci1zbGlkZS1hY3RpdmUnKVxuXHRcdFx0XHQuaW5kZXgoKTtcblxuXHRcdFx0Ly8gSWYgdGhlcmUgYXJlIGR1cGxpY2F0ZSBzbGlkZXMgKGdlbmVyYXRlZFxuXHRcdFx0Ly8gYnkgdGhlIHN3aXBlcikgcmVjYWxjdWxhdGUgdGhlIGluZGV4XG5cdFx0XHRpbmRleCA9IGR1cGxpY2F0ZXMgPyBpbmRleCAtIDEgOiBpbmRleDtcblx0XHRcdGluZGV4ID0gaW5kZXggfHwgMDtcblxuXHRcdFx0cmV0dXJuIGluZGV4O1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gYWRkIHRoZSBhY3RpdmVcblx0XHQgKiBjbGFzcyB0byB0aGUgYWN0aXZlIHNsaWRlXG5cdFx0ICogQHBhcmFtICAgICAgIHtpbnRlZ2VyfSAgICAgICAgICAgaW5kZXggICAgICAgVGhlIGluZGV4IG9mIHRoZSBhY3RpdmUgc2xpZGVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0QWN0aXZlID0gZnVuY3Rpb24oaW5kZXgpIHtcblx0XHRcdCRzbGlkZXMgPSAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlOm5vdCguc3dpcGVyLXNsaWRlLWR1cGxpY2F0ZSknKTtcblx0XHRcdGluZGV4ID0gZHVwbGljYXRlcyA/IGluZGV4ICsgMSA6IGluZGV4O1xuXHRcdFx0JHNsaWRlc1xuXHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpXG5cdFx0XHRcdC5lcShpbmRleClcblx0XHRcdFx0LmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgbW91c2VlbnRlciBldmVudC5cblx0XHQgKiBJdCBkaXNhYmxlcyB0aGUgYXV0b3BsYXlcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VFbnRlckhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRyeSB7XG5cdFx0XHRcdGlmIChzd2lwZXIpIHtcblx0XHRcdFx0XHRzd2lwZXIuc3RvcEF1dG9wbGF5KCk7XG5cdFx0XHRcdH1cblx0XHRcdH0gY2F0Y2ggKGUpIHtcblx0XHRcdFx0Ly8gRG8gbm90IGxvZyB0aGUgZXJyb3Jcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlbGVhdmUgZXZlbnQuXG5cdFx0ICogSXQgZW5hYmxlcyB0aGUgYXV0b3BsYXlcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VMZWF2ZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRyeSB7XG5cdFx0XHRcdGlmIChzd2lwZXIpIHtcblx0XHRcdFx0XHRzd2lwZXIuc3RhcnRBdXRvcGxheSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9IGNhdGNoIChlKSB7XG5cdFx0XHRcdC8vIERvIG5vdCBsb2cgdGhlIGVycm9yXG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBnb3RvIGV2ZW50LlxuXHRcdCAqIEl0IHN3aXRjaGVzIHRoZSBjdXJyZW50IHNsaWRlIHRvIHRoZSBnaXZlbiBpbmRleFxuXHRcdCAqIGFuZCBhZGRzIHRoZSBhY3RpdmUgY2xhc3MgdG8gdGhlIG5ldyBhY3RpdmUgc2xpZGVcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtudW1iZXJ9ICAgIGQgICAgICAgSW5kZXggb2YgdGhlIHNsaWRlIHRvIHNob3dcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ290b0hhbmRsZXIgPSBmdW5jdGlvbihlLCBkKSB7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHQvLyBTZXQgdGhlIGFjdGl2ZSBzbGlkZVxuXHRcdFx0X3NldEFjdGl2ZShkKTtcblxuXHRcdFx0Ly8gVGVtcG9yYXJ5IGRlYWN0aXZhdGUgdGhlIG9uU2xpZGVDaGFuZ2VTdGFydCBldmVudFxuXHRcdFx0Ly8gdG8gcHJldmVudCBsb29waW5nIHRocm91Z2ggdGhlIGdvdG8gLyBjaGFuZ2VTdGFydFxuXHRcdFx0Ly8gZXZlbnRzXG5cdFx0XHRwcmV2ZW50U2xpZGVTdGFydCA9IHRydWU7XG5cblx0XHRcdC8vIFJlbW92ZSB0aGUgYXV0b3BsYXkgYWZ0ZXIgYSBnb3RvIGV2ZW50XG5cdFx0XHQkdGhpcy5vZmYoJ21vdXNlbGVhdmUuc3dpcGVyJyk7XG5cdFx0XHRzd2lwZXIuc3RvcEF1dG9wbGF5KCk7XG5cblx0XHRcdC8vIFRyeSB0byBjb3JyZWN0IHRoZSBpbmRleCBiZXR3ZWVuIHNsaWRlcnNcblx0XHRcdC8vIHdpdGggYW5kIHdpdGhvdXQgZHVwbGljYXRlc1xuXHRcdFx0dmFyIGluZGV4ID0gZHVwbGljYXRlcyA/IGQgKyAxIDogZDtcblx0XHRcdGlmIChpbmRleCA+ICRzbGlkZXMubGVuZ3RoIC0gMSkge1xuXHRcdFx0XHRpbmRleCA9IDA7XG5cdFx0XHR9XG5cblx0XHRcdC8vIEdvdG8gdGhlIGRlc2lyZWQgc2xpZGVcblx0XHRcdHN3aXBlci5zbGlkZVRvKGluZGV4KTtcblxuXHRcdFx0Ly8gUmVhY3RpdmF0ZSB0aGUgb25TbGlkZUNoYW5nZUV2ZW50XG5cdFx0XHRwcmV2ZW50U2xpZGVTdGFydCA9IGZhbHNlO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBDbGljayBldmVudCBoYW5kbGVyIHRoYXQgdHJpZ2dlcnMgYVxuXHRcdCAqIFwiZ290b1wiIGV2ZW50IHRvIHRoZSB0YXJnZXQgc3dpcGVyXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0ZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0aW5kZXggPSAkc2VsZi5pbmRleCgpO1xuXG5cdFx0XHRpbmRleCA9IGR1cGxpY2F0ZXMgPyBpbmRleCAtIDEgOiBpbmRleDtcblxuXHRcdFx0Ly8gU2V0IHRoZSBhY3RpdmUgc2xpZGVcblx0XHRcdF9zZXRBY3RpdmUoaW5kZXgpO1xuXG5cdFx0XHQvLyBJbmZvcm0gdGhlIG1haW4gc3dpcGVyXG5cdFx0XHQkdGFyZ2V0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlNXSVBFUl9HT1RPKCksIGluZGV4KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgdGhhdCBnZXRzIHRyaWdnZXJlZCBvbiBzbGlkZUNoYW5nZS5cblx0XHQgKiBJZiB0aGUgc2xpZGUgZ2V0cyBjaGFuZ2VkLCB0aGUgY29udHJvbHNcblx0XHQgKiB3aWxsIGZvbGxvdyB0aGUgY3VycmVudCBzbGlkZSBpbiBwb3NpdGlvblxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF90cmlnZ2VyU2xpZGVDaGFuZ2UgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICghcHJldmVudFNsaWRlU3RhcnQpIHtcblx0XHRcdFx0dmFyIGluZGV4ID0gX2dldEluZGV4KCksXG5cdFx0XHRcdFx0bGFzdEluZGV4ID0gJHNsaWRlcy5sZW5ndGggLSAyO1xuXG5cblx0XHRcdFx0Ly8gUmVjYWxjdWxhdGUgaW5kZXggaWYgZHVwbGljYXRlIHNsaWRlcyBhcmUgaW5zaWRlIHRoZSBzbGlkZXJcblx0XHRcdFx0aWYgKGluZGV4IDwgMCkge1xuXHRcdFx0XHRcdGluZGV4ID0gJHNsaWRlcy5sZW5ndGggLSAzO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdGluZGV4ID0gKGR1cGxpY2F0ZXMgJiYgaW5kZXggPT09IGxhc3RJbmRleCkgPyBpbmRleCAtIGxhc3RJbmRleCA6IGluZGV4O1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0Ly8gU2V0IHRoZSBhY3RpdmUgc2xpZGVcblx0XHRcdFx0X3NldEFjdGl2ZShpbmRleCk7XG5cblx0XHRcdFx0Ly8gSW5mb3JtIHRoZSBjb250cm9sc1xuXHRcdFx0XHQkY29udHJvbHMudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuU1dJUEVSX0dPVE8oKSwgaW5kZXgpO1xuXHRcdFx0fVxuXHRcdH07XG5cblxuXHRcdC8qKlxuXHRcdCAqIFdvcmthcm91bmQgZm9yIHRoZSB0cmFuc2x1Y2VuY2UgaXNzdWVcblx0XHQgKiB0aGF0IGhhcHBlbnMgb24gc21hbGwgc2NyZWVucyB3aXRoIGVuYWJsZWRcblx0XHQgKiBmYWRlIGVmZmVjdC4gTWF5YmUgaXQgY2FuIGJlIHJlbW92ZWQsIGlmIHRoZVxuXHRcdCAqIHN3aXBlciBnZXRzIHVwZGF0ZWQgaXRzZWxmXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RyYW5zbHVjZW5jZVdvcmthcm91bmQgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICghb3B0aW9ucy5kaXNhYmxlVHJhbnNsdWNlbmNlRml4ICYmIHNsaWRlck9wdGlvbnMgJiYgc2xpZGVyT3B0aW9ucy5lZmZlY3QgPT09ICdmYWRlJykge1xuXHRcdFx0XHQkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlJylcblx0XHRcdFx0XHQuZmlsdGVyKCc6bm90KC5zd2lwZXItc2xpZGUtYWN0aXZlKScpXG5cdFx0XHRcdFx0LmZhZGVUbygzMDAsIDAsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5jc3MoJ3Zpc2liaWxpdHknLCAnaGlkZGVuJyk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlJylcblx0XHRcdFx0XHQuZmlsdGVyKCcuc3dpcGVyLXNsaWRlLWFjdGl2ZScpXG5cdFx0XHRcdFx0LmZhZGVUbygzMDAsIDEsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5jc3MoJ3Zpc2liaWxpdHknLCAnJyk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFRoZSBicmVha3BvaW50IGhhbmRsZXIgaW5pdGlhbGl6ZXMgdGhlIHN3aXBlclxuXHRcdCAqIHdpdGggdGhlIHNldHRpbmdzIGZvciB0aGUgY3VycmVudCBicmVha3BvaW50LlxuXHRcdCAqIFRoZXJlZm9yZSBpdCB1c2VzIHRoZSBkZWZhdWx0IHNsaWRlciBvcHRpb25zLFxuXHRcdCAqIHRoZSBjdXN0b20gc2xpZGVyIG9wdGlvbnMgZ2l2ZW4gYnkgdGhlIG9wdGlvbnNcblx0XHQgKiBvYmplY3QgYW5kIHRoZSBicmVha3BvaW50IG9wdGlvbnMgb2JqZWN0IGFsc29cblx0XHQgKiBnaXZlbiBieSB0aGUgb3B0aW9ucyAoaW4gdGhpcyBvcmRlcilcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblxuXHRcdFx0Ly8gR2V0IHRoZSBjdXJyZW50IHZpZXdtb2RlXG5cdFx0XHR2YXIgb2xkTW9kZSA9IG1vZGUgfHwge30sXG5cdFx0XHRcdG5ld01vZGUgPSBqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKSxcblx0XHRcdFx0ZXh0ZW5kT3B0aW9ucyA9IG9wdGlvbnMuYnJlYWtwb2ludHNbMF0gfHwge30sXG5cdFx0XHRcdG5ld0JyZWFrcG9pbnREYXRhc2V0ID0gbnVsbDtcblxuXHRcdFx0Ly8gT25seSBkbyBzb21ldGhpbmcgaWYgdGhlIHZpZXcgd2FzIGNoYW5nZWRcblx0XHRcdGlmIChuZXdNb2RlLmlkICE9PSBvbGRNb2RlLmlkKSB7XG5cblx0XHRcdFx0Ly8gU3RvcmUgdGhlIG5ldyB2aWV3bW9kZVxuXHRcdFx0XHRtb2RlID0gJC5leHRlbmQoe30sIG5ld01vZGUpO1xuXG5cdFx0XHRcdC8vIEl0ZXJhdGUgdGhyb3VnaCB0aGUgYnJlYWtwb2ludHMgb2JqZWN0IHRvIGRldGVjdFxuXHRcdFx0XHQvLyB0aGUgY29ycmVjdCBzZXR0aW5ncyBmb3IgdGhlIGN1cnJlbnQgYnJlYWtwb2ludFxuXHRcdFx0XHQkLmVhY2gob3B0aW9ucy5icmVha3BvaW50cywgZnVuY3Rpb24oaSwgdikge1xuXHRcdFx0XHRcdGlmICh2LmJyZWFrcG9pbnQgPiBuZXdNb2RlLmlkKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdG5ld0JyZWFrcG9pbnREYXRhc2V0ID0gaTtcblx0XHRcdFx0XHRleHRlbmRPcHRpb25zID0gdjtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAob3B0aW9ucy5zbGlkZXJPcHRpb25zICYmIG9wdGlvbnMuc2xpZGVyT3B0aW9ucy5icmVha3BvaW50cykge1xuXHRcdFx0XHRcdCQuZWFjaChvcHRpb25zLnNsaWRlck9wdGlvbnMuYnJlYWtwb2ludHMsIGZ1bmN0aW9uKGksIHYpIHtcblx0XHRcdFx0XHRcdGlmICh2LmJyZWFrcG9pbnQgPT09IG5ld01vZGUuaWQpIHtcblx0XHRcdFx0XHRcdFx0ZXh0ZW5kT3B0aW9ucyA9IHY7XG5cdFx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdC8vIE9ubHkgZG8gc29tZXRoaW5nIGlmIHRoZSBzZXR0aW5ncyBjaGFuZ2UgZHVlIGJyb3dzZXJcblx0XHRcdFx0Ly8gcmVzaXplIG9yIGlmIGl0J3MgdGhlIGZpcnN0IHRpbWUgcnVuXG5cdFx0XHRcdGlmIChuZXdCcmVha3BvaW50RGF0YXNldCAhPT0gYnJlYWtwb2ludERhdGFzZXQgfHwgaW5pdCkge1xuXHRcdFx0XHRcdC8vIENvbWJpbmUgdGhlIHNldHRpbmdzXG5cdFx0XHRcdFx0c2xpZGVyT3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBzbGlkZXJEZWZhdWx0cywgb3B0aW9ucy5zbGlkZXJPcHRpb25zIHx8IHt9LCBleHRlbmRPcHRpb25zKTtcblxuXHRcdFx0XHRcdC8vIEFkZCB0aGUgcHJldmlldyBpbWFnZSBidWxsZXRzIGZ1bmN0aW9uIHRvIHRoZSBvcHRpb25zIG9iamVjdFxuXHRcdFx0XHRcdGlmIChzbGlkZXJPcHRpb25zLnVzZVByZXZpZXdCdWxsZXRzICYmIGhhc1RodW1ibmFpbHMpIHtcblx0XHRcdFx0XHRcdHNsaWRlck9wdGlvbnMucGFnaW5hdGlvbkJ1bGxldFJlbmRlciA9IF9nZW5lcmF0ZVByZXZpZXdCdXR0b25zO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEFkZCB0aGUgYXV0b3BsYXkgaW50ZXJ2YWwgdG8gdGhlIG9wdGlvbnMgb2JqZWN0XG5cdFx0XHRcdFx0c2xpZGVyT3B0aW9ucy5hdXRvcGxheSA9IChzbGlkZXJPcHRpb25zLmF1dG9wbGF5KSA/IChzbGlkZXJPcHRpb25zLmF1dG9wbGF5ICogMTAwMCkgOiAwO1xuXG5cdFx0XHRcdFx0Ly8gSWYgYW4gc3dpcGVyIGV4aXN0cywgZ2V0IHRoZSBjdXJyZW50XG5cdFx0XHRcdFx0Ly8gc2xpZGUgbm8uIGFuZCByZW1vdmUgdGhlIG9sZCBzd2lwZXJcblx0XHRcdFx0XHRpZiAoc3dpcGVyKSB7XG5cdFx0XHRcdFx0XHRzbGlkZXJPcHRpb25zLmluaXRpYWxTbGlkZSA9IF9nZXRJbmRleCgpO1xuXHRcdFx0XHRcdFx0dHJ5IHtcblx0XHRcdFx0XHRcdFx0c3dpcGVyLmRlc3Ryb3kodHJ1ZSwgdHJ1ZSk7XG5cdFx0XHRcdFx0XHR9IGNhdGNoIChpZ25vcmUpIHtcblx0XHRcdFx0XHRcdFx0c3dpcGVyID0gbnVsbDtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRzbGlkZXJPcHRpb25zLmluaXRpYWxTbGlkZSA9IG9wdGlvbnMuaW5pdFNsaWRlIHx8IHNsaWRlck9wdGlvbnMuaW5pdGlhbFNsaWRlIHx8IDA7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0dmFyICRkdXBsaWNhdGUgPSAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlOm5vdCguc3dpcGVyLXNsaWRlLWR1cGxpY2F0ZSknKTtcblxuXHRcdFx0XHRcdGlmICghb3B0aW9ucy5hdXRvT2ZmIHx8ICgkZHVwbGljYXRlLmxlbmd0aCA+IHNsaWRlck9wdGlvbnMuc2xpZGVzUGVyVmlldyAmJiBvcHRpb25zLmF1dG9PZmYpKSB7XG5cdFx0XHRcdFx0XHQkdGhpc1xuXHRcdFx0XHRcdFx0XHQuYWRkQ2xhc3MoJ3N3aXBlci1pcy1hY3RpdmUnKVxuXHRcdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ3N3aXBlci1pcy1ub3QtYWN0aXZlJyk7XG5cblx0XHRcdFx0XHRcdC8vIEluaXRpYWxpemUgdGhlIHN3aXBlclxuXHRcdFx0XHRcdFx0dHJ5IHtcblx0XHRcdFx0XHRcdFx0c3dpcGVyID0gbmV3IFN3aXBlcigkdGhpcywgc2xpZGVyT3B0aW9ucyk7XG5cdFx0XHRcdFx0XHR9IGNhdGNoIChlKSB7XG5cdFx0XHRcdFx0XHRcdC8vIFN3aXBlciBtaWdodCB0aHJvdyBhbiBlcnJvciB1cG9uIGluaXRpYWxpemF0aW9uIHRoYXQgc2hvdWxkIG5vdCBoYWx0IHRoZSBcblx0XHRcdFx0XHRcdFx0Ly8gc2NyaXB0IGV4ZWN1dGlvbi5cblx0XHRcdFx0XHRcdFx0cmV0dXJuOyBcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0c3dpcGVyXG5cdFx0XHRcdFx0XHRcdC5vZmYoJ29uVHJhbnNpdGlvbkVuZCBvblNsaWRlQ2hhbmdlU3RhcnQnKVxuXHRcdFx0XHRcdFx0XHQub24oJ29uVHJhbnNpdGlvbkVuZCcsIF90cmFuc2x1Y2VuY2VXb3JrYXJvdW5kKTtcblxuXHRcdFx0XHRcdFx0Ly8gSWYgdGhpcyBpcyBhIFwibWFpblwiIHN3aXBlciBhbmQgaGFzIGV4dGVybmFsIGNvbnRyb2xzLCBhblxuXHRcdFx0XHRcdFx0Ly8gZ290byBldmVudCBpcyB0cmlnZ2VyZWQgaWYgdGhlIGN1cnJlbnQgc2xpZGUgaXMgY2hhbmdlZFxuXHRcdFx0XHRcdFx0aWYgKCRjb250cm9scy5sZW5ndGgpIHtcblx0XHRcdFx0XHRcdFx0c3dpcGVyLm9uKCdvblNsaWRlQ2hhbmdlU3RhcnQnLCBfdHJpZ2dlclNsaWRlQ2hhbmdlKTtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0Ly8gQWRkIHRoZSBldmVudCBoYW5kbGVyXG5cdFx0XHRcdFx0XHQkdGhpc1xuXHRcdFx0XHRcdFx0XHQub2ZmKCdtb3VzZWVudGVyLnN3aXBlciBtb3VzZWxlYXZlLnN3aXBlciAnICsganNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlNXSVBFUl9HT1RPKCkgKyAnICdcblx0XHRcdFx0XHRcdFx0ICAgICArIGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5TTElERVNfVVBEQVRFKCkpXG5cdFx0XHRcdFx0XHRcdC5vbignbW91c2VlbnRlci5zd2lwZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG5cdFx0XHRcdFx0XHRcdC5vbignbW91c2VsZWF2ZS5zd2lwZXInLCBfbW91c2VMZWF2ZUhhbmRsZXIpXG5cdFx0XHRcdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuU1dJUEVSX0dPVE8oKSwgX2dvdG9IYW5kbGVyKVxuXHRcdFx0XHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlNMSURFU19VUERBVEUoKSwgX3VwZGF0ZVNsaWRlcyk7XG5cblx0XHRcdFx0XHRcdGlmIChpbml0KSB7XG5cdFx0XHRcdFx0XHRcdC8vIENoZWNrIGlmIHRoZXJlIGFyZSBkdXBsaWNhdGVzIHNsaWRlcyAoZ2VuZXJhdGVkIGJ5IHRoZSBzd2lwZXIpXG5cdFx0XHRcdFx0XHRcdC8vIGFmdGVyIHRoZSBmaXJzdCB0aW1lIGluaXQgb2YgdGhlIHN3aXBlclxuXHRcdFx0XHRcdFx0XHRkdXBsaWNhdGVzID0gISEkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlLWR1cGxpY2F0ZScpLmxlbmd0aDtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0Ly8gU2V0IHRoZSBhY3RpdmUgc2xpZGVcblx0XHRcdFx0XHRcdHZhciBpbmRleCA9IChpbml0ICYmIG9wdGlvbnMuaW5pdFNsaWRlKSA/IG9wdGlvbnMuaW5pdFNsaWRlIDogX2dldEluZGV4KCk7XG5cdFx0XHRcdFx0XHRfc2V0QWN0aXZlKGluZGV4KTtcblxuXHRcdFx0XHRcdFx0Ly8gSW5mb3JtIHRoZSBjb250cm9scyB0aGF0IHRoZSBtYWluIHN3aXBlciBoYXMgY2hhbmdlZFxuXHRcdFx0XHRcdFx0Ly8gSW4gY2FzZSB0aGF0IHRoZSBvdGhlciBzbGlkZXIgaXNuJ3QgaW5pdGlhbGl6ZWQgeWV0LFxuXHRcdFx0XHRcdFx0Ly8gc2V0IGFuIGRhdGEgYXR0cmlidXRlIHRvIHRoZSBtYXJrdXAgZWxlbWVudCB0byBpbmZvcm1cblx0XHRcdFx0XHRcdC8vIGl0IG9uIGluaXRcblx0XHRcdFx0XHRcdGlmICgkY29udHJvbHMubGVuZ3RoKSB7XG5cdFx0XHRcdFx0XHRcdCRjb250cm9scy5hdHRyKCdkYXRhLXN3aXBlci1pbml0LXNsaWRlJywgaW5kZXgpO1xuXHRcdFx0XHRcdFx0XHRfdHJpZ2dlclNsaWRlQ2hhbmdlKCk7XG5cdFx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRcdC8vIFVuc2V0IHRoZSBpbml0IGZsYWdcblx0XHRcdFx0XHRcdGluaXQgPSBmYWxzZTtcblxuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHQvLyBEaXNhYmxlIHRoZSBzd2lwZXIgYnV0dG9uc1xuXHRcdFx0XHRcdFx0JHRoaXNcblx0XHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCdzd2lwZXItaXMtYWN0aXZlJylcblx0XHRcdFx0XHRcdFx0LmFkZENsYXNzKCdzd2lwZXItaXMtbm90LWFjdGl2ZScpO1xuXHRcdFx0XHRcdFx0aW5pdCA9IHRydWU7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cblx0XHRcdH1cblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIHRoYXQgYWRkcyAmIHJlbW92ZXMgc2xpZGVzIGZyb20gdGhlXG5cdFx0ICogc3dpcGVyLiBBZnRlciB0aGUgc2xpZGVzIHdlcmUgcHJvY2Vzc2VkLCB0aGUgZmlyc3Rcblx0XHQgKiBzbGlkZSBpcyBzaG93blxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZCAgICAgICBKU09OIG9iamVjdCB0aGF0IGNvbnRhaW5zIHRoZSBjYXRlZ29yaWVzIC8gaW1hZ2VzXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3VwZGF0ZVNsaWRlcyA9IGZ1bmN0aW9uKGUsIGQpIHtcblxuXHRcdFx0Ly8gTG9vcHMgdGhyb3VnaCBlYWNoIGNhdGVnb3J5IGluc2lkZSB0aGUgaW1hZ2VzIGFycmF5XG5cdFx0XHQkLmVhY2goZCwgZnVuY3Rpb24oY2F0ZWdvcnksIGRhdGFzZXQpIHtcblx0XHRcdFx0dmFyIGNhdE5hbWUgPSBjYXRlZ29yeSArICctY2F0ZWdvcnknLFxuXHRcdFx0XHRcdGFkZCA9IFtdLFxuXHRcdFx0XHRcdHJlbW92ZSA9IFtdLFxuXHRcdFx0XHRcdG1hcmt1cCA9ICR0ZW1wbGF0ZS5odG1sKCk7XG5cblx0XHRcdFx0Ly8gR2V0IGFsbCBpbmRleGVzIGZyb20gdGhlIHNsaWRlc1xuXHRcdFx0XHQvLyBvZiB0aGUgc2FtZSBjYXRlZ29yeSBhbmQgcmVtb3ZlXG5cdFx0XHRcdC8vIHRoZW0gZnJvbSB0aGUgc2xpZGVyXG5cdFx0XHRcdCRzbGlkZXNcblx0XHRcdFx0XHQuZmlsdGVyKCcuJyArIGNhdE5hbWUpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRpbmRleCA9ICRzZWxmLmRhdGEoKS5zd2lwZXJTbGlkZUluZGV4O1xuXG5cdFx0XHRcdFx0XHRpbmRleCA9IGluZGV4ID09PSB1bmRlZmluZWQgPyAkc2VsZi5pbmRleCgpIDogaW5kZXg7XG5cdFx0XHRcdFx0XHRyZW1vdmUucHVzaChpbmRleCk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdHN3aXBlci5yZW1vdmVTbGlkZShyZW1vdmUpO1xuXG5cdFx0XHRcdC8vIEdlbmVyYXRlIHRoZSBtYXJrdXAgZm9yIHRoZSBuZXcgc2xpZGVzXG5cdFx0XHRcdC8vIGFuZCBhZGQgdGhlbSB0byB0aGUgc2xpZGVyXG5cdFx0XHRcdCQuZWFjaChkYXRhc2V0IHx8IFtdLCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdFx0di5jbGFzc05hbWUgPSBjYXROYW1lO1xuXHRcdFx0XHRcdHYuc3JjYXR0ciA9ICdzcmM9XCInICsgdi5zcmMgKyAnXCInO1xuXHRcdFx0XHRcdGFkZC5wdXNoKE11c3RhY2hlLnJlbmRlcihtYXJrdXAsIHYpKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdHN3aXBlci5hcHBlbmRTbGlkZShhZGQpO1xuXG5cdFx0XHR9KTtcblxuXHRcdFx0JHNsaWRlcyA9ICR0aGlzLmZpbmQoJy5zd2lwZXItc2xpZGUnKTtcblxuXHRcdFx0Ly8gVG8gcHJldmVudCBhbiBpbmNvbnNpc3RlbnQgc3RhdGVcblx0XHRcdC8vIGluIGNvbnRyb2wgLyBtYWluIHNsaWRlciBjb21iaW5hdGlvbnNcblx0XHRcdC8vIHNsaWRlIHRvIHRoZSBmaXJzdCBzbGlkZVxuXHRcdFx0X3NldEFjdGl2ZSgwKTtcblx0XHRcdHZhciBpbmRleCA9IGR1cGxpY2F0ZXMgPyAxIDogMDtcblx0XHRcdHN3aXBlci5zbGlkZVRvKGluZGV4LCAwKTtcblxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUHJldmVudCB0ZXh0IHNlbGVjdGlvbiBieSBjbGlja2luZyBvbiBzd2lwZXIgYnV0dG9uc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9wcmV2ZW50VGV4dFNlbGVjdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JChvcHRpb25zLnNsaWRlck9wdGlvbnMubmV4dEJ1dHRvbikub24oJ3NlbGVjdHN0YXJ0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH0pO1xuXHRcdFx0JChvcHRpb25zLnNsaWRlck9wdGlvbnMucHJldkJ1dHRvbikub24oJ3NlbGVjdHN0YXJ0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH0pO1xuXHRcdH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRcdCRzbGlkZXMgPSAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlJyk7XG5cdFx0XHQkY29udHJvbHMgPSAkKG9wdGlvbnMuY29udHJvbHMpO1xuXHRcdFx0JHRhcmdldCA9ICQob3B0aW9ucy50YXJnZXQpO1xuXHRcdFx0JHRlbXBsYXRlID0gJHRoaXMuZmluZCgndGVtcGxhdGUnKTtcblxuXHRcdFx0Ly8gQ2hlY2sgaWYgYWxsIGltYWdlcyBpbnNpZGUgdGhlIHN3aXBlciBoYXZlXG5cdFx0XHQvLyB0aHVtYm5haWwgaW1hZ2UgZ2l2ZW5cblx0XHRcdCRzbGlkZXMuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCEkKHRoaXMpLmRhdGEoKS50aHVtYkltYWdlKSB7XG5cdFx0XHRcdFx0aGFzVGh1bWJuYWlscyA9IGZhbHNlO1xuXHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdC8vIEFkZCB0aGUgYnJlYWtwb2ludCBoYW5kbGVyIHR5IGR5bmFtaWNhbGx5XG5cdFx0XHQvLyBzZXQgdGhlIG9wdGlvbnMgY29ycmVzcG9uZGluZyB0byB0aGUgYnJvd3NlciBzaXplXG5cdFx0XHQkYm9keS5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBfYnJlYWtwb2ludEhhbmRsZXIpO1xuXHRcdFx0X2JyZWFrcG9pbnRIYW5kbGVyKCk7XG5cblx0XHRcdC8vIElmIHRoaXMgaW5zdGFuY2UgaXMgYSBcImNvbnRyb2xcIiBzd2lwZXIgdGhlIHRhcmdldCBpcyB0aGUgbWFpbiBzd2lwZXJcblx0XHRcdC8vIHdoaWNoIHdpbGwgYmUgdXBkYXRlZCBvbiBhIGNsaWNrIGluc2lkZSB0aGlzIGNvbnRyb2wgc3dpcGVyXG5cdFx0XHRpZiAob3B0aW9ucy50YXJnZXQpIHtcblx0XHRcdFx0JHRoaXMub24oJ2NsaWNrLnN3aXBlcicsICcuc3dpcGVyLXNsaWRlJywgX2NsaWNrSGFuZGxlcik7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkKCcuc3dpcGVyLXZlcnRpY2FsIC5zd2lwZXItc2xpZGVbZGF0YS1pbmRleF0nKS5jc3MoJ2Rpc3BsYXknLCAnaW5saW5lLWJsb2NrJyk7XG5cdFx0XHRcdCQoJy5wcm9kdWN0LWluZm8taW1hZ2UgLnN3aXBlci1zbGlkZVtkYXRhLWluZGV4XScpLmNzcygnei1pbmRleCcsICdpbmhlcml0Jyk7XG5cdFx0XHRcdCQoJy5wcm9kdWN0LWluZm8taW1hZ2UgLnN3aXBlci1zbGlkZVtkYXRhLWluZGV4XSAuc3dpcGVyLXNsaWRlLWluc2lkZSBpbWcuaW1nLXJlc3BvbnNpdmUnKS5mYWRlSW4oMTAwMCk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0X3RyYW5zbHVjZW5jZVdvcmthcm91bmQoKTtcblx0XHRcdF9wcmV2ZW50VGV4dFNlbGVjdGlvbigpO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
