/* combis_main.js <?php
#   --------------------------------------------------------------
#   combis_main.js 2014-11-09 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

var tmp_lightbox_identifier;
var autobuild_combis = true;

if($('.box_properties_combis_row').length == 0){
    $(".properties_view1").show();
    $("#switch_view").hide();
    $("#sort_combis").hide();
    $("#settings").hide();
}else{
    $(".properties_view2").show();
}

$("#sort_combis a").unbind("click");
$("#sort_combis a").bind("click", function()
{
    var t_products_id = parseInt($("#box_properties_combis").attr("class").split("_")[1]);
    $.ajax(
    {
        url: "request_port.php?module=reset_combis_sort_order&products_id" + t_products_id,
        type: "GET",
        error: function(p_response)
        { 
            if(p_response.status == 0 || p_response.status == 404)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.connection_error);
            }
            else if(p_response.status == 200)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }  
        },
        success: function(p_response)
        { 
            if($.isEmptyObject(p_response))
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }
            else
            {
                if(fb)console.log(p_response.action + ': success');
                location.reload(true);
            }
        }
    });
    return false;
});

$("#switch_view a").unbind("click");
$("#switch_view a").bind("click", function()
{
    if($(".properties_view1").css("display") == "block")
    {
        $(".properties_view1").hide();
        $(".properties_view2").show();
        $("#sort_combis").css("visibility", "visible");
        $("#settings").css("visibility", "visible");
    }
    else
    {
        $(".properties_view1").show();
        $(".properties_view2").hide();
        $("#sort_combis").css("visibility", "hidden");
        $("#settings").css("visibility", "hidden");
    }
    $(".check_propertie").prop("checked", false);
    if($(".box_properties_combis_row").length > 0)
    {
        $(".properties_checkbox input").prop("checked", true);
        $("#check_all").prop("checked", false);
        $(".properties_table_content.disable").hide();
        $(".properties_checkbox input").unbind("click");
        $(".properties_checkbox input").bind("click", function()
        {
            $(this).prop("checked", true);
        });
        $(".properties_table_content.disable .properties_checkbox input").prop("checked", false);
        $(".delete_selected").hide();
    }
    else
    {
        $(".properties_checkbox input").prop("checked", false);
        $(".properties_table_content.disable").show();
        $(".properties_checkbox input").unbind("click");
        $("#switch_view").hide();
    }
    return false;
});


$(".button_display_container").die("click");
$(".button_display_container").live("click", function()
{
    var self = this;	
    if($(self).closest(".properties_table_content").hasClass("active"))
    {
        $(self).closest(".properties_table_content").removeClass("active");
    }
    else
    {
        $(".properties_table_content").removeClass("active");
        $(self).closest(".properties_table_content").addClass("active");
    }
    return false;
});

var t_products_id = parseInt($("#box_properties_combis").attr("class").split("_")[1]);
var t_properties_value_ids_array = new Object();

$(".startConfiguration").die("click");
$(".startConfiguration").live("click", function()
{
    if($(".properties_checkbox input:checked").length > 0)
    {
        t_properties_value_ids_array = new Object();
        $.each($(".properties_checkbox input:checked"), function()
        {
            var checkbox = this;
            var t_properties_id = $(checkbox).val();
            var t_properties_values_id = new Array();
            $.each($(checkbox).closest(".properties_table_content").find("option:selected"), function()
            {
                t_properties_values_id.push($(this).val());
            });
            if(t_properties_values_id.length == 0)
            {
                $.each($(checkbox).closest(".properties_table_content").find("option"), function()
                {
                    t_properties_values_id.push($(this).val());
                });
            }
            t_properties_value_ids_array[t_properties_id] = t_properties_values_id;
        });
        
        if($(this).attr('rel') == 'automatic')
        {
            save_admin_select(true);
        }
        else
        {
            save_admin_select(false);
        }
        
    }
    return false;
});

function save_admin_select(p_run_autobuild)
{
    $.ajax(
    {
        data: {"products_id": t_products_id, "properties_values_ids_array": t_properties_value_ids_array},
        url:	"request_port.php?module=PropertiesCombisAdmin&action=save&type=admin_select",
        type: "POST",
        dataType:	"text",
        error: function(p_response)
        { 
            if(p_response.status == 0 || p_response.status == 404)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.connection_error);
            }
            else if(p_response.status == 200)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }  
        },
        success: function(p_response)
        { 
            if($.isEmptyObject(p_response))
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }
            else
            {
                if(p_run_autobuild)
                {
                    var t_a_tag = $( "<a href='lightbox_progress.html?section=combis&amp;message=add_combis_automatically'></a>" );
                    tmp_lightbox_identifier = $( t_a_tag ).lightbox_plugin(
                    {
                        'lightbox_width': '360px'
                    });

                    $("body").on("lightbox_loaded_" + tmp_lightbox_identifier, function()
                    {
                        run_autobuild(0);
                    });
                }
                else
                {
                    if(fb)console.log(p_response.action + ': success');
                    $(".properties_view1").hide();
                    $(".properties_view2").show();
                }
            }
        }
    });
}

function run_autobuild(p_actual_value)
{
    if(autobuild_combis == false)
    {
        rebuild_properties_index();
    }
    $.ajax(
    {
        data: {"products_id": t_products_id, "properties_values_ids_array": t_properties_value_ids_array, 'actual_index': p_actual_value},
        url: "request_port.php?module=PropertiesCombisAdmin&action=run_autobuild",
        type: "POST",
        dataType: "json",
        error: function(p_response)
        { 
            if(p_response.status == 0 || p_response.status == 404)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.connection_error);
            }
            else if(p_response.status == 200)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }  
        },
        success: function(p_response)
        { 
            if($.isEmptyObject(p_response))
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }
            else
            {
                $(".lightbox_progress .progress_text").text( p_response.progress_text);
                $(".lightbox_progress .progress_marker").width( p_response.progress + "%" );
                $(".lightbox_progress .job").text(p_response.job);
                
                if(p_response.status == 'progress')
                {
                    run_autobuild(p_response.combis_last_index);
                }
                else
                {
                    if(fb)console.log(p_response.action + ': success');
                    if(fb)console.log('start rebuild_properties_index');
                    rebuild_properties_index();
                }
            }
        }
    });
}

function rebuild_properties_index()
{
    $.ajax(
    {
        data: {"products_id": t_products_id},
        url: "request_port.php?module=PropertiesCombisAdmin&action=rebuild_properties_index",
        type: "POST",
        dataType:	"json",
        error: function(p_response)
        { 
            if(p_response.status == 0 || p_response.status == 404)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.connection_error);
            }
            else if(p_response.status == 200)
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            } 
        },
        success: function(p_response)
        { 
            if($.isEmptyObject(p_response))
            {
                $('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
            }
            else
            {
                $(".lightbox_progress .progress_text").text( p_response.progress_text );
                $(".lightbox_progress .progress_marker").width( p_response.progress + "%" );
                $(".lightbox_progress .job").text(p_response.job);
                if(fb)console.log(p_response.action + ': success');
                setTimeout(function(){
                    location.reload();    
                }, 2000);
            }
        }
    });
}

$("#check_all").die("click");
$("#check_all").live("click", function()
{
    if($(this).is(":checked"))
    {
        $(".box_properties_combis_row input").prop("checked", true);
        $(".delete_selected").show();
    }
    else
    {
        $(".box_properties_combis_row input").prop("checked", false);
        $(".delete_selected").hide();
    }
});

$(".check_propertie").die("click");
$(".check_propertie").live("click", function()
{
    if($(this).is(":checked"))
    {
        $(".delete_selected").show();
        if($(".check_propertie:not(:checked)").length == 0)
        {
            $("#check_all").prop("checked", true);
        }
    }
    else
    {
        $("#check_all").prop("checked", false);
        if($(".check_propertie:checked").length == 0)
        {
            $(".delete_selected").hide();
        }
    }
});	

$(".combination_mode").die("click");
$(".combination_mode").live("click", function()
{
    if($(".properties_view2").hasClass("active"))
    {
        $(".properties_view2").removeClass("active");
    }
    else
    {
        $(".properties_view2").addClass("active");
    }
    return false;
});

$(".delete_selected").die("click");
$(".delete_selected").live("click", function()
{
    var inputs = new Array();
    $.each($(".check_propertie:checked"), function()
    {
        inputs.push(this.value);
    });
	var t_products_id = parseInt($("#box_properties_combis").attr("class").split("_")[1]);

    var template = "properties/properties_combis_delete_selected.html?properties_combis_id_array=" + inputs + "&products_id=" + t_products_id;        
    $(this).attr("href", template);
    $(this).lightbox_plugin('lightbox_open');
    return false;
});

$(".lightbox_progress .cancel").die("click");
$(".lightbox_progress .cancel").live("click", function()
{
    if($(this).hasClass("active"))
    {
        return false;
    }
    $(this).addClass("active");
    autobuild_combis = false;
    return false;
});