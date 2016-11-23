/* --------------------------------------------------------------
 switcher.js 2016-10-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Switcher Widget
 *
 * This widget originates from the "switcher" mode of the existing checkbox widget. Because of the increased
 * complexity of the old widget code, the switcher mode is now server by this file. Apply the widget in a parent
 * container and it will search and convert all the checkbox/radio instances into switchers.
 *
 * ### Options
 *
 * **On State | `data-switcher-on-state` | String | Optional**
 *
 * Define the content of the "on" state.
 *
 * **Off State | `data-switcher-off-state` | String | Optional**
 *
 * Define the content of the "off" state.
 * 
 * **Selector | `data-switcher-selector` | String | Optional**
 *
 * Set the selector of the checkboxes to be converted to switcher instances. It defaults to **input:checkbox**.
 *
 * ### Examples
 *
 * In the following example the checkbox element will be converted into a single-checkbox instance.
 *
 * ```html
 * <div class="wrapper" data-gx-widget="switcher">
 *   <input type="checkbox" />
 * </div>
 * ```
 *
 * @todo Add method for disabling the switcher widget (e.g. $('#my-switcher').switcher('disabled', true));
 *
 * @module Admin/Widgets/switcher
 */
gx.widgets.module('switcher', [], function(data) {
	
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
	 * Default Options
	 *
	 * @type {Object}
	 */
	const defaults = {
		onState: '<span class="fa fa-check"></span>',
		offState: '<span class="fa fa-times"></span>',
		selector: 'input:checkbox'
	};
	
	/**
	 * Final Options
	 *
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
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
	 * On Switcher Click Event
	 *
	 * Delegate the click event to the checkbox elements which will update the DOM accordingly.
	 *
	 * @param {object} event
	 */
	function _onSwitcherClick(event) {
		event.stopPropagation();
		
		if ($(this).hasClass('disabled')) {
			return false; // The switcher is disabled.
		}
		
		const $checkbox = $(this).find('input:checkbox');
		
		$checkbox
			.prop('checked', !$checkbox.prop('checked'))
			.trigger('change');
	}
	
	/**
	 * On Checkbox Change
	 *
	 * This callback will update the display of the widget. It will perform the required animations and set the
	 * respective state classes.
	 */
	function _onCheckboxChange() {
		const $checkbox = $(this);
		const $switcher = $checkbox.parent();
		
		if (!$switcher.hasClass('checked') && $checkbox.prop('checked')) {
			$switcher.addClass('checked');
		} else if ($switcher.hasClass('checked') && !$checkbox.prop('checked')) {
			$switcher.removeClass('checked');
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.find(options.selector).each(function() {
			const $checkbox = $(this);
			const title = $checkbox.prop('title') ? `title="${$checkbox.prop('title')}"` : '';
			
			$checkbox
				.wrap(`<div class="switcher" ${title}></div>`)
				.parent()
				.append(`
					<div class="switcher-toggler"></div>
					<div class="switcher-inner">
						<div class="switcher-state-on">${options.onState}</div>
						<div class="switcher-state-off">${options.offState}</div>
					</div>
				`);
			
			// Bind the switcher event handlers.  
			$checkbox
				.parent()
				.on('click', _onSwitcherClick)
				.on('change', 'input:checkbox', _onCheckboxChange);
			
			// Trigger the change event to update the checkbox display.
			$checkbox.trigger('change');
		});
		
		done();
	};
	
	return module;
	
}); 