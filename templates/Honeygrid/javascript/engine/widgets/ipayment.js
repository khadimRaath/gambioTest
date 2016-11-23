/* --------------------------------------------------------------
	ipayment.js 2016-06-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('ipayment', [], function(data) {
	'use strict';

	// ########## VARIABLE INITIALIZATION ##########
	var $this = $(this),
		defaults = {
		},
		options = $.extend(true, {}, defaults, data),
		module = {};

	// ########## INITIALIZATION ##########

	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {
		$('#ipayment_form').submit();
		done();
	};

	// Return data to widget engine
	return module;
});
