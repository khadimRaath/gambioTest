/* --------------------------------------------------------------
 dynamic_shop_messages.js 2016-05-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Dynamic Shop Messages
 *
 * This extension module is meant to be executed once in every page load. Its purpose is to display
 * custom notifications into various positions of the HTML. The notification source may vary in each
 * case but the original data should come from Gambio's Customer Portal.
 *
 * The module supports the use of a "url" option which will be used for loading the JSON data through an
 * AJAX call.
 * 
 * ### Options 
 * 
 * **Data Source URL | `data-dynamic_shop_messages-url` | String | Optional**
 * 
 * Provide the URL which will be used to fetch the dynamic shop messages. By default the DynamicShopMessages
 * controller will be used. 
 * 
 * **Response Envelope | `data-dynamic_shop_messages-response-envelope` | String | Optional**
 * 
 * Set a custom response envelop for the response object. By default "MESSAGES" will be used, because this is 
 * the envelope from the Gambio Portal response. 
 *
 * ### Example
 * 
 * ```html
 * <div data-gx-extension="dynamic_shop_messages"
 *     data-dynamic_shop_messages-url="http://custom-url.com/myscript.php"
 *     data-dynamic_shop_messages-response-envelope="MESSAGES">
 *   <-- HTML CONTENT -->
 * </div>
 * ```
 *
 * @module Admin/Extensions/dynamic_shop_messages
 * @ignore
 */
gx.extensions.module(
	'dynamic_shop_messages',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES
		// ------------------------------------------------------------------------
		
		let
			/**
			 * Module Selector
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=DynamicShopMessages',
				lifetime: 30000, // maximum search lifetime (ms)
				responseEnvelope: 'MESSAGES'
			},
			
			/**
			 * Final Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Checks if an HTML markup string is valid.
		 *
		 * {@link http://stackoverflow.com/a/14216406}
		 *
		 * @param {string} html The HTML markup to be validated.
		 *
		 * @returns {bool} Returns the validation result.
		 */
		var _validateHtml = function(html) {
			var doc = document.createElement('div');
			doc.innerHTML = html;
			return (doc.innerHTML === html);
		};
		
		/**
		 * Check the current page matches the target_page value of the JSON data.
		 *
		 * @param {string|array} targetPageValue Contains a URL string or an array of URLs to be matched.
		 *
		 * @return {bool} Returns the validation check.
		 */
		var _checkTargetPage = function(targetPageValue) {
			var result = false;
			
			if (typeof targetPageValue !== 'object') {
				targetPageValue = [targetPageValue];
			}
			
			$.each(targetPageValue, function() {
				var regex = new RegExp(this);
				
				if (window.location.href === jse.core.config.get('appUrl') + '/admin/' + this
					|| regex.test(window.location.href)) {
					result = true;
					return false; // exit loop
				}
			});
			
			return result;
		};
		
		/**
		 * Try to apply the dynamic message data into the page.
		 *
		 * @param {array} messages
		 */
		var _apply = function(messages) {
			$.each(messages, function(index, entry) {
				try {
					// Check if we have target information in the message entry.
					if (entry.target_page === undefined || entry.target_selector === undefined) {
						throw new TypeError('No target information provided. Skipping to the next entry...');
					}
					
					// Check if we are in the target page.
					if (!_checkTargetPage(entry.target_page)) {
						throw new TypeError(
							'The entry is not targeted for the current page. Skipping to the next entry...');
					}
					
					// Find the target selector and append the HTML message. The module will keep on searching
					// for the target selector for as long as the "options.lifetime" value is.
					var currentTimestamp = Date.now;
					
					var intv = setInterval(function() {
						var $target = $this.find(entry.target_selector);
						
						if ($target.length > 0) {
							var htmlBackup = $target.html();
							$target.append(entry.message);
							
							// Check if the current HTML is valid and revert it otherwise.
							if (!_validateHtml($target.html())) {
								$target.html(htmlBackup);
								jse.core.debug.error('Dynamic message couldn\'t be applied.', entry);
							}
							
							clearInterval(intv); // stop searching
						}
						
						if (Date.now - currentTimestamp > options.lifetime) {
							clearInterval(intv);
							throw Error(
								'Search lifetime limit exceeded, no element matched the provided selector.');
						}
					}, 300);
					
				} catch (e) {
					return true; // Continue loop with next message entry.
				}
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$.getJSON(options.url)
				.done(function(response) {
					_apply(response[options.responseEnvelope]);
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					jse.core.debug.info('Could not load the dynamic shop messages.', jqXHR, textStatus,
						errorThrown);
				});
			
			done();
		};
		
		return module;
	});
