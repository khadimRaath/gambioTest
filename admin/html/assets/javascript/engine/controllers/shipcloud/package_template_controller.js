'use strict';

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
['loading_spinner'], function (data) {
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

	var _selectRow = function _selectRow($that) {
		var packageTemplateId = window.location.hash.substring(1);
		var withSpinner = undefined === $that ? false : true;
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

	var _loadContent = function _loadContent(packageTemplateId, withSpinner) {
		var $configBox = $('.gx-configuration-box');

		if (withSpinner) {
			var $spinner = jse.libs.loading_spinner.show($configBox);
		}

		$.ajax({
			url: 'admin.php?do=ShipcloudModuleCenterModule/GetPackageTemplate&templateId=' + packageTemplateId,
			type: 'GET',
			dataType: 'json'
		}).success(function (data) {
			$('.configuration-box-header h2').html(data['packages/' + packageTemplateId + '/name']);

			$('.configuration-box-form-content .package-details .package-weight').html(data['packages/' + packageTemplateId + '/weight']);
			$('.configuration-box-form-content .package-details .package-length').html(data['packages/' + packageTemplateId + '/length']);
			$('.configuration-box-form-content .package-details .package-width').html(data['packages/' + packageTemplateId + '/width']);
			$('.configuration-box-form-content .package-details .package-height').html(data['packages/' + packageTemplateId + '/height']);

			$('.configuration-box-form-content .package-form-data .package_id_input').val(packageTemplateId);
			$('.configuration-box-form-content .package-form-data .package_name_input').val(data['packages/' + packageTemplateId + '/name']);
			$('.configuration-box-form-content .package-form-data .package_weight_input').val(data['packages/' + packageTemplateId + '/weight']);
			$('.configuration-box-form-content .package-form-data .package_length_input').val(data['packages/' + packageTemplateId + '/length']);
			$('.configuration-box-form-content .package-form-data .package_width_input').val(data['packages/' + packageTemplateId + '/width']);
			$('.configuration-box-form-content .package-form-data .package_height_input').val(data['packages/' + packageTemplateId + '/height']);

			$('.configuration-box-form-content .package-form-data .is_default_input_row').toggle(data.is_default === false);
			$('.configuration-box-form-content .package-form-data .is_default_input').val(packageTemplateId);

			if ($spinner && withSpinner) {
				jse.libs.loading_spinner.hide($spinner);
			}
		});
	};

	var _setRowActive = function _setRowActive() {
		$('.gx-modules-table .dataTableRow.active').removeClass('active');

		_selectRow($(this));

		$('html, body').animate({
			scrollTop: 0
		});
	};

	var _showCreateFormData = function _showCreateFormData() {
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

	var _showDetails = function _showDetails() {
		$('.box-content').hide();
		$('.button-set').hide();
		$('.package-details').show();
		$('.detail-buttons').show();
		$('.gx-configuration-box').css('visibility', 'visible');
	};

	var _showFormData = function _showFormData() {
		$('.box-content').hide();
		$('.button-set').hide();
		$('.package-form-data').show();
		$('.form-data-buttons').show();
		$('.gx-configuration-box').css('visibility', 'visible');
	};

	var _showDeleteConfirmation = function _showDeleteConfirmation() {
		$('.box-content').hide();
		$('.button-set').hide();
		$('.delete-confirmation').show();
		$('.confirm-delete-buttons').show();
		$('.gx-configuration-box').css('visibility', 'visible');
	};

	var _syncTitle = function _syncTitle() {
		if ($('.package-form-data .package_name_input').val()) {
			$('.configuration-box-header h2').text($('.package-form-data .package_name_input').val());
		} else {
			$('.configuration-box-header h2').html('&nbsp;');
		}
	};

	var _deletePackageTemplate = function _deletePackageTemplate() {
		$('#configuration-box-form').attr('action', 'admin.php?do=ShipcloudModuleCenterModule/DeletePackageTemplate&templateId=' + parseInt($('.configuration-box-form-content .package-form-data .package_id_input').val()));
		$('#configuration-box-form').submit();
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
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
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNoaXBjbG91ZC9wYWNrYWdlX3RlbXBsYXRlX2NvbnRyb2xsZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2VsZWN0Um93IiwiJHRoYXQiLCJwYWNrYWdlVGVtcGxhdGVJZCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaGFzaCIsInN1YnN0cmluZyIsIndpdGhTcGlubmVyIiwidW5kZWZpbmVkIiwiaGlkZSIsInNob3ciLCJhZGRDbGFzcyIsIl9sb2FkQ29udGVudCIsImNzcyIsIiRjb25maWdCb3giLCIkc3Bpbm5lciIsImpzZSIsImxpYnMiLCJsb2FkaW5nX3NwaW5uZXIiLCJhamF4IiwidXJsIiwidHlwZSIsImRhdGFUeXBlIiwic3VjY2VzcyIsImh0bWwiLCJ2YWwiLCJ0b2dnbGUiLCJpc19kZWZhdWx0IiwiX3NldFJvd0FjdGl2ZSIsInJlbW92ZUNsYXNzIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsIl9zaG93Q3JlYXRlRm9ybURhdGEiLCJfc2hvd0RldGFpbHMiLCJfc2hvd0Zvcm1EYXRhIiwiX3Nob3dEZWxldGVDb25maXJtYXRpb24iLCJfc3luY1RpdGxlIiwidGV4dCIsIl9kZWxldGVQYWNrYWdlVGVtcGxhdGUiLCJhdHRyIiwicGFyc2VJbnQiLCJzdWJtaXQiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZjtBQUNDO0FBQ0EsNkJBRkQ7QUFHQztBQUNBLENBQ0MsaUJBREQsQ0FKRCxFQU9DLFVBQVNDLElBQVQsRUFBZTtBQUNkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1B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LFFBRFAsRUFDaUIsK0VBQ2JDLFNBQVM1QyxFQUFFLHNFQUFGLEVBQTBFNkIsR0FBMUUsRUFBVCxDQUZKO0FBR0E3QixJQUFFLHlCQUFGLEVBQTZCNkMsTUFBN0I7QUFDQSxFQUxEOztBQU9BO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FoRCxRQUFPaUQsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1Qi9DLElBQUUsdUJBQUYsRUFBMkJpQixHQUEzQixDQUErQixZQUEvQixFQUE2QyxRQUE3QztBQUNBYjtBQUNBSixJQUFFLGlDQUFGLEVBQXFDZ0QsRUFBckMsQ0FBd0MsT0FBeEMsRUFBaURoQixhQUFqRDtBQUNBaEMsSUFBRSxrREFBRixFQUFzRGdELEVBQXRELENBQXlELE9BQXpELEVBQWtFVixhQUFsRTtBQUNBdEMsSUFBRSxvREFBRixFQUF3RGdELEVBQXhELENBQTJELE9BQTNELEVBQW9FWCxZQUFwRTtBQUNBckMsSUFBRSwyQ0FBRixFQUErQ2dELEVBQS9DLENBQWtELE9BQWxELEVBQTJEWixtQkFBM0Q7QUFDQXBDLElBQUUsb0RBQUYsRUFBd0RnRCxFQUF4RCxDQUEyRCxPQUEzRCxFQUFvRVQsdUJBQXBFO0FBQ0F2QyxJQUFFLDREQUFGLEVBQWdFZ0QsRUFBaEUsQ0FBbUUsT0FBbkUsRUFBNEVOLHNCQUE1RTtBQUNBMUMsSUFBRSx3Q0FBRixFQUE0Q2dELEVBQTVDLENBQStDLE9BQS9DLEVBQXdEUixVQUF4RDtBQUNBTztBQUNBLEVBWEQ7O0FBYUEsUUFBT2xELE1BQVA7QUFDQSxDQTFNRiIsImZpbGUiOiJzaGlwY2xvdWQvcGFja2FnZV90ZW1wbGF0ZV9jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwYWNrYWdlX3RlbXBsYXRlX2NvbnRyb2xsZXIuanMgMjAxNi0wMS0yMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0Ly8gTW9kdWxlIG5hbWVcblx0J3BhY2thZ2VfdGVtcGxhdGVfY29udHJvbGxlcicsXG5cdC8vIE1vZHVsZSBkZXBlbmRlbmNpZXNcblx0W1xuXHRcdCdsb2FkaW5nX3NwaW5uZXInXG5cdF0sXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3NlbGVjdFJvdyA9IGZ1bmN0aW9uKCR0aGF0KSB7XG5cdFx0XHR2YXIgcGFja2FnZVRlbXBsYXRlSWQgPSB3aW5kb3cubG9jYXRpb24uaGFzaC5zdWJzdHJpbmcoMSk7XG5cdFx0XHR2YXIgd2l0aFNwaW5uZXIgPSAodW5kZWZpbmVkID09PSAkdGhhdCkgPyBmYWxzZSA6IHRydWU7XG5cdFx0XHQkKCcuYm94LWNvbnRlbnQnKS5oaWRlKCk7XG5cdFx0XHQkKCcuYnV0dG9uLXNldCcpLmhpZGUoKTtcblx0XHRcdCQoJy5wYWNrYWdlLWRldGFpbHMnKS5zaG93KCk7XG5cdFx0XHQkKCcuZGV0YWlsLWJ1dHRvbnMnKS5zaG93KCk7XG5cdFx0XHQkdGhhdCA9ICR0aGF0IHx8ICQoJy5zY19wYWNrYWdlX3RlbXBsYXRlXycgKyBwYWNrYWdlVGVtcGxhdGVJZCk7XG5cdFx0XHRpZiAocGFja2FnZVRlbXBsYXRlSWQpIHtcblx0XHRcdFx0JHRoYXQuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XHRfbG9hZENvbnRlbnQocGFja2FnZVRlbXBsYXRlSWQsIHdpdGhTcGlubmVyKTtcblx0XHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfbG9hZENvbnRlbnQgPSBmdW5jdGlvbihwYWNrYWdlVGVtcGxhdGVJZCwgd2l0aFNwaW5uZXIpIHtcblx0XHRcdHZhciAkY29uZmlnQm94ID0gJCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94Jyk7XG5cdFx0XHRcblx0XHRcdGlmICh3aXRoU3Bpbm5lcikge1xuXHRcdFx0XHR2YXIgJHNwaW5uZXIgPSBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuc2hvdygkY29uZmlnQm94KTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0dXJsOiAnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZE1vZHVsZUNlbnRlck1vZHVsZS9HZXRQYWNrYWdlVGVtcGxhdGUmdGVtcGxhdGVJZD0nXG5cdFx0XHRcdCsgcGFja2FnZVRlbXBsYXRlSWQsXG5cdFx0XHRcdHR5cGU6ICdHRVQnLFxuXHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHR9KS5zdWNjZXNzKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWhlYWRlciBoMicpLmh0bWwoZGF0YVsncGFja2FnZXMvJyArIHBhY2thZ2VUZW1wbGF0ZUlkICsgJy9uYW1lJ10pO1xuXHRcdFx0XHRcblx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1kZXRhaWxzIC5wYWNrYWdlLXdlaWdodCcpXG5cdFx0XHRcdFx0Lmh0bWwoZGF0YVsncGFja2FnZXMvJyArIHBhY2thZ2VUZW1wbGF0ZUlkICsgJy93ZWlnaHQnXSk7XG5cdFx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZGV0YWlscyAucGFja2FnZS1sZW5ndGgnKVxuXHRcdFx0XHRcdC5odG1sKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvbGVuZ3RoJ10pO1xuXHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9ybS1jb250ZW50IC5wYWNrYWdlLWRldGFpbHMgLnBhY2thZ2Utd2lkdGgnKVxuXHRcdFx0XHRcdC5odG1sKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvd2lkdGgnXSk7XG5cdFx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZGV0YWlscyAucGFja2FnZS1oZWlnaHQnKVxuXHRcdFx0XHRcdC5odG1sKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvaGVpZ2h0J10pO1xuXHRcdFx0XHRcblx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2VfaWRfaW5wdXQnKVxuXHRcdFx0XHRcdC52YWwocGFja2FnZVRlbXBsYXRlSWQpO1xuXHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9ybS1jb250ZW50IC5wYWNrYWdlLWZvcm0tZGF0YSAucGFja2FnZV9uYW1lX2lucHV0Jylcblx0XHRcdFx0XHQudmFsKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvbmFtZSddKTtcblx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2Vfd2VpZ2h0X2lucHV0Jylcblx0XHRcdFx0XHQudmFsKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvd2VpZ2h0J10pO1xuXHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9ybS1jb250ZW50IC5wYWNrYWdlLWZvcm0tZGF0YSAucGFja2FnZV9sZW5ndGhfaW5wdXQnKVxuXHRcdFx0XHRcdC52YWwoZGF0YVsncGFja2FnZXMvJyArIHBhY2thZ2VUZW1wbGF0ZUlkICsgJy9sZW5ndGgnXSk7XG5cdFx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX3dpZHRoX2lucHV0Jylcblx0XHRcdFx0XHQudmFsKGRhdGFbJ3BhY2thZ2VzLycgKyBwYWNrYWdlVGVtcGxhdGVJZCArICcvd2lkdGgnXSk7XG5cdFx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX2hlaWdodF9pbnB1dCcpXG5cdFx0XHRcdFx0LnZhbChkYXRhWydwYWNrYWdlcy8nICsgcGFja2FnZVRlbXBsYXRlSWQgKyAnL2hlaWdodCddKTtcblx0XHRcdFx0XG5cdFx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZm9ybS1kYXRhIC5pc19kZWZhdWx0X2lucHV0X3JvdycpXG5cdFx0XHRcdFx0LnRvZ2dsZShkYXRhLmlzX2RlZmF1bHQgPT09IGZhbHNlKTtcblx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLmlzX2RlZmF1bHRfaW5wdXQnKVxuXHRcdFx0XHRcdC52YWwocGFja2FnZVRlbXBsYXRlSWQpO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKCRzcGlubmVyICYmIHdpdGhTcGlubmVyKSB7XG5cdFx0XHRcdFx0anNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUoJHNwaW5uZXIpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc2V0Um93QWN0aXZlID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuZ3gtbW9kdWxlcy10YWJsZSAuZGF0YVRhYmxlUm93LmFjdGl2ZScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFxuXHRcdFx0X3NlbGVjdFJvdygkKHRoaXMpKTtcblx0XHRcdFxuXHRcdFx0JCgnaHRtbCwgYm9keScpLmFuaW1hdGUoe1xuXHRcdFx0XHRzY3JvbGxUb3A6IDBcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zaG93Q3JlYXRlRm9ybURhdGEgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5neC1tb2R1bGVzLXRhYmxlIC5kYXRhVGFibGVSb3cuYWN0aXZlJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XG5cdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtaGVhZGVyIGgyJykuaHRtbCgnJm5ic3A7Jyk7XG5cdFx0XHRcblx0XHRcdCQoJy5ib3gtY29udGVudCcpLmhpZGUoKTtcblx0XHRcdCQoJy5idXR0b24tc2V0JykuaGlkZSgpO1xuXHRcdFx0JCgnLnBhY2thZ2UtZm9ybS1kYXRhJykuc2hvdygpO1xuXHRcdFx0JCgnLmNyZWF0ZS1mb3JtLWRhdGEtYnV0dG9ucycpLnNob3coKTtcblx0XHRcdFxuXHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2VfaWRfaW5wdXQnKS52YWwoJycpO1xuXHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2VfbmFtZV9pbnB1dCcpLnZhbCgnJyk7XG5cdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9ybS1jb250ZW50IC5wYWNrYWdlLWZvcm0tZGF0YSAucGFja2FnZV93ZWlnaHRfaW5wdXQnKS52YWwoJycpO1xuXHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvcm0tY29udGVudCAucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2VfbGVuZ3RoX2lucHV0JykudmFsKCcnKTtcblx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX3dpZHRoX2lucHV0JykudmFsKCcnKTtcblx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb3JtLWNvbnRlbnQgLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX2hlaWdodF9pbnB1dCcpLnZhbCgnJyk7XG5cdFx0XHRcblx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCcpLmNzcygndmlzaWJpbGl0eScsICd2aXNpYmxlJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3Nob3dEZXRhaWxzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuYm94LWNvbnRlbnQnKS5oaWRlKCk7XG5cdFx0XHQkKCcuYnV0dG9uLXNldCcpLmhpZGUoKTtcblx0XHRcdCQoJy5wYWNrYWdlLWRldGFpbHMnKS5zaG93KCk7XG5cdFx0XHQkKCcuZGV0YWlsLWJ1dHRvbnMnKS5zaG93KCk7XG5cdFx0XHQkKCcuZ3gtY29uZmlndXJhdGlvbi1ib3gnKS5jc3MoJ3Zpc2liaWxpdHknLCAndmlzaWJsZScpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zaG93Rm9ybURhdGEgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5ib3gtY29udGVudCcpLmhpZGUoKTtcblx0XHRcdCQoJy5idXR0b24tc2V0JykuaGlkZSgpO1xuXHRcdFx0JCgnLnBhY2thZ2UtZm9ybS1kYXRhJykuc2hvdygpO1xuXHRcdFx0JCgnLmZvcm0tZGF0YS1idXR0b25zJykuc2hvdygpO1xuXHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc2hvd0RlbGV0ZUNvbmZpcm1hdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnLmJveC1jb250ZW50JykuaGlkZSgpO1xuXHRcdFx0JCgnLmJ1dHRvbi1zZXQnKS5oaWRlKCk7XG5cdFx0XHQkKCcuZGVsZXRlLWNvbmZpcm1hdGlvbicpLnNob3coKTtcblx0XHRcdCQoJy5jb25maXJtLWRlbGV0ZS1idXR0b25zJykuc2hvdygpO1xuXHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc3luY1RpdGxlID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJCgnLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX25hbWVfaW5wdXQnKS52YWwoKSkge1xuXHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtaGVhZGVyIGgyJykudGV4dCgkKCcucGFja2FnZS1mb3JtLWRhdGEgLnBhY2thZ2VfbmFtZV9pbnB1dCcpLnZhbCgpKTtcblx0XHRcdH1cblx0XHRcdGVsc2Uge1xuXHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtaGVhZGVyIGgyJykuaHRtbCgnJm5ic3A7Jyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfZGVsZXRlUGFja2FnZVRlbXBsYXRlID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcjY29uZmlndXJhdGlvbi1ib3gtZm9ybScpXG5cdFx0XHRcdC5hdHRyKCdhY3Rpb24nLCAnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZE1vZHVsZUNlbnRlck1vZHVsZS9EZWxldGVQYWNrYWdlVGVtcGxhdGUmdGVtcGxhdGVJZD0nXG5cdFx0XHRcdFx0KyBwYXJzZUludCgkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9ybS1jb250ZW50IC5wYWNrYWdlLWZvcm0tZGF0YSAucGFja2FnZV9pZF9pbnB1dCcpLnZhbCgpKSk7XG5cdFx0XHQkKCcjY29uZmlndXJhdGlvbi1ib3gtZm9ybScpLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgbW9kdWxlLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCcpLmNzcygndmlzaWJpbGl0eScsICdoaWRkZW4nKTtcblx0XHRcdF9zZWxlY3RSb3coKTtcblx0XHRcdCQoJy5neC1tb2R1bGVzLXRhYmxlIC5kYXRhVGFibGVSb3cnKS5vbignY2xpY2snLCBfc2V0Um93QWN0aXZlKTtcblx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb290ZXIgLmVkaXQtcGFja2FnZS10ZW1wbGF0ZScpLm9uKCdjbGljaycsIF9zaG93Rm9ybURhdGEpO1xuXHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvb3RlciAuY2FuY2VsLXBhY2thZ2UtdGVtcGxhdGUnKS5vbignY2xpY2snLCBfc2hvd0RldGFpbHMpO1xuXHRcdFx0JCgnLmNyZWF0ZS1uZXctd3JhcHBlciAuYWRkLXBhY2thZ2UtdGVtcGxhdGUnKS5vbignY2xpY2snLCBfc2hvd0NyZWF0ZUZvcm1EYXRhKTtcblx0XHRcdCQoJy5jb25maWd1cmF0aW9uLWJveC1mb290ZXIgLmRlbGV0ZS1wYWNrYWdlLXRlbXBsYXRlJykub24oJ2NsaWNrJywgX3Nob3dEZWxldGVDb25maXJtYXRpb24pO1xuXHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWZvb3RlciAuY29uZmlybS1kZWxldGUtcGFja2FnZS10ZW1wbGF0ZScpLm9uKCdjbGljaycsIF9kZWxldGVQYWNrYWdlVGVtcGxhdGUpO1xuXHRcdFx0JCgnLnBhY2thZ2UtZm9ybS1kYXRhIC5wYWNrYWdlX25hbWVfaW5wdXQnKS5vbigna2V5dXAnLCBfc3luY1RpdGxlKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH1cbik7Il19
