/* parcel_service_edit.js <?php
 #   --------------------------------------------------------------
 #   parcel_services_overview.js 2014-10-01 tb@gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/

function show_confirm_lightbox(p_parent_identifier)
{
    var t_a_tag = $( "<a href='lightbox_confirm.html?section=parcel_services&amp;message=discard_changes_message&amp;buttons=cancel-discard'></a>" )
    var tmp_lightbox_identifier = $( t_a_tag ).lightbox_plugin(
    {
        'background_color': '#EFEFEF',
        'lightbox_width': '360px',
        'shadow_close_onclick': false
    });
    
    $('#lightbox_package_' + tmp_lightbox_identifier).on('click', '.discard', function()
    {
        $.lightbox_plugin('close', tmp_lightbox_identifier);
        $.lightbox_plugin('close', p_parent_identifier);
    });
    return false;
}

function update_current_context()
{
    var t_text = $('.parcel_service_name', t_lightbox_package).val();
    if($.trim(t_text) == '')
    {
        t_text = '-';
    }
    $( ".current_context span", t_lightbox_package ).text(t_text);
}

function save_parcel_service()
{
    $( "#parcel_service_edit_form", t_lightbox_package ).validation_plugin();

    if( $( "#parcel_service_edit_form .row.error", t_lightbox_package ).length > 0 ) return false;
        
    if( $(this).hasClass( "active" ) ) return false;
    $(this).addClass( "active" );

    $.ajax({
        type:       "POST",
        url:        "request_port.php?module=ParcelServices&action=save_parcel_service",
        timeout:    30000,
        dataType:	"json",
        context:	this,
        data:		{
            "parcel_service_id":	t_lightbox_parameters['_'+t_lightbox_identifier]['parcel_service_id'],
            "form_data":            $( "#parcel_service_edit_form *", t_lightbox_package ).serialize()
        },
        success:    function( p_response )
        {
            $("#parcel_services_wrapper").html(p_response.html);

            $( "#parcel_service_edit_form", t_lightbox_package ).form_changes_checker( 'initialize', false );
            $.lightbox_plugin('close', t_lightbox_identifier);
        },
        error:      function( p_jqXHR, p_exception )
        {
            $.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
        }
    });
}

function close_parcel_service_edit(p_event, p_confirmed)
{
    var t_form_changed = $("#parcel_service_edit_form", t_lightbox_package).form_changes_checker();
    
    if( t_form_changed.length > 0 && p_confirmed != true )
    {
        show_confirm_lightbox( t_lightbox_identifier, "click" );
    }
    else
    {
        $.lightbox_plugin('close', t_lightbox_identifier);
    }
}

$( t_lightbox_package ).delegate( ".save", "click", save_parcel_service);
$( t_lightbox_package ).delegate( ".close", "click", close_parcel_service_edit);

$( t_lightbox_package ).delegate( ".parcel_service_name", "keyup", update_current_context);

$("#parcel_service_edit_form", t_lightbox_package).form_changes_checker('initialize', false);