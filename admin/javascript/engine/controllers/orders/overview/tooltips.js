/* --------------------------------------------------------------
 tooltip.js 2016-09-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Orders Table Tooltip
 *
 * This controller displays tooltips for the orders overview table. The tooltips are loaded after the
 * table data request is ready for optimization purposes.
 */
gx.controllers.module(
	'tooltips',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES
		// ------------------------------------------------------------------------
		
		/**
		 * Module Selector
		 *
		 * @var {jQuery}
		 */
		const $this = $(this);
		
		/**
		 * Default Options
		 *
		 * @type {Object}
		 */
		const defaults = {
			sourceUrl: jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/Tooltips',
			selectors: {
				mouseenter: {
					orderItems: '.tooltip-order-items',
					customerMemos: '.tooltip-customer-memos',
					customerAddresses: '.tooltip-customer-addresses',
					orderSumBlock: '.tooltip-order-sum-block',
					orderStatusHistory: '.tooltip-order-status-history',
					orderComment: '.tooltip-order-comment',
				},
				click: {
					trackingLinks: '.tooltip-tracking-links'	
				}
			}
		};
		
		/**
		 * Final Options
		 *
		 * @var {Object}
		 */
		const options = $.extend(true, {}, defaults, data);
		
		/**
		 * Module Object
		 *
		 * @type {Object}
		 */
		const module = {};
		
		/**
		 * Tooltip Contents
		 *
		 * Contains the rendered HTML of the tooltips. The HTML is rendered with each table draw.
		 *
		 * e.g. tooltips.400210.orderItems >> HTML for order items tooltip of order #400210.
		 *
		 * @type {Object}
		 */
		let tooltips = [];
		
		/**
		 * DataTables XHR Parameters
		 *
		 * The same parameters used for fetching the table data need to be used for fetching the tooltips.
		 *
		 * @type {Object}
		 */
		let datatablesXhrParameters;
		
		// ------------------------------------------------------------------------
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Get Target Position
		 *
		 * @param {jQuery} $target
		 *
		 * @return {String}
		 */
		function _getTargetPosition($target) {
			const horizontal = $target.offset().left - $(window).scrollLeft() > $(window).width() / 2
					? 'left'
					: 'right';
			const vertical = $target.offset().top - $(window).scrollTop() > $(window).height() / 2
					? 'top'
					: 'bottom';
			
			return horizontal + ' ' + vertical;
		}
		
		/**
		 * Get Tooltip Position
		 *
		 * @param {jQuery} $target
		 *
		 * @return {String}
		 */
		function _getTooltipPosition($target) {
			const horizontal = $target.offset().left - $(window).scrollLeft() > $(window).width() / 2
					? 'right'
					: 'left';
			const vertical = $target.offset().top - $(window).scrollTop() > $(window).height() / 2
					? 'bottom'
					: 'top';
			
			return horizontal + ' ' + vertical;
		}
		
		/**
		 * If there is only one link then open it in a new tab. 
		 */
		function _onTrackingLinksClick() {
			const trackingLinks = $(this).parents('tr').data('trackingLinks'); 
			
			if (trackingLinks.length === 1) {
				window.open(trackingLinks[0], '_blank');
			}
		}
		
		/**
		 * Initialize tooltip for static table data.
		 *
		 * Replaces the browsers default tooltip with a qTip instance for every element on the table which has
		 * a title attribute.
		 */
		function _initTooltipsForStaticContent() {
			$this.find('tbody [title]').qtip({
				style: {classes: 'gx-qtip info'}
			});
		}
		
		/**
		 * Show Tooltip
		 *
		 * Display the Qtip instance of the target. The tooltip contents are fetched after the table request
		 * is finished for performance reasons. This method will not show anything until the tooltip contents
		 * are fetched.
		 *
		 * @param {jQuery.Event} event
		 */
		function _showTooltip(event) {
			event.stopPropagation();
			
			const orderId = $(this).parents('tr').data('id');
			
			if (!tooltips[orderId]) {
				return; // The requested tooltip is not loaded, do not continue.
			}
			
			const tooltipPosition = _getTooltipPosition($(this));
			const targetPosition = _getTargetPosition($(this));
			
			$(this).qtip({
				content: tooltips[orderId][event.data.name],
				style: {
					classes: 'gx-qtip info'
				},
				position: {
					my: tooltipPosition,
					at: targetPosition,
					effect: false,
					viewport: $(window),
					adjust: {
						method: 'none shift'
					}
				},
				hide: {
					fixed: true,
					delay: 300
				},
				show: {
					ready: true,
					delay: 100
				},
				events: {
					hidden: (event, api) => {
						api.destroy(true);
					}
				}
			});
		}
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this
				.on('draw.dt', _initTooltipsForStaticContent)
				.on('preXhr.dt', (event, settings, json) => datatablesXhrParameters = json)
				.on('xhr.dt', () => $.post(options.sourceUrl, datatablesXhrParameters,
					response => tooltips = response, 'json'))
				.on('click', '.tooltip-tracking-links', _onTrackingLinksClick); 
			
			for (let event in options.selectors) {
				for (let name in options.selectors[event]) {
					$this.on(event, options.selectors[event][name], {name}, _showTooltip);
				}	
			}
			
			done();
		};
		
		return module;
	}
);