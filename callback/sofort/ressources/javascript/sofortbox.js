// <![CDATA[
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2012-11-21 12:09:39 +0100 (Wed, 21 Nov 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofortbox.js 5725 2012-11-21 11:09:39Z rotsch $
 */
function sofortOverlay(element, ajaxScript, fromUrl) {
	var overlayObj = {
		id : null,
		overlayElement : null,
		state : 'none',
		content : '',
		init : function(element, ajaxScript, fromUrl) {
			this.overlayElement = element;
			jQuery(document).keyup(function(e) {
				if (e.keyCode == 27 && overlayObj.state == 'block') { 
					overlayObj.trigger();
				}
			});
			jQuery(element).find('.closeButton').bind('click', function() {
				overlayObj.trigger();
			});
			jQuery(element).find('.loader').css('border', '10px solid #C0C0C0');
			this.setContent(ajaxScript, fromUrl, jQuery(element).find('.content'));
			return this;
		},
		setContent : function(ajaxScript, fromUrl, toElement) {
			var content = $.ajax({
				url: ajaxScript,
				type: "post",
				data: "url="+fromUrl,
				success : function(response) {
					jQuery(toElement).html(response);
					jQuery(toElement).show();
					jQuery(toElement).css('white-space', 'normal');
					overlayObj.content = response;
				}
			});
		},
		setOverlayElement : function(state) {
			this.overlayElement.css('display', state);
			this.state = state;
		},
		trigger : function() {
			if(this.state == 'none') {
				this.setOverlayElement('block');
			} else if(this.state == 'block') {
				this.setOverlayElement('none');
			}
		}
	};
	var obj = overlayObj.init(element, ajaxScript, fromUrl);
	return obj;
}
//]]>