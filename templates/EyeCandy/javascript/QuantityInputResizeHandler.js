/* QuantityInputResizeHandler.js <?php
#   --------------------------------------------------------------
#   QuantityInputResizeHandler.js 2012-12-12 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function QuantityInputResizeHandler(){if(fb)console.log('QuantityInputResizeHandler ready');this.init_binds=function(){if(fb)console.log('QuantityInputResizeHandler init_binds');set_input_width();$('input.products_quantity').die('keyup');$('input.products_quantity').live('keyup',function(e){set_input_width()});function set_input_width(){var inputs=$('input.products_quantity');$.each(inputs,function(index,input){var comparison_span=$('<span></span>').attr('id','comparison_span').addClass('comparison_span').html($(input).val());$(input).after(comparison_span);$(input).width(comparison_span.width());var diff=$('span.quantity_container').width()-$(input).outerWidth()-($(input).prev()&&$(input).prev().hasClass('products_quantity_unit')?$(input).prev().outerWidth():0);if(diff>0){$(input).width($(input).width()+diff)}$(input).next().remove()})}};this.init_binds()};
/*<?php
}
else
{
?>*/
function QuantityInputResizeHandler()
{
	if(fb)console.log('QuantityInputResizeHandler ready');
	
	this.init_binds = function()
	{
		if(fb)console.log('QuantityInputResizeHandler init_binds');
		
		set_input_width();
		
		$('input.products_quantity').die('keyup');
		$('input.products_quantity').live('keyup', function (e)
		{
			set_input_width();
		});
		
		function set_input_width()
		{
			var inputs = $('input.products_quantity');
			$.each(inputs, function (index, input)
			{
				var comparison_span = $('<span></span>')
					.attr('id', 'comparison_span')
					.addClass('comparison_span')
					.html($(input).val())
				;
				$(input).after(comparison_span);
				$(input).width(comparison_span.width());
				var diff = $('span.quantity_container').width() - $(input).outerWidth() - ($(input).prev() && $(input).prev().hasClass('products_quantity_unit') ? $(input).prev().outerWidth() : 0);
				if (diff > 0)
				{
					$(input).width($(input).width() + diff);
				}
				$(input).next().remove();
			});
		}
	}

	this.init_binds();
}
/*<?php
}
?>*/