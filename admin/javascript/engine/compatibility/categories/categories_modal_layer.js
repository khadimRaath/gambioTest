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
gx.compatibility.module(
	'categories_modal_layer',
	
	[],
	
	/**  @lends module:Compatibility/categories_modal_layer */
	
	function(data) {
		
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
		var _getUrlParameter = function(parameterName) {
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
		var _getModalButtons = function() {
			
			var buttons = [];
			
			switch (options.action) {
				case 'delete':
					buttons.push(
						{
							'text': jse.core.lang.translate('close', 'buttons'),
							'class': 'btn',
							'click': function() {
								$(this).dialog('close');
								var url = $container.find('a.btn').attr('href');
								window.open(url, '_self');
							}
						},
						{
							'text': jse.core.lang.translate('delete', 'buttons'),
							'class': 'btn btn-primary',
							'click': function() {
								var page_token = $('input[name="page_token"]').attr('value'),
									data = $('tr[data-id]').has('input[type="checkbox"]:checked').data();
								
								// Manipulate URL
								var url = [
									window.location.origin,
									window.location.pathname,
									'?action=multi_action_confirm',
									(data.isProduct ? '&pID=' : '&cID=') + data.id,
									'&cPath=' + data.cpath
								].join('');
								
								var search = _getUrlParameter('search');
								if (search !== 0 && search !== null) {
									url += ('&search=' + search);
								}
								
								var $form = $('<form name="multi_action_form" method="post" action=' + url
									+ '></form>');
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
					buttons.push(
						{
							'text': jse.core.lang.translate('close', 'buttons'),
							'class': 'btn',
							'click': function() {
								$(this).dialog('close');
								var url = $container.find('a.btn').attr('href');
								window.open(url, '_self');
							}
						},
						{
							'text': jse.core.lang.translate('BUTTON_MOVE', 'admin_buttons'),
							'class': 'btn btn-primary',
							'click': function() {
								var page_token = $('input[name="page_token"]:first').attr('value'),
									data = $('tr[data-id]').has('input[type="checkbox"]:checked').data(),
									toCatId = $(this).find('select[name="move_to_category_id"] option:selected').val();
								
								// Manipulate URL
								var url = [
									window.location.origin,
									window.location.pathname,
									'?action=multi_action_confirm',
									(data.isProduct ? '&pID=' : '&cID=') + data.id,
									'&cPath=' + data.cpath
								].join('');
								
								var $form = $('<form name="multi_action_form" method="post" action=' + url
									+ '></form>');
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
					buttons.push(
						{
							'text': jse.core.lang.translate('close', 'buttons'),
							'class': 'btn',
							'click': function() {
								$(this).dialog('close');
								var url = $container.find('a.btn').attr('href');
								window.open(url, '_self');
							}
						},
						{
							'text': jse.core.lang.translate('BUTTON_COPY', 'admin_buttons'),
							'class': 'btn btn-primary',
							'click': function() {
								var page_token = $('input[name="page_token"]:first').attr('value'),
									data = $('tr[data-id]').has('input[type="checkbox"]:checked').data(),
									destCatId = $(this).find('select[name="dest_category_id"] option:selected').val();
								
								// Manipulate URL
								var url = [
									window.location.origin,
									window.location.pathname,
									'?action=multi_action_confirm',
									(data.isProduct ? '&pID=' : '&cID=') + data.id,
									'&cPath=' + data.cpath
								].join('');
								
								var search = _getUrlParameter('search');
								if (search !== 0 && search !== null) {
									url += ('&search=' + search);
								}
								
								var $form = $('<form name="multi_action_form" method="post" action=' + url
									+ '></form>');
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
		var _openDeleteDialog = function() {
			
			$this.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_ELEMENTS', 'categories'),
				'modal': true,
				'dialogClass': 'gx-container modal-old-table',
				'buttons': _getModalButtons(),
				'width': 420,
				'closeOnEscape': false,
				'open': function() {
					$('.ui-dialog-titlebar-close').hide();
				}
			});
		};
		
		/**
		 * Creates dialog for the move of an article/category
		 * @private
		 */
		var _openMoveDialog = function() {
			
			$this.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_MOVE_ELEMENTS', 'categories'),
				'modal': true,
				'dialogClass': 'gx-container modal-old-table',
				'buttons': _getModalButtons(),
				'width': 420,
				'closeOnEscape': false,
				'open': function() {
					$('.ui-dialog-titlebar-close').hide();
				}
			});
		};
		
		/**
		 * Creates dialog for the copy of an article/category
		 * @private
		 */
		var _openCopyDialog = function() {
			$container.find('tr:eq(-2)').hide();
			
			$container.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_COPY_TO', 'categories'),
				'modal': true,
				'dialogClass': 'gx-container modal-old-table',
				'buttons': _getModalButtons(),
				'width': 420,
				'closeOnEscape': false,
				'open': function() {
					$('.ui-dialog-titlebar-close').hide();
				}
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
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
