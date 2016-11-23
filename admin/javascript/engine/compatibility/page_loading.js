/* --------------------------------------------------------------
 page_loading.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Page Loading Module
 *
 * This module will display a loading page screen for approximately 1 second,
 * the time needed by the engine for the conversion. It will also fade the page
 * out when the user leaves a page, making the transition very smooth.
 *
 * @module Compatibility/page_loading
 */
gx.compatibility.module(
	'page_loading',
	
	[],
	
	/**  @lends module:Compatibility/page_loading */
	
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
			 * Excluded pages to prevent white fade in.
			 * @type {string[]}
			 */
			excludedPages = [
				'backup.php',
				'gm_backup_files_zip.php',
				'orders_iloxx.php'
			],
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE FUNCTIONS
		// ------------------------------------------------------------------------
		
		var _pageLoad = function() {
			// show page content
			$this.delay(300).fadeIn(200, function() {
				$this.removeClass('hidden');
			});
		};
		
		var _pageUnload = function() {
			$('body').fadeOut(100); // Hide the entire body tag
		};
		
		/**
		 * Indicates if the current page is contained in excluded pages array.
		 * @return {boolean}
		 */
		var _isExcludedPage = function() {
			var currentFile, found, result;
			
			currentFile = jse.libs.url_arguments.getCurrentFile();
			found = excludedPages.indexOf(currentFile);
			
			return found !== -1 ? true : false;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			//_pageLoad();
			
			$(window).on('beforeunload', function() {
				if (!_isExcludedPage()) {
					_pageUnload();
				}
			});
			
			
			$('body').on('JSENGINE_INIT_FINISHED', function() {
				$this.fadeIn(200, function() {
					$this.removeClass('hidden');
				});
			});
			
			done();
		};
		
		return module;
	});
