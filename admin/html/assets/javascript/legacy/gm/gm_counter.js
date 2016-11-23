/* gm_counter.js <?php
#   --------------------------------------------------------------
#   gm_counter.js 2015-09-21 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

// Compatibility menu handling 

$(document).ready(function() {
	$(document).on('click', '.nav-tab a', function () {
		var $parent = $(this).parents('.page-nav-tabs');

		if (navTabHtmlBackup != '') {
			$parent.find('.nav-tab').filter(function () {
				if ($(this).find('a').length == 0)
					return true;
				else
					return false;
			}).html(navTabHtmlBackup).removeClass('no-link');
		}

		navTabHtmlBackup = $(this).parent().html();
		$(this).parent().addClass('no-link');
		$(this).parent().text($(this).text());
	});
    
    $(document).on('click', '.tab-headline-wrapper a', function() {
        $(this).siblings('.active').removeClass('active');
        $(this).addClass('active');
    });
    
    // Fetch initial content for the page once the page compatibility mode is completely loaded.
    var interval = setInterval(function() {
        if ($('.page-nav-tabs .nav-tab').length > 0) {
            gm_get_content(pageSettings.initialContentUrl, "gm_counter_visitor", pageSettings.initialMenuUrl)
                .done(function() {
                    navTabHtmlBackup = $(".nav-tab:first").html();
                    $(".nav-tab:first").html($(".nav-tab:first a").text()).addClass("no-link");
                });
            clearInterval(interval);
        }
    }, 500); 
});

/*
* -> load contents
*/
function gm_get_content(action, submenu, submenu_link, session_id, resetIndex, $targetContainer) {
    if (typeof resetIndex == 'undefined') resetIndex = true;
	var deferred = $.Deferred();
	
	if(action.search(session_id) == -1)
	{
		action = action + '&XTCsid=' + session_id;
	}

	action = action
			+ '&gm_start='	+ escape($("#start-date").val())
			+ '&gm_end='	+ escape($("#end-date").val())
			+ '&gm_count='	+ escape($("#gm_count").val())
			+ '&gm_date='	+ escape($("#gm_date").val())
			+ '&gm_type='	+ escape($("#gm_type").val());

	// -> get count
	var gm_count = $("#gm_count").val();
	var gm_page = $("#gm_page").val();

	if(gm_count != null) {
		action = action + '&gm_count=' + gm_count;
	}

	if(gm_page != null) {
		action = action + '&gm_page=' + gm_page;
	}

	// -> show image while loading
	if(submenu != "") {
		$("#gm_box_content").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
	}
    
    $('.main.gx-container > div').hide();

    if (typeof $targetContainer == 'undefined') {
        $targetContainer = $('#gm_box_content');
        $('.ui-tabs').show();
    } else {
        $targetContainer.show();
    }
    
    
	// -> load contents
    $targetContainer.load(action, '', function() {

		var dates = $("#start-date, #end-date").datepicker(
			{			
				dayNamesMin: ['So', 'Mo','Di','Mi','Do','Fr','Sa'],
				monthNames: ['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
				firstDay: 1,				
				dateFormat: 'yy-mm-dd',
				changeMonth: false,
				onSelect: 
					function(selectedDate)
					{
						var option = this.id == "start-date" ? "minDate" : "maxDate",
						instance = $( this ).data( "datepicker" ),
						date = $.datepicker.parseDate(
							instance.settings.dateFormat ||
							$.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
							dates.not(this).datepicker( "option", option, date );
					}
			}
		);

		var dates_conf = $("#gm_counter_date").datepicker(
			{			
				dayNamesMin: ['So', 'Mo','Di','Mi','Do','Fr','Sa'],
				monthNames: ['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
				firstDay: 1,				
				dateFormat: 'yy-mm-dd',
				changeMonth: false
			}
		);



		if(submenu != "") {
			$("#gm_box_submenu").show('fast');
            var index = (!resetIndex) ? $('.tab-headline-wrapper a.active').index() : 0;
            
			$("#gm_box_submenu").load(submenu_link, function() {
                $('.tab-headline-wrapper a:eq(' + index + ')').addClass('active');
            });
		} else {
			$("#gm_box_submenu").hide('fast');
		}
		
		deferred.resolve();
	});
	
	return deferred.promise();
}

/*
* -> load contents
*/
function gm_get_selected_content(action, submenu, submenu_link) {

	// -> get count
	var gm_count = $("#gm_select_count").val();
	var gm_page = $("#gm_page").val();

	if(gm_count != null) {
		action = action + '&gm_count=' + gm_count;
	}

	if(gm_page != null) {
		action = action + '&gm_page=' + gm_page;
	}

	// -> show image while loading
	$("#gm_box_content").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');

	// -> load contents
	$("#gm_box_content").load(action, '', function() {

		if(submenu != "") {
			$("#gm_box_submenu").show('fast');
			$("#gm_box_submenu").load(submenu_link);
		} else {
			$("#gm_box_submenu").hide('fast');
		}
	});
}

/*
* -> update content 'get'
*/
function gm_update_boxes(action, box) {	

	// -> show image while loading
	$("#gm_status").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
	var getString = '';
	$.each($('#gm_counter_form').get(0).elements, function(k, ele) {
		if(ele.id != '' && ele.id.search(/PICKER/) == -1) {
			getString = ele.id + "=" +  escape(ele.value) + '&' + getString;
		}
	});
	getString = getString.substr(0, getString.length-1);
	gm_fadein_boxes(box);
	$("#" + box).load(action + "&" + getString);
}

/*
* -> fade out boxes
*/
function gm_fadeout_boxes(box) {	
	$("#" + box).fadeOut('normal');
}

/*
* -> hide boxes
*/
function gm_hide_boxes(box) {	
	$("#" + box).hide('fast');
}


/*
* -> fade in boxes
*/
function gm_fadein_boxes(box) {	
	$("#" + box).fadeIn('normal');
}