'use strict';

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
(function (exports) {

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
		render: function render(data, type, full, meta) {
			var linkElement = '';

			if (full.DT_RowData.comment !== '') {
				linkElement += '\n\t\t\t\t\t\t\t\t\t<i class="fa fa-comment-o tooltip-order-comment tooltip-trigger"\n\t\t\t\t\t\t\t\t\t\taria-hidden="true" title="' + full.DT_RowData.comment + '"></i>&nbsp;\n\t\t\t\t\t\t\t\t';
			}

			linkElement += '\n\t\t\t\t\t\t\t\t<a class="tooltip-order-items"\n\t\t\t\t\t\t\t\t\t\thref="orders.php?oID=' + full.DT_RowData.id + '&action=edit">\n\t\t\t\t\t\t\t\t\t' + full.DT_RowData.id + '\n\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t';

			return linkElement;
		}
	};

	exports.customer = exports.customer || {
		data: 'customer',
		minWidth: '190px',
		widthFactor: 1.5,
		render: function render(data, type, full, meta) {
			var linkElement = full.DT_RowData.customerId ? '<a class="tooltip-customer-addresses" \n\t\t\t\t\t\t\thref="customers.php?cID=' + full.DT_RowData.customerId + '&action=edit">' + data + '</a>' : '<span class="tooltip-customer-addresses">' + data + '</span>';

			if (full.DT_RowData.customerMemos.length > 0) {
				linkElement += ' <i class="fa fa-sticky-note-o tooltip-customer-memos tooltip-trigger" \n                                aria-hidden="true"></i>';
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
		render: function render(data, type, full, meta) {
			return '<span class="tooltip-order-sum-block">' + data + '</span>';
		}
	};

	exports.paymentMethod = exports.paymentMethod || {
		data: 'paymentMethod',
		minWidth: '110px',
		widthFactor: 2,
		render: function render(data, type, full, meta) {
			return '<span title="' + full.DT_RowData.paymentMethod + '">' + data + '</span>';
		}
	};

	exports.shippingMethod = exports.shippingMethod || {
		data: 'shippingMethod',
		minWidth: '110px',
		widthFactor: 2,
		className: 'shipping-method',
		render: function render(data, type, full, meta) {
			var icon = full.DT_RowData.trackingLinks.length ? ' <i class="fa fa-truck fa-lg tooltip-tracking-links tooltip-trigger"</i>' : '';
			return '<span title="' + full.DT_RowData.shippingMethod + '">' + data + '</span>' + icon;
		},
		createdCell: function createdCell(td, cellData, rowData, row, col) {
			$(td).data('orderId', rowData.DT_RowData.id).attr('data-toggle', 'modal').attr('data-target', '.add-tracking-number.modal');
		}
	};

	exports.countryIsoCode = exports.countryIsoCode || {
		data: 'countryIsoCode',
		minWidth: '75px',
		widthFactor: 1.4,
		render: function render(data, type, full, meta) {
			var html = '';

			if (data) {
				var imageSrc = jse.core.config.get('appUrl') + '/images/icons/flags/' + data.toLowerCase() + '.png';
				html = '<img src="' + imageSrc + '" />&nbsp;';
			}

			var title = jse.core.lang.translate('SHIPPING_ORIGIN_COUNTRY_TITLE', 'configuration') + ': ' + full.DT_RowData.country;

			html += '<span title="' + title + '">' + data + '</span>';

			return html;
		}
	};

	exports.date = exports.date || {
		data: 'date',
		minWidth: '100px',
		widthFactor: 1.6,
		render: function render(data, type, full, meta) {
			return moment(data).format('DD.MM.YY - HH:mm');
		}
	};

	exports.status = exports.status || {
		data: 'status',
		minWidth: '120px',
		widthFactor: 2,
		render: function render(data, type, full, meta) {
			return '\n\t\t\t\t\t<span data-toggle="modal" data-target=".status.modal"\n\t\t\t\t\t\t\tclass="order-status tooltip-order-status-history label label-' + full.DT_RowData.statusId + '">\n\t\t\t\t\t\t' + data + '\n\t\t\t\t\t</span>\n\t\t\t\t';
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
		render: function render(data, type, full, meta) {
			var withdrawalIdsHtml = '';
			var withdrawalIdHeading = jse.core.lang.translate('TABLE_HEADING_WITHDRAWAL_ID', 'orders');

			full.DT_RowData.withdrawalIds.forEach(function (withdrawalId) {
				withdrawalIdsHtml += '\n\t\t\t\t\t\t<a href="withdrawals.php?id=' + withdrawalId + '&action=edit" \n\t\t\t\t\t\t\t\ttitle="' + withdrawalIdHeading + ' ' + withdrawalId + '">\n\t\t\t\t\t\t\t<img src="html/assets/images/legacy/icons/withdrawal-on.png" \n\t\t\t\t\t\t\t\tclass="tooltip-withdrawal tooltip-trigger meta-icon" \n\t\t\t\t\t\t\t\tdata-withdrawal-id="' + withdrawalId + '" />\n\t\t\t\t\t\t</a>\n\t\t\t\t\t';
			});

			var mailStatusHtml = !full.DT_RowData.mailStatus ? '<i class="fa fa-envelope-o meta-icon tooltip-confirmation-not-sent email-order tooltip-trigger"\n\t\t\t\t\t\ttitle="' + jse.core.lang.translate('TEXT_CONFIRMATION_NOT_SENT', 'orders') + '"></i>' : '';

			return '\n\t\t\t\t\t<div class="pull-left">\n\t\t\t\t\t\t' + withdrawalIdsHtml + '\n\t\t\t\t\t\t' + mailStatusHtml + '\n\t\t\t\t\t</div>\n\t\t\t\t\t\n\t\t\t\t\t<div class="pull-right visible-on-hover">\n\t\t\t\t\t\t<i class="fa fa-eye edit"></i>\n\t\t\t\t\t\t<i class="fa fa-trash-o delete"></i>\n\t\t\t\t\t\t\n\t\t\t\t\t\t<div class="btn-group dropdown">\n\t\t\t\t\t\t\t<button type="button"\n\t\t\t\t\t\t\t\t\tclass="btn btn-default"></button>\n\t\t\t\t\t\t\t<button type="button"\n\t\t\t\t\t\t\t\t\tclass="btn btn-default dropdown-toggle"\n\t\t\t\t\t\t\t\t\tdata-toggle="dropdown"\n\t\t\t\t\t\t\t\t\taria-haspopup="true"\n\t\t\t\t\t\t\t\t\taria-expanded="false">\n\t\t\t\t\t\t\t\t<span class="caret"></span>\n\t\t\t\t\t\t\t\t<span class="sr-only">Toggle Dropdown</span>\n\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t\t<ul class="dropdown-menu dropdown-menu-right"></ul>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t';
		}
	};
})(jse.libs.orders_overview_columns);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVyc19vdmVydmlld19jb2x1bW5zLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJvcmRlcnNfb3ZlcnZpZXdfY29sdW1ucyIsImV4cG9ydHMiLCJjaGVja2JveCIsImRhdGEiLCJtaW5XaWR0aCIsIndpZHRoRmFjdG9yIiwib3JkZXJhYmxlIiwic2VhcmNoYWJsZSIsImRlZmF1bHRDb250ZW50IiwibnVtYmVyIiwiY2xhc3NOYW1lIiwicmVuZGVyIiwidHlwZSIsImZ1bGwiLCJtZXRhIiwibGlua0VsZW1lbnQiLCJEVF9Sb3dEYXRhIiwiY29tbWVudCIsImlkIiwiY3VzdG9tZXIiLCJjdXN0b21lcklkIiwiY3VzdG9tZXJNZW1vcyIsImxlbmd0aCIsImdyb3VwIiwic3VtIiwicGF5bWVudE1ldGhvZCIsInNoaXBwaW5nTWV0aG9kIiwiaWNvbiIsInRyYWNraW5nTGlua3MiLCJjcmVhdGVkQ2VsbCIsInRkIiwiY2VsbERhdGEiLCJyb3dEYXRhIiwicm93IiwiY29sIiwiJCIsImF0dHIiLCJjb3VudHJ5SXNvQ29kZSIsImh0bWwiLCJpbWFnZVNyYyIsImNvcmUiLCJjb25maWciLCJnZXQiLCJ0b0xvd2VyQ2FzZSIsInRpdGxlIiwibGFuZyIsInRyYW5zbGF0ZSIsImNvdW50cnkiLCJkYXRlIiwibW9tZW50IiwiZm9ybWF0Iiwic3RhdHVzIiwic3RhdHVzSWQiLCJ0b3RhbFdlaWdodCIsImFjdGlvbnMiLCJ3aXRoZHJhd2FsSWRzSHRtbCIsIndpdGhkcmF3YWxJZEhlYWRpbmciLCJ3aXRoZHJhd2FsSWRzIiwiZm9yRWFjaCIsIndpdGhkcmF3YWxJZCIsIm1haWxTdGF0dXNIdG1sIiwibWFpbFN0YXR1cyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLHVCQUFULEdBQW1DRixJQUFJQyxJQUFKLENBQVNDLHVCQUFULElBQW9DLEVBQXZFOztBQUVBOzs7Ozs7Ozs7O0FBVUEsQ0FBQyxVQUFTQyxPQUFULEVB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QWhCLENBQThCQyxPQUE5QixDQUFzQyxVQUFDQyxZQUFELEVBQWtCO0FBQ3ZESix3RUFDK0JJLFlBRC9CLCtDQUVZSCxtQkFGWixTQUVtQ0csWUFGbkMsb01BS3lCQSxZQUx6QjtBQVFBLElBVEQ7O0FBV0EsT0FBSUMsaUJBQWlCLENBQUMvQyxLQUFLRyxVQUFMLENBQWdCNkMsVUFBakIsNEhBRVYvRCxJQUFJMEMsSUFBSixDQUFTSyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsNEJBQXhCLEVBQXNELFFBQXRELENBRlUsY0FFZ0UsRUFGckY7O0FBSUEsZ0VBRUlTLGlCQUZKLHNCQUdJSyxjQUhKO0FBeUJBO0FBbkRrQyxFQUFyQztBQXFEQSxDQS9NRCxFQStNRzlELElBQUlDLElBQUosQ0FBU0MsdUJBL01aIiwiZmlsZSI6Im9yZGVyc19vdmVydmlld19jb2x1bW5zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIG9yZGVyc19vdmVydmlld19jb2x1bW5zLmpzIDIwMTYtMDgtMThcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5qc2UubGlicy5vcmRlcnNfb3ZlcnZpZXdfY29sdW1ucyA9IGpzZS5saWJzLm9yZGVyc19vdmVydmlld19jb2x1bW5zIHx8IHt9O1xyXG5cclxuLyoqXHJcbiAqICMjIE9yZGVycyBUYWJsZSBDb2x1bW4gRGVmaW5pdGlvbnNcclxuICpcclxuICogVGhpcyBtb2R1bGUgZGVmaW5lcyB0aGUgY29sdW1uIGRlZmluaXRpb24gb2YgdGhlIG9yZGVyIG92ZXJ2aWV3IHRhYmxlLiBUaGV5IGNhbiBiZSBvdmVycmlkZGVuIGJ5IG90aGVyXHJcbiAqIHNjcmlwdHMgYnkgbW9kaWZ5aW5nIHRoZSBhcnJheSB3aXRoIG5ldyBjb2x1bW5zLCBvciBieSByZXBsYWNpbmcgdGhlIHByb3BlcnR5IHZhbHVlcyBvZiB0aGUgY29udGFpbmVkXHJcbiAqIGZpZWxkcy5cclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9MaWJzL29yZGVyc19vdmVydmlld19jb2x1bW5zXHJcbiAqIEBleHBvcnRzIGpzZS5saWJzLm9yZGVyc19vdmVydmlld19jb2x1bW5zXHJcbiAqL1xyXG4oZnVuY3Rpb24oZXhwb3J0cykge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHRleHBvcnRzLmNoZWNrYm94ID0gZXhwb3J0cy5jaGVja2JveCB8fCB7XHJcblx0XHRcdGRhdGE6IG51bGwsXHJcblx0XHRcdG1pbldpZHRoOiAnNTBweCcsXHJcblx0XHRcdHdpZHRoRmFjdG9yOiAwLjgsXHJcblx0XHRcdG9yZGVyYWJsZTogZmFsc2UsXHJcblx0XHRcdHNlYXJjaGFibGU6IGZhbHNlLCBcclxuXHRcdFx0ZGVmYXVsdENvbnRlbnQ6ICc8aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgLz4nXHJcblx0XHR9O1xyXG5cdFxyXG5cdGV4cG9ydHMubnVtYmVyID0gZXhwb3J0cy5udW1iZXIgfHwge1xyXG5cdFx0XHRkYXRhOiAnbnVtYmVyJyxcclxuXHRcdFx0bWluV2lkdGg6ICc3NXB4JyxcclxuXHRcdFx0d2lkdGhGYWN0b3I6IDEsXHJcblx0XHRcdGNsYXNzTmFtZTogJ251bWVyaWMnLFxyXG5cdFx0XHRyZW5kZXI6IChkYXRhLCB0eXBlLCBmdWxsLCBtZXRhKSA9PiB7XHJcblx0XHRcdFx0bGV0IGxpbmtFbGVtZW50ID0gJyc7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0aWYgKGZ1bGwuRFRfUm93RGF0YS5jb21tZW50ICE9PSAnJykge1xyXG5cdFx0XHRcdFx0bGlua0VsZW1lbnQgKz0gYFxyXG5cdFx0XHRcdFx0XHRcdFx0XHQ8aSBjbGFzcz1cImZhIGZhLWNvbW1lbnQtbyB0b29sdGlwLW9yZGVyLWNvbW1lbnQgdG9vbHRpcC10cmlnZ2VyXCJcclxuXHRcdFx0XHRcdFx0XHRcdFx0XHRhcmlhLWhpZGRlbj1cInRydWVcIiB0aXRsZT1cIiR7ZnVsbC5EVF9Sb3dEYXRhLmNvbW1lbnR9XCI+PC9pPiZuYnNwO1xyXG5cdFx0XHRcdFx0XHRcdFx0YDtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0bGlua0VsZW1lbnQgKz1gXHJcblx0XHRcdFx0XHRcdFx0XHQ8YSBjbGFzcz1cInRvb2x0aXAtb3JkZXItaXRlbXNcIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGhyZWY9XCJvcmRlcnMucGhwP29JRD0ke2Z1bGwuRFRfUm93RGF0YS5pZH0mYWN0aW9uPWVkaXRcIj5cclxuXHRcdFx0XHRcdFx0XHRcdFx0JHtmdWxsLkRUX1Jvd0RhdGEuaWR9XHJcblx0XHRcdFx0XHRcdFx0XHQ8L2E+XHJcblx0XHRcdFx0XHRcdFx0YDtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gbGlua0VsZW1lbnQ7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0XHJcblx0ZXhwb3J0cy5jdXN0b21lciA9IGV4cG9ydHMuY3VzdG9tZXIgfHwge1xyXG5cdFx0XHRkYXRhOiAnY3VzdG9tZXInLFxyXG5cdFx0XHRtaW5XaWR0aDogJzE5MHB4JyxcclxuXHRcdFx0d2lkdGhGYWN0b3I6IDEuNSxcclxuXHRcdFx0cmVuZGVyOiAoZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkgPT4ge1xyXG5cdFx0XHRcdGxldCBsaW5rRWxlbWVudCA9IGZ1bGwuRFRfUm93RGF0YS5jdXN0b21lcklkXHJcblx0XHRcdFx0XHQ/IGA8YSBjbGFzcz1cInRvb2x0aXAtY3VzdG9tZXItYWRkcmVzc2VzXCIgXHJcblx0XHRcdFx0XHRcdFx0aHJlZj1cImN1c3RvbWVycy5waHA/Y0lEPSR7ZnVsbC5EVF9Sb3dEYXRhLmN1c3RvbWVySWR9JmFjdGlvbj1lZGl0XCI+JHtkYXRhfTwvYT5gXHJcblx0XHRcdFx0XHQ6IGA8c3BhbiBjbGFzcz1cInRvb2x0aXAtY3VzdG9tZXItYWRkcmVzc2VzXCI+JHtkYXRhfTwvc3Bhbj5gO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmIChmdWxsLkRUX1Jvd0RhdGEuY3VzdG9tZXJNZW1vcy5sZW5ndGggPiAwKSB7XHJcblx0XHRcdFx0XHRsaW5rRWxlbWVudCArPVxyXG5cdFx0XHRcdFx0XHRgIDxpIGNsYXNzPVwiZmEgZmEtc3RpY2t5LW5vdGUtbyB0b29sdGlwLWN1c3RvbWVyLW1lbW9zIHRvb2x0aXAtdHJpZ2dlclwiIFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPjwvaT5gO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gbGlua0VsZW1lbnQ7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0XHJcblx0ZXhwb3J0cy5ncm91cCA9IGV4cG9ydHMuZ3JvdXAgfHwge1xyXG5cdFx0XHRkYXRhOiAnZ3JvdXAnLFxyXG5cdFx0XHRtaW5XaWR0aDogJzg1cHgnLFxyXG5cdFx0XHR3aWR0aEZhY3RvcjogMS4yXHJcblx0XHR9O1xyXG5cdFxyXG5cdGV4cG9ydHMuc3VtID0gZXhwb3J0cy5zdW0gfHwge1xyXG5cdFx0XHRkYXRhOiAnc3VtJyxcclxuXHRcdFx0bWluV2lkdGg6ICc5MHB4JyxcclxuXHRcdFx0d2lkdGhGYWN0b3I6IDEsXHJcblx0XHRcdGNsYXNzTmFtZTogJ251bWVyaWMnLFxyXG5cdFx0XHRyZW5kZXI6IGZ1bmN0aW9uKGRhdGEsIHR5cGUsIGZ1bGwsIG1ldGEpIHtcclxuXHRcdFx0XHRyZXR1cm4gYDxzcGFuIGNsYXNzPVwidG9vbHRpcC1vcmRlci1zdW0tYmxvY2tcIj4ke2RhdGF9PC9zcGFuPmA7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0XHJcblx0ZXhwb3J0cy5wYXltZW50TWV0aG9kID0gZXhwb3J0cy5wYXltZW50TWV0aG9kIHx8IHtcclxuXHRcdFx0ZGF0YTogJ3BheW1lbnRNZXRob2QnLFxyXG5cdFx0XHRtaW5XaWR0aDogJzExMHB4JyxcclxuXHRcdFx0d2lkdGhGYWN0b3I6IDIsXHJcblx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkge1xyXG5cdFx0XHRcdHJldHVybiBgPHNwYW4gdGl0bGU9XCIke2Z1bGwuRFRfUm93RGF0YS5wYXltZW50TWV0aG9kfVwiPiR7ZGF0YX08L3NwYW4+YDtcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcclxuXHRleHBvcnRzLnNoaXBwaW5nTWV0aG9kID0gZXhwb3J0cy5zaGlwcGluZ01ldGhvZCB8fCB7XHJcblx0XHRcdGRhdGE6ICdzaGlwcGluZ01ldGhvZCcsXHJcblx0XHRcdG1pbldpZHRoOiAnMTEwcHgnLFxyXG5cdFx0XHR3aWR0aEZhY3RvcjogMixcclxuXHRcdFx0Y2xhc3NOYW1lOiAnc2hpcHBpbmctbWV0aG9kJyxcclxuXHRcdFx0cmVuZGVyOiBmdW5jdGlvbihkYXRhLCB0eXBlLCBmdWxsLCBtZXRhKSB7XHJcblx0XHRcdFx0Y29uc3QgaWNvbiA9IGZ1bGwuRFRfUm93RGF0YS50cmFja2luZ0xpbmtzLmxlbmd0aFxyXG5cdFx0XHRcdFx0PyAnIDxpIGNsYXNzPVwiZmEgZmEtdHJ1Y2sgZmEtbGcgdG9vbHRpcC10cmFja2luZy1saW5rcyB0b29sdGlwLXRyaWdnZXJcIjwvaT4nXHJcblx0XHRcdFx0XHQ6ICcnO1xyXG5cdFx0XHRcdHJldHVybiBgPHNwYW4gdGl0bGU9XCIke2Z1bGwuRFRfUm93RGF0YS5zaGlwcGluZ01ldGhvZH1cIj4ke2RhdGF9PC9zcGFuPiR7aWNvbn1gO1xyXG5cdFx0XHR9LFxyXG5cdFx0XHRjcmVhdGVkQ2VsbDogZnVuY3Rpb24odGQsIGNlbGxEYXRhLCByb3dEYXRhLCByb3csIGNvbCkge1xyXG5cdFx0XHRcdCQodGQpXHJcblx0XHRcdFx0XHQuZGF0YSgnb3JkZXJJZCcsIHJvd0RhdGEuRFRfUm93RGF0YS5pZClcclxuXHRcdFx0XHRcdC5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpXHJcblx0XHRcdFx0XHQuYXR0cignZGF0YS10YXJnZXQnLCAnLmFkZC10cmFja2luZy1udW1iZXIubW9kYWwnKTtcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcclxuXHRleHBvcnRzLmNvdW50cnlJc29Db2RlID0gZXhwb3J0cy5jb3VudHJ5SXNvQ29kZSB8fCB7XHJcblx0XHRcdGRhdGE6ICdjb3VudHJ5SXNvQ29kZScsXHJcblx0XHRcdG1pbldpZHRoOiAnNzVweCcsXHJcblx0XHRcdHdpZHRoRmFjdG9yOiAxLjQsXHJcblx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkge1xyXG5cdFx0XHRcdGxldCBodG1sID0gJyc7IFxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmIChkYXRhKSB7XHJcblx0XHRcdFx0XHRjb25zdCBpbWFnZVNyYyA9IGAke2pzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpfS9pbWFnZXMvaWNvbnMvZmxhZ3MvJHtkYXRhLnRvTG93ZXJDYXNlKCl9LnBuZ2A7IFxyXG5cdFx0XHRcdFx0aHRtbCA9IGA8aW1nIHNyYz1cIiR7aW1hZ2VTcmN9XCIgLz4mbmJzcDtgOyBcdFxyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRcclxuXHRcdFx0XHRjb25zdCB0aXRsZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdTSElQUElOR19PUklHSU5fQ09VTlRSWV9USVRMRScsICdjb25maWd1cmF0aW9uJykgXHJcblx0XHRcdFx0XHQrICc6ICcgKyBmdWxsLkRUX1Jvd0RhdGEuY291bnRyeTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRodG1sICs9IGA8c3BhbiB0aXRsZT1cIiR7dGl0bGV9XCI+JHtkYXRhfTwvc3Bhbj5gO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHJldHVybiBodG1sO1xyXG5cdFx0XHR9XHJcblx0XHR9O1xyXG5cdFxyXG5cdGV4cG9ydHMuZGF0ZSA9IGV4cG9ydHMuZGF0ZSB8fCB7XHJcblx0XHRcdGRhdGE6ICdkYXRlJyxcclxuXHRcdFx0bWluV2lkdGg6ICcxMDBweCcsXHJcblx0XHRcdHdpZHRoRmFjdG9yOiAxLjYsXHJcblx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkge1xyXG5cdFx0XHRcdHJldHVybiBtb21lbnQoZGF0YSkuZm9ybWF0KCdERC5NTS5ZWSAtIEhIOm1tJyk7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0XHJcblx0ZXhwb3J0cy5zdGF0dXMgPSBleHBvcnRzLnN0YXR1cyB8fCB7XHJcblx0XHRcdGRhdGE6ICdzdGF0dXMnLFxyXG5cdFx0XHRtaW5XaWR0aDogJzEyMHB4JyxcclxuXHRcdFx0d2lkdGhGYWN0b3I6IDIsXHJcblx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkge1xyXG5cdFx0XHRcdHJldHVybiBgXHJcblx0XHRcdFx0XHQ8c3BhbiBkYXRhLXRvZ2dsZT1cIm1vZGFsXCIgZGF0YS10YXJnZXQ9XCIuc3RhdHVzLm1vZGFsXCJcclxuXHRcdFx0XHRcdFx0XHRjbGFzcz1cIm9yZGVyLXN0YXR1cyB0b29sdGlwLW9yZGVyLXN0YXR1cy1oaXN0b3J5IGxhYmVsIGxhYmVsLSR7ZnVsbC5EVF9Sb3dEYXRhLnN0YXR1c0lkfVwiPlxyXG5cdFx0XHRcdFx0XHQke2RhdGF9XHJcblx0XHRcdFx0XHQ8L3NwYW4+XHJcblx0XHRcdFx0YDtcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcclxuXHRleHBvcnRzLnRvdGFsV2VpZ2h0ID0gZXhwb3J0cy50b3RhbFdlaWdodCB8fCB7XHJcblx0XHRcdGRhdGE6ICd0b3RhbFdlaWdodCcsXHJcblx0XHRcdG1pbldpZHRoOiAnNTBweCcsXHJcblx0XHRcdHdpZHRoRmFjdG9yOiAwLjYsXHJcblx0XHRcdGNsYXNzTmFtZTogJ251bWVyaWMnXHJcblx0XHR9O1xyXG5cdFxyXG5cdGV4cG9ydHMuYWN0aW9ucyA9IGV4cG9ydHMuYWN0aW9ucyB8fCB7XHJcblx0XHRcdGRhdGE6IG51bGwsXHJcblx0XHRcdG1pbldpZHRoOiAnMzUwcHgnLFxyXG5cdFx0XHR3aWR0aEZhY3RvcjogNC42LFxyXG5cdFx0XHRjbGFzc05hbWU6ICdhY3Rpb25zJyxcclxuXHRcdFx0b3JkZXJhYmxlOiBmYWxzZSxcclxuXHRcdFx0c2VhcmNoYWJsZTogZmFsc2UsXHJcblx0XHRcdHJlbmRlcjogZnVuY3Rpb24oZGF0YSwgdHlwZSwgZnVsbCwgbWV0YSkge1xyXG5cdFx0XHRcdGxldCB3aXRoZHJhd2FsSWRzSHRtbCA9ICcnO1xyXG5cdFx0XHRcdGNvbnN0IHdpdGhkcmF3YWxJZEhlYWRpbmcgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVEFCTEVfSEVBRElOR19XSVRIRFJBV0FMX0lEJywgJ29yZGVycycpOyBcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRmdWxsLkRUX1Jvd0RhdGEud2l0aGRyYXdhbElkcy5mb3JFYWNoKCh3aXRoZHJhd2FsSWQpID0+IHtcclxuXHRcdFx0XHRcdHdpdGhkcmF3YWxJZHNIdG1sICs9IGBcclxuXHRcdFx0XHRcdFx0PGEgaHJlZj1cIndpdGhkcmF3YWxzLnBocD9pZD0ke3dpdGhkcmF3YWxJZH0mYWN0aW9uPWVkaXRcIiBcclxuXHRcdFx0XHRcdFx0XHRcdHRpdGxlPVwiJHt3aXRoZHJhd2FsSWRIZWFkaW5nfSAke3dpdGhkcmF3YWxJZH1cIj5cclxuXHRcdFx0XHRcdFx0XHQ8aW1nIHNyYz1cImh0bWwvYXNzZXRzL2ltYWdlcy9sZWdhY3kvaWNvbnMvd2l0aGRyYXdhbC1vbi5wbmdcIiBcclxuXHRcdFx0XHRcdFx0XHRcdGNsYXNzPVwidG9vbHRpcC13aXRoZHJhd2FsIHRvb2x0aXAtdHJpZ2dlciBtZXRhLWljb25cIiBcclxuXHRcdFx0XHRcdFx0XHRcdGRhdGEtd2l0aGRyYXdhbC1pZD1cIiR7d2l0aGRyYXdhbElkfVwiIC8+XHJcblx0XHRcdFx0XHRcdDwvYT5cclxuXHRcdFx0XHRcdGA7XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0bGV0IG1haWxTdGF0dXNIdG1sID0gIWZ1bGwuRFRfUm93RGF0YS5tYWlsU3RhdHVzXHJcblx0XHRcdFx0XHQ/IGA8aSBjbGFzcz1cImZhIGZhLWVudmVsb3BlLW8gbWV0YS1pY29uIHRvb2x0aXAtY29uZmlybWF0aW9uLW5vdC1zZW50IGVtYWlsLW9yZGVyIHRvb2x0aXAtdHJpZ2dlclwiXHJcblx0XHRcdFx0XHRcdHRpdGxlPVwiJHtqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVEVYVF9DT05GSVJNQVRJT05fTk9UX1NFTlQnLCAnb3JkZXJzJyl9XCI+PC9pPmAgOiAnJztcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gYFxyXG5cdFx0XHRcdFx0PGRpdiBjbGFzcz1cInB1bGwtbGVmdFwiPlxyXG5cdFx0XHRcdFx0XHQke3dpdGhkcmF3YWxJZHNIdG1sfVxyXG5cdFx0XHRcdFx0XHQke21haWxTdGF0dXNIdG1sfVxyXG5cdFx0XHRcdFx0PC9kaXY+XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJwdWxsLXJpZ2h0IHZpc2libGUtb24taG92ZXJcIj5cclxuXHRcdFx0XHRcdFx0PGkgY2xhc3M9XCJmYSBmYS1leWUgZWRpdFwiPjwvaT5cclxuXHRcdFx0XHRcdFx0PGkgY2xhc3M9XCJmYSBmYS10cmFzaC1vIGRlbGV0ZVwiPjwvaT5cclxuXHRcdFx0XHRcdFx0XHJcblx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJidG4tZ3JvdXAgZHJvcGRvd25cIj5cclxuXHRcdFx0XHRcdFx0XHQ8YnV0dG9uIHR5cGU9XCJidXR0b25cIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRjbGFzcz1cImJ0biBidG4tZGVmYXVsdFwiPjwvYnV0dG9uPlxyXG5cdFx0XHRcdFx0XHRcdDxidXR0b24gdHlwZT1cImJ1dHRvblwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdGNsYXNzPVwiYnRuIGJ0bi1kZWZhdWx0IGRyb3Bkb3duLXRvZ2dsZVwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdGRhdGEtdG9nZ2xlPVwiZHJvcGRvd25cIlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRhcmlhLWhhc3BvcHVwPVwidHJ1ZVwiXHJcblx0XHRcdFx0XHRcdFx0XHRcdGFyaWEtZXhwYW5kZWQ9XCJmYWxzZVwiPlxyXG5cdFx0XHRcdFx0XHRcdFx0PHNwYW4gY2xhc3M9XCJjYXJldFwiPjwvc3Bhbj5cclxuXHRcdFx0XHRcdFx0XHRcdDxzcGFuIGNsYXNzPVwic3Itb25seVwiPlRvZ2dsZSBEcm9wZG93bjwvc3Bhbj5cclxuXHRcdFx0XHRcdFx0XHQ8L2J1dHRvbj5cclxuXHRcdFx0XHRcdFx0XHQ8dWwgY2xhc3M9XCJkcm9wZG93bi1tZW51IGRyb3Bkb3duLW1lbnUtcmlnaHRcIj48L3VsPlxyXG5cdFx0XHRcdFx0XHQ8L2Rpdj5cclxuXHRcdFx0XHRcdDwvZGl2PlxyXG5cdFx0XHRcdGA7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcbn0pKGpzZS5saWJzLm9yZGVyc19vdmVydmlld19jb2x1bW5zKTsgIl19
