/* --------------------------------------------------------------
 orders_overview_columns.js 2016-08-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.orders_overview_columns = jse.libs.orders_overview_columns || {};

/**
 * ## Orders Table Column Definitions
 *
 * This module defines the column definition of the order overview table. They can be overridden by other
 * scripts by modifying the array with new columns, or by replacing the property values of the contained
 * fields.
 *
 * @module Admin/Libs/orders_overview_columns
 * @exports jse.libs.orders_overview_columns
 */
(function(exports) {
	
	'use strict';
	
	exports.checkbox = exports.checkbox || {
			data: null,
			minWidth: '50px',
			widthFactor: 0.8,
			orderable: false,
			searchable: false, 
			defaultContent: '<input type="checkbox" />'
		};
	
	exports.number = exports.number || {
			data: 'number',
			minWidth: '75px',
			widthFactor: 1,
			className: 'numeric',
			render: (data, type, full, meta) => {
				let linkElement = '';
				
				if (full.DT_RowData.comment !== '') {
					linkElement += `
									<i class="fa fa-comment-o tooltip-order-comment tooltip-trigger"
										aria-hidden="true" title="${full.DT_RowData.comment}"></i>&nbsp;
								`;
				}
				
				linkElement +=`
								<a class="tooltip-order-items"
										href="orders.php?oID=${full.DT_RowData.id}&action=edit">
									${full.DT_RowData.id}
								</a>
							`;
				
				return linkElement;
			}
		};
	
	exports.customer = exports.customer || {
			data: 'customer',
			minWidth: '190px',
			widthFactor: 1.5,
			render: (data, type, full, meta) => {
				let linkElement = full.DT_RowData.customerId
					? `<a class="tooltip-customer-addresses" 
							href="customers.php?cID=${full.DT_RowData.customerId}&action=edit">${data}</a>`
					: `<span class="tooltip-customer-addresses">${data}</span>`;
				
				if (full.DT_RowData.customerMemos.length > 0) {
					linkElement +=
						` <i class="fa fa-sticky-note-o tooltip-customer-memos tooltip-trigger" 
                                aria-hidden="true"></i>`;
				}
				
				return linkElement;
			}
		};
	
	exports.group = exports.group || {
			data: 'group',
			minWidth: '85px',
			widthFactor: 1.2
		};
	
	exports.sum = exports.sum || {
			data: 'sum',
			minWidth: '90px',
			widthFactor: 1,
			className: 'numeric',
			render: function(data, type, full, meta) {
				return `<span class="tooltip-order-sum-block">${data}</span>`;
			}
		};
	
	exports.paymentMethod = exports.paymentMethod || {
			data: 'paymentMethod',
			minWidth: '110px',
			widthFactor: 2,
			render: function(data, type, full, meta) {
				return `<span title="${full.DT_RowData.paymentMethod}">${data}</span>`;
			}
		};
	
	exports.shippingMethod = exports.shippingMethod || {
			data: 'shippingMethod',
			minWidth: '110px',
			widthFactor: 2,
			className: 'shipping-method',
			render: function(data, type, full, meta) {
				const icon = full.DT_RowData.trackingLinks.length
					? ' <i class="fa fa-truck fa-lg tooltip-tracking-links tooltip-trigger"</i>'
					: '';
				return `<span title="${full.DT_RowData.shippingMethod}">${data}</span>${icon}`;
			},
			createdCell: function(td, cellData, rowData, row, col) {
				$(td)
					.data('orderId', rowData.DT_RowData.id)
					.attr('data-toggle', 'modal')
					.attr('data-target', '.add-tracking-number.modal');
			}
		};
	
	exports.countryIsoCode = exports.countryIsoCode || {
			data: 'countryIsoCode',
			minWidth: '75px',
			widthFactor: 1.4,
			render: function(data, type, full, meta) {
				let html = ''; 
				
				if (data) {
					const imageSrc = `${jse.core.config.get('appUrl')}/images/icons/flags/${data.toLowerCase()}.png`; 
					html = `<img src="${imageSrc}" />&nbsp;`; 	
				}
				
				const title = jse.core.lang.translate('SHIPPING_ORIGIN_COUNTRY_TITLE', 'configuration') 
					+ ': ' + full.DT_RowData.country;
				
				html += `<span title="${title}">${data}</span>`;
				
				return html;
			}
		};
	
	exports.date = exports.date || {
			data: 'date',
			minWidth: '100px',
			widthFactor: 1.6,
			render: function(data, type, full, meta) {
				return moment(data).format('DD.MM.YY - HH:mm');
			}
		};
	
	exports.status = exports.status || {
			data: 'status',
			minWidth: '120px',
			widthFactor: 2,
			render: function(data, type, full, meta) {
				return `
					<span data-toggle="modal" data-target=".status.modal"
							class="order-status tooltip-order-status-history label label-${full.DT_RowData.statusId}">
						${data}
					</span>
				`;
			}
		};
	
	exports.totalWeight = exports.totalWeight || {
			data: 'totalWeight',
			minWidth: '50px',
			widthFactor: 0.6,
			className: 'numeric'
		};
	
	exports.actions = exports.actions || {
			data: null,
			minWidth: '350px',
			widthFactor: 4.6,
			className: 'actions',
			orderable: false,
			searchable: false,
			render: function(data, type, full, meta) {
				let withdrawalIdsHtml = '';
				const withdrawalIdHeading = jse.core.lang.translate('TABLE_HEADING_WITHDRAWAL_ID', 'orders'); 
				
				full.DT_RowData.withdrawalIds.forEach((withdrawalId) => {
					withdrawalIdsHtml += `
						<a href="withdrawals.php?id=${withdrawalId}&action=edit" 
								title="${withdrawalIdHeading} ${withdrawalId}">
							<img src="html/assets/images/legacy/icons/withdrawal-on.png" 
								class="tooltip-withdrawal tooltip-trigger meta-icon" 
								data-withdrawal-id="${withdrawalId}" />
						</a>
					`;
				});
				
				let mailStatusHtml = !full.DT_RowData.mailStatus
					? `<i class="fa fa-envelope-o meta-icon tooltip-confirmation-not-sent email-order tooltip-trigger"
						title="${jse.core.lang.translate('TEXT_CONFIRMATION_NOT_SENT', 'orders')}"></i>` : '';
				
				return `
					<div class="pull-left">
						${withdrawalIdsHtml}
						${mailStatusHtml}
					</div>
					
					<div class="pull-right visible-on-hover">
						<i class="fa fa-eye edit"></i>
						<i class="fa fa-trash-o delete"></i>
						
						<div class="btn-group dropdown">
							<button type="button"
									class="btn btn-default"></button>
							<button type="button"
									class="btn btn-default dropdown-toggle"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right"></ul>
						</div>
					</div>
				`;
			}
		};
})(jse.libs.orders_overview_columns); 