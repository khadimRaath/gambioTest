/* --------------------------------------------------------------
 anchor.js 2015-10-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('anchor', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		defaults = {
			offset: 80,     // Offset in px from top (to prevent the header is hiding an element)
			duration: 300     // Scroll duration in ms
		},
		options = $.extend(true, {}, defaults, data),
		module = {};

// ########## EVENT HANDLER ##########

	/**
	 * Handler for the click on an anchor
	 * link. It calculates the position of
	 * the target and scroll @ that position
	 * @param       {object}        e           jQuery event object
	 * @private
	 */
	var _anchorHandler = function(e) {
		var $self = $(this),
			$target = null,
			link = $self.attr('href'),
			position = null;

		// Only react if the link is an anchor
		if (link && link.indexOf('#') === 0) {
			e.preventDefault();
			e.stopPropagation();

			$target = $(link);

			if ($target.length) {
				position = $target
					.offset()
					.top;

				$('html, body').animate({scrollTop: position - options.offset}, options.duration);
			}
		}
	};

// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {
		$this.on('click', 'a:not(.js-open-modal)', _anchorHandler);
		done();
	};

	// Return data to widget engine
	return module;
});