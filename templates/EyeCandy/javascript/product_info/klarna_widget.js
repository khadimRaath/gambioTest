$(function() {
	var load_klarna_widget = function(amount_value) {
		var widget_container = $('<div class="klarna_widget_container"></div>');
		widget_container.load('<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG ?>request_port.php?module=KlarnaWidget', {amount: amount_value},
			function(responseText, textStatus) {
				$('.klarna_widget_container').remove();
				$('#product_info div.price-container').after(widget_container);
			});
	};

	var update_klarna_widget = function() {
		var klarna_amount = find_amount();
		if(!isNaN(klarna_amount)) {
			load_klarna_widget(klarna_amount);
		}
	}

	var find_amount = function() {
		var price = parseFloat($('#gm_attr_calc_price').text().replace(/.* (\d+)[.,](\d+) \w+$/, '$1.$2'));
		var qty = parseFloat($('#gm_attr_calc_qty').val().replace(/(\d+)(,(\d+))?/, '$1.$3'));
		var full_amount = price * qty;
		return full_amount;
	}
});

