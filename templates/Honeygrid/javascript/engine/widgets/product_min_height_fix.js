/* --------------------------------------------------------------
 product_min_height_fix.js 2016-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that fixes min height of product info content element
 */
gambio.widgets.module(
	'product_min_height_fix',
	
	[
		gambio.source + '/libs/events',
	],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			$window = $(window),
			defaults = {
				productInfoContent: '.product-info-content' // Selector to apply min height to
			},
			options = $.extend(true, {}, defaults, data),
			module = {};
		
		// ########## HELPER FUNCTIONS ##########		
		
		/**
		 * Fix for problem that box overlaps content like cross selling products if product content is too short
		 *
		 * @private
		 */
		var _setProductInfoContentMinHeight = function() {
			var minHeight = $this.outerHeight() + parseFloat($this.css('top'));
			$(options.productInfoContent).css('min-height', minHeight + 'px');
		};
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			_setProductInfoContentMinHeight();
			
			$window.on(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE(), _setProductInfoContentMinHeight);
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});
