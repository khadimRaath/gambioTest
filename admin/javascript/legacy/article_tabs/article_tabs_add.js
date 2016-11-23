/* article_tabs_add.js <?php
 #   --------------------------------------------------------------
 #   article_tabs_add.js 2014-03-06 tb@gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/

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
	var tabHeadline    = $.trim($(t_lightbox_package).find('.tab_headline').val()),
	    tmpTabContent  = '',
	    id             = $(container).parent().attr('id'),
	    idArray        = id.split('_'),
	    langId         = idArray[1],
	    $input         = $('<input />'),
	    $textArea      = $('<textarea></textarea>'),
	    $gridContainer = $('<div></div>'),
	    $spanContainer = $('<div></div>'),
	    $iconContainer = $('<div></div>'),
	    $editIcon      = $('<i></i>'),
	    $removeIcon    = $('<i></i>');

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
		$input.attr({
			"type":  "text",
			"name":  "products_tab_headline_" + langId + '[]',
			"value": tabHeadline,
			"class": "hidden"
		});

		$textArea
			.attr('name', 'products_tab_' + langId + '[]')
			.addClass('hidden')
			.text(tmpTabContent);

		$editIcon.attr('class', 'fa fa-pencil cursor-pointer add-padding-right-5');
		$removeIcon.attr('class', 'fa fa-trash-o cursor-pointer');
		$iconContainer
			.attr('class', 'pull-right tab-icons-container')
			.append([$editIcon, $removeIcon]);

		$spanContainer
			.attr('class', 'span6')
			.append([$(
				'<i class="fa fa-sort add-margin-right-12"></i>' +
				'<span>' + tabHeadline + '</span>'), $iconContainer, $input, $textArea]);
		$gridContainer
			.attr('class', 'grid tab-section')
			.append($spanContainer);

		$('.tab-btn-container-' + langId).before($gridContainer);

		$.lightbox_plugin('close', t_lightbox_identifier);
	} else if (tabHeadline !== '') {
		$.lightbox_plugin('error', t_lightbox_identifier, 'article_tab_content_empty');
	} else {
		$.lightbox_plugin('error', t_lightbox_identifier, 'article_tab_headline_empty');
	}
	return false;
});