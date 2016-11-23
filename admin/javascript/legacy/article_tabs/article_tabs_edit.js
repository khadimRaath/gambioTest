/* article_tabs_edit.js <?php
 #   --------------------------------------------------------------
 #   article_tabs_edit.js 2014-01-03 tb@gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/

$(".tab_headline", t_lightbox_package).val($(container).find("input").val());
$(".tab_content_" + t_lightbox_identifier).val($(container).find("textarea").val());

if (use_wysiwyg == true) {
	CKEDITOR.replace("tab_content_" + t_lightbox_identifier, {
		filebrowserBrowseUrl: "includes/ckeditor/filemanager/index.html",
		language:             "<?php echo $_SESSION['language_code']; ?>",
		baseHref:             "<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>",
		enterMode:            CKEDITOR.ENTER_BR,
		shiftEnterMode:       CKEDITOR.ENTER_P,
		width:                "798px",
		height:               "300px"
	});
}

$(".save", t_lightbox_package).bind("click", function () {
	'use strict';
	var tabHeadline   = $.trim($(t_lightbox_package).find('.tab_headline').val()),
	    tmpTabContent = '';

	if (use_wysiwyg === true) {
		tmpTabContent = $.trim(CKEDITOR.instances['tab_content_' + t_lightbox_identifier].getData());
	} else {
		tmpTabContent = $trim($('.tab_content_' + t_lightbox_identifier).val());
	}
	if (tmpTabContent === "<br />\n" +
	                      "&nbsp;") {
		tmpTabContent = '';
	}

	if (tmpTabContent !== '' && tabHeadline !== '') {

		$(container).find('span').text(tabHeadline);
		$(container).find('input').val(tabHeadline);
		$(container).find('textarea').text(tmpTabContent);

		$.lightbox_plugin('close', t_lightbox_identifier);
	} else if (tabHeadline !== '') {
		$.lightbox_plugin('error', t_lightbox_identifier, 'article_tab_content_empty');
	} else {
		$.lightbox_plugin('error', t_lightbox_identifier, 'article_tab_headline_empty');
	}
	return false;
});