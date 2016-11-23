'use strict';

/* --------------------------------------------------------------
 tooltip_icon.js 2016-02-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Tooltip Icon Widget
 *
 * This widget will automatically transform the following markup to an icon widget.
 *
 * ### Options
 *
 * **Type | `data-tooltip_icon-type` | String | Optional**
 *
 * The type of the tooltip icon. Possible options are `'info'` and `'warning'`.
 *
 * ### Example
 *
 * ```html
 * <div class="gx-container" style="width:50px">
 *   <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="warning">
 *     This is the tooltip content of the warning tooltip icon.
 *   </span>
 *   <span data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
 *     This is the tooltip content of the info tooltip icon.
 *   </span>
 * </div>
 * ```
 * **Note:** Currently, the wrapping `<div>` of the tooltip icon widget, has to have a CSS-Style
 * of `50px`.
 * 
 * @todo Make sure to set the width automatically. Currently, a style of 50px has to be applied manually.
 * @module Admin/Widgets/tooltip_icon
 */
gx.widgets.module('tooltip_icon', [],

/**  @lends module:Widgets/tooltip_icon */

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
	defaults = {
		type: 'info'
	},


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
  * Gets the content and tries to add the
  * images at "Configuration > Image-Options" to the content.
  * @returns {String | HTML}
  */
	var _getContent = function _getContent() {
		// Is this from a configuration.php row?
		var $parentConfigRow = $this.parents('[data-config-key]:first');
		var isConfigRow = !!$parentConfigRow.length;

		// Try to get image and append it to the tooltip description
		if (isConfigRow) {
			var $image = $parentConfigRow.find('img:first');
			var hasImage = !!$image.length;

			if (hasImage) {
				$this.append('<br><br>');
				$this.append($image);
			}
		}

		return $this.html();
	};

	/**
  * Get the image tag element selector for the widget.
  *
  * This method will return a different image depending on the provided type option.
  */
	var _getImageElement = function _getImageElement() {
		var $icon;

		switch (options.type) {
			case 'warning':
				$icon = $('<span class="gx-container tooltip_icon pull-left ' + options.type + '">' + '<i class="fa fa-exclamation-triangle"></i>' + '</span>');
				break;
			case 'info':
				$icon = $('<span class="gx-container tooltip_icon ' + options.type + '">' + '<i class="fa fa-info-circle"></i>' + '</span>');
				break;
		}

		$icon.qtip({
			content: _getContent(),
			style: {
				classes: 'gx-container gx-qtip ' + options.type // use the type as a class for styling
			},
			position: {
				my: options.type === 'warning' ? 'bottom left' : 'left center',
				at: options.type === 'warning' ? 'top left' : 'right center'
			},
			hide: { // Delay the tooltip hide by 300ms so that users can interact with it.
				fixed: true,
				delay: 300
			}
		});

		return $icon;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		if ($this.text().replace(/\s+/, '') !== '') {
			var $icon = _getImageElement();

			$this.text('');

			$icon.appendTo($this);
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRvb2x0aXBfaWNvbi5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJ0eXBlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9nZXRDb250ZW50IiwiJHBhcmVudENvbmZpZ1JvdyIsInBhcmVudHMiLCJpc0NvbmZpZ1JvdyIsImxlbmd0aCIsIiRpbWFnZSIsImZpbmQiLCJoYXNJbWFnZSIsImFwcGVuZCIsImh0bWwiLCJfZ2V0SW1hZ2VFbGVtZW50IiwiJGljb24iLCJxdGlwIiwiY29udGVudCIsInN0eWxlIiwiY2xhc3NlcyIsInBvc2l0aW9uIiwibXkiLCJhdCIsImhpZGUiLCJmaXhlZCIsImRlbGF5IiwiaW5pdCIsImRvbmUiLCJ0ZXh0IiwicmVwbGFjZSIsImFwcGVuZFRvIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBNkJBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxjQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVztBQUNWQyxRQUFNO0FBREksRUFiWjs7O0FBaUJDOzs7OztBQUtBQyxXQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQXRCWDs7O0FBd0JDOzs7OztBQUtBRCxVQUFTLEVBN0JWOztBQStCQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0EsS0FBSVEsY0FBYyxTQUFkQSxXQUFjLEdBQVc7QUFDNUI7QUFDQSxNQUFJQyxtQkFBbUJQLE1BQU1RLE9BQU4sQ0FBYyx5QkFBZCxDQUF2QjtBQUNBLE1BQUlDLGNBQWMsQ0FBQyxDQUFDRixpQkFBaUJHLE1BQXJDOztBQUVBO0FBQ0EsTUFBSUQsV0FBSixFQUFpQjtBQUNoQixPQUFJRSxTQUFTSixpQkFBaUJLLElBQWpCLENBQXNCLFdBQXRCLENBQWI7QUFDQSxPQUFJQyxXQUFXLENBQUMsQ0FBQ0YsT0FBT0QsTUFBeEI7O0FBRUEsT0FBSUcsUUFBSixFQUFjO0FBQ2JiLFVBQU1jLE1BQU4sQ0FBYSxVQUFiO0FBQ0FkLFVBQU1jLE1BQU4sQ0FBYUgsTUFBYjtBQUNBO0FBQ0Q7O0FBRUQsU0FBT1gsTUFBTWUsSUFBTixFQUFQO0FBRUEsRUFsQkQ7O0FBb0JBOzs7OztBQUtBLEtBQUlDLG1CQUFtQixTQUFuQkEsZ0JBQW1CLEdBQVc7QUFDakMsTUFBSUMsS0FBSjs7QUFFQSxVQUFRYixRQUFRRCxJQUFoQjtBQUNDLFFBQUssU0FBTDtBQUNDYyxZQUFRaEIsRUFBRSxzREFBc0RHLFFBQVFELElBQTlELEdBQXFFLElBQXJFLEdBQ1QsNENBRFMsR0FFVCxTQUZPLENBQVI7QUFHQTtBQUNELFFBQUssTUFBTDtBQUNDYyxZQUFRaEIsRUFBRSw0Q0FBNENHLFFBQVFELElBQXBELEdBQTJELElBQTNELEdBQ1QsbUNBRFMsR0FFVCxTQUZPLENBQVI7QUFHQTtBQVZGOztBQWFBYyxRQUFNQyxJQUFOLENBQVc7QUFDVkMsWUFBU2IsYUFEQztBQUVWYyxVQUFPO0FBQ05DLGFBQVMsMEJBQTBCakIsUUFBUUQsSUFEckMsQ0FDMEM7QUFEMUMsSUFGRztBQUtWbUIsYUFBVTtBQUNUQyxRQUFJbkIsUUFBUUQsSUFBUixLQUFpQixTQUFqQixHQUE2QixhQUE3QixHQUE2QyxhQUR4QztBQUVUcUIsUUFBSXBCLFFBQVFELElBQVIsS0FBaUIsU0FBakIsR0FBNkIsVUFBN0IsR0FBMEM7QUFGckMsSUFMQTtBQVNWc0IsU0FBTSxFQUFFO0FBQ1BDLFdBQU8sSUFERjtBQUVMQyxXQUFPO0FBRkY7QUFUSSxHQUFYOztBQWVBLFNBQU9WLEtBQVA7QUFDQSxFQWhDRDs7QUFrQ0E7QUFDQTtBQUNBOztBQUVBbkIsUUFBTzhCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCLE1BQUk3QixNQUFNOEIsSUFBTixHQUFhQyxPQUFiLENBQXFCLEtBQXJCLEVBQTRCLEVBQTVCLE1BQW9DLEVBQXhDLEVBQTRDO0FBQzNDLE9BQUlkLFFBQVFELGtCQUFaOztBQUVBaEIsU0FBTThCLElBQU4sQ0FBVyxFQUFYOztBQUVBYixTQUFNZSxRQUFOLENBQWVoQyxLQUFmO0FBQ0E7O0FBRUQ2QjtBQUNBLEVBWEQ7O0FBYUEsUUFBTy9CLE1BQVA7QUFDQSxDQXBJRiIsImZpbGUiOiJ0b29sdGlwX2ljb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRvb2x0aXBfaWNvbi5qcyAyMDE2LTAyLTE5IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBUb29sdGlwIEljb24gV2lkZ2V0XG4gKlxuICogVGhpcyB3aWRnZXQgd2lsbCBhdXRvbWF0aWNhbGx5IHRyYW5zZm9ybSB0aGUgZm9sbG93aW5nIG1hcmt1cCB0byBhbiBpY29uIHdpZGdldC5cbiAqXG4gKiAjIyMgT3B0aW9uc1xuICpcbiAqICoqVHlwZSB8IGBkYXRhLXRvb2x0aXBfaWNvbi10eXBlYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqXG4gKiBUaGUgdHlwZSBvZiB0aGUgdG9vbHRpcCBpY29uLiBQb3NzaWJsZSBvcHRpb25zIGFyZSBgJ2luZm8nYCBhbmQgYCd3YXJuaW5nJ2AuXG4gKlxuICogIyMjIEV4YW1wbGVcbiAqXG4gKiBgYGBodG1sXG4gKiA8ZGl2IGNsYXNzPVwiZ3gtY29udGFpbmVyXCIgc3R5bGU9XCJ3aWR0aDo1MHB4XCI+XG4gKiAgIDxzcGFuIGRhdGEtZ3gtd2lkZ2V0PVwidG9vbHRpcF9pY29uXCIgZGF0YS10b29sdGlwX2ljb24tdHlwZT1cIndhcm5pbmdcIj5cbiAqICAgICBUaGlzIGlzIHRoZSB0b29sdGlwIGNvbnRlbnQgb2YgdGhlIHdhcm5pbmcgdG9vbHRpcCBpY29uLlxuICogICA8L3NwYW4+XG4gKiAgIDxzcGFuIGRhdGEtZ3gtd2lkZ2V0PVwidG9vbHRpcF9pY29uXCIgZGF0YS10b29sdGlwX2ljb24tdHlwZT1cImluZm9cIj5cbiAqICAgICBUaGlzIGlzIHRoZSB0b29sdGlwIGNvbnRlbnQgb2YgdGhlIGluZm8gdG9vbHRpcCBpY29uLlxuICogICA8L3NwYW4+XG4gKiA8L2Rpdj5cbiAqIGBgYFxuICogKipOb3RlOioqIEN1cnJlbnRseSwgdGhlIHdyYXBwaW5nIGA8ZGl2PmAgb2YgdGhlIHRvb2x0aXAgaWNvbiB3aWRnZXQsIGhhcyB0byBoYXZlIGEgQ1NTLVN0eWxlXG4gKiBvZiBgNTBweGAuXG4gKiBcbiAqIEB0b2RvIE1ha2Ugc3VyZSB0byBzZXQgdGhlIHdpZHRoIGF1dG9tYXRpY2FsbHkuIEN1cnJlbnRseSwgYSBzdHlsZSBvZiA1MHB4IGhhcyB0byBiZSBhcHBsaWVkIG1hbnVhbGx5LlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL3Rvb2x0aXBfaWNvblxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0J3Rvb2x0aXBfaWNvbicsXG5cdFxuXHRbXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpXaWRnZXRzL3Rvb2x0aXBfaWNvbiAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0dHlwZTogJ2luZm8nXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXRzIHRoZSBjb250ZW50IGFuZCB0cmllcyB0byBhZGQgdGhlXG5cdFx0ICogaW1hZ2VzIGF0IFwiQ29uZmlndXJhdGlvbiA+IEltYWdlLU9wdGlvbnNcIiB0byB0aGUgY29udGVudC5cblx0XHQgKiBAcmV0dXJucyB7U3RyaW5nIHwgSFRNTH1cblx0XHQgKi9cblx0XHR2YXIgX2dldENvbnRlbnQgPSBmdW5jdGlvbigpIHtcblx0XHRcdC8vIElzIHRoaXMgZnJvbSBhIGNvbmZpZ3VyYXRpb24ucGhwIHJvdz9cblx0XHRcdHZhciAkcGFyZW50Q29uZmlnUm93ID0gJHRoaXMucGFyZW50cygnW2RhdGEtY29uZmlnLWtleV06Zmlyc3QnKTtcblx0XHRcdHZhciBpc0NvbmZpZ1JvdyA9ICEhJHBhcmVudENvbmZpZ1Jvdy5sZW5ndGg7XG5cdFx0XHRcblx0XHRcdC8vIFRyeSB0byBnZXQgaW1hZ2UgYW5kIGFwcGVuZCBpdCB0byB0aGUgdG9vbHRpcCBkZXNjcmlwdGlvblxuXHRcdFx0aWYgKGlzQ29uZmlnUm93KSB7XG5cdFx0XHRcdHZhciAkaW1hZ2UgPSAkcGFyZW50Q29uZmlnUm93LmZpbmQoJ2ltZzpmaXJzdCcpO1xuXHRcdFx0XHR2YXIgaGFzSW1hZ2UgPSAhISRpbWFnZS5sZW5ndGg7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoaGFzSW1hZ2UpIHtcblx0XHRcdFx0XHQkdGhpcy5hcHBlbmQoJzxicj48YnI+Jyk7XG5cdFx0XHRcdFx0JHRoaXMuYXBwZW5kKCRpbWFnZSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0cmV0dXJuICR0aGlzLmh0bWwoKTtcblx0XHRcdFxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogR2V0IHRoZSBpbWFnZSB0YWcgZWxlbWVudCBzZWxlY3RvciBmb3IgdGhlIHdpZGdldC5cblx0XHQgKlxuXHRcdCAqIFRoaXMgbWV0aG9kIHdpbGwgcmV0dXJuIGEgZGlmZmVyZW50IGltYWdlIGRlcGVuZGluZyBvbiB0aGUgcHJvdmlkZWQgdHlwZSBvcHRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9nZXRJbWFnZUVsZW1lbnQgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkaWNvbjtcblx0XHRcdFxuXHRcdFx0c3dpdGNoIChvcHRpb25zLnR5cGUpIHtcblx0XHRcdFx0Y2FzZSAnd2FybmluZyc6XG5cdFx0XHRcdFx0JGljb24gPSAkKCc8c3BhbiBjbGFzcz1cImd4LWNvbnRhaW5lciB0b29sdGlwX2ljb24gcHVsbC1sZWZ0ICcgKyBvcHRpb25zLnR5cGUgKyAnXCI+JyArXG5cdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJmYSBmYS1leGNsYW1hdGlvbi10cmlhbmdsZVwiPjwvaT4nICtcblx0XHRcdFx0XHRcdCc8L3NwYW4+Jyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2luZm8nOlxuXHRcdFx0XHRcdCRpY29uID0gJCgnPHNwYW4gY2xhc3M9XCJneC1jb250YWluZXIgdG9vbHRpcF9pY29uICcgKyBvcHRpb25zLnR5cGUgKyAnXCI+JyArXG5cdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJmYSBmYS1pbmZvLWNpcmNsZVwiPjwvaT4nICtcblx0XHRcdFx0XHRcdCc8L3NwYW4+Jyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCRpY29uLnF0aXAoe1xuXHRcdFx0XHRjb250ZW50OiBfZ2V0Q29udGVudCgpLFxuXHRcdFx0XHRzdHlsZToge1xuXHRcdFx0XHRcdGNsYXNzZXM6ICdneC1jb250YWluZXIgZ3gtcXRpcCAnICsgb3B0aW9ucy50eXBlIC8vIHVzZSB0aGUgdHlwZSBhcyBhIGNsYXNzIGZvciBzdHlsaW5nXG5cdFx0XHRcdH0sXG5cdFx0XHRcdHBvc2l0aW9uOiB7XG5cdFx0XHRcdFx0bXk6IG9wdGlvbnMudHlwZSA9PT0gJ3dhcm5pbmcnID8gJ2JvdHRvbSBsZWZ0JyA6ICdsZWZ0IGNlbnRlcicsXG5cdFx0XHRcdFx0YXQ6IG9wdGlvbnMudHlwZSA9PT0gJ3dhcm5pbmcnID8gJ3RvcCBsZWZ0JyA6ICdyaWdodCBjZW50ZXInXG5cdFx0XHRcdH0sXG5cdFx0XHRcdGhpZGU6IHsgLy8gRGVsYXkgdGhlIHRvb2x0aXAgaGlkZSBieSAzMDBtcyBzbyB0aGF0IHVzZXJzIGNhbiBpbnRlcmFjdCB3aXRoIGl0LlxuXHRcdFx0XHRcdGZpeGVkOiB0cnVlLFxuXHRcdFx0XHRcdGRlbGF5OiAzMDBcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiAkaWNvbjtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdGlmICgkdGhpcy50ZXh0KCkucmVwbGFjZSgvXFxzKy8sICcnKSAhPT0gJycpIHtcblx0XHRcdFx0dmFyICRpY29uID0gX2dldEltYWdlRWxlbWVudCgpO1xuXHRcdFx0XHRcblx0XHRcdFx0JHRoaXMudGV4dCgnJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkaWNvbi5hcHBlbmRUbygkdGhpcyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
