/* --------------------------------------------------------------
 lang.js 2016-08-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.core.lang = jse.core.lang || {};

/**
 * JSE Localization Library
 *
 * The global Lang object contains language information that can be easily used in your
 * JavaScript code. The object contains constance translations and dynamic sections that
 * can be loaded and used in different page.
 *
 * #### Important
 * The engine will automatically load translation sections that are present in the
 * `window.JSEngineConfiguration.translations` property upon initialization. For more
 * information look at the "core/initialize" page of documentation reference.
 *
 * ```javascript
 * jse.core.lang.addSection('sectionName', { translationKey: 'translationValue' }); // Add translation section.
 * jse.core.translate('translationKey', 'sectionName'); // Get the translated string.
 * jse.core.getSections(); // returns array with sections e.g. ['admin_buttons', 'general']
 * ```
 *
 * @module JSE/Core/lang
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
	 * Contains various translation sections.
	 *
	 * @type {Object}
	 */
	let sections = {};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
	 * Add a translation section.
	 *
	 * @param {String} name Name of the section, used later for accessing translation strings.
	 * @param {Object} translations Key - value object containing the translations.
	 *
	 * @throws {Error} If "name" or "translations" arguments are invalid.
	 */
	exports.addSection = function (name, translations) {
		if (typeof name !== 'string' || typeof translations !== 'object' || translations === null) {
			throw new Error(`window.gx.core.lang.addSection: Invalid arguments provided (name: ${typeof name}, ` 
			                + `translations: ${typeof translations}).`);
		}
		sections[name] = translations;
	};

	/**
	 * Get loaded translation sections.
	 *
	 * Useful for asserting present translation sections.
	 *
	 * @return {Array} Returns array with the existing sections.
	 */
	exports.getSections = function () {
		let result = [];
		
		for (let section in sections) {
			result.push(section);
		}
		
		return result;
	};

	/**
	 * Translate string in Javascript code.
	 *
	 * @param {String} phrase Name of the phrase containing the translation.
	 * @param {String} section Section name containing the translation string.
	 *
	 * @return {String} Returns the translated string.
	 *
	 * @throws {Error} If provided arguments are invalid.
	 * @throws {Error} If required section does not exist or translation could not be found.
	 */
	exports.translate = function (phrase, section) {
		// Validate provided arguments.
		if (typeof phrase !== 'string' || typeof section !== 'string') {
			throw new Error(`Invalid arguments provided in translate method (phrase: ${typeof phrase}, `
			                + `section: ${typeof section}).`);
		}

		// Check if translation exists.
		if (sections[section] === undefined || sections[section][phrase] === undefined) {
			jse.core.debug.warn(`Could not found requested translation (phrase: ${phrase}, section: ${section}).`);
			return '{' + section + '.' + phrase + '}';
		}

		return sections[section][phrase];
	};

}(jse.core.lang));
