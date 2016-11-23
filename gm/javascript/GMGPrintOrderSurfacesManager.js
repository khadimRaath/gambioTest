/* GMGPrintOrderSurfacesManager.js <?php
#   --------------------------------------------------------------
#   GMGPrintOrderSurfacesManager.js 2013-11-14 gm
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('2 Y(f){1.3=B Z();1.8=\'\';1.k=0;1.l=f;1.C=2(a,b,c,d){9 e=B 10(d,1);1.3[d]=e;1.3[d].11(b);1.3[d].12(c);1.3[d].q(a);1.m(d)};1.D=2(b){9 c,r,h,13,E=F(b);14.15({16:\'17=18&19=D&1a=\'+E+\'&1b=\'+1c,1d:\'1e.1f\',1g:\'1h\',1i:"1j",1k:1l,1m:2(a){c=a}});s(c.k!=\'0\'){1.G();1n(9 d 1o c.3){s(h==1p){h=d}1.C(c.3[d].8,c.3[d].1q,c.3[d].1r,d);1.n(d);r=c.3[d].1s;1.3[d].1t(r)}1.q(c.8);1.m(h);1.n(h);1.H()}1.i()};1.H=2(){9 b=1;$(\'#t\'+1.4()+\' .j, #t\'+1.4()+\' .5\').1u(2(){9 a=$(1).I(\'o\');a=a.1v(/1w/g,\'\');J=F(a);b.m(J);b.n(b.K())})};1.n=2(a){$(\'#t\'+1.4()+\' .5\').L(2(){$(1).M(\'5\');$(1).N(\'j\')});$(\'#p\'+1.4()+\' .O\').L(2(){$(1).1x()});s($(\'#6\'+a).I(\'o\')!=\'6\'+a){$(\'#7\'+1.4()).P(\'<Q R="5" o="6\'+1.3[a].S()+\'"><u>\'+1.3[a].v()+\'</u></Q>\');$(\'#p\'+1.4()).P(\'<T R="O" o="U\'+1.3[a].S()+\'" 1y="1z: 1A; 1B: 1C; 1D: \'+1.3[a].1E()+\'V; 1F: \'+1.3[a].1G()+\'V;"></T>\')}1H{$(\'#6\'+a).M(\'j\');$(\'#6\'+a+\' u\').w(1.3[a].v());$(\'#6\'+a).N(\'5\');$(\'#U\'+a).i()}$(\'#7\'+1.4()+\' .j\').W(2(){$(1).x({\'y-z\':\'1I\'})});$(\'#7\'+1.4()+\' .5\').W(2(){$(1).x({\'y-z\':\'X\'})});$(\'#7\'+1.4()+\' .j\').1J(2(){$(1).x({\'y-z\':\'X\'})})};1.G=2(){$(\'#7\'+1.4()).w(\'\');$(\'#p\'+1.4()).w(\'\')};1.i=2(){$(\'#7\'+1.4()).i();$(\'#p\'+1.4()).i()};1.q=2(a){1.8=1K(a)};1.v=2(){A 1.8};1.m=2(a){1.k=a};1.K=2(){A 1.k};1.1L=2(a){1.l=l};1.4=2(){A 1.l}}',62,110,'|this|function|v_surfaces|get_surfaces_groups_id|gm_gprint_tab_active|tab_|gm_gprint_tabs_|v_name|var||||||||t_first_surfaces_id|show|gm_gprint_tab|v_current_surfaces_id|v_surfaces_groups_id|set_current_surfaces_id|display_surface|id|gm_gprint_content_|set_name|coo_elements|if|order_surfaces_groups_id_|span|get_name|html|css|text|decoration|return|new|load_surface|load_surfaces_group|c_surfaces_groups_id|gm_gprint_clear_number|reset_display|activate_tabs|attr|c_clicked_surfaces_id|get_current_surfaces_id|each|removeClass|addClass|gm_gprint_surface|append|li|class|get_surfaces_id|div|surface_|px|mouseover|none|GMGPrintOrderSurfacesManager|Object|GMGPrintOrderSurfaces|set_width|set_height|c_product|jQuery|ajax|data|module|GPrintOrder|action|surfaces_groups_id|XTCsid|gm_session_id|url|request_port|php|dataType|json|type|GET|async|false|success|for|in|null|v_width|v_height|v_elements|load_elements|click|replace|gm_gprint_tab_|hide|style|overflow|hidden|position|relative|width|get_width|height|get_height|else|underline|mouseout|gm_unescape|set_surfaces_groups_id'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMGPrintOrderSurfacesManager(p_surfaces_groups_id)
{
    this.v_surfaces = new Object();
    this.v_name = '';
    this.v_current_surfaces_id = 0;
    this.v_surfaces_groups_id = p_surfaces_groups_id;

    this.load_surface = function(p_name, p_width, p_height, p_surfaces_id)
	{
		var coo_surface = new GMGPrintOrderSurfaces(p_surfaces_id, this);

        this.v_surfaces[p_surfaces_id] = coo_surface;
        this.v_surfaces[p_surfaces_id].set_width(p_width);
        this.v_surfaces[p_surfaces_id].set_height(p_height);
        this.v_surfaces[p_surfaces_id].set_name(p_name);

        this.set_current_surfaces_id(p_surfaces_id);
	}

    this.load_surfaces_group = function(p_surfaces_groups_id)
	{
        var coo_surfaces_group;
		var coo_elements;
		var t_first_surfaces_id;
		var c_product;
		var c_surfaces_groups_id = gm_gprint_clear_number(p_surfaces_groups_id);

		jQuery.ajax({
            data: 'module=GPrintOrder&action=load_surfaces_group&surfaces_groups_id=' + c_surfaces_groups_id + '&XTCsid=' + gm_session_id,
            url: 'request_port.php',
            dataType: 'json',
            type: "GET",
            async: false,
            success: function(p_surfaces_group)
			{
				coo_surfaces_group = p_surfaces_group;
			}
        });

		if(coo_surfaces_group.v_current_surfaces_id != '0')
		{
			this.reset_display();

			for(var t_surfaces_id in coo_surfaces_group.v_surfaces)
			{
				if(t_first_surfaces_id == null)
				{
					t_first_surfaces_id = t_surfaces_id;
				}

				this.load_surface(coo_surfaces_group.v_surfaces[t_surfaces_id].v_name, coo_surfaces_group.v_surfaces[t_surfaces_id].v_width, coo_surfaces_group.v_surfaces[t_surfaces_id].v_height, t_surfaces_id);
				this.display_surface(t_surfaces_id);

				coo_elements = coo_surfaces_group.v_surfaces[t_surfaces_id].v_elements;
				this.v_surfaces[t_surfaces_id].load_elements(coo_elements);
			}

			this.set_name(coo_surfaces_group.v_name);
			this.set_current_surfaces_id(t_first_surfaces_id);
			this.display_surface(t_first_surfaces_id);

			this.activate_tabs();
		}

		this.show();
    }

	this.activate_tabs = function()
	{

		var coo_surfaces_manager_copy = this;

		$('#order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab, #order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').click(function()
		{
			var f_clicked_surfaces_id = $(this).attr('id');
            f_clicked_surfaces_id = f_clicked_surfaces_id.replace(/gm_gprint_tab_/g, '');

            c_clicked_surfaces_id = gm_gprint_clear_number(f_clicked_surfaces_id);

            coo_surfaces_manager_copy.set_current_surfaces_id(c_clicked_surfaces_id);
            coo_surfaces_manager_copy.display_surface(coo_surfaces_manager_copy.get_current_surfaces_id());
        });
    }

    this.display_surface = function(p_surfaces_id)
	{
        $('#order_surfaces_groups_id_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').each(function()
		{
            $(this).removeClass('gm_gprint_tab_active');
            $(this).addClass('gm_gprint_tab');
        });

        $('#gm_gprint_content_' + this.get_surfaces_groups_id() + ' .gm_gprint_surface').each(function()
		{
            $(this).hide();
        });

        if($('#tab_' + p_surfaces_id).attr('id') != 'tab_' + p_surfaces_id)
		{
            $('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).append('<li class="gm_gprint_tab_active" id="tab_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '"><span>' + this.v_surfaces[p_surfaces_id].get_name() + '</span></li>');
            $('#gm_gprint_content_' + this.get_surfaces_groups_id()).append('<div class="gm_gprint_surface" id="surface_' + this.v_surfaces[p_surfaces_id].get_surfaces_id() + '" style="overflow: hidden; position: relative; width: ' + this.v_surfaces[p_surfaces_id].get_width() + 'px; height: ' + this.v_surfaces[p_surfaces_id].get_height() + 'px;"></div>');
        }
        else
		{
            $('#tab_' + p_surfaces_id).removeClass('gm_gprint_tab');
			$('#tab_' + p_surfaces_id + ' span').html(this.v_surfaces[p_surfaces_id].get_name());
			$('#tab_' + p_surfaces_id).addClass('gm_gprint_tab_active');
			$('#surface_' + p_surfaces_id).show();
        }

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'underline'
			});
		});

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab_active').mouseover(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});

		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id() + ' .gm_gprint_tab').mouseout(function()
		{
			$(this).css({
				'text-decoration': 'none'
			});
		});
    }

	this.reset_display = function()
	{
		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).html('');
		$('#gm_gprint_content_' + this.get_surfaces_groups_id()).html('');
	}

	this.show = function()
	{
		$('#gm_gprint_tabs_' + this.get_surfaces_groups_id()).show();
		$('#gm_gprint_content_' + this.get_surfaces_groups_id()).show();
	}

	this.set_name = function(p_name)
	{
		this.v_name = gm_unescape(p_name);
	}

	this.get_name = function()
	{
		return this.v_name;
	}

    this.set_current_surfaces_id = function(p_surfaces_id)
	{
        this.v_current_surfaces_id = p_surfaces_id;
    }

    this.get_current_surfaces_id = function()
	{
        return this.v_current_surfaces_id;
    }

    this.set_surfaces_groups_id = function(p_surfaces_groups_id)
	{
        this.v_surfaces_groups_id = v_surfaces_groups_id;
    }

    this.get_surfaces_groups_id = function()
	{
        return this.v_surfaces_groups_id;
    }
}
/*<?php
}
?>*/

