/* --------------------------------------------------------------
 toolbar_icons.js 2015-09-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Toolbar Icons Extension
 *
 * This extension will search for specific-class elements inside a container and will prepend them with
 * a new `<i>` element that has the corresponding FontAwesome icon. By doing so you can dynamically inject
 * icons into existing toolbar items by setting the required classes.
 * 
 * In the following list you can see the relations between the classes and their icons: 
 * 
 * - btn-edit: [fa-pencil](http://fortawesome.github.io/Font-Awesome/icon/pencil)
 * - btn-editdoc: [fa-pencil](http://fortawesome.github.io/Font-Awesome/icon/pencil)
 * - btn-view: [fa-eye](http://fortawesome.github.io/Font-Awesome/icon/eye)
 * - btn-delete: [fa-trash-o](http://fortawesome.github.io/Font-Awesome/icon/trash-o)
 * - btn-order: [fa-shopping-cart](http://fortawesome.github.io/Font-Awesome/icon/shopping-cart)
 * - btn-caret: [fa-caret-right](http://fortawesome.github.io/Font-Awesome/icon/caret-right)
 * - btn-folder: [fa-folder-open](http://fortawesome.github.io/Font-Awesome/icon/folder)
 * - btn-multi-action: [fa-check-square-o](http://fortawesome.github.io/Font-Awesome/icon/check-square-o)
 * - btn-cash: [fa-money](http://fortawesome.github.io/Font-Awesome/icon/money)
 * - btn-add: [fa-plus](http://fortawesome.github.io/Font-Awesome/icon/plus)
 * 
 * ### Options
 *
 * The extension contains additional options that can be used to modify the display of the icons. You can
 * use them together at the same time.
 *
 * **Large Icons | `data-toolbar_icons-large` | Boolean | Optional**
 *
 * This option will add the "fa-lg" class to the icons which will make them bigger.
 *
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons" data-toolbar_icons-large="true">
 *   <button class="btn-edit"></button>
 * </div>
 * ```
 *
 * **Fixed Width | `data-toolbar_icons-fixedwidth` | Boolean | Optional**
 *
 * This option will add the "fa-fw" class to the icons which will keep the icon width fixed.
 *
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons" data-toolbar_icons-fixedwidth="true">
 *   <button class="btn-view"></button>
 * </div>
 * ```
 * 
 * ### Example
 * 
 * After the engine is initialized the following button elements will contain the corresponding FontAwesome icons.
 * 
 * ```html
 * <div class="container" data-gx-extension="toolbar_icons"> 
 *   <button class="btn-edit">&amp;nbsp;Edit</button>    
 *   <button class="btn-view">&amp;nbsp;View</button>    
 *   <button class="btn-order">&amp;nbsp;Buy Item</button>    
 * </div>
 * ```
 * 
 * *Note that the use of **&amp;nbsp;** is required only if you want to add some space between the icon and the 
 * text. You can avoid it by styling the margin space between the icon and the text.* 
 * 
 * FontAwesome provides many helper classes that can be used directly on the elements in order to adjust the
 * final visual result. Visit the follow link for more examples and sample code.
 * {@link https://fortawesome.github.io/Font-Awesome/examples}
 *  
 * @module Admin/Extensions/toolbar_icons
 */
gx.extensions.module(
	'toolbar_icons',
	
	[],
	
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
			 * Default Options for Extension
			 *
			 * @type {object}
			 * 
			 * @todo Add default values to the extension. 
			 */
			defaults = {},
			
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
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function(done) {
			
			// Define class names and the respective Font-Awesome classes here
			// @todo The selectors must be dynamic, move these to the "defaults.selectors" property.
			var classes = {
				'.btn-edit': 'fa-pencil',
				'.btn-view': 'fa-eye',
				'.btn-editdoc': 'fa-pencil',
				'.btn-delete': 'fa-trash-o',
				'.btn-order': 'fa-shopping-cart',
				'.btn-caret': 'fa-caret-right',
				'.btn-folder': 'fa-folder-open',
				'.btn-multi-action': 'fa-check-square-o',
				'.btn-cash': 'fa-money',
				'.btn-add': 'fa-plus'
			};
			
			// Let's rock
			$.each(classes, function(key, value) {
				var composedClassName = [
					value,
					(options.large ? ' fa-lg' : ''),
					// @todo "fixedwidth" must be CamelCase or underscore_separated.
					(options.fixedwidth ? ' fa-fw' : '') 
				].join('');
				
				var $tag = $('<i class="fa ' + composedClassName + '"></i>');
				$this.find(key).prepend($tag);
			});
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
