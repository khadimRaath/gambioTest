/* --------------------------------------------------------------
 about.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Information Module
 * 
 * Execute the `jse.about()` command and you will get a new log entry in the
 * console with info about the engine. The "about" method is only available in
 * the "development" environment of the engine.
 *
 * @module JSE/Core/about
 */
document.addEventListener('DOMContentLoaded', function() {

	'use strict';

	if (jse.core.config.get('environment') === 'production') {
		return;
	}

	jse.about = function () {
		let info = `
			JS ENGINE v${jse.core.config.get('version')} © GAMBIO GMBH
			----------------------------------------------------------------
			The JS Engine enables developers to load automatically small pieces of javascript code by
			placing specific data attributes to the HTML markup of a page. It was built with modularity
			in mind so that modules can be reused into multiple places without extra effort. The engine
			contains namespaces which contain collections of modules, each one of whom serve a different
			generic purpose.
			Visit http://developers.gambio.de for complete reference of the JS Engine.
			
			FALLBACK INFORMATION
			----------------------------------------------------------------
			Since the engine code becomes bigger there are sections that need to be refactored in order
			to become more flexible. In most cases a warning log will be displayed at the browser\'s console
			whenever there is a use of a deprecated function. Below there is a quick list of fallback support
			that will be removed in the future versions of the engine.
			
			1. The main engine object was renamed from "gx" to "jse" which stands for the JavaScript Engine.
			2. The "gx.lib" object is removed after a long deprecation period. You should update the modules 
			   that contained calls to the functions of this object.
			3. The gx.<collection-name>.register function is deprecated by v1.2, use the 
			   <namespace>.<collection>.module() instead.
		`;
		
		jse.core.debug.info(info);
	};
});
