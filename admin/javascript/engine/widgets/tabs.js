/* --------------------------------------------------------------
 tabs.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tabs Widget
 *
 * This widget is a custom implementation of tabs functionality and must not be confused with
 * jQueryUI's tab widget.
 * 
 * The actual `<div>` which contains the tabs, has to have a CSS-Class named **tab-headline-wrapper**.
 * The tabs will be identified by this CSS-Class. The content of the tabs has to be in a `<div>` which has to have
 * a CSS-Class called **tab-content-wrapper**. The elements inside, have to be in the same order as the tabs.
 *
 * ### Example
 *
 * ```html
 * <div data-gx-widget="tabs">
 *   <!-- Tabs -->
 *   <div class="tab-headline-wrapper">
 *     <a href="#tab1">Tab #1</a>
 *     <a href="#tab2">Tab #2</a>
 *   </div>
 *   
 *   <!-- Content -->
 *   <div class="tab-content-wrapper">
 *     <div>Content of tab #1.</div>
 *     <div>Content of tab #2.</div>
 *   </div>
 * </div>
 * ```
 *
 * @module Admin/Widgets/tabs
 */
gx.widgets.module(
	'tabs',
	
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
			 * Headline Tags Selector
			 *
			 * @type {object}
			 */
			$headlineTags = null,
			
			/**
			 * Content Tags Selector
			 *
			 * @type {object}
			 */
			$contentTags = null,
			
			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {},
			
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Click handler for the tabs onClick the content gets switched.
		 *
		 * @param {object} event jQuery event object contains information of the event.
		 */
		var _clickHandler = function(event) {
			event.preventDefault();
			event.stopPropagation();
			
			$headlineTags.removeClass('active');
			
			var index = $(this)
				.addClass('active')
				.index();
			
			$contentTags
				.hide()
				.eq(index)
				.show();
		};
		
		/**
		 * Handles external "show" event
		 *
		 * @param {object} event jQuery event object contains information of the event.
		 * @param {number} tab index to show
		 */
		var _showHandler = function(event, index) {
			event.preventDefault();
			event.stopPropagation();
			$headlineTags.eq(index).trigger('click');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZE
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			$headlineTags = $this
				.children('.tab-headline-wrapper')
				.children('a');
			
			$contentTags = $this
				.children('.tab-content-wrapper')
				.children('div');
			
			$this.addClass('ui-tabs');
			$this.on('click', '.tab-headline-wrapper > a', _clickHandler);
			$this.on('show:tab', _showHandler);
			
			// Set first tab as selected.
			$headlineTags
				.eq(0)
				.trigger('click');
			
			done();
		};
		
		// Return data to module engine
		return module;
	});
