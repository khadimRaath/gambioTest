/*
	--------------------------------------------------------------
	gm_meta.js 2007-11-26 pt@gambio
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2007 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   
   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/

	/*
	* -> load contents
	*/
	function gm_get_content(action) {

		// -> show image while loading
		$("#gm_box_content").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');

		// -> load contents
		$("#gm_box_content").load(action, {}, function () {
			window.gx.widgets.init($('#gm_bookmarks_form'));
		});
	}


	/*
	* -> load additonal contents
	*/
	function gm_get_more_contents(action, box) {

		// -> show image while loading
		$("#" + box).html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');

		// -> load contents
		$("#" + box).load(action);
		$(".gm_more_hidden").fadeIn('normal');
		$("#gm_more_link").fadeOut('normal');

	}


	/*
	* -> update content 'get'
	*/
	function gm_update_boxes(action, box) {

		// -> show image while loading
		$("#gm_status").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');

		var getString = '';
		$.each($('#gm_bookmarks_form').get(0).elements, function(k, ele) {
			if(ele.id != '') {
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
