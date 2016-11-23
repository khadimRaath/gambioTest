/* --------------------------------------------------------------
 single_checkbox.js 2016-10-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Single Checkbox Widget
 *
 * This widget originates from the "single-checkbox" mode of the existing checkbox widget. Because of the
 * increased complexity of the old widget code, the single-checkbox mode is now served by this file. Apply the
 * widget in a parent container and it will search and convert all the instances into fine checkboxes.
 *
 * If you want to dynamically change the state of the checkbox, apply the new "checked" prop in the input:checkbox
 * element and then trigger the "change" event. This will also update the .single-checkbox wrapper.
 *
 * ### Options 
 * 
 * **Selector | `data-single_checkbox-selector` | String | Optional**
 *
 * Set the selector of the checkboxes to be converted to single checkbox instances. It defaults to **input:checkbox**.
 * 
 * ### Methods 
 * 
 * **Checked** 
 * 
 * ```js
 * // Set the checked value of the single checkbox selection (no change event will be triggered!). 
 * $('table input:checkbox').single_checkbox('checked', true);
 * ```
 * 
 * ### Events
 *
 * **Initialization**
 *
 * This module triggers the "single_checkbox:ready" event, which will be handled in the `checkbox_mapping.js` file.
 * It is needed to add the caret symbol next to the checkbox and to open the multi select dropdown menu next to it.
 *
 * ### Examples
 *
 * In the following example the checkbox element will be converted into a single-checkbox instance.
 *
 * ```html
 * <div class="wrapper" data-gx-widget="single_checkbox">
 *   <input type="checkbox" />
 * </div>
 * ```
 */
gx.widgets.module('single_checkbox', [], function(data) {
	
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
	 * Set the "checked" property of the single checkbox instances.
	 * 
	 * This method will update the value and display of the widgets without triggering a "change" event. 
	 * 
	 * @param {Boolean} isChecked The checkbox values will be updated along with their representation. 
	 * 
	 * @return {jQuery} Returns the jQuery selector for chained calls.
	 */
	function _checked(isChecked) {
		$(this).prop('checked', isChecked);
		_onCheckboxChange.call(this);
		return $(this); 
	}
	
	/**
	 * Add Public Module Methods
	 * 
	 * Example: $('input:checkbox').single_checkbox('checked', false); 
	 */
	function _addPublicMethod() {
		if ($.fn.single_checkbox) {
			return; // Method is already registered.  
		}
		
		$.fn.extend({
			single_checkbox: function(action, ...args) {
				switch (action) {
					case 'checked':
						return _checked.apply(this, args);
				}
			}
		});
	}
	
	/**
	 * Wrap the checkbox elements with an external <span> which will be styled with CSS.
	 *
	 * This method will also bind the event handlers of each checkbox element.
	 */
	function _wrapCheckboxElements() {
		$this.find(options.selector).each(function() {
			const checked = $(this).prop('checked') ? 'checked' : '';
			const disabled = $(this).prop('disabled') ? 'disabled' : '';
			let title = $(this).attr('title');
			
			$(this)
				.css({
					position: 'absolute',
					left: '-100000px'
				})
				.wrap(`<span class="single-checkbox ${checked} ${disabled}" ${title ? title=`"${title}"` : ''}></span>`)
				.parent()
				.append('<i class="fa fa-check"></i>');
			
			$(this)
				.on('focus', _onCheckboxFocus)
				.on('blur', _onCheckboxBlur)
				.on('change', _onCheckboxChange);
		});
	}
	
	/**
	 * On Checkbox Change
	 *
	 * This event handler will make sure that the parent has the correct classes depending the checkbox state.
	 */
	function _onCheckboxChange() {
		const $wrapper = $(this).parent();
		const isChecked = $(this).prop('checked');
		
		if (isChecked && !$wrapper.hasClass('checked')) {
			$wrapper.addClass('checked');
		} else if (!isChecked && $wrapper.hasClass('checked')) {
			$wrapper.removeClass('checked');
		}
	}
	
	/**
	 * On Checkbox Focus
	 *
	 * This event handler will add the "focused" class which is used for styling.
	 */
	function _onCheckboxFocus() {
		$(this).parent().addClass('focused');
	}
	
	/**
	 * On Checkbox Blur
	 *
	 * This event handler will remove the "focused" class which is used for styling.
	 */
	function _onCheckboxBlur() {
		$(this).parent().removeClass('focused');
	}
	
	/**
	 * On Wrapper Click
	 *
	 * This event handler will delegate the click to the checkbox and must not change the state of the widget.
	 *
	 * @param event {object}
	 */
	function _onWrapperClick(event) {
		event.stopPropagation();
		
		if ($(this).hasClass('disabled') || $this.find('.dataTables_empty').length) {
			return;
		}
		
		const $checkbox = $(this).children('input:checkbox');
		
		$checkbox
			.prop('checked', !$checkbox.prop('checked'))
			.trigger('change');
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		_addPublicMethod(); 
		_wrapCheckboxElements();
		
		$this
			.off('click', '.single-checkbox')
			.on('click', '.single-checkbox', _onWrapperClick);
		
		$this.trigger('single_checkbox:ready'); // Needed for the checkbox_mapping.js file.
		
		done();
	};
	
	return module;
}); 
