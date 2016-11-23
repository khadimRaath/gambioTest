'use strict';

/* --------------------------------------------------------------
 product_tooltip.js 2016-03-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Product Tooltip
 *
 * This controller displays a tooltip when hovering the product name.
 *
 * Use attribute 'data-product_tooltip-image-url' to set the image url to load.
 * Use attribute 'data-product_tooltip-description' to set the text content.
 *
 * @module Controllers/product_tooltip
 */
gx.controllers.module('product_tooltip', [],

/**  @lends module:Controllers/product_tooltip */

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
  * Module Object
  *
  * @type {object}
  */
	module = {},


	/**
  * qTip plugin options object.
  * @type {Object}
  */
	tooltipOptions = {
		style: {
			classes: 'gx-container gx-qtip info large'
		},
		position: {
			my: 'left top',
			at: 'right bottom'
		}
	},


	/**
  * Hover trigger.
  * @type {jQuery}
  */
	$trigger = $this.find('[data-tooltip-trigger]');

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Prepares the tooltip content by fetching the image and setting the description.
  * @param {jQuery.Event} event Hover event.
  * @param {Object} api qTip plugin internal API.
  * @private
  */
	var _getContent = function _getContent(event, api) {
		var $content = $('<div/>');

		var $description = $.parseHTML(data.description);
		$content.append($description);

		// Fetch image.
		var image = new Image();
		$(image).load(function () {
			var $imageContainer = $('<div class="text-center" style="margin-bottom: 24px;"></div>');

			$imageContainer.append($('<br>')).prepend(image);

			$content.prepend($imageContainer);

			// Set new tooltip content.
			api.set('content.text', $content);
		}).attr({ src: data.imageUrl });

		return $content;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		if ($trigger.length) {
			var options = $.extend(true, tooltipOptions, { content: { text: _getContent } });
			$trigger.qtip(options);
		} else {
			throw new Error('Could not find trigger element');
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3QvcHJvZHVjdF90b29sdGlwLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwidG9vbHRpcE9wdGlvbnMiLCJzdHlsZSIsImNsYXNzZXMiLCJwb3NpdGlvbiIsIm15IiwiYXQiLCIkdHJpZ2dlciIsImZpbmQiLCJfZ2V0Q29udGVudCIsImV2ZW50IiwiYXBpIiwiJGNvbnRlbnQiLCIkZGVzY3JpcHRpb24iLCJwYXJzZUhUTUwiLCJkZXNjcmlwdGlvbiIsImFwcGVuZCIsImltYWdlIiwiSW1hZ2UiLCJsb2FkIiwiJGltYWdlQ29udGFpbmVyIiwicHJlcGVuZCIsInNldCIsImF0dHIiLCJzcmMiLCJpbWFnZVVybCIsImluaXQiLCJkb25lIiwibGVuZ3RoIiwib3B0aW9ucyIsImV4dGVuZCIsImNvbnRlbnQiLCJ0ZXh0IiwicXRpcCIsIkVycm9yIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7QUFVQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0MsaUJBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVVDLElBQVYsRUFBZ0I7O0FBRWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUgsVUFBUyxFQWJWOzs7QUFlQzs7OztBQUlBSSxrQkFBaUI7QUFDaEJDLFNBQU87QUFDTkMsWUFBUztBQURILEdBRFM7QUFJaEJDLFlBQVU7QUFDVEMsT0FBSSxVQURLO0FBRVRDLE9BQUk7QUFGSztBQUpNLEVBbkJsQjs7O0FBNkJDOzs7O0FBSUFDLFlBQVdSLE1BQU1TLElBQU4sQ0FBVyx3QkFBWCxDQWpDWjs7QUFtQ0E7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFNQSxLQUFJQyxjQUFjLFNBQWRBLFdBQWMsQ0FBVUMsS0FBVixFQUFpQkMsR0FBakIsRUFBc0I7QUFDdkMsTUFBSUMsV0FBV1osRUFBRSxRQUFGLENBQWY7O0FBRUEsTUFBSWEsZUFBZWIsRUFBRWMsU0FBRixDQUFZaEIsS0FBS2lCLFdBQWpCLENBQW5CO0FBQ0FILFdBQVNJLE1BQVQsQ0FBZ0JILFlBQWhCOztBQUVBO0FBQ0EsTUFBSUksUUFBUSxJQUFJQyxLQUFKLEVBQVo7QUFDQWxCLElBQUVpQixLQUFGLEVBQ0VFLElBREYsQ0FDTyxZQUFZO0FBQ2pCLE9BQUlDLGtCQUFrQnBCLEVBQUUsOERBQUYsQ0FBdEI7O0FBRUFvQixtQkFDRUosTUFERixDQUNTaEIsRUFBRSxNQUFGLENBRFQsRUFFRXFCLE9BRkYsQ0FFVUosS0FGVjs7QUFJQUwsWUFBU1MsT0FBVCxDQUFpQkQsZUFBakI7O0FBRUE7QUFDQVQsT0FBSVcsR0FBSixDQUFRLGNBQVIsRUFBd0JWLFFBQXhCO0FBQ0EsR0FaRixFQWFFVyxJQWJGLENBYU8sRUFBQ0MsS0FBSzFCLEtBQUsyQixRQUFYLEVBYlA7O0FBZUEsU0FBT2IsUUFBUDtBQUNBLEVBeEJEOztBQTBCQTtBQUNBO0FBQ0E7O0FBRUFmLFFBQU82QixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUM3QixNQUFJcEIsU0FBU3FCLE1BQWIsRUFBcUI7QUFDcEIsT0FBSUMsVUFBVTdCLEVBQUU4QixNQUFGLENBQVMsSUFBVCxFQUFlN0IsY0FBZixFQUErQixFQUFDOEIsU0FBUyxFQUFDQyxNQUFNdkIsV0FBUCxFQUFWLEVBQS9CLENBQWQ7QUFDQUYsWUFBUzBCLElBQVQsQ0FBY0osT0FBZDtBQUNBLEdBSEQsTUFHTztBQUNOLFNBQU0sSUFBSUssS0FBSixDQUFVLGdDQUFWLENBQU47QUFDQTs7QUFFRFA7QUFDQSxFQVREOztBQVdBLFFBQU85QixNQUFQO0FBQ0EsQ0F0R0YiLCJmaWxlIjoicHJvZHVjdC9wcm9kdWN0X3Rvb2x0aXAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHByb2R1Y3RfdG9vbHRpcC5qcyAyMDE2LTAzLTMxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBQcm9kdWN0IFRvb2x0aXBcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgZGlzcGxheXMgYSB0b29sdGlwIHdoZW4gaG92ZXJpbmcgdGhlIHByb2R1Y3QgbmFtZS5cbiAqXG4gKiBVc2UgYXR0cmlidXRlICdkYXRhLXByb2R1Y3RfdG9vbHRpcC1pbWFnZS11cmwnIHRvIHNldCB0aGUgaW1hZ2UgdXJsIHRvIGxvYWQuXG4gKiBVc2UgYXR0cmlidXRlICdkYXRhLXByb2R1Y3RfdG9vbHRpcC1kZXNjcmlwdGlvbicgdG8gc2V0IHRoZSB0ZXh0IGNvbnRlbnQuXG4gKlxuICogQG1vZHVsZSBDb250cm9sbGVycy9wcm9kdWN0X3Rvb2x0aXBcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQncHJvZHVjdF90b29sdGlwJyxcblxuXHRbXSxcblxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvcHJvZHVjdF90b29sdGlwICovXG5cblx0ZnVuY3Rpb24gKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9LFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIHFUaXAgcGx1Z2luIG9wdGlvbnMgb2JqZWN0LlxuXHRcdFx0ICogQHR5cGUge09iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0dG9vbHRpcE9wdGlvbnMgPSB7XG5cdFx0XHRcdHN0eWxlOiB7XG5cdFx0XHRcdFx0Y2xhc3NlczogJ2d4LWNvbnRhaW5lciBneC1xdGlwIGluZm8gbGFyZ2UnXG5cdFx0XHRcdH0sXG5cdFx0XHRcdHBvc2l0aW9uOiB7XG5cdFx0XHRcdFx0bXk6ICdsZWZ0IHRvcCcsXG5cdFx0XHRcdFx0YXQ6ICdyaWdodCBib3R0b20nXG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogSG92ZXIgdHJpZ2dlci5cblx0XHRcdCAqIEB0eXBlIHtqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdCR0cmlnZ2VyID0gJHRoaXMuZmluZCgnW2RhdGEtdG9vbHRpcC10cmlnZ2VyXScpO1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBQcmVwYXJlcyB0aGUgdG9vbHRpcCBjb250ZW50IGJ5IGZldGNoaW5nIHRoZSBpbWFnZSBhbmQgc2V0dGluZyB0aGUgZGVzY3JpcHRpb24uXG5cdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IEhvdmVyIGV2ZW50LlxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBhcGkgcVRpcCBwbHVnaW4gaW50ZXJuYWwgQVBJLlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9nZXRDb250ZW50ID0gZnVuY3Rpb24gKGV2ZW50LCBhcGkpIHtcblx0XHRcdHZhciAkY29udGVudCA9ICQoJzxkaXYvPicpO1xuXG5cdFx0XHR2YXIgJGRlc2NyaXB0aW9uID0gJC5wYXJzZUhUTUwoZGF0YS5kZXNjcmlwdGlvbik7XG5cdFx0XHQkY29udGVudC5hcHBlbmQoJGRlc2NyaXB0aW9uKTtcblxuXHRcdFx0Ly8gRmV0Y2ggaW1hZ2UuXG5cdFx0XHR2YXIgaW1hZ2UgPSBuZXcgSW1hZ2UoKTtcblx0XHRcdCQoaW1hZ2UpXG5cdFx0XHRcdC5sb2FkKGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHR2YXIgJGltYWdlQ29udGFpbmVyID0gJCgnPGRpdiBjbGFzcz1cInRleHQtY2VudGVyXCIgc3R5bGU9XCJtYXJnaW4tYm90dG9tOiAyNHB4O1wiPjwvZGl2PicpO1xuXG5cdFx0XHRcdFx0JGltYWdlQ29udGFpbmVyXG5cdFx0XHRcdFx0XHQuYXBwZW5kKCQoJzxicj4nKSlcblx0XHRcdFx0XHRcdC5wcmVwZW5kKGltYWdlKTtcblxuXHRcdFx0XHRcdCRjb250ZW50LnByZXBlbmQoJGltYWdlQ29udGFpbmVyKTtcblxuXHRcdFx0XHRcdC8vIFNldCBuZXcgdG9vbHRpcCBjb250ZW50LlxuXHRcdFx0XHRcdGFwaS5zZXQoJ2NvbnRlbnQudGV4dCcsICRjb250ZW50KTtcblx0XHRcdFx0fSlcblx0XHRcdFx0LmF0dHIoe3NyYzogZGF0YS5pbWFnZVVybH0pO1xuXG5cdFx0XHRyZXR1cm4gJGNvbnRlbnQ7XG5cdFx0fTtcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cdFx0XHRpZiAoJHRyaWdnZXIubGVuZ3RoKSB7XG5cdFx0XHRcdHZhciBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwgdG9vbHRpcE9wdGlvbnMsIHtjb250ZW50OiB7dGV4dDogX2dldENvbnRlbnR9fSk7XG5cdFx0XHRcdCR0cmlnZ2VyLnF0aXAob3B0aW9ucyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0NvdWxkIG5vdCBmaW5kIHRyaWdnZXIgZWxlbWVudCcpO1xuXHRcdFx0fVxuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
