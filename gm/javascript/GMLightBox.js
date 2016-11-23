/* GMLightBox.js <?php
#   --------------------------------------------------------------
#   GMLightBox.js 2014-08-25 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function GMLightBox(){var current_box_id='',t_ie6_elements_array=Array(),coo_this=this;jQuery.extend({dimScreen:function(speed,opacity,callback,nc_height){if(jQuery('#__dimScreen').size()>0)return;if(typeof speed=='function'){callback=speed;speed=null}if(typeof opacity=='function'){callback=opacity;opacity=null}if(speed<1){var placeholder=opacity;opacity=speed;speed=placeholder}if(opacity>=1){var placeholder=speed;speed=opacity;opacity=placeholder}speed=(speed>0)?speed:500;opacity=(opacity>0)?opacity:0.5;if(typeof nc_height=='undefined'){if(typeof nc_height=='undefined')var nc_height=$('body').attr('offsetHeight');if(typeof nc_height=='undefined')var nc_height=$(document).attr('height');if(typeof nc_height=='undefined')var nc_height=$(document).height()}var gm_height=$(document).height()+150,t_append_to='body';if(navigator.appVersion.match(/MSIE [0-7]\./)&&$('#container').length>0){t_append_to='#container'}return jQuery('<div></div>').attr({id:'__dimScreen',fade_opacity:opacity,speed:speed}).css({background:'#000000',left:'0px',opacity:'0',position:'absolute',top:'0px',width:'100%',zIndex:'999'}).appendTo(t_append_to).fadeTo(speed,opacity,callback)},dimScreenStop:function(callback){var x=jQuery('#__dimScreen'),opacity=x.attr('fade_opacity'),speed=x.attr('speed');x.fadeOut(speed,function(){x.remove();if(typeof callback=='function')callback()})}});jQuery.extend({dimScreenIFrame:function(speed,opacity,callback,nc_height){if(jQuery('#__dimScreenIFrame').size()>0)return;if(typeof speed=='function'){callback=speed;speed=null}if(typeof opacity=='function'){callback=opacity;opacity=null}if(speed<1){var placeholder=opacity;opacity=speed;speed=placeholder}if(opacity>=1){var placeholder=speed;speed=opacity;opacity=placeholder}speed=(speed>0)?speed:500;opacity=(opacity>0)?opacity:0.5;if(typeof nc_height=='undefined'){if(typeof nc_height=='undefined')var nc_height=$('body').attr('offsetHeight');if(typeof nc_height=='undefined')var nc_height=$(document).attr('height');if(typeof nc_height=='undefined')var nc_height=$(document).height()}var gm_height=$(document).height()+150,t_append_to='body';if(navigator.appVersion.match(/MSIE [0-7]\./)&&$('#container').length>0){t_append_to='#container'}return jQuery('<div></div>').attr({id:'__dimScreenIFrame',fade_opacity:opacity,speed:speed}).css({background:'#000000',height:gm_height+'px',left:'0px',opacity:'0',position:'absolute',top:'0px',width:'100%',zIndex:'1001'}).appendTo(t_append_to).fadeTo(speed,opacity,callback)},dimScreenStopIFrame:function(callback){var x=jQuery('#__dimScreenIFrame'),opacity=x.attr('fade_opacity'),speed=x.attr('speed');x.fadeOut(speed,function(){x.remove();if(typeof callback=='function')callback()})}});$(document).ready(function(){$('.lightbox_iframe').live('click',function(e){if(fb)console.log('.lightbox_iframe click');var t_iframe_url=$(this).attr('href');gmLightBox.load_iframe(t_iframe_url);return false});$('.gx_microshop_lightbox').live('click',function(e){var t_iframe_url=$(this).attr('href');gmLightBox.load_iframe(t_iframe_url,750);return false})});this.load_iframe=function(p_url,p_width,p_height){if(typeof p_width=='undefined')var p_width=850;if(typeof p_height=='undefined')var p_height=600;$('#iframe_layer').remove();$('body').append('<div id="iframe_layer"><div id="iframe_box_bar"><a href="#" onclick="return gmLightBox.close_iframe_box()" class="icon_lightbox_close">&nbsp;</a></div><div id="iframe_box"></div></div>');$('#iframe_layer').css({position:'absolute',left:'0px',top:'50px',width:'100%',height:'100%'});$('#iframe_box').css({marginLeft:'auto',marginRight:'auto',width:p_width+'px',height:p_height+'px',backgroundColor:'white'});$('#iframe_box_bar').css({marginLeft:'auto',marginRight:'auto',width:p_width+'px',color:'white',textAlign:'right'});$('#iframe_box').html('<iframe src="'+p_url+'" width="100%" height="100%" frameborder="0"></iframe>');this.load_box('#iframe_layer',100,true);$('#menubox_gm_scroller').css({display:'none'});if($(document).height()>$('#product_images_box').height()){var pt_height=$(document).height()}else{var pt_height=$('#product_images_box').height()+200}$('#__dimScreen').css({height:pt_height+'px'})};this.load_box=function(box_id,fade_background_speed,fade_in,mb_height){scroll(0,0);if(fb)console.log('load_box: '+box_id);if(typeof v=='undefined')var fade_background_speed=100;if(typeof fade_in=='undefined')var fade_in=true;current_box_id=box_id;if(navigator.appVersion.match(/MSIE [0-6]\./)){this.ie6_fix(true)}if(box_id=='#iframe_layer'){$(current_box_id).css({zIndex:'1002',display:'none'});$.dimScreenIFrame(fade_background_speed,0.7,function(){if(fb)console.log('dim done:'+current_box_id);if(fade_in)$(current_box_id).fadeIn();else $(current_box_id).show()},mb_height)}else{$(current_box_id).css({zIndex:'1000',display:'none'});$.dimScreen(fade_background_speed,0.7,function(){if(fb)console.log('dim done:'+current_box_id);if(fade_in)$(current_box_id).fadeIn();else $(current_box_id).show()},mb_height)}};this.close_iframe_box=function(){var t_close=true;if(typeof(gm_style_edit_mode_running)=='boolean'&&gm_style_edit_mode_running==true){t_close=confirm('<?php echo ADMIN_LINK_INFO_TEXT; ?>')}if(t_close){if(typeof(gmLightBox)!='undefined'){$('#iframe_box_bar').remove();$('#iframe_box').remove();this.close_box('#iframe_box')}}return false};this.close_box=function(p_box_id,p_current_box_id){if(fb)console.log(current_box_id+': close_box()');$('#menubox_gm_scroller').css({display:'block'});if(typeof(p_box_id)=='undefined'||p_box_id!='#iframe_box'){$.dimScreenStop()}else{$.dimScreenStopIFrame()}if(typeof(p_current_box_id)!='undefined'){current_box_id=p_current_box_id}$(current_box_id).fadeOut("normal",function(){if(navigator.appVersion.match(/MSIE [0-6]\./)){$('.lightbox_visibility_hidden').css({visibility:'visible'});coo_this.ie6_fix(false)}$(this).remove()});current_box_id=''};this.centered_left=function(element_width){var x=(screen.width/2)-(element_width/2);if(fb)console.log('centered width:'+x);return Math.round(x)};this.centered_top=function(element_height){var y=(screen.height/2)-(element_height/2);if(fb)console.log('centered height:'+y);return Math.round(y)};this.ie6_fix=function(p_hide){if(p_hide){$('select').each(function(){if($(this).css('visibility')!='hidden'&&$(this).css('display')!='none'){t_ie6_elements_array.push(this);$(this).css({visibility:'hidden'})}})}else{for(var i=0;i<t_ie6_elements_array.length;i++){$(t_ie6_elements_array[i]).css({visibility:'visible'})}}};this.test=function(){$('.wrap_shop').append('<div id="test_box" onClick="gmLightBox.close_box()"></div>');$('#test_box').css({position:'absolute',left:this.centered_left(500)+'px',top:'150px',width:'500px',height:'0px',background:'white'});this.load_box('#test_box')};}
/*<?php
}
else
{
?>*/
function GMLightBox()
{
	var current_box_id = '';
	var t_ie6_elements_array = Array();
	var coo_this = this;

	//dimScreen()
	//by Brandon Goldman
	jQuery.extend({
	  //dims the screen
	  dimScreen: function(speed, opacity, callback, nc_height) {
      if(jQuery('#__dimScreen').size() > 0) return;

      if(typeof speed == 'function') {
        callback = speed;
        speed = null;
      }

      if(typeof opacity == 'function') {
        callback = opacity;
        opacity = null;
      }

      if(speed < 1) {
        var placeholder = opacity;
        opacity = speed;
        speed = placeholder;
      }

      if(opacity >= 1) {
        var placeholder = speed;
        speed = opacity;
        opacity = placeholder;
      }

      speed = (speed > 0) ? speed : 500;
      opacity = (opacity > 0) ? opacity : 0.5;

      //NC_MB_MOD
		if(typeof nc_height == 'undefined'){
					if(typeof nc_height == 'undefined') var nc_height = $('body').attr('offsetHeight'); //IE
					if(typeof nc_height == 'undefined') var nc_height = $(document).attr('height');	//firefox
					if(typeof nc_height == 'undefined') var nc_height = $(document).height(); //Opera
		  }
		var gm_height = $(document).height() + 150;

		var t_append_to = 'body';
		if(navigator.appVersion.match(/MSIE [0-7]\./) && $('#container').length > 0)
		{
			t_append_to = '#container';
		}

      return jQuery('<div></div>').attr({
              id: 					'__dimScreen',
              fade_opacity: opacity,
              speed: 				speed
          }).css({
			background: '#000000',
			//height: 		gm_height + 'px',
          	left: 			'0px',
          	opacity: 		'0',
          	position: 	'absolute',
          	top: 				'0px',
          	width: 			'100%',
          	zIndex: 		'999'
          }).appendTo(t_append_to).fadeTo(speed, opacity, callback);


	  },

	  //stops current dimming of the screen
	  dimScreenStop: function(callback) {
      var x = jQuery('#__dimScreen');
      var opacity = x.attr('fade_opacity');
      var speed = x.attr('speed');
      x.fadeOut(speed, function() {
        x.remove();
        if(typeof callback == 'function') callback();
      });
	  }
	});

	//dimScreen()
	//by Brandon Goldman
	jQuery.extend({
	  //dims the screen
	  dimScreenIFrame: function(speed, opacity, callback, nc_height) {
      if(jQuery('#__dimScreenIFrame').size() > 0) return;

      if(typeof speed == 'function') {
        callback = speed;
        speed = null;
      }

      if(typeof opacity == 'function') {
        callback = opacity;
        opacity = null;
      }

      if(speed < 1) {
        var placeholder = opacity;
        opacity = speed;
        speed = placeholder;
      }

      if(opacity >= 1) {
        var placeholder = speed;
        speed = opacity;
        opacity = placeholder;
      }

      speed = (speed > 0) ? speed : 500;
      opacity = (opacity > 0) ? opacity : 0.5;

      //NC_MB_MOD
		if(typeof nc_height == 'undefined'){
					if(typeof nc_height == 'undefined') var nc_height = $('body').attr('offsetHeight'); //IE
					if(typeof nc_height == 'undefined') var nc_height = $(document).attr('height');	//firefox
					if(typeof nc_height == 'undefined') var nc_height = $(document).height(); //Opera
		  }
		var gm_height = $(document).height() + 150;

		var t_append_to = 'body';
		if(navigator.appVersion.match(/MSIE [0-7]\./) && $('#container').length > 0)
		{
			t_append_to = '#container';
		}

      return jQuery('<div></div>').attr({
              id: 					'__dimScreenIFrame',
              fade_opacity: opacity,
              speed: 				speed
          }).css({
			background: '#000000',
			height: 		gm_height + 'px',
          	left: 			'0px',
          	opacity: 		'0',
          	position: 	'absolute',
          	top: 				'0px',
          	width: 			'100%',
          	zIndex: 		'1001'
          }).appendTo(t_append_to).fadeTo(speed, opacity, callback);


	  },

	  //stops current dimming of the screen
	  dimScreenStopIFrame: function(callback) {
      var x = jQuery('#__dimScreenIFrame');
      var opacity = x.attr('fade_opacity');
      var speed = x.attr('speed');
      x.fadeOut(speed, function() {
        x.remove();
        if(typeof callback == 'function') callback();
      });
	  }
	});


	// click events with lightbox-classes
	$(document).ready(function()
	{
		$('.lightbox_iframe').live('click', function(e)
		{
			if(fb)console.log('.lightbox_iframe click');
			var t_iframe_url = $(this).attr('href');
			gmLightBox.load_iframe(t_iframe_url);

			return false;
		});

		$('.gx_microshop_lightbox').live('click', function(e)
		{
			var t_iframe_url = $(this).attr('href');
			gmLightBox.load_iframe(t_iframe_url, 750);

			return false;
		});
	});



	// usage: gmLightBox.load_iframe('test.html', 850, 600);
	this.load_iframe = function(p_url, p_width, p_height)
	{
		if(typeof p_width == 'undefined')  var p_width = 850;
		if(typeof p_height == 'undefined') var p_height = 600;


		// create container for iframe
		$('#iframe_layer').remove();
		$('body').append('<div id="iframe_layer"><div id="iframe_box_bar"><a href="#" onclick="return gmLightBox.close_iframe_box()" class="icon_lightbox_close">&nbsp;</a></div><div id="iframe_box"></div></div>');
		$('#iframe_layer').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 			'50px',
			width: 			'100%',
			height: 		'100%'
		});


		$('#iframe_box').css(
		{
			marginLeft:		'auto',
			marginRight:	'auto',
			width: 			p_width  +'px',
			height: 		p_height +'px',
			backgroundColor:'white'
		});
		$('#iframe_box_bar').css(
		{
			marginLeft:		'auto',
			marginRight:	'auto',
			width: 			p_width  +'px',
			color:			'white',
			textAlign:		'right'
		});



		// load iframe into box
		$('#iframe_box').html('<iframe src="'+ p_url +'" width="100%" height="100%" frameborder="0"></iframe>');

		this.load_box('#iframe_layer', 100, true);

		// BOF MOD by PT
		$('#menubox_gm_scroller').css({
			display: 'none'
		});

		if($(document).height() > $('#product_images_box').height()) {
			var pt_height = $(document).height();
		} else {
			var pt_height = $('#product_images_box').height()+ 200;
		}
		$('#__dimScreen').css({height: pt_height + 'px'});
		// EOF MOD by PT

	}

	this.load_box = function(box_id, fade_background_speed, fade_in, mb_height)
	{
		// scroll to top:
		scroll(0,0);

		if(fb)console.log('load_box: ' + box_id);

		if(typeof v == 'undefined') var fade_background_speed = 100;
		if(typeof fade_in == 'undefined') var fade_in = true;

		current_box_id = box_id;

		if(navigator.appVersion.match(/MSIE [0-6]\./))
		{
			this.ie6_fix(true);
		}

		if(box_id == '#iframe_layer')
		{
			$(current_box_id).css(
			{
				zIndex: 		'1002',
				display:		'none'
			});

			$.dimScreenIFrame(fade_background_speed, 0.7, function()
			{
				if(fb)console.log('dim done:' + current_box_id);
				if(fade_in) $(current_box_id).fadeIn();
				else $(current_box_id).show();
			}, mb_height);
		}
		else
		{
			$(current_box_id).css(
			{
				zIndex: 		'1000',
				display:		'none'
			});

			$.dimScreen(fade_background_speed, 0.7, function()
			{
				if(fb)console.log('dim done:' + current_box_id);
				if(fade_in) $(current_box_id).fadeIn();
				else $(current_box_id).show();
			}, mb_height);
		}

	}

	this.close_iframe_box = function()
	{
		var t_close = true;
		if(typeof(gm_style_edit_mode_running) == 'boolean' && gm_style_edit_mode_running == true)
		{
			t_close = confirm('<?php echo ADMIN_LINK_INFO_TEXT; ?>');
		}

		if(t_close)
		{
			if(typeof(gmLightBox) != 'undefined')
			{
				$('#iframe_box_bar').remove();
				$('#iframe_box').remove();

				this.close_box('#iframe_box');
			}
		}

		return false;
	}


	this.close_box = function(p_box_id, p_current_box_id)
	{
		if(fb)console.log(current_box_id + ': close_box()');

		// BOF MOD by PT
		$('#menubox_gm_scroller').css({
			display: 'block'
		});
		// EOF MOD by PT

		if(typeof(p_box_id) == 'undefined' || p_box_id != '#iframe_box')
		{
			$.dimScreenStop();
		}
		else
		{
			$.dimScreenStopIFrame();
		}

        if(typeof(p_current_box_id) != 'undefined')
        {
            current_box_id = p_current_box_id;
        }

		$(current_box_id).fadeOut("normal", function(){
			if(navigator.appVersion.match(/MSIE [0-6]\./))
			{
				$('.lightbox_visibility_hidden').css(
				{
					visibility: 	'visible'
				});
				coo_this.ie6_fix(false);
			}
			$(this).remove();
		});

		current_box_id = '';
	}

	this.centered_left = function(element_width) {
		var x = (screen.width / 2) - (element_width / 2);
		if(fb)console.log('centered width:' + x);
		return Math.round(x);
	}
	this.centered_top = function(element_height) {
		var y = (screen.height / 2) - (element_height / 2);
		if(fb)console.log('centered height:' + y);
		return Math.round(y);
	}

	this.ie6_fix = function(p_hide)
	{
		if(p_hide)
		{
			$('select').each(function()
			{
				if($(this).css('visibility') != 'hidden' && $(this).css('display') != 'none')
				{
					t_ie6_elements_array.push(this);
					$(this).css(
					{
						visibility: 'hidden'
					});
				}
			});
		}
		else
		{
			for(var i = 0; i < t_ie6_elements_array.length; i++)
			{
				$(t_ie6_elements_array[i]).css(
				{
					visibility: 'visible'
				});
			}
		}

	}


	this.test = function() {
		$('.wrap_shop').append('<div id="test_box" onClick="gmLightBox.close_box()"></div>');
		$('#test_box').css(
		{
			position: 	'absolute',
			left: 			this.centered_left(500) + 'px',
			top: 				'150px',
			width: 			'500px',
			height: 		'0px',
			background: 'white'
		});
		this.load_box('#test_box');
	}

}
/*<?php
}
?>*/