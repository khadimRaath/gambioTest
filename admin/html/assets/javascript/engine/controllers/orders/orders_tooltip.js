'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 orders_tooltip.js 2015-10-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order Tooltip
 *
 * This controller displays a tooltip when hovering the order
 *
 * @module Controllers/orders_tooltip
 */
gx.controllers.module('orders_tooltip', [],

/**  @lends module:Controllers/orders_tooltip */

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
		'url': ''
	},


	/**
  * timeout for tooltip assignment
  *
  * @type {boolean}
  */
	timeout = 0,


	/**
  * delay until tooltip appears
  *
  * @type {number}
  */
	delay = 300,


	/**
  * flag, if element is hoverd
  *
  * @type {boolean}
  */
	hoverd = true,


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

	var _loadOrderData = function _loadOrderData() {
		if (options.url !== '') {
			$.ajax({
				url: options.url,
				type: 'GET',
				dataType: 'json',
				success: function success(response) {
					var content = '<table>';

					for (var id in response.products) {
						var product = response.products[id];
						content += '<tr>';

						for (var key in product) {

							if (_typeof(product[key]) !== 'object') {

								var align = key === 'price' ? ' align="right"' : '';

								content += '<td valign="top"' + align + '>' + product[key];

								if (key === 'name') {
									for (var i in product.attributes) {
										content += '<br />- ' + product.attributes[i].name;
										content += ': ' + product.attributes[i].value;
									}
								}

								content += '</td>';
							}
						}

						content += '</tr>';
					}

					content += '<tr><td class="total_price" colspan="4" align="right">' + response.total_price + '</td></tr>';

					content += '</table>';

					timeout = window.setTimeout(function () {
						$this.qtip({
							content: content,
							style: {
								classes: 'gx-container gx-qtip info large'
							},
							position: {
								my: 'left top',
								at: 'right bottom'
							},
							show: {
								when: false,
								ready: hoverd,
								delay: delay
							},
							hide: {
								fixed: true
							}
						});

						options.url = '';
					}, delay);
				}
			});
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('hover', _loadOrderData);
		$this.on('mouseout', function () {
			hoverd = false;
			clearTimeout(timeout);
		});

		// Finish it
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfdG9vbHRpcC5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwidGltZW91dCIsImRlbGF5IiwiaG92ZXJkIiwib3B0aW9ucyIsImV4dGVuZCIsIl9sb2FkT3JkZXJEYXRhIiwidXJsIiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsInN1Y2Nlc3MiLCJyZXNwb25zZSIsImNvbnRlbnQiLCJpZCIsInByb2R1Y3RzIiwicHJvZHVjdCIsImtleSIsImFsaWduIiwiaSIsImF0dHJpYnV0ZXMiLCJuYW1lIiwidmFsdWUiLCJ0b3RhbF9wcmljZSIsIndpbmRvdyIsInNldFRpbWVvdXQiLCJxdGlwIiwic3R5bGUiLCJjbGFzc2VzIiwicG9zaXRpb24iLCJteSIsImF0Iiwic2hvdyIsIndoZW4iLCJyZWFkeSIsImhpZGUiLCJmaXhlZCIsImluaXQiLCJkb25lIiwib24iLCJjbGVhclRpbWVvdXQiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLGdCQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVztBQUNWLFNBQU87QUFERyxFQWJaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVUsQ0F0Qlg7OztBQXdCQzs7Ozs7QUFLQUMsU0FBUSxHQTdCVDs7O0FBK0JDOzs7OztBQUtBQyxVQUFTLElBcENWOzs7QUFzQ0M7Ozs7O0FBS0FDLFdBQVVMLEVBQUVNLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkwsUUFBbkIsRUFBNkJILElBQTdCLENBM0NYOzs7QUE2Q0M7Ozs7O0FBS0FELFVBQVMsRUFsRFY7O0FBb0RBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJVSxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0IsTUFBSUYsUUFBUUcsR0FBUixLQUFnQixFQUFwQixFQUF3QjtBQUN2QlIsS0FBRVMsSUFBRixDQUFPO0FBQ05ELFNBQUtILFFBQVFHLEdBRFA7QUFFTkUsVUFBTSxLQUZBO0FBR05DLGNBQVUsTUFISjtBQUlOQyxhQUFTLGlCQUFTQyxRQUFULEVBQW1CO0FBQzNCLFNBQUlDLFVBQVUsU0FBZDs7QUFFQSxVQUFLLElBQUlDLEVBQVQsSUFBZUYsU0FBU0csUUFBeEIsRUFBa0M7QUFDakMsVUFBSUMsVUFBVUosU0FBU0csUUFBVCxDQUFrQkQsRUFBbEIsQ0FBZDtBQUNBRCxpQkFBVyxNQUFYOztBQUVBLFdBQUssSUFBSUksR0FBVCxJQUFnQkQsT0FBaEIsRUFBeUI7O0FBRXhCLFdBQUksUUFBT0EsUUFBUUMsR0FBUixDQUFQLE1BQXdCLFFBQTVCLEVBQXNDOztBQUVyQyxZQUFJQyxRQUFTRCxRQUFRLE9BQVQsR0FBb0IsZ0JBQXBCLEdBQXVDLEVBQW5EOztBQUVBSixtQkFBVyxxQkFBcUJLLEtBQXJCLEdBQTZCLEdBQTdCLEdBQW1DRixRQUFRQyxHQUFSLENBQTlDOztBQUVBLFlBQUlBLFFBQVEsTUFBWixFQUFvQjtBQUNuQixjQUFLLElBQUlFLENBQVQsSUFBY0gsUUFBUUksVUFBdEIsRUFBa0M7QUFDakNQLHFCQUFXLGFBQWFHLFFBQVFJLFVBQVIsQ0FBbUJELENBQW5CLEVBQXNCRSxJQUE5QztBQUNBUixxQkFBVyxPQUFPRyxRQUFRSSxVQUFSLENBQW1CRCxDQUFuQixFQUFzQkcsS0FBeEM7QUFDQTtBQUNEOztBQUVEVCxtQkFBVyxPQUFYO0FBQ0E7QUFDRDs7QUFFREEsaUJBQVcsT0FBWDtBQUNBOztBQUVEQSxnQkFDQywyREFBMkRELFNBQVNXLFdBQXBFLEdBQ0EsWUFGRDs7QUFJQVYsZ0JBQVcsVUFBWDs7QUFFQVosZUFBVXVCLE9BQU9DLFVBQVAsQ0FBa0IsWUFBVztBQUN0QzNCLFlBQU00QixJQUFOLENBQVc7QUFDVmIsZ0JBQVNBLE9BREM7QUFFVmMsY0FBTztBQUNOQyxpQkFBUztBQURILFFBRkc7QUFLVkMsaUJBQVU7QUFDVEMsWUFBSSxVQURLO0FBRVRDLFlBQUk7QUFGSyxRQUxBO0FBU1ZDLGFBQU07QUFDTEMsY0FBTSxLQUREO0FBRUxDLGVBQU8vQixNQUZGO0FBR0xELGVBQU9BO0FBSEYsUUFUSTtBQWNWaUMsYUFBTTtBQUNMQyxlQUFPO0FBREY7QUFkSSxPQUFYOztBQW1CQWhDLGNBQVFHLEdBQVIsR0FBYyxFQUFkO0FBQ0EsTUFyQlMsRUFxQlBMLEtBckJPLENBQVY7QUFzQkE7QUE3REssSUFBUDtBQStEQTtBQUNELEVBbEVEOztBQW9FQTtBQUNBO0FBQ0E7O0FBRUFOLFFBQU95QyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCeEMsUUFBTXlDLEVBQU4sQ0FBUyxPQUFULEVBQWtCakMsY0FBbEI7QUFDQVIsUUFBTXlDLEVBQU4sQ0FBUyxVQUFULEVBQXFCLFlBQVc7QUFDL0JwQyxZQUFTLEtBQVQ7QUFDQXFDLGdCQUFhdkMsT0FBYjtBQUNBLEdBSEQ7O0FBS0E7QUFDQXFDO0FBQ0EsRUFURDs7QUFXQSxRQUFPMUMsTUFBUDtBQUNBLENBM0pGIiwiZmlsZSI6Im9yZGVycy9vcmRlcnNfdG9vbHRpcC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gb3JkZXJzX3Rvb2x0aXAuanMgMjAxNS0xMC0wMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXIgVG9vbHRpcFxuICpcbiAqIFRoaXMgY29udHJvbGxlciBkaXNwbGF5cyBhIHRvb2x0aXAgd2hlbiBob3ZlcmluZyB0aGUgb3JkZXJcbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL29yZGVyc190b29sdGlwXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J29yZGVyc190b29sdGlwJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL29yZGVyc190b29sdGlwICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQndXJsJzogJydcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogdGltZW91dCBmb3IgdG9vbHRpcCBhc3NpZ25tZW50XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge2Jvb2xlYW59XG5cdFx0XHQgKi9cblx0XHRcdHRpbWVvdXQgPSAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIGRlbGF5IHVudGlsIHRvb2x0aXAgYXBwZWFyc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtudW1iZXJ9XG5cdFx0XHQgKi9cblx0XHRcdGRlbGF5ID0gMzAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIGZsYWcsIGlmIGVsZW1lbnQgaXMgaG92ZXJkXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge2Jvb2xlYW59XG5cdFx0XHQgKi9cblx0XHRcdGhvdmVyZCA9IHRydWUsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfbG9hZE9yZGVyRGF0YSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKG9wdGlvbnMudXJsICE9PSAnJykge1xuXHRcdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHVybDogb3B0aW9ucy51cmwsXG5cdFx0XHRcdFx0dHlwZTogJ0dFVCcsXG5cdFx0XHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcblx0XHRcdFx0XHRzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0dmFyIGNvbnRlbnQgPSAnPHRhYmxlPic7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGZvciAodmFyIGlkIGluIHJlc3BvbnNlLnByb2R1Y3RzKSB7XG5cdFx0XHRcdFx0XHRcdHZhciBwcm9kdWN0ID0gcmVzcG9uc2UucHJvZHVjdHNbaWRdO1xuXHRcdFx0XHRcdFx0XHRjb250ZW50ICs9ICc8dHI+Jztcblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdGZvciAodmFyIGtleSBpbiBwcm9kdWN0KSB7XG5cdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0aWYgKHR5cGVvZiBwcm9kdWN0W2tleV0gIT09ICdvYmplY3QnKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdHZhciBhbGlnbiA9IChrZXkgPT09ICdwcmljZScpID8gJyBhbGlnbj1cInJpZ2h0XCInIDogJyc7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQgKz0gJzx0ZCB2YWxpZ249XCJ0b3BcIicgKyBhbGlnbiArICc+JyArIHByb2R1Y3Rba2V5XTtcblx0XHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdFx0aWYgKGtleSA9PT0gJ25hbWUnKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdGZvciAodmFyIGkgaW4gcHJvZHVjdC5hdHRyaWJ1dGVzKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y29udGVudCArPSAnPGJyIC8+LSAnICsgcHJvZHVjdC5hdHRyaWJ1dGVzW2ldLm5hbWU7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0Y29udGVudCArPSAnOiAnICsgcHJvZHVjdC5hdHRyaWJ1dGVzW2ldLnZhbHVlO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQgKz0gJzwvdGQ+Jztcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdGNvbnRlbnQgKz0gJzwvdHI+Jztcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Y29udGVudCArPVxuXHRcdFx0XHRcdFx0XHQnPHRyPjx0ZCBjbGFzcz1cInRvdGFsX3ByaWNlXCIgY29sc3Bhbj1cIjRcIiBhbGlnbj1cInJpZ2h0XCI+JyArIHJlc3BvbnNlLnRvdGFsX3ByaWNlICtcblx0XHRcdFx0XHRcdFx0JzwvdGQ+PC90cj4nO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRjb250ZW50ICs9ICc8L3RhYmxlPic7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdHRpbWVvdXQgPSB3aW5kb3cuc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JHRoaXMucXRpcCh7XG5cdFx0XHRcdFx0XHRcdFx0Y29udGVudDogY29udGVudCxcblx0XHRcdFx0XHRcdFx0XHRzdHlsZToge1xuXHRcdFx0XHRcdFx0XHRcdFx0Y2xhc3NlczogJ2d4LWNvbnRhaW5lciBneC1xdGlwIGluZm8gbGFyZ2UnXG5cdFx0XHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdFx0XHRwb3NpdGlvbjoge1xuXHRcdFx0XHRcdFx0XHRcdFx0bXk6ICdsZWZ0IHRvcCcsXG5cdFx0XHRcdFx0XHRcdFx0XHRhdDogJ3JpZ2h0IGJvdHRvbSdcblx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdHNob3c6IHtcblx0XHRcdFx0XHRcdFx0XHRcdHdoZW46IGZhbHNlLFxuXHRcdFx0XHRcdFx0XHRcdFx0cmVhZHk6IGhvdmVyZCxcblx0XHRcdFx0XHRcdFx0XHRcdGRlbGF5OiBkZWxheVxuXHRcdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdFx0aGlkZToge1xuXHRcdFx0XHRcdFx0XHRcdFx0Zml4ZWQ6IHRydWVcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0b3B0aW9ucy51cmwgPSAnJztcblx0XHRcdFx0XHRcdH0sIGRlbGF5KTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzLm9uKCdob3ZlcicsIF9sb2FkT3JkZXJEYXRhKTtcblx0XHRcdCR0aGlzLm9uKCdtb3VzZW91dCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRob3ZlcmQgPSBmYWxzZTtcblx0XHRcdFx0Y2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIEZpbmlzaCBpdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
