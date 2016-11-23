'use strict';

/* --------------------------------------------------------------
 edit_product_controller.js 2015-09-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Edit product controller
 *
 * This controller contains the dynamic form changes of the new_product page.
 *
 * @module Compatibility/edit_product_controller
 */
gx.compatibility.module('edit_product_controller', [],

/**  @lends module:Compatibility/edit_product_controller */

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
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$('.delete_personal_offer').on('click', function () {
			var t_quantity = $(this).closest('.old_personal_offer').find('input[name^="products_quantity_staffel_"]').val();
			var t_group_id = '' + $(this).closest('.personal_offers').prop('id').replace('scale_price_', '');

			$(this).closest('.personal_offers').find('.added_personal_offers').append('<input type="hidden" name="delete_products_quantity_staffel_' + t_group_id + '[]" value="' + t_quantity + '" />');
			$(this).closest('.old_personal_offer').remove();

			return false;
		});

		$('.add_personal_offer').on('click', function () {
			$(this).closest('.personal_offers').find('.added_personal_offers').append($(this).closest('.personal_offers').find('.new_personal_offer').html());
			$(this).closest('.personal_offers').find('.added_personal_offers input[name^="products_quantity_staffel_"]:last').val('');
			$(this).closest('.personal_offers').find('.added_personal_offers input[name^="products_price_staffel_"]:last').val('0');

			return false;
		});

		$('input[name=products_model]').bind('change', function () {
			if ($(this).val().match(/GIFT_/g)) {
				$('select[name=products_tax_class_id]').val(0);
				$('select[name=products_tax_class_id]').attr('disabled', 'disabled');
				$('select[name=products_tax_class_id]').parent().append('<span style="display: inline-block; margin: 0 0 0 20px; color: red;">' + '<?php echo TEXT_NO_TAX_RATE_BY_GIFT; ?></span>');
			} else if ($('select[name=products_tax_class_id]').attr('disabled')) {
				$('select[name=products_tax_class_id]').removeAttr('disabled');
				$('select[name=products_tax_class_id]').parent().find('span').remove();
			}
		});

		$('.category-details').sortable({
			// axis: 'y', 
			items: '> .tab-section',
			containment: 'parent'
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3RzL2VkaXRfcHJvZHVjdF9jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsIm9uIiwidF9xdWFudGl0eSIsImNsb3Nlc3QiLCJmaW5kIiwidmFsIiwidF9ncm91cF9pZCIsInByb3AiLCJyZXBsYWNlIiwiYXBwZW5kIiwicmVtb3ZlIiwiaHRtbCIsImJpbmQiLCJtYXRjaCIsImF0dHIiLCJwYXJlbnQiLCJyZW1vdmVBdHRyIiwic29ydGFibGUiLCJpdGVtcyIsImNvbnRhaW5tZW50Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyx5QkFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTs7QUFFQUEsUUFBT08sSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QkwsSUFBRSx3QkFBRixFQUE0Qk0sRUFBNUIsQ0FBK0IsT0FBL0IsRUFBd0MsWUFBVztBQUNsRCxPQUFJQyxhQUFhUCxFQUFFLElBQUYsRUFBUVEsT0FBUixDQUFnQixxQkFBaEIsRUFBdUNDLElBQXZDLENBQ2hCLDJDQURnQixFQUM2QkMsR0FEN0IsRUFBakI7QUFFQSxPQUFJQyxhQUFhLEtBQUtYLEVBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLGtCQUFoQixFQUFvQ0ksSUFBcEMsQ0FBeUMsSUFBekMsRUFBK0NDLE9BQS9DLENBQXVELGNBQXZELEVBQ3BCLEVBRG9CLENBQXRCOztBQUdBYixLQUFFLElBQUYsRUFBUVEsT0FBUixDQUFnQixrQkFBaEIsRUFBb0NDLElBQXBDLENBQXlDLHdCQUF6QyxFQUFtRUssTUFBbkUsQ0FDQyxpRUFBaUVILFVBQWpFLEdBQ0EsYUFEQSxHQUNnQkosVUFEaEIsR0FFQSxNQUhEO0FBSUFQLEtBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLHFCQUFoQixFQUF1Q08sTUFBdkM7O0FBRUEsVUFBTyxLQUFQO0FBQ0EsR0FiRDs7QUFlQWYsSUFBRSxxQkFBRixFQUF5Qk0sRUFBekIsQ0FBNEIsT0FBNUIsRUFBcUMsWUFBVztBQUMvQ04sS0FBRSxJQUFGLEVBQVFRLE9BQVIsQ0FBZ0Isa0JBQWhCLEVBQW9DQyxJQUFwQyxDQUF5Qyx3QkFBekMsRUFBbUVLLE1BQW5FLENBQTBFZCxFQUFFLElBQUYsRUFBUVEsT0FBUixDQUN6RSxrQkFEeUUsRUFDckRDLElBRHFELENBRXpFLHFCQUZ5RSxFQUVsRE8sSUFGa0QsRUFBMUU7QUFHQWhCLEtBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLGtCQUFoQixFQUFvQ0MsSUFBcEMsQ0FDQyx1RUFERCxFQUMwRUMsR0FEMUUsQ0FDOEUsRUFEOUU7QUFFQVYsS0FBRSxJQUFGLEVBQVFRLE9BQVIsQ0FBZ0Isa0JBQWhCLEVBQW9DQyxJQUFwQyxDQUNDLG9FQURELEVBQ3VFQyxHQUR2RSxDQUVDLEdBRkQ7O0FBSUEsVUFBTyxLQUFQO0FBQ0EsR0FYRDs7QUFhQVYsSUFBRSw0QkFBRixFQUFnQ2lCLElBQWhDLENBQXFDLFFBQXJDLEVBQStDLFlBQVc7QUFDekQsT0FBSWpCLEVBQUUsSUFBRixFQUFRVSxHQUFSLEdBQWNRLEtBQWQsQ0FBb0IsUUFBcEIsQ0FBSixFQUFtQztBQUNsQ2xCLE1BQUUsb0NBQUYsRUFBd0NVLEdBQXhDLENBQTRDLENBQTVDO0FBQ0FWLE1BQUUsb0NBQUYsRUFBd0NtQixJQUF4QyxDQUE2QyxVQUE3QyxFQUF5RCxVQUF6RDtBQUNBbkIsTUFBRSxvQ0FBRixFQUF3Q29CLE1BQXhDLEdBQWlETixNQUFqRCxDQUNDLDBFQUNBLGdEQUZEO0FBSUEsSUFQRCxNQU9PLElBQUlkLEVBQUUsb0NBQUYsRUFBd0NtQixJQUF4QyxDQUE2QyxVQUE3QyxDQUFKLEVBQThEO0FBQ3BFbkIsTUFBRSxvQ0FBRixFQUF3Q3FCLFVBQXhDLENBQW1ELFVBQW5EO0FBQ0FyQixNQUFFLG9DQUFGLEVBQXdDb0IsTUFBeEMsR0FBaURYLElBQWpELENBQXNELE1BQXRELEVBQThETSxNQUE5RDtBQUNBO0FBQ0QsR0FaRDs7QUFjQWYsSUFBRSxtQkFBRixFQUF1QnNCLFFBQXZCLENBQWdDO0FBQy9CO0FBQ0FDLFVBQU8sZ0JBRndCO0FBRy9CQyxnQkFBYTtBQUhrQixHQUFoQzs7QUFNQW5CO0FBQ0EsRUFsREQ7O0FBb0RBLFFBQU9SLE1BQVA7QUFDQSxDQXJHRiIsImZpbGUiOiJwcm9kdWN0cy9lZGl0X3Byb2R1Y3RfY29udHJvbGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZWRpdF9wcm9kdWN0X2NvbnRyb2xsZXIuanMgMjAxNS0wOS0wMSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRWRpdCBwcm9kdWN0IGNvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgY29udGFpbnMgdGhlIGR5bmFtaWMgZm9ybSBjaGFuZ2VzIG9mIHRoZSBuZXdfcHJvZHVjdCBwYWdlLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9lZGl0X3Byb2R1Y3RfY29udHJvbGxlclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2VkaXRfcHJvZHVjdF9jb250cm9sbGVyJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvZWRpdF9wcm9kdWN0X2NvbnRyb2xsZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCQoJy5kZWxldGVfcGVyc29uYWxfb2ZmZXInKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIHRfcXVhbnRpdHkgPSAkKHRoaXMpLmNsb3Nlc3QoJy5vbGRfcGVyc29uYWxfb2ZmZXInKS5maW5kKFxuXHRcdFx0XHRcdCdpbnB1dFtuYW1lXj1cInByb2R1Y3RzX3F1YW50aXR5X3N0YWZmZWxfXCJdJykudmFsKCk7XG5cdFx0XHRcdHZhciB0X2dyb3VwX2lkID0gJycgKyAkKHRoaXMpLmNsb3Nlc3QoJy5wZXJzb25hbF9vZmZlcnMnKS5wcm9wKCdpZCcpLnJlcGxhY2UoJ3NjYWxlX3ByaWNlXycsXG5cdFx0XHRcdFx0XHQnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkKHRoaXMpLmNsb3Nlc3QoJy5wZXJzb25hbF9vZmZlcnMnKS5maW5kKCcuYWRkZWRfcGVyc29uYWxfb2ZmZXJzJykuYXBwZW5kKFxuXHRcdFx0XHRcdCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJkZWxldGVfcHJvZHVjdHNfcXVhbnRpdHlfc3RhZmZlbF8nICsgdF9ncm91cF9pZCArXG5cdFx0XHRcdFx0J1tdXCIgdmFsdWU9XCInICsgdF9xdWFudGl0eSArXG5cdFx0XHRcdFx0J1wiIC8+Jyk7XG5cdFx0XHRcdCQodGhpcykuY2xvc2VzdCgnLm9sZF9wZXJzb25hbF9vZmZlcicpLnJlbW92ZSgpO1xuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoJy5hZGRfcGVyc29uYWxfb2ZmZXInKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCh0aGlzKS5jbG9zZXN0KCcucGVyc29uYWxfb2ZmZXJzJykuZmluZCgnLmFkZGVkX3BlcnNvbmFsX29mZmVycycpLmFwcGVuZCgkKHRoaXMpLmNsb3Nlc3QoXG5cdFx0XHRcdFx0Jy5wZXJzb25hbF9vZmZlcnMnKS5maW5kKFxuXHRcdFx0XHRcdCcubmV3X3BlcnNvbmFsX29mZmVyJykuaHRtbCgpKTtcblx0XHRcdFx0JCh0aGlzKS5jbG9zZXN0KCcucGVyc29uYWxfb2ZmZXJzJykuZmluZChcblx0XHRcdFx0XHQnLmFkZGVkX3BlcnNvbmFsX29mZmVycyBpbnB1dFtuYW1lXj1cInByb2R1Y3RzX3F1YW50aXR5X3N0YWZmZWxfXCJdOmxhc3QnKS52YWwoJycpO1xuXHRcdFx0XHQkKHRoaXMpLmNsb3Nlc3QoJy5wZXJzb25hbF9vZmZlcnMnKS5maW5kKFxuXHRcdFx0XHRcdCcuYWRkZWRfcGVyc29uYWxfb2ZmZXJzIGlucHV0W25hbWVePVwicHJvZHVjdHNfcHJpY2Vfc3RhZmZlbF9cIl06bGFzdCcpLnZhbChcblx0XHRcdFx0XHQnMCcpO1xuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoJ2lucHV0W25hbWU9cHJvZHVjdHNfbW9kZWxdJykuYmluZCgnY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICgkKHRoaXMpLnZhbCgpLm1hdGNoKC9HSUZUXy9nKSkge1xuXHRcdFx0XHRcdCQoJ3NlbGVjdFtuYW1lPXByb2R1Y3RzX3RheF9jbGFzc19pZF0nKS52YWwoMCk7XG5cdFx0XHRcdFx0JCgnc2VsZWN0W25hbWU9cHJvZHVjdHNfdGF4X2NsYXNzX2lkXScpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cdFx0XHRcdFx0JCgnc2VsZWN0W25hbWU9cHJvZHVjdHNfdGF4X2NsYXNzX2lkXScpLnBhcmVudCgpLmFwcGVuZChcblx0XHRcdFx0XHRcdCc8c3BhbiBzdHlsZT1cImRpc3BsYXk6IGlubGluZS1ibG9jazsgbWFyZ2luOiAwIDAgMCAyMHB4OyBjb2xvcjogcmVkO1wiPicgK1xuXHRcdFx0XHRcdFx0Jzw/cGhwIGVjaG8gVEVYVF9OT19UQVhfUkFURV9CWV9HSUZUOyA/Pjwvc3Bhbj4nXG5cdFx0XHRcdFx0KTtcblx0XHRcdFx0fSBlbHNlIGlmICgkKCdzZWxlY3RbbmFtZT1wcm9kdWN0c190YXhfY2xhc3NfaWRdJykuYXR0cignZGlzYWJsZWQnKSkge1xuXHRcdFx0XHRcdCQoJ3NlbGVjdFtuYW1lPXByb2R1Y3RzX3RheF9jbGFzc19pZF0nKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXHRcdFx0XHRcdCQoJ3NlbGVjdFtuYW1lPXByb2R1Y3RzX3RheF9jbGFzc19pZF0nKS5wYXJlbnQoKS5maW5kKCdzcGFuJykucmVtb3ZlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkKCcuY2F0ZWdvcnktZGV0YWlscycpLnNvcnRhYmxlKHtcblx0XHRcdFx0Ly8gYXhpczogJ3knLCBcblx0XHRcdFx0aXRlbXM6ICc+IC50YWItc2VjdGlvbicsXG5cdFx0XHRcdGNvbnRhaW5tZW50OiAncGFyZW50J1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
