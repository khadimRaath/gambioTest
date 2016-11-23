'use strict';

/* --------------------------------------------------------------
 actions.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Main Table Actions
 *
 * This module creates the bulk and row actions for the table.
 */
gx.controllers.module('actions', ['user_configuration_service', gx.source + '/libs/button_dropdown'], function () {

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
  * Create Bulk Actions
  *
  * This callback can be called once during the initialization of this module.
  */
	function _createBulkActions() {
		// Add actions to the bulk-action dropdown.
		var $bulkActions = $('.bulk-action');
		var defaultBulkAction = $this.data('defaultBulkAction') || 'change-status';

		jse.libs.button_dropdown.bindDefaultAction($bulkActions, jse.core.registry.get('userId'), 'ordersOverviewBulkAction', jse.libs.user_configuration_service);

		// Change status
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('BUTTON_MULTI_CHANGE_ORDER_STATUS', 'orders'),
			class: 'change-status',
			data: { configurationValue: 'change-status' },
			isDefault: defaultBulkAction === 'change-status',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Delete
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('BUTTON_MULTI_DELETE', 'orders'),
			class: 'delete',
			data: { configurationValue: 'delete' },
			isDefault: defaultBulkAction === 'delete',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Cancel
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('BUTTON_MULTI_CANCEL', 'orders'),
			class: 'cancel',
			data: { configurationValue: 'cancel' },
			isDefault: defaultBulkAction === 'cancel',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Send order confirmation.
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('BUTTON_MULTI_SEND_ORDER', 'orders'),
			class: 'bulk-email-order',
			data: { configurationValue: 'bulk-email-order' },
			isDefault: defaultBulkAction === 'bulk-email-order',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Send invoice.
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('BUTTON_MULTI_SEND_INVOICE', 'orders'),
			class: 'bulk-email-invoice',
			data: { configurationValue: 'bulk-email-invoice' },
			isDefault: defaultBulkAction === 'bulk-email-invoice',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Download invoices.
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('TITLE_INVOICE', 'orders'),
			class: 'bulk-download-invoice',
			data: { configurationValue: 'bulk-download-invoice' },
			isDefault: defaultBulkAction === 'bulk-download-invoice',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		// Download packing slips.
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('TITLE_PACKINGSLIP', 'orders'),
			class: 'bulk-download-packing-slip',
			data: { configurationValue: 'bulk-download-packing-slip' },
			isDefault: defaultBulkAction === 'bulk-download-packing-slip',
			callback: function callback(e) {
				return e.preventDefault();
			}
		});

		$this.datatable_default_actions('ensure', 'bulk');
	}

	/**
  * Create Table Row Actions
  *
  * This function must be call with every table draw.dt event.
  */
	function _createRowActions() {
		// Re-create the checkbox widgets and the row actions. 
		var defaultRowAction = $this.data('defaultRowAction') || 'edit';

		jse.libs.button_dropdown.bindDefaultAction($this.find('.btn-group.dropdown'), jse.core.registry.get('userId'), 'ordersOverviewRowAction', jse.libs.user_configuration_service);

		$this.find('.btn-group.dropdown').each(function () {
			var orderId = $(this).parents('tr').data('id');

			// Edit
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TEXT_SHOW', 'orders'),
				href: 'orders.php?oID=' + orderId + '&action=edit',
				class: 'edit',
				data: { configurationValue: 'edit' },
				isDefault: defaultRowAction === 'edit',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Change Status
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TEXT_GM_STATUS', 'orders'),
				class: 'change-status',
				data: { configurationValue: 'change-status' },
				isDefault: defaultRowAction === 'change-status',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Delete
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('BUTTON_MULTI_DELETE', 'orders'),
				class: 'delete',
				data: { configurationValue: 'delete' },
				isDefault: defaultRowAction === 'delete',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Cancel
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('BUTTON_GM_CANCEL', 'orders'),
				class: 'cancel',
				data: { configurationValue: 'cancel' },
				isDefault: defaultRowAction === 'cancel',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Invoice
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_INVOICE', 'orders'),
				href: 'gm_pdf_order.php?oID=' + orderId + '&type=invoice',
				target: '_blank',
				class: 'invoice',
				data: { configurationValue: 'invoice' },
				isDefault: defaultRowAction === 'invoice',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Email Invoice
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_INVOICE_MAIL', 'orders'),
				class: 'email-invoice',
				data: { configurationValue: 'email-invoice' },
				isDefault: defaultRowAction === 'email-invoice',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Packing Slip
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_PACKINGSLIP', 'orders'),
				href: 'gm_pdf_order.php?oID=' + orderId + '&type=packingslip',
				target: '_blank',
				class: 'packing-slip',
				data: { configurationValue: 'packing-slip' },
				isDefault: defaultRowAction === 'packing-slip'
			});

			// Show Order Acceptance
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_ORDER', 'orders'),
				href: 'gm_send_order.php?oID=' + orderId + '&type=order',
				target: '_blank',
				class: 'show-acceptance',
				data: { configurationValue: 'show-acceptance' },
				isDefault: defaultRowAction === 'show-acceptance'
			});

			// Recreate Order Acceptance
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_RECREATE_ORDER', 'orders'),
				href: 'gm_send_order.php?oID=' + orderId + '&type=recreate_order',
				target: '_blank',
				class: 'recreate-order-acceptance',
				data: { configurationValue: 'recreate-order-acceptance' },
				isDefault: defaultRowAction === 'recreate-order-acceptance'
			});

			// Email Order
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TITLE_SEND_ORDER', 'orders'),
				class: 'email-order',
				data: { configurationValue: 'email-order' },
				isDefault: defaultRowAction === 'email-order',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			// Create Withdrawal
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TEXT_CREATE_WITHDRAWAL', 'orders'),
				href: '../withdrawal.php?order_id=' + orderId,
				target: '_blank',
				class: 'create-withdrawal',
				data: { configurationValue: 'create-withdrawal' },
				isDefault: defaultRowAction === 'create-withdrawal'
			});

			// Add Tracking Code
			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('TXT_PARCEL_TRACKING_SENDBUTTON_TITLE', 'parcel_services'),
				class: 'add-tracking-number',
				data: { configurationValue: 'add-tracking-number' },
				isDefault: defaultRowAction === 'add-tracking-number',
				callback: function callback(e) {
					return e.preventDefault();
				}
			});

			$this.datatable_default_actions('ensure', 'row');
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$(window).on('JSENGINE_INIT_FINISHED', function () {
			$this.on('draw.dt', _createRowActions);
			_createRowActions();
			_createBulkActions();
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy9hY3Rpb25zLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJzb3VyY2UiLCIkdGhpcyIsIiQiLCJfY3JlYXRlQnVsa0FjdGlvbnMiLCIkYnVsa0FjdGlvbnMiLCJkZWZhdWx0QnVsa0FjdGlvbiIsImRhdGEiLCJqc2UiLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwiYmluZERlZmF1bHRBY3Rpb24iLCJjb3JlIiwicmVnaXN0cnkiLCJnZXQiLCJ1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSIsImFkZEFjdGlvbiIsInRleHQiLCJsYW5nIiwidHJhbnNsYXRlIiwiY2xhc3MiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJpc0RlZmF1bHQiLCJjYWxsYmFjayIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMiLCJfY3JlYXRlUm93QWN0aW9ucyIsImRlZmF1bHRSb3dBY3Rpb24iLCJmaW5kIiwiZWFjaCIsIm9yZGVySWQiLCJwYXJlbnRzIiwiaHJlZiIsInRhcmdldCIsImluaXQiLCJkb25lIiwid2luZG93Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0MsU0FERCxFQUdDLENBQUMsNEJBQUQsRUFBa0NGLEdBQUdHLE1BQXJDLDJCQUhELEVBS0MsWUFBVzs7QUFFVjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0EsVUFBU0ksa0JBQVQsR0FBOEI7QUFDN0I7QUFDQSxNQUFNQyxlQUFlRixFQUFFLGNBQUYsQ0FBckI7QUFDQSxNQUFNRyxvQkFBb0JKLE1BQU1LLElBQU4sQ0FBVyxtQkFBWCxLQUFtQyxlQUE3RDs7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YixFQUF1QyxZQUFNO0FBQzVDcEMsU0FBTW9DLEVBQU4sQ0FBUyxTQUFULEVBQW9CWCxpQkFBcEI7QUFDQUE7QUFDQXZCO0FBQ0EsR0FKRDs7QUFNQWdDO0FBQ0EsRUFSRDs7QUFVQSxRQUFPcEMsTUFBUDtBQUVBLENBcFFGIiwiZmlsZSI6Im9yZGVycy9vdmVydmlldy9hY3Rpb25zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGFjdGlvbnMuanMgMjAxNi0wNi0yMFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBNYWluIFRhYmxlIEFjdGlvbnNcclxuICpcclxuICogVGhpcyBtb2R1bGUgY3JlYXRlcyB0aGUgYnVsayBhbmQgcm93IGFjdGlvbnMgZm9yIHRoZSB0YWJsZS5cclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcclxuXHQnYWN0aW9ucycsXHJcblx0XHJcblx0Wyd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZScsIGAke2d4LnNvdXJjZX0vbGlicy9idXR0b25fZHJvcGRvd25gXSxcclxuXHRcclxuXHRmdW5jdGlvbigpIHtcclxuXHRcdFxyXG5cdFx0J3VzZSBzdHJpY3QnO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFZBUklBQkxFU1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge2pRdWVyeX1cclxuXHRcdCAqL1xyXG5cdFx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0XHQgKi9cclxuXHRcdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIEZVTkNUSU9OU1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogQ3JlYXRlIEJ1bGsgQWN0aW9uc1xyXG5cdFx0ICpcclxuXHRcdCAqIFRoaXMgY2FsbGJhY2sgY2FuIGJlIGNhbGxlZCBvbmNlIGR1cmluZyB0aGUgaW5pdGlhbGl6YXRpb24gb2YgdGhpcyBtb2R1bGUuXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9jcmVhdGVCdWxrQWN0aW9ucygpIHtcclxuXHRcdFx0Ly8gQWRkIGFjdGlvbnMgdG8gdGhlIGJ1bGstYWN0aW9uIGRyb3Bkb3duLlxyXG5cdFx0XHRjb25zdCAkYnVsa0FjdGlvbnMgPSAkKCcuYnVsay1hY3Rpb24nKTtcclxuXHRcdFx0Y29uc3QgZGVmYXVsdEJ1bGtBY3Rpb24gPSAkdGhpcy5kYXRhKCdkZWZhdWx0QnVsa0FjdGlvbicpIHx8ICdjaGFuZ2Utc3RhdHVzJztcclxuXHRcdFx0XHJcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5iaW5kRGVmYXVsdEFjdGlvbigkYnVsa0FjdGlvbnMsIGpzZS5jb3JlLnJlZ2lzdHJ5LmdldCgndXNlcklkJyksXHJcblx0XHRcdFx0J29yZGVyc092ZXJ2aWV3QnVsa0FjdGlvbicsIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlKTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIENoYW5nZSBzdGF0dXNcclxuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkYnVsa0FjdGlvbnMsIHtcclxuXHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQlVUVE9OX01VTFRJX0NIQU5HRV9PUkRFUl9TVEFUVVMnLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0Y2xhc3M6ICdjaGFuZ2Utc3RhdHVzJyxcclxuXHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnY2hhbmdlLXN0YXR1cyd9LFxyXG5cdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdEJ1bGtBY3Rpb24gPT09ICdjaGFuZ2Utc3RhdHVzJyxcclxuXHRcdFx0XHRjYWxsYmFjazogZSA9PiBlLnByZXZlbnREZWZhdWx0KClcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBEZWxldGVcclxuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkYnVsa0FjdGlvbnMsIHtcclxuXHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQlVUVE9OX01VTFRJX0RFTEVURScsICdvcmRlcnMnKSxcclxuXHRcdFx0XHRjbGFzczogJ2RlbGV0ZScsXHJcblx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ2RlbGV0ZSd9LFxyXG5cdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdEJ1bGtBY3Rpb24gPT09ICdkZWxldGUnLFxyXG5cdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIENhbmNlbFxyXG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCRidWxrQWN0aW9ucywge1xyXG5cdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVVRUT05fTVVMVElfQ0FOQ0VMJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdGNsYXNzOiAnY2FuY2VsJyxcclxuXHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnY2FuY2VsJ30sXHJcblx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0QnVsa0FjdGlvbiA9PT0gJ2NhbmNlbCcsXHJcblx0XHRcdFx0Y2FsbGJhY2s6IGUgPT4gZS5wcmV2ZW50RGVmYXVsdCgpXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gU2VuZCBvcmRlciBjb25maXJtYXRpb24uXHJcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJGJ1bGtBY3Rpb25zLCB7XHJcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JVVFRPTl9NVUxUSV9TRU5EX09SREVSJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdGNsYXNzOiAnYnVsay1lbWFpbC1vcmRlcicsXHJcblx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ2J1bGstZW1haWwtb3JkZXInfSxcclxuXHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRCdWxrQWN0aW9uID09PSAnYnVsay1lbWFpbC1vcmRlcicsXHJcblx0XHRcdFx0Y2FsbGJhY2s6IGUgPT4gZS5wcmV2ZW50RGVmYXVsdCgpXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gU2VuZCBpbnZvaWNlLlxyXG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCRidWxrQWN0aW9ucywge1xyXG5cdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVVRUT05fTVVMVElfU0VORF9JTlZPSUNFJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdGNsYXNzOiAnYnVsay1lbWFpbC1pbnZvaWNlJyxcclxuXHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnYnVsay1lbWFpbC1pbnZvaWNlJ30sXHJcblx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0QnVsa0FjdGlvbiA9PT0gJ2J1bGstZW1haWwtaW52b2ljZScsXHJcblx0XHRcdFx0Y2FsbGJhY2s6IGUgPT4gZS5wcmV2ZW50RGVmYXVsdCgpXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gRG93bmxvYWQgaW52b2ljZXMuXHJcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJGJ1bGtBY3Rpb25zLCB7XHJcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RJVExFX0lOVk9JQ0UnLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0Y2xhc3M6ICdidWxrLWRvd25sb2FkLWludm9pY2UnLFxyXG5cdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdidWxrLWRvd25sb2FkLWludm9pY2UnfSxcclxuXHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRCdWxrQWN0aW9uID09PSAnYnVsay1kb3dubG9hZC1pbnZvaWNlJyxcclxuXHRcdFx0XHRjYWxsYmFjazogZSA9PiBlLnByZXZlbnREZWZhdWx0KClcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBEb3dubG9hZCBwYWNraW5nIHNsaXBzLlxyXG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCRidWxrQWN0aW9ucywge1xyXG5cdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdUSVRMRV9QQUNLSU5HU0xJUCcsICdvcmRlcnMnKSxcclxuXHRcdFx0XHRjbGFzczogJ2J1bGstZG93bmxvYWQtcGFja2luZy1zbGlwJyxcclxuXHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnYnVsay1kb3dubG9hZC1wYWNraW5nLXNsaXAnfSxcclxuXHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRCdWxrQWN0aW9uID09PSAnYnVsay1kb3dubG9hZC1wYWNraW5nLXNsaXAnLFxyXG5cdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzLmRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMoJ2Vuc3VyZScsICdidWxrJyk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogQ3JlYXRlIFRhYmxlIFJvdyBBY3Rpb25zXHJcblx0XHQgKlxyXG5cdFx0ICogVGhpcyBmdW5jdGlvbiBtdXN0IGJlIGNhbGwgd2l0aCBldmVyeSB0YWJsZSBkcmF3LmR0IGV2ZW50LlxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBfY3JlYXRlUm93QWN0aW9ucygpIHtcclxuXHRcdFx0Ly8gUmUtY3JlYXRlIHRoZSBjaGVja2JveCB3aWRnZXRzIGFuZCB0aGUgcm93IGFjdGlvbnMuIFxyXG5cdFx0XHRjb25zdCBkZWZhdWx0Um93QWN0aW9uID0gJHRoaXMuZGF0YSgnZGVmYXVsdFJvd0FjdGlvbicpIHx8ICdlZGl0JztcclxuXHRcdFx0XHJcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5iaW5kRGVmYXVsdEFjdGlvbigkdGhpcy5maW5kKCcuYnRuLWdyb3VwLmRyb3Bkb3duJyksIFxyXG5cdFx0XHRcdGpzZS5jb3JlLnJlZ2lzdHJ5LmdldCgndXNlcklkJyksICdvcmRlcnNPdmVydmlld1Jvd0FjdGlvbicsIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlKTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzLmZpbmQoJy5idG4tZ3JvdXAuZHJvcGRvd24nKS5lYWNoKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBFZGl0XHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XHJcblx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVEVYVF9TSE9XJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdFx0aHJlZjogYG9yZGVycy5waHA/b0lEPSR7b3JkZXJJZH0mYWN0aW9uPWVkaXRgLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdlZGl0JyxcclxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdlZGl0J30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdlZGl0JyxcclxuXHRcdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIENoYW5nZSBTdGF0dXNcclxuXHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCQodGhpcyksIHtcclxuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0dNX1NUQVRVUycsICdvcmRlcnMnKSxcclxuXHRcdFx0XHRcdGNsYXNzOiAnY2hhbmdlLXN0YXR1cycsXHJcblx0XHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnY2hhbmdlLXN0YXR1cyd9LFxyXG5cdFx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0Um93QWN0aW9uID09PSAnY2hhbmdlLXN0YXR1cycsXHJcblx0XHRcdFx0XHRjYWxsYmFjazogZSA9PiBlLnByZXZlbnREZWZhdWx0KClcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBEZWxldGVcclxuXHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCQodGhpcyksIHtcclxuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVVRUT05fTVVMVElfREVMRVRFJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdkZWxldGUnLFxyXG5cdFx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ2RlbGV0ZSd9LFxyXG5cdFx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0Um93QWN0aW9uID09PSAnZGVsZXRlJyxcclxuXHRcdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIENhbmNlbFxyXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xyXG5cdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JVVFRPTl9HTV9DQU5DRUwnLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0XHRjbGFzczogJ2NhbmNlbCcsXHJcblx0XHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnY2FuY2VsJ30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdjYW5jZWwnLFxyXG5cdFx0XHRcdFx0Y2FsbGJhY2s6IGUgPT4gZS5wcmV2ZW50RGVmYXVsdCgpXHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gSW52b2ljZVxyXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xyXG5cdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RJVExFX0lOVk9JQ0UnLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0XHRocmVmOiBgZ21fcGRmX29yZGVyLnBocD9vSUQ9JHtvcmRlcklkfSZ0eXBlPWludm9pY2VgLFxyXG5cdFx0XHRcdFx0dGFyZ2V0OiAnX2JsYW5rJyxcclxuXHRcdFx0XHRcdGNsYXNzOiAnaW52b2ljZScsXHJcblx0XHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnaW52b2ljZSd9LFxyXG5cdFx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0Um93QWN0aW9uID09PSAnaW52b2ljZScsXHJcblx0XHRcdFx0XHRjYWxsYmFjazogZSA9PiBlLnByZXZlbnREZWZhdWx0KClcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBFbWFpbCBJbnZvaWNlXHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XHJcblx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVElUTEVfSU5WT0lDRV9NQUlMJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdlbWFpbC1pbnZvaWNlJyxcclxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdlbWFpbC1pbnZvaWNlJ30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdlbWFpbC1pbnZvaWNlJyxcclxuXHRcdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIFBhY2tpbmcgU2xpcFxyXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xyXG5cdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RJVExFX1BBQ0tJTkdTTElQJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdFx0aHJlZjogYGdtX3BkZl9vcmRlci5waHA/b0lEPSR7b3JkZXJJZH0mdHlwZT1wYWNraW5nc2xpcGAsXHJcblx0XHRcdFx0XHR0YXJnZXQ6ICdfYmxhbmsnLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdwYWNraW5nLXNsaXAnLFxyXG5cdFx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ3BhY2tpbmctc2xpcCd9LFxyXG5cdFx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0Um93QWN0aW9uID09PSAncGFja2luZy1zbGlwJ1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIFNob3cgT3JkZXIgQWNjZXB0YW5jZVxyXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xyXG5cdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RJVExFX09SREVSJywgJ29yZGVycycpLFxyXG5cdFx0XHRcdFx0aHJlZjogYGdtX3NlbmRfb3JkZXIucGhwP29JRD0ke29yZGVySWR9JnR5cGU9b3JkZXJgLFxyXG5cdFx0XHRcdFx0dGFyZ2V0OiAnX2JsYW5rJyxcclxuXHRcdFx0XHRcdGNsYXNzOiAnc2hvdy1hY2NlcHRhbmNlJyxcclxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdzaG93LWFjY2VwdGFuY2UnfSxcclxuXHRcdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdFJvd0FjdGlvbiA9PT0gJ3Nob3ctYWNjZXB0YW5jZSdcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBSZWNyZWF0ZSBPcmRlciBBY2NlcHRhbmNlXHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XHJcblx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVElUTEVfUkVDUkVBVEVfT1JERVInLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0XHRocmVmOiBgZ21fc2VuZF9vcmRlci5waHA/b0lEPSR7b3JkZXJJZH0mdHlwZT1yZWNyZWF0ZV9vcmRlcmAsXHJcblx0XHRcdFx0XHR0YXJnZXQ6ICdfYmxhbmsnLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdyZWNyZWF0ZS1vcmRlci1hY2NlcHRhbmNlJyxcclxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdyZWNyZWF0ZS1vcmRlci1hY2NlcHRhbmNlJ30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdyZWNyZWF0ZS1vcmRlci1hY2NlcHRhbmNlJ1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIEVtYWlsIE9yZGVyXHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XHJcblx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVElUTEVfU0VORF9PUkRFUicsICdvcmRlcnMnKSxcclxuXHRcdFx0XHRcdGNsYXNzOiAnZW1haWwtb3JkZXInLFxyXG5cdFx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ2VtYWlsLW9yZGVyJ30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdlbWFpbC1vcmRlcicsXHJcblx0XHRcdFx0XHRjYWxsYmFjazogZSA9PiBlLnByZXZlbnREZWZhdWx0KClcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBDcmVhdGUgV2l0aGRyYXdhbFxyXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xyXG5cdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RFWFRfQ1JFQVRFX1dJVEhEUkFXQUwnLCAnb3JkZXJzJyksXHJcblx0XHRcdFx0XHRocmVmOiBgLi4vd2l0aGRyYXdhbC5waHA/b3JkZXJfaWQ9JHtvcmRlcklkfWAsXHJcblx0XHRcdFx0XHR0YXJnZXQ6ICdfYmxhbmsnLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdjcmVhdGUtd2l0aGRyYXdhbCcsXHJcblx0XHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnY3JlYXRlLXdpdGhkcmF3YWwnfSxcclxuXHRcdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdFJvd0FjdGlvbiA9PT0gJ2NyZWF0ZS13aXRoZHJhd2FsJ1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIEFkZCBUcmFja2luZyBDb2RlXHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XHJcblx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVFhUX1BBUkNFTF9UUkFDS0lOR19TRU5EQlVUVE9OX1RJVExFJywgJ3BhcmNlbF9zZXJ2aWNlcycpLFxyXG5cdFx0XHRcdFx0Y2xhc3M6ICdhZGQtdHJhY2tpbmctbnVtYmVyJyxcclxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdhZGQtdHJhY2tpbmctbnVtYmVyJ30sXHJcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdhZGQtdHJhY2tpbmctbnVtYmVyJyxcclxuXHRcdFx0XHRcdGNhbGxiYWNrOiBlID0+IGUucHJldmVudERlZmF1bHQoKVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdCR0aGlzLmRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMoJ2Vuc3VyZScsICdyb3cnKTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdFx0JCh3aW5kb3cpLm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgKCkgPT4ge1xyXG5cdFx0XHRcdCR0aGlzLm9uKCdkcmF3LmR0JywgX2NyZWF0ZVJvd0FjdGlvbnMpO1xyXG5cdFx0XHRcdF9jcmVhdGVSb3dBY3Rpb25zKCk7XHJcblx0XHRcdFx0X2NyZWF0ZUJ1bGtBY3Rpb25zKCk7XHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0ZG9uZSgpO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIG1vZHVsZTtcclxuXHRcdFxyXG5cdH0pOyAiXX0=
