/* --------------------------------------------------------------
 admin_favicon_fix.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Admin Section Favicon - JS Fix
 *
 * Many pages in the admin section are missing the favicon.ico file because they do not specify this
 * directive in the <head> tag. The following code (pure JavaScript) will fix the issue. This solution
 * will not work in IE9 see: http://stackoverflow.com/a/13388728.
 *
 * This module requires two attributes to be provided as in the following example:
 *
 * ```html
 * <div class="page-wrapper-element"
 *      data-gx-compatibility="admin_favicon_fix"
 *      data-admin_favicon_fix-status="enabled"
 *      data-admin_favicon_fix-filename="favicon.ico"> ... </div>
 * ```
 *
 * @module Compatibility/admin_favicon_fix
 */
gx.compatibility.module(
	'admin_favicon_fix',
	
	[],
	
	/** @lends module:Compatibility/admin_favicon_fix */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DECLARATION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Element Selector
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
				'filename': '/admin/html/assets/images/gx-admin/favicon.ico'
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
			try {
				if (!document.querySelector('head link[rel="shortcut icon"]') && options.status === 'enabled') {
					var favicon = document.createElement('link');
					favicon.rel = 'shortcut icon';
					favicon.href = jse.core.config.get('appUrl') + options.filename;
					document.getElementsByTagName('head')[0].appendChild(favicon);
				}
			} catch (exception) {
				if (typeof console === 'object') {
					console.log('Failed to create favicon tag in document <head> element. Exception: ' +
						exception);
				}
			}
			
			done();
		};
		
		return module;
	});
