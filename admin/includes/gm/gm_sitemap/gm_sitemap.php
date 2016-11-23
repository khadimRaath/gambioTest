<?php
/* --------------------------------------------------------------
   gm_sitemap.php 2015-09-10 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
$languageTextManager = MainFactory::create('LanguageTextManager', 'buttons', $_SESSION['languages_id']);
?>

<div class="simple-container">
	<div class="span6">
		<label for="gm_generate"><?php echo TITLE_GENERATE; ?></label>
		<a href="#" id="gm_generate"
		data-gx-compatibility="sitemap/sitemap_generator"
		data-sitemap_generator-url="gm_sitemap_creator.php"
		data-sitemap_generator-params="<?php echo 'action=create_sitemap&page_token='. $_SESSION['coo_page_token']->generate_token()?>"
		class="button pull-right">
			<?php echo $languageTextManager->get_text('execute'); ?>
		</a>
	</div>
</div>
