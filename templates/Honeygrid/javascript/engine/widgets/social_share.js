/* --------------------------------------------------------------
 social_share.js 2016-07-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the social sharing support
 * 
 * (e.g.: Facebook, Twitter, Google+)
 * 
 * {@link https://github.com/heiseonline/shariff}
 */
gambio.widgets.module(
	'social_share',

	[],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ########## 

		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 */
		module.init = function(done) {
			$this.addClass('shariff'); 
			
			var config = {
				url: window.location.href,
				theme: 'standard',
				lang: jse.core.config.get('languageCode'),
				services: []
			};
			
			if (options.facebook !== undefined) {
				config.services.push('facebook'); 
			}
			
			if (options.twitter !== undefined) {
				config.services.push('twitter');
			}
			
			if (options.googleplus !== undefined) {
				config.services.push('googleplus');
			}
			
			if (options.pinterest !== undefined) {
				config.services.push('pinterest');
			}
			
			new Shariff($this, config);
			
			done();
		};

		// Return data to widget engine
		return module;
	});