/* --------------------------------------------------------------
 bookmarks_nav_tabs.js 2015-09-29 gm
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
	'bookmarks_nav_tabs',
	
	[],
	
	/**  @lends module:Compatibility/bookmarks_nav_tabs */
	
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
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Link of the activated tab
			 *
			 * @type {object}
			 */
			link = '',
			
			/**
			 * Link of the activated tab
			 *
			 * @type {object}
			 */
			onClickValue = '',
			
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
		// OPERATIONS
		// ------------------------------------------------------------------------
		
		// Timeout is needed, else, some elements won't be found
		setTimeout(function() {
			$('.nav-tab').on('click', function(event) {
				
				if (link && onClickValue) {
					$('.no-link')
						.wrapInner('<a></a>')
						.removeClass('no-link')
						.children()
						.attr('href', link)
						.attr('onclick', onClickValue);
				}
				
				link = $(this).children().attr('href');
				onClickValue = $(this).children().attr('onclick');
				
				$(this).addClass('no-link');
				$(this).css('text-align', 'center');
				$(this).children().contents().unwrap();
			});
		}, 1000);
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			done();
		};
		
		return module;
	});
