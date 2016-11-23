<?php
/* --------------------------------------------------------------
   language.php 2015-02-19 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(language.php,v 1.4 2003/02/11); www.oscommerce.com 
   (c) 2003	 nextcommerce (language.php,v 1.5 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: language.php 950 2005-05-14 16:45:21Z mz $)

   browser language detection logic 
   Copyright phpMyAdmin (select_lang.lib.php3 v1.24 04/19/2002)
   Copyright Stephane Garin <sgarin@sgarin.com> (detect_language.php v0.1 04/02/2002)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  class language_ORIGIN {
    var $languages, $catalog_languages, $browser_languages, $language;

    function __construct($lng = '') {
      $this->languages = array('ar' => array('ar([-_][[:alpha:]]{2})?|arabic', 'arabic', 'ar'),
                               'bg-win1251' => array('bg|bulgarian', 'bulgarian-win1251', 'bg'),
                               'bg-koi8r' => array('bg|bulgarian', 'bulgarian-koi8', 'bg'),
                               'ca' => array('ca|catalan', 'catala', 'ca'),
                               'cs-iso' => array('cs|czech', 'czech-iso', 'cs'),
                               'cs-win1250' => array('cs|czech', 'czech-win1250', 'cs'),
                               'da' => array('da|danish', 'danish', 'da'),
                               'de' => array('de([-_][[:alpha:]]{2})?|german', 'german', 'de'),
                               'el' => array('el|greek',  'greek', 'el'),
                               'en' => array('en([-_][[:alpha:]]{2})?|english', 'english', 'en'),
                               'es' => array('es([-_][[:alpha:]]{2})?|spanish', 'spanish', 'es'),
                               'et' => array('et|estonian', 'estonian', 'et'),
                               'fi' => array('fi|finnish', 'finnish', 'fi'),
                               'fr' => array('fr([-_][[:alpha:]]{2})?|french', 'french', 'fr'),
                               'gl' => array('gl|galician', 'galician', 'gl'),
                               'he' => array('he|hebrew', 'hebrew', 'he'),
                               'hu' => array('hu|hungarian', 'hungarian', 'hu'),
                               'id' => array('id|indonesian', 'indonesian', 'id'),
                               'it' => array('it|italian', 'italian', 'it'),
                               'ja-euc' => array('ja|japanese', 'japanese-euc', 'ja'),
                               'ja-sjis' => array('ja|japanese', 'japanese-sjis', 'ja'),
                               'ko' => array('ko|korean', 'korean', 'ko'),
                               'ka' => array('ka|georgian', 'georgian', 'ka'),
                               'lt' => array('lt|lithuanian', 'lithuanian', 'lt'),
                               'lv' => array('lv|latvian', 'latvian', 'lv'),
                               'nl' => array('nl([-_][[:alpha:]]{2})?|dutch', 'dutch', 'nl'),
                               'no' => array('no|norwegian', 'norwegian', 'no'),
                               'pl' => array('pl|polish', 'polish', 'pl'),
                               'pt-br' => array('pt[-_]br|brazilian portuguese', 'brazilian_portuguese', 'pt-BR'),
                               'pt' => array('pt([-_][[:alpha:]]{2})?|portuguese', 'portuguese', 'pt'),
                               'ro' => array('ro|romanian', 'romanian', 'ro'),
                               'ru-koi8r' => array('ru|russian', 'russian-koi8', 'ru'),
                               'ru-win1251' => array('ru|russian', 'russian-win1251', 'ru'),
                               'sk' => array('sk|slovak', 'slovak-iso', 'sk'),
                               'sk-win1250' => array('sk|slovak', 'slovak-win1250', 'sk'),
                               'sr-win1250' => array('sr|serbian', 'serbian-win1250', 'sr'),
                               'sv' => array('sv|swedish', 'swedish', 'sv'),
                               'th' => array('th|thai', 'thai', 'th'),
                               'tr' => array('tr|turkish', 'turkish', 'tr'),
                               'uk-win1251' => array('uk|ukrainian', 'ukrainian-win1251', 'uk'),
                               'zh-tw' => array('zh[-_]tw|chinese traditional', 'chinese_big5', 'zh-TW'),
                               'zh' => array('zh|chinese simplified', 'chinese_gb', 'zh'));


      $this->catalog_languages = array();
      // BOF GM_MOD:
      $languages_query = xtc_db_query("select languages_id, name, code, image, directory, language_charset, status from " . TABLE_LANGUAGES . " order by sort_order");
      while ($languages = xtc_db_fetch_array($languages_query)) {
        // BOF GM_MOD:
      	$this->catalog_languages[$languages['code']] = array(
															'id' => $languages['languages_id'],
															'name' => $languages['name'],
															'image' => $languages['image'],
															'directory' => $languages['directory'],
															'language_charset' => $languages['language_charset'],
															'code' => $languages['code'],
															'status' => (int) $languages['status']
														);
      }

      $this->browser_languages = '';
      $this->language = '';

      if ( (!empty($lng)) && (isset($this->catalog_languages[$lng])) ) {
        $this->language = $this->catalog_languages[$lng];
      } else {
        $this->language = $this->catalog_languages[DEFAULT_LANGUAGE];
      }
    }

    function get_browser_language() {
      $this->browser_languages = explode(',', getenv('HTTP_ACCEPT_LANGUAGE'));

      for ($i=0, $n=sizeof($this->browser_languages); $i<$n; $i++) {
        reset($this->languages);
        while (list($key, $value) = each($this->languages)) {
          if (preg_match('/^(' . $value[0] . ')(;q=[0-9]\\.[0-9])?$/i', $this->browser_languages[$i]) 
              && isset($this->catalog_languages[$key]) 
              && $this->catalog_languages[$key]['status'] == 1) {
            $this->language = $this->catalog_languages[$key];
            break 2;
          }
        }
      }
    }
  }

MainFactory::load_origin_class('language');
