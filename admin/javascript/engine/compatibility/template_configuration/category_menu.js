/* --------------------------------------------------------------
 category_menu 2016-09-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.compatibility.module('category_menu', [], function(data) {
	
	'use strict';
	
	var $this = $(this),
		
		/**
		 * Module Object
		 *
		 * @type {object}
		 */
		module = {};
	
	const $catMenuTopSwitcher = $('input:checkbox[name="CAT_MENU_TOP"]');
	const $catMenuLeftSwitcher = $('input:checkbox[name="CAT_MENU_LEFT"]');
	const $showSubcategoriesSwitcher = $('input:checkbox[name="SHOW_SUBCATEGORIES"]');
	
	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------
	
	function _onCatMenuTopSwitcherChange() {
		if ($catMenuTopSwitcher.prop('checked') === false) {
			$catMenuLeftSwitcher.parent().addClass('checked disabled');
			$showSubcategoriesSwitcher.parent().addClass('disabled').removeClass('checked');
			
			$catMenuLeftSwitcher.prop('checked', true);
			$showSubcategoriesSwitcher.prop('checked', false);
		} else {
			$catMenuLeftSwitcher.parent().removeClass('disabled');
			$showSubcategoriesSwitcher.parent().removeClass('disabled');
		}
	}
	
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	
	/**
	 * Initialize method of the widget, called by the engine.
	 */
	module.init = function(done) {
		$this.on('checkbox:change', $catMenuTopSwitcher, _onCatMenuTopSwitcherChange);
		
		$(document).on('JSENGINE_INIT_FINISHED', function() {
			_onCatMenuTopSwitcherChange();
		});
		
		
		done();
	};
	
	// Return data to module engine.
	return module;
});