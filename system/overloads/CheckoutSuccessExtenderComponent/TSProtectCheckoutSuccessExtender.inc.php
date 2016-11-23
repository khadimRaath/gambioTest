<?php
/* --------------------------------------------------------------
   TSProtectCheckoutSuccessExtender.inc.php 2015-02-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * CheckoutSuccessExtender implementing Trusted Shops Buyer Protection Classic
 */
class TSProtectCheckoutSuccessExtender extends TSProtectCheckoutSuccessExtender_parent
{
	function proceed()
	{
		parent::proceed();

		$text = array(
			'de' => array(
				'title_seal' => 'Trusted Shops Gütesiegel - Bitte hier Gültigkeit prüfen!',
				'registration' => 'Als zusätzlichen Service bieten wir Ihnen den Trusted Shops Käuferschutz an. Wir übernehmen alle Kosten dieser <a href="http://www.trustedshops.de/guetesiegel/kaeuferschutz.html#fifth_aspect" target="_blank">Garantie</a>, Sie müssen sich lediglich anmelden.',
				'button' => 'Anmeldung zum Trusted Shops Käuferschutz',
				'application_saved' => 'Ihr Antrag auf Käuferschutz wurde registriert, die erhalten in Kürze eine Bestätigung per E-Mail.',
			),
			'en' => array(
				'title_seal' => 'Trusted Shops Seal of Approval - Click to verify.',
				'registration' => 'We offer you the Trusted Shops Buyer Protection as an additional service. We cover all costs for this <a href="http://www.trustedshops.co.uk/seal-of-approval/buyer-protection.html#fifth_aspect" target="_blank">guarantee</a>. All you have to do is register!',
				'button' => 'Register for Trusted Shops Buyer Protection',
				'application_saved' => 'Your application for Buyer Protection has been saved. You will soon receive confirmation via email.',
			),
			'es' => array(
				'title_seal' => 'Sello de calidad Trusted Shops - ¡Compruebe aquí la validez!',
				'registration' => 'Como servicio adicional le ofrecemos la protección del comprador Trusted Shops. Asumimos todos los costes de esta <a href="http://www.trustedshops.es/sello-de-calidad/proteccion-del-comprador.html#fifth_aspect" target="_blank">garantía</a>; sólo tiene que registrarse.',
				'button' => 'Registro para la protección del comprador Trusted',
				'application_saved' => '',
			),
			'fr' => array(
				'title_seal' => 'Label de qualité Trusted Shops - Cliquez pour le verifier.',
				'registration' => 'En tant que membre Trusted Shops, nous vous offrons un service complémentaire exceptionnel: la Protection AcheteurTrusted Shops. Les frais relatifs à cette <a href="http://www.trustedshops.fr/label-de-qualite/protection-acheteur.html#fifth_aspect" target="_blank">garantie</a> sont entièrement à notre charge, alors n\'hésitez pas, il vous suffit de vous inscrire.',
				'button' => 'Enregistrez-vous pour la garantie Protection Acheteur Trusted Shops...',
				'application_saved' => '',
			),
			'pl' => array(
				'title_seal' => 'Znak Jako ci Trusted Shops ? tu mo esz sprawdzi wa no !',
				'registration' => 'Jako dodatkowy serwis oferujemy Ochron Kupuj cego Trusted Shops. Przejmujemy pe?ny koszt tej <a href="http://www.trustedshops.pl/znak-jakosci/ochrona-kupujacego.html#fifth_aspect" target="_blank">gwarancji</a>, wystarczy si tylko zarejestrowa , aby z niej skorzysta .',
				'button' => 'Zg?oszenie do Ochrony Kupuj cego Trusted Shops',
				'application_saved' => '',
			),
		);

		if(!in_array($_SESSION['language_code'], array_keys($text))) {
			// language not supported by Trusted Shops
			$this->v_output_buffer = '';
			return;
		}

		$service = new GMTSService();

		/*
		 * Buyer Protection EXCELLENCE
		 */
		$tsid = $service->findExcellenceID($_SESSION['language_code']);
		if($tsid !== false) {
			$this->v_output_buffer = '';
			if(isset($_SESSION['ts_excellence'])) {
				$order = $this->v_data_array['coo_order'];
				$currency = $order->info['currency'];
				$paymentType = $service->getPaymentMapping($order->info['payment_method']);
				$buyerEmail = $order->customer['email_address'];
				$shopCustomerID = empty($this->v_data_array['coo_order']->customer['csID']) ? $this->v_data_array['coo_order']->customer['id'] : $this->v_data_array['coo_order']->customer['csID'];
				$orderDate = date('c');
				$amount = $_SESSION['ts_excellence']['cart_total'];
				$tsproductid = $_SESSION['ts_excellence']['tsproductid'];
				$orders_id = $this->v_data_array['orders_id'];
				// request protection
				$application_number = $service->requestForProtection($tsid, $tsproductid, $amount, $currency, $paymentType, $buyerEmail, $shopCustomerID, $orderDate, $orders_id);
				if($application_number > 0) {
					// application for protection accepted
					unset($_SESSION['ts_excellence']);
				}
				$trusted_block = '<div class="ts_bpclassic" style="overflow:auto;margin:auto;max-width:50em;">
								<div class="seal" style="float:left;margin-right:20px;">
									<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php" target="_blank">
									<input type="image" border="0" src="images/trusted_siegel.gif" title="'.$text[$_SESSION['language_code']]['title_seal'].'">
									<input name="shop_id" type="hidden" value="'.$tsid.'">
									</form>
								</div>
								<div class="ts_protection" style="">
									'.$text[$_SESSION['language_code']]['application_saved'].'
								</div>
								</div>';
			}
		}
		else {
			/*
			* Buyer Protection CLASSIC
			*/

			$trusted_block = '<!-- TS Buyer Protection Classic via Trust Badge -->';
			// cf. TrustBadgeCheckoutSuccessExtender

			/*
			$tsid = $service->findClassicID($_SESSION['language_code']);

			if($tsid !== false && isset($this->v_data_array['orders_id'])
					&& !empty($this->v_data_array['orders_id'])
					&& isset($this->v_data_array['coo_order'])
					&& is_object($this->v_data_array['coo_order']) ) {
				$trusted_amount = round($this->v_data_array['coo_order']->info['pp_total'], 2);
				$paymentType = $service->getPaymentMapping($this->v_data_array['coo_order']->info['payment_method']);
				$customers_id = empty($this->v_data_array['coo_order']->customer['csID']) ? $this->v_data_array['coo_order']->customer['id'] : $this->v_data_array['coo_order']->customer['csID'];
				$trusted_block = '<div class="ts_bpclassic" style="overflow:auto;margin:auto;max-width:50em;">
								<div class="seal" style="float:left;margin-right:20px;">
									<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php" target="_blank">
									<input type="image" border="0" src="images/trusted_siegel.gif" title="'.$text[$_SESSION['language_code']]['title_seal'].'">
									<input name="shop_id" type="hidden" value="'.$tsid.'">
									</form>
								</div>
								<div class="ts_protection" style="">
									<form id="formTShops" name="formTShops" method="post" action="https://www.trustedshops.com/shop/protection.php" target="_blank">
									<input name="_charset_" type="hidden">
									<input name="shop_id" type="hidden" value="'.$tsid.'">
									<input name="email" type="hidden" value="'.$this->v_data_array['coo_order']->customer['email_address'].'">
									<input name="first_name" type="hidden" value="'.$this->v_data_array['coo_order']->customer['firstname'].'">
									<input name="last_name" type="hidden" value="'.$this->v_data_array['coo_order']->customer['lastname'].'">
									<input name="street" type="hidden" value="'.$this->v_data_array['coo_order']->customer['street_address'].'">
									<input name="zip" type="hidden" value="'.$this->v_data_array['coo_order']->customer['postcode'].'">
									<input name="city" type="hidden" value="'.$this->v_data_array['coo_order']->customer['city'].'">
									<input name="country" type="hidden" value="'.$this->v_data_array['coo_order']->customer['country'].'">
									<input name="phone" type="hidden" value="'.$this->v_data_array['coo_order']->customer['telephone'].'">
									<input name="amount" type="hidden" value="'.$trusted_amount	.'">
									<input name="paymentType" type="hidden" value="'.$paymentType.'">
									<input name="curr" type="hidden" value="'.$this->v_data_array['coo_order']->info['currency'].'">
									<input name="KDNR" type="hidden" value="'.$customers_id.'">
									<input name="ORDERNR" type="hidden" value="'.$this->v_data_array['orders_id'].'">

									<p>'.$text[$_SESSION['language_code']]['registration'].'</p>

									<input type="submit" id="btnProtect" name="btnProtect" value="'.$text[$_SESSION['language_code']]['button'].'" style="display:block;margin:auto;padding:1em;text-shadow:0 0 1px #fff;color:#000;">
									</form>
								</div>
								</div>';
			}
			else {
				$trusted_block = '<!-- TS Buyer Protection Classic unavailable -->';
			}
			*/
		}
		$this->v_output_buffer['TRUSTED_BLOCK'] = $trusted_block;
	}

}
