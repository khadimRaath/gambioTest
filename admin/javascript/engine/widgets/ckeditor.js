/* --------------------------------------------------------------
 ckeditor.js 2016-03-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## CKEditor Widget
 *
 * Use this widget on a parent container to convert all the textareas with the "wysiwyg" class into 
 * CKEditor instances at once. 
 * 
 * Official CKEditor Website: {@link http://ckeditor.com}
 * 
 * ### Options 
 * 
 * **File Browser URL | `data-ckeditor-filebrowser-browse-url` | String | Optional**
 * 
 * Provide the default URL of the file browser that is integrated within the CKEditor instance. The default
 * value points is 'includes/ckeditor/filemanager/index.html'.
 * 
 * **Base URL | `data-ckeditor-base-href` | String | Optional** 
 * 
 * The base URL of the CKEditor instance. The default value points to the `http://shop.de/admin` directory.
 * 
 * **Enter Mode | `data-ckeditor-enter-mode` | Number | Optional**
 * 
 * Define the enter mode of the CKEditor instance. The default value of this option is CKEDITOR.ENTER_BR which
 * means that the editor will use the `<br>` element for every line break. For a list of possible values visit 
 * this [CKEditor API reference page](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.enterMode).
 * 
 * **Shift Enter Mode | `data-ckeditor-shift-enter-mode` | Number| Optional**
 * 
 * Define the shift-enter mode of the CKEditor instance. The default value of this option is CKEDITOR.ENTER_P which
 * means that the editor will use the `<p>` element for every line break. For a list of possible values visit this
 * [CKEditor API reference page](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.shiftEnterMode).
 * 
 * **Language Code | `data-ckeditor-language` | String | Optional**
 * 
 * Provide a language code for the CKEditor instance. The default value comes from the 
 * `jse.core.config.get('languageCode')` value which has the active language setting of the current page. 
 * 
 * ### Example
 * 
 * When the page loads the textarea element will be converted into a CKEditor instance.
 * 
 * ```html
 * <div data-gx-widget="ckeditor"> 
 *   <textarea class="wysiwyg"></textarea>
 * </div>    
 * ```
 *
 * @module Admin/Widgets/ckeditor
 * @requires CKEditor-Library
 * 
 * @todo Replace the "wysiwyg" class with a simple "convert-to-ckeditor" class which is easier to remember.
 */
gx.widgets.module(
	'ckeditor',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Widget Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = { // Configuration gets passed to the ckeditor.
				'filebrowserBrowseUrl': 'includes/ckeditor/filemanager/index.html',
				'baseHref': jse.core.config.get('appUrl') + '/admin',
				'enterMode': CKEDITOR.ENTER_BR,
				'shiftEnterMode': CKEDITOR.ENTER_P,
				'language': jse.core.config.get('languageCode'),
				'useRelPath': true
			},
			
			/**
			 * Final Widget Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {},
			
			/**
			 * Editors Selector Object
			 *
			 * @type {object}
			 */
			$editors = null;
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			if (!options.useRelPath) {
				options.filebrowserBrowseUrl += '?mode=mail';
			}
			
			$editors = $this
				.filter('.wysiwyg')
				.add($this.find('.wysiwyg'));
			
			$editors
				.each(function() {
					var $self = $(this),
						dataset = $.extend({}, options, $self.data()), // Get textarea specific configuration.
						name = $self.attr('name');
					$self.removeClass('wysiwyg');
					CKEDITOR.replace(name, dataset);
				});
			
			// Event handler for the update event, which is updating the ckeditor with the value
			// of the textarea.
			$this.on('ckeditor.update', function() {
				$editors
					.each(function() {
						var $self = $(this),
							name = $self.attr('name'),
							editor = (CKEDITOR) ? CKEDITOR.instances[name] : null;
						
						if (editor) {
							editor.setData($self.val());
						}
					});
			});
			
			$this.trigger('widget.initialized', 'ckeditor');
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
