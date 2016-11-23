<?php
/* --------------------------------------------------------------
  gm_lang_edit.php 2015-09-28 gm
  Gambio GmbG
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbG
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project
  (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
  (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

  Released under the GNU General Public License
  -------------------------------------------------------------- */

require('includes/application_top.php');

require_once DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
	</head>
	<body class="textedit">
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

		<link href="html/assets/styles/legacy/textedit.css" type="text/css" rel="stylesheet">

		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					</table>
				</td>
				<td class="boxCenter">

					<div class="pageHeading"><?php echo HEADING_TITLE; ?></div>

					<div class="breakpoint-large">
						<p class="main"><?php echo TITLE_INFO; ?></p>
						<h3 class="dataTableHeadingContent"><?php echo TITLE_KEYWORDS; ?></h3>

						<form action="request_port.php?module=AdminLangEdit" method="post" id="filterform"
							      data-gx-extension="ajax_search event_driven_submit"
							      data-ajax_search-target="#results"
							      data-ajax_search-template="#resultTpl"
									data-gx-widget="checkbox" class="gx-container">
							<fieldset>
								<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
								<input type="text" id="needle" name="needle" value="" size="50" required />
								<select id="action" name="action">
									<option value="search"><?php echo FILTER_GLOBAL; ?></option>
									<option value="searchValue"><?php echo FILTER_TEXT; ?></option>
									<option value="searchPhrase"><?php echo FILTER_PHRASE; ?></option>
									<option value="searchSection"><?php echo FILTER_SECTION; ?></option>
								</select>
								<?php
									$languages = gm_get_language();

									foreach($languages as $languageId => $language)
									{
										echo '
											<div class="filter">
												<input type="checkbox" name="languages[]" value="' . $languageId . '" checked data-single_checkbox /> ' . $language['name'] . '
											</div>';
									}
								?>
								<div class="filter">
									<input type="checkbox" name="only_edited" value="true" data-single_checkbox>
									<?php echo FILTER_ONLY_EDITED; ?>&nbsp;&nbsp;
								</div>
								<input type="submit" id="go_search" name="go_search" class="button" value="<?php echo TITLE_SEARCH; ?>" />
							</fieldset>
						</form>

						<div id="results" data-gx-extension="text_edit"
										     data-text_edit-url="request_port.php?module=AdminLangEdit"
										     data-text_edit-filter="filterform"></div>

						<div class="templates">
						<div id="resultTpl">
							<h3 class="dataTableHeadingContent"><?php echo TITLE_RESULTS; ?></h3>
							{{#data}}
							<div class="dataTableRow {{^editable}}locked{{/editable}}">
								<div class="result_phrase_name">
									<label>
										<?php echo FILTER_PHRASE; ?>:<span class="searchPhrase" data-text_edit-action="searchPhrase" data-text_edit-needle="{{name}}">{{name}}</span> ({{language}}, <?php echo FILTER_SECTION; ?>: <span class="searchSection" data-text_edit-action="searchSection" data-text_edit-needle="{{section}}" title="{{source}}">{{section}}</span>):
									</label>
									{{#editable}}
									<ul class="actions">
										<li class="reset{{^edited}} hidden{{/edited}}" data-text_edit-action="reset_content" data-text_edit-section="{{section}}" data-text_edit-phrase="{{name}}" data-text_edit-langid="{{langId}}"><?php echo BUTTON_RESET_ORIGINAL_PHRASE; ?></li>
										<li class="edit"><?php echo BUTTON_EDIT; ?></li>
										<li class="save hidden"><?php echo BUTTON_SAVE; ?></li>
										<li class="abort hidden"><?php echo BUTTON_CANCEL; ?></li>
									</ul>
									{{/editable}}
								</div>
								<textarea data-text_edit-action="save_content" data-text_edit-section="{{section}}" data-text_edit-phrase="{{name}}" data-text_edit-langid="{{langId}}" data-text_edit-source="{{source}}" data-text_edit-edited="{{edited}}" class="result_phrase_value" disabled>{{value}}</textarea>
							</div>
							{{/data}}
							{{^data}}
								<p><?php echo TEXT_NO_RESULT; ?></p>
							{{/data}}
						</div>

						<div id="modal_prompt">
							<div>
								{{#content}}
								<div class="icon">&nbsp;</div>
								<p>{{.}}</p>
								{{/content}}
								<form name="prompt" action="">
									<input type="text" name="input" value="{{value}}" autocomplete="off"/>
								</form>
							</div>
						</div>

						<div id="modal_alert">
							<div>
								{{#content}}
								<div class="icon">&nbsp;</div>
								<p>{{{.}}}</p>
								{{/content}}
							</div>
						</div>
					</div>
					</div>
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
