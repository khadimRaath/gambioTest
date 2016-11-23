/* article_tabs_delete.js <?php
 #   --------------------------------------------------------------
 #   article_tabs_delete.js 2014-01-03 tb@gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
var headingName = $(t_lightbox_package).find('.tab_message').html().replace('#tab_headline#',
                                                                            $(container).find('span').html());
$(t_lightbox_package).find('.tab_message').text(headingName);

$(".delete", t_lightbox_package).bind("click", function () {
	$(container).parent().remove();
	$.lightbox_plugin("close", t_lightbox_identifier);
	return false;
});