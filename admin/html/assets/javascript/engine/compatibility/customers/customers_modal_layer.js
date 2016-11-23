'use strict';

/* --------------------------------------------------------------
 customers_modal_layer.js 2016-03-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Customers Modal Layer Module
 *
 * This module will open a modal layer for
 * customers actions like deleting the article.
 *
 * @module Compatibility/customers_modal_layer
 */
gx.compatibility.module('customers_modal_layer', [],

/**  @lends module:Compatibility/customers_modal_layer */

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
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#modal_layer_container'),


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
	module = {},


	/**
  * Reference to the actual file
  *
  * @var {string}
  */
	srcPath = window.location.origin + window.location.pathname,


	/**
  * Query parameter string
  *
  * @type {string}
  */
	queryString = '?' + window.location.search.replace(/\?/, '').replace(/cID=[\d]+/g, '').replace(/action=[\w]+/g, '').replace(/pageToken=[\w]+/g, '').concat('&').replace(/&[&]+/g, '&').replace(/^&/g, '');

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Prepares buttons for the modal
  * @param {object | jQuery} $that
  * @returns {Array}
  * @private
  */
	var _getModalButtons = function _getModalButtons($that) {
		var buttons = [];

		var submitBtn, abortBtn;

		switch (options.action) {
			case 'delete':
				submitBtn = $that.find('input:first');
				abortBtn = $that.find('a.btn');

				$(submitBtn).hide();
				$(abortBtn).hide();

				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
						abortBtn.trigger('click');
					}
				}, {
					'text': jse.core.lang.translate('delete', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						var obj = {
							pageToken: $('input[name="page_token"]:first').attr('value'),
							cID: window.location.href.match(/cID=\d+/)[0]
						};

						obj.url = [srcPath, queryString, 'action=deleteconfirm', '&' + obj.cID].join('');

						var $form = $('<form name="customers" method="post" action=' + obj.url + '></form>');
						$form.append('<input type="hidden" name="page_token" value=' + obj.pageToken + '>');
						$form.append('<input type="hidden" name="deleteconfirm" value="DeleteConfirm">');
						$form.appendTo('body');
						$form.submit();
					}
				});
				break;
			case 'editstatus':
				submitBtn = $that.find('input:eq(1)');
				abortBtn = $that.find('a.btn');

				$(submitBtn).hide();
				$(abortBtn).hide();

				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
						window.open(abortBtn.attr('href'), '_self');
					}
				}, {
					'text': jse.core.lang.translate('update', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						var obj = {
							pageToken: $('input[name="page_token"]:first').attr('value'),
							cID: window.location.href.match(/cID=\d+/)[0],
							status: $that.find('select').val()
						};

						obj.url = [srcPath, queryString, 'action=statusconfirm', '&' + obj.cID].join('');

						var $form = $('<form name="customers" method="post" action=' + obj.url + '></form>');
						$form.append('<input type="hidden" name="page_token" value=' + obj.pageToken + '>');
						$form.append('<input type="hidden" name="status" value=' + obj.status + '>');
						$form.append('<input type="hidden" name="statusconfirm" value="Update">');
						$form.appendTo('body');
						$form.submit();
					}
				});
				break;
			case 'iplog':
				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
					}
				});
				break;
			case 'new_memo':
				console.log(submitBtn);
				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
					}
				});
				buttons.push({
					'text': jse.core.lang.translate('send', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click(event) {
						//event.preventDefault();
						//gm_cancel('gm_send_order.php', '&type=cancel', 'CANCEL');
						$that.submit();
					}
				});
				break;
		}

		return buttons;
	};

	/**
  * Creates dialog for single removal
  * @private
  */
	var _openDeleteDialog = function _openDeleteDialog() {
		$this.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_CUSTOMER', 'admin_customers'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($this),
			'width': 420,
			'closeOnEscape': false,
			'open': function open() {
				$('.ui-dialog-titlebar-close').hide();
			}
		});
	};

	/**
  * Creates dialog for single status change
  * @private
  */
	var _openEditStatusDialog = function _openEditStatusDialog() {
		$this.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_STATUS_CUSTOMER', 'admin_customers'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($this),
			'width': 420,
			'closeOnEscape': false,
			'open': function open() {
				// Make Some Fixes
				$('.ui-dialog-titlebar-close').hide();
				$(this).find('select[name="status"]').css({
					width: '100%',
					height: '35px',
					fontSize: '12px'
				});
			}
		});
	};

	/**
  * Creates dialog for single IP log
  * @private
  */
	var _openIpLogDialog = function _openIpLogDialog() {
		$this = $('<div></div>');

		$('[data-iplog]').each(function () {
			$this.append(this);
			$this.append('<br><br>');
		});

		$this.appendTo('body');
		$this.dialog({
			'title': 'IP-Log',
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($this),
			'width': 420,
			'closeOnEscape': false
		});
	};

	var _openNewMemoDialog = function _openNewMemoDialog(event) {
		var $form = $('#customer_memo_form');

		event.preventDefault();

		$form.dialog({
			'title': jse.core.lang.translate('TEXT_NEW_MEMO', 'admin_customers'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 580
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		switch (options.action) {
			case 'delete':
				_openDeleteDialog();
				break;
			case 'editstatus':
				_openEditStatusDialog();
				break;
			case 'iplog':
				_openIpLogDialog();
				break;
			case 'new_memo':
				$this.on('click', _openNewMemoDialog);
				break;
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImN1c3RvbWVycy9jdXN0b21lcnNfbW9kYWxfbGF5ZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRtb2RhbCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsInNyY1BhdGgiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsIm9yaWdpbiIsInBhdGhuYW1lIiwicXVlcnlTdHJpbmciLCJzZWFyY2giLCJyZXBsYWNlIiwiY29uY2F0IiwiX2dldE1vZGFsQnV0dG9ucyIsIiR0aGF0IiwiYnV0dG9ucyIsInN1Ym1pdEJ0biIsImFib3J0QnRuIiwiYWN0aW9uIiwiZmluZCIsImhpZGUiLCJwdXNoIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJkaWFsb2ciLCJ0cmlnZ2VyIiwib2JqIiwicGFnZVRva2VuIiwiYXR0ciIsImNJRCIsImhyZWYiLCJtYXRjaCIsInVybCIsImpvaW4iLCIkZm9ybSIsImFwcGVuZCIsImFwcGVuZFRvIiwic3VibWl0Iiwib3BlbiIsInN0YXR1cyIsInZhbCIsImNvbnNvbGUiLCJsb2ciLCJldmVudCIsIl9vcGVuRGVsZXRlRGlhbG9nIiwiX29wZW5FZGl0U3RhdHVzRGlhbG9nIiwiY3NzIiwid2lkdGgiLCJoZWlnaHQiLCJmb250U2l6ZSIsIl9vcGVuSXBMb2dEaWFsb2ciLCJlYWNoIiwiX29wZW5OZXdNZW1vRGlhbG9nIiwicHJldmVudERlZmF1bHQiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsdUJBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxVQUFT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QUFxQixDQUFTVCxLQUFULEVBQWdCO0FBQ3hDLE1BQUlULFFBQVFyQyxFQUFFLHFCQUFGLENBQVo7O0FBRUE4QyxRQUFNVSxjQUFOOztBQUVBbkIsUUFBTVYsTUFBTixDQUFhO0FBQ1osWUFBU0osSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsZUFBeEIsRUFBeUMsaUJBQXpDLENBREc7QUFFWixZQUFTLElBRkc7QUFHWixrQkFBZSxjQUhIO0FBSVosY0FBV1osaUJBQWlCdUIsS0FBakIsQ0FKQztBQUtaLFlBQVM7QUFMRyxHQUFiO0FBT0EsRUFaRDs7QUFjQTtBQUNBO0FBQ0E7O0FBRUF4QyxRQUFPNEQsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUIsVUFBUXZELFFBQVFnQixNQUFoQjtBQUNDLFFBQUssUUFBTDtBQUNDNEI7QUFDQTtBQUNELFFBQUssWUFBTDtBQUNDQztBQUNBO0FBQ0QsUUFBSyxPQUFMO0FBQ0NLO0FBQ0E7QUFDRCxRQUFLLFVBQUw7QUFDQ3RELFVBQU00RCxFQUFOLENBQVMsT0FBVCxFQUFrQkosa0JBQWxCO0FBQ0E7QUFaRjs7QUFlQUc7QUFDQSxFQWxCRDs7QUFvQkEsUUFBTzdELE1BQVA7QUFDQSxDQXZURiIsImZpbGUiOiJjdXN0b21lcnMvY3VzdG9tZXJzX21vZGFsX2xheWVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjdXN0b21lcnNfbW9kYWxfbGF5ZXIuanMgMjAxNi0wMy0xN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQ3VzdG9tZXJzIE1vZGFsIExheWVyIE1vZHVsZVxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgb3BlbiBhIG1vZGFsIGxheWVyIGZvclxuICogY3VzdG9tZXJzIGFjdGlvbnMgbGlrZSBkZWxldGluZyB0aGUgYXJ0aWNsZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY3VzdG9tZXJzX21vZGFsX2xheWVyXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnY3VzdG9tZXJzX21vZGFsX2xheWVyJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvY3VzdG9tZXJzX21vZGFsX2xheWVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZGFsIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JG1vZGFsID0gJCgnI21vZGFsX2xheWVyX2NvbnRhaW5lcicpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9LFxuXHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZWZlcmVuY2UgdG8gdGhlIGFjdHVhbCBmaWxlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7c3RyaW5nfVxuXHRcdFx0ICovXG5cdFx0XHRzcmNQYXRoID0gd2luZG93LmxvY2F0aW9uLm9yaWdpbiArIHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBRdWVyeSBwYXJhbWV0ZXIgc3RyaW5nXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge3N0cmluZ31cblx0XHRcdCAqL1xuXHRcdFx0cXVlcnlTdHJpbmcgPSAnPycgKyAod2luZG93LmxvY2F0aW9uLnNlYXJjaFxuXHRcdFx0XHRcdC5yZXBsYWNlKC9cXD8vLCAnJylcblx0XHRcdFx0XHQucmVwbGFjZSgvY0lEPVtcXGRdKy9nLCAnJylcblx0XHRcdFx0XHQucmVwbGFjZSgvYWN0aW9uPVtcXHddKy9nLCAnJylcblx0XHRcdFx0XHQucmVwbGFjZSgvcGFnZVRva2VuPVtcXHddKy9nLCAnJylcblx0XHRcdFx0XHQuY29uY2F0KCcmJylcblx0XHRcdFx0XHQucmVwbGFjZSgvJlsmXSsvZywgJyYnKVxuXHRcdFx0XHRcdC5yZXBsYWNlKC9eJi9nLCAnJykpO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgRlVOQ1RJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUHJlcGFyZXMgYnV0dG9ucyBmb3IgdGhlIG1vZGFsXG5cdFx0ICogQHBhcmFtIHtvYmplY3QgfCBqUXVlcnl9ICR0aGF0XG5cdFx0ICogQHJldHVybnMge0FycmF5fVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9nZXRNb2RhbEJ1dHRvbnMgPSBmdW5jdGlvbigkdGhhdCkge1xuXHRcdFx0dmFyIGJ1dHRvbnMgPSBbXTtcblx0XHRcdFxuXHRcdFx0dmFyIHN1Ym1pdEJ0biwgYWJvcnRCdG47XG5cdFx0XHRcblx0XHRcdHN3aXRjaCAob3B0aW9ucy5hY3Rpb24pIHtcblx0XHRcdFx0Y2FzZSAnZGVsZXRlJzpcblx0XHRcdFx0XHRzdWJtaXRCdG4gPSAkdGhhdC5maW5kKCdpbnB1dDpmaXJzdCcpO1xuXHRcdFx0XHRcdGFib3J0QnRuID0gJHRoYXQuZmluZCgnYS5idG4nKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKHN1Ym1pdEJ0bikuaGlkZSgpO1xuXHRcdFx0XHRcdCQoYWJvcnRCdG4pLmhpZGUoKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRidXR0b25zLnB1c2goXG5cdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0XHRcdGFib3J0QnRuLnRyaWdnZXIoJ2NsaWNrJyk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2RlbGV0ZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4gYnRuLXByaW1hcnknLFxuXHRcdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHR2YXIgb2JqID0ge1xuXHRcdFx0XHRcdFx0XHRcdFx0cGFnZVRva2VuOiAkKCdpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXTpmaXJzdCcpXG5cdFx0XHRcdFx0XHRcdFx0XHRcdC5hdHRyKCd2YWx1ZScpLFxuXHRcdFx0XHRcdFx0XHRcdFx0Y0lEOiB3aW5kb3cubG9jYXRpb24uaHJlZi5tYXRjaCgvY0lEPVxcZCsvKVswXVxuXHRcdFx0XHRcdFx0XHRcdH07XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0b2JqLnVybCA9IFtcblx0XHRcdFx0XHRcdFx0XHRcdHNyY1BhdGgsXG5cdFx0XHRcdFx0XHRcdFx0XHRxdWVyeVN0cmluZyxcblx0XHRcdFx0XHRcdFx0XHRcdCdhY3Rpb249ZGVsZXRlY29uZmlybScsXG5cdFx0XHRcdFx0XHRcdFx0XHQnJicgKyBvYmouY0lEXG5cdFx0XHRcdFx0XHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHR2YXIgJGZvcm0gPSAkKCc8Zm9ybSBuYW1lPVwiY3VzdG9tZXJzXCIgbWV0aG9kPVwicG9zdFwiIGFjdGlvbj0nICsgb2JqLnVybCArICc+PC9mb3JtPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwicGFnZV90b2tlblwiIHZhbHVlPScgKyBvYmoucGFnZVRva2VuICsgJz4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImRlbGV0ZWNvbmZpcm1cIiB2YWx1ZT1cIkRlbGV0ZUNvbmZpcm1cIj4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnZWRpdHN0YXR1cyc6XG5cdFx0XHRcdFx0c3VibWl0QnRuID0gJHRoYXQuZmluZCgnaW5wdXQ6ZXEoMSknKTtcblx0XHRcdFx0XHRhYm9ydEJ0biA9ICR0aGF0LmZpbmQoJ2EuYnRuJyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JChzdWJtaXRCdG4pLmhpZGUoKTtcblx0XHRcdFx0XHQkKGFib3J0QnRuKS5oaWRlKCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0YnV0dG9ucy5wdXNoKFxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHR3aW5kb3cub3BlbihhYm9ydEJ0bi5hdHRyKCdocmVmJyksICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCd1cGRhdGUnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIG9iaiA9IHtcblx0XHRcdFx0XHRcdFx0XHRcdHBhZ2VUb2tlbjogJCgnaW5wdXRbbmFtZT1cInBhZ2VfdG9rZW5cIl06Zmlyc3QnKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQuYXR0cigndmFsdWUnKSxcblx0XHRcdFx0XHRcdFx0XHRcdGNJRDogd2luZG93LmxvY2F0aW9uLmhyZWYubWF0Y2goL2NJRD1cXGQrLylbMF0sXG5cdFx0XHRcdFx0XHRcdFx0XHRzdGF0dXM6ICR0aGF0LmZpbmQoJ3NlbGVjdCcpLnZhbCgpXG5cdFx0XHRcdFx0XHRcdFx0fTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRvYmoudXJsID0gW1xuXHRcdFx0XHRcdFx0XHRcdFx0c3JjUGF0aCxcblx0XHRcdFx0XHRcdFx0XHRcdHF1ZXJ5U3RyaW5nLFxuXHRcdFx0XHRcdFx0XHRcdFx0J2FjdGlvbj1zdGF0dXNjb25maXJtJyxcblx0XHRcdFx0XHRcdFx0XHRcdCcmJyArIG9iai5jSURcblx0XHRcdFx0XHRcdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdHZhciAkZm9ybSA9ICQoJzxmb3JtIG5hbWU9XCJjdXN0b21lcnNcIiBtZXRob2Q9XCJwb3N0XCIgYWN0aW9uPScgKyBvYmoudXJsICsgJz48L2Zvcm0+Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJwYWdlX3Rva2VuXCIgdmFsdWU9JyArIG9iai5wYWdlVG9rZW4gKyAnPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwic3RhdHVzXCIgdmFsdWU9JyArIG9iai5zdGF0dXMgKyAnPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwic3RhdHVzY29uZmlybVwiIHZhbHVlPVwiVXBkYXRlXCI+Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uYXBwZW5kVG8oJ2JvZHknKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2lwbG9nJzpcblx0XHRcdFx0XHRidXR0b25zLnB1c2goe1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ25ld19tZW1vJzpcblx0XHRcdFx0XHRjb25zb2xlLmxvZyhzdWJtaXRCdG4pO1xuXHRcdFx0XHRcdGJ1dHRvbnMucHVzaCh7XG5cdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRidXR0b25zLnB1c2goe1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2VuZCcsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdC8vZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHRcdFx0Ly9nbV9jYW5jZWwoJ2dtX3NlbmRfb3JkZXIucGhwJywgJyZ0eXBlPWNhbmNlbCcsICdDQU5DRUwnKTtcblx0XHRcdFx0XHRcdFx0JHRoYXQuc3VibWl0KCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiBidXR0b25zO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ3JlYXRlcyBkaWFsb2cgZm9yIHNpbmdsZSByZW1vdmFsXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX29wZW5EZWxldGVEaWFsb2cgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19ERUxFVEVfQ1VTVE9NRVInLCAnYWRtaW5fY3VzdG9tZXJzJyksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IF9nZXRNb2RhbEJ1dHRvbnMoJHRoaXMpLFxuXHRcdFx0XHQnd2lkdGgnOiA0MjAsXG5cdFx0XHRcdCdjbG9zZU9uRXNjYXBlJzogZmFsc2UsXG5cdFx0XHRcdCdvcGVuJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCgnLnVpLWRpYWxvZy10aXRsZWJhci1jbG9zZScpLmhpZGUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDcmVhdGVzIGRpYWxvZyBmb3Igc2luZ2xlIHN0YXR1cyBjaGFuZ2Vcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3BlbkVkaXRTdGF0dXNEaWFsb2cgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19TVEFUVVNfQ1VTVE9NRVInLCAnYWRtaW5fY3VzdG9tZXJzJyksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IF9nZXRNb2RhbEJ1dHRvbnMoJHRoaXMpLFxuXHRcdFx0XHQnd2lkdGgnOiA0MjAsXG5cdFx0XHRcdCdjbG9zZU9uRXNjYXBlJzogZmFsc2UsXG5cdFx0XHRcdCdvcGVuJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0Ly8gTWFrZSBTb21lIEZpeGVzXG5cdFx0XHRcdFx0JCgnLnVpLWRpYWxvZy10aXRsZWJhci1jbG9zZScpLmhpZGUoKTtcblx0XHRcdFx0XHQkKHRoaXMpXG5cdFx0XHRcdFx0XHQuZmluZCgnc2VsZWN0W25hbWU9XCJzdGF0dXNcIl0nKVxuXHRcdFx0XHRcdFx0LmNzcyh7XG5cdFx0XHRcdFx0XHRcdHdpZHRoOiAnMTAwJScsXG5cdFx0XHRcdFx0XHRcdGhlaWdodDogJzM1cHgnLFxuXHRcdFx0XHRcdFx0XHRmb250U2l6ZTogJzEycHgnXG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDcmVhdGVzIGRpYWxvZyBmb3Igc2luZ2xlIElQIGxvZ1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9vcGVuSXBMb2dEaWFsb2cgPSBmdW5jdGlvbigpIHtcblx0XHRcdCR0aGlzID0gJCgnPGRpdj48L2Rpdj4nKTtcblx0XHRcdFxuXHRcdFx0JCgnW2RhdGEtaXBsb2ddJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMuYXBwZW5kKHRoaXMpO1xuXHRcdFx0XHQkdGhpcy5hcHBlbmQoJzxicj48YnI+Jyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JHRoaXMuYXBwZW5kVG8oJ2JvZHknKTtcblx0XHRcdCR0aGlzLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6ICdJUC1Mb2cnLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBfZ2V0TW9kYWxCdXR0b25zKCR0aGlzKSxcblx0XHRcdFx0J3dpZHRoJzogNDIwLFxuXHRcdFx0XHQnY2xvc2VPbkVzY2FwZSc6IGZhbHNlXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfb3Blbk5ld01lbW9EaWFsb2cgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyICRmb3JtID0gJCgnI2N1c3RvbWVyX21lbW9fZm9ybScpO1xuXHRcdFx0XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XG5cdFx0XHQkZm9ybS5kaWFsb2coe1xuXHRcdFx0XHQndGl0bGUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVEVYVF9ORVdfTUVNTycsICdhZG1pbl9jdXN0b21lcnMnKSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdCdidXR0b25zJzogX2dldE1vZGFsQnV0dG9ucygkZm9ybSksXG5cdFx0XHRcdCd3aWR0aCc6IDU4MFxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHRzd2l0Y2ggKG9wdGlvbnMuYWN0aW9uKSB7XG5cdFx0XHRcdGNhc2UgJ2RlbGV0ZSc6XG5cdFx0XHRcdFx0X29wZW5EZWxldGVEaWFsb2coKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnZWRpdHN0YXR1cyc6XG5cdFx0XHRcdFx0X29wZW5FZGl0U3RhdHVzRGlhbG9nKCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2lwbG9nJzpcblx0XHRcdFx0XHRfb3BlbklwTG9nRGlhbG9nKCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ25ld19tZW1vJzpcblx0XHRcdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb3Blbk5ld01lbW9EaWFsb2cpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
