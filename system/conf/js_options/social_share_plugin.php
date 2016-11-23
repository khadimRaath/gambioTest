<?php
/*
#   --------------------------------------------------------------
#   social_share_plugin.js 2015-08-18
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/

	$t_local_code = $_SESSION['language_code'];
	
	switch($t_local_code){
		case 'en': $t_local_language_code = 'US';
			break;
		default: $t_local_language_code = strtoupper($t_local_code);
	}
	
	$array['social_share'] = array();
	$array['social_share']['facebook_share_box'] = array();
    $array['social_share']['facebook_share_box']['image'] = "<img src='templates/" . CURRENT_TEMPLATE . "/img/social_share_dummy_facebook.png' alt='' title='Facebook' />";
    $array['social_share']['facebook_share_box']['code'] = "<iframe src='https://www.facebook.com/plugins/like.php?locale=" . $t_local_code . "_" . $t_local_language_code . "&amp;href=#location_encoded#&amp;send=false&amp;layout=button_count&amp;width=115&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;height=20' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:115px; height:20px;' allowTransparency='true'></iframe>";
    $array['social_share']['twitter_share_box'] = array();
    $array['social_share']['twitter_share_box']['image'] = "<img src='templates/" . CURRENT_TEMPLATE . "/img/social_share_dummy_twitter.png' alt='' title='Twitter' />";
    $array['social_share']['twitter_share_box']['code'] = "<iframe allowtransparency='true' frameborder='0' scrolling='no' src='https://platform.twitter.com/widgets/tweet_button.html?url=#location_encoded#&amp;count=none&amp;counturl=" . HTTP_SERVER . DIR_WS_CATALOG . "&amp;text=#text#&amp;count=data-count&amp;lang=" . $t_local_code . "' style='width:115px; height:20px;' class='twitter-count-none'></iframe>";
    $array['social_share']['googleplus_share_box'] = array();
    $array['social_share']['googleplus_share_box']['image'] = "<img src='templates/" . CURRENT_TEMPLATE . "/img/social_share_dummy_googleplus.png' alt='' title='Google +1' />";
    $array['social_share']['googleplus_share_box']['code'] = "<div class='g-plusone' data-size='medium' data-href='#location#'></div><script type='text/javascript'>window.___gcfg = {lang: '" . $t_local_code . "'}; (function() { var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s); })(); </script>";
    $array['social_share']['pinterest_share_box'] = array();
    $array['social_share']['pinterest_share_box']['image'] = "<img src='templates/" . CURRENT_TEMPLATE . "/img/social_share_dummy_pinterest.png' alt='' title='Pinterest' />";
	$array['social_share']['pinterest_share_box']['code'] = "<a href='https://pinterest.com/pin/create/button/?url=#location_encoded#&amp;media=#product_image#&amp;description=#text#' rel='nofollow' data-pin-do='buttonPin' data-pin-config='none'><img src='https://assets.pinterest.com/images/pidgets/pin_it_button.png' alt='Pinterest' /></a><script type='text/javascript' src='https://assets.pinterest.com/js/pinit.js'></script>";