/* --------------------------------------------------------------
 magnifier.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that shows a zoom image on mouseover at a specific target
 */
gambio.widgets.module(
	'magnifier',

	[
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			$target = null,
			dataWasSet = false,
			defaults = {
				// Default zoom image target selector
				target: null,
				// If true, the zoom image will always fill the whole target container
				keepInView: true,
				// The class that gets added to the body while the magnifier window is visible
				bodyClass: 'magnifier-active',
				// Maximum breakpoint for mobile view mode
				breakpoint: 60
			},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## HELPER FUNCTIONS ##########

		/**
		 * Helper function to calculate the sizes and positions
		 * (that doesn't alter until the browser gets resized).
		 * The data object is stored at the source image and returned
		 * to the caller function
		 * @param    {object}        $self           jQuery selection of the source image
		 * @param    {object}        $thisTarget     jQuery selection of the zoom image target container
		 * @param    {object}        $image          jQuery selection of the zoom image itself
		 * @return   {object}                        JSON object which contains the calculated sizes and positions
		 * @private
		 */
		var _prepareData = function($self, $thisTarget, $image) {
			var dataset = {
				offset: $self.offset(),
				height: $self.height(),
				width: $self.width(),
				targetWidth: $thisTarget.width(),
				targetHeight: $thisTarget.height(),
				imageWidth: $image.width(),
				imageHeight: $image.height()
			};

			dataset.aspectX = -1 / (dataset.width / dataset.imageWidth);
			dataset.aspectY = -1 / (dataset.height / dataset.imageHeight);
			dataset.boundaryX = -1 * (dataset.imageWidth - dataset.targetWidth);
			dataset.boundaryY = -1 * (dataset.imageHeight - dataset.targetHeight);

			$self.data('magnifier', dataset);
			dataWasSet = true;

			return $.extend({}, dataset);
		};


// ########## EVENT HANDLER ##########

		/**
		 * Event handler for the mousemove event. If the cursor gets
		 * moved over the image, the cursor position will be scaled to
		 * the zoom target and the zoom image gets positioned at that point
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _mouseMoveHandler = function(e) {
			var $self = $(this),
				dataset = $self.data('magnifier'),
				$image = $target.children('img');

			dataset = dataset || _prepareData($self, $target, $image);

			var marginTop = dataset.aspectY * (e.pageY - dataset.offset.top) + dataset.targetHeight / 2,
				marginLeft = dataset.aspectX * (e.pageX - dataset.offset.left) + dataset.targetWidth / 2;

			// If this setting is true, the zoomed image will always
			// fill the whole preview container
			if (options.keepInView) {
				marginTop = Math.min(0, marginTop);
				marginTop = Math.max(dataset.boundaryY, marginTop);
				marginLeft = Math.min(0, marginLeft);
				marginLeft = Math.max(dataset.boundaryX, marginLeft);
			}

			// Set the calculated styles
			$image.css({
				           'margin-top': marginTop + 'px',
				           'margin-left': marginLeft + 'px'
			           });
		};

		/**
		 * Event handler for the mouse enter event
		 * on the target. It creates the zoom image
		 * and embeds it to the magnifier target
		 * @private
		 */
		var _mouseEnterHandler = function(e) {

			// Only open in desktop mode
			if (jse.libs.template.responsive.breakpoint().id > options.breakpoint) {

				var $self = $(this),
					dataset = $self.data(),
					$preloader = $target.find('.preloader'),
					$image = $('<img />'),
					alt = $self.attr('alt'),
					title = $self.attr('title');

				// CleansUp the magnifier target
				$target
					.children('img')
					.remove();

				$preloader.show();
				$body.addClass(options.bodyClass);

				// Creates the image element and binds
				// a load handler to it, so that the
				// preloader gets hidden after the image
				// is loaded by the browser
				$image.one('load', function() {
					      $image.css({
						                 'height': this.height + 'px',
						                 'width': this.width + 'px'
					                 });
					      $preloader.hide();

					      // Bind the mousemove handler to zoom to
					      // the correct position of the image
					      $self
						      .off('mousemove.magnifier')
						      .on('mousemove.magnifier', _mouseMoveHandler);
				      })
				      .attr({src: dataset.magnifierSrc, alt: alt, title: title});

				// Append the image to the maginifier target
				$target
					.append($image)
					.show();

			}

		};

		/**
		 * Handler for the browser resize event.
		 * It removes all stored data so that a
		 * recalculation is forced
		 * @private
		 */
		var _resizeHandler = function() {
			if (dataWasSet) {
				$this
					.find('img[data-magnifier-src]')
					.removeData('magnifier');

				dataWasSet = false;
			}
		};

		/**
		 * Event handler for the mouseleave event. In case
		 * the cursor leaves the image, the zoom target gets
		 * hidden
		 * @private
		 */
		var _mouseLeaveHandler = function() {
			$target.hide();
			$body.removeClass(options.bodyClass);

			$this
				.off('mouseenter')
				.on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler);
		};

		/**
		 * Removes the mouseenter handler on touchstart,
		 * so that the magnifier not starts on touch.
		 * The function gets reactivated in the mouseleave
		 * handler
		 * @private
		 */
		var _touchHandler = function() {
			$this.off('mouseenter');
		};

// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$target = $(options.target);

			$this
				.on('touchstart', 'img[data-magnifier-src]', _touchHandler)
				.on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler)
				.on('mouseleave', 'img[data-magnifier-src]', _mouseLeaveHandler);

			$(window).on('resize', _resizeHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});
