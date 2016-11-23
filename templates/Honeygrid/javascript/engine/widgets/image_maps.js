/* --------------------------------------------------------------
 image_maps.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that searches for images with an image map and calls
 * a plugin on them, so that the image maps getting responsive
 */
gambio.widgets.module('image_maps', [], function() {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		module = {};


// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		$this
			.find('img[usemap]')
			.rwdImageMaps();

		done();
	};

	// Return data to widget engine
	return module;
});