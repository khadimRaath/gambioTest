/* CartControl.js <?php
#   --------------------------------------------------------------
#   CartControl.js 2013-12-04 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function CartControl(){if(fb)console.log('CartControl ready');this.submit_buy_now_form=function(p_form){var coo_this=this,t_form_data=$(p_form).serialize();if(fb)console.log('t_form_data: '+t_form_data);var t_url='request_port.php?module=buy_now&XTCsid='+gm_session_id;$.ajax({url:t_url,type:'POST',dataType:'json',data:t_form_data,success:function(p_result_json){if(fb)console.log('show_details: '+p_result_json.show_details);if(p_result_json.show_details==false&&p_result_json.show_cart==false){coo_this.load_dropdown_content()}if(p_result_json.show_details==false&&p_result_json.show_cart==true){document.location.href='<?php echo xtc_href_link(FILENAME_SHOPPING_CART, "", "NONSSL"); ?>'}if(p_result_json.show_details==true){document.location.href=p_result_json.products_details_url}}})};this.load_dropdown_content=function(){var coo_this=this,t_url='',t_close_timeout=false;$(document).bind("cart_shipping_costs_info_active",function(){if(t_close_timeout!=false){clearTimeout(t_close_timeout);t_close_timeout=false}});if(coo_this.need_fixed_cart()==true){t_url='request_port.php?module=CartDropdown&part=fixed&XTCsid='+gm_session_id;$.ajax({type:'GET',url:t_url,success:function(p_result){var t_dropdown_html=p_result;$('#fixed_dropdown_shopping_cart').remove();$('.wrap_shop').prepend(t_dropdown_html);coo_this.position_fixed_dropdown();coo_this.open_fixed_dropdown();setTimeout('coo_cart_control.close_fixed_dropdown()',5000)}})}t_url='request_port.php?module=CartDropdown&part=header&XTCsid='+gm_session_id;$.ajax({type:'GET',url:t_url,success:function(p_result){var t_header_html=p_result;$('#head_shopping_cart').remove();$('#head_toolbox_inner').prepend(t_header_html);if(coo_this.need_fixed_cart()==false){$('#head_shopping_cart').addClass('active')}}});t_url='request_port.php?module=CartDropdown&part=dropdown&XTCsid='+gm_session_id;$.ajax({type:'GET',url:t_url,success:function(p_result){var t_dropdown_html=p_result;$('#dropdown_shopping_cart').remove();$('.wrap_shop').prepend(t_dropdown_html);coo_this.position_dropdown();if(coo_this.need_fixed_cart()==false){coo_this.open_dropdown();t_close_timeout=setTimeout('coo_cart_control.close_dropdown()',5000)}}})};this.need_fixed_cart=function(){var t_cart_header=$('#head_shopping_cart'),t_dropdown_offset_top=$('#container').prop('offsetTop')+$(t_cart_header).prop('offsetTop')+$(t_cart_header).height(),scrOfY=0;if(typeof(window.pageYOffset)=='number'){scrOfY=window.pageYOffset}else if(document.body&&(document.body.scrollLeft||document.body.scrollTop)){scrOfY=document.body.scrollTop}else if(document.documentElement&&(document.documentElement.scrollLeft||document.documentElement.scrollTop)){scrOfY=document.documentElement.scrollTop}if(fb)console.log('offset '+t_dropdown_offset_top);if(fb)console.log('scrollTop '+scrOfY);if(t_dropdown_offset_top<scrOfY){if(fb)console.log('use fixed cart dropdown');return true}else{if(fb)console.log('use attached cart dropdown');return false}};this.open_fixed_dropdown=function(){if(fb)console.log('open_fixed_dropdown');var t_cart_dropdown=$('#fixed_dropdown_shopping_cart');$(t_cart_dropdown).slideDown('slow')};if(typeof gm_style_edit_mode_running=='undefined'){this.close_fixed_dropdown=function(){if(fb)console.log('close_fixed_dropdown');var t_cart_dropdown=$('#fixed_dropdown_shopping_cart');$(t_cart_dropdown).slideUp()};}this.position_fixed_dropdown=function(){var t_cart_header=$('#head_shopping_cart'),t_cart_dropdown=$('#fixed_dropdown_shopping_cart');$(t_cart_dropdown).css('left',$(t_cart_header).offset().left);if(typeof($('body').css('padding-top'))!='undefined'&&$('body').css('padding-top').search('px')!=-1){$(t_cart_dropdown).css('top','-'+$('body').css('padding-top'))}};this.open_dropdown=function(){if(fb)console.log('open_dropdown');var t_cart_header=$('#head_shopping_cart'),t_cart_dropdown=$('#dropdown_shopping_cart');$(t_cart_header).addClass('active');$(t_cart_dropdown).slideDown('slow')};if(typeof gm_style_edit_mode_running=='undefined'){this.close_dropdown=function(){if(fb)console.log('close_dropdown');var t_cart_header=$('#head_shopping_cart'),t_cart_dropdown=$('#dropdown_shopping_cart');$(t_cart_header).removeClass('active');$(t_cart_dropdown).slideUp()};}this.position_dropdown=function(){var t_cart_header=$('#head_shopping_cart'),t_cart_dropdown=$('#dropdown_shopping_cart'),t_top=Number($(t_cart_header).offset().top)+Number($(t_cart_header).height())+Number($(t_cart_header).css('padding-top').replace('px',''))+Number($(t_cart_header).css('padding-bottom').replace('px',''));$(t_cart_dropdown).css('left',$(t_cart_header).offset().left).css('top',t_top+'px')};}
/*<?php
}
else
{
?>*/
function CartControl()
{
	if(fb)console.log('CartControl ready');

	this.submit_buy_now_form = function(p_form)
	{
		var coo_this = this; // handle for callback functions

		var t_form_data = $(p_form).serialize();
		if(fb)console.log('t_form_data: '+ t_form_data);

		var t_url = 'request_port.php?module=buy_now&XTCsid=' + gm_session_id;

		$.ajax({
			url: t_url,
			type: 'POST',
			dataType: 'json',
			data: t_form_data,
			success: function(p_result_json)
					{
						if(fb)console.log('show_details: ' + p_result_json.show_details);
						/*
						0-0 dropdown
						0-1 cart
						1-0 details
						1-1 details
						*/

						// dont redirect. use cart dropdown.
						if(p_result_json.show_details == false && p_result_json.show_cart == false)
						{
							// update cart dropdown
							coo_this.load_dropdown_content();
						}

						// redirect to cart
						if(p_result_json.show_details == false && p_result_json.show_cart == true)
						{
							//TODO: add session-id
							document.location.href = '<?php echo xtc_href_link(FILENAME_SHOPPING_CART, "", "NONSSL"); ?>';
						}

                        // redirect to product details
						if(p_result_json.show_details == true)
						{
							document.location.href = p_result_json.products_details_url;
						}
					}
		});

	}

	this.load_dropdown_content = function()
	{
		var coo_this = this; // handle for callback functions

		var t_url = '';
		
		var t_close_timeout = false;
		
		$( document ).bind( "cart_shipping_costs_info_active", function()
		{
			if( t_close_timeout != false )
			{
				clearTimeout( t_close_timeout );
				t_close_timeout = false;
			}
		});
		
		if(coo_this.need_fixed_cart() == true)
		{
			t_url = 'request_port.php?module=CartDropdown&part=fixed&XTCsid=' + gm_session_id;
			$.ajax({
				type: 'GET',
				url: t_url,
				success: function(p_result)
						 {
							var t_dropdown_html = p_result;

							// remove old dropdown
							$('#fixed_dropdown_shopping_cart').remove();

							// set loaded dropdown
							$('.wrap_shop').prepend(t_dropdown_html);

							// set position
							coo_this.position_fixed_dropdown();

							// show dropdown
							coo_this.open_fixed_dropdown();

							// start timeout for closing the dropdown
							setTimeout('coo_cart_control.close_fixed_dropdown()', 5000);
						 }
			});
		}


		// update dropdown content
		t_url = 'request_port.php?module=CartDropdown&part=header&XTCsid=' + gm_session_id;
		$.ajax({
			type: 'GET',
			url: t_url,
			success: function(p_result)
					 {
						var t_header_html = p_result;
						$('#head_shopping_cart').remove();
						$('#head_toolbox_inner').prepend(t_header_html);

						if(coo_this.need_fixed_cart() == false)
						{
							// fixed cart not needed, so need to activate dropdown
							$('#head_shopping_cart').addClass('active');
						}
					 }
		});


		// update dropdown content
		t_url = 'request_port.php?module=CartDropdown&part=dropdown&XTCsid=' + gm_session_id;
		$.ajax({
			type: 'GET',
			url: t_url,
			success: function(p_result)
					 {
						var t_dropdown_html = p_result;

						// remove old dropdown
						$('#dropdown_shopping_cart').remove();

						// set loaded dropdown
						$('.wrap_shop').prepend(t_dropdown_html);

						// set position
						coo_this.position_dropdown();

						if(coo_this.need_fixed_cart() == false)
						{
							// fixed cart not needed, so need to activate dropdown

							// show dropdown
							coo_this.open_dropdown();

							// start timeout for closing the dropdown
							t_close_timeout = setTimeout('coo_cart_control.close_dropdown()', 5000);
						}
					 }
		 });
	}


	this.need_fixed_cart = function()
	{
		var t_cart_header = $('#head_shopping_cart');

		var t_dropdown_offset_top = $('#container').prop('offsetTop') + $(t_cart_header).prop('offsetTop') + $(t_cart_header).height();

		var scrOfY = 0;
		if( typeof( window.pageYOffset ) == 'number' ) {
			//Netscape compliant
			scrOfY = window.pageYOffset;
		} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
			//DOM compliant
			scrOfY = document.body.scrollTop;
		} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
			//IE6 standards compliant mode
			scrOfY = document.documentElement.scrollTop;
		}

		if(fb)console.log('offset ' + t_dropdown_offset_top);
		if(fb)console.log('scrollTop ' + scrOfY);

		// use fixed position, if dropdown not in viewport
		if(t_dropdown_offset_top < scrOfY)
		{
			if(fb)console.log('use fixed cart dropdown');
			return true;
		}
		else
		{
			if(fb)console.log('use attached cart dropdown');
			return false;
		}
	}



	this.open_fixed_dropdown = function()
	{
		if(fb)console.log('open_fixed_dropdown');

		var t_cart_dropdown = $('#fixed_dropdown_shopping_cart');
		$(t_cart_dropdown).slideDown('slow');
	}

	// dont bind closing events, if styleedit is running
	if(typeof gm_style_edit_mode_running == 'undefined')
	{
		this.close_fixed_dropdown = function()
		{
			if(fb)console.log('close_fixed_dropdown');

			var t_cart_dropdown = $('#fixed_dropdown_shopping_cart');
			$(t_cart_dropdown).slideUp();
		}
	}

	this.position_fixed_dropdown = function()
	{
		var t_cart_header = $('#head_shopping_cart');
		var t_cart_dropdown = $('#fixed_dropdown_shopping_cart');

		// set top relative to dropdown header
		$(t_cart_dropdown).css('left', $(t_cart_header).offset().left);

		if(typeof($('body').css('padding-top')) != 'undefined' && $('body').css('padding-top').search('px') != -1)
		{
			$(t_cart_dropdown).css('top', '-' + $('body').css('padding-top'));
		}
	}



	this.open_dropdown = function()
	{
		if(fb)console.log('open_dropdown');

		var t_cart_header = $('#head_shopping_cart');
		var t_cart_dropdown = $('#dropdown_shopping_cart');

		$(t_cart_header).addClass('active');
		$(t_cart_dropdown).slideDown('slow');
	}

	// dont bind closing events, if styleedit is running
	if(typeof gm_style_edit_mode_running == 'undefined')
	{
		this.close_dropdown = function()
		{
			if(fb)console.log('close_dropdown');

			var t_cart_header = $('#head_shopping_cart');
			var t_cart_dropdown = $('#dropdown_shopping_cart');

			$(t_cart_header).removeClass('active');
			$(t_cart_dropdown).slideUp();
		}
	}

	this.position_dropdown = function()
	{
		var t_cart_header = $('#head_shopping_cart');
		var t_cart_dropdown = $('#dropdown_shopping_cart');

		var t_top = Number($(t_cart_header).offset().top) +
					Number($(t_cart_header).height()) +
					Number($(t_cart_header).css('padding-top').replace('px', '')) +
					Number($(t_cart_header).css('padding-bottom').replace('px', ''));

		// set top relative to dropdown header
		$(t_cart_dropdown).css('left', $(t_cart_header).offset().left).css('top', t_top + 'px');
	}

}
/*<?php
}
?>*/