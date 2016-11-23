<?php
/* --------------------------------------------------------------
   credits.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
   
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
   (c) 2003	 nextcommerce ( start.php,v 1.6 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: credits.php 1263 2005-09-30 10:14:08Z mz $)


   Released under the GNU General Public License
   --------------------------------------------------------------*/


  require('includes/application_top.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
			.credits .main strong a,
			.credits .main strong a:visited,
			.credits .main strong a:hover,
			.credits .main strong a:active
			{
				font-weight: bold;
				font-size: 12px;
				font-family: Verdana,Arial,sans-serif;
				text-decoration: underline;
			}

			.credits .main a,
			.credits .main a:visited, 
			.credits .main a:hover,
			.credits .main a:active
			{
				font-size: 12px;
				font-family: Verdana,Arial,sans-serif;
				text-decoration: underline;
			}

		</style>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">

						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->

					</table>
				</td>

				<!-- body_text //-->

				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="credits">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<div class="pageHeading">
												Credits
											</div>
										</td>
										<td width="80" rowspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td class="main" valign="top">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="main">

								<span style="color: #0264bb;"><h2 class="section-header">Gambio GmbH &copy; 2005 - 2016</h2></span><br />
								<?php
									include_once('../release_info.php');
									echo $gx_version;
								?><br />
								<br />
								<strong>=====================================<br />
								Please visit our website: <a href="https://www.gambio.de" target="_blank">www.gambio.de</a><br />
									=====================================</strong><br />
								<br />
								Gambio GmbH provides no warranty. The Shopsoftware is redistributable under the GNU General Public License (Version 2)<br />
								[<a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">http://www.gnu.org/licenses/gpl-2.0.html</a>]<br />
								<br />
								This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License (Version 2) for more details.<br />
								<br />
								You should have received a copy of the GNU General Public License (Version 2) along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.<br />
								<br />
								<br />
								<br />
								<br />
								<strong>Die Shopsoftware basiert auf:</strong><br />
								<hr />
								<br />
								<strong>&copy; 2000-2001 The Exchange Project</strong> &copy; Harald Ponce de Leon | <a href="http://www.oscommerce.com" target="_blank">http://www.oscommerce.com</a><br />
								<br />
								<strong>&copy; 2002-2003 osCommerce (Milestone2)</strong> &copy; Harald Ponce de Leon | <a href="http://www.oscommerce.com" target="_blank">http://www.oscommerce.com</a><br />
								Released under the GNU General Public License - die exakten Versionsnummern der Originalfiles entnehmen Sie den Copyright-Headern der einzelnen Dateien<br />
								<br />
								<strong>&copy; neXTCommerce (XTC 0.9 RC3 CVS)</strong> &copy; 2003 neXTCommerce | <a href="http://www.nextcommerce.org" target="_blank">http://www.nextcommerce.org</a> ( code-modifications & redesign by Guido Winger/Mario Zanier/Andreas Oberzier)<br />
								Mario Zanier <a href="mailto:mzanier@xtcommerce.com" target="_blank">mzanier@xtcommerce.com</a> / Guido Winger <a href="mailto:gwinger@xtcommerce.com" target="_blank">gwinger@xtcommerce.com</a> / Andreas Oberzier <a href="mailto:aoberzier@nextcommerce.org" target="_blank">aoberzier@nextcommerce.org</a><br />
								Released under the GNU General Public License - die exakten Versionsnummern der Originaldateien entnehmen Sie den Copyright-Headern der einzelnen Dateien<br />
								<br />
								<strong>xt:Commerce v3.0.4 SP2.1 (Release Datum: 17 Aug 2006)</strong><br />
								Programmierer von XT-Commerce:<br />
								Mario Zanier (<a href="mailto:mzanier@xtcommerce.com" target="_blank">mzanier@xtcommerce.com</a>)<br />
								Guido Winger (<a href="mailto:gwinger@xtcommerce.com" target="_blank">gwinger@xtcommerce.com</a>)<br />
								Released under the GNU General Public License<br />
								This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA. See <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">http://www.gnu.org/copyleft/gpl.html</a> for details.

							</td>
						</tr>
					</table>
				</td>

				<!-- body_text_eof //-->
				
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>