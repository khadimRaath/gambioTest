/* --------------------------------------------------------------
 datatable_fixed_header.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Fixed DataTable Header
 *
 * The table header will remain in the viewport as the user scrolls down the page. The style change of this
 * module is a bit tricky because we need to remove the thead from the normal flow, something that breaks the
 * display of the table. Therefore a helper clone of the thead is used to maintain the table formatting.
 *
 * **Notice #1**: The .table-fixed-header class is styled by the _tables.scss and is part of this solution.
 *
 * **Notice #2**: This method will take into concern the .content-header element which shouldn't overlap the
 * table header.
 *
 * @module Admin/Extensions/datatable_fixed_header
 */
gx.extensions.module('datatable_fixed_header', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Table Header Selector
	 *
	 * @type {jQuery}
	 */
	const $thead = $this.children('thead');
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	/**
	 * Marks the end of the table.
	 *
	 * This value is used to stop the fixed header when the user reaches the end of the table.
	 *
	 * @type {Number}
	 */
	let tableOffsetBottom;
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * On DataTable Draw Event
	 *
	 * Re-calculate the table bottom offset value.
	 */
	function _onDataTableDraw() {
		tableOffsetBottom = $this.offset().top + $this.height() - $thead.height();
	}
	
	/**
	 * On DataTable Initialization
	 *
	 * Modify the table HTML and set the required event handling for the fixed header functionality.
	 */
	function _onDataTableInit() {
		const $mainHeader = $('#main-header');
		const $contentHeader = $('.content-header');
		const $clone = $thead.clone();
		const originalTop = $thead.offset().top;
		let isFixed = false;
		let rollingAnimationInterval = null;
		
		$clone
			.hide()
			.addClass('table-fixed-header-helper')
			.prependTo($this);
		
		$(window)
			.on('scroll', function() {
				const scrollTop = $(window).scrollTop();
				
				if (!isFixed && scrollTop + $mainHeader.outerHeight() > originalTop) {
					$this.addClass('table-fixed-header');
					$thead
						.outerWidth($this.outerWidth())
						.addClass('fixed');
					$clone.show();
					isFixed = true;
				} else if (isFixed && scrollTop + $mainHeader.outerHeight() < originalTop) {
					$this.removeClass('table-fixed-header');
					$thead
						.outerWidth('')
						.removeClass('fixed');
					$clone.hide();
					isFixed = false;
				}
				
				if (scrollTop >= tableOffsetBottom) {
					$thead.removeClass('fixed');
				} else if ($(window).scrollTop() < tableOffsetBottom && !$thead.hasClass('fixed')) {
					$thead.addClass('fixed');
				}
			})
			.on('content_header:roll_in', function() {
				rollingAnimationInterval = setInterval(() => {
					$thead.css('top', $contentHeader.position().top + $contentHeader.outerHeight());
					if ($contentHeader.hasClass('fixed')) {
						clearInterval(rollingAnimationInterval);
					}
				}, 1);
			})
			.on('content_header:roll_out', function() {
				clearInterval(rollingAnimationInterval);
				$thead.css('top', $mainHeader.outerHeight());
			});
	}
	
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this
			.on('draw.dt', _onDataTableDraw)
			.on('init.dt', _onDataTableInit);
		
		done();
	};
	
	return module;
	
});