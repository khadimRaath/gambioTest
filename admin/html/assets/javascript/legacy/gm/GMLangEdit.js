/* GMLangEdit.js <?php
#   --------------------------------------------------------------
#   GMLangEdit.js 2014-03-07 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

function GMLangEdit()
{
	this.content_backup = '';
	this.content_id = '';

	$('#constant_edit_needle').ready(function()
	{
		if(fb)console.log('constant_edit_needle ready');


		$('#go_search').click(function(e) {
			if(fb)console.log('constant_edit_needle change');
			gmLangEdit.find_needle($('#constant_edit_needle').attr('value'));
			$('.result_phrase_value').unbind();
		});

		$('#constant_edit_needle').keypress(function(e) {

			if(e.keyCode == 13) {
				gmLangEdit.find_needle($('#constant_edit_needle').attr('value'));
				$('.result_phrase_value').unbind();

				//gmLangEdit.send_form();
			}
		});
	});


	this.activate_edit_field = function()
	{

		$("#gm_status").fadeIn('fast');

		$('.result_phrase_value').click(function(e)
		{
			var content = $(this).html();

			gmLangEdit.content_backup = content;
			gmLangEdit.content_id = $(this).attr("id");

			$(this).html('<textarea name="phrase_value_content" rows="4" cols="80">'+ content +'</textarea><br/>');
			$(this).append('<input style="float:left;margin-right:6px" class="button" id="go_save" type="button" name="go_save" value="Speichern" />');
			$(this).append('<input class="button" id="go_cancel" type="button" name="go_cancel" value="Abbrechen" />');
			$('.result_phrase_value').unbind();


			$('#go_save').click(function(e) {

				var content 	 = $(this).parent().find('textarea').val();
				var content_id = $(this).parent().attr('id');
				if(fb)console.log('content_id:' + content_id + ' content: ' + content);

				jQuery.ajax({
					data: 'content=' + encodeURIComponent(content) + '&content_id=' + encodeURIComponent(content_id),
					url: 'request_port.php?module=AdminLangEdit&act=save_content',
					type: "POST",
					async: true
				});
				
				$(this).parent().text(content).html();


				$('.result_phrase_value').click(function(e) {
					gmLangEdit.activate_edit_field();
				});
			});

			$('#go_cancel').click(function(e) {
				if(fb)console.log('go_cancel');

				var content = $(this).parent().find('textarea').val();
				$(this).parent().html(gmLangEdit.content_backup);

				$('.result_phrase_value').click(function(e) {
					gmLangEdit.activate_edit_field();
				});
			});
		});
	};

	this.find_needle = function(needle)
	{
		jQuery.ajax({
			data: 'needle=' + encodeURIComponent(needle),
			url: 'request_port.php?module=AdminLangEdit&act=search',
            type: "POST",
            async: true,
            success: function(t_result)
			{
            	$("#results_box").html(t_result);
            	gmLangEdit.activate_edit_field();
			}
        }).html;
	};
}