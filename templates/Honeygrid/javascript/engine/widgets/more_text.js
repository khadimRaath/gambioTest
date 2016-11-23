/* --------------------------------------------------------------
 more_text.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Enables the 'more' or '...' buttons in long text fields.
 */
gambio.widgets.module(
	'more_text',

	[
		gambio.source + '/libs/events'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			transition = {
				classClose: 'hide',
				open: true,
				calcHeight: true
			},
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## EVENT HANDLER ##########

		/**
		 * Event handler for the click event on the '...'-more
		 * button. It starts the transition to open the full
		 * text
		 * @param       {object}    e       jQuery event object
		 * @private
		 */
		var _openText = function(e) {
			e.preventDefault();

			var $self = $(this),
				$container = $self.closest('.more-text-container'),
				$fullText = $container.children('.more-text-full');

			$self.hide();
			$fullText.trigger(jse.libs.template.events.TRANSITION(), transition);
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$this.on('click', '.more-text-container .more-text-link', _openText);

			done();
		};

		// Return data to widget engine
		return module;
	});
