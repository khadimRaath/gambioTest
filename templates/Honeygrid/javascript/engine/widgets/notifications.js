/* --------------------------------------------------------------
 notifications.js 2016-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Top-Bar- and the Pop-Up-Notification
 */
gambio.widgets.module(
	'notifications',

	[],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			initialMarginTop = '0',
			defaults = {
				outerWrapperSelector: '#outer-wrapper',
				headerSelector: '#header'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## EVENT HANDLER ##########

		var _topBarPositioning = function() {
			var topBarHeight = $('.topbar-notification').outerHeight();
			
			topBarHeight += parseInt(initialMarginTop.replace('px', ''));
			
			$(options.outerWrapperSelector).css('margin-top', topBarHeight + 'px');
		};

		var _hideTopbarNotification = function(event) {
			event.stopPropagation();

			$.ajax({
				       type: 'POST',
				       url: 'request_port.php?module=Notification&action=hide_topbar',
				       timeout: 5000,
				       dataType: 'json',
				       context: this,
				       data: {},
				       success: function(p_response) {
					       $('.topbar-notification').remove();
					       $(options.outerWrapperSelector).removeClass('topbar-active');
					
					       if ($(options.headerSelector).css('position') !== 'fixed') {
						       $(options.outerWrapperSelector).css('margin-top', initialMarginTop);
					       }
				       }
			       });

			return false;
		};

		var _hidePopUpNotification = function(event) {
			event.stopPropagation();

			$.ajax({
				       type: 'POST',
				       url: 'request_port.php?module=Notification&action=hide_popup_notification',
				       timeout: 5000,
				       dataType: 'json',
				       context: this,
				       data: {},
				       success: function(p_response) {
					       $('.popup-notification').remove();
				       }
			       });

			return false;
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			
			initialMarginTop = $(options.outerWrapperSelector).css('margin-top');
			
			if ($(options.headerSelector).css('position') !== 'fixed') {
				_topBarPositioning();
			}

			$this.on('click', '.hide-topbar-notification', _hideTopbarNotification);
			$this.on('click', '.hide-popup-notification', _hidePopUpNotification);

			done();
		};

		// Return data to widget engine
		return module;
	});