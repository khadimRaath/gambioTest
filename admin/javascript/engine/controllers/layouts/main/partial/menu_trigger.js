/* --------------------------------------------------------------
 menu_trigger.js 2016-04-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Menu Trigger Controller
 *
 * This controller will handle the main menu trigger. Provide the "data-menu_trigger-menu-selector" attribute
 * that must select the main menu. It also works with the "user_configuration_service" so the user ID is required.
 * Provide it with the "data-menu_trigger-customer-id" attribute.
 *
 * There are three states for the main menu: "collapse", "expand" and "expand-all".
 */
gx.controllers.module('menu_trigger', ['user_configuration_service'], function(data) {
	
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
	 * Main Menu Selector
	 *
	 * @type {jQuery}
	 */
	const $menu = $(data.menuSelector);
	
	/**
	 * Menu Items List Selector
	 *
	 * @type {jQuery}
	 */
	const $list = $menu.find('nav > ul');
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Set the menu state.
	 *
	 * This function will update the UI and save the state in the users_configuration db table.
	 *
	 * @param {String} state Accepts the "collapse", "expand" and "expandAll".
	 * @param {Boolean} save Optional (false), whether to save the change with the user configuration service.
	 */
	function _setMenuState(state, save = false) {
		let stateClass = '';
		
		switch (state) {
			case 'collapse':
			case 'expand':
				stateClass = state;
				break;
			
			case 'expandAll':
				stateClass = 'expand-all';
				break;
			
			default:
				stateClass = 'expand';
		}
		
		const $radio = $this.find('input:radio#menu-' + stateClass);
		
		// Set the class to the <ul> element of the main menu.
		$list.attr('class', stateClass);
		
		// Make sure the correct radio is selected.
		$radio.prop('checked', true);
		
		// Display the next-state icons.
		$radio.prev('label').hide();
		if ($radio.next('label').length > 0) {
			$radio.next('label').show();
		} else {
			$this.find('label:first').show();
		}
		
		// Save the configuration setting.
		if (save) {
			jse.libs.user_configuration_service.set({
				data: {
					userId: data.customerId,
					configurationKey: 'menuVisibility',
					configurationValue: state
				},
				onSuccess: function(response) {
					// Trigger a window resize in order to update the position of other UI elements.
					$(window).trigger('resize');
				}
			});
		}
	}
	
	/**
	 * Handles the radio buttons change.
	 *
	 * This is triggered by the click on the menu trigger button.
	 */
	function _onInputRadioChange() {
		_setMenuState($(this).val(), true);
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		_setMenuState(data.menuVisibility);
		
		$this.on('change', 'input:radio', _onInputRadioChange);
		
		done();
	};
	
	return module;
	
});