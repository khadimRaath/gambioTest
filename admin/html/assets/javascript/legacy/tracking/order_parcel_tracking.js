/* parcel_service_edit.js <?php
 #   --------------------------------------------------------------
 #   parcel_services_overview.js 2014-10-14 tb@gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/



function addTrackingCodeFromDetail(event)
{
    event.stopPropagation();

    if( $(this).hasClass( "active" ) ) return false;
    $(this).addClass( "active" );

    $.ajax({
        type:       "POST",
        url:        "request_port.php?module=ParcelServices&action=add_tracking_code",
        timeout:    30000,
        dataType:	"json",
        context:	this,
        data:		{

            "tracking_code":	$('.tracking_code_wrapper input[name="parcel_service_tracking_code"]').val(),
            "service_id":       $('.tracking_code_wrapper select[name="parcel_service"] option:selected').val(),
            "order_id":         $('#gm_order_id').val(),
            "page_token":       $('.page_token').val()
        },
        success:    function( p_response )
        {

            $(".tracking_code_wrapper").html(p_response.html);
        },
    });

    return false;
}

$('.tracking_code_wrapper').on("click", '.add_tracking_code', addTrackingCodeFromDetail);


function addTrackingCodeFromOverview(event)
{
    event.stopPropagation();

    if( $(this).hasClass( "active" ) ) return false;
    $(this).addClass( "active" );


    $.ajax({
        type:       "POST",
        url:        "request_port.php?module=ParcelServices&action=add_tracking_code",
        timeout:    30000,
        dataType:	"json",
        context:	this,
        data:		{

            "tracking_code":	$('.parcel_tracking_code_sidebox input[name="parcel_service_tracking_code"]').val(),
            "service_id":       $('.parcel_tracking_code_sidebox select[name="parcel_service"] option:selected').val(),
            "order_id":         $('.add_tracking_code').attr('data-order_id'),
            "page_token":       $('.page_token').val()
        },
        success:    function( p_response )
        {

            document.location.reload();

        },
    });

    return false;
}

$('.parcel_tracking_code_sidebox').on("click", '.add_tracking_code', addTrackingCodeFromOverview);