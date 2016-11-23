/* --------------------------------------------------------------
 slider_size.js 2016-02-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gets the size of the biggest image from the applied element and puts the previous and next buttons to the right 
 * position, if the screen-width is bigger than 1920px.
 */
gambio.widgets.module(
	'slider_size',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {},
		
			maxWidth = 0,
			nextButton = $('.js-teaser-slider-next.swiper-button-next'),
			prevButton = $('.js-teaser-slider-prev.swiper-button-prev');
		
		
		// ########## PRIVATE FUNCTIONS ##########
		
		
		/**
		 * Gets the biggest image from the applied element and calls the positioning method.
		 * 
		 * @private
		 */
		var _getBiggestImageWidth = function() {
			
			var windowWidth = $(window).width();
			
			$(window).load(function(){
				$('#slider').each(function(){
					
					$this.find('.swiper-container .swiper-wrapper .swiper-slide img').each(function(){
						
						var w = $(this).get(0).naturalWidth;
						if (w > maxWidth) {
							maxWidth = w;
						}
					});
					if (maxWidth && windowWidth > 1920) {
						_positionButtons(maxWidth);
					}
				});
			});
		};
		
		/**
		 * Puts the previous and next buttons of the swiper to the correct position, if the screen-width is bigger than
		 * 1920px
		 * 
		 * @param maxWidth int
		 * @private
		 */
		var _positionButtons = function (maxWidth) {
			
			var marginVal = Math.ceil(-(maxWidth/2)+30);
			
			nextButton.css({
				'right':'50%',
				'margin-right': marginVal + 'px'
			});
			
			prevButton.css({
				'left':'50%',
				'margin-left': marginVal + 'px'
			});
		};
		
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			
			_getBiggestImageWidth();
			
			$(window).resize(function() {
				if ($(window).width() <= 1920 && nextButton.attr('style') && prevButton.attr('style')) {
					nextButton.removeAttr('style');
					prevButton.removeAttr('style');
				} else if ($(window).width() > 1920 && !nextButton.attr('style') && !prevButton.attr('style')) {
					_positionButtons(maxWidth);
				}
			});
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});