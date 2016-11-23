'use strict';

/* --------------------------------------------------------------
 orders_edit_controller.js 2015-08-24 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Edit Controller
 *
 * This controller contains the mapping logic of orders edit table.
 *
 * @module Compatibility/orders_edit_controller
 */
gx.compatibility.module('orders_edit_controller', [],

/**  @lends module:Compatibility/orders_edit_controller */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};
	// ------------------------------------------------------------------------
	// OPERATIONS
	// ------------------------------------------------------------------------

	/**
  * Map trash icon to submit button
  */
	$('[data-new-delete-button]').on('click', function () {
		$(this).closest('form[name="product_option_delete"]').submit();
	});

	/**
  * Hide the original submit and save button and set the icon
  * font size to 1 em
  */
	$(document).ready($('[name="save_original"]').hide(), $(this).find('.btn-delete').closest('form').find(':submit').hide(), $(this).find('.fa-trash-o').css('font-size', '16px'));

	/**
  * Map the new save button to the old one on click
  */
	$('[data-new-save-button]').on('click', function (e) {
		e.preventDefault();

		$(this).closest('tr').find('[name="save_original"]').click();
	});

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfZWRpdF9jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJvbiIsImNsb3Nlc3QiLCJzdWJtaXQiLCJkb2N1bWVudCIsInJlYWR5IiwiaGlkZSIsImZpbmQiLCJjc3MiLCJlIiwicHJldmVudERlZmF1bHQiLCJjbGljayIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyx3QkFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7QUE0QkE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUcsR0FBRSwwQkFBRixFQUE4QkksRUFBOUIsQ0FBaUMsT0FBakMsRUFBMEMsWUFBVztBQUNwREosSUFBRSxJQUFGLEVBQ0VLLE9BREYsQ0FDVSxvQ0FEVixFQUVFQyxNQUZGO0FBR0EsRUFKRDs7QUFNQTs7OztBQUlBTixHQUFFTyxRQUFGLEVBQVlDLEtBQVosQ0FDQ1IsRUFBRSx3QkFBRixFQUE0QlMsSUFBNUIsRUFERCxFQUVDVCxFQUFFLElBQUYsRUFBUVUsSUFBUixDQUFhLGFBQWIsRUFBNEJMLE9BQTVCLENBQW9DLE1BQXBDLEVBQTRDSyxJQUE1QyxDQUFpRCxTQUFqRCxFQUE0REQsSUFBNUQsRUFGRCxFQUdDVCxFQUFFLElBQUYsRUFBUVUsSUFBUixDQUFhLGFBQWIsRUFBNEJDLEdBQTVCLENBQWdDLFdBQWhDLEVBQTZDLE1BQTdDLENBSEQ7O0FBTUE7OztBQUdBWCxHQUFFLHdCQUFGLEVBQTRCSSxFQUE1QixDQUErQixPQUEvQixFQUF3QyxVQUFTUSxDQUFULEVBQVk7QUFDbkRBLElBQUVDLGNBQUY7O0FBRUFiLElBQUUsSUFBRixFQUNFSyxPQURGLENBQ1UsSUFEVixFQUVFSyxJQUZGLENBRU8sd0JBRlAsRUFHRUksS0FIRjtBQUlBLEVBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBakIsUUFBT2tCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJBO0FBQ0EsRUFGRDs7QUFJQSxRQUFPbkIsTUFBUDtBQUNBLENBdkZGIiwiZmlsZSI6Im9yZGVycy9vcmRlcnNfZWRpdF9jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvcmRlcnNfZWRpdF9jb250cm9sbGVyLmpzIDIwMTUtMDgtMjQgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIE9yZGVycyBFZGl0IENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgY29udGFpbnMgdGhlIG1hcHBpbmcgbG9naWMgb2Ygb3JkZXJzIGVkaXQgdGFibGUuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L29yZGVyc19lZGl0X2NvbnRyb2xsZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdvcmRlcnNfZWRpdF9jb250cm9sbGVyJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvb3JkZXJzX2VkaXRfY29udHJvbGxlciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBPUEVSQVRJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwIHRyYXNoIGljb24gdG8gc3VibWl0IGJ1dHRvblxuXHRcdCAqL1xuXHRcdCQoJ1tkYXRhLW5ldy1kZWxldGUtYnV0dG9uXScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0JCh0aGlzKVxuXHRcdFx0XHQuY2xvc2VzdCgnZm9ybVtuYW1lPVwicHJvZHVjdF9vcHRpb25fZGVsZXRlXCJdJylcblx0XHRcdFx0LnN1Ym1pdCgpO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhpZGUgdGhlIG9yaWdpbmFsIHN1Ym1pdCBhbmQgc2F2ZSBidXR0b24gYW5kIHNldCB0aGUgaWNvblxuXHRcdCAqIGZvbnQgc2l6ZSB0byAxIGVtXG5cdFx0ICovXG5cdFx0JChkb2N1bWVudCkucmVhZHkoXG5cdFx0XHQkKCdbbmFtZT1cInNhdmVfb3JpZ2luYWxcIl0nKS5oaWRlKCksXG5cdFx0XHQkKHRoaXMpLmZpbmQoJy5idG4tZGVsZXRlJykuY2xvc2VzdCgnZm9ybScpLmZpbmQoJzpzdWJtaXQnKS5oaWRlKCksXG5cdFx0XHQkKHRoaXMpLmZpbmQoJy5mYS10cmFzaC1vJykuY3NzKCdmb250LXNpemUnLCAnMTZweCcpXG5cdFx0KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNYXAgdGhlIG5ldyBzYXZlIGJ1dHRvbiB0byB0aGUgb2xkIG9uZSBvbiBjbGlja1xuXHRcdCAqL1xuXHRcdCQoJ1tkYXRhLW5ldy1zYXZlLWJ1dHRvbl0nKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcblx0XHRcdCQodGhpcylcblx0XHRcdFx0LmNsb3Nlc3QoJ3RyJylcblx0XHRcdFx0LmZpbmQoJ1tuYW1lPVwic2F2ZV9vcmlnaW5hbFwiXScpXG5cdFx0XHRcdC5jbGljaygpO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
