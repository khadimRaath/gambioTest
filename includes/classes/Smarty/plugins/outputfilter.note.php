<?php
/* --------------------------------------------------------------
   outputfilter.note.php 2016-09-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: outputfilter.note.php 779 2005-02-19 17:19:28Z novalis $)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


function smarty_outputfilter_note($tpl_output, &$smarty)
{
	/*
    The following copyright announcement is in compliance
    to section 2c of the GNU General Public License, and
    thus can not be removed, or can only be modified
    appropriately.
    */
	$footer  = gm_get_content('GM_FOOTER', $_SESSION['languages_id']) ? : gm_get_conf('GM_FOOTER');
	$gm_part = '<span class="strong">' . $footer . '</span>';
	$cop     = '';

	$coo_template_control =& MainFactory::create_object('TemplateControl', array(), true);
	if($coo_template_control->get_template_presentation_version() < FIRST_GX2_TEMPLATE_VERSION)
	{
		$cop = '<div class="copyright">
					'.$gm_part.'<br />
					<span class="copyright" style="font-size:7pt;">eCommerce Engine &copy; 2006 <a class="copyright" target="_blank" href="http://www.xt-commerce.com" style="font-size:7pt;">xt:Commerce Shopsoftware</a></span>
				</div>';
	}

    return $tpl_output.$cop;
}
?>