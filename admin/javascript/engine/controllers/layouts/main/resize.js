/* --------------------------------------------------------------
 resize.js 2016-05-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Resize Layout Controller
 *
 * During the admin layout lifecycle there are events that will change the size of the document (not the window!)
 * and the layout must react to them. This controller will make sure that the layout will remain stable after such
 * changes are marked with the "data-resize-layout" attribute as in the following example.
 *
 * ```html
 * <!-- DataTable Instance -->
 * <table data-gx-widget="datatable" data-resize-layout="draw.dt">
 *   ...
 * </table>
 * ```
 *
 * After a table draw is performed, it is possible that there will be more rows to be displayed and thus the
 * #main-content element gets bigger. Once the datatable "draw.dt" event is executed this module will make
 * sure that the layout remains solid.
 *
 * The event must bubble up to the container this module is bound.
 *
 * ### Dynamic Elements
 *
 * It is possible that during the page lifecycle there will be dynamic elements that will need to register
 * an the "resize-layout" event. In this case apply the "data-resize-layout" attribute in the dynamic
 * element and trigger the "resize:bind" event from that element. The event must bubble up to the layout
 * container which will then register the dynamic elements.
 */
gx.controllers.module('resize', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Marks event listeners.
	 *
	 * @type {string}
	 */
	const ATTRIBUTE_NAME = 'data-resize-layout';
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	/**
	 * Main Header Selector
	 *
	 * @type {jQuery}
	 */
	const $mainHeader = $('#main-header');
	
	/**
	 * Main Menu Selector
	 *
	 * @type {jQuery}
	 */
	const $mainMenu = $('#main-menu');
	
	/**
	 * Main Footer Selector
	 *
	 * @type {jQuery}
	 */
	const $mainFooter = $('#main-footer');
	
	/**
	 * Main Footer Info
	 *
	 * @type {jQuery}
	 */
	const $mainFooterInfo = $mainFooter.find('.info');
	
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Bind resize events.
	 */
	function _bindResizeEvents() {
		$this.find(`[${ATTRIBUTE_NAME}]`).each(function() {
			let event = $(this).attr(ATTRIBUTE_NAME);
			$(this)
				.removeAttr(ATTRIBUTE_NAME)
				.on(event, _updateLayoutComponents);
		});
	}
	
	/**
	 * Give initial min height to main menu.
	 */
	function _updateLayoutComponents() {
		const mainMenuHeight = window.innerHeight - $mainHeader.outerHeight() - $mainFooterInfo.outerHeight();
		$mainMenu.css('min-height', mainMenuHeight);
		_setFooterInfoPosition();
	}
	
	/**
	 * Calculate the correct footer info position.
	 */
	function _setFooterInfoPosition() {
		if (($(document).scrollTop() + window.innerHeight - $mainFooterInfo.outerHeight()) < $mainFooter.offset().top) {
			$mainFooter.addClass('fixed');
		} else if ($mainFooterInfo.offset().top + $mainFooterInfo.height() >= $mainFooter.offset().top) {
			$mainFooter.removeClass('fixed');
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$(window)
			.on('resize', _updateLayoutComponents)
			.on('JSENGINE_INIT_FINISHED', _updateLayoutComponents)
			.on('scroll', _setFooterInfoPosition)
			.on('register:bind', _bindResizeEvents);
		
		_bindResizeEvents();
		
		done();
	};
	
	return module;
});
