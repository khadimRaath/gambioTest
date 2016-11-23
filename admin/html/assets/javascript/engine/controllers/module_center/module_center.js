'use strict';

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
gx.controllers.module('module_center', ['datatable'],

/**  @lends module:Controllers/module_center */

function (data) {

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

	var _loadModuleData = function _loadModuleData(module) {

		$.ajax({
			url: 'admin.php?do=ModuleCenter/GetData&module=' + module,
			type: 'GET',
			dataType: 'json'
		}).success(function (data) {
			if (data.success) {
				$('.configuration-box-header h2').html(data.payload.title);
				$('.configuration-box-description p').html(data.payload.description);

				$('.gx-configuration-box a.btn').attr('href', 'admin.php?do=' + data.payload.name + 'ModuleCenterModule');

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

	var _openUninstallDialog = function _openUninstallDialog(event) {
		event.preventDefault();
		var $dialog = $('#module-center-confirmation-dialog'),
		    module = $('.gx-configuration-box').find('input[name="module"]').val();

		//$form.attr('action', 'admin.php?do=ModuleCenter/Destroy');
		$dialog.find('.modal-info-text').html(jse.core.lang.translate('text_uninstall_confirmation', 'module_center'));
		$dialog.find('input[name="module"]').val(module);

		$dialog.dialog({
			'title': jse.core.lang.translate('uninstall_confirmation_title', 'module_center').replace('%s', module),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': [{
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function click() {
					$(this).dialog('close');
				}
			}, {
				'text': jse.core.lang.translate('uninstall', 'buttons'),
				'class': 'btn btn-primary',
				'click': function click() {
					$dialog.find('form').submit();
				}
			}]
		});
	};

	var queryString = function (a) {
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
	}(window.location.search.substr(1).split('&'));

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _hashChange = function _hashChange() {
		var module = window.location.hash.substring(1);

		if (module !== '') {
			_loadModuleData(module);
		}
	};

	var _setRowActive = function _setRowActive() {
		$('.gx-modules-table .dataTableRow.active').removeClass('active');

		$(this).addClass('active');

		$('html, body').animate({
			scrollTop: 0
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		jse.libs.datatable.create($this.find('.gx-modules-table'), {
			'dom': 't',
			'autoWidth': false,
			'pageLength': 1000,
			'columnDefs': [{
				'targets': [1, 2],
				'orderable': false
			}],
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1vZHVsZV9jZW50ZXIvbW9kdWxlX2NlbnRlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9sb2FkTW9kdWxlRGF0YSIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwiZGF0YVR5cGUiLCJzdWNjZXNzIiwiaHRtbCIsInBheWxvYWQiLCJ0aXRsZSIsImRlc2NyaXB0aW9uIiwiYXR0ciIsIm5hbWUiLCJpc0luc3RhbGxlZCIsInNob3ciLCJoaWRlIiwidmFsIiwiY3NzIiwiX29wZW5Vbmluc3RhbGxEaWFsb2ciLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiJGRpYWxvZyIsImZpbmQiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImRpYWxvZyIsInJlcGxhY2UiLCJzdWJtaXQiLCJxdWVyeVN0cmluZyIsImEiLCJiIiwiaSIsImxlbmd0aCIsInAiLCJzcGxpdCIsImRlY29kZVVSSUNvbXBvbmVudCIsIndpbmRvdyIsImxvY2F0aW9uIiwic2VhcmNoIiwic3Vic3RyIiwiX2hhc2hDaGFuZ2UiLCJoYXNoIiwic3Vic3RyaW5nIiwiX3NldFJvd0FjdGl2ZSIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJhbmltYXRlIiwic2Nyb2xsVG9wIiwiaW5pdCIsImRvbmUiLCJsaWJzIiwiZGF0YXRhYmxlIiwiY3JlYXRlIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FDQyxlQURELEVBR0MsQ0FBQyxXQUFELENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJTyxrQkFBa0IsU0FBbEJBLGVBQWtCLENBQVNQLE1BQVQsRUFBaUI7O0FBRXRDRyxJQUFFSyxJQUFGLENBQU87QUFDTEMsUUFBSyw4Q0FBOENULE1BRDlDO0FBRUxVLFNBQU0sS0FGRDtBQUdMQyxhQUFVO0FBSEwsR0FBUCxFQUtFQyxPQUxGLENBS1UsVUFBU1gsSUFBVCxFQUFlO0FBQ3ZCLE9BQUlBLEtBQUtXLE9BQVQsRUFBa0I7QUFDakJULE1BQUUsOEJBQUYsRUFBa0NVLElBQWxDLENBQXVDWixLQUFLYSxPQUFMLENBQWFDLEtBQXBEO0FBQ0FaLE1BQUUsa0NBQUYsRUFBc0NVLElBQXRDLENBQTJDWixLQUFLYSxPQUFMLENBQWFFLFdBQXhEOztBQUVBYixNQUFFLDZCQUFGLEVBQWlDYyxJQUFqQyxDQUFzQyxNQUF0QyxFQUE4QyxrQkFBa0JoQixLQUFLYSxPQUFMLENBQWFJLElBQS9CLEdBQzdDLG9CQUREOztBQUdBLFFBQUlqQixLQUFLYSxPQUFMLENBQWFLLFdBQWpCLEVBQThCO0FBQzdCO0FBQ0FoQixPQUFFLHFEQUFGLEVBQXlEaUIsSUFBekQ7QUFDQWpCLE9BQUUsa0NBQUYsRUFBc0NpQixJQUF0QztBQUNBakIsT0FBRSxtREFBRixFQUF1RGtCLElBQXZEO0FBQ0EsS0FMRCxNQUtPO0FBQ05sQixPQUFFLDRCQUFGLEVBQWdDYyxJQUFoQyxDQUFxQyxRQUFyQyxFQUErQyxpQ0FBL0M7QUFDQWQsT0FBRSxxREFBRixFQUF5RGtCLElBQXpEO0FBQ0FsQixPQUFFLGtDQUFGLEVBQXNDa0IsSUFBdEM7QUFDQWxCLE9BQUUsbURBQUYsRUFBdURpQixJQUF2RDtBQUNBOztBQUVEakIsTUFBRSxpREFBRixFQUFxRG1CLEdBQXJELENBQXlEckIsS0FBS2EsT0FBTCxDQUFhSSxJQUF0RTtBQUNBZixNQUFFLHVCQUFGLEVBQTJCb0IsR0FBM0IsQ0FBK0IsWUFBL0IsRUFBNkMsU0FBN0M7QUFDQTtBQUNELEdBNUJGO0FBNkJBLEVBL0JEOztBQWlDQSxLQUFJQyx1QkFBdUIsU0FBdkJBLG9CQUF1QixDQUFTQyxLQUFULEVBQWdCO0FBQzFDQSxRQUFNQyxjQUFOO0FBQ0EsTUFBSUMsVUFBVXhCLEVBQUUsb0NBQUYsQ0FBZDtBQUFBLE1BQ0NILFNBQVNHLEVBQUUsdUJBQUYsRUFBMkJ5QixJQUEzQixDQUFnQyxzQkFBaEMsRUFBd0ROLEdBQXhELEVBRFY7O0FBR0E7QUFDQUssVUFBUUMsSUFBUixDQUFhLGtCQUFiLEVBQWlDZixJQUFqQyxDQUFzQ2dCLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLDZCQUF4QixFQUNyQyxlQURxQyxDQUF0QztBQUVBTCxVQUFRQyxJQUFSLENBQWEsc0JBQWIsRUFBcUNOLEdBQXJDLENBQXlDdEIsTUFBekM7O0FBRUEyQixVQUFRTSxNQUFSLENBQWU7QUFDZCxZQUFTSixJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3Qiw4QkFBeEIsRUFBd0QsZUFBeEQsRUFDUEUsT0FETyxDQUNDLElBREQsRUFFUGxDLE1BRk8sQ0FESztBQUlkLFlBQVMsSUFKSztBQUtkLGtCQUFlLGNBTEQ7QUFNZCxjQUFXLENBQ1Y7QUFDQyxZQUFRNkIsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsT0FBeEIsRUFBaUMsU0FBakMsQ0FEVDtBQUVDLGFBQVMsS0FGVjtBQUdDLGFBQVMsaUJBQVc7QUFDbkI3QixPQUFFLElBQUYsRUFBUThCLE1BQVIsQ0FBZSxPQUFmO0FBQ0E7QUFMRixJQURVLEVBUVY7QUFDQyxZQUFRSixJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixXQUF4QixFQUFxQyxTQUFyQyxDQURUO0FBRUMsYUFBUyxpQkFGVjtBQUdDLGFBQVMsaUJBQVc7QUFDbkJMLGFBQVFDLElBQVIsQ0FBYSxNQUFiLEVBQXFCTyxNQUFyQjtBQUNBO0FBTEYsSUFSVTtBQU5HLEdBQWY7QUF1QkEsRUFqQ0Q7O0FBbUNBLEtBQUlDLGNBQWUsVUFBU0MsQ0FBVCxFQUFZO0FBQzlCLE1BQUlBLE1BQU0sRUFBVixFQUFjO0FBQ2IsVUFBTyxFQUFQO0FBQ0E7QUFDRCxNQUFJQyxJQUFJLEVBQVI7QUFDQSxPQUFLLElBQUlDLElBQUksQ0FBYixFQUFnQkEsSUFBSUYsRUFBRUcsTUFBdEIsRUFBOEIsRUFBRUQsQ0FBaEMsRUFBbUM7QUFDbEMsT0FBSUUsSUFBSUosRUFBRUUsQ0FBRixFQUFLRyxLQUFMLENBQVcsR0FBWCxFQUFnQixDQUFoQixDQUFSO0FBQ0EsT0FBSUQsRUFBRUQsTUFBRixLQUFhLENBQWpCLEVBQW9CO0FBQ25CRixNQUFFRyxFQUFFLENBQUYsQ0FBRixJQUFVLEVBQVY7QUFDQSxJQUZELE1BRU87QUFDTkgsTUFBRUcsRUFBRSxDQUFGLENBQUYsSUFBVUUsbUJBQW1CRixFQUFFLENBQUYsRUFBS1AsT0FBTCxDQUFhLEtBQWIsRUFBb0IsR0FBcEIsQ0FBbkIsQ0FBVjtBQUNBO0FBQ0Q7QUFDRCxTQUFPSSxDQUFQO0FBQ0EsRUFkaUIsQ0FjZk0sT0FBT0MsUUFBUCxDQUFnQkMsTUFBaEIsQ0FBdUJDLE1BQXZCLENBQThCLENBQTlCLEVBQWlDTCxLQUFqQyxDQUF1QyxHQUF2QyxDQWRlLENBQWxCOztBQWdCQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSU0sY0FBYyxTQUFkQSxXQUFjLEdBQVc7QUFDNUIsTUFBSWhELFNBQVM0QyxPQUFPQyxRQUFQLENBQWdCSSxJQUFoQixDQUFxQkMsU0FBckIsQ0FBK0IsQ0FBL0IsQ0FBYjs7QUFFQSxNQUFJbEQsV0FBVyxFQUFmLEVBQW1CO0FBQ2xCTyxtQkFBZ0JQLE1BQWhCO0FBQ0E7QUFDRCxFQU5EOztBQVFBLEtBQUltRCxnQkFBZ0IsU0FBaEJBLGFBQWdCLEdBQVc7QUFDOUJoRCxJQUFFLHdDQUFGLEVBQTRDaUQsV0FBNUMsQ0FBd0QsUUFBeEQ7O0FBRUFqRCxJQUFFLElBQUYsRUFBUWtELFFBQVIsQ0FBaUIsUUFBakI7O0FBRUFsRCxJQUFFLFlBQUYsRUFBZ0JtRCxPQUFoQixDQUF3QjtBQUN2QkMsY0FBVztBQURZLEdBQXhCO0FBR0EsRUFSRDs7QUFVQTtBQUNBO0FBQ0E7O0FBRUF2RCxRQUFPd0QsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjVCLE1BQUk2QixJQUFKLENBQVNDLFNBQVQsQ0FBbUJDLE1BQW5CLENBQTBCMUQsTUFBTTBCLElBQU4sQ0FBVyxtQkFBWCxDQUExQixFQUEyRDtBQUMxRCxVQUFPLEdBRG1EO0FBRTFELGdCQUFhLEtBRjZDO0FBRzFELGlCQUFjLElBSDRDO0FBSTFELGlCQUFjLENBQ2I7QUFDQyxlQUFXLENBQUMsQ0FBRCxFQUFJLENBQUosQ0FEWjtBQUVDLGlCQUFhO0FBRmQsSUFEYSxDQUo0QztBQVUxRCxZQUFTO0FBVmlELEdBQTNEOztBQWFBLE1BQUksT0FBT1EsWUFBWXBDLE1BQW5CLEtBQThCLFdBQWxDLEVBQStDO0FBQzlDTyxtQkFBZ0I2QixZQUFZcEMsTUFBNUI7QUFDQSxHQUZELE1BRU87QUFDTkcsS0FBRSx1QkFBRixFQUEyQm9CLEdBQTNCLENBQStCLFlBQS9CLEVBQTZDLFFBQTdDO0FBQ0E7O0FBRUR5Qjs7QUFFQTdDLElBQUV5QyxNQUFGLEVBQVVpQixFQUFWLENBQWEsWUFBYixFQUEyQmIsV0FBM0I7QUFDQTdDLElBQUUsaUNBQUYsRUFBcUMwRCxFQUFyQyxDQUF3QyxPQUF4QyxFQUFpRFYsYUFBakQ7QUFDQWhELElBQUUsb0RBQUYsRUFBd0QwRCxFQUF4RCxDQUEyRCxPQUEzRCxFQUFvRXJDLG9CQUFwRTs7QUFFQWlDO0FBQ0EsRUEzQkQ7O0FBNkJBLFFBQU96RCxNQUFQO0FBQ0EsQ0E1TEYiLCJmaWxlIjoibW9kdWxlX2NlbnRlci9tb2R1bGVfY2VudGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2R1bGVfY2VudGVyLmpzIDIwMTUtMTAtMTkgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIE1vZHVsZSBDZW50ZXJcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGhhbmRsZSB0aGUgY2xpY2sgZXZlbnRzIG9uIHRoZSBtb2R1bGUgY2VudGVyIHBhZ2VcbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL21vZHVsZV9jZW50ZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnbW9kdWxlX2NlbnRlcicsXG5cdFxuXHRbJ2RhdGF0YWJsZSddLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL21vZHVsZV9jZW50ZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9sb2FkTW9kdWxlRGF0YSA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuXHRcdFx0XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHVybDogJ2FkbWluLnBocD9kbz1Nb2R1bGVDZW50ZXIvR2V0RGF0YSZtb2R1bGU9JyArIG1vZHVsZSxcblx0XHRcdFx0XHR0eXBlOiAnR0VUJyxcblx0XHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5zdWNjZXNzKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0XHRpZiAoZGF0YS5zdWNjZXNzKSB7XG5cdFx0XHRcdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtaGVhZGVyIGgyJykuaHRtbChkYXRhLnBheWxvYWQudGl0bGUpO1xuXHRcdFx0XHRcdFx0JCgnLmNvbmZpZ3VyYXRpb24tYm94LWRlc2NyaXB0aW9uIHAnKS5odG1sKGRhdGEucGF5bG9hZC5kZXNjcmlwdGlvbik7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCBhLmJ0bicpLmF0dHIoJ2hyZWYnLCAnYWRtaW4ucGhwP2RvPScgKyBkYXRhLnBheWxvYWQubmFtZSArXG5cdFx0XHRcdFx0XHRcdCdNb2R1bGVDZW50ZXJNb2R1bGUnKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aWYgKGRhdGEucGF5bG9hZC5pc0luc3RhbGxlZCkge1xuXHRcdFx0XHRcdFx0XHQvLyQoJy5neC1jb25maWd1cmF0aW9uLWJveCBmb3JtJykuYXR0cignYWN0aW9uJywgJ2FkbWluLnBocD9kbz1Nb2R1bGVDZW50ZXIvRGVzdHJveScpO1xuXHRcdFx0XHRcdFx0XHQkKCcuZ3gtY29uZmlndXJhdGlvbi1ib3ggZm9ybSBidXR0b25bbmFtZT1cInVuaW5zdGFsbFwiXScpLnNob3coKTtcblx0XHRcdFx0XHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94IGZvcm0gYS5idG4nKS5zaG93KCk7XG5cdFx0XHRcdFx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCBmb3JtIGJ1dHRvbltuYW1lPVwiaW5zdGFsbFwiXScpLmhpZGUoKTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCBmb3JtJykuYXR0cignYWN0aW9uJywgJ2FkbWluLnBocD9kbz1Nb2R1bGVDZW50ZXIvU3RvcmUnKTtcblx0XHRcdFx0XHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94IGZvcm0gYnV0dG9uW25hbWU9XCJ1bmluc3RhbGxcIl0nKS5oaWRlKCk7XG5cdFx0XHRcdFx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCBmb3JtIGEuYnRuJykuaGlkZSgpO1xuXHRcdFx0XHRcdFx0XHQkKCcuZ3gtY29uZmlndXJhdGlvbi1ib3ggZm9ybSBidXR0b25bbmFtZT1cImluc3RhbGxcIl0nKS5zaG93KCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdCQoJy5neC1jb25maWd1cmF0aW9uLWJveCBmb3JtIGlucHV0W25hbWU9XCJtb2R1bGVcIl0nKS52YWwoZGF0YS5wYXlsb2FkLm5hbWUpO1xuXHRcdFx0XHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9vcGVuVW5pbnN0YWxsRGlhbG9nID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHR2YXIgJGRpYWxvZyA9ICQoJyNtb2R1bGUtY2VudGVyLWNvbmZpcm1hdGlvbi1kaWFsb2cnKSxcblx0XHRcdFx0bW9kdWxlID0gJCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuZmluZCgnaW5wdXRbbmFtZT1cIm1vZHVsZVwiXScpLnZhbCgpO1xuXHRcdFx0XG5cdFx0XHQvLyRmb3JtLmF0dHIoJ2FjdGlvbicsICdhZG1pbi5waHA/ZG89TW9kdWxlQ2VudGVyL0Rlc3Ryb3knKTtcblx0XHRcdCRkaWFsb2cuZmluZCgnLm1vZGFsLWluZm8tdGV4dCcpLmh0bWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3RleHRfdW5pbnN0YWxsX2NvbmZpcm1hdGlvbicsXG5cdFx0XHRcdCdtb2R1bGVfY2VudGVyJykpO1xuXHRcdFx0JGRpYWxvZy5maW5kKCdpbnB1dFtuYW1lPVwibW9kdWxlXCJdJykudmFsKG1vZHVsZSk7XG5cdFx0XHRcblx0XHRcdCRkaWFsb2cuZGlhbG9nKHtcblx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3VuaW5zdGFsbF9jb25maXJtYXRpb25fdGl0bGUnLCAnbW9kdWxlX2NlbnRlcicpXG5cdFx0XHRcdFx0LnJlcGxhY2UoJyVzJyxcblx0XHRcdFx0XHRcdG1vZHVsZSksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3VuaW5zdGFsbCcsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkZGlhbG9nLmZpbmQoJ2Zvcm0nKS5zdWJtaXQoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIHF1ZXJ5U3RyaW5nID0gKGZ1bmN0aW9uKGEpIHtcblx0XHRcdGlmIChhID09PSAnJykge1xuXHRcdFx0XHRyZXR1cm4ge307XG5cdFx0XHR9XG5cdFx0XHR2YXIgYiA9IHt9O1xuXHRcdFx0Zm9yICh2YXIgaSA9IDA7IGkgPCBhLmxlbmd0aDsgKytpKSB7XG5cdFx0XHRcdHZhciBwID0gYVtpXS5zcGxpdCgnPScsIDIpO1xuXHRcdFx0XHRpZiAocC5sZW5ndGggPT09IDEpIHtcblx0XHRcdFx0XHRiW3BbMF1dID0gJyc7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0YltwWzBdXSA9IGRlY29kZVVSSUNvbXBvbmVudChwWzFdLnJlcGxhY2UoL1xcKy9nLCAnICcpKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0cmV0dXJuIGI7XG5cdFx0fSkod2luZG93LmxvY2F0aW9uLnNlYXJjaC5zdWJzdHIoMSkuc3BsaXQoJyYnKSk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX2hhc2hDaGFuZ2UgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBtb2R1bGUgPSB3aW5kb3cubG9jYXRpb24uaGFzaC5zdWJzdHJpbmcoMSk7XG5cdFx0XHRcblx0XHRcdGlmIChtb2R1bGUgIT09ICcnKSB7XG5cdFx0XHRcdF9sb2FkTW9kdWxlRGF0YShtb2R1bGUpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zZXRSb3dBY3RpdmUgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5neC1tb2R1bGVzLXRhYmxlIC5kYXRhVGFibGVSb3cuYWN0aXZlJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XG5cdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFxuXHRcdFx0JCgnaHRtbCwgYm9keScpLmFuaW1hdGUoe1xuXHRcdFx0XHRzY3JvbGxUb3A6IDBcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdGpzZS5saWJzLmRhdGF0YWJsZS5jcmVhdGUoJHRoaXMuZmluZCgnLmd4LW1vZHVsZXMtdGFibGUnKSwge1xuXHRcdFx0XHQnZG9tJzogJ3QnLFxuXHRcdFx0XHQnYXV0b1dpZHRoJzogZmFsc2UsXG5cdFx0XHRcdCdwYWdlTGVuZ3RoJzogMTAwMCxcblx0XHRcdFx0J2NvbHVtbkRlZnMnOiBbXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0J3RhcmdldHMnOiBbMSwgMl0sXG5cdFx0XHRcdFx0XHQnb3JkZXJhYmxlJzogZmFsc2Vcblx0XHRcdFx0XHR9XG5cdFx0XHRcdF0sXG5cdFx0XHRcdCdvcmRlcic6IFtdXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0aWYgKHR5cGVvZiBxdWVyeVN0cmluZy5tb2R1bGUgIT09ICd1bmRlZmluZWQnKSB7XG5cdFx0XHRcdF9sb2FkTW9kdWxlRGF0YShxdWVyeVN0cmluZy5tb2R1bGUpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JCgnLmd4LWNvbmZpZ3VyYXRpb24tYm94JykuY3NzKCd2aXNpYmlsaXR5JywgJ2hpZGRlbicpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRfaGFzaENoYW5nZSgpO1xuXHRcdFx0XG5cdFx0XHQkKHdpbmRvdykub24oJ2hhc2hjaGFuZ2UnLCBfaGFzaENoYW5nZSk7XG5cdFx0XHQkKCcuZ3gtbW9kdWxlcy10YWJsZSAuZGF0YVRhYmxlUm93Jykub24oJ2NsaWNrJywgX3NldFJvd0FjdGl2ZSk7XG5cdFx0XHQkKCcuY29uZmlndXJhdGlvbi1ib3gtZm9vdGVyIGJ1dHRvbltuYW1lPVwidW5pbnN0YWxsXCJdJykub24oJ2NsaWNrJywgX29wZW5Vbmluc3RhbGxEaWFsb2cpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
