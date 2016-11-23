/* --------------------------------------------------------------
 product_tooltip.js 2016-03-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Product Tooltip
 *
 * This controller displays a tooltip when hovering the product name.
 *
 * Use attribute 'data-product_tooltip-image-url' to set the image url to load.
 * Use attribute 'data-product_tooltip-description' to set the text content.
 *
 * @module Controllers/product_tooltip
 */
gx.controllers.module(
	'product_tooltip',

	[],

	/**  @lends module:Controllers/product_tooltip */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),

			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {},

			/**
			 * qTip plugin options object.
			 * @type {Object}
			 */
			tooltipOptions = {
				style: {
					classes: 'gx-container gx-qtip info large'
				},
				position: {
					my: 'left top',
					at: 'right bottom'
				}
			},

			/**
			 * Hover trigger.
			 * @type {jQuery}
			 */
			$trigger = $this.find('[data-tooltip-trigger]');

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Prepares the tooltip content by fetching the image and setting the description.
		 * @param {jQuery.Event} event Hover event.
		 * @param {Object} api qTip plugin internal API.
		 * @private
		 */
		var _getContent = function (event, api) {
			var $content = $('<div/>');

			var $description = $.parseHTML(data.description);
			$content.append($description);

			// Fetch image.
			var image = new Image();
			$(image)
				.load(function () {
					var $imageContainer = $('<div class="text-center" style="margin-bottom: 24px;"></div>');

					$imageContainer
						.append($('<br>'))
						.prepend(image);

					$content.prepend($imageContainer);

					// Set new tooltip content.
					api.set('content.text', $content);
				})
				.attr({src: data.imageUrl});

			return $content;
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		module.init = function (done) {
			if ($trigger.length) {
				var options = $.extend(true, tooltipOptions, {content: {text: _getContent}});
				$trigger.qtip(options);
			} else {
				throw new Error('Could not find trigger element');
			}

			done();
		};

		return module;
	});
