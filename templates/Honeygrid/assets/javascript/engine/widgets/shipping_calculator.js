'use strict';

/* --------------------------------------------------------------
 shipping_calculator.js 2016-05-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates the shipping cost box at the
 * shopping cart page
 */
gambio.widgets.module('shipping_calculator', ['form', 'xhr'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    defaults = {
		// URL at which the request is send.
		url: 'shop.php?do=CartShippingCosts',
		selectorMapping: {
			gambioUltraCosts: '.cart_shipping_costs_gambio_ultra_dropdown, ' + '.order-total-shipping-info-gambioultra-costs',
			shippingWeight: '.shipping-calculator-shipping-weight-unit, .shipping-weight-value',
			shippingCost: '.shipping-calculator-shipping-costs, .order-total-shipping-info, ' + '.shipping-cost-value',
			shippingCalculator: '.shipping-calculator-shipping-modules',
			invalidCombinationError: '#cart_shipping_costs_invalid_combination_error'
		}
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Function that requests the given URL and
  * fills the page with the delivered data
  * @private
  */
	var _updateShippingCosts = function _updateShippingCosts() {
		var formdata = jse.libs.form.getData($this);

		jse.libs.xhr.ajax({ url: options.url, data: formdata }).done(function (result) {
			jse.libs.template.helpers.fill(result.content, $body, options.selectorMapping);
		});

		// update modal content source
		var value = $this.find('select[name="cart_shipping_country"]').val();
		$('#shipping-information-layer.hidden select[name="cart_shipping_country"] option').attr('selected', false);
		$('#shipping-information-layer.hidden select[name="cart_shipping_country"] option[value="' + value + '"]').attr('selected', true);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on('change update', _updateShippingCosts);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hpcHBpbmdfY2FsY3VsYXRvci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiZGVmYXVsdHMiLCJ1cmwiLCJzZWxlY3Rvck1hcHBpbmciLCJnYW1iaW9VbHRyYUNvc3RzIiwic2hpcHBpbmdXZWlnaHQiLCJzaGlwcGluZ0Nvc3QiLCJzaGlwcGluZ0NhbGN1bGF0b3IiLCJpbnZhbGlkQ29tYmluYXRpb25FcnJvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlU2hpcHBpbmdDb3N0cyIsImZvcm1kYXRhIiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwieGhyIiwiYWpheCIsImRvbmUiLCJyZXN1bHQiLCJ0ZW1wbGF0ZSIsImhlbHBlcnMiLCJmaWxsIiwiY29udGVudCIsInZhbHVlIiwiZmluZCIsInZhbCIsImF0dHIiLCJpbml0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7OztBQUlBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxxQkFERCxFQUdDLENBQUMsTUFBRCxFQUFTLEtBQVQsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFFBQVFELEVBQUUsTUFBRixDQURUO0FBQUEsS0FFQ0UsV0FBVztBQUNWO0FBQ0FDLE9BQUssK0JBRks7QUFHVkMsbUJBQWlCO0FBQ2hCQyxxQkFBa0IsaURBQ2YsOENBRmE7QUFHaEJDLG1CQUFnQixtRUFIQTtBQUloQkMsaUJBQWMsc0VBQ1gsc0JBTGE7QUFNaEJDLHVCQUFvQix1Q0FOSjtBQU9oQkMsNEJBQXlCO0FBUFQ7QUFIUCxFQUZaO0FBQUEsS0FlQ0MsVUFBVVYsRUFBRVcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CVCxRQUFuQixFQUE2QkosSUFBN0IsQ0FmWDtBQUFBLEtBZ0JDRCxTQUFTLEVBaEJWOztBQW1CRjs7QUFFRTs7Ozs7QUFLQSxLQUFJZSx1QkFBdUIsU0FBdkJBLG9CQUF1QixHQUFXO0FBQ3JDLE1BQUlDLFdBQVdDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxPQUFkLENBQXNCbEIsS0FBdEIsQ0FBZjs7QUFFQWUsTUFBSUMsSUFBSixDQUFTRyxHQUFULENBQWFDLElBQWIsQ0FBa0IsRUFBQ2hCLEtBQUtPLFFBQVFQLEdBQWQsRUFBbUJMLE1BQU1lLFFBQXpCLEVBQWxCLEVBQXNETyxJQUF0RCxDQUEyRCxVQUFTQyxNQUFULEVBQWlCO0FBQzNFUCxPQUFJQyxJQUFKLENBQVNPLFFBQVQsQ0FBa0JDLE9BQWxCLENBQTBCQyxJQUExQixDQUErQkgsT0FBT0ksT0FBdEMsRUFBK0N4QixLQUEvQyxFQUFzRFMsUUFBUU4sZUFBOUQ7QUFDQSxHQUZEOztBQUlBO0FBQ0EsTUFBSXNCLFFBQVEzQixNQUFNNEIsSUFBTixDQUFXLHNDQUFYLEVBQW1EQyxHQUFuRCxFQUFaO0FBQ0E1QixJQUFFLGdGQUFGLEVBQW9GNkIsSUFBcEYsQ0FBeUYsVUFBekYsRUFBb0csS0FBcEc7QUFDQTdCLElBQUUsMkZBQTJGMEIsS0FBM0YsR0FBbUcsSUFBckcsRUFDRUcsSUFERixDQUNPLFVBRFAsRUFDa0IsSUFEbEI7QUFFQSxFQVpEOztBQWVGOztBQUVFOzs7O0FBSUFoQyxRQUFPaUMsSUFBUCxHQUFjLFVBQVNWLElBQVQsRUFBZTs7QUFFNUJyQixRQUFNZ0MsRUFBTixDQUFTLGVBQVQsRUFBMEJuQixvQkFBMUI7O0FBRUFRO0FBRUEsRUFORDs7QUFRQTtBQUNBLFFBQU92QixNQUFQO0FBQ0EsQ0FwRUYiLCJmaWxlIjoid2lkZ2V0cy9zaGlwcGluZ19jYWxjdWxhdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzaGlwcGluZ19jYWxjdWxhdG9yLmpzIDIwMTYtMDUtMTlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHVwZGF0ZXMgdGhlIHNoaXBwaW5nIGNvc3QgYm94IGF0IHRoZVxuICogc2hvcHBpbmcgY2FydCBwYWdlXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J3NoaXBwaW5nX2NhbGN1bGF0b3InLFxuXG5cdFsnZm9ybScsICd4aHInXSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCRib2R5ID0gJCgnYm9keScpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdC8vIFVSTCBhdCB3aGljaCB0aGUgcmVxdWVzdCBpcyBzZW5kLlxuXHRcdFx0XHR1cmw6ICdzaG9wLnBocD9kbz1DYXJ0U2hpcHBpbmdDb3N0cycsXG5cdFx0XHRcdHNlbGVjdG9yTWFwcGluZzoge1xuXHRcdFx0XHRcdGdhbWJpb1VsdHJhQ29zdHM6ICcuY2FydF9zaGlwcGluZ19jb3N0c19nYW1iaW9fdWx0cmFfZHJvcGRvd24sICcgXG5cdFx0XHRcdFx0XHQrICcub3JkZXItdG90YWwtc2hpcHBpbmctaW5mby1nYW1iaW91bHRyYS1jb3N0cycsXG5cdFx0XHRcdFx0c2hpcHBpbmdXZWlnaHQ6ICcuc2hpcHBpbmctY2FsY3VsYXRvci1zaGlwcGluZy13ZWlnaHQtdW5pdCwgLnNoaXBwaW5nLXdlaWdodC12YWx1ZScsXG5cdFx0XHRcdFx0c2hpcHBpbmdDb3N0OiAnLnNoaXBwaW5nLWNhbGN1bGF0b3Itc2hpcHBpbmctY29zdHMsIC5vcmRlci10b3RhbC1zaGlwcGluZy1pbmZvLCAnIFxuXHRcdFx0XHRcdFx0KyAnLnNoaXBwaW5nLWNvc3QtdmFsdWUnLFxuXHRcdFx0XHRcdHNoaXBwaW5nQ2FsY3VsYXRvcjogJy5zaGlwcGluZy1jYWxjdWxhdG9yLXNoaXBwaW5nLW1vZHVsZXMnLCBcblx0XHRcdFx0XHRpbnZhbGlkQ29tYmluYXRpb25FcnJvcjogJyNjYXJ0X3NoaXBwaW5nX2Nvc3RzX2ludmFsaWRfY29tYmluYXRpb25fZXJyb3InXG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBGdW5jdGlvbiB0aGF0IHJlcXVlc3RzIHRoZSBnaXZlbiBVUkwgYW5kXG5cdFx0ICogZmlsbHMgdGhlIHBhZ2Ugd2l0aCB0aGUgZGVsaXZlcmVkIGRhdGFcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlU2hpcHBpbmdDb3N0cyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGZvcm1kYXRhID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0aGlzKTtcblxuXHRcdFx0anNlLmxpYnMueGhyLmFqYXgoe3VybDogb3B0aW9ucy51cmwsIGRhdGE6IGZvcm1kYXRhfSkuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkYm9keSwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuXHRcdFx0fSk7XG5cblx0XHRcdC8vIHVwZGF0ZSBtb2RhbCBjb250ZW50IHNvdXJjZVxuXHRcdFx0dmFyIHZhbHVlID0gJHRoaXMuZmluZCgnc2VsZWN0W25hbWU9XCJjYXJ0X3NoaXBwaW5nX2NvdW50cnlcIl0nKS52YWwoKTtcblx0XHRcdCQoJyNzaGlwcGluZy1pbmZvcm1hdGlvbi1sYXllci5oaWRkZW4gc2VsZWN0W25hbWU9XCJjYXJ0X3NoaXBwaW5nX2NvdW50cnlcIl0gb3B0aW9uJykuYXR0cignc2VsZWN0ZWQnLGZhbHNlKTtcblx0XHRcdCQoJyNzaGlwcGluZy1pbmZvcm1hdGlvbi1sYXllci5oaWRkZW4gc2VsZWN0W25hbWU9XCJjYXJ0X3NoaXBwaW5nX2NvdW50cnlcIl0gb3B0aW9uW3ZhbHVlPVwiJyArIHZhbHVlICsgJ1wiXScpXG5cdFx0XHRcdC5hdHRyKCdzZWxlY3RlZCcsdHJ1ZSk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHQkdGhpcy5vbignY2hhbmdlIHVwZGF0ZScsIF91cGRhdGVTaGlwcGluZ0Nvc3RzKTtcblxuXHRcdFx0ZG9uZSgpO1xuXG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTsiXX0=
