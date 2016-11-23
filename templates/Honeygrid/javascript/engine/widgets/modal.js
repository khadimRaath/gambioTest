/* --------------------------------------------------------------
 modal.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that opens a modal layer with the URL given by
 * an a-tag that has the class "js-open-modal". For backwards
 * compatibility the class "lightbox_iframe" is possible, also.
 */
gambio.widgets.module(
	'modal',

	[
		gambio.source + '/libs/modal.ext-magnific',
		gambio.source + '/libs/modal'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {
				add: '&lightbox_mode=1',   // Add this parameter to each URL
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

// ########## EVENT HANDLER ##########

		/**
		 * Event handler to open the modal
		 * window with the link data
		 * @param       {object}    e       jQuery event object
		 * @private
		 */
		var _openModal = function(e) {
			e.preventDefault();

			var $self = $(this),
				url = $self.attr('href'),
				dataset = $self.parseModuleData('modal'),
				type = dataset.type || e.data.type,
				settings = $.extend({}, dataset.settings || {});

			url += (url[0] === '#' || url[0] === '.') ? '' : options.add;
			settings.template = url;

			jse.libs.template.modal[type](settings);
			if (dataset.finishEvent) {
				$('body').trigger(dataset.finishEvent);
			}
		};

// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$this
				.on('click', '.js-open-modal', _openModal)
				.on('click', '.lightbox_iframe', {type: 'iframe'}, _openModal);

			done();
		};

		// Return data to widget engine
		return module;
	});
