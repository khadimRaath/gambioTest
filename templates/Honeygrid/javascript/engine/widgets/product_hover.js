/* --------------------------------------------------------------
 product_hover.js 2016-06-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that is used for the hover functionality
 * of the product tiles. It includes the functionality
 * for the image gallery inside the tile
 */
gambio.widgets.module(
	'product_hover',

	[
		gambio.source + '/libs/events'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$window = $(window),
			$body = $('body'),
			$container = null,
			timer = null,
			componentId = null,
			clickTimer = 0,
			defaults = {
				delay: 50,       // Delay in ms after which a hovered element gets closed after mouseleave
				flyoverClass: 'flyover',  // Class that gets added to every flyover
				scope: '',          // Sets the scope selector for the mouseover events
				container: '#wrapper', // Container selector which is the boundary for the cloned element
				productUrlSelector: '.product-url' // a tag selector of product's url
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

// ########## HELPER FUNCTIONS ##########

		/**
		 * Helper function to remove the opened flyovers that
		 * were appended to the body by this component
		 * @private
		 */
		var _removeFlyover = function(all) {
			var $flyover = $body.children('.' + options.flyoverClass);
			$flyover = all ? $flyover : $flyover.filter('.product-hover-' + componentId);

			$flyover.remove();
		};

		/**
		 * Helper function that replaces the preloader
		 * images with the real thumbnail images on
		 * layer creation. This is needed to save
		 * bandwidth
		 * @param       {object}    $clone      jQuery selection of the layer
		 * @private
		 */
		var _loadImages = function($clone) {
			$clone
				.find('.thumbnails img')
				.each(function() {

					var $self = $(this),
						$img = $('<img />'),
						dataset = $self.data(),
						src = dataset.thumbSrc || dataset.src,
						$parentListItem = null;

					$img.on('load', function() {
						$parentListItem = $self.closest('li');
						$parentListItem
							.addClass('loaded')
							.css({
								     'background': '#FFFFFF url("' + src + '") no-repeat center',
								     'background-size': 'contain'
							     })
							.find('img, .align-helper')
							.remove();
					}).attr('src', src);

				});
		};


// ########## EVENT HANDLER ##########

		/**
		 * Handler for the click event on the thumbnail
		 * images. After a click on such an image the
		 * main image of the hover element gets replaced
		 * with the bigger version of the thumbnail image
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _mouseEnterThumbHandler = function(e) {
			e.preventDefault();

			var $img = $(this),
				$container = $img.closest('.' + options.flyoverClass),
				dataSrc = $img.css('background-image');
			
			dataSrc = dataSrc.replace('/thumbnail_images/', '/info_images/');

			if (dataSrc) {
				$container
					.find('.product-hover-main-image')
					.css('background-image', dataSrc);
			}
		};

		/**
		 * Event handler for the mouse leave event of the
		 * hovered element. It sets a timer to remove the
		 * hover element after a certain time
		 * @param       {object}    e       jQuery event object
		 * @private
		 */
		var _mouseLeaveHandler = function(e) {
			e.stopPropagation();
			timer = timer ? clearTimeout(timer) : null;
			timer = window.setTimeout(_removeFlyover, options.delay);
		};

		/**
		 * Event handler for the mouse enter event on both
		 * elements (initial & hovered element).
		 * It clones the initial element and adds the clone
		 * to the body. It additionally adds functionality
		 * for the image gallery inside the hovered element
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _mouseEnterHandler = function(e) {
			e.stopPropagation();

			var $self = $(this),
				$clone = null,
				$target = $body,
				uid = $self.data().uid || parseInt(Math.random() * 10000, 10),
				$flyover = $target.children('.' + options.flyoverClass + '.product-hover-' + componentId
				                            + '[data-product_hover-uid="' + uid + '"]'),
				offset = $self.offset();

			timer = timer ? clearTimeout(timer) : null;

			// Check if flyover needs to be created
			if (!$self.hasClass(options.flyoverClass) && !$flyover.length) {
				// Remove old opened flyovers
				_removeFlyover(true);
				$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), $this);

				// Add a UID for identification to th hovered object
				$self
					.attr('data-product_hover-uid', uid)
					.data('uid', uid);

				// Generate the markup
				$clone = $self.clone(true);

				// Replace the preloader images with the thumbnail images
				_loadImages($clone);

				// Set the positioning of the layer
				$clone
					.addClass(options.flyoverClass + ' product-hover-' + componentId)
					.css({
						     'position': 'absolute',
						     'left': offset.left,
						     'top': offset.top,
						     'width': $self[0].getBoundingClientRect().width,
						     'height': $self[0].getBoundingClientRect().height
					     });

				// Add event listener to the hover elements
				$clone
					.on('mouseenter', _mouseEnterHandler)
					.on('mouseleave', _mouseLeaveHandler)
					.on('mouseenter', '.thumbnails', _mouseEnterThumbHandler)
					.on('click', _clickHandler);

				// Add the element to the body element
				$body.append($clone);

				if ($container.offset().left > $clone.offset().left) {
					$clone.addClass('gallery-right');
				}
			}
		};

		/**
		 * Handler for the window resize event. It
		 * recalculates the position of the overlays
		 * @private
		 */
		var _resizeHandler = function() {

			var $flyover = $body.children('.' + options.flyoverClass + '.product-hover-' + componentId);

			$flyover.each(function() {
				var $self = $(this),
					uid = $self.data().uid,
					$source = $this.find('[data-product_hover-uid="' + uid + '"]'),
					offset = $source.offset();

				$self.css({
					          left: offset.left,
					          top: offset.top,
					          width: 2 * $source.outerWidth()
				          });
			});

		};

		/**
		 * Event handler that closes the flyovers
		 * if another flyover opens on the page
		 * @param       {object}        e           jQuery event object
		 * @param       {object}        d           jQuery selection of the event emitter
		 * @private
		 */
		var _closeLayers = function(e, d) {
			if ($this !== d) {
				_removeFlyover();
			}
		};
		
		
		/**
		 * Event handler that makes the flyover and product image clickable linking to the product details page
		 * 
		 * @param       {object}        e           jQuery event object
		 * @private
		 */
		var _clickHandler = function(e) {
			var $container = $(this);
			
			if ($(this).hasClass('product-container') === false) {
				$container = $(this).closest('.product-container');
			} 
			
			var $link = $container.find(options.productUrlSelector).first();
			
			if ($link.length) {
				var url = $link.attr('href');
				
				if (url !== undefined) {
					e.stopPropagation();
					e.preventDefault();
					
					// prevent double _clickHandler actions
					if (new Date().getTime() - clickTimer < 100) {
						return;
					} else {
						clickTimer = new Date().getTime();
					}
					
					switch (e.which)
					{
						// left click
						case 1:
							if (e.ctrlKey) {
								window.open(url, '_blank');
								return;
							}
							break;
						
						// middle click
						case 2:
							window.open(url, '_blank');
							return;
						
						// right click
						case 3:
							return;
					}
					
					location.href = url;
				}
			}
		};

// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			componentId = parseInt(Math.random() * 10000, 10);
			$container = $(options.container);

			$this
				.on('touchstart', function() {
					// Workaround for tablet navigation problem
					$this.off('mouseenter mouseleave');
				})
				.on('touchend', function() {
					$this
						.off('mouseenter', options.scope + ' .product-container', _mouseEnterHandler)
						.off('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);
				})
				.on('mouseenter', options.scope + ' .product-container', _mouseEnterHandler)
				.on('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);
			
			$this.find('.product-container .product-image').on('click mouseup', _clickHandler);
			
			$body
				.on(jse.libs.template.events.OPEN_FLYOUT(), _closeLayers);

			$window
				.on('resize', _resizeHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});
