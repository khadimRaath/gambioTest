/* Findologic.js <?php
#   --------------------------------------------------------------
#   Findologic.js 2014-07-03 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

function preloadFilterImages (dom, maxW, maxH) {
	jQuery(dom).preload({
		onFinish:completePL,
		treshhold:5
	});
	function completePL (data) {
		if(data.found) {
			resizeImage(jQuery(data.original), maxW, maxH);
		}
	}
}
// resizes image to given dimensions
function resizeImage (original, maxW, maxH) {
	if (original.width() > maxW || original.height() > maxH) {
                if (original.width() * maxH > original.height() * maxW) {
                        newW = new String(maxW) + "px";
                        newH = new String(maxW * original.height() / original.width()) + "px";
                } else {
                        newH = new String(maxH) + "px";
                        newW = new String(maxH * original.width() / original.height()) + "px";
                }
        } else {
                newW = new String(original.width()) + "px";
                newH = new String(original.height()) + "px";
        }
	original.css({width:newW,height:newH});
	original.show();
}
function resizeToFit(id, width, height) {
        var img = jQuery("#"+id);
        img.removeAttr('width');
        img.removeAttr('height');
        var naturalImg = new Image();
        naturalImg.src = img.attr('src');
        if (naturalImg.width > width || naturalImg.height > height) {
                if (naturalImg.width * height > naturalImg.height * width) {
                        img.css('width', width + "px");
                        img.css('height',(width * naturalImg.height / naturalImg.width) + "px");
                } else {
                        img.css('height', height + "px");
                        img.css('width', (height * naturalImg.width / naturalImg.height) + "px");
                }
        } else {
                img.css('width', naturalImg.width + "px");
                img.css('height	', naturalImg.height + "px");
        }
        delete naturalImg;
}

jQuery(document).ready(function() {
	jQuery("#flExpandMoreFilters a").click(function() {
		jQuery("#flMoreFilters").slideToggle('fast');
		return false;
	});
	jQuery("img.flImageFilter").each(function() {
		preloadFilterImages(jQuery("#"+this.id), jQuery("#filter_img_width").val(), jQuery("#filter_img_height").val());
	});
});

<?php
$t_fl_lang = 'de';
if(isset($_SESSION['language_code']))
{
    $t_fl_lang = $_SESSION['language_code'];
}
$t_fl_shop_id = gm_get_conf("FL_SHOP_ID_".$t_fl_lang);
if(empty($t_fl_shop_id) !== true)
{
	$t_fl_shop_hash = strtoupper(md5($t_fl_shop_id));
	?>

	(function() {
		var flDataMain = "https://cdn.findologic.com/autocomplete/<?php echo $t_fl_shop_hash ?>/autocomplete.js";
		var flAutocomplete = document.createElement('script');
		flAutocomplete.type = 'text/javascript';
		flAutocomplete.async = true;
		flAutocomplete.src = "https://cdn.findologic.com/autocomplete/require.js";
		var s = document.getElementsByTagName('script')[0];
		flAutocomplete.setAttribute('data-main', flDataMain);
		s.parentNode.insertBefore(flAutocomplete, s);
	})();
	<?php
}
?>