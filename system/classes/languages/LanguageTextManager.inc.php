<?php
/* --------------------------------------------------------------
  LanguageTextManager.inc.php 2016-07-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Example:
 * $textManager = MainFactory::create_object('LanguageTextManager', array('index', $_SESSION['languages_id']), true);
 * echo 'test: '. $textManager->get_text('wishlist');
 *
 * Class LanguageTextManager
 */
class LanguageTextManager
{
	protected static $db;
	protected static $languagePhrasesCacheTable = 'language_phrases_cache';

	protected $shopWideDefaultLanguage   = null;
	protected $defaultLanguageId = 0;
	protected $defaultSection    = '';
	protected static $languages  = array();
	protected static $sectionMappings;
	protected $sectionsContent   = array();
	protected $useFallback       = true;


	/**
	 * @param string $p_defaultSection
	 * @param int    $p_defaultLanguageId
	 * @param bool   $p_useFallback
	 */
	public function __construct($p_defaultSection = '', $p_defaultLanguageId = 0, $p_useFallback = true)
	{
		$this->_initLanguages();
		$this->_initMappingArray();

		if((int)$p_defaultLanguageId > 0)
		{
			$this->defaultLanguageId = (int)$p_defaultLanguageId;
		}
		elseif(isset($_SESSION['languages_id']))
		{
			$this->defaultLanguageId = (int)$_SESSION['languages_id'];
		}

		$this->useFallback = (bool)$p_useFallback;
		$defaultSection    = (string)$p_defaultSection;

		if($defaultSection !== '')
		{
			$this->defaultSection    = $defaultSection;

			$cacheKey  = 'LanguageTextManager' . $this->defaultLanguageId . $defaultSection . (int)$this->useFallback;
			$dataCache = DataCache::get_instance();
			if($dataCache->key_exists($cacheKey, true))
			{
				#use cached data
				$this->sectionsContent = $dataCache->get_data($cacheKey);
			}
			else
			{
				#build new content
				$this->_initSection($this->defaultSection, $this->defaultLanguageId);
				$dataCache->set_data($cacheKey, $this->sectionsContent, true);
			}
		}
	}


	/**
	 * @param string $p_defaultSection
	 * @param int    $p_defaultLanguageId
	 * @param bool   $p_useFallback
	 *
	 * @return LanguageTextManager
	 */
	static function get_instance($p_defaultSection = '', $p_defaultLanguageId = 0, $p_useFallback = true)
	{
		/** @var LanguageTextManager $instance */
		static $instance;

		if($instance === null)
		{
			/** @var LanguageTextManager $instance */
			$instance = MainFactory::create_object('LanguageTextManager',
			                                       array($p_defaultSection, $p_defaultLanguageId, $p_useFallback));
		}
		else
		{
			$defaultLanguageId = (int)$p_defaultLanguageId;
			if($defaultLanguageId === 0 && isset($_SESSION['languages_id']))
			{
				$defaultLanguageId = (int)$_SESSION['languages_id'];
			}

			if($instance->defaultSection != $p_defaultSection || $instance->defaultLanguageId != $defaultLanguageId)
			{
				# re-init with new parameters
				$instance->defaultSection    = $p_defaultSection;
				$instance->defaultLanguageId = $defaultLanguageId;

				$instance->_initSection($p_defaultSection, $p_defaultLanguageId);
			}
		}

		return $instance;
	}


	/**
	 * @param string $p_phraseName
	 * @param string $p_section
	 * @param int    $p_languageId
	 *
	 * @return string
	 */
	public function get_text($p_phraseName, $p_section = '', $p_languageId = 0)
	{
		if($p_section === '')
		{
			$section = $this->defaultSection;
		}
		else
		{
			$section = $this->_getSectionName($p_section);
		}

		$languageId = (int)$p_languageId;
		if($languageId === 0)
		{
			$languageId = $this->defaultLanguageId;
		}

		# section content already available?
		if(isset($this->sectionsContent[$section]) == false)
		{
			# do init if not
			$this->_initSection($section, $languageId);
		}

		# get var value and return
		$phraseName = (string)$p_phraseName;
		$phraseText = $phraseName;
		if(isset($this->sectionsContent[$section][$phraseName]))
		{
			$phraseText = $this->sectionsContent[$section][$phraseName];
		}

		return $phraseText;
	}


	/**
	 * @param string $p_filePath
	 * @param int    $p_languageId
	 */
	public function init_from_lang_file($p_filePath, $p_languageId = 0)
	{
		if($p_languageId !== 0)
		{
			$c_languageId = (int)$p_languageId;
		}
		else
		{
			$c_languageId = $this->_getLanguageIdByFilePath($p_filePath);
			if($c_languageId === 0)
			{
				$c_languageId = $this->defaultLanguageId;
			}
		}

		$sectionName = $this->_getSectionName($p_filePath);

		$this->_initSection($sectionName, $c_languageId);
		$this->_initConstants($sectionName);
		$this->_initConstantsFromDeprecatedLangFile($p_filePath);
	}


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 *
	 * @return string
	 */
	public function get_section_array($p_section = '', $p_languageId = 0)
	{
		if($p_section === '')
		{
			$section = $this->defaultSection;
		}
		else
		{
			$section = $p_section;
		}

		$languageId = (int)$p_languageId;
		if($languageId === 0)
		{
			$languageId = $this->defaultLanguageId;
		}

		# section content already available?
		if(isset($this->sectionsContent[$section]) === false)
		{
			# do init if not
			$this->_initSection($section, $languageId);
		}

		$sectionArray = $this->sectionsContent[$section];

		return $sectionArray;
	}


	/**
	 * Returns the default language ID
	 *
	 * @return int The default language id
	 */
	public function getDefaultLanguageId()
	{
		if($this->shopWideDefaultLanguage === null)
		{
			$queryString = 'SELECT
									languages_id
								FROM
									' . TABLE_LANGUAGES . '
								WHERE
									code = "' . DEFAULT_LANGUAGE . '"';
			$query       = xtc_db_query($queryString);

			if(xtc_db_num_rows($query))
			{
				$result                        = xtc_db_fetch_array($query);
				$this->shopWideDefaultLanguage = (int)$result['languages_id'];
			}
		}

		return $this->shopWideDefaultLanguage;
	}


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 */
	protected function _initSection($p_section, $p_languageId = 0)
	{
		$section = $this->_getSectionName($p_section);

		$stopWatch = LogControl::get_instance()->get_stop_watch();
		$stopWatch->start('init_section');

		$this->_resetSection($section);

		// read fallback section phrases
		$languageId = ($p_languageId != 0) ? (int)$p_languageId : $this->defaultLanguageId;
		if($this->useFallback && isset(self::$languages[DEFAULT_LANGUAGE]['language_id'])
		   && $languageId !== self::$languages[DEFAULT_LANGUAGE]['language_id']
		)
		{
			// read section from fallback language
			$this->_readSectionFromDB($section, self::$languages[DEFAULT_LANGUAGE]['language_id']);
		}

		$this->_readSectionFromDB($section, $p_languageId);

		$stopWatch->stop('init_section');
	}


	/**
	 * @param string $p_section
	 * @param array  $p_sectionArray
	 */
	protected function _addSection($p_section, array $p_sectionArray)
	{
		$this->sectionsContent[$p_section] = array_merge($this->sectionsContent[$p_section], $p_sectionArray);
	}


	/**
	 * @param string $p_section
	 */
	protected function _resetSection($p_section)
	{
		$this->sectionsContent[$p_section] = array();
	}


	/**
	 * old lang file paths are mapped to a section name
	 */
	protected function _initMappingArray()
	{
		if(!empty(self::$sectionMappings))
		{
			return;
		}

		$mappingArray = array(
			'admin/banner_manager.php'                         => 'banner_manager',
			'admin/banner_statistics.php'                      => 'banner_statistics',
			'admin/blacklist.php'                              => 'blacklist',
			'admin/buttons.php'                                => 'admin_buttons',
			'admin/cache.php'                                  => 'cache',
			'admin/campaigns.php'                              => 'campaigns',
			'admin/categories.php'                             => 'categories',
			'admin/clear_cache.php'                            => 'clear_cache',
			'admin/configuration.php'                          => 'configuration',
			'admin/content_manager.php'                        => 'content_manager',
			'admin/countries.php'                              => 'admin_countries',
			'admin/coupon_admin.php'                           => 'coupon_admin',
			'admin/create_account.php'                         => 'admin_create_account',
			'admin/cross_sell_groups.php'                      => 'cross_sell_groups',
			'admin/csv_backend.php'                            => 'csv_backend',
			'admin/currencies.php'                             => 'currencies',
			'admin/customers.php'                              => 'admin_customers',
			'admin/customers_status.php'                       => 'customers_status',
			'admin/define_language.php'                        => 'define_language',
			'admin/ekomi.php'                                  => 'ekomi',
			'admin/geo_zones.php'                              => 'geo_zones',
			'admin/%s.php'                                     => 'admin_general',
			'admin/gm_analytics.php'                           => 'gm_analytics',
			'admin/gm_backup_files_zip.php'                    => 'gm_backup_files_zip',
			'admin/gm_bookmarks.php'                           => 'gm_bookmarks',
			'admin/gm_bookmarks_action.php'                    => 'gm_bookmarks_action',
			'admin/gm_callback_service.php'                    => 'admin_gm_callback_service',
			'admin/gm_counter.php'                             => 'gm_counter',
			'admin/gm_counter_action.php'                      => 'gm_counter_action',
			'admin/gm_emails.php'                              => 'gm_emails',
			'admin/gm_feature_control.php'                     => 'gm_feature_control',
			'admin/gm_%s.php'                                  => 'gm_general',
			'admin/gm_gmotion.php'                             => 'gm_gmotion',
			'admin/gm_gprint.php'                              => 'admin_gm_gprint',
			'admin/gm_id_starts.php'                           => 'gm_id_starts',
			'admin/gm_invoicing.php'                           => 'gm_invoicing',
			'admin/gm_lang_edit.php'                           => 'gm_lang_edit',
			'admin/gm_lightbox.php'                            => 'gm_lightbox',
			'admin/gm_logo.php'                                => 'gm_logo',
			'admin/gm_meta.php'                                => 'gm_meta',
			'admin/gm_miscellaneous.php'                       => 'gm_miscellaneous',
			'admin/gm_laws.php'                                => 'gm_laws',
			'admin/gm_module_export.php'                       => 'gm_module_export',
			'admin/gm_offline.php'                             => 'gm_offline',
			'admin/gm_opensearch.php'                          => 'admin_gm_opensearch',
			'admin/gm_order_menu.php'                          => 'gm_order_menu',
			'admin/gm_pdf.php'                                 => 'gm_pdf',
			'admin/gm_pdf_action.php'                          => 'gm_pdf_action',
			'admin/gm_pdf_order.php'                           => 'gm_pdf_order',
			'admin/gm_product_export.php'                      => 'gm_product_export',
			'admin/gm_product_images.php'                      => 'gm_product_images',
			'admin/gm_scroller.php'                            => 'gm_scroller',
			'admin/gm_security.php'                            => 'gm_security',
			'admin/gm_send_order.php'                          => 'gm_send_order',
			'admin/gm_seo_boost.php'                           => 'gm_seo_boost',
			'admin/gm_sitemap.php'                             => 'gm_sitemap',
			'admin/gm_sitemap_creator.php'                     => 'gm_sitemap_creator',
			'admin/gm_slider.php'                              => 'gm_slider',
			'admin/gm_sql.php'                                 => 'gm_sql',
			'admin/gm_style_edit.php'                          => 'gm_style_edit',
			'admin/gm_trusted_shop_id.php'                     => 'gm_trusted_shop_id',
			'admin/gv_mail.php'                                => 'gv_mail',
			'admin/gv_queue.php'                               => 'gv_queue',
			'admin/gv_sent.php'                                => 'gv_sent',
			'admin/index.php'                                  => 'admin_index',
			'admin/itransact.php'                              => 'itransact',
			'admin/languages.php'                              => 'languages',
			'admin/lettr_de.php'                               => 'lettr_de',
			'admin/mail.php'                                   => 'mail',
			'admin/manufacturers.php'                          => 'manufacturers',
			'admin/modules.php'                                => 'modules',
			'admin/module_export.php'                          => 'module_export',
			'admin/module_newsletter.php'                      => 'module_newsletter',
			'admin/new_attributes.php'                         => 'new_attributes',
			'admin/orders.php'                                 => 'orders',
			'admin/orders_edit.php'                            => 'orders_edit',
			'admin/orders_ipayment.php'                        => 'orders_ipayment',
			'admin/orders_status.php'                          => 'orders_status',
			'admin/paypal.php'                                 => 'paypal',
			'admin/products_attributes.php'                    => 'products_attributes',
			'admin/products_expected.php'                      => 'products_expected',
			'admin/products_vpe.php'                           => 'products_vpe',
			'admin/quantity_units.php'                         => 'quantity_units',
			'admin/reviews.php'                                => 'admin_reviews',
			'admin/robots_download.php'                        => 'robots_download',
			'admin/saferpay.php'                               => 'saferpay',
			'admin/server_info.php'                            => 'server_info',
			'admin/shipping_status.php'                        => 'shipping_status',
			'admin/show_logs.php'                              => 'show_logs',
			'admin/specials.php'                               => 'admin_specials',
			'admin/start.php'                                  => 'start',
			'admin/stats_campaigns.php'                        => 'stats_campaigns',
			'admin/stats_customers.php'                        => 'stats_customers',
			'admin/stats_products_purchased.php'               => 'stats_products_purchased',
			'admin/stats_products_viewed.php'                  => 'stats_products_viewed',
			'admin/stats_sales_report.php'                     => 'stats_sales_report',
			'admin/stats_stock_warning.php'                    => 'stats_stock_warning',
			'admin/tax_classes.php'                            => 'tax_classes',
			'admin/tax_rates.php'                              => 'tax_rates',
			'admin/template_configuration.php'                 => 'template_configuration',
			'admin/whos_online.php'                            => 'whos_online',
			'admin/yoochoose.php'                              => 'yoochoose',
			'admin/zones.php'                                  => 'zones',
			'customers.php'                                    => 'customers',
			'%s.php'                                           => 'general',
			'gm_account_delete.php'                            => 'gm_account_delete',
			'gm_callback_service.php'                          => 'gm_callback_service',
			'gm_gprint.php'                                    => 'gm_gprint',
			'gm_logger.php'                                    => 'gm_logger',
			'gm_price_offer.php'                               => 'gm_price_offer',
			'gm_shopping_cart.php'                             => 'gm_shopping_cart',
			'gm_tell_a_friend.php'                             => 'gm_tell_a_friend',
			'modules/order_total/ot_billsafe3.php'             => 'ot_billsafe3',
			'modules/order_total/ot_cod_fee.php'               => 'ot_cod_fee',
			'modules/order_total/ot_coupon.php'                => 'ot_coupon',
			'modules/order_total/ot_discount.php'              => 'ot_discount',
			'modules/order_total/ot_gambioultra.php'           => 'ot_gambioultra',
			'modules/order_total/ot_gm_tax_free.php'           => 'ot_gm_tax_free',
			'modules/order_total/ot_gv.php'                    => 'ot_gv',
			'modules/order_total/ot_klarna2_fee.php'           => 'ot_klarna2_fee',
			'modules/order_total/ot_loworderfee.php'           => 'ot_loworderfee',
			'modules/order_total/ot_payment.php'               => 'ot_payment',
			'modules/order_total/ot_ps_fee.php'                => 'ot_ps_fee',
			'modules/order_total/ot_shipping.php'              => 'ot_shipping',
			'modules/order_total/ot_sofort.php'                => 'ot_sofort',
			'modules/order_total/ot_subtotal.php'              => 'ot_subtotal',
			'modules/order_total/ot_subtotal_no_tax.php'       => 'ot_subtotal_no_tax',
			'modules/order_total/ot_tax.php'                   => 'ot_tax',
			'modules/order_total/ot_total.php'                 => 'ot_total',
			'modules/order_total/ot_total_netto.php'           => 'ot_total_netto',
			'modules/order_total/ot_tsexcellence.php'          => 'ot_tsexcellence',
			'modules/payment/amazon.php'                       => 'amazon',
			'modules/payment/amazonadvpay.php'                 => 'amazonadvpay',
			'modules/payment/banktransfer.php'                 => 'banktransfer',
			'modules/payment/billsafe_3_base.php'              => 'billsafe_3_base',
			'modules/payment/billsafe_3_installment.php'       => 'billsafe_3_installment',
			'modules/payment/billsafe_3_invoice.php'           => 'billsafe_3_invoice',
			'modules/payment/cash.php'                         => 'cash',
			'modules/payment/cod.php'                          => 'cod',
			'modules/payment/ebay.php'                         => 'ebay',
			'modules/payment/eustandardtransfer.php'           => 'eustandardtransfer',
			'modules/payment/hitmeister.php'                   => 'hitmeister',
			'modules/payment/hgwConf.php'                      => 'hgwConf',
			'modules/payment/hpbp.php'                         => 'hpbp',
			'modules/payment/hpbs.php'                         => 'hpbs',
			'modules/payment/hpcc.php'                         => 'hpcc',
			'modules/payment/hpdc.php'                         => 'hpdc',
			'modules/payment/hpdd.php'                         => 'hpdd',
			'modules/payment/hpeps.php'                        => 'hpeps',
			'modules/payment/hpgp.php'                         => 'hpgp',
			'modules/payment/hpidl.php'                        => 'hpidl',
			'modules/payment/hpinfo.php'                       => 'hpinfo',
			'modules/payment/hpiv.php'                         => 'hpiv',
			'modules/payment/hpmk.php'                         => 'hpmk',
			'modules/payment/hppay.php'                        => 'hppay',
			'modules/payment/hppf.php'                         => 'hppf',
			'modules/payment/hppp.php'                         => 'hppp',
			'modules/payment/hpppal.php'                       => 'hpppal',
			'modules/payment/hpsu.php'                         => 'hpsu',
			'modules/payment/invoice.php'                      => 'invoice',
			'modules/payment/ipayment.php'                     => 'payment_ipayment',
			'modules/payment/ipayment_cc.php'                  => 'ipayment_cc',
			'modules/payment/ipayment_elv.php'                 => 'ipayment_elv',
			'modules/payment/klarna2_invoice.php'              => 'klarna2_invoice',
			'modules/payment/klarna2_partpay.php'              => 'klarna2_partpay',
			'modules/payment/luupws.php'                       => 'luupws',
			'modules/payment/marketplace.php'                  => 'marketplace',
			'modules/payment/masterpayment_anzahlungskauf.php' => 'masterpayment_anzahlungskauf',
			'modules/payment/masterpayment_config.php'         => 'masterpayment_config',
			'modules/payment/masterpayment_credit_card.php'    => 'masterpayment_credit_card',
			'modules/payment/masterpayment_debit_card.php'     => 'masterpayment_debit_card',
			'modules/payment/masterpayment_elv.php'            => 'masterpayment_elv',
			'modules/payment/masterpayment_finanzierung.php'   => 'masterpayment_finanzierung',
			'modules/payment/masterpayment_phone.php'          => 'masterpayment_phone',
			'modules/payment/masterpayment_ratenzahlung.php'   => 'masterpayment_ratenzahlung',
			'modules/payment/masterpayment_rechnungskauf.php'  => 'masterpayment_rechnungskauf',
			'modules/payment/masterpayment_sofortbanking.php'  => 'masterpayment_sofortbanking',
			'modules/payment/meinpaket.php'                    => 'meinpaket',
			'modules/payment/moneyorder.php'                   => 'moneyorder',
			'modules/payment/paygate_ssl.php'                  => 'paygate_ssl',
			'modules/payment/payone_cc.php'                    => 'payone_cc',
			'modules/payment/payone_cod.php'                   => 'payone_cod',
			'modules/payment/payone_elv.php'                   => 'payone_elv',
			'modules/payment/payone_installment.php'           => 'payone_installment',
			'modules/payment/payone_safeinv.php'               => 'payone_safeinv',
			'modules/payment/payone_invoice.php'               => 'payone_invoice',
			'modules/payment/payone_otrans.php'                => 'payone_otrans',
			'modules/payment/payone_prepay.php'                => 'payone_prepay',
			'modules/payment/payone_wlt.php'                   => 'payone_wlt',
			'modules/payment/paypal3.php'                      => 'paypal3_module',
			'modules/payment/paypalgambio_alt.php'             => 'paypalgambio_alt',
			'modules/payment/paypalng.php'                     => 'payment_paypalng',
			'modules/payment/postfinanceag_amex.php'           => 'postfinanceag_amex',
			'modules/payment/postfinanceag_basic.php'          => 'postfinanceag_basic',
			'modules/payment/postfinanceag_creditcards.php'    => 'postfinanceag_creditcards',
			'modules/payment/postfinanceag_diners.php'         => 'postfinanceag_diners',
			'modules/payment/postfinanceag_mastercard.php'     => 'postfinanceag_mastercard',
			'modules/payment/postfinanceag_visa.php'           => 'postfinanceag_visa',
			'modules/payment/postfinance_epayment.php'         => 'postfinance_epayment',
			'modules/payment/rsmartsepa.php'                   => 'rsmartsepa',
			'modules/payment/saferpaygw.php'                   => 'saferpaygw',
			'modules/payment/sepa.php'                         => 'sepa',
			'modules/payment/skrill_cc.php'                    => 'skrill_cc',
			'modules/payment/skrill_cgb.php'                   => 'skrill_cgb',
			'modules/payment/skrill_csi.php'                   => 'skrill_csi',
			'modules/payment/skrill_elv.php'                   => 'skrill_elv',
			'modules/payment/skrill_giropay.php'               => 'skrill_giropay',
			'modules/payment/skrill_ideal.php'                 => 'skrill_ideal',
			'modules/payment/skrill_info.php'                  => 'skrill_info',
			'modules/payment/skrill_mae.php'                   => 'skrill_mae',
			'modules/payment/skrill_netpay.php'                => 'skrill_netpay',
			'modules/payment/skrill_payins.php'                => 'skrill_payins',
			'modules/payment/skrill_payinv.php'                => 'skrill_payinv',
			'modules/payment/skrill_psp.php'                   => 'skrill_psp',
			'modules/payment/skrill_pwy.php'                   => 'skrill_pwy',
			'modules/payment/skrill_sft.php'                   => 'skrill_sft',
			'modules/payment/skrill_wlt.php'                   => 'skrill_wlt',
			'modules/payment/sofort_general.php'               => 'sofort_general',
			'modules/payment/sofort_lastschrift.php'           => 'sofort_lastschrift',
			'modules/payment/sofort_sofortueberweisung.php'    => 'sofort_sofortueberweisung',
			'modules/payment/wcp.php'                          => 'wcp',
			'modules/payment/wcp_bmc.php'                      => 'wcp_bmc',
			'modules/payment/wcp_c2p.php'                      => 'wcp_c2p',
			'modules/payment/wcp_ccard.php'                    => 'wcp_ccard',
			'modules/payment/wcp_ccardmoto.php'                => 'wcp_ccardmoto',
			'modules/payment/wcp_ekonto.php'                   => 'wcp_ekonto',
			'modules/payment/wcp_elv.php'                      => 'wcp_elv',
			'modules/payment/wcp_eps.php'                      => 'wcp_eps',
			'modules/payment/wcp_giropay.php'                  => 'wcp_giropay',
			'modules/payment/wcp_idl.php'                      => 'wcp_idl',
			'modules/payment/wcp_installment.php'              => 'wcp_installment',
			'modules/payment/wcp_instantbank.php'              => 'wcp_instantbank',
			'modules/payment/wcp_invoice.php'                  => 'wcp_invoice',
			'modules/payment/wcp_maestro.php'                  => 'wcp_maestro',
			'modules/payment/wcp_moneta.php'                   => 'wcp_moneta',
			'modules/payment/wcp_mpass.php'                    => 'wcp_mpass',
			'modules/payment/wcp_p24.php'                      => 'wcp_p24',
			'modules/payment/wcp_paypal.php'                   => 'wcp_paypal',
			'modules/payment/wcp_pbx.php'                      => 'wcp_pbx',
			'modules/payment/wcp_poli.php'                     => 'wcp_poli',
			'modules/payment/wcp_psc.php'                      => 'wcp_psc',
			'modules/payment/wcp_quick.php'                    => 'wcp_quick',
			'modules/payment/wcp_select.php'                   => 'wcp_select',
			'modules/payment/wcp_skrilldirect.php'             => 'wcp_skrilldirect',
			'modules/payment/wcp_skrillwallet.php'             => 'wcp_skrillwallet',
			'modules/payment/wcp_sue.php'                      => 'wcp_sue',
			'modules/payment/worldpay.php'                     => 'worldpay',
			'modules/payment/yatego.php'                       => 'yatego',
			'modules/shipping/amazon.php'                      => 'shipping_amazon',
			'modules/shipping/ap.php'                          => 'ap',
			'modules/shipping/chp.php'                         => 'chp',
			'modules/shipping/chronopost.php'                  => 'chronopost',
			'modules/shipping/dhl.php'                         => 'dhl',
			'modules/shipping/dhlmeinpaket.php'                => 'dhlmeinpaket',
			'modules/shipping/dhl_meinpaket.php'               => 'dhl_meinpaket',
			'modules/shipping/dp.php'                          => 'dp',
			'modules/shipping/dpd.php'                         => 'dpd',
			'modules/shipping/ebay.php'                        => 'shipping_ebay',
			'modules/shipping/fedexeu.php'                     => 'fedexeu',
			'modules/shipping/flat.php'                        => 'flat',
			'modules/shipping/free.php'                        => 'free',
			'modules/shipping/freeamount.php'                  => 'freeamount',
			'modules/shipping/gambioultra.php'                 => 'shipping_gambioultra',
			'modules/shipping/hermesprops.php'                 => 'hermesprops',
			'modules/shipping/hitmeister.php'                  => 'shipping_hitmeister',
			'modules/shipping/interkurier.php'                 => 'interkurier',
			'modules/shipping/item.php'                        => 'item',
			'modules/shipping/marketplace.php'                 => 'shipping_marketplace',
			'modules/shipping/selfpickup.php'                  => 'selfpickup',
			'modules/shipping/table.php'                       => 'table',
			'modules/shipping/ups.php'                         => 'ups',
			'modules/shipping/upse.php'                        => 'upse',
			'modules/shipping/yatego.php'                      => 'shipping_yatego',
			'modules/shipping/zones.php'                       => 'shipping_zones',
			'modules/shipping/zonese.php'                      => 'zonese'
		);

		foreach(self::$languages as $languageArray)
		{
			foreach($mappingArray as $old => $new)
			{
				$old = 'lang/' . $languageArray['directory'] . '/' . sprintf($old, $languageArray['directory']);
				$new = sprintf($new, $languageArray['directory']);

				self::$sectionMappings[$old] = $new;
			}
		}
	}


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 */
	protected function _readSectionFromDB($p_section, $p_languageId)
	{
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder(); 
		
		$results = $db->select('phrase_name, phrase_text')
			->from(self::$languagePhrasesCacheTable)
			->where([
				'section_name' => $p_section, 
			    'language_id' => $p_languageId
			        ])
			->get()
			->result_array(); 
		
		$sectionArray = []; 
		
		foreach($results as $row)
		{
			$sectionArray[$row['phrase_name']] = $row['phrase_text']; 
		}
		
		if(count($results) > 0) 
		{
			$this->_addSection($p_section, $sectionArray);
		}
	}


	/**
	 * @param string $p_section
	 */
	protected function _initConstants($p_section)
	{
		foreach($this->sectionsContent[$p_section] as $phraseName => $phraseText)
		{
			if(defined($phraseName) == false)
			{
				define($phraseName, $phraseText);
			}
		}
	}


	/**
	 * @param $p_filePath
	 *
	 * @return int
	 */
	protected function _getLanguageIdByFilePath($p_filePath)
	{
		$languageId    = 0;
		$languageArray = $this->_getLanguageArrayByFilePath($p_filePath);
		if(!empty($languageArray) && isset($languageArray['language_id']))
		{
			$languageId = (int)$languageArray['language_id'];
		}

		return $languageId;
	}


	/**
	 * @param $p_filePath
	 *
	 * @return array
	 */
	protected function _getLanguageArrayByFilePath($p_filePath)
	{
		$languageArray = array();
		foreach(self::$languages as $language)
		{
			if(strpos($p_filePath, 'lang/' . $language['directory'] . '/') !== false)
			{
				$languageArray = $language;
				break;
			}
		}

		return $languageArray;
	}


	/**
	 * load languages from database
	 */
	protected function _initLanguages()
	{
		if(!empty(self::$languages))
		{
			return;
		}

		$query  = 'SELECT * FROM ' . TABLE_LANGUAGES;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			self::$languages[$row['code']]                = array();
			self::$languages[$row['code']]['language_id'] = (int)$row['languages_id'];
			self::$languages[$row['code']]['directory']   = $row['directory'];
		}
	}


	/**
	 * @param string $p_section
	 *
	 * @return string
	 */
	protected function _getSectionName($p_section)
	{
		$this->_initMappingArray();

		if(isset(self::$sectionMappings[$p_section]))
		{
			$p_section = self::$sectionMappings[$p_section];
		}

		return $p_section;
	}


	/**
	 * support for deprecated language files containing define() statements
	 *
	 * @param string $p_filePath
	 */
	protected function _initConstantsFromDeprecatedLangFile($p_filePath)
	{
		if(file_exists(DIR_FS_CATALOG . $p_filePath) && strpos($p_filePath, '..') === false)
		{
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
			include_once DIR_FS_CATALOG . $p_filePath;
		}
	}
}