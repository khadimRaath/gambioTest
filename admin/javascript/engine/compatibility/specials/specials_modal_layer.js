/* --------------------------------------------------------------
 specials_modal_layer.js 2015-09-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Lang */

/**
 * ## Specials Modal Layer Module
 *
 * This module will open a modal layer for order actions like deleting or changing the oder status.
 *
 * @module Compatibility/specials_modal_layer
 */
gx.compatibility.module(
	'specials_modal_layer',
	
	[],
	
	/**  @lends module:Compatibility/specials_modal_layer */
	
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
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE FUNCTIONS
		// ------------------------------------------------------------------------
		
		var _openDeleteDialog = function(event) {
			
			var $form = $('#delete_confirm_form');
			var stringPos = $form.attr('action').indexOf('&sID=');
			
			if (stringPos !== -1) {
				$form.attr('action', $form.attr('action').substr(0, stringPos));
			}
			
			$form.attr('action', $form.attr('action') + '&sID=' + options.special_id);
			
			$form.find('.product-name').html(options.name);
			
			event.preventDefault();
			$form.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_SPECIALS', 'admin_specials'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($form),
				'width': 420
			});
			
		};
		
		var _getModalButtons = function($form) {
			var buttons = [
				{
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': jse.core.lang.translate('delete', 'buttons'),
					'class': 'btn btn-primary',
					'click': function() {
						$form.submit();
					}
				}
			];
			
			return buttons;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('click', _openDeleteDialog);
			done();
		};
		
		return module;
	});
