/* --------------------------------------------------------------
 visibility_switcher.js 2015-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Visibility Switcher Extension
 * 
 * Use this extension in a parent element to easily define the visibility of child elements during the 
 * mouse hover of their containers. When the "mouseleave" event is triggered the children will be hidden.
 * 
 * ### Options 
 * 
 * **Rows | data-visibility_switcher-rows | String | Required**
 *
 * Provide a jQuery selector string which points to the elements that have the "hover" event. 
 * 
 * **Selections | data-visibility_switcher-selections | String | Required** 
 * 
 * Provide a jQuery selector string which points to the elements to be displayed upon the "hover" event.
 * 
 * ### Example 
 * 
 * In the following example the .row-action elements will be visible whenever the user hovers above of the 
 * `<tr>` element. The initial state of the elements must be hidden (thus the 'hidden' class).
 * 
 * ```html
 * <table data-gx-extension="visibility_switcher" 
 *       data-visibility_switcher-rows="tr.row" 
 *       data-visibility_switcher-selections="i.row-action"> 
 *   <tr class="row">
 *     <td>#1</td>
 *     <td>John Doe</td>
 *     <td>
 *       <i class="fa fa-pencil row-action edit hidden"></i>
 *       <i class="fa fa-trash row-action delete hidden"></i>
 *     </td>
 *   </tr>
 * </table>
 * ```
 * 
 * *Whenever the user hovers at the table rows the .row-action elements will be visible and whenever the 
 * mouse leaves the rows they will be hidden.*
 *
 * @module Admin/Extensions/visibility_switcher
 */
gx.extensions.module(
	'visibility_switcher',
	
	[],
	
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
			 * 
			 * @todo Rename 'rows' option to 'containerSelector' and 'selections' to 'childrenSelector'.
			 */
			defaults = {
				'rows': '.visibility_switcher', 
				'selections': '.tooltip-icon'
			},
			
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
		
		var _visibility = function(e) {
			var $self = $(this);
			$self
				.filter(options.selections)
				.add($self.find(options.selections))
				.css('visibility', e.data.state);
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			$this
				.on('mouseenter', options.rows, {'state': 'visible'}, _visibility)
				.on('mouseleave', options.rows, {'state': 'hidden'}, _visibility);
			
			$this
				.find(options.rows + ' ' + options.selections)
				.css('visibility', 'hidden');
			
			done();
			
		};
		
		return module;
	});
