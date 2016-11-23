/* --------------------------------------------------------------
 link.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals getSelection */

/**
 * ## Link Extension
 *
 * Use this extension to simulate any HTML element as an `<a>` link. Whenever the user clicks that element 
 * he will be navigated into the target page as if he was clicking an `<a>` element.
 *
 * This module requires one extra option which will define the target URL to be used when navigating to 
 * the next page. Provide it in the same element as in the following example. 
 * 
 * ### Options
 * 
 * **URL | data-link-url | String | Required** 
 * 
 * The destination URL to be used after the user clicks on the element.
 * 
 * ### Example
 * 
 * ```html 
 * <label data-gx-extension="link" data-link-url="http://gambio.de">Navigate To Official Website</label>
 * ```
 * 
 * @module Admin/Extensions/link
 */
gx.extensions.module(
	'link',
	
	[],
	
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
			defaults = {
				url: '#'
			},
			
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
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			$this.on('mouseup', function(event) {
				
				// 1 = left click, 2 = middle click
				if (event.which === 1 || event.which === 2) {
					event.preventDefault();
					event.stopPropagation();
					
					var target = (event.which === 1) ? '_self' : '_blank';
					var sel = getSelection().toString();
					
					if (!sel) {
						window.open(options.url, target);
					}
				}
				
			});
			
			done();
		};
		
		return module;
	});
