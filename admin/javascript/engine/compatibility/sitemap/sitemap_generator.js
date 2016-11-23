/* --------------------------------------------------------------
 sitemap_generator.js 2016-08-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Sitemap Generator Controller
 *
 * This module will execute the sitemap generation
 *
 * @module Compatibility/sitemap_generator
 */
gx.compatibility.module(
	'sitemap_generator',
	
	[
		gx.source + '/libs/info_messages',
		'loading_spinner'
	],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES 
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Loading Spinner Selector 
			 * 
			 * @type {object}
			 */
			$spinner,
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {url: 'gm_sitemap_creator.php'},
			
			/**
			 * Final Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Reference to the info messages library
			 * 
			 * @type {object}
			 */
			messages = jse.libs.info_messages,
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		var _createSitemapXml = function() {
			$.ajax({
					url: options.url,
					data: options.params
				})
				// On success
				.done(function(response) {
					messages.addSuccess(response);
					jse.libs.loading_spinner.hide($spinner);
				})
				// On Failure
				.fail(function(response) {
					jse.core.debug.error('Prepare Content Error: ', response);
				});
		};
		
		var _prepareCategories = function(deferred) {
			deferred = deferred || $.Deferred(); 
			
			$.ajax({
					url: options.url,
					data: {
						action: 'prepare_categories',
						page_token: jse.core.config.get('pageToken')
					},
					dataType: 'json'
				})
				// On success
				.done(function(response) {
					if (response.repeat === true) {
						_prepareCategories(deferred); 
					} else {
						deferred.resolve();
					}
				})
				// On Failure
				.fail(function(response) {
					jse.core.debug.error('Prepare Categories Error: ', response);
					deferred.reject();
				});
			
			return deferred.promise();
		};
		
		var _prepareContent = function(deferred) {
			deferred = deferred || $.Deferred(); 
			
			$.ajax({
					url: options.url,
					data: {
						action: 'prepare_content',
						page_token: jse.core.config.get('pageToken')
					},
					dataType: 'json'
				})
				// On success
				.done(function(response) {
					if (response.repeat === true) {
						_prepareContent(deferred);
					} else {
						deferred.resolve();
					}
				})
				// On Failure
				.fail(function(response) {
					jse.core.debug.error('Prepare Content Error: ', response);
					deferred.reject();
				});
			
			return deferred.promise(); 
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('click', function() {
				$spinner = jse.libs.loading_spinner.show($this.parents().eq(2));
				$.when(_prepareCategories(), _prepareContent()).done(_createSitemapXml); 
				$this.blur();
				return false;
			});
			
			done();
		};
		
		return module;
	});
