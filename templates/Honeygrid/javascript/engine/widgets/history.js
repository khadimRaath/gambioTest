/* --------------------------------------------------------------
 history.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Simple component that adds browser history-functionality
 * to elements (back, forward & refresh)
 */
gambio.widgets.module('history', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		defaults = {},
		options = $.extend(true, {}, defaults, data),
		module = {};

// ########## EVENT HANDLER ##########

	/**
	 * Event handler that executes the browser
	 * history functionality depending on the
	 * given data
	 * @param       {object}    e       jQuery event object
	 * @private
	 */
	var _navigate = function(e) {
		e.preventDefault();

		history.go(e.data.step);
	};

	/**
	 * Event handler that executes the browser
	 * refresh functionality
	 * @param       {object}    e       jQuery event object
	 * @private
	 */
	var _refresh = function(e) {
		e.preventDefault();

		location.reload();
	};

// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {

		$this
			.on('click', '.history-back', {step: -1}, _navigate)
			.on('click', '.history-forward', {step: 1}, _navigate)
			.on('click', '.history-refresh', _refresh);

		done();
	};

	// Return data to widget engine
	return module;
});