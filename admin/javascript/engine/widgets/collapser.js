/* --------------------------------------------------------------
 collapser.js 2016-02-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Collapser Widget
 *
 * This widget expands or collapses the target element. It is not visible when collapsed but visible when expanded.
 *
 * ### Options
 *
 * **Collapsed | `data-collapser-collapsed` | Boolean | Optional**
 *
 * Default state of the collapser. If no value is provided, it defaults to `false`.
 *
 * **Collapsed Icon Class | `data-collapser-collapsed_icon_class` | String | Optional**
 *
 * Default Font Awesome icon when the collapser is collapsed. If no value is provided, it defaults 
 * to **'fa-plus-square-o'**.
 * 
 * **Expanded Icon Class | `data-collapser-expanded_icon_class` | String | Optional**
 *
 * Default Font Awesome icon when the collapser is expanded. If no value is provided, it defaults 
 * to **'fa-minus-square-o'**.
 *
 * **Additional Classes | `data-collapser-additional_classes` | String | Optional**
 *
 * Provide additional CSS-Classes which should be added. If no value is provided, it defaults to **'pull-right'**, 
 * which applies a CSS *'float: right'* style.
 *
 * **Target Selector | `data-collapser-target_selector` | String | Required**
 *
 * Provide the target selector, which is the element thath will be collapsed or expanded. 
 *
 * **Parent Selector | `data-collapser-parent_selector` | String | Optional**
 *
 * Provide a parent selector for the collapser. It's empty by default. 
 * 
 * ### Example
 *
 * When the page loads, the **collapser** widget will be added to the `<div>` element.
 * On click, the target selector will be shown or hidden.
 *
 * ```html
 *  <div class="headline-wrapper"
 *      data-gx-widget="collapser"
 *      data-collapser-target_selector=".content-wrapper"
 *      data-collapser-section="category_base_data"
 *      data-collapser-user_id="1"
 *      data-collapser-collapsed="true">
 *    Click This Headline
 *  </div>
 *  <div class="content-wrapper">
 *    Toggled content
 *  </div>
 * ```
 *
 * @module Admin/Widgets/collapser
 * 
 * @todo Make the styling for this widget (like it is on the products site) more general. Currently, the "div" element
 * has to be wrapped in another "div" with specific CSS-Classes like ".gx-container" and ".frame-head".
 */
gx.widgets.module(
	'collapser',
	
	['user_configuration_service'],
	
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
			 * UserConfigurationService Alias
			 *
			 * @type {object}
			 */
			userConfigurationService = jse.libs.user_configuration_service,
			
			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {
				collapsed: false,
				collapsed_icon_class: 'fa-plus-square-o',
				expanded_icon_class: 'fa-minus-square-o',
				additional_classes: 'pull-right',
				parent_selector: ''
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
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Sets the cursor to pointer
		 * @private
		 */
		var _setMouseCursorPointer = function() {
			$this.addClass('cursor-pointer').children().addClass('cursor-pointer');
		};
		
		/**
		 * Sets the initial visibility according to the 'collapsed' value
		 * @private
		 */
		var _setInitialVisibilityState = function() {
			if (options.collapsed) {
				if (options.parent_selector) {
					$this.parents(options.parent_selector).next(options.target_selector).hide();
				}
				else {
					$this.next(options.target_selector).hide();
				}
			}
		};
		
		/**
		 * Creates the markup for the collapser and adds the click event handler
		 * @private
		 */
		var _createCollapser = function() {
			$this.append(
				$('<span></span>').addClass('collapser').addClass(options.additional_classes).append(
					$('<i></i>').addClass('fa').addClass(options.collapsed ? options.collapsed_icon_class :
					                                     options.expanded_icon_class)
				)
			).on('click', _toggleVisibilityState);
		};

		/**
		 * Saves the current visibility state.
		 *
		 * @private
		 */
		var _saveVisibilityState = function() {
			var collapseState = $this.find('.collapser > i.fa').hasClass(options.collapsed_icon_class);
			
			userConfigurationService.set({
				data: {
					userId: options.user_id,
					configurationKey: options.section + '_collapse',
					configurationValue: collapseState
				}
			});
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Toggles the visibility state and switches between plus and minus icon.
		 *
		 * @private
		 */
		var _toggleVisibilityState = function() {
			if (options.parent_selector) {
				$this.parents(options.parent_selector).next(options.target_selector).toggle();
			}
			else {
				$this.next(options.target_selector).toggle();
			}
			
			$this.find('.collapser > i.fa').toggleClass(options.collapsed_icon_class);
			$this.find('.collapser > i.fa').toggleClass(options.expanded_icon_class);

			
			_saveVisibilityState();
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZE
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			_setMouseCursorPointer();
			_setInitialVisibilityState();
			_createCollapser();
			done();
		};
		
		// Return data to module engine
		return module;
	});
