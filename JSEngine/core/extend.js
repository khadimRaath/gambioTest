/* --------------------------------------------------------------
 extensions.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Extensions
 *
 * Extend the default behaviour of engine components or external plugins before they are loaded.
 *
 * @module JSE/Core/extend
 */
(function () {

	'use strict';
	
	// ------------------------------------------------------------------------
	// PARSE MODULE DATA JQUERY EXTENSION
	// ------------------------------------------------------------------------
	
	$.fn.extend({
		parseModuleData: function(moduleName) {
			if (!moduleName || moduleName === '') {
				throw new Error('Module name was not provided as an argument.');
			}
			
			let initialData = $(this).data(),
				filteredData = {};
			
			// Searches for module relevant data inside the main-data-object. Data for other widgets will not get 
			// passed to this widget.
			$.each(initialData, function (key, value) {
				if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
					let newKey = key.substr(moduleName.length);
					newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
					filteredData[newKey] = value;
				}
			});
			
			return filteredData;
		}
	});

	// ------------------------------------------------------------------------
	// DATEPICKER REGIONAL INFO
	// ------------------------------------------------------------------------

	$.datepicker.regional.de = {
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional.de);
}());
