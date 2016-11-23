/* --------------------------------------------------------------
 link_crypter.js 2016-02-02 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that replaces the href-attributes of links with the given
 * data if the element gets in focus / hover state. Additionally
 * it is possible to remove every X sign for decryption
 */
gambio.widgets.module('link_crypter', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		defaults = {
			decrypt: true, // If true, it uses the period option to decrypt the links
			period: 3 // Remove every X sign of the data given for the url
		},
		options = $.extend(true, {}, defaults, data),
		module = {};


// ########## EVENT HANDLER ##########


	/**
	 * Function to replace the href value
	 * with the URL or a # (depending on
	 * the focus / hover state). Additionally
	 * it does some decrypting optionally.
	 * @param       {object}    e   jQuery-event-object which contains as data the focus state
	 * @private
	 */
	var _switchUrl = function(e) {
		var $self = $(this),
			url = $(this).parseModuleData('link_crypter').url;

		if (url) {
			if (e.data.in) {
				// Simple decryption functionality. It removes every x. sign inside the URL. 
				// x is given by options.period
				if (options.decrypt) {
					var decryptedUrl = '';
					for (var i = 0; i < url.length; i++) {
						if (i % options.period) {
							decryptedUrl += url.charAt(i);
						}
					}
					url = decryptedUrl; 
				}
				$self.attr('href', url);
			} else {
				$self.attr('href', '#');
			}
		}
	};

// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {
		$this
			.on('mouseenter focus', 'a', {in: true}, _switchUrl)
			.on('mouseleave blur', 'a', {in: false}, _switchUrl);

		done();
	};

	// Return data to widget engine
	return module;
});
