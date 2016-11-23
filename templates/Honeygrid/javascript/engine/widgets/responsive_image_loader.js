/* --------------------------------------------------------------
 responsive_image_loader.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Example:
 *
 * <img src="img/testbild4_320.jpg" title="Testbild" alt="Testbild" data-image-xs="img/testbild4_320.jpg"
 *      data-image-sm="img/testbild4_640.jpg" data-image-md="img/testbild4_1024.jpg"
 *      data-image-lg="img/testbild4_1600.jpg"/>
 */
gambio.widgets.module(
	'responsive_image_loader',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {
				breakpoints: ['xs', 'sm', 'md', 'lg']
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

// ######### HELPER FUNCTIONS ##########

		/**
		 * Helper function that registers the ":attr"
		 * selector to jQuery. With this one it's possible
		 * to select elements with an regular expression
		 * @private
		 */
		var _registerSelector = function() {
			if ($.expr.pseudos.attr === undefined) {
				$.expr.pseudos.attr = $.expr.createPseudo(function(arg) {
					var regexp = new RegExp(arg);
					return function(elem) {
						for (var i = 0; i < elem.attributes.length; i++) {
							var attr = elem.attributes[i];
							if (regexp.test(attr.name)) {
								return true;
							}
						}
						return false;
					};
				});
			}
		};


// ########## MAIN FUNCTIONALITY ##########

		/**
		 * Function that searches for the best fitting image
		 * for the parent container, so that it can set the src-attribute
		 * inside the img-tag
		 * @param       {object}    $target     jQuery selection that contains the image to set (optional)
		 * @private
		 */
		var _resizeImages = function($target) {
			var $self = $(this),
				breakpoint = jse.libs.template.responsive.breakpoint(),
				$elements = ($target && $target.length) ? $target : $self
					.filter(':attr(^data-image)')
					.add($self.find(':attr(^data-image)'));

			// Iterate trough every image element
			// and check if there is a new image
			// size to set
			$elements
				.not('.lazyLoading')
				.each(function() {

					var $element = $(this),
						breakpoint = jse.libs.template.responsive.breakpoint(),
						bp = options.breakpoints.indexOf(breakpoint.name),
						bpCount = options.breakpoints.length,
						img = null;

					for (bp; bp < bpCount; bp += 1) {
						var attrName = 'data-image-' + options.breakpoints[bp],
							value = $element.attr(attrName);

						if (value) {
							img = value;
							break;
						}
					}

					if (!img) {
						img = $element.attr('data-image');
					}

					// If an image was found and the target element has a
					// different value inside it's src-attribute set the
					// new value
					if (img && $element.attr('src') !== img) {
						$element.attr('src', img);
					}
				});
		};


		/**
		 * Function that initializes the lazy loading
		 * capability of images
		 * @private
		 */
		var _registerLazyLoading = function() {
			var $elements = $this
				.filter('.lazyLoading:attr(^data-image)')
				.add($this.find('.lazyLoading:attr(^data-image)'));

			/**
			 * Function that scans the given elements for images
			 * that are in the viewport and set the source attribute
			 * @private
			 */
			var _lazyLoadingScrollHandler = function() {

				var windowWidth = $(window).width(),
					windowHeight = $(window).height(),
					top = $(window).scrollTop(),
					left = $(window).scrollLeft();

				$elements.each(function() {
					var $self = $(this),
						offset = $self.offset();

					if (offset.top < (top + windowHeight) || offset.left < (left + windowWidth)) {
						$elements = $elements.not($self);
						$self.trigger('lazyLoadImage');
					}
				});
			};

			/**
			 * Removes the class "lazyLoading" from the image
			 * so that the "_resizeImages" is able to select it
			 * Afterwards execute this function to set the
			 * correct image source
			 * @param       {object}    e       jQuery event object
			 * @private
			 */
			var _loadImage = function(e) {
				e.stopPropagation();

				var $self = $(this);
				$self.removeClass('lazyLoading');
				_resizeImages($self);
			};

			// Add an event handler for loading the first real image
			// to every image element that is only executed once
			$elements.each(function() {
				$(this).one('lazyLoadImage', _loadImage);
			});

			// Add event handler to every event that changes the dimension / viewport
			$(window).on('scroll windowWasResized', _lazyLoadingScrollHandler);

			// Load images that are in view on load
			_lazyLoadingScrollHandler();
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			_registerSelector();
			_registerLazyLoading();

			$(window).on(jse.libs.template.events.BREAKPOINT(), function() {
				_resizeImages.call($this);
			});

			_resizeImages.call($this);

			done();
		};

		// Return data to widget engine
		return module;
	});
