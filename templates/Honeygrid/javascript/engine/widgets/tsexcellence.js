/* --------------------------------------------------------------
	tsexcellence.js 2016-09-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('tsexcellence', [], function(data) {

	'use strict';

// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
		defaults = {
		},
		options = $.extend(true, {}, defaults, data),
		module = {};

	module.init = function(done) {
		$('button#remove_tsbp').on('click', function(e) {
			e.preventDefault();
			$.ajax({
				"data": {
					"remove_tsbp": "true",
				},
				"url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
				"type": "POST"
			}).done(function(data) {
				window.location = window.location;
			});
		});
		$('button#add_tsbp').on('click', function(e) {
			e.preventDefault();
			$.ajax({
				"data": {
					"add_tsbp": "true",
					"amount": $("input[name=tsbp_amount]").val()
				},
				"url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
				"type": "POST"
			}).done(function(data) {
				window.location = window.location;
			});
		});
		done();
	};

	// Return data to widget engine
	return module;
});
