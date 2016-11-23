<?php
/* --------------------------------------------------------------
   main_bottom_footer.php 2015-10-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once DIR_FS_CATALOG . 'release_info.php';

$languageTextManager = MainFactory::create('LanguageTextManager', 'shop_key', $_SESSION['languages_id']);

?>

<div class="gx-container">
	<div class="footer-info grid text-left">

		<!-- Menu toggle -->
		<div class="pull-left info">
			<?php include DIR_FS_ADMIN . 'html/compatibility/collapse_left_menu.php'; ?>
		</div>

		<!-- Version Info -->
		<div class="pull-left info">
			<span class="version-info">Gambio Version: <?php echo $gx_version; ?></span>
			<a class="shop-key-link" href="<?php echo DIR_WS_ADMIN . 'admin.php?do=ShopKey'; ?>">
				<span class="shop-key-information">
					<i class="fa fa-exclamation-triangle fa-lg shop-key-invalid<?php echo (gm_get_conf('SHOP_KEY_VALID') === '1') ? ' hidden' : '' ?>" title="<?php echo $languageTextManager->get_text('shop_key_invalid') ?>"></i>
					<i class="fa fa-check fa-lg shop-key-valid<?php echo (gm_get_conf('SHOP_KEY_VALID') === '1') ? '' : ' hidden' ?>" title="<?php echo $languageTextManager->get_text('shop_key_valid') ?>"></i>
				</span>
			</a>
		</div>

		<!-- Select Language -->
		<div class="pull-left info">
			<div class="current-language">
				<ul class="pull-left">
					<?php
						$availableLanguages = xtc_get_languages();
						foreach($availableLanguages as $language) {
							$active = ($language['code'] == $_SESSION['language_code']) ? 'active' : '';
							$url = xtc_href_link(basename($_SERVER['SCRIPT_NAME']), 'language='.$language['code']) . "&" . xtc_get_all_get_params(array('language'));
							echo '
								<li class="pull-left ' . $active . '">
									<a href="' . str_replace('"', '', $url) . '">
										<img src="../lang/' . $language['directory'] . '/icon.gif" />
									</a>
								</li>';
						}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
