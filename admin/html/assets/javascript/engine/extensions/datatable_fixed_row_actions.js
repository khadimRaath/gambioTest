'use strict';

/* --------------------------------------------------------------
 datatable_fixed_row_actions.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable fixed table row actions that do not hide on mouse leave.
 * 
 * By default the actions will be hidden when on mouse leave event. This module will make sure that they 
 * stay visible.
 *
 * @module Admin/Extensions/datatable_fixed_row_actions
 */
gx.extensions.module('datatable_fixed_row_actions', [], function (data) {

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
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On Table Row Mouse Leave
  *
  * The dropdown must remain visible if it was open when the cursor leaves the table row.
  */
	function _onTableRowMouseLeave() {
		var visibility = $(this).find('.btn-group.dropdown').hasClass('open') ? 'visible' : '';
		$(this).find('.actions .visible-on-hover').css('visibility', visibility);
	}

	/**
  * On Bootstrap Dropdown Menu Toggle
  *
  * Remove any custom visibility set by this module whenever the user interacts with a dropdown toggle.
  */
	function _onBootstrapDropdownToggle() {
		$this.find('.actions .visible-on-hover').css('visibility', '');
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('mouseleave', 'tr', _onTableRowMouseLeave).on('shown.bs.dropdown hidden.bs.dropdown', _onBootstrapDropdownToggle);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9maXhlZF9yb3dfYWN0aW9ucy5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiX29uVGFibGVSb3dNb3VzZUxlYXZlIiwidmlzaWJpbGl0eSIsImZpbmQiLCJoYXNDbGFzcyIsImNzcyIsIl9vbkJvb3RzdHJhcERyb3Bkb3duVG9nZ2xlIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7OztBQVFBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FBcUIsNkJBQXJCLEVBQW9ELEVBQXBELEVBQXdELFVBQVNDLElBQVQsRUFBZTs7QUFFdEU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTLEVBQWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNJLHFCQUFULEdBQWlDO0FBQ2hDLE1BQU1DLGFBQWFGLEVBQUUsSUFBRixFQUFRRyxJQUFSLENBQWEscUJBQWIsRUFBb0NDLFFBQXBDLENBQTZDLE1BQTdDLElBQXVELFNBQXZELEdBQW1FLEVBQXRGO0FBQ0FKLElBQUUsSUFBRixFQUFRRyxJQUFSLENBQWEsNEJBQWIsRUFBMkNFLEdBQTNDLENBQStDLFlBQS9DLEVBQTZESCxVQUE3RDtBQUNBOztBQUVEOzs7OztBQUtBLFVBQVNJLDBCQUFULEdBQXNDO0FBQ3JDUCxRQUFNSSxJQUFOLENBQVcsNEJBQVgsRUFBeUNFLEdBQXpDLENBQTZDLFlBQTdDLEVBQTJELEVBQTNEO0FBQ0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBUixRQUFPVSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCVCxRQUNFVSxFQURGLENBQ0ssWUFETCxFQUNtQixJQURuQixFQUN5QlIscUJBRHpCLEVBRUVRLEVBRkYsQ0FFSyxzQ0FGTCxFQUU2Q0gsMEJBRjdDOztBQUlBRTtBQUNBLEVBTkQ7O0FBUUEsUUFBT1gsTUFBUDtBQUVBLENBM0REIiwiZmlsZSI6ImRhdGF0YWJsZV9maXhlZF9yb3dfYWN0aW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBkYXRhdGFibGVfZml4ZWRfcm93X2FjdGlvbnMuanMgMjAxNi0wNy0xM1xyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiAjIyBFbmFibGUgZml4ZWQgdGFibGUgcm93IGFjdGlvbnMgdGhhdCBkbyBub3QgaGlkZSBvbiBtb3VzZSBsZWF2ZS5cclxuICogXHJcbiAqIEJ5IGRlZmF1bHQgdGhlIGFjdGlvbnMgd2lsbCBiZSBoaWRkZW4gd2hlbiBvbiBtb3VzZSBsZWF2ZSBldmVudC4gVGhpcyBtb2R1bGUgd2lsbCBtYWtlIHN1cmUgdGhhdCB0aGV5IFxyXG4gKiBzdGF5IHZpc2libGUuXHJcbiAqXHJcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy9kYXRhdGFibGVfZml4ZWRfcm93X2FjdGlvbnNcclxuICovXHJcbmd4LmV4dGVuc2lvbnMubW9kdWxlKCdkYXRhdGFibGVfZml4ZWRfcm93X2FjdGlvbnMnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBGVU5DVElPTlNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBUYWJsZSBSb3cgTW91c2UgTGVhdmVcclxuXHQgKlxyXG5cdCAqIFRoZSBkcm9wZG93biBtdXN0IHJlbWFpbiB2aXNpYmxlIGlmIGl0IHdhcyBvcGVuIHdoZW4gdGhlIGN1cnNvciBsZWF2ZXMgdGhlIHRhYmxlIHJvdy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25UYWJsZVJvd01vdXNlTGVhdmUoKSB7XHJcblx0XHRjb25zdCB2aXNpYmlsaXR5ID0gJCh0aGlzKS5maW5kKCcuYnRuLWdyb3VwLmRyb3Bkb3duJykuaGFzQ2xhc3MoJ29wZW4nKSA/ICd2aXNpYmxlJyA6ICcnO1xyXG5cdFx0JCh0aGlzKS5maW5kKCcuYWN0aW9ucyAudmlzaWJsZS1vbi1ob3ZlcicpLmNzcygndmlzaWJpbGl0eScsIHZpc2liaWxpdHkpO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBCb290c3RyYXAgRHJvcGRvd24gTWVudSBUb2dnbGVcclxuXHQgKlxyXG5cdCAqIFJlbW92ZSBhbnkgY3VzdG9tIHZpc2liaWxpdHkgc2V0IGJ5IHRoaXMgbW9kdWxlIHdoZW5ldmVyIHRoZSB1c2VyIGludGVyYWN0cyB3aXRoIGEgZHJvcGRvd24gdG9nZ2xlLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkJvb3RzdHJhcERyb3Bkb3duVG9nZ2xlKCkge1xyXG5cdFx0JHRoaXMuZmluZCgnLmFjdGlvbnMgLnZpc2libGUtb24taG92ZXInKS5jc3MoJ3Zpc2liaWxpdHknLCAnJyk7XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkdGhpc1xyXG5cdFx0XHQub24oJ21vdXNlbGVhdmUnLCAndHInLCBfb25UYWJsZVJvd01vdXNlTGVhdmUpXHJcblx0XHRcdC5vbignc2hvd24uYnMuZHJvcGRvd24gaGlkZGVuLmJzLmRyb3Bkb3duJywgX29uQm9vdHN0cmFwRHJvcGRvd25Ub2dnbGUpO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsiXX0=
