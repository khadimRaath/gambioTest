/* --------------------------------------------------------------
 categories_multi_action_controller.js 2016-02-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Table Controller
 *
 * This controller contains the mapping logic of the categories/articles multi select actions for the button
 * dropdown (on the bottom).
 *
 * @module Compatibility/categories_multi_action_controller
 */
gx.compatibility.module(
	'categories_multi_action_controller',
	
	[
		gx.source + '/libs/button_dropdown'
	],
	
	/**  @lends module:Compatibility/categories_multi_action_controller */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Dropdown button selector
			 * @var {object}
			 */
			$dropdown = $this.find('.js-bottom-dropdown'),
			
			/**
			 * Input fields
			 * @type {*|jQuery|HTMLElement}
			 */
			$inputs = $('tr[data-id] input[type="checkbox"]'),
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		
		/**
		 * Get Url Parameter
		 *
		 * Gets a specific URL get parameter from the address bar,
		 * which name should be provided as an argument.
		 * @param {string} parameterName
		 * @returns {object}
		 * @private
		 */
		var _getUrlParameter = function(parameterName) {
			var results = new RegExp('[\?&]' + parameterName + '=([^&#]*)').exec(window.location.href);
			if (results == null) {
				return null;
			} else {
				return results[1] || 0;
			}
		};
		
		
		/**
		 * Prepare Form
		 *
		 * @param {string} action
		 *
		 * @return {object | jQuery}
		 */
		var _$prepareForm = function(action) {
			var cPath;
			try {
				cPath = window.location.href.match(/cPath=(.*)/)[1];
			}
			catch (e) {
				cPath = $('[data-cpath]:first').data().cpath;
			}
			
			var page_token = $('input[name="page_token"]:first').attr('value');
			
			var formUrl = [
				_getSourcePath(),
				'categories.php',
				'?action=multi_action',
				'&cPath=' + cPath
			].join('');
			
			var search = _getUrlParameter('search');
			if (search !== 0 && search !== null) {
				formUrl += ('&search=' + search);
			}
			
			var page = _getUrlParameter('page');
			if (page !== 0 && page !== null && formUrl.indexOf('page=') === -1) {
				formUrl += '&page=' + page;
			}
			
			var $form = $('<form name="multi_action_form" method="post" action=' + formUrl + '></form>');
			$form.append('<input type="hidden" name="cpath" value=' + cPath + '>');
			$form.append('<input type="hidden" name="page_token" value=' + page_token + '>');
			$form.append('<input type="hidden" name=' + action + ' value="Action">');
			$form.appendTo('body');
			return $form;
		};
		
		var _sectionMapping = {
			delete: 'buttons',
			BUTTON_MOVE: 'admin_buttons',
			BUTTON_COPY: 'admin_buttons',
			BUTTON_STATUS_ON: 'admin_buttons',
			BUTTON_STATUS_OFF: 'admin_buttons'
		};
		
		/**
		 * Map actions for the dropdown button
		 *
		 * This method will map the actions for multiple selects.
		 */
		var _mapMultiActions = function() {
			var actions = [
				'delete',
				'BUTTON_MOVE',
				'BUTTON_COPY',
				'BUTTON_STATUS_ON',
				'BUTTON_STATUS_OFF'
			];
			
			for (var index in actions) {
				_mapAction(actions[index]);
			}
		};
		
		var _mapAction = function(action) {
			var section = _sectionMapping[action],
				callback = _getActionCallback(action);
			jse.libs.button_dropdown.mapAction($dropdown, action, section, callback);
		};
		
		var _callbackDelete = function(event) {
			// Do not do anything when no product/category is checked
			if (!$inputs.filter(':checked').length) {
				return;
			}
			
			// Submit cached form
			var $form = _$prepareForm('multi_delete');
			$inputs.filter(':checked').appendTo($form);
			$form.submit();
		};
		
		var _callbackMove = function(event) {
			// Do not do anything when no product/category is checked
			if (!$inputs.filter(':checked').length) {
				return;
			}
			
			// Submit cached form
			var $form = _$prepareForm('multi_move');
			$inputs.filter(':checked').appendTo($form);
			$form.submit();
		};
		
		var _callbackCopy = function(event) {
			// Do not do anything when no product/category is checked
			if (!$inputs.filter(':checked').length) {
				return;
			}
			
			// Submit cached form
			var $form = _$prepareForm('multi_copy');
			$inputs.filter(':checked').appendTo($form);
			$form.submit();
		};
		
		var _callbackStatusOn = function(event) {
			// Do not do anything when no product/category is checked
			if (!$inputs.filter(':checked').length) {
				return;
			}
			
			// Submit cached form
			var $form = _$prepareForm('multi_status_on');
			$inputs.filter(':checked').appendTo($form);
			$form.submit();
		};
		
		var _callbackStatusOff = function(event) {
			// Do not do anything when no product/category is checked
			if (!$inputs.filter(':checked').length) {
				return;
			}
			
			// Submit cached form
			var $form = _$prepareForm('multi_status_off');
			$inputs.filter(':checked').appendTo($form);
			$form.submit();
		};
		
		var _getActionCallback = function(action) {
			switch (action) {
				case 'delete':
					return _callbackDelete;
				case 'BUTTON_MOVE':
					return _callbackMove;
				case 'BUTTON_COPY':
					return _callbackCopy;
				case 'BUTTON_STATUS_ON':
					return _callbackStatusOn;
				case 'BUTTON_STATUS_OFF':
					return _callbackStatusOff;
				default:
					console.alert('_getActionCallback: Action not found');
			}
			return null;
		};
		
		/**
		 * Get path of the admin folder
		 *
		 * @returns {string}
		 */
		var _getSourcePath = function() {
			var url = window.location.origin,
				path = window.location.pathname;
			
			var splittedPath = path.split('/');
			splittedPath.pop();
			
			var joinedPath = splittedPath.join('/');
			
			return url + joinedPath + '/';
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Wait until the dropdown is filled
			var interval = setInterval(function() {
				if ($('.js-button-dropdown').length > 0) {
					clearInterval(interval);
					_mapMultiActions();
				}
			}, 200);
			
			done();
		};
		
		return module;
	});
