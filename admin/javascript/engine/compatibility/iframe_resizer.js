/* --------------------------------------------------------------
 iframe_resizer.js 2015-11-12 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## iFrame resizer
 *
 * Widget that resizes the iframes on isolated modules page
 *
 * @module Compatibility/iframe_resizer
 */
gx.compatibility.module(
	'iframe_resizer',
	
	[],
	
	/**  @lends module:Compatibility/iframe_resizer */
	
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
		
		var _resize = function() {
			var $iframe = $this.contents(),
				$body = $iframe.find('body'),
				height = $body.outerHeight(),
				width = $('.boxCenter').width() - 70;
			
			$this.css({'height': height + 'px', 'width': width + 'px'});
		};
		
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			_resize();
			$this.one('load', _resize);
			setInterval(_resize, 100);
			done();
		};
		
		return module;
	});
