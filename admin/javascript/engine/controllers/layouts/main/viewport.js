/* --------------------------------------------------------------
 viewport.js 2016-06-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module('viewport', [], function(data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);

	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};

	/**
	 * Info Row
	 *
	 * @type {jQuery}
	 */
	const $infoRow = $('#main-footer .info.row');


	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
	 * Checks if the provided dropdown is out of the viewport.
	 *
	 * @param {jQuery} $dropDownMenu
	 *
	 * @returns {boolean}
	 */
	function _isDropDownOutOfView($dropDownMenu) {
		const infoRowTopPosition = $infoRow.offset().top;

		return ($dropDownMenu.height() + $dropDownMenu.siblings('.dropdown-toggle').offset().top) > infoRowTopPosition;
	}

	/**
	 * Adjust the dropdown position, depending on the current viewport.
	 */
	function _adjustDropDownPosition() {

		const $target = $(this);

		let $dropDownMenu = $target.find('.dropdown-menu');

		// Put the dropdown menu above the clicked target,
		// if the menu would touch or even be larger than the info row in the main footer.
		if(_isDropDownOutOfView($dropDownMenu)) {
			$target.addClass('dropup');
			$target.find('.caret').addClass('caret-reversed');
		} else if ($target.hasClass('dropup')) {
			$target.removeClass('dropup');
			$target.find('.caret').removeClass('caret-reversed');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function(done) {
		$('body').on('show.bs.dropdown', '.btn-group.dropdown', _adjustDropDownPosition);

		done();
	};

	return module;
});
