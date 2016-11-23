/* --------------------------------------------------------------
 responsive.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.responsive = jse.libs.template.responsive || {};

/**
 * ## Honeygrid Responsive Utilities Library
 *
 * Library to make the template responsive. This function depends on jQuery.
 *
 * @module Honeygrid/Libs/responsive
 * @exports jse.libs.template.responsive
 */
(function(exports) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $body = $('body'),
		current = null,
		timer = null,
		breakpoints = [
			{
				id: 20,
				name: 'too small',
				width: 480
			},
			{
				id: 40,
				name: 'xs',
				width: 768
			},
			{
				id: 60,
				name: 'sm',
				width: 992
			},
			{
				id: 80,
				name: 'md',
				width: 1200
			},
			{
				id: 100,
				name: 'lg',
				width: null
			}
		];


	// ########## EVENT HANDLER ##########

	/**
	 * Returns the breakpoint of the current page, 
	 * false if no breakpoint could be identified.
	 *
	 * @return Breakpoint
	 */
	var _getBreakpoint = function() {
		var width = window.innerWidth,
			result = null;

		// check if page is loaded inside an iframe and, if appropriate, set the iframe's width
		if (window.self !== window.top) {
			document.body.style.overflow = 'hidden';
			width = document.body.clientWidth;
			document.body.style.overflow = 'visible';
		}
		
		if (width === 0) {
			timer = setTimeout(function() {
				_getBreakpoint();
			}, 10);
			current = $.extend({}, breakpoints[0]); // set default breakpoint value
			return false;
		}

		$.each(breakpoints, function(i, v) {
			if (!v.width || width < v.width) {

				result = $.extend({}, v);
				return false;
			}
		});


		if (result && (!current || current.id !== result.id)) {
			current = $.extend({}, result);
			clearTimeout(timer);
			timer = setTimeout(function() {
				// @todo This lib depends on the existence of the events lib (both are loaded asynchronously).
				if (jse.libs.template.events !== undefined) {
					$body.trigger(jse.libs.template.events.BREAKPOINT(), current);
				}
			}, 10);
		}
	};


	// ########## INITIALIZATION ##########

	_getBreakpoint();

	$(window).on('resize', _getBreakpoint);

	/**
	 * @todo rename method to "getBreakpoint".
	 */
	exports.breakpoint = function() {
		return current;
	};

}(jse.libs.template.responsive));