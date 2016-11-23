/* --------------------------------------------------------------
 image_gallery.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that opens the gallery modal layer (which is
 * used for the article pictures)
 */
gambio.widgets.module(
	'image_gallery',

	[
		gambio.source + '/libs/modal.ext-magnific',
		gambio.source + '/libs/modal',
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$template = null,
			$body = $('body'),
			layer = null,
			configuration = {                                       // Modal layer configuration
				noTemplate: false,
				preloader: true,
				closeOnOuter: true,
				dialogClass: 'product_images',
				gallery: {
					enabled: true
				}
			},
			defaults = {
				target: '.swiper-slide', // Selector for the click event listener
				template: '#product_image_layer', // Template that is used for the layer
				breakpoint: 40 // Maximum breakpoint for mobile view mode
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

// ########## EVENT HANDLER ##########

		/**
		 * Click event handler that configures the swiper(s)
		 * inside the layer and opens it afterwards
		 * @param       {object}    e       jQuery event object
		 * @private
		 */
		var _clickHandler = function(e) {
			e.preventDefault();

			// Only open in desktop mode
			if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {
				var $self = $(this),
					$swiper = $template.find('[data-swiper-slider-options]'),
					dataset = $self.data(),
					index = dataset.index || dataset.swiperSlideIndex || 0;

				// Loop that replaces the initial slide of
				// each swiper inside the layer
				$swiper.each(function() {
					$(this).attr('data-swiper-init-slide', index);
				});

				// Opens the modal layer
				layer = jse.libs.template.modal.custom(configuration);
			}

		};

		/**
		 * Handler which closes an opened gallery if the
		 * screen width gets under the size of an desktop mode
		 * @private
		 */
		var _breakpointHandler = function() {
			if (jse.libs.template.responsive.breakpoint().id <= options.breakpoint && layer) {
				layer.close(true);
			}
		};

		/**
		 * Event handler to append / remove slides from the
		 * gallery layer swipers
		 * @param       {object}        e           jQuery event object
		 * @param       {object}        d           JSON data of the images
		 * @private
		 */
		var _addSlides = function(e, d) {

			// Loops through all swipers inside the layer
			$template
				.find('.swiper-container template')
				.each(function() {
					var $tpl = $(this),
						$slideContainer = $tpl.siblings('.swiper-wrapper');

					// Loops through each category inside the images array
					$.each(d, function(category, dataset) {
						var catName = category + '-category',
							add = '',
							markup = $tpl.html();

						// Generate the markup for the new slides
						// and replace the old images of that category
						// eith the new ones
						$.each(dataset || [], function(i, v) {
							v.className = catName;
							add += Mustache.render(markup, v);
						});

						$slideContainer
							.find('.' + catName)
							.remove();

						$slideContainer.append(add);
					});
				});
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 *
		 * @constructor
		 */
		module.init = function(done) {
			configuration.template = options.template;
			$template = $(options.template);

			$this
				.on('click', options.target, _clickHandler)
				.on(jse.libs.template.events.SLIDES_UPDATE(), _addSlides);

			$body
				.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});
