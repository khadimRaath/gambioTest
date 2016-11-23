'use strict';

/* --------------------------------------------------------------
 category_menu 2016-09-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.compatibility.module('category_menu', [], function (data) {

	'use strict';

	var $this = $(this),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	var $catMenuTopSwitcher = $('input:checkbox[name="CAT_MENU_TOP"]');
	var $catMenuLeftSwitcher = $('input:checkbox[name="CAT_MENU_LEFT"]');
	var $showSubcategoriesSwitcher = $('input:checkbox[name="SHOW_SUBCATEGORIES"]');

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	function _onCatMenuTopSwitcherChange() {
		if ($catMenuTopSwitcher.prop('checked') === false) {
			$catMenuLeftSwitcher.parent().addClass('checked disabled');
			$showSubcategoriesSwitcher.parent().addClass('disabled').removeClass('checked');

			$catMenuLeftSwitcher.prop('checked', true);
			$showSubcategoriesSwitcher.prop('checked', false);
		} else {
			$catMenuLeftSwitcher.parent().removeClass('disabled');
			$showSubcategoriesSwitcher.parent().removeClass('disabled');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------


	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		$this.on('checkbox:change', $catMenuTopSwitcher, _onCatMenuTopSwitcherChange);

		$(document).on('JSENGINE_INIT_FINISHED', function () {
			_onCatMenuTopSwitcherChange();
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRlbXBsYXRlX2NvbmZpZ3VyYXRpb24vY2F0ZWdvcnlfbWVudS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGNhdE1lbnVUb3BTd2l0Y2hlciIsIiRjYXRNZW51TGVmdFN3aXRjaGVyIiwiJHNob3dTdWJjYXRlZ29yaWVzU3dpdGNoZXIiLCJfb25DYXRNZW51VG9wU3dpdGNoZXJDaGFuZ2UiLCJwcm9wIiwicGFyZW50IiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImluaXQiLCJkb25lIiwib24iLCJkb2N1bWVudCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUF3QixlQUF4QixFQUF5QyxFQUF6QyxFQUE2QyxVQUFTQyxJQUFULEVBQWU7O0FBRTNEOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaOzs7QUFFQzs7Ozs7QUFLQUgsVUFBUyxFQVBWOztBQVNBLEtBQU1JLHNCQUFzQkQsRUFBRSxxQ0FBRixDQUE1QjtBQUNBLEtBQU1FLHVCQUF1QkYsRUFBRSxzQ0FBRixDQUE3QjtBQUNBLEtBQU1HLDZCQUE2QkgsRUFBRSwyQ0FBRixDQUFuQzs7QUFFQTtBQUNBO0FBQ0E7O0FBRUEsVUFBU0ksMkJBQVQsR0FBdUM7QUFDdEMsTUFBSUgsb0JBQW9CSSxJQUFwQixDQUF5QixTQUF6QixNQUF3QyxLQUE1QyxFQUFtRDtBQUNsREgsd0JBQXFCSSxNQUFyQixHQUE4QkMsUUFBOUIsQ0FBdUMsa0JBQXZDO0FBQ0FKLDhCQUEyQkcsTUFBM0IsR0FBb0NDLFFBQXBDLENBQTZDLFVBQTdDLEVBQXlEQyxXQUF6RCxDQUFxRSxTQUFyRTs7QUFFQU4sd0JBQXFCRyxJQUFyQixDQUEwQixTQUExQixFQUFxQyxJQUFyQztBQUNBRiw4QkFBMkJFLElBQTNCLENBQWdDLFNBQWhDLEVBQTJDLEtBQTNDO0FBQ0EsR0FORCxNQU1PO0FBQ05ILHdCQUFxQkksTUFBckIsR0FBOEJFLFdBQTlCLENBQTBDLFVBQTFDO0FBQ0FMLDhCQUEyQkcsTUFBM0IsR0FBb0NFLFdBQXBDLENBQWdELFVBQWhEO0FBQ0E7QUFDRDs7QUFHRDtBQUNBO0FBQ0E7OztBQUdBOzs7QUFHQVgsUUFBT1ksSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QlgsUUFBTVksRUFBTixDQUFTLGlCQUFULEVBQTRCVixtQkFBNUIsRUFBaURHLDJCQUFqRDs7QUFFQUosSUFBRVksUUFBRixFQUFZRCxFQUFaLENBQWUsd0JBQWYsRUFBeUMsWUFBVztBQUNuRFA7QUFDQSxHQUZEOztBQUtBTTtBQUNBLEVBVEQ7O0FBV0E7QUFDQSxRQUFPYixNQUFQO0FBQ0EsQ0F4REQiLCJmaWxlIjoidGVtcGxhdGVfY29uZmlndXJhdGlvbi9jYXRlZ29yeV9tZW51LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjYXRlZ29yeV9tZW51IDIwMTYtMDktMjJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZSgnY2F0ZWdvcnlfbWVudScsIFtdLCBmdW5jdGlvbihkYXRhKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0ICovXG5cdFx0bW9kdWxlID0ge307XG5cdFxuXHRjb25zdCAkY2F0TWVudVRvcFN3aXRjaGVyID0gJCgnaW5wdXQ6Y2hlY2tib3hbbmFtZT1cIkNBVF9NRU5VX1RPUFwiXScpO1xuXHRjb25zdCAkY2F0TWVudUxlZnRTd2l0Y2hlciA9ICQoJ2lucHV0OmNoZWNrYm94W25hbWU9XCJDQVRfTUVOVV9MRUZUXCJdJyk7XG5cdGNvbnN0ICRzaG93U3ViY2F0ZWdvcmllc1N3aXRjaGVyID0gJCgnaW5wdXQ6Y2hlY2tib3hbbmFtZT1cIlNIT1dfU1VCQ0FURUdPUklFU1wiXScpO1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIEVWRU5UIEhBTkRMRVJTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0ZnVuY3Rpb24gX29uQ2F0TWVudVRvcFN3aXRjaGVyQ2hhbmdlKCkge1xuXHRcdGlmICgkY2F0TWVudVRvcFN3aXRjaGVyLnByb3AoJ2NoZWNrZWQnKSA9PT0gZmFsc2UpIHtcblx0XHRcdCRjYXRNZW51TGVmdFN3aXRjaGVyLnBhcmVudCgpLmFkZENsYXNzKCdjaGVja2VkIGRpc2FibGVkJyk7XG5cdFx0XHQkc2hvd1N1YmNhdGVnb3JpZXNTd2l0Y2hlci5wYXJlbnQoKS5hZGRDbGFzcygnZGlzYWJsZWQnKS5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XG5cdFx0XHQkY2F0TWVudUxlZnRTd2l0Y2hlci5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHQkc2hvd1N1YmNhdGVnb3JpZXNTd2l0Y2hlci5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHQkY2F0TWVudUxlZnRTd2l0Y2hlci5wYXJlbnQoKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblx0XHRcdCRzaG93U3ViY2F0ZWdvcmllc1N3aXRjaGVyLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXHRcdH1cblx0fVxuXHRcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBJTklUSUFMSVpBVElPTlxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdCAqL1xuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHQkdGhpcy5vbignY2hlY2tib3g6Y2hhbmdlJywgJGNhdE1lbnVUb3BTd2l0Y2hlciwgX29uQ2F0TWVudVRvcFN3aXRjaGVyQ2hhbmdlKTtcblx0XHRcblx0XHQkKGRvY3VtZW50KS5vbignSlNFTkdJTkVfSU5JVF9GSU5JU0hFRCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0X29uQ2F0TWVudVRvcFN3aXRjaGVyQ2hhbmdlKCk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXHRcblx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
