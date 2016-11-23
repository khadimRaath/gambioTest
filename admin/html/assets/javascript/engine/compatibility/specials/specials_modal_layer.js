'use strict';

/* --------------------------------------------------------------
 specials_modal_layer.js 2015-09-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Lang */

/**
 * ## Specials Modal Layer Module
 *
 * This module will open a modal layer for order actions like deleting or changing the oder status.
 *
 * @module Compatibility/specials_modal_layer
 */
gx.compatibility.module('specials_modal_layer', [],

/**  @lends module:Compatibility/specials_modal_layer */

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
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#modal_layer_container'),


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
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _openDeleteDialog = function _openDeleteDialog(event) {

		var $form = $('#delete_confirm_form');
		var stringPos = $form.attr('action').indexOf('&sID=');

		if (stringPos !== -1) {
			$form.attr('action', $form.attr('action').substr(0, stringPos));
		}

		$form.attr('action', $form.attr('action') + '&sID=' + options.special_id);

		$form.find('.product-name').html(options.name);

		event.preventDefault();
		$form.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_SPECIALS', 'admin_specials'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 420
		});
	};

	var _getModalButtons = function _getModalButtons($form) {
		var buttons = [{
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
			}
		}, {
			'text': jse.core.lang.translate('delete', 'buttons'),
			'class': 'btn btn-primary',
			'click': function click() {
				$form.submit();
			}
		}];

		return buttons;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', _openDeleteDialog);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNwZWNpYWxzL3NwZWNpYWxzX21vZGFsX2xheWVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkbW9kYWwiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfb3BlbkRlbGV0ZURpYWxvZyIsImV2ZW50IiwiJGZvcm0iLCJzdHJpbmdQb3MiLCJhdHRyIiwiaW5kZXhPZiIsInN1YnN0ciIsInNwZWNpYWxfaWQiLCJmaW5kIiwiaHRtbCIsIm5hbWUiLCJwcmV2ZW50RGVmYXVsdCIsImRpYWxvZyIsImpzZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwiX2dldE1vZGFsQnV0dG9ucyIsImJ1dHRvbnMiLCJzdWJtaXQiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7O0FBRUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxzQkFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFVBQVNELEVBQUUsd0JBQUYsQ0FiVjs7O0FBZUM7Ozs7O0FBS0FFLFlBQVcsRUFwQlo7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkosSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7QUFvQ0E7QUFDQTtBQUNBOztBQUVBLEtBQUlRLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVNDLEtBQVQsRUFBZ0I7O0FBRXZDLE1BQUlDLFFBQVFQLEVBQUUsc0JBQUYsQ0FBWjtBQUNBLE1BQUlRLFlBQVlELE1BQU1FLElBQU4sQ0FBVyxRQUFYLEVBQXFCQyxPQUFyQixDQUE2QixPQUE3QixDQUFoQjs7QUFFQSxNQUFJRixjQUFjLENBQUMsQ0FBbkIsRUFBc0I7QUFDckJELFNBQU1FLElBQU4sQ0FBVyxRQUFYLEVBQXFCRixNQUFNRSxJQUFOLENBQVcsUUFBWCxFQUFxQkUsTUFBckIsQ0FBNEIsQ0FBNUIsRUFBK0JILFNBQS9CLENBQXJCO0FBQ0E7O0FBRURELFFBQU1FLElBQU4sQ0FBVyxRQUFYLEVBQXFCRixNQUFNRSxJQUFOLENBQVcsUUFBWCxJQUF1QixPQUF2QixHQUFpQ04sUUFBUVMsVUFBOUQ7O0FBRUFMLFFBQU1NLElBQU4sQ0FBVyxlQUFYLEVBQTRCQyxJQUE1QixDQUFpQ1gsUUFBUVksSUFBekM7O0FBRUFULFFBQU1VLGNBQU47QUFDQVQsUUFBTVUsTUFBTixDQUFhO0FBQ1osWUFBU0MsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsbUNBQXhCLEVBQTZELGdCQUE3RCxDQURHO0FBRVosWUFBUyxJQUZHO0FBR1osa0JBQWUsY0FISDtBQUlaLGNBQVdDLGlCQUFpQmYsS0FBakIsQ0FKQztBQUtaLFlBQVM7QUFMRyxHQUFiO0FBUUEsRUF0QkQ7O0FBd0JBLEtBQUllLG1CQUFtQixTQUFuQkEsZ0JBQW1CLENBQVNmLEtBQVQsRUFBZ0I7QUFDdEMsTUFBSWdCLFVBQVUsQ0FDYjtBQUNDLFdBQVFMLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLE9BQXhCLEVBQWlDLFNBQWpDLENBRFQ7QUFFQyxZQUFTLEtBRlY7QUFHQyxZQUFTLGlCQUFXO0FBQ25CckIsTUFBRSxJQUFGLEVBQVFpQixNQUFSLENBQWUsT0FBZjtBQUNBO0FBTEYsR0FEYSxFQVFiO0FBQ0MsV0FBUUMsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsUUFBeEIsRUFBa0MsU0FBbEMsQ0FEVDtBQUVDLFlBQVMsaUJBRlY7QUFHQyxZQUFTLGlCQUFXO0FBQ25CZCxVQUFNaUIsTUFBTjtBQUNBO0FBTEYsR0FSYSxDQUFkOztBQWlCQSxTQUFPRCxPQUFQO0FBQ0EsRUFuQkQ7O0FBcUJBO0FBQ0E7QUFDQTs7QUFFQTFCLFFBQU80QixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCM0IsUUFBTTRCLEVBQU4sQ0FBUyxPQUFULEVBQWtCdEIsaUJBQWxCO0FBQ0FxQjtBQUNBLEVBSEQ7O0FBS0EsUUFBTzdCLE1BQVA7QUFDQSxDQTlHRiIsImZpbGUiOiJzcGVjaWFscy9zcGVjaWFsc19tb2RhbF9sYXllci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc3BlY2lhbHNfbW9kYWxfbGF5ZXIuanMgMjAxNS0wOS0yNFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgTGFuZyAqL1xuXG4vKipcbiAqICMjIFNwZWNpYWxzIE1vZGFsIExheWVyIE1vZHVsZVxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgb3BlbiBhIG1vZGFsIGxheWVyIGZvciBvcmRlciBhY3Rpb25zIGxpa2UgZGVsZXRpbmcgb3IgY2hhbmdpbmcgdGhlIG9kZXIgc3RhdHVzLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9zcGVjaWFsc19tb2RhbF9sYXllclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J3NwZWNpYWxzX21vZGFsX2xheWVyJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvc3BlY2lhbHNfbW9kYWxfbGF5ZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kYWwgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkbW9kYWwgPSAkKCcjbW9kYWxfbGF5ZXJfY29udGFpbmVyJyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX29wZW5EZWxldGVEaWFsb2cgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XG5cdFx0XHR2YXIgJGZvcm0gPSAkKCcjZGVsZXRlX2NvbmZpcm1fZm9ybScpO1xuXHRcdFx0dmFyIHN0cmluZ1BvcyA9ICRmb3JtLmF0dHIoJ2FjdGlvbicpLmluZGV4T2YoJyZzSUQ9Jyk7XG5cdFx0XHRcblx0XHRcdGlmIChzdHJpbmdQb3MgIT09IC0xKSB7XG5cdFx0XHRcdCRmb3JtLmF0dHIoJ2FjdGlvbicsICRmb3JtLmF0dHIoJ2FjdGlvbicpLnN1YnN0cigwLCBzdHJpbmdQb3MpKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JGZvcm0uYXR0cignYWN0aW9uJywgJGZvcm0uYXR0cignYWN0aW9uJykgKyAnJnNJRD0nICsgb3B0aW9ucy5zcGVjaWFsX2lkKTtcblx0XHRcdFxuXHRcdFx0JGZvcm0uZmluZCgnLnByb2R1Y3QtbmFtZScpLmh0bWwob3B0aW9ucy5uYW1lKTtcblx0XHRcdFxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdCRmb3JtLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19ERUxFVEVfU1BFQ0lBTFMnLCAnYWRtaW5fc3BlY2lhbHMnKSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdCdidXR0b25zJzogX2dldE1vZGFsQnV0dG9ucygkZm9ybSksXG5cdFx0XHRcdCd3aWR0aCc6IDQyMFxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfZ2V0TW9kYWxCdXR0b25zID0gZnVuY3Rpb24oJGZvcm0pIHtcblx0XHRcdHZhciBidXR0b25zID0gW1xuXHRcdFx0XHR7XG5cdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9LFxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0XTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIGJ1dHRvbnM7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29wZW5EZWxldGVEaWFsb2cpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
