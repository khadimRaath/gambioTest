'use strict';

/* --------------------------------------------------------------
 categories_modal_layer.js 2015-09-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Modal Layer Module
 *
 * This module will open a modal layer for categories/articles actions like deleting the article.
 *
 * @module Compatibility/categories_modal_layer
 */
gx.compatibility.module('categories_modal_layer', [],

/**  @lends module:Compatibility/categories_modal_layer */

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
  * Parent container table, which contains this part and the buttons
  * @type {object}
  */
	$container = $(this).parents('table:first'),


	/**
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#modal_layer_container'),


	/**
  * Get checkboxes selector
  *
  * @type {object}
  */
	// $checkboxes = $('.gx-categories-table tr:not(.dataTableHeadingRow) input'),

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
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Get Url Parameter
  *
  * Gets a specific URL get parameter from the address bar,
  * which name should be provided as an argument.
  * @param {string} parameterName
  * @returns {object}
  * @private
  */
	var _getUrlParameter = function _getUrlParameter(parameterName) {
		var results = new RegExp('[\?&]' + parameterName + '=([^&#]*)').exec(window.location.href);
		if (results == null) {
			return null;
		} else {
			return results[1] || 0;
		}
	};

	/**
  * Prepares buttons for the modal
  *
  * @returns {Array}
  */
	var _getModalButtons = function _getModalButtons() {

		var buttons = [];

		switch (options.action) {
			case 'delete':
				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
						var url = $container.find('a.btn').attr('href');
						window.open(url, '_self');
					}
				}, {
					'text': jse.core.lang.translate('delete', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						var page_token = $('input[name="page_token"]').attr('value'),
						    data = $('tr[data-id]').has('input[type="checkbox"]:checked').data();

						// Manipulate URL
						var url = [window.location.origin, window.location.pathname, '?action=multi_action_confirm', (data.isProduct ? '&pID=' : '&cID=') + data.id, '&cPath=' + data.cpath].join('');

						var search = _getUrlParameter('search');
						if (search !== 0 && search !== null) {
							url += '&search=' + search;
						}

						var $form = $('<form name="multi_action_form" method="post" action=' + url + '></form>');
						$form.append('<input type="hidden" name="cPath" value=' + data.cpath + '>');
						$form.append('<input type="hidden" name="page_token" value=' + page_token + '>');
						$('tr[data-id]').find('input[type="checkbox"]:checked').clone().appendTo($form);
						$(this).find('input[type="checkbox"]:checked').clone().appendTo($form);
						$form.append('<input type="hidden" name="multi_delete_confirm" value="DeleteConfirm">');
						$form.appendTo('body');
						$form.submit();
					}
				});
				break;

			case 'move':
				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
						var url = $container.find('a.btn').attr('href');
						window.open(url, '_self');
					}
				}, {
					'text': jse.core.lang.translate('BUTTON_MOVE', 'admin_buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						var page_token = $('input[name="page_token"]:first').attr('value'),
						    data = $('tr[data-id]').has('input[type="checkbox"]:checked').data(),
						    toCatId = $(this).find('select[name="move_to_category_id"] option:selected').val();

						// Manipulate URL
						var url = [window.location.origin, window.location.pathname, '?action=multi_action_confirm', (data.isProduct ? '&pID=' : '&cID=') + data.id, '&cPath=' + data.cpath].join('');

						var $form = $('<form name="multi_action_form" method="post" action=' + url + '></form>');
						$form.append('<input type="hidden" name="cPath" value=' + data.cpath + '>');
						$form.append('<input type="hidden" name="page_token" value=' + page_token + '>');
						$('tr[data-id]').find('input[type="checkbox"]:checked').clone().appendTo($form);
						$container.find('input[name="src_category_id"]').clone().appendTo($form);
						$form.append('<input type="hidden" name="move_to_category_id" value=' + toCatId + '>');
						$form.append('<input type="hidden" name="multi_move_confirm" value="MoveConfirm">');
						$form.appendTo('body');
						$form.submit();
					}
				});
				break;

			case 'copy':
				buttons.push({
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
						var url = $container.find('a.btn').attr('href');
						window.open(url, '_self');
					}
				}, {
					'text': jse.core.lang.translate('BUTTON_COPY', 'admin_buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						var page_token = $('input[name="page_token"]:first').attr('value'),
						    data = $('tr[data-id]').has('input[type="checkbox"]:checked').data(),
						    destCatId = $(this).find('select[name="dest_category_id"] option:selected').val();

						// Manipulate URL
						var url = [window.location.origin, window.location.pathname, '?action=multi_action_confirm', (data.isProduct ? '&pID=' : '&cID=') + data.id, '&cPath=' + data.cpath].join('');

						var search = _getUrlParameter('search');
						if (search !== 0 && search !== null) {
							url += '&search=' + search;
						}

						var $form = $('<form name="multi_action_form" method="post" action=' + url + '></form>');
						$form.append('<input type="hidden" name="cPath" value=' + data.cpath + '>');
						$form.append('<input type="hidden" name="page_token" value=' + page_token + '>');
						$('tr[data-id]').find('input[type="checkbox"]:checked').clone().appendTo($form);
						$container.find('input').clone().appendTo($form);
						$form.append('<input type="hidden" name="dest_category_id" value=' + destCatId + '>');
						$form.append('<input type="hidden" name="multi_copy_confirm" value="MoveConfirm">');
						$form.appendTo('body');
						$form.submit();
					}
				});
				break;
		}

		return buttons;
	};

	/**
  * Creates dialog for single removal of an article/category
  * @private
  */
	var _openDeleteDialog = function _openDeleteDialog() {

		$this.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_ELEMENTS', 'categories'),
			'modal': true,
			'dialogClass': 'gx-container modal-old-table',
			'buttons': _getModalButtons(),
			'width': 420,
			'closeOnEscape': false,
			'open': function open() {
				$('.ui-dialog-titlebar-close').hide();
			}
		});
	};

	/**
  * Creates dialog for the move of an article/category
  * @private
  */
	var _openMoveDialog = function _openMoveDialog() {

		$this.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_MOVE_ELEMENTS', 'categories'),
			'modal': true,
			'dialogClass': 'gx-container modal-old-table',
			'buttons': _getModalButtons(),
			'width': 420,
			'closeOnEscape': false,
			'open': function open() {
				$('.ui-dialog-titlebar-close').hide();
			}
		});
	};

	/**
  * Creates dialog for the copy of an article/category
  * @private
  */
	var _openCopyDialog = function _openCopyDialog() {
		$container.find('tr:eq(-2)').hide();

		$container.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_COPY_TO', 'categories'),
			'modal': true,
			'dialogClass': 'gx-container modal-old-table',
			'buttons': _getModalButtons(),
			'width': 420,
			'closeOnEscape': false,
			'open': function open() {
				$('.ui-dialog-titlebar-close').hide();
			}
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
			case 'move':
				_openMoveDialog();
				break;
			case 'copy':
				_openCopyDialog();
				break;
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc19tb2RhbF9sYXllci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGNvbnRhaW5lciIsInBhcmVudHMiLCIkbW9kYWwiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0VXJsUGFyYW1ldGVyIiwicGFyYW1ldGVyTmFtZSIsInJlc3VsdHMiLCJSZWdFeHAiLCJleGVjIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwiX2dldE1vZGFsQnV0dG9ucyIsImJ1dHRvbnMiLCJhY3Rpb24iLCJwdXNoIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJkaWFsb2ciLCJ1cmwiLCJmaW5kIiwiYXR0ciIsIm9wZW4iLCJwYWdlX3Rva2VuIiwiaGFzIiwib3JpZ2luIiwicGF0aG5hbWUiLCJpc1Byb2R1Y3QiLCJpZCIsImNwYXRoIiwiam9pbiIsInNlYXJjaCIsIiRmb3JtIiwiYXBwZW5kIiwiY2xvbmUiLCJhcHBlbmRUbyIsInN1Ym1pdCIsInRvQ2F0SWQiLCJ2YWwiLCJkZXN0Q2F0SWQiLCJfb3BlbkRlbGV0ZURpYWxvZyIsImhpZGUiLCJfb3Blbk1vdmVEaWFsb2ciLCJfb3BlbkNvcHlEaWFsb2ciLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVdBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0Msd0JBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7O0FBSUFDLGNBQWFELEVBQUUsSUFBRixFQUFRRSxPQUFSLENBQWdCLGFBQWhCLENBWmQ7OztBQWNDOzs7OztBQUtBQyxVQUFTSCxFQUFFLHdCQUFGLENBbkJWOzs7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RTtBQUlqQixjQUFXUCxrQkFKTTtBQUtqQixZQUFTLEdBTFE7QUFNakIsb0JBQWlCLEtBTkE7QUFPakIsV0FBUSxnQkFBVztBQUNsQmYsTUFBRSwyQkFBRixFQUErQjhDLElBQS9CO0FBQ0E7QUFUZ0IsR0FBbEI7QUFXQSxFQWREOztBQWdCQTtBQUNBO0FBQ0E7O0FBRUFqRCxRQUFPb0QsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixVQUFRN0MsUUFBUVksTUFBaEI7QUFDQyxRQUFLLFFBQUw7QUFDQzRCO0FBQ0E7QUFDRCxRQUFLLE1BQUw7QUFDQ0U7QUFDQTtBQUNELFFBQUssTUFBTDtBQUNDQztBQUNBO0FBVEY7O0FBWUFFO0FBQ0EsRUFkRDs7QUFnQkEsUUFBT3JELE1BQVA7QUFDQSxDQXpURiIsImZpbGUiOiJjYXRlZ29yaWVzL2NhdGVnb3JpZXNfbW9kYWxfbGF5ZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhdGVnb3JpZXNfbW9kYWxfbGF5ZXIuanMgMjAxNS0wOS0yNFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cblxuLyoqXG4gKiAjIyBDYXRlZ29yaWVzIE1vZGFsIExheWVyIE1vZHVsZVxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgb3BlbiBhIG1vZGFsIGxheWVyIGZvciBjYXRlZ29yaWVzL2FydGljbGVzIGFjdGlvbnMgbGlrZSBkZWxldGluZyB0aGUgYXJ0aWNsZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY2F0ZWdvcmllc19tb2RhbF9sYXllclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2NhdGVnb3JpZXNfbW9kYWxfbGF5ZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jYXRlZ29yaWVzX21vZGFsX2xheWVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFBhcmVudCBjb250YWluZXIgdGFibGUsIHdoaWNoIGNvbnRhaW5zIHRoaXMgcGFydCBhbmQgdGhlIGJ1dHRvbnNcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRjb250YWluZXIgPSAkKHRoaXMpLnBhcmVudHMoJ3RhYmxlOmZpcnN0JyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kYWwgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkbW9kYWwgPSAkKCcjbW9kYWxfbGF5ZXJfY29udGFpbmVyJyksXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogR2V0IGNoZWNrYm94ZXMgc2VsZWN0b3Jcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0ICovXG5cdFx0Ly8gJGNoZWNrYm94ZXMgPSAkKCcuZ3gtY2F0ZWdvcmllcy10YWJsZSB0cjpub3QoLmRhdGFUYWJsZUhlYWRpbmdSb3cpIGlucHV0JyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgVXJsIFBhcmFtZXRlclxuXHRcdCAqXG5cdFx0ICogR2V0cyBhIHNwZWNpZmljIFVSTCBnZXQgcGFyYW1ldGVyIGZyb20gdGhlIGFkZHJlc3MgYmFyLFxuXHRcdCAqIHdoaWNoIG5hbWUgc2hvdWxkIGJlIHByb3ZpZGVkIGFzIGFuIGFyZ3VtZW50LlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBwYXJhbWV0ZXJOYW1lXG5cdFx0ICogQHJldHVybnMge29iamVjdH1cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0VXJsUGFyYW1ldGVyID0gZnVuY3Rpb24ocGFyYW1ldGVyTmFtZSkge1xuXHRcdFx0dmFyIHJlc3VsdHMgPSBuZXcgUmVnRXhwKCdbXFw/Jl0nICsgcGFyYW1ldGVyTmFtZSArICc9KFteJiNdKiknKS5leGVjKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcblx0XHRcdGlmIChyZXN1bHRzID09IG51bGwpIHtcblx0XHRcdFx0cmV0dXJuIG51bGw7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRyZXR1cm4gcmVzdWx0c1sxXSB8fCAwO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUHJlcGFyZXMgYnV0dG9ucyBmb3IgdGhlIG1vZGFsXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7QXJyYXl9XG5cdFx0ICovXG5cdFx0dmFyIF9nZXRNb2RhbEJ1dHRvbnMgPSBmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdFx0dmFyIGJ1dHRvbnMgPSBbXTtcblx0XHRcdFxuXHRcdFx0c3dpdGNoIChvcHRpb25zLmFjdGlvbikge1xuXHRcdFx0XHRjYXNlICdkZWxldGUnOlxuXHRcdFx0XHRcdGJ1dHRvbnMucHVzaChcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHVybCA9ICRjb250YWluZXIuZmluZCgnYS5idG4nKS5hdHRyKCdocmVmJyk7XG5cdFx0XHRcdFx0XHRcdFx0d2luZG93Lm9wZW4odXJsLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0biBidG4tcHJpbWFyeScsXG5cdFx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdHZhciBwYWdlX3Rva2VuID0gJCgnaW5wdXRbbmFtZT1cInBhZ2VfdG9rZW5cIl0nKS5hdHRyKCd2YWx1ZScpLFxuXHRcdFx0XHRcdFx0XHRcdFx0ZGF0YSA9ICQoJ3RyW2RhdGEtaWRdJykuaGFzKCdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl06Y2hlY2tlZCcpLmRhdGEoKTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHQvLyBNYW5pcHVsYXRlIFVSTFxuXHRcdFx0XHRcdFx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdFx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24ub3JpZ2luLFxuXHRcdFx0XHRcdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uLnBhdGhuYW1lLFxuXHRcdFx0XHRcdFx0XHRcdFx0Jz9hY3Rpb249bXVsdGlfYWN0aW9uX2NvbmZpcm0nLFxuXHRcdFx0XHRcdFx0XHRcdFx0KGRhdGEuaXNQcm9kdWN0ID8gJyZwSUQ9JyA6ICcmY0lEPScpICsgZGF0YS5pZCxcblx0XHRcdFx0XHRcdFx0XHRcdCcmY1BhdGg9JyArIGRhdGEuY3BhdGhcblx0XHRcdFx0XHRcdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdHZhciBzZWFyY2ggPSBfZ2V0VXJsUGFyYW1ldGVyKCdzZWFyY2gnKTtcblx0XHRcdFx0XHRcdFx0XHRpZiAoc2VhcmNoICE9PSAwICYmIHNlYXJjaCAhPT0gbnVsbCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dXJsICs9ICgnJnNlYXJjaD0nICsgc2VhcmNoKTtcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0dmFyICRmb3JtID0gJCgnPGZvcm0gbmFtZT1cIm11bHRpX2FjdGlvbl9mb3JtXCIgbWV0aG9kPVwicG9zdFwiIGFjdGlvbj0nICsgdXJsXG5cdFx0XHRcdFx0XHRcdFx0XHQrICc+PC9mb3JtPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiY1BhdGhcIiB2YWx1ZT0nICsgZGF0YS5jcGF0aCArICc+Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJwYWdlX3Rva2VuXCIgdmFsdWU9JyArIHBhZ2VfdG9rZW4gKyAnPicpO1xuXHRcdFx0XHRcdFx0XHRcdCQoJ3RyW2RhdGEtaWRdJykuZmluZCgnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdOmNoZWNrZWQnKS5jbG9uZSgpLmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLmZpbmQoJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXTpjaGVja2VkJykuY2xvbmUoKS5hcHBlbmRUbygkZm9ybSk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJtdWx0aV9kZWxldGVfY29uZmlybVwiIHZhbHVlPVwiRGVsZXRlQ29uZmlybVwiPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZFRvKCdib2R5Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcblx0XHRcdFx0Y2FzZSAnbW92ZSc6XG5cdFx0XHRcdFx0YnV0dG9ucy5wdXNoKFxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHR2YXIgdXJsID0gJGNvbnRhaW5lci5maW5kKCdhLmJ0bicpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdFx0XHRcdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVVRUT05fTU9WRScsICdhZG1pbl9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4gYnRuLXByaW1hcnknLFxuXHRcdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHR2YXIgcGFnZV90b2tlbiA9ICQoJ2lucHV0W25hbWU9XCJwYWdlX3Rva2VuXCJdOmZpcnN0JykuYXR0cigndmFsdWUnKSxcblx0XHRcdFx0XHRcdFx0XHRcdGRhdGEgPSAkKCd0cltkYXRhLWlkXScpLmhhcygnaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdOmNoZWNrZWQnKS5kYXRhKCksXG5cdFx0XHRcdFx0XHRcdFx0XHR0b0NhdElkID0gJCh0aGlzKS5maW5kKCdzZWxlY3RbbmFtZT1cIm1vdmVfdG9fY2F0ZWdvcnlfaWRcIl0gb3B0aW9uOnNlbGVjdGVkJykudmFsKCk7XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0Ly8gTWFuaXB1bGF0ZSBVUkxcblx0XHRcdFx0XHRcdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRcdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uLm9yaWdpbixcblx0XHRcdFx0XHRcdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSxcblx0XHRcdFx0XHRcdFx0XHRcdCc/YWN0aW9uPW11bHRpX2FjdGlvbl9jb25maXJtJyxcblx0XHRcdFx0XHRcdFx0XHRcdChkYXRhLmlzUHJvZHVjdCA/ICcmcElEPScgOiAnJmNJRD0nKSArIGRhdGEuaWQsXG5cdFx0XHRcdFx0XHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoXG5cdFx0XHRcdFx0XHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHR2YXIgJGZvcm0gPSAkKCc8Zm9ybSBuYW1lPVwibXVsdGlfYWN0aW9uX2Zvcm1cIiBtZXRob2Q9XCJwb3N0XCIgYWN0aW9uPScgKyB1cmxcblx0XHRcdFx0XHRcdFx0XHRcdCsgJz48L2Zvcm0+Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJjUGF0aFwiIHZhbHVlPScgKyBkYXRhLmNwYXRoICsgJz4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cInBhZ2VfdG9rZW5cIiB2YWx1ZT0nICsgcGFnZV90b2tlbiArICc+Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JCgndHJbZGF0YS1pZF0nKS5maW5kKCdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl06Y2hlY2tlZCcpLmNsb25lKCkuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0XHRcdFx0XHRcdCRjb250YWluZXIuZmluZCgnaW5wdXRbbmFtZT1cInNyY19jYXRlZ29yeV9pZFwiXScpLmNsb25lKCkuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwibW92ZV90b19jYXRlZ29yeV9pZFwiIHZhbHVlPScgKyB0b0NhdElkICsgJz4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIm11bHRpX21vdmVfY29uZmlybVwiIHZhbHVlPVwiTW92ZUNvbmZpcm1cIj4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmRUbygnYm9keScpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XG5cdFx0XHRcdGNhc2UgJ2NvcHknOlxuXHRcdFx0XHRcdGJ1dHRvbnMucHVzaChcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHVybCA9ICRjb250YWluZXIuZmluZCgnYS5idG4nKS5hdHRyKCdocmVmJyk7XG5cdFx0XHRcdFx0XHRcdFx0d2luZG93Lm9wZW4odXJsLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQlVUVE9OX0NPUFknLCAnYWRtaW5fYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHBhZ2VfdG9rZW4gPSAkKCdpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXTpmaXJzdCcpLmF0dHIoJ3ZhbHVlJyksXG5cdFx0XHRcdFx0XHRcdFx0XHRkYXRhID0gJCgndHJbZGF0YS1pZF0nKS5oYXMoJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXTpjaGVja2VkJykuZGF0YSgpLFxuXHRcdFx0XHRcdFx0XHRcdFx0ZGVzdENhdElkID0gJCh0aGlzKS5maW5kKCdzZWxlY3RbbmFtZT1cImRlc3RfY2F0ZWdvcnlfaWRcIl0gb3B0aW9uOnNlbGVjdGVkJykudmFsKCk7XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0Ly8gTWFuaXB1bGF0ZSBVUkxcblx0XHRcdFx0XHRcdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRcdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uLm9yaWdpbixcblx0XHRcdFx0XHRcdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSxcblx0XHRcdFx0XHRcdFx0XHRcdCc/YWN0aW9uPW11bHRpX2FjdGlvbl9jb25maXJtJyxcblx0XHRcdFx0XHRcdFx0XHRcdChkYXRhLmlzUHJvZHVjdCA/ICcmcElEPScgOiAnJmNJRD0nKSArIGRhdGEuaWQsXG5cdFx0XHRcdFx0XHRcdFx0XHQnJmNQYXRoPScgKyBkYXRhLmNwYXRoXG5cdFx0XHRcdFx0XHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHR2YXIgc2VhcmNoID0gX2dldFVybFBhcmFtZXRlcignc2VhcmNoJyk7XG5cdFx0XHRcdFx0XHRcdFx0aWYgKHNlYXJjaCAhPT0gMCAmJiBzZWFyY2ggIT09IG51bGwpIHtcblx0XHRcdFx0XHRcdFx0XHRcdHVybCArPSAoJyZzZWFyY2g9JyArIHNlYXJjaCk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdHZhciAkZm9ybSA9ICQoJzxmb3JtIG5hbWU9XCJtdWx0aV9hY3Rpb25fZm9ybVwiIG1ldGhvZD1cInBvc3RcIiBhY3Rpb249JyArIHVybFxuXHRcdFx0XHRcdFx0XHRcdFx0KyAnPjwvZm9ybT4nKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImNQYXRoXCIgdmFsdWU9JyArIGRhdGEuY3BhdGggKyAnPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwicGFnZV90b2tlblwiIHZhbHVlPScgKyBwYWdlX3Rva2VuICsgJz4nKTtcblx0XHRcdFx0XHRcdFx0XHQkKCd0cltkYXRhLWlkXScpLmZpbmQoJ2lucHV0W3R5cGU9XCJjaGVja2JveFwiXTpjaGVja2VkJykuY2xvbmUoKS5hcHBlbmRUbygkZm9ybSk7XG5cdFx0XHRcdFx0XHRcdFx0JGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLmNsb25lKCkuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiZGVzdF9jYXRlZ29yeV9pZFwiIHZhbHVlPScgKyBkZXN0Q2F0SWQgKyAnPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwibXVsdGlfY29weV9jb25maXJtXCIgdmFsdWU9XCJNb3ZlQ29uZmlybVwiPicpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmFwcGVuZFRvKCdib2R5Jyk7XG5cdFx0XHRcdFx0XHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRyZXR1cm4gYnV0dG9ucztcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENyZWF0ZXMgZGlhbG9nIGZvciBzaW5nbGUgcmVtb3ZhbCBvZiBhbiBhcnRpY2xlL2NhdGVnb3J5XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX29wZW5EZWxldGVEaWFsb2cgPSBmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZGlhbG9nKHtcblx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RFWFRfSU5GT19IRUFESU5HX0RFTEVURV9FTEVNRU5UUycsICdjYXRlZ29yaWVzJyksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXIgbW9kYWwtb2xkLXRhYmxlJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBfZ2V0TW9kYWxCdXR0b25zKCksXG5cdFx0XHRcdCd3aWR0aCc6IDQyMCxcblx0XHRcdFx0J2Nsb3NlT25Fc2NhcGUnOiBmYWxzZSxcblx0XHRcdFx0J29wZW4nOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkKCcudWktZGlhbG9nLXRpdGxlYmFyLWNsb3NlJykuaGlkZSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENyZWF0ZXMgZGlhbG9nIGZvciB0aGUgbW92ZSBvZiBhbiBhcnRpY2xlL2NhdGVnb3J5XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX29wZW5Nb3ZlRGlhbG9nID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRcblx0XHRcdCR0aGlzLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19NT1ZFX0VMRU1FTlRTJywgJ2NhdGVnb3JpZXMnKSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lciBtb2RhbC1vbGQtdGFibGUnLFxuXHRcdFx0XHQnYnV0dG9ucyc6IF9nZXRNb2RhbEJ1dHRvbnMoKSxcblx0XHRcdFx0J3dpZHRoJzogNDIwLFxuXHRcdFx0XHQnY2xvc2VPbkVzY2FwZSc6IGZhbHNlLFxuXHRcdFx0XHQnb3Blbic6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQoJy51aS1kaWFsb2ctdGl0bGViYXItY2xvc2UnKS5oaWRlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ3JlYXRlcyBkaWFsb2cgZm9yIHRoZSBjb3B5IG9mIGFuIGFydGljbGUvY2F0ZWdvcnlcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3BlbkNvcHlEaWFsb2cgPSBmdW5jdGlvbigpIHtcblx0XHRcdCRjb250YWluZXIuZmluZCgndHI6ZXEoLTIpJykuaGlkZSgpO1xuXHRcdFx0XG5cdFx0XHQkY29udGFpbmVyLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19DT1BZX1RPJywgJ2NhdGVnb3JpZXMnKSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lciBtb2RhbC1vbGQtdGFibGUnLFxuXHRcdFx0XHQnYnV0dG9ucyc6IF9nZXRNb2RhbEJ1dHRvbnMoKSxcblx0XHRcdFx0J3dpZHRoJzogNDIwLFxuXHRcdFx0XHQnY2xvc2VPbkVzY2FwZSc6IGZhbHNlLFxuXHRcdFx0XHQnb3Blbic6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQoJy51aS1kaWFsb2ctdGl0bGViYXItY2xvc2UnKS5oaWRlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdHN3aXRjaCAob3B0aW9ucy5hY3Rpb24pIHtcblx0XHRcdFx0Y2FzZSAnZGVsZXRlJzpcblx0XHRcdFx0XHRfb3BlbkRlbGV0ZURpYWxvZygpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdtb3ZlJzpcblx0XHRcdFx0XHRfb3Blbk1vdmVEaWFsb2coKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnY29weSc6XG5cdFx0XHRcdFx0X29wZW5Db3B5RGlhbG9nKCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
