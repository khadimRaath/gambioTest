/* --------------------------------------------------------------
 image_resizer.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.image_resizer = jse.libs.image_resizer || {};

/**
 * ## Image Resizer Library
 *
 * Resizes images with respective aspect ratio.
 *
 * @module JSE/Libs/image_resizer
 * @exports jse.libs.image_resizer
 */
(function (exports) {

	'use strict';
	
	/**
	 * Resize an image element with the provided width and height values.
	 * 
	 * @param {string} element Selector string for the image element to be resized.
	 * @param {object} options (optional) This object must contain the "width" and "height" properties.
	 */
	exports.resize = function (element, options) {

		var $that = $(element);
		var settings = {
			width: 150,
			height: 150
		};
		options = $.extend(settings, options);

		var maxWidth = options.width;
		var maxHeight = options.height;
		var ratio = 0;
		var width = $that.width();
		var height = $that.height();

		if (width > maxWidth) {
			ratio = maxWidth / width;
			$that.css('width', maxWidth);
			$that.css('height', height * ratio);

		}

		if (height > maxHeight) {
			ratio = maxHeight / height;
			$that.css('height', maxHeight);
			$that.css('width', width * ratio);

		}

	};

})(jse.libs.image_resizer);
