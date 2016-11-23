<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>

<form class="yoochoose_prefs" name="yoochoose_prefs" method="POST" action="yoochoose.php?page=models">
<div style="padding: 20px 15px; width: 760px;">

<?php

    require_once(DIR_FS_ADMIN.'includes/modules/yoochoose/utils.php');

    $post = isset($_POST['YOOCHOOSE_HOMEPAGE_TOPSELLERS_MAX_DISPLAY']);

    $languages = xtc_get_languages();

    $strategies = array();

	// YOOCHOOSE_STRATEGY_ HOMEPAGE_PERSONALIZED
	// YOOCHOOSE_STRATEGY_ HOMEPAGE_TOPSELLERS
	// YOOCHOOSE_STRATEGY_ CATEGORY_TOPSELLERS
	// YOOCHOOSE_STRATEGY_ PRODUCT_ALSO_PURCHASED  <--- overwriting
	// YOOCHOOSE_STRATEGY_ PRODUCT_ALSO_INTERESTING
	// YOOCHOOSE_STRATEGY_ SHOPPING_CART

    class RecommendationBox {
    	public $id;
    	public $headers;
    	public $header_readonly = false;
    	public $max_display;
    	public $strategy;

    	public $template; // templtate file this box is used in
    	public $template_const;

    	function RecommendationBox($id, $template_const = null, $template = null) {
    		$this->id = $id;
    		$this->strategy = getYoochooseStrategy($id);
    		$this->template_const = $template_const;
    		$this->template = $template === null ? array() : (is_array($template) ? $template : array($template));
    	}

    	function load() {
    		global $languages;

    		$header_key = getYoochooseHeaderConstantName($this->id);

    		foreach ($languages as $lang) {
    			$i = $lang["id"];
    			if ('PRODUCT_ALSO_PURCHASED' == $this->id) {
    				$this->headers[$i] = $this->loadAlsoPurchasedHeader($lang);
            		$this->header_readonly = true;
    			} else {
		            if (isset($_POST[$header_key]) && isset($_POST[$header_key][$i])) {
		    	    	$this->headers[$i] = $_POST[$header_key][$i];
	            	} else {
						$from_db = gm_get_content($header_key, $i);
						$this->headers[$i] = $from_db ? $from_db : yoochooseDefaultHeader($this->id, $i);
	            	}
	            }
    		}

    		$max_display_key = getMaxDisplayConstantName($this->id);

    		if (isset($_POST[$max_display_key])) {
    			$this->max_display = $_POST[$max_display_key];
    		} else {
    			$this->max_display = getMaxDisplay($this->id);
    		}
    	}

    	function loadAlsoPurchasedHeader($lang)
        {
            $alsoPurchasedSection = MainFactory::create('LanguageTextManager', 'also_purchased', $_SESSION['languages_id']);
            return $alsoPurchasedSection->get_text('heading_text');
    	}

        /** Updates the table 'gm_contents', setting all the languages
	     *  specified in array. Creates records, if some language was not found.
	     *  Do not delete values, if some language was not specified in
	     *  array.
	     */
	    function update() {
	    	$name = getYoochooseHeaderConstantName($this->id);
	    	foreach ($this->headers as $langKey=>$value) {
	    		if (! gm_set_content($name, $value, $langKey)) {
	    			throw new Exception("Unable to set the content [$name] for language [$values].");
	    		}
	    	}
	    	$d = getMaxDisplayDefaultValue($this->id);
	    	$k = getMaxDisplayConstantName($this->id);
	    	updateProperty($k, $this->max_display, $d);
	    }
    }

    $p_templates = yooProductTemplates();
    $c_templates = yooListingTemplates();

	if(gm_get_env_info('TEMPLATE_VERSION') >= 3)
	{
		$p_templates = '/snippets/product_info/product_lists.html';
	}

    $homePers  = new RecommendationBox('HOMEPAGE_PERSONALIZED',    'MODULE_yoochoose_homepage_personalized', '/module/main_content.html');
	$homeTops  = new RecommendationBox('HOMEPAGE_TOPSELLERS',      'MODULE_yoochoose_homepage_topsellers',   '/module/main_content.html');
	$catTops   = new RecommendationBox('CATEGORY_TOPSELLERS',      'MODULE_yoochoose_category_topsellers',   $c_templates);
	$prodBuy   = new RecommendationBox('PRODUCT_ALSO_PURCHASED',   'MODULE_also_purchased',                  $p_templates);
	$prodClick = new RecommendationBox('PRODUCT_ALSO_INTERESTING', 'MODULE_yoochoose_also_interesting',      $p_templates);
	$shopCart  = new RecommendationBox('SHOPPING_CART',            'MODULE_yoochoose_shopping_cart',         '/module/shopping_cart.html');

	$boxes = array($homePers, $homeTops, $catTops, $prodBuy, $prodClick, $shopCart);

    foreach ($boxes as $box) {
    	$box->load();
    	if ($post) {
    		$box->update();
    	}
    }

    try {
        $loaded = load_json_url_ex(getRegServerUrl()."/api/".YOOCHOOSE_ID."/configuration/strategies.json");

        foreach($loaded->strategies->strategy as $strategy) {
            $strategies[] = $strategy->referenceCode;
        }
    } catch (IOException $ex) {
        $text = sprintf(YOOCHOOSE_ERROR_LOADING_STRATEGIES, $ex->getMessage());
        printInfoDiv($text , "onebit_49.png", "error");
    }

    $not_found_tip = false;

    // validation

    foreach ($boxes as $box) {
    	if ($box->max_display == 0) {
    		continue;
    	}
    	foreach ($box->template as $template) {
	    	if ($box->template_const) {
	    		$tfile = DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.$template;
	    		$varn = '{$'.$box->template_const.'}';
		    	if (file_exists($tfile) && ! strpos(file_get_contents($tfile), $varn) !== false) {
		    		$text = sprintf(YOOCHOOSE_ERROR_TEMPLATE_NOT_PREPARED, CURRENT_TEMPLATE, $template, $varn);
		    		printInfoDiv($text , "onebit_47.png", "warning");
		    	}
	    	}
    	}
    	if ( ! in_array($box->strategy, $strategies)) {
    		$text = sprintf(YOOCHOOSE_ERROR_STRATEGY_NOT_FOUND, $box->strategy);
    		printInfoDiv($text , "onebit_49.png", "error");
    		$not_found_tip = true;
    	}
    }

    if ($not_found_tip) {
	    printInfoDiv(YOOCHOOSE_STRATEGY_NOT_FOUND_TIP , "onebit_20.png");
    }

?>

	<table class="superstrategies">
	<tr><td class="rblock"><div class="rblock"><?php echo sprintf(YOOCHOOSE_MODELS_LANDING_PAGE)?></div></td><td>

    <table cellspacing="0" cellpadding="0" class="strategies">
        <tr>
	        <td style="width: 10em; vertical-align:text-top;">
	            <div class="unimportant" style="height: 3em; margin: 0 5px 0 0;">
	            	<?php echo sprintf(YOOCHOOSE_MODELS_LOGIN)?>
		        </div>
	            <div class="unimportant" style="height: 9em; margin: 5px 5px 0 0;">
	            	<?php echo sprintf(YOOCHOOSE_MODELS_MENU)?>
		        </div>
		    </td><td style="vertical-align:text-top;">
		        <div class="unimportant" style=" height: 2em; margin: 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_SPECIAL_OFFER)?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0; ">
		        	<?php printRecommendationBox($homePers); ?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0;">
		        	<?php printRecommendationBox($homeTops); ?>
		        </div>
                <div class="unimportant" style=" height: 2em; margin: 5px 0 0 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_NEW_IN_SHOP)?>
		        </div>
		    </td>
        </tr>
    </table>

    </td></tr></table>

    <hr>

    <table class="superstrategies">
	<tr><td class="rblock"><div class="rblock"><?php echo sprintf(YOOCHOOSE_MODELS_CATEGORY_PAGE)?></div></td><td>

    <table cellspacing="0" cellpadding="0" class="strategies">
        <tr>
	        <td style="width: 10em; vertical-align:text-top;">
	            <div class="unimportant" style="height: 9em; margin: 0 5px 0 0;">
	            	<?php echo sprintf(YOOCHOOSE_MODELS_MENU)?>
		        </div>
		    </td><td style="vertical-align:text-top;">
		        <div class="unimportant" style="margin: 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_CATEGORY_TITLE)?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0; ">
		        	<?php printRecommendationBox($catTops); ?>
		        </div>
                <div class="unimportant" style=" height: 2em; margin: 5px 0 0 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_PRODUCTS)?>
		        </div>
		    </td>
        </tr>
    </table>

    </td></tr></table>

    <hr>

    <table class="superstrategies">
	<tr><td class="rblock"><div class="rblock"><?php echo sprintf(YOOCHOOSE_MODELS_SHOPPING_CART)?></div></td><td>

    <table cellspacing="0" cellpadding="0" class="strategies">
        <tr>
	        <td style="width: 10em; vertical-align:text-top;">
	            <div class="unimportant" style="height: 9em; margin: 0 5px 0 0;">
	            	<?php echo sprintf(YOOCHOOSE_MODELS_MENU)?>
		        </div>
		    </td><td style="vertical-align:text-top;">
                <div class="unimportant" style=" height: 2em; margin: 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_PRODUCTS)?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0; ">
		        	<?php printRecommendationBox($shopCart); ?>
		        </div>
		    </td>
        </tr>
    </table>

    </td></tr></table>

    <hr>

    <table class="superstrategies">
	<tr><td class="rblock"><div class="rblock"><?php echo sprintf(YOOCHOOSE_MODELS_PRODUCT)?></div></td><td>

    <table cellspacing="0" cellpadding="0" class="strategies">
        <tr>
	        <td style="width: 10em; vertical-align:text-top;">
	            <div class="unimportant" style="height: 9em; margin: 0 5px 0 0;">
		           <?php echo sprintf(YOOCHOOSE_MODELS_MENU)?>
		        </div>
		    </td><td style="vertical-align:text-top;">
		        <div class="unimportant" style=" height: 2em; margin: 0;">
		            <?php echo sprintf(YOOCHOOSE_MODELS_PRODUCT)?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0; ">
		            <?php printRecommendationBox($prodBuy); ?>
		        </div>
		        <div class="important" style="background-color: #D6E6F3; margin: 5px 0 0 0;">
		            <?php printRecommendationBox($prodClick); ?>
		        </div>
		    </td>
        </tr>
    </table>

    </td></tr></table>

    <hr>

    <table cellspacing="0" cellpadding="0" class="strategies">
        <tr>
 	        <td colspan="2" style="height: 2em; padding: 7px;">
	            <input type="submit" class="button" style="display:block; margin: 10px auto 0 auto;"
	                value="<?php echo sprintf(YOOCHOOSE_PREF_BTN)?>" name="btn"/>
	        </td>
        </tr>
    </table>

</div>


</form>

<?php

	/**
	 * @param $box
	 * 		valiable of type <code>RecommendationBox</code>
	 */
    function printRecommendationBox($box) {

    	global $languages;
    	global $strategies;

    	$found = in_array($box->strategy, $strategies);
    	$global_readonly = !$found;

    	$header_key = getYoochooseHeaderConstantName($box->id);

    	$size = 80;

        foreach ($box->headers as $landId => $text) {
        	$lang = yoochooseLang($landId);
    		$inputName = $header_key.'['.$landId.']';
    		$imgSrc = DIR_WS_CATALOG . 'lang/' . $lang['directory'].'/'.$lang['image'];
    		$hro = (! $global_readonly && ! $box->header_readonly)?'':'readonly="readonly"';
    		echo "<div class='values'>";
    	    echo "<input class='header' size='$size' name='$inputName' ".$hro." value=\"".htmlspecialchars($text)."\">"; // double quotes in value!
    	    echo "<img class='langicon' src='$imgSrc'>";
    	    echo "</div>";
    	}

    	$max = $box->max_display;
    	$max_key = getMaxDisplayConstantName($box->id);

    	echo "<div class='values'>".YOOCHOOSE_MAX_RECOMMENDATIONS."  ";
    	echo "<input size='2' name='$max_key' value='$max'>"; // always editable (use 0 to disable validation)
    	echo "</div>";

    	$style = 'strategy';

    	if (count($strategies) == 0) {
    		$style .= " not_init";
    	} else if ( ! $found) {
			$style .= " not_found";
    	}

        echo "<div class='$style' title=\"".htmlspecialchars(YOOCHOOSE_STRATEGY)."\">";
	  	echo htmlspecialchars($box->strategy);
	   	echo "</div>";
    }


    function printInputCheckbox($inputName, $checked, $readonly = false) {
         echo "<input name='$inputName' type='checkbox' class='checked'";
         echo $checked ? " checked='checked'" : "";
         echo $readonly ? " disabled='disabled'" : "";
         echo ">";
    }

?>



<?php


    /** Boxes can be enabled or disabled via GUI only, if the StyleEdit installed.
     *  If the StyleEdit is not installes, see the file "<your template>/template_settings.php" */
    function isStyleEditInstalled() {
        $activeBoxesEditable = is_dir(DIR_FS_CATALOG.'StyleEdit/');
        return $activeBoxesEditable;
    }


    /** Reads the box array from the template settings file.
     *
     *  Returns false, if the file not found or the
     *  specified box doesn't exists in the configuration.
     */
    function getBoxStaticSettings($boxName) {

    	$settingsfile = getTemplateSettingFile();

    	if (is_file($settingsfile)) {
    	   include($settingsfile);

    	   if (@isset($t_template_settings_array['MENUBOXES'][$boxName])) {
    	   	   return $t_template_settings_array['MENUBOXES'][$boxName];
    	   } else {
    	   	   return false;
    	   }
    	} else {
    		return false;
    	}
    }

    function getTemplateSettingFile() {
    	$setting_file = DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/template_settings.php';
    	return $setting_file;
    }

?>