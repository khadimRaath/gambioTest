/* --------------------------------------------------------------
 core_workarounds.js 2015-08-05 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Core Workarounds Module
 *
 * This file contains workarounds that do not belong in other JS modules.
 */
gambio.widgets.module('core_workarounds', [], function() {

	'use strict';

	var module = {};

	var _initMobileMenu = function() {
		var $profile = $('#topbar-container nav > ul> li').clone(),
			$login = $profile.find('.login-off-item'),
			$loginClone = $login.clone();

		$loginClone.addClass('dropdown navbar-topbar-item');
		$login.remove();
		$profile = $profile.add($loginClone);

		$('#categories nav > ul').append($profile);
		$('#categories nav > ul').attr('data-gambio-widget', 'link_crypter');  //reinitialize widgets
		gambio.widgets.init($('#categories nav > ul'));

		var $verticalMenu = $('.navbar-categories-left');
		if ($verticalMenu.length > 0) {
			$verticalMenu.find('ul.level-1').append($profile.clone());

			$verticalMenu.find('ul.level-1').attr('data-gambio-widget', 'link_crypter');
			gambio.widgets.init($verticalMenu.find('ul.level-1'));

			// hide the new elements
			$verticalMenu.find('.navbar-topbar-item').hide();
		}
	};


	/**
	 * Init function of the widget
	 * @constructor
	 */
	module.init = function(done) {
		_initMobileMenu();

		done();
	};

	return module;
});
