/* --------------------------------------------------------------
 module_center.js 2015-10-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Module Center
 *
 * This module will handle the click events on the module center page
 *
 * @module Controllers/module_center
 */
gx.controllers.module(
	'module_center',
	
	['datatable'],
	
	/**  @lends module:Controllers/module_center */
	
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
		
		var _loadModuleData = function(module) {
			
			$.ajax({
					url: 'admin.php?do=ModuleCenter/GetData&module=' + module,
					type: 'GET',
					dataType: 'json'
				})
				.success(function(data) {
					if (data.success) {
						$('.configuration-box-header h2').html(data.payload.title);
						$('.configuration-box-description p').html(data.payload.description);
						
						$('.gx-configuration-box a.btn').attr('href', 'admin.php?do=' + data.payload.name +
							'ModuleCenterModule');
						
						if (data.payload.isInstalled) {
							//$('.gx-configuration-box form').attr('action', 'admin.php?do=ModuleCenter/Destroy');
							$('.gx-configuration-box form button[name="uninstall"]').show();
							$('.gx-configuration-box form a.btn').show();
							$('.gx-configuration-box form button[name="install"]').hide();
						} else {
							$('.gx-configuration-box form').attr('action', 'admin.php?do=ModuleCenter/Store');
							$('.gx-configuration-box form button[name="uninstall"]').hide();
							$('.gx-configuration-box form a.btn').hide();
							$('.gx-configuration-box form button[name="install"]').show();
						}
						
						$('.gx-configuration-box form input[name="module"]').val(data.payload.name);
						$('.gx-configuration-box').css('visibility', 'visible');
					}
				});
		};
		
		var _openUninstallDialog = function(event) {
			event.preventDefault();
			var $dialog = $('#module-center-confirmation-dialog'),
				module = $('.gx-configuration-box').find('input[name="module"]').val();
			
			//$form.attr('action', 'admin.php?do=ModuleCenter/Destroy');
			$dialog.find('.modal-info-text').html(jse.core.lang.translate('text_uninstall_confirmation',
				'module_center'));
			$dialog.find('input[name="module"]').val(module);
			
			$dialog.dialog({
				'title': jse.core.lang.translate('uninstall_confirmation_title', 'module_center')
					.replace('%s',
						module),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					},
					{
						'text': jse.core.lang.translate('uninstall', 'buttons'),
						'class': 'btn btn-primary',
						'click': function() {
							$dialog.find('form').submit();
						}
					}
				]
			});
		};
		
		var queryString = (function(a) {
			if (a === '') {
				return {};
			}
			var b = {};
			for (var i = 0; i < a.length; ++i) {
				var p = a[i].split('=', 2);
				if (p.length === 1) {
					b[p[0]] = '';
				} else {
					b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, ' '));
				}
			}
			return b;
		})(window.location.search.substr(1).split('&'));
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _hashChange = function() {
			var module = window.location.hash.substring(1);
			
			if (module !== '') {
				_loadModuleData(module);
			}
		};
		
		var _setRowActive = function() {
			$('.gx-modules-table .dataTableRow.active').removeClass('active');
			
			$(this).addClass('active');
			
			$('html, body').animate({
				scrollTop: 0
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			jse.libs.datatable.create($this.find('.gx-modules-table'), {
				'dom': 't',
				'autoWidth': false,
				'pageLength': 1000,
				'columnDefs': [
					{
						'targets': [1, 2],
						'orderable': false
					}
				],
				'order': []
			});
			
			if (typeof queryString.module !== 'undefined') {
				_loadModuleData(queryString.module);
			} else {
				$('.gx-configuration-box').css('visibility', 'hidden');
			}
			
			_hashChange();
			
			$(window).on('hashchange', _hashChange);
			$('.gx-modules-table .dataTableRow').on('click', _setRowActive);
			$('.configuration-box-footer button[name="uninstall"]').on('click', _openUninstallDialog);
			
			done();
		};
		
		return module;
	});
