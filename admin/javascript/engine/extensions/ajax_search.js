/* --------------------------------------------------------------
 ajax_search.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## AJAX Search Extension
 *
 * Enables the AJAX search and display for an element. This extension is used along with text_edit.js and 
 * ajax_search.js in the Gambio Admin "Text Edit | Texte Anpassen" page.
 *
 * @module Admin/Extensions/ajax_search
 * @ignore
 */
gx.extensions.module(
	'ajax_search',
	
	['form'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Extension Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options for Extension.
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * AJAX URL
			 *
			 * @type {string}
			 */
			url = $this.attr('action'),
			
			/**
			 * Final Extension Options
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
		// META INITIALIZE
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize function of the extension, called by the engine.
		 */
		module.init = function(done) {
			
			var template = $(options.template).html(),
				$target = $(options.target);
			
			$this.on('submit', function(event) {
				event.preventDefault();
				
				var data = jse.libs.form.getData($this);
				
				// Check for required fields.
				var abort = false;
				$this.find('[required]').each(function() {
					if ($(this).val() === '') {
						abort = true;
						return false; // exit $.each loop
					}
				});
				if (abort) {
					return; // abort because there is a missing field
				}
				
				$.ajax({
					'url': url,
					'method': 'post',
					'dataType': 'json',
					'data': data,
					'page_token': jse.core.config.get('pageToken')
				}).done(function(result) {
					var markup = Mustache.render(template, result.payload);
					$target
						.empty()
						.append(markup)
						.parent()
						.show();
				});
				
			});
			
			done();
		};
		
		// Return data to module engine
		return module;
	});
