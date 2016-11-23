'use strict';

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
gx.widgets.module('single_checkbox', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options 
  * 
  * @type {Object}
  */
	var defaults = {
		selector: 'input:checkbox'
	};

	/**
  * Final Options
  *
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

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
			single_checkbox: function single_checkbox(action) {
				switch (action) {
					case 'checked':
						for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
							args[_key - 1] = arguments[_key];
						}

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
		$this.find(options.selector).each(function () {
			var checked = $(this).prop('checked') ? 'checked' : '';
			var disabled = $(this).prop('disabled') ? 'disabled' : '';
			var title = $(this).attr('title');

			$(this).css({
				position: 'absolute',
				left: '-100000px'
			}).wrap('<span class="single-checkbox ' + checked + ' ' + disabled + '" ' + (title ? title = '"' + title + '"' : '') + '></span>').parent().append('<i class="fa fa-check"></i>');

			$(this).on('focus', _onCheckboxFocus).on('blur', _onCheckboxBlur).on('change', _onCheckboxChange);
		});
	}

	/**
  * On Checkbox Change
  *
  * This event handler will make sure that the parent has the correct classes depending the checkbox state.
  */
	function _onCheckboxChange() {
		var $wrapper = $(this).parent();
		var isChecked = $(this).prop('checked');

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

		var $checkbox = $(this).children('input:checkbox');

		$checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_addPublicMethod();
		_wrapCheckboxElements();

		$this.off('click', '.single-checkbox').on('click', '.single-checkbox', _onWrapperClick);

		$this.trigger('single_checkbox:ready'); // Needed for the checkbox_mapping.js file.

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNpbmdsZV9jaGVja2JveC5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJzZWxlY3RvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2hlY2tlZCIsImlzQ2hlY2tlZCIsInByb3AiLCJfb25DaGVja2JveENoYW5nZSIsImNhbGwiLCJfYWRkUHVibGljTWV0aG9kIiwiZm4iLCJzaW5nbGVfY2hlY2tib3giLCJhY3Rpb24iLCJhcmdzIiwiYXBwbHkiLCJfd3JhcENoZWNrYm94RWxlbWVudHMiLCJmaW5kIiwiZWFjaCIsImNoZWNrZWQiLCJkaXNhYmxlZCIsInRpdGxlIiwiYXR0ciIsImNzcyIsInBvc2l0aW9uIiwibGVmdCIsIndyYXAiLCJwYXJlbnQiLCJhcHBlbmQiLCJvbiIsIl9vbkNoZWNrYm94Rm9jdXMiLCJfb25DaGVja2JveEJsdXIiLCIkd3JhcHBlciIsImhhc0NsYXNzIiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyIsIl9vbldyYXBwZXJDbGljayIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwibGVuZ3RoIiwiJGNoZWNrYm94IiwiY2hpbGRyZW4iLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiLCJvZmYiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMENBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FBa0IsaUJBQWxCLEVBQXFDLEVBQXJDLEVBQXlDLFVBQVNDLElBQVQsRUFBZTs7QUFFdkQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNQyxXQUFXO0FBQ2hCQyxZQUFVO0FBRE0sRUFBakI7O0FBSUE7Ozs7O0FBS0EsS0FBTUMsVUFBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxF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VUFBakIsS0FBZ0NsQyxNQUFNa0IsSUFBTixDQUFXLG1CQUFYLEVBQWdDc0IsTUFBcEUsRUFBNEU7QUFDM0U7QUFDQTs7QUFFRCxNQUFNQyxZQUFZeEMsRUFBRSxJQUFGLEVBQVF5QyxRQUFSLENBQWlCLGdCQUFqQixDQUFsQjs7QUFFQUQsWUFDRWpDLElBREYsQ0FDTyxTQURQLEVBQ2tCLENBQUNpQyxVQUFVakMsSUFBVixDQUFlLFNBQWYsQ0FEbkIsRUFFRW1DLE9BRkYsQ0FFVSxRQUZWO0FBR0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBN0MsUUFBTzhDLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJsQztBQUNBTTs7QUFFQWpCLFFBQ0U4QyxHQURGLENBQ00sT0FETixFQUNlLGtCQURmLEVBRUVoQixFQUZGLENBRUssT0FGTCxFQUVjLGtCQUZkLEVBRWtDTyxlQUZsQzs7QUFJQXJDLFFBQU0yQyxPQUFOLENBQWMsdUJBQWQsRUFSNEIsQ0FRWTs7QUFFeENFO0FBQ0EsRUFYRDs7QUFhQSxRQUFPL0MsTUFBUDtBQUNBLENBakxEIiwiZmlsZSI6InNpbmdsZV9jaGVja2JveC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBzaW5nbGVfY2hlY2tib3guanMgMjAxNi0xMC0xN1xyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiAjIyBTaW5nbGUgQ2hlY2tib3ggV2lkZ2V0XHJcbiAqXHJcbiAqIFRoaXMgd2lkZ2V0IG9yaWdpbmF0ZXMgZnJvbSB0aGUgXCJzaW5nbGUtY2hlY2tib3hcIiBtb2RlIG9mIHRoZSBleGlzdGluZyBjaGVja2JveCB3aWRnZXQuIEJlY2F1c2Ugb2YgdGhlXHJcbiAqIGluY3JlYXNlZCBjb21wbGV4aXR5IG9mIHRoZSBvbGQgd2lkZ2V0IGNvZGUsIHRoZSBzaW5nbGUtY2hlY2tib3ggbW9kZSBpcyBub3cgc2VydmVkIGJ5IHRoaXMgZmlsZS4gQXBwbHkgdGhlXHJcbiAqIHdpZGdldCBpbiBhIHBhcmVudCBjb250YWluZXIgYW5kIGl0IHdpbGwgc2VhcmNoIGFuZCBjb252ZXJ0IGFsbCB0aGUgaW5zdGFuY2VzIGludG8gZmluZSBjaGVja2JveGVzLlxyXG4gKlxyXG4gKiBJZiB5b3Ugd2FudCB0byBkeW5hbWljYWxseSBjaGFuZ2UgdGhlIHN0YXRlIG9mIHRoZSBjaGVja2JveCwgYXBwbHkgdGhlIG5ldyBcImNoZWNrZWRcIiBwcm9wIGluIHRoZSBpbnB1dDpjaGVja2JveFxyXG4gKiBlbGVtZW50IGFuZCB0aGVuIHRyaWdnZXIgdGhlIFwiY2hhbmdlXCIgZXZlbnQuIFRoaXMgd2lsbCBhbHNvIHVwZGF0ZSB0aGUgLnNpbmdsZS1jaGVja2JveCB3cmFwcGVyLlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9ucyBcclxuICogXHJcbiAqICoqU2VsZWN0b3IgfCBgZGF0YS1zaW5nbGVfY2hlY2tib3gtc2VsZWN0b3JgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxyXG4gKlxyXG4gKiBTZXQgdGhlIHNlbGVjdG9yIG9mIHRoZSBjaGVja2JveGVzIHRvIGJlIGNvbnZlcnRlZCB0byBzaW5nbGUgY2hlY2tib3ggaW5zdGFuY2VzLiBJdCBkZWZhdWx0cyB0byAqKmlucHV0OmNoZWNrYm94KiouXHJcbiAqIFxyXG4gKiAjIyMgTWV0aG9kcyBcclxuICogXHJcbiAqICoqQ2hlY2tlZCoqIFxyXG4gKiBcclxuICogYGBganNcclxuICogLy8gU2V0IHRoZSBjaGVja2VkIHZhbHVlIG9mIHRoZSBzaW5nbGUgY2hlY2tib3ggc2VsZWN0aW9uIChubyBjaGFuZ2UgZXZlbnQgd2lsbCBiZSB0cmlnZ2VyZWQhKS4gXHJcbiAqICQoJ3RhYmxlIGlucHV0OmNoZWNrYm94Jykuc2luZ2xlX2NoZWNrYm94KCdjaGVja2VkJywgdHJ1ZSk7XHJcbiAqIGBgYFxyXG4gKiBcclxuICogIyMjIEV2ZW50c1xyXG4gKlxyXG4gKiAqKkluaXRpYWxpemF0aW9uKipcclxuICpcclxuICogVGhpcyBtb2R1bGUgdHJpZ2dlcnMgdGhlIFwic2luZ2xlX2NoZWNrYm94OnJlYWR5XCIgZXZlbnQsIHdoaWNoIHdpbGwgYmUgaGFuZGxlZCBpbiB0aGUgYGNoZWNrYm94X21hcHBpbmcuanNgIGZpbGUuXHJcbiAqIEl0IGlzIG5lZWRlZCB0byBhZGQgdGhlIGNhcmV0IHN5bWJvbCBuZXh0IHRvIHRoZSBjaGVja2JveCBhbmQgdG8gb3BlbiB0aGUgbXVsdGkgc2VsZWN0IGRyb3Bkb3duIG1lbnUgbmV4dCB0byBpdC5cclxuICpcclxuICogIyMjIEV4YW1wbGVzXHJcbiAqXHJcbiAqIEluIHRoZSBmb2xsb3dpbmcgZXhhbXBsZSB0aGUgY2hlY2tib3ggZWxlbWVudCB3aWxsIGJlIGNvbnZlcnRlZCBpbnRvIGEgc2luZ2xlLWNoZWNrYm94IGluc3RhbmNlLlxyXG4gKlxyXG4gKiBgYGBodG1sXHJcbiAqIDxkaXYgY2xhc3M9XCJ3cmFwcGVyXCIgZGF0YS1neC13aWRnZXQ9XCJzaW5nbGVfY2hlY2tib3hcIj5cclxuICogICA8aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgLz5cclxuICogPC9kaXY+XHJcbiAqIGBgYFxyXG4gKi9cclxuZ3gud2lkZ2V0cy5tb2R1bGUoJ3NpbmdsZV9jaGVja2JveCcsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogRGVmYXVsdCBPcHRpb25zIFxyXG5cdCAqIFxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgZGVmYXVsdHMgPSB7XHJcblx0XHRzZWxlY3RvcjogJ2lucHV0OmNoZWNrYm94J1xyXG5cdH07XHJcblx0XHJcblx0LyoqXHJcblx0ICogRmluYWwgT3B0aW9uc1xyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogU2V0IHRoZSBcImNoZWNrZWRcIiBwcm9wZXJ0eSBvZiB0aGUgc2luZ2xlIGNoZWNrYm94IGluc3RhbmNlcy5cclxuXHQgKiBcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIHVwZGF0ZSB0aGUgdmFsdWUgYW5kIGRpc3BsYXkgb2YgdGhlIHdpZGdldHMgd2l0aG91dCB0cmlnZ2VyaW5nIGEgXCJjaGFuZ2VcIiBldmVudC4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtCb29sZWFufSBpc0NoZWNrZWQgVGhlIGNoZWNrYm94IHZhbHVlcyB3aWxsIGJlIHVwZGF0ZWQgYWxvbmcgd2l0aCB0aGVpciByZXByZXNlbnRhdGlvbi4gXHJcblx0ICogXHJcblx0ICogQHJldHVybiB7alF1ZXJ5fSBSZXR1cm5zIHRoZSBqUXVlcnkgc2VsZWN0b3IgZm9yIGNoYWluZWQgY2FsbHMuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX2NoZWNrZWQoaXNDaGVja2VkKSB7XHJcblx0XHQkKHRoaXMpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xyXG5cdFx0X29uQ2hlY2tib3hDaGFuZ2UuY2FsbCh0aGlzKTtcclxuXHRcdHJldHVybiAkKHRoaXMpOyBcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogQWRkIFB1YmxpYyBNb2R1bGUgTWV0aG9kc1xyXG5cdCAqIFxyXG5cdCAqIEV4YW1wbGU6ICQoJ2lucHV0OmNoZWNrYm94Jykuc2luZ2xlX2NoZWNrYm94KCdjaGVja2VkJywgZmFsc2UpOyBcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfYWRkUHVibGljTWV0aG9kKCkge1xyXG5cdFx0aWYgKCQuZm4uc2luZ2xlX2NoZWNrYm94KSB7XHJcblx0XHRcdHJldHVybjsgLy8gTWV0aG9kIGlzIGFscmVhZHkgcmVnaXN0ZXJlZC4gIFxyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQkLmZuLmV4dGVuZCh7XHJcblx0XHRcdHNpbmdsZV9jaGVja2JveDogZnVuY3Rpb24oYWN0aW9uLCAuLi5hcmdzKSB7XHJcblx0XHRcdFx0c3dpdGNoIChhY3Rpb24pIHtcclxuXHRcdFx0XHRcdGNhc2UgJ2NoZWNrZWQnOlxyXG5cdFx0XHRcdFx0XHRyZXR1cm4gX2NoZWNrZWQuYXBwbHkodGhpcywgYXJncyk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogV3JhcCB0aGUgY2hlY2tib3ggZWxlbWVudHMgd2l0aCBhbiBleHRlcm5hbCA8c3Bhbj4gd2hpY2ggd2lsbCBiZSBzdHlsZWQgd2l0aCBDU1MuXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIGFsc28gYmluZCB0aGUgZXZlbnQgaGFuZGxlcnMgb2YgZWFjaCBjaGVja2JveCBlbGVtZW50LlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF93cmFwQ2hlY2tib3hFbGVtZW50cygpIHtcclxuXHRcdCR0aGlzLmZpbmQob3B0aW9ucy5zZWxlY3RvcikuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0Y29uc3QgY2hlY2tlZCA9ICQodGhpcykucHJvcCgnY2hlY2tlZCcpID8gJ2NoZWNrZWQnIDogJyc7XHJcblx0XHRcdGNvbnN0IGRpc2FibGVkID0gJCh0aGlzKS5wcm9wKCdkaXNhYmxlZCcpID8gJ2Rpc2FibGVkJyA6ICcnO1xyXG5cdFx0XHRsZXQgdGl0bGUgPSAkKHRoaXMpLmF0dHIoJ3RpdGxlJyk7XHJcblx0XHRcdFxyXG5cdFx0XHQkKHRoaXMpXHJcblx0XHRcdFx0LmNzcyh7XHJcblx0XHRcdFx0XHRwb3NpdGlvbjogJ2Fic29sdXRlJyxcclxuXHRcdFx0XHRcdGxlZnQ6ICctMTAwMDAwcHgnXHJcblx0XHRcdFx0fSlcclxuXHRcdFx0XHQud3JhcChgPHNwYW4gY2xhc3M9XCJzaW5nbGUtY2hlY2tib3ggJHtjaGVja2VkfSAke2Rpc2FibGVkfVwiICR7dGl0bGUgPyB0aXRsZT1gXCIke3RpdGxlfVwiYCA6ICcnfT48L3NwYW4+YClcclxuXHRcdFx0XHQucGFyZW50KClcclxuXHRcdFx0XHQuYXBwZW5kKCc8aSBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9pPicpO1xyXG5cdFx0XHRcclxuXHRcdFx0JCh0aGlzKVxyXG5cdFx0XHRcdC5vbignZm9jdXMnLCBfb25DaGVja2JveEZvY3VzKVxyXG5cdFx0XHRcdC5vbignYmx1cicsIF9vbkNoZWNrYm94Qmx1cilcclxuXHRcdFx0XHQub24oJ2NoYW5nZScsIF9vbkNoZWNrYm94Q2hhbmdlKTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBDaGVja2JveCBDaGFuZ2VcclxuXHQgKlxyXG5cdCAqIFRoaXMgZXZlbnQgaGFuZGxlciB3aWxsIG1ha2Ugc3VyZSB0aGF0IHRoZSBwYXJlbnQgaGFzIHRoZSBjb3JyZWN0IGNsYXNzZXMgZGVwZW5kaW5nIHRoZSBjaGVja2JveCBzdGF0ZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25DaGVja2JveENoYW5nZSgpIHtcclxuXHRcdGNvbnN0ICR3cmFwcGVyID0gJCh0aGlzKS5wYXJlbnQoKTtcclxuXHRcdGNvbnN0IGlzQ2hlY2tlZCA9ICQodGhpcykucHJvcCgnY2hlY2tlZCcpO1xyXG5cdFx0XHJcblx0XHRpZiAoaXNDaGVja2VkICYmICEkd3JhcHBlci5oYXNDbGFzcygnY2hlY2tlZCcpKSB7XHJcblx0XHRcdCR3cmFwcGVyLmFkZENsYXNzKCdjaGVja2VkJyk7XHJcblx0XHR9IGVsc2UgaWYgKCFpc0NoZWNrZWQgJiYgJHdyYXBwZXIuaGFzQ2xhc3MoJ2NoZWNrZWQnKSkge1xyXG5cdFx0XHQkd3JhcHBlci5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBDaGVja2JveCBGb2N1c1xyXG5cdCAqXHJcblx0ICogVGhpcyBldmVudCBoYW5kbGVyIHdpbGwgYWRkIHRoZSBcImZvY3VzZWRcIiBjbGFzcyB3aGljaCBpcyB1c2VkIGZvciBzdHlsaW5nLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkNoZWNrYm94Rm9jdXMoKSB7XHJcblx0XHQkKHRoaXMpLnBhcmVudCgpLmFkZENsYXNzKCdmb2N1c2VkJyk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIENoZWNrYm94IEJsdXJcclxuXHQgKlxyXG5cdCAqIFRoaXMgZXZlbnQgaGFuZGxlciB3aWxsIHJlbW92ZSB0aGUgXCJmb2N1c2VkXCIgY2xhc3Mgd2hpY2ggaXMgdXNlZCBmb3Igc3R5bGluZy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25DaGVja2JveEJsdXIoKSB7XHJcblx0XHQkKHRoaXMpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFdyYXBwZXIgQ2xpY2tcclxuXHQgKlxyXG5cdCAqIFRoaXMgZXZlbnQgaGFuZGxlciB3aWxsIGRlbGVnYXRlIHRoZSBjbGljayB0byB0aGUgY2hlY2tib3ggYW5kIG11c3Qgbm90IGNoYW5nZSB0aGUgc3RhdGUgb2YgdGhlIHdpZGdldC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBldmVudCB7b2JqZWN0fVxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbldyYXBwZXJDbGljayhldmVudCkge1xyXG5cdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblx0XHRcclxuXHRcdGlmICgkKHRoaXMpLmhhc0NsYXNzKCdkaXNhYmxlZCcpIHx8ICR0aGlzLmZpbmQoJy5kYXRhVGFibGVzX2VtcHR5JykubGVuZ3RoKSB7XHJcblx0XHRcdHJldHVybjtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0Y29uc3QgJGNoZWNrYm94ID0gJCh0aGlzKS5jaGlsZHJlbignaW5wdXQ6Y2hlY2tib3gnKTtcclxuXHRcdFxyXG5cdFx0JGNoZWNrYm94XHJcblx0XHRcdC5wcm9wKCdjaGVja2VkJywgISRjaGVja2JveC5wcm9wKCdjaGVja2VkJykpXHJcblx0XHRcdC50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHR9XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSU5JVElBTElaQVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdF9hZGRQdWJsaWNNZXRob2QoKTsgXHJcblx0XHRfd3JhcENoZWNrYm94RWxlbWVudHMoKTtcclxuXHRcdFxyXG5cdFx0JHRoaXNcclxuXHRcdFx0Lm9mZignY2xpY2snLCAnLnNpbmdsZS1jaGVja2JveCcpXHJcblx0XHRcdC5vbignY2xpY2snLCAnLnNpbmdsZS1jaGVja2JveCcsIF9vbldyYXBwZXJDbGljayk7XHJcblx0XHRcclxuXHRcdCR0aGlzLnRyaWdnZXIoJ3NpbmdsZV9jaGVja2JveDpyZWFkeScpOyAvLyBOZWVkZWQgZm9yIHRoZSBjaGVja2JveF9tYXBwaW5nLmpzIGZpbGUuXHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcbn0pOyBcclxuIl19
