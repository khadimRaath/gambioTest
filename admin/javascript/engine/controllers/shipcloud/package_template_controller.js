/* --------------------------------------------------------------
 package_template_controller.js 2016-01-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module(
	// Module name
	'package_template_controller',
	// Module dependencies
	[
		'loading_spinner'
	],
	function(data) {
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
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
		
		var _selectRow = function($that) {
			var packageTemplateId = window.location.hash.substring(1);
			var withSpinner = (undefined === $that) ? false : true;
			$('.box-content').hide();
			$('.button-set').hide();
			$('.package-details').show();
			$('.detail-buttons').show();
			$that = $that || $('.sc_package_template_' + packageTemplateId);
			if (packageTemplateId) {
				$that.addClass('active');
				_loadContent(packageTemplateId, withSpinner);
				$('.gx-configuration-box').css('visibility', 'visible');
			}
		};
		
		var _loadContent = function(packageTemplateId, withSpinner) {
			var $configBox = $('.gx-configuration-box');
			
			if (withSpinner) {
				var $spinner = jse.libs.loading_spinner.show($configBox);
			}
			
			$.ajax({
				url: 'admin.php?do=ShipcloudModuleCenterModule/GetPackageTemplate&templateId='
				+ packageTemplateId,
				type: 'GET',
				dataType: 'json'
			}).success(function(data) {
				$('.configuration-box-header h2').html(data['packages/' + packageTemplateId + '/name']);
				
				$('.configuration-box-form-content .package-details .package-weight')
					.html(data['packages/' + packageTemplateId + '/weight']);
				$('.configuration-box-form-content .package-details .package-length')
					.html(data['packages/' + packageTemplateId + '/length']);
				$('.configuration-box-form-content .package-details .package-width')
					.html(data['packages/' + packageTemplateId + '/width']);
				$('.configuration-box-form-content .package-details .package-height')
					.html(data['packages/' + packageTemplateId + '/height']);
				
				$('.configuration-box-form-content .package-form-data .package_id_input')
					.val(packageTemplateId);
				$('.configuration-box-form-content .package-form-data .package_name_input')
					.val(data['packages/' + packageTemplateId + '/name']);
				$('.configuration-box-form-content .package-form-data .package_weight_input')
					.val(data['packages/' + packageTemplateId + '/weight']);
				$('.configuration-box-form-content .package-form-data .package_length_input')
					.val(data['packages/' + packageTemplateId + '/length']);
				$('.configuration-box-form-content .package-form-data .package_width_input')
					.val(data['packages/' + packageTemplateId + '/width']);
				$('.configuration-box-form-content .package-form-data .package_height_input')
					.val(data['packages/' + packageTemplateId + '/height']);
				
				$('.configuration-box-form-content .package-form-data .is_default_input_row')
					.toggle(data.is_default === false);
				$('.configuration-box-form-content .package-form-data .is_default_input')
					.val(packageTemplateId);
				
				if ($spinner && withSpinner) {
					jse.libs.loading_spinner.hide($spinner);
				}
			});
		};
		
		var _setRowActive = function() {
			$('.gx-modules-table .dataTableRow.active').removeClass('active');
			
			_selectRow($(this));
			
			$('html, body').animate({
				scrollTop: 0
			});
		};
		
		var _showCreateFormData = function() {
			$('.gx-modules-table .dataTableRow.active').removeClass('active');
			
			$('.configuration-box-header h2').html('&nbsp;');
			
			$('.box-content').hide();
			$('.button-set').hide();
			$('.package-form-data').show();
			$('.create-form-data-buttons').show();
			
			$('.configuration-box-form-content .package-form-data .package_id_input').val('');
			$('.configuration-box-form-content .package-form-data .package_name_input').val('');
			$('.configuration-box-form-content .package-form-data .package_weight_input').val('');
			$('.configuration-box-form-content .package-form-data .package_length_input').val('');
			$('.configuration-box-form-content .package-form-data .package_width_input').val('');
			$('.configuration-box-form-content .package-form-data .package_height_input').val('');
			
			$('.gx-configuration-box').css('visibility', 'visible');
		};
		
		var _showDetails = function() {
			$('.box-content').hide();
			$('.button-set').hide();
			$('.package-details').show();
			$('.detail-buttons').show();
			$('.gx-configuration-box').css('visibility', 'visible');
		};
		
		var _showFormData = function() {
			$('.box-content').hide();
			$('.button-set').hide();
			$('.package-form-data').show();
			$('.form-data-buttons').show();
			$('.gx-configuration-box').css('visibility', 'visible');
		};
		
		var _showDeleteConfirmation = function() {
			$('.box-content').hide();
			$('.button-set').hide();
			$('.delete-confirmation').show();
			$('.confirm-delete-buttons').show();
			$('.gx-configuration-box').css('visibility', 'visible');
		};
		
		var _syncTitle = function() {
			if ($('.package-form-data .package_name_input').val()) {
				$('.configuration-box-header h2').text($('.package-form-data .package_name_input').val());
			}
			else {
				$('.configuration-box-header h2').html('&nbsp;');
			}
			
		};
		
		var _deletePackageTemplate = function() {
			$('#configuration-box-form')
				.attr('action', 'admin.php?do=ShipcloudModuleCenterModule/DeletePackageTemplate&templateId='
					+ parseInt($('.configuration-box-form-content .package-form-data .package_id_input').val()));
			$('#configuration-box-form').submit();
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			$('.gx-configuration-box').css('visibility', 'hidden');
			_selectRow();
			$('.gx-modules-table .dataTableRow').on('click', _setRowActive);
			$('.configuration-box-footer .edit-package-template').on('click', _showFormData);
			$('.configuration-box-footer .cancel-package-template').on('click', _showDetails);
			$('.create-new-wrapper .add-package-template').on('click', _showCreateFormData);
			$('.configuration-box-footer .delete-package-template').on('click', _showDeleteConfirmation);
			$('.configuration-box-footer .confirm-delete-package-template').on('click', _deletePackageTemplate);
			$('.package-form-data .package_name_input').on('keyup', _syncTitle);
			done();
		};
		
		return module;
	}
);