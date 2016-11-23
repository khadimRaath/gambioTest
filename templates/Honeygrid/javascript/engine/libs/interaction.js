/* --------------------------------------------------------------
 interaction.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.interaction = jse.libs.template.interaction || {};

/**
 * ## Honeygrid Interaction Library
 * 
 * Handles the template interactions.
 * 
 * @module Honeygrid/Libs/interaction
 * @exports jse.libs.template.interaction
 */
(function(exports) {
	'use strict';

	var $body = $('body'),
		mousedown = false;

	/**
	 * Returns the mousedown state.
	 *
	 * @param  {object} e Event
	 * @return {boolean} True if mousedown is active 
	 */
	var _clickHandler = function(e) {
		mousedown = e.data.mousedown;
	};

	$body
		.on('mousedown', {mousedown: true}, _clickHandler)
		.on('mouseup', {mousedown: false}, _clickHandler);

	/**
	 * Returns true if a mouse button is clicked.
	 * 
	 * @return {Boolean} Is the mouse clicked?
	 */
	exports.isMouseDown = function() {
		return mousedown;
	};

}(jse.libs.template.interaction));