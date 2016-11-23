/* --------------------------------------------------------------
 shop_key.js 2016-03-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Shop Key
 *
 * This module will update the information in the textarea of the shop key page and opens a modal layer for
 * more detailed information of the shop key
 *
 * @module Controllers/shop_key
 */
gx.controllers.module(
	'shop_key',
	
	[],
	
	/**  @lends module:Controllers/shop_key */
	
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Entering the shop key into the input field updates the content of the textarea containing shop information
		 * like the shop key
		 *
		 * @private
		 */
		var _updateTextarea = function() {
			var $textarea = $this.find('#shop-key-data'),
				html = $textarea.html().replace(/shop_key=.*?\nlanguage/g, 'shop_key=' + $.trim($(this).val()) +
					'\nlanguage');
			
			$textarea.html(html);
		};
		
		/**
		 * Clicking the link for more information about the shop key opens a modal box
		 *
		 * @param event
		 * @private
		 */
		var _showInformation = function(event) {
			var $information = $('<p class="shop-key-information">' +
				jse.core.lang.translate('purpose_description', 'shop_key') +
				'</p>');
			
			event.preventDefault();
			
			$information.dialog({
				'title': jse.core.lang.translate('page_title', 'shop_key'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					}
				],
				'width': 420
			});
		};
		
		/**
		 * Update action parameter of form to the delete-url if delete button is clicked
		 *
		 * @param event
		 * @private
		 */
		var _deleteShopKey = function(event) {
			var actionUrl = $this.attr('action');
			
			event.preventDefault();
			
			actionUrl = actionUrl.replace('do=ShopKey/Store', 'do=ShopKey/Destroy');
			$this.attr('action', actionUrl);
			
			$this.submit();
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this
				.on('change', '#gambio-shop-key', _updateTextarea)
				.on('keyup', '#gambio-shop-key', _updateTextarea)
				.on('click', '.show-shop-key-information', _showInformation)
				.on('click', 'input[name="delete"]', _deleteShopKey);
			
			done();
		};
		
		return module;
	});
