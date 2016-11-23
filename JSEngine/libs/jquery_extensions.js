/* --------------------------------------------------------------
 jquery_extensions.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function() {
	
	'use strict';
	
	/**
	 * Add ":attr" pseudo selector.
	 *
	 * This selector enables jQuery to use regular expressions for attribute name matching. Although useful,
	 * the engine will remove all dependencies to jQuery and thus it must be moved into an external library
	 * or file.
	 */
	if ($.expr.pseudos.attr === undefined) {
		$.expr.pseudos.attr = $.expr.createPseudo(function(selector) {
			let regexp = new RegExp(selector);
			return function(elem) {
				for(let i = 0; i < elem.attributes.length; i++) {
					let attr = elem.attributes[i];
					if(regexp.test(attr.name)) {
						return true;
					}
				}
				return false;
			};
		});
	}
	
})();