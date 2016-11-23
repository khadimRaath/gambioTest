/* 
	--------------------------------------------------------------
	lightbox_plugin.js 2015-09-17 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
 
    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/

(function( $, jQuery ) 
{
	
	if( lightbox_array == undefined )
	{
		var lightbox_array = new Array();
	}

	var _Lightbox = function( p_target, p_identifier, settings, params ) 
	{
		this.console_log( "$.fn.lightbox_plugin" );
		
		this.settings = $.extend({}, jQuery.fn.lightbox_plugin.defaults, settings );
        this.params = params;
		
		this.target = p_target;
		this.lightbox_package;
		this.lightbox_shadow;
		this.lightbox_loading_image;
		
		this.identifier = p_identifier;
		
		this.lightbox_content_type = false;
		
		this.window_width = $( window ).width();
		this.window_height = $( window ).height();
		
		// global
		this.headline;
		
		// template
		this.template_name;
		this.template_section;
		this.template_parameters;
		
		// iframe
		this.iframe_link;
		
		// image + image_group
		this.actual_image = false;
		this.actual_image_orig_width;
		this.actual_image_orig_height;
		this.actual_image_ratio;
		this.image_group = false;
		this.image_group_name;
		this.image_group_size = -1;
		this.image_group_index = -1;
		
		this.initialize();
	}

	_Lightbox.prototype = {
		initialize: function()
		{
			this.get_configuration();
			this.check_settings();
			this.initialize_lightbox_html();
			this.initialize_border_html();
			this.initialize_headline_html();
			this.initialize_navigation_html();
			this.initialize_close_button_html();
			this.bind_events();
			this.get_content();
		},
		
		get_configuration: function()
		{
			this.console_log( "get_configuration" );

			// get headline
			this.headline = this.target.attr( "title" );

            var t_target_href;
            if (this.params && this.params.href !== undefined)
            {
                t_target_href = $.trim( this.params.href );
            }
            else
            {
                t_target_href = $.trim( this.target.attr( "href" ) );
            }
			
			var t_target_href_lowercase = t_target_href.toLowerCase();

			if( t_target_href_lowercase.indexOf (".jpg" ) >= 0 || t_target_href_lowercase.indexOf( ".jpeg" ) >= 0 || t_target_href_lowercase.indexOf( ".gif" ) >= 0 || t_target_href_lowercase.indexOf( ".png" ) >= 0 )
			{
				// link contains image (.jpg || .jpeg || .gif || .png)
				var t_target_rel = $.trim( this.target.attr( "rel" ) );
				t_target_rel = t_target_rel.split( " " )[0];

				if( t_target_rel == "" )
				{
					// single image
					this.lightbox_content_type = "image";
				}
				else
				{
					// image group
					this.lightbox_content_type = "image_group";

					// get image group data
					this.image_group_name = t_target_rel;
					this.image_group = $( "a[rel~='" + this.image_group_name + "']" );
					this.image_group_size = this.image_group.length;

					// get actual image_group index
					$.each( this.image_group, function( image_key, image_value )
					{
						if( $( image_value ).get( 0 ) == $( this.target ).get( 0 ))
						{
							this.image_group_index = image_key;
						}
					});
				}
			}
			else if( t_target_href_lowercase.indexOf( "#iframe" ) >= 0 )
			{
				// link contains iframe path
				this.lightbox_content_type = "iframe";

				this.iframe_link = t_target_href.replace( "#iframe", "" );
			}
			else if( t_target_href_lowercase.indexOf( ".html" ) >= 0 )
			{
				// link contains template information
				this.lightbox_content_type = "template";

				// get template parameters
				var t_template_parameters = {};
				var t_component_array_1 = t_target_href.split(/\?/g);
				if(t_component_array_1.length == 2)
				{
					var t_component_array_2 = t_component_array_1[1].split(/\&/g);
					for( var i = 0; i < t_component_array_2.length; i++ )
					{
						var t_param = t_component_array_2[i].split(/\=/g);
						if( t_param.length == 2 )
						{
							t_template_parameters[t_param[0]] = t_param[1];
						}
					}
				}
				t_template_parameters.identifier = this.identifier;
                t_template_parameters.lightbox_identifier = this.identifier;
                t_template_parameters.element = this.target;

				this.template_parameters = $.extend({}, t_template_parameters, this.params);

				// get template section & template name
				var t_template_name_array =  t_component_array_1[0].split(/\//g);  
				if(t_template_name_array.length == 2)
				{              
					this.template_name = t_template_name_array[1];
					this.template_section = t_template_name_array[0];
				}
				else
				{    
					this.template_name = t_template_name_array[0];
					this.template_section = "";
				}
			}
			else
			{
				this.console_log( "type of content is undefined" );
			}				
		},
		
		check_settings: function()
		{
			this.console_log( "check_settings" );

			// check settings for width
			if( this.settings.lightbox_width != "full" )
			{
				this.settings.lightbox_width = parseInt( this.settings.lightbox_width );
				if( isNaN( this.settings.lightbox_width ) || this.settings.lightbox_width <= 0 )
				{
					this.settings.lightbox_width = jQuery.fn.lightbox.defaults.lightbox_width;
				}
			}

			// check this.settings for height
			if( this.settings.lightbox_height != "full" && this.settings.lightbox_height != "auto" )
			{
				this.settings.lightbox_height = parseInt( this.settings.lightbox_height );
				if( isNaN( this.settings.lightbox_height ) || this.settings.lightbox_height <= 0 )
				{
					this.settings.lightbox_height = jQuery.fn.lightbox.defaults.lightbox_height;
				}
			}

			//this.settings.shadow_opacity
			this.settings.shadow_opacity = parseInt( this.settings.shadow_opacity );
			if( isNaN( this.settings.shadow_opacity ) || this.settings.shadow_opacity < 0 || this.settings.shadow_opacity > 100 )
			{
				this.settings.shadow_opacity = jQuery.fn.lightbox.defaults.shadow_opacity;
			}
		},
		
		initialize_lightbox_html: function()
		{
			this.console_log( "initialize_html" );
			
			var t_z_index = 1000 * ( $( ".lightbox_package" ).length + 1 );
			
			// generate lightbox_package
			this.lightbox_package = $( "<div></div>" ).attr( "id", "lightbox_package_" + this.identifier).addClass( "lightbox_package" );
            if(this.template_parameters)
            {
                
                if(this.template_parameters.component)
                {
                    $( this.lightbox_package ).attr( "data-gx-extension", this.template_parameters.component );
                }

                if(this.template_parameters.controller)
                {
                    $( this.lightbox_package ).attr( "data-gx-controller", this.template_parameters.controller );
                }

                if(this.template_parameters.widget)
                {
                    $( this.lightbox_package ).attr( "data-gx-widget", this.template_parameters.widget );
                }
                
                $( this.lightbox_package ).data( "lightboxParams", this.template_parameters );
            }
			$( this.lightbox_package ).css( "z-index", t_z_index + 100 );

			// generate lightbox_border_top
			var t_lightbox_border_top = $( "<div></div>" ).addClass( "lightbox_border_top" );
			$( t_lightbox_border_top ).appendTo( this.lightbox_package );

			// generate lightbox_wrapper
			var t_lightbox_wrapper = $( "<div></div>" ).addClass("lightbox_wrapper gx-container");
			$( t_lightbox_wrapper ).css( "background-color", this.settings.background_color );
			$( t_lightbox_wrapper ).css( "font-family", this.settings.content_font_family );
			$( t_lightbox_wrapper ).css( "font-size", this.settings.content_font_size );
			$( t_lightbox_wrapper ).css( "color", this.settings.content_color );

			if( this.settings.border_round == true )
			{
				$( t_lightbox_wrapper ).css( "padding", "0px 15px" );
			}
			$( t_lightbox_wrapper ).appendTo( this.lightbox_package );

			// generate lightbox_border_bottom
			var t_lightbox_border_bottom = $( "<div></div>" ).addClass( "lightbox_border_bottom" );
			$( t_lightbox_border_bottom ).appendTo( this.lightbox_package );

			// generate lightbox_header
			var t__lightbox_header = $( "<div></div>" ).addClass( "lightbox_header" );
			$( t__lightbox_header ).appendTo( t_lightbox_wrapper );

			// generate lightbox_content_container
			var t_lightbox_content_container = $( "<div></div>" ).addClass( "lightbox_content_container" );
			$( t_lightbox_content_container ).appendTo( t_lightbox_wrapper );

			// generate lightbox_footer
			var t_lightbox_footer = $( "<div></div>" ).addClass( "lightbox_footer" );
			$( t_lightbox_footer ).appendTo( t_lightbox_wrapper );

			if( this.settings.shadow_active )
			{
				// generate lightbox_shadow
				this.lightbox_shadow = $( "<div></div>" ).attr( "id", "lightbox_shadow_" + this.identifier ).addClass( "lightbox_shadow" );
				$( this.lightbox_shadow ).css( "background", this.settings.shadow_background_color );
				$( this.lightbox_shadow ).css( "z-index", t_z_index );
				$( this.lightbox_shadow ).appendTo( "body" );
			}

			if( this.settings.loading_image_active )
			{
				// generate lightbox_loading
				this.lightbox_loading_image = $( "<img></img>" ).addClass( "lightbox_loading" ).attr( "id", "lightbox_loading_" + this.identifier );
				$( this.lightbox_loading_image ).attr( "src", this.settings.loading_image_source );
				$( this.lightbox_loading_image ).css( "width", this.settings.loading_image_width );
				$( this.lightbox_loading_image ).css( "height", this.settings.loading_image_height );
				$( this.lightbox_loading_image ).css( "top", ( this.window_height / 2 ) - ( this.settings.loading_image_height / 2 ) )
				$( this.lightbox_loading_image ).css( "left", ( this.window_width / 2 ) - ( this.settings.loading_image_width / 2 ) );
				$( this.lightbox_loading_image ).css( "z-index", t_z_index + 50 );

				$( this.lightbox_loading_image ).appendTo( "body" );
			}

			$( this.lightbox_package ).appendTo( "body" );
		},
		
		initialize_border_html: function()
		{
			this.console_log( "initialize_border_html" );

			if( this.settings.border_round )
			{
				for( var i = 1; i <= 8; i++ )
				{						
					if( i <= 4 )
					{
						$( ".lightbox_border_top", this.lightbox_package ).append($("<b></b>").html("<!-- -->"));
					}
					else if( 4 < i <= 8 )
					{
						$( ".lightbox_border_bottom", this.lightbox_package ).append($("<b></b>").html("<!-- -->"));
					}
				}
				$( ".lightbox_border_top b", this.lightbox_package ).css( "background-color", this.settings.background_color );
				$( ".lightbox_border_bottom b", this.lightbox_package ).css( "background-color", this.settings.background_color );
			}
		},
		
		initialize_headline_html: function()
		{
			this.console_log( "initialize_headline_html" );

			if( $.trim( this.headline ) != "" )
			{
				var t_lightbox_headline = $( "<div></div>" ).addClass( "lightbox_headline" ).text( this.headline );
				$( t_lightbox_headline ).css( "font-family", this.settings.headline_font_family );
				$( t_lightbox_headline ).css( "font-size", this.settings.headline_font_size );
				$( t_lightbox_headline ).css( "color", this.settings.headline_color );
				$( t_lightbox_headline ).css( "font-weight", this.settings.headline_font_weight );
				$( t_lightbox_headline ).css( "text-decoration", this.settings.headline_text_decoration );
				$( t_lightbox_headline ).css( "text-transform", this.settings.headline_text_transform );

				switch( this.settings.headline_position )
				{
					case "top":
						$( t_lightbox_headline ).appendTo( $( ".lightbox_header", this.lightbox_package ) );
						break;

					case "bottom":
						$( t_lightbox_headline ).appendTo( $( ".lightbox_footer", this.lightbox_package ) );
						break;

					default:
						break;
				}
			}
		},
		
		initialize_navigation_html: function()
		{
			this.console_log( "initialize_navigation_html" );

			if( this.lightbox_content_type == "image_group" && this.image_group_size > 1 )
			{
				var t_lightbox_navigation = $( "<div></div>" ).addClass( "lightbox_navigation" );
				$( t_lightbox_navigation ).css( "font-family", this.settings.headline_font_family );
				$( t_lightbox_navigation ).css( "font-size", this.settings.headline_font_size );
				$( t_lightbox_navigation ).css( "color", this.settings.headline_color );
				$( t_lightbox_navigation ).css( "font-weight", this.settings.headline_font_weight );
				$( t_lightbox_navigation ).css( "text-decoration", this.settings.headline_text_decoration );
				$( t_lightbox_navigation ).css( "text-transform", this.settings.headline_text_transform );

				var t_navigation_button_prev = $( "<a></a>" ).addClass( "navigation_button_prev" ).attr( "href", "#" ).attr( "title", this.settings.navigation_button_prev_title ).html( "<!-- &nbsp; -->" );
				$( t_navigation_button_prev ).css( "background-image", "url(" + this.settings.navigation_button_prev_image + ")" );
				$( t_navigation_button_prev ).css( "width", this.settings.navigation_button_prev_width );
				$( t_navigation_button_prev ).css( "height", this.settings.navigation_button_prev_height );
				if( this.image_group_index == 0 )
				{
					$( t_navigation_button_prev ).addClass( "disabled" );
				}

				var t_navigation_text = $( "<div></div>" ).addClass( "navigation_text" ).html( "/" );
				var t_navigation_actual_index = $( "<div></div>" ).addClass( "navigation_actual_index" ).html( this.image_group_index + 1 );
				var t_navigation_group_size = $( "<div></div>" ).addClass( "navigation_group_size" ).html( this.image_group_size );
				$( t_navigation_text ).prepend( t_navigation_actual_index ).append( t_navigation_group_size );

				var t_navigation_button_next = $( "<a></a>" ).addClass( "navigation_button_next" ).attr( "href", "#" ).attr( "title", this.settings.navigation_button_next_title ).html( "<!-- &nbsp; -->" );
				$( t_navigation_button_next ).css( "background-image", "url(" + this.settings.navigation_button_next_image + ")" );
				$( t_navigation_button_next ).css( "width", this.settings.navigation_button_next_width );
				$( t_navigation_button_next ).css( "height", this.settings.navigation_button_next_height );
				if( this.image_group_index + 1 == this.image_group_size )
				{
					$( t_navigation_button_next ).addClass( "disabled" );
				}

				$( t_lightbox_navigation ).append( t_navigation_button_prev ).append( t_navigation_button_next ).append( t_navigation_text );

				switch( this.settings.navigation_position )
				{
					case "top":
						$( t_lightbox_navigation ).appendTo( $( ".lightbox_header", this.lightbox_package ) );
						break;

					case "bottom":
						$( t_lightbox_navigation ).appendTo( $( ".lightbox_footer", this.lightbox_package ) );
						break;

					default:
						break;
				}
			}
		},
		
		initialize_close_button_html: function()
		{
			this.console_log( "initialize_close_button_html" );

			var t_lightbox_package_close_button = $( "<div></div>" ).addClass( "lightbox_close_button lightbox_close" ).attr( "title", this.settings.close_button_title );
			$( t_lightbox_package_close_button ).css( "background-image", "url(" + this.settings.close_button_image + ")" );
			$( t_lightbox_package_close_button ).css( "width", this.settings.close_button_width );
			$( t_lightbox_package_close_button ).css( "height", this.settings.close_button_height );

			switch( this.settings.close_button_position )
			{
				case "top":
					$( t_lightbox_package_close_button ).appendTo( $( ".lightbox_header", this.lightbox_package ) );
					break;

				case "bottom":
					$( t_lightbox_package_close_button ).appendTo( $( ".lightbox_footer", this.lightbox_package ) );
					break;

				default:
					break;
			}
		},
		
		bind_events: function()
		{
			this.console_log( "bind_events" );

			$( this.lightbox_package ).delegate( ".lightbox_close", "click", $.proxy(function(){
				this.close_lightbox();
				return false;
			}, this));

			if( this.settings.shadow_close_onclick )
			{
				$( this.lightbox_shadow ).bind( "click", $.proxy(function(){
					this.close_lightbox();
					return false;
				}, this));
			}
			
			$( window ).bind( "resize." + this.identifier , $.proxy(function(){
				this.window_width = $( window ).width();
				this.window_height = $( window ).height();
				
				switch( this.lightbox_content_type )
				{
					case "template":
						this.update_template_view();
						break;

					case "iframe":
						break;

					case "image":
					case "image_group":
						this.update_image_view();
						break;	

					default:
						break;
				}
			}, this));
			
			$( window ).bind( "scroll." + this.identifier , $.proxy(function(){				
				switch( this.lightbox_content_type )
				{
					case "template":
						this.update_template_view();
						break;

					case "iframe":
						break;

					case "image":
					case "image_group":
						this.update_image_view();
						break;	

					default:
						break;
				}
			}, this));

			$( ".navigation_button_prev", this.lightbox_package ).bind( "click", $.proxy(function(){
				if( !$(this).hasClass( "disabled" ) && !$(this).hasClass( "active" ) )
				{
					$( ".navigation_button_next", this.lightbox_package ).addClass( "active" );
					$( this ).addClass( "active" );
					this.get_previous_image();
				}
				return false;
			}, this));

			$( ".navigation_button_next", this.lightbox_package ).bind( "click", $.proxy(function(){
				if( !$(this).hasClass( "disabled" ) && !$(this).hasClass( "active" ) )
				{
					$( ".navigation_button_prev", this.lightbox_package ).addClass( "active" );
					$( this ).addClass( "active" );
					this.get_next_image();
				}
				return false;
			}, this));
		},
		
		unbind_events: function()
		{
			this.console_log( "unbind_events" );

			$( this.lightbox_package ).undelegate( ".lightbox_close", "click" );

			$( this.lightbox_shadow ).unbind( "click" );

			$( window ).unbind( "resize." + this.identifier );

			$( ".navigation_button_prev", this.lightbox_package ).unbind( "click");

			$( ".navigation_button_next", this.lightbox_package ).unbind( "click");
		},
		
		get_content: function()
		{
			this.console_log( "get_content" );

			if( this.headline != "" )
			{
				$( ".lightbox_headline", this.lightbox_package ).text( this.headline );
			}
			else
			{
				$( ".lightbox_headline", this.lightbox_package ).empty();
			}

			switch( this.lightbox_content_type )
			{
				case "template":
					this.get_template_content();
					break;

				case "iframe":
					break;

				case "image":
				case "image_group":
					this.get_image_content();
					break;	

				default:
					break;
			}
		},
		
		get_template_content: function()
		{
			this.console_log( "get_template_content" );

            var temp_params =  $.extend({}, this.template_parameters);
            delete temp_params.element;

			$.ajax({
				type:       "GET",
				url:        "request_port.php?module=LightboxPluginAdmin",
				timeout:    30000,
				dataType:	"json",
				context:	this,
				data:      {
					"action":       "get_template", 
					"template":     this.template_name, 
					"section":		this.template_section, 
					"param":        temp_params
				},
				success:    function( template )
				{
                    if($.destroy_tooltip_plugin !== undefined)
                    {
                        $.destroy_tooltip_plugin();
                    }
					var t_contentContainer = $( ".lightbox_content_container", this.lightbox_package );

					$( t_contentContainer ).html( template.html );

					this.update_template_view();

					this.show_lightbox();

                    // Apply module initialization for JavaScript Engine. 
                    if(window.gx !== undefined && window.gx.components !== undefined)
                    {
                        $(this.lightbox_package).data();
                        window.gx.components.init($( this.lightbox_package ));
                    }

                    if(window.gx !== undefined && window.gx.controllers !== undefined)
                    {
                        $(this.lightbox_package).data();
                        window.gx.controllers.init($( this.lightbox_package ));
                    }

                    if(window.gx !== undefined && window.gx.widgets !== undefined)
                    {
                        $(this.lightbox_package).data();
                        window.gx.widgets.init($( this.lightbox_package ));
                    }
                    
					$('body').trigger('lightbox_loaded_' + this.identifier);
                    
                    if($.initialize_tooltip_plugin !== undefined)
                    {
                        $.initialize_tooltip_plugin();
                    }
				},
				error:      function( jqXHR, exception )
				{	
					this.check_error( jqXHR, exception );
					$('body').trigger('lightbox_loaded_' + this.identifier);
				}
			});
		},
		
		update_template_view: function()
		{
			if( this.settings.lightbox_width == "full" )
			{
				if( this.window_width - 60 > 220 )
				{
					$( this.lightbox_package ).width( this.window_width - 60 );
				}
				else
				{
					$( this.lightbox_package ).width( 220 );
				}
			}
			else
			{
				$( this.lightbox_package ).css( "width", this.settings.lightbox_width + 30 );
				$( ".lightbox_content_container", this.lightbox_package ).css( "width", this.settings.lightbox_width );
			}

			if( this.settings.bind_to_element && this.settings.lightbox_width != "full" )
			{
			// get position of element
			}
			else if( this.settings.lightbox_width == "full" )
			{
				$( this.lightbox_package ).css( "left", 0 );
				$( this.lightbox_package ).css( "margin-left", 30 );
			}
			else
			{
				$( this.lightbox_package ).css( "left", "50%" );
				$( this.lightbox_package ).css( "margin-left", ( ( this.settings.lightbox_width / 2 ) + 30 ) * -1  );
			}

			if( this.settings.lightbox_height == "full" )
			{
				if( this.window_height - 60 > 180 )
				{
					$( this.lightbox_package ).height( this.window_height - 60 );
				}
				else
				{
					$( this.lightbox_package ).height( 180 );
				}
				$( ".lightbox_wrapper", this.lightbox_package ).height( $( this.lightbox_package ).height() - $( ".lightbox_border_top", this.lightbox_package ).height() - $( ".lightbox_border_bottom", this.lightbox_package ).height() );
				$( ".lightbox_content_container", this.lightbox_package ).height( $( ".lightbox_wrapper", this.lightbox_package ).height() - $( ".lightbox_header", this.lightbox_package ).height() - $( ".lightbox_footer", this.lightbox_package ).height() );
			}
			else if( this.settings.lightbox_height == "auto" )
			{
				var t_scrolltop_position = $( ".lightbox_content_container", this.lightbox_package ).scrollTop();
				$( this.lightbox_package ).height( "auto" );
				$( ".lightbox_wrapper", this.lightbox_package ).height( "auto" );
				$( ".lightbox_content_container", this.lightbox_package ).height( "auto" );
				
				if( $( this.lightbox_package ).outerHeight( true ) > this.window_height )
				{
					var t_height = this.window_height - 60;
					if( t_height < 80 )
					{
						t_height = 80;
					}
					
					$( this.lightbox_package ).height( t_height );

					$( ".lightbox_wrapper", this.lightbox_package ).height( $( this.lightbox_package ).height() - $( ".lightbox_border_top", this.lightbox_package ).height() - $( ".lightbox_border_bottom", this.lightbox_package ).height() );
					$( ".lightbox_content_container", this.lightbox_package ).height( $( ".lightbox_wrapper", this.lightbox_package ).innerHeight() - $( ".lightbox_header", this.lightbox_package ).outerHeight() - $( ".lightbox_footer", this.lightbox_package ).outerHeight() - 10 );                       
					$( ".lightbox_content_container", this.lightbox_package ).css( "overflow", "auto" ); 
				}
				else
				{
					$( this.lightbox_package ).height( "auto" );
					$( ".lightbox_wrapper", this.lightbox_package ).height( "auto" );
					$( ".lightbox_content_container", this.lightbox_package ).height( "auto" );
					$( ".lightbox_content_container", this.lightbox_package ).css( "overflow", "auto" );    
				}
				$( ".lightbox_content_container", this.lightbox_package ).scrollTop( t_scrolltop_position );
			}
			else
			{
				$( ".lightbox_content_container", this.lightbox_package ).css('height', this.settings.lightbox_height);
			}
		},
		
		get_image_content: function()
		{
			this.console_log( "get_image_content" );

			// generate image_loading
			var t_image_loading = $( "<img></img>" ).addClass( "image_loading" );
			$( t_image_loading ).attr( "src", this.settings.loading_image_source );
			$( t_image_loading ).css( "width", this.settings.loading_image_width );
			$( t_image_loading ).css( "height", this.settings.loading_image_height );
			$( t_image_loading ).css( "top", ( $( this.actual_image ).height() / 2 ) - ( this.settings.loading_image_height / 2 ) );
			$( t_image_loading ).css( "left", ( $( this.actual_image ).width() / 2 ) - ( this.settings.loading_image_width / 2 ) );

			$( t_image_loading ).appendTo( $( ".lightbox_content_container", this.lightbox_package ) );

			this.actual_image = new Image();
			this.actual_image.onload = function()
			{
				$( ".lightbox_content_container", this.lightbox_package ).css( "text-align", "center" ).html( this );

				this.actual_image_orig_width = $(this).width();
				this.actual_image_orig_height = $(this).height();
				this.actual_image_ratio = this.actual_image_orig_width / this.actual_image_orig_height;

				this.headline = $( this.target ).attr( "title" );

				$( ".lightbox_headline", this.lightbox_package ).html( this.headline );

				if( this.lightbox_content_type == "image_group" )
				{
					$( ".navigation_button_prev", this.lightbox_package ).removeClass( "active" );
					$( ".navigation_button_next", this.lightbox_package ).removeClass( "active" );

					$( ".navigation_actual_index", this.lightbox_package ).html( this.image_group_index + 1 );

					$( ".navigation_button_prev", this.lightbox_package ).removeClass( "disabled" );
					$( ".navigation_button_next", this.lightbox_package ).removeClass( "disabled" );

					if( this.image_group_index == 0 )
					{
						$( ".navigation_button_prev", this.lightbox_package ).addClass( "disabled" );
					}

					if( this.image_group_index == this.image_group_size - 1 )
					{
						$( ".navigation_button_next", this.lightbox_package ).addClass( "disabled" );
					}
				}

				this.update_image_view();

				this.show_lightbox();
			};

			this.actual_image.onerror = function()
			{
				// image not found
				this.show_lightbox_error( "image_not_found", $( this.target ).attr( "href" ) );
			};

			this.actual_image.src = $( this.target ).attr( "href" );
		},

		get_previous_image: function()
		{
			this.console_log( "get_previous_image" );

			this.image_group_index = this.image_group_index - 1;

			this.target = this.image_group.get( this.image_group_index );

			this.get_image_content();			
		},

		get_next_image: function()
		{
			this.console_log( "get_next_image" );

			this.image_group_index = this.image_group_index + 1;

			this.target = this.image_group.get( this.image_group_index );

			this.get_image_content();
		},

		update_image_view: function()
		{
			// reset width + height
			$( this.actual_image ).width( this.actual_image_orig_width );
			$( this.actual_image ).height( this.actual_image_orig_height );
			$( ".lightbox_content_container", this.lightbox_package ).width( "auto" );
			$( ".lightbox_content_container", this.lightbox_package ).height( "auto" );

			var t_min_content_width = 160;
			var t_min_content_height = 120;

			var t_content_width;
			var t_content_height;

			var t_available_width = this.window_width - 110;
			var t_available_height = this.window_height - 60 - $( ".lightbox_border_top", this.lightbox_package ).height() - $( ".lightbox_border_bottom", this.lightbox_package ).height() - $( ".lightbox_header", this.lightbox_package ).height() - $( ".lightbox_footer", this.lightbox_package ).height();

			if( t_available_width < t_min_content_width )
			{
				t_available_width = t_min_content_width;
			}

			if( t_available_height < t_min_content_height )
			{
				t_available_height = t_min_content_height;
			}

			var t_image_width = $( this.actual_image ).width();
			var t_image_height  = $( this.actual_image ).height();

			if( t_image_width > t_available_width )
			{
				t_image_width = t_available_width;
				t_image_height = parseInt( t_image_width / this.actual_image_ratio );
			}

			if( t_image_height > t_available_height )
			{
				t_image_height = t_available_height;
				t_image_width = parseInt( t_image_height * v_actual_image_ratio );
			}

			if( this.actual_image_orig_width < t_min_content_width )
			{
				t_content_width = t_min_content_width;
			}
			else
			{
				t_content_width = t_image_width;
			}

			if( this.actual_image_orig_height < t_min_content_height )
			{
				t_content_height = t_min_content_height;
			}
			else
			{
				t_content_height = t_image_height;
			}

			$( this.actual_image ).width( t_image_width );
			$( this.actual_image ).height( t_image_height );

			$( ".lightbox_content_container", this.lightbox_package ).width(t_content_width);
			$( ".lightbox_content_container", this.lightbox_package ).height(t_content_height);

			if( this.lightbox_content_type == "image_group" && this.settings.headline_position == this.settings.navigation_position )
			{
				var t_navigation_margin = $( ".lightbox_navigation", this.lightbox_package ).position().left;
				$( ".lightbox_headline", this.lightbox_package ).width( t_navigation_margin );
			}

			if( this.settings.bind_to_element )
			{
			// get position of element
			}
			else
			{
				$( this.lightbox_package ).css( "left", "50%" );
				$( this.lightbox_package ).css( "margin-left", ( ( t_content_width / 2 ) + 30 ) * -1  );
			}
		},
		
		show_lightbox: function()
		{
			this.console_log( "show_lightbox" );
			
			$( this.target ).removeClass( "active" );

			if( this.settings.open_close_animation )
			{                    
				$( this.lightbox_package ).animate(
				{
					opacity: 1,
					filter: "Alpha(opacity=100)"
				}, ( this.settings.open_close_animation_time ), function(){});

				$( this.lightbox_shadow ).animate(
				{
					opacity: this.settings.shadow_opacity / 100,
					filter: "Alpha(opacity=" + this.settings.shadow_opacity + ")"
				}, ( this.settings.open_close_animation_time ), function(){});
			} 
			else 
			{
				$( this.lightbox_package ).css( "opacity", 1 ).css( "filter", "Alpha(opacity=100)" );
				$( this.lightbox_shadow ).css( "opacity", this.settings.shadow_opacity / 100 ).css( "filter", "Alpha(opacity=" + this.settings.shadow_opacity + ")" );
			}

			$( this.lightbox_loading_image ).remove();
		},
		
		close_lightbox: function()
		{
			this.console_log( "close_lightbox" );

            if($.destroy_tooltip_plugin !== undefined)
            {
                $.destroy_tooltip_plugin();
            }
			
			$( this.lightbox_loading_image ).remove();

			this.unbind_events();

			if( this.settings.open_close_animation )
			{

				$( this.lightbox_package ).animate(
				{
					opacity: 0,
					filter: "Alpha(opacity=0)"
				}, ( this.settings.open_close_animation_time ), $.proxy( function()
				{
					$( this.lightbox_package ).remove();
				}, this));

				$( this.lightbox_shadow ).animate(
				{
					opacity: 0,
					filter: "Alpha(opacity=0)"
				}, ( this.settings.open_close_animation_time ), $.proxy( function()
				{
					$( this.lightbox_shadow ).remove();
				}, this));
			}
			else
			{
				$( this.lightbox_package ).remove();
				$( this.lightbox_shadow ).remove();
			}

            if($.initialize_tooltip_plugin !== undefined)
            {
                $.initialize_tooltip_plugin();
            }
		},
		
		check_error: function( p_jqXHR, p_exception, p_custom_error_code )
		{
			var t_error_shown = false;
			var t_error_message;
			if( p_custom_error_code != undefined )
			{
				t_error_message = p_custom_error_code;
				$.each(js_options.error_handling, $.proxy(function( t_code, t_message )
				{
					if( t_error_message == t_code )
					{
						t_error_shown = true;
						this.show_error( t_code, "" );
					}
				}, this));
			}
			else
			{
				if( typeof p_jqXHR.responseText == "undefined")
				{
					p_jqXHR.responseText = "";
				}
				$.each(js_options.error_handling, $.proxy(function( t_error_code, t_error_message )
				{
					var t_error_code_regex = new RegExp( t_error_code.replace( /\//g, "" ) );
					if( p_jqXHR.responseText.match( t_error_code_regex ) && t_error_code.substr( 0,1 ) == "/" && p_jqXHR.responseText != "" )
					{
						t_error_shown = true;
						this.show_error( t_error_code, p_jqXHR );
					}
					else if( p_jqXHR.status == t_error_code )
					{
						t_error_shown = true;
						this.show_error( t_error_code, p_jqXHR );
					}
					else if( p_jqXHR.statusText == t_error_code )
					{
						t_error_shown = true;
						this.show_error( t_error_code, p_jqXHR );
					}
					else if( p_exception == t_error_code )
					{
						t_error_shown = true;
						this.show_error( t_error_code, p_jqXHR );
					}
				}, this));
			}
			if( t_error_shown == false && ( typeof p_jqXHR == 'string' || p_jqXHR.statusText != "abort" ) )
			{
				// unknown error
				this.show_error( "unknown_error", t_error_message );
			}
		},
		
		show_error: function( p_error_code, p_error_content )
		{
			this.console_log( "error: " + p_error_code );
				
			var t_error_message = js_options.error_handling[ p_error_code ];
			if( typeof p_error_content === "object" )
			{
				// system (ajax) error = replace content with error message
				this.headline = js_options.error_handling[ "error_headline" ];
				
				$( ".lightbox_close_button", this.lightbox_package ).show();
				
				$( ".lightbox_headline", this.lightbox_package ).css( "color", "#cc0000" ).html( this.headline );
				
				$( ".lightbox_content_container", this.lightbox_package ).css( "text-align", "center" ).html( "<span class='lightbox_content_error'>" + t_error_message + "</span>" );
				
				$( ".lightbox_navigation", this.lightbox_package ).hide();
			}
			else if( typeof p_error_content === "string" )
			{
				// custom error = show error message in error container
				$( ".lightbox_content_error", this.lightbox_package ).html( t_error_message + " " + p_error_content ).show();
			}

			this.update_template_view();

			this.show_lightbox();
		},
		
		console_log: function( p_message )
		{
			if( fb )
			{
				if( typeof( p_message ) == "object" )
				{
					console.log( p_message );
				}
				else
				{
					console.log( "lightbox: " + p_message );
				}                    
			}
		}
	};
	
	jQuery.extend(
	{
		lightbox_plugin: function( p_method, p_identifier, p_jqXHR, p_exception ) 
		{
			var t_lightbox_object = lightbox_array["_" + p_identifier];
			if( typeof t_lightbox_object == "undefined" )
			{
				if( fb ) console.log( "lightbox_plugin: lightbox not found" );
				return false;
			}
			
			switch( p_method )
			{
				case "close":
					t_lightbox_object.close_lightbox();
					lightbox_array.splice( $.inArray( "_" + p_identifier, lightbox_array ) ,1 );
					break;
				case "update_view":
					switch( t_lightbox_object.lightbox_content_type )
					{
						case "template":
							t_lightbox_object.update_template_view();
							break;

						case "iframe":
							break;

						case "image":
						case "image_group":
							t_lightbox_object.update_image_view();
							break;	

						default:
							break;
					}
					break;
				case "error":
					// check if ajax or custom error
					if( typeof p_jqXHR === "object" )
					{
						// jqXHR Object is given (ajax error)
						t_lightbox_object.check_error( p_jqXHR, p_exception );
					}
					else if( typeof p_jqXHR === "string" )
					{
						// Custom error (String) ist given
						t_lightbox_object.check_error( "", "", p_jqXHR );
					}
					break;
				default:
					if( fb ) console.log( "lightbox_plugin: method not exists" );
					break;
			}
			return false;
		}
	});

	jQuery.fn.lightbox_plugin = function( settings, params ) 
	{
		if( $( this ).hasClass( "active" ) ) return this;
		$( this ).addClass( "active" );
		
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		var lightbox_object = new _Lightbox( $(this), t_identifier, settings, params );
		
		lightbox_array["_" + t_identifier] = lightbox_object;
		
		return t_identifier;
	}

	jQuery.fn.lightbox_plugin.defaults = {
		
		lightbox_width: js_options.lightbox_plugin.width,
		lightbox_height: js_options.lightbox_plugin.height,
		
		bind_to_element: false,
		
		shadow_active: js_options.lightbox_plugin.shadow_active,
		shadow_opacity: js_options.lightbox_plugin.shadow_opacity,
		shadow_background_color: js_options.lightbox_plugin.shadow_background_color,
		shadow_close_onclick: js_options.lightbox_plugin.shadow_close_on_click,
		
		loading_image_active: js_options.lightbox_plugin.image_loading_active,
		loading_image_source: js_options.lightbox_plugin.image_loading_source,
		loading_image_width: js_options.lightbox_plugin.image_loading_width,
		loading_image_height: js_options.lightbox_plugin.image_loading_height,
		
		open_close_animation: js_options.lightbox_plugin.open_close_animate,
		open_close_animation_time: js_options.lightbox_plugin.open_close_animate_time,
		
		border_round: js_options.lightbox_plugin.border_round,
		
		headline_position: js_options.lightbox_plugin.headline_position,
		headline_font_family: "Verdana, sans-serif",
		headline_font_size: js_options.lightbox_plugin.headline_font_size,
		headline_color: js_options.lightbox_plugin.headline_color,
		headline_font_weight: js_options.lightbox_plugin.headline_font_weight,
		headline_text_decoration: js_options.lightbox_plugin.headline_text_decoration,
		headline_text_transform: js_options.lightbox_plugin.headline_text_transform,
		
		navigation_position: js_options.lightbox_plugin.navigation_position,
		navigation_button_prev_title: js_options.lightbox_plugin.navigation_button_prev_title,
		navigation_button_prev_image: js_options.lightbox_plugin.navigation_button_prev_image,
		navigation_button_prev_width: js_options.lightbox_plugin.navigation_button_prev_width,
		navigation_button_prev_height: js_options.lightbox_plugin.navigation_button_prev_height,
		
		navigation_button_next_title: js_options.lightbox_plugin.navigation_arrow_prev_text,
		navigation_button_next_image: js_options.lightbox_plugin.navigation_arrow_prev_text,
		navigation_button_next_width: js_options.lightbox_plugin.navigation_arrow_prev_text,
		navigation_button_next_height: js_options.lightbox_plugin.navigation_arrow_prev_text,
		
		close_button_position: js_options.lightbox_plugin.close_button_position,
		close_button_image: js_options.lightbox_plugin.close_button_image,
		close_button_width: js_options.lightbox_plugin.close_button_width,
		close_button_height: js_options.lightbox_plugin.close_button_height,
		close_button_title: js_options.lightbox_plugin.close_button_title,
		
		background_color: js_options.lightbox_plugin.background_color,
		content_font_family: js_options.lightbox_plugin.font_family,
		content_font_size: js_options.lightbox_plugin.font_size,
		content_color: js_options.lightbox_plugin.content_color
		
	};
})( jQuery, jQuery );

$( 'body' ).on( "click",  "a.lightbox_open", function()
{
    $( this ).lightbox_plugin();
    return false;
});

$( 'body' ).on( "click",  "a.lightbox_open_small", function()
{
    $( this ).lightbox_plugin(
        {
            'background_color': '#EFEFEF',
            'lightbox_width': '460px'
        });
    return false;
});