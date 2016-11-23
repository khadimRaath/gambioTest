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
gx.compatibility.module(
	'customers_modal_layer',
	
	[],
	
	/**  @lends module:Compatibility/customers_modal_layer */
	
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
			queryString = '?' + (window.location.search
					.replace(/\?/, '')
					.replace(/cID=[\d]+/g, '')
					.replace(/action=[\w]+/g, '')
					.replace(/pageToken=[\w]+/g, '')
					.concat('&')
					.replace(/&[&]+/g, '&')
					.replace(/^&/g, ''));
		
		// ------------------------------------------------------------------------
		// PRIVATE FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Prepares buttons for the modal
		 * @param {object | jQuery} $that
		 * @returns {Array}
		 * @private
		 */
		var _getModalButtons = function($that) {
			var buttons = [];
			
			var submitBtn, abortBtn;
			
			switch (options.action) {
				case 'delete':
					submitBtn = $that.find('input:first');
					abortBtn = $that.find('a.btn');
					
					$(submitBtn).hide();
					$(abortBtn).hide();
					
					buttons.push(
						{
							'text': jse.core.lang.translate('close', 'buttons'),
							'class': 'btn',
							'click': function() {
								$(this).dialog('close');
								abortBtn.trigger('click');
							}
						},
						{
							'text': jse.core.lang.translate('delete', 'buttons'),
							'class': 'btn btn-primary',
							'click': function() {
								var obj = {
									pageToken: $('input[name="page_token"]:first')
										.attr('value'),
									cID: window.location.href.match(/cID=\d+/)[0]
								};
								
								obj.url = [
									srcPath,
									queryString,
									'action=deleteconfirm',
									'&' + obj.cID
								].join('');
								
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
					
					buttons.push(
						{
							'text': jse.core.lang.translate('close', 'buttons'),
							'class': 'btn',
							'click': function() {
								$(this).dialog('close');
								window.open(abortBtn.attr('href'), '_self');
							}
						},
						{
							'text': jse.core.lang.translate('update', 'buttons'),
							'class': 'btn btn-primary',
							'click': function() {
								var obj = {
									pageToken: $('input[name="page_token"]:first')
										.attr('value'),
									cID: window.location.href.match(/cID=\d+/)[0],
									status: $that.find('select').val()
								};
								
								obj.url = [
									srcPath,
									queryString,
									'action=statusconfirm',
									'&' + obj.cID
								].join('');
								
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
						'click': function() {
							$(this).dialog('close');
						}
					});
					break;
				case 'new_memo':
					console.log(submitBtn);
					buttons.push({
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					});
					buttons.push({
						'text': jse.core.lang.translate('send', 'buttons'),
						'class': 'btn btn-primary',
						'click': function(event) {
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
		var _openDeleteDialog = function() {
			$this.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_CUSTOMER', 'admin_customers'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($this),
				'width': 420,
				'closeOnEscape': false,
				'open': function() {
					$('.ui-dialog-titlebar-close').hide();
				}
			});
		};
		
		/**
		 * Creates dialog for single status change
		 * @private
		 */
		var _openEditStatusDialog = function() {
			$this.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_STATUS_CUSTOMER', 'admin_customers'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($this),
				'width': 420,
				'closeOnEscape': false,
				'open': function() {
					// Make Some Fixes
					$('.ui-dialog-titlebar-close').hide();
					$(this)
						.find('select[name="status"]')
						.css({
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
		var _openIpLogDialog = function() {
			$this = $('<div></div>');
			
			$('[data-iplog]').each(function() {
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
		
		var _openNewMemoDialog = function(event) {
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
		
		module.init = function(done) {
			
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
