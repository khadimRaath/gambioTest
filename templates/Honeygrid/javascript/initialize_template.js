/* --------------------------------------------------------------
 initialize_template.js 2016-01-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Initialize Template JS Environment
 *
 * This script will set some parameters needed by other javascript sections. Use it to configure or override code from
 * the JS Engine.
 */

jse.core.config = jse.core.config || {};

jse.libs.template = {}; // Create new libs object for the template libraries.

(function(exports) {
	
	'use strict';

	// Backup original "init" method.
	var init = jse.core.config.init;

	exports.init = function(jsEngineConfiguration) {
		jse.core.registry.set('mainModalLayer', 'magnific');
		jse.core.registry.set('tplPath', jsEngineConfiguration.tplPath);

		// Call original config file init.
		init(jsEngineConfiguration);
	};

})(jse.core.config);
