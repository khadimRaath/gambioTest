/* GMGPrintOrderElements.js <?php
#   --------------------------------------------------------------
#   GMGPrintOrderElements.js 2011-01-24 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 o(b){2.9=0;2.c=0;2.d=0;2.e=0;2.f=0;2.5=6;2.g=\'\';2.h=\'\';2.i=\'\';2.j=0;2.k=\'\';2.n=b;2.p=3(a){2.9=a};2.q=3(a){2.c=a};2.r=3(){4 2.9};2.s=3(){4 2.c};2.t=3(a){2.d=a};2.u=3(a){2.e=a};2.v=3(){4 2.d};2.w=3(){4 2.e};2.x=3(a){2.f=a};2.y=3(){4 2.f};2.z=3(a){l(a==\'1\'||a==\'7\'||a==1){2.5=7}m l(a==\'0\'||a==\'6\'||a==0){2.5=6}m l(a==7){2.5=7}m{2.5=6}};2.A=3(){4 2.5};2.B=3(a){2.g=8(a)};2.C=3(){4 2.g};2.D=3(){4 2.h};2.E=3(a){2.h=a};2.F=3(a){2.i=8(a)};2.G=3(){4 2.i};2.H=3(a){2.j=8(a)};2.I=3(){4 2.j};2.J=3(a){2.k=8(a)};2.K=3(){4 2.k};2.L=3(){4 2.n}}',48,48,'||this|function|return|v_show_name|false|true|gm_unescape|v_width|||v_height|v_position_x|v_position_y|v_z_index|v_name|v_type|v_value|v_uploads_id|v_download_key|if|else|v_elements_id|GMGPrintOrderElements|set_width|set_height|get_width|get_height|set_position_x|set_position_y|get_position_x|get_position_y|set_z_index|get_z_index|set_show_name|get_show_name|set_name|get_name|get_type|set_type|set_value|get_value|set_uploads_id|get_uploads_id|set_download_key|get_download_key|get_elements_id'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMGPrintOrderElements(p_elements_id)
{
    this.v_width = 0;
    this.v_height = 0;
    this.v_position_x = 0;
    this.v_position_y = 0;
    this.v_z_index = 0;
    this.v_show_name = false;
    this.v_name = '';
    this.v_type = '';
    this.v_value = '';
    this.v_uploads_id = 0;
    this.v_download_key = '';
    this.v_elements_id = p_elements_id;

    this.set_width = function(p_width)
	{
        this.v_width = p_width;
    }

    this.set_height = function(p_height)
	{
        this.v_height = p_height;
    }

    this.get_width = function()
	{
        return this.v_width;
    }

    this.get_height = function()
	{
        return this.v_height;
    }

    this.set_position_x = function(p_position_x)
	{
        this.v_position_x = p_position_x;
    }

    this.set_position_y = function(p_position_y)
	{
        this.v_position_y = p_position_y;
    }

    this.get_position_x = function()
	{
        return this.v_position_x;
    }

    this.get_position_y = function()
	{
        return this.v_position_y;
    }

    this.set_z_index = function(p_z_index)
	{
        this.v_z_index = p_z_index;
    }

    this.get_z_index = function()
	{
        return this.v_z_index;
    }

    this.set_show_name = function(p_show_name)
	{
    	if(p_show_name == '1' || p_show_name == 'true' || p_show_name == 1)
        {
        	this.v_show_name = true;
        }
        else if(p_show_name == '0' || p_show_name == 'false' || p_show_name == 0)
        {
        	this.v_show_name = false;
        }
        else if(p_show_name == true)
        {
        	this.v_show_name = true;
        }
        else
        {
        	this.v_show_name = false;
        }
    }

    this.get_show_name = function()
	{
        return this.v_show_name;
    }

    this.set_name = function(p_name)
	{
        this.v_name = gm_unescape(p_name);
    }

    this.get_name = function()
	{
        return this.v_name;
    }

    this.get_type = function()
	{
        return this.v_type;
    }

    this.set_type = function(p_type)
	{
        this.v_type = p_type;
    }

    this.set_value = function(p_value)
	{
    	this.v_value = gm_unescape(p_value);
    }

    this.get_value = function()
	{
    	return this.v_value;
    }

    this.set_uploads_id = function(p_uploads_id)
	{
    	this.v_uploads_id = gm_unescape(p_uploads_id);
    }

    this.get_uploads_id = function()
	{
    	return this.v_uploads_id;
    }

    this.set_download_key = function(p_download_key)
	{
    	this.v_download_key = gm_unescape(p_download_key);
    }

    this.get_download_key = function()
	{
    	return this.v_download_key;
    }

    this.get_elements_id = function()
	{
        return this.v_elements_id;
    }
}
/*<?php
}
?>*/

