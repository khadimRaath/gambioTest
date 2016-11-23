/* --------------------------------------------------------------
 sticky_footer.js 2015-09-14 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Handle footer position for backend.
 *
 * This module will handle the footer position on scrolling or whenever the page window size changes.
 *
 * @module Compatibility/sticky_footer
 */
gx.compatibility.module(
	'sticky_footer',
	
	[],
	
	/**  @lends module:Compatibility/sticky_footer */
	
	function(data) {
		
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
			 * Copyright Element Selector
			 *
			 * @var {object}
			 */
			$copyright = $('.main-bottom-copyright'),
			
			/**
			 * Footer Offset Top
			 *
			 * @var {int}
			 */
			initialOffsetTop = $this.offset().top,
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _checkOffset = function() {
			if (($(document).scrollTop() + window.innerHeight) < $copyright.offset().top) {
				$this.css('position', 'fixed');
			} else if ($this.offset().top + $this.height() >= $copyright.offset().top) {
				$this.css('position', 'absolute');
			}
		};
		
		var _fixMainContentHeight = function() {
			if (initialOffsetTop + $this.height() <= window.innerHeight) {
				var newContentHeight = window.innerHeight - $('.main-page-content').offset().top;
				$('.main-page-content').css('min-height', newContentHeight + 'px');
				// First table of the page needs to be also resized.
				$('td.columnLeft2').parents('table:first').css('min-height', newContentHeight + 'px');
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Delay the footer position by some time until so that most elements are rendered
			// properly. Adjust the timeout interval approximately.
			setTimeout(function() {
				_fixMainContentHeight();
				
				$(window)
					.on('scroll', _checkOffset)
					.on('resize', _checkOffset)
					.on('resize', _fixMainContentHeight);
				_checkOffset();
			}, 300);
			
			done();
		};
		
		return module;
	});
