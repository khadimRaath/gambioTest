<?php
/* --------------------------------------------------------------
   MainAutoloader.inc.php 2016-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class MainAutoloader
{
	var $v_class_mapping_mode = NULL;
	var $v_frontend_classes_array = NULL;
	var $v_backend_classes_array = NULL;

	function MainAutoloader($p_mapping_mode)
	{
		$this->v_frontend_classes_array = array(
			'Smarty'			=> DIR_FS_CATALOG.'includes/classes/Smarty/SmartyBC.class.php',
			'AccountCheck'		=> DIR_FS_CATALOG.'includes/classes/banktransfer_validation.php',
			'breadcrumb'		=> DIR_FS_CATALOG.'includes/classes/breadcrumb.php',
			'cc_validation'		=> DIR_FS_CATALOG.'includes/classes/cc_validation.php',
			'PHPMailer'			=> DIR_FS_CATALOG.'vendor/phpmailer/phpmailer/class.phpmailer.php',
			'SMTP'				=> DIR_FS_CATALOG.'vendor/phpmailer/phpmailer/class.smtp.php',
			'main'				=> DIR_FS_CATALOG.'includes/classes/main.php',
			'messageStack'		=> DIR_FS_CATALOG.'includes/classes/message_stack.php',
			'order_total'		=> DIR_FS_CATALOG.'includes/classes/order_total.php',
			'paypal_checkout'	=> DIR_FS_CATALOG.'includes/classes/paypal_checkout.php',
			'shoppingCart'		=> DIR_FS_CATALOG.'includes/classes/shopping_cart.php',
			'wishList'			=> DIR_FS_CATALOG.'includes/classes/wish_list.php',
			'XMLParser'			=> DIR_FS_CATALOG.'includes/classes/xmlparserv4.php',
			'heidelpay'			=> DIR_FS_CATALOG.'includes/classes/class.heidelpay.php',
			'xtcPrice'			=> DIR_FS_CATALOG.'includes/classes/xtcPrice.php',
			'language'			=> DIR_FS_CATALOG.'includes/classes/language.php',
			'order'				=> DIR_FS_CATALOG.'includes/classes/order.php',
			'payment'			=> DIR_FS_CATALOG.'includes/classes/payment.php',
			'product'			=> DIR_FS_CATALOG.'includes/classes/product.php',
			'shipping'			=> DIR_FS_CATALOG.'includes/classes/shipping.php',
			'splitPageResults'	=> DIR_FS_CATALOG.'includes/classes/split_page_results.php',
			'vat_validation'	=> DIR_FS_CATALOG.'includes/classes/vat_validation.php',
			'InputFilter'		=> DIR_FS_CATALOG.'includes/classes/class.inputfilter.php',
			'httpClient'		=> DIR_FS_CATALOG.'includes/classes/http_client.php',
			'xtc_afterbuy_functions' => DIR_FS_CATALOG.'includes/classes/afterbuy.php'
		);

		$this->v_backend_classes_array = array(
				'paypal_admin'			=> DIR_FS_ADMIN.DIR_WS_CLASSES.'class.paypal.php',
				'image_manipulation'	=> DIR_FS_ADMIN.DIR_WS_CLASSES.'image_manipulator_GD2.php',
				'xtcImport'			    => DIR_FS_ADMIN.DIR_WS_CLASSES.'import.php',
				'xtcExport'			    => DIR_FS_ADMIN.DIR_WS_CLASSES.'import.php',
				'language'		    	=> DIR_FS_ADMIN.DIR_WS_CLASSES.'language.php',
				'Messages'			    => DIR_FS_ADMIN.DIR_WS_CLASSES.'messages.php',
				'messageStack'			=> DIR_FS_ADMIN.DIR_WS_CLASSES.'message_stack.php',
				'objectInfo'			=> DIR_FS_ADMIN.DIR_WS_CLASSES.'object_info.php',
				'paymentModuleInfo'		=> DIR_FS_ADMIN.DIR_WS_CLASSES.'payment_module_info.php',
				'PclZip'		    	=> DIR_FS_ADMIN.DIR_WS_CLASSES.'pclzip.lib.php',
				'shoppingCart'			=> DIR_FS_ADMIN.DIR_WS_CLASSES.'shopping_cart.php',
				'splitPageResults'		=> DIR_FS_ADMIN.DIR_WS_CLASSES.'split_page_results.php',
				'tableBlock'			=> DIR_FS_ADMIN.DIR_WS_CLASSES.'table_block.php'
		);

		$this->v_class_mapping_mode = $p_mapping_mode;
	}

	function load($p_class)
	{
		# set in switch/case
		$t_class_map_array = array();

		# get default class_map
		switch($this->v_class_mapping_mode)
		{
			case 'frontend':
				$t_class_map_array = $this->v_frontend_classes_array;
				break;

			case 'backend':
				$t_class_map_array = array_merge(
						$this->v_frontend_classes_array,
						$this->v_backend_classes_array
				);
                //print_r($t_class_map_array);
				break;

			default:
				trigger_error('unknown class_mapping_mode: '.$this->v_class_mapping_mode, E_USER_ERROR);
		}

		# look for overwriting user_class
		$t_user_class_file = 'GXUserComponents/'.$p_class.'.inc.php';

		if(file_exists($t_user_class_file))
		{
			# set/overwrite default class with user_class
			$t_class_map_array[$p_class] = $t_user_class_file;
		}

		# load class
		if(isset($t_class_map_array[$p_class]))
		{
			$t_mapped_class_path = $this->v_frontend_classes_array[$p_class];
			MainFactory::load_origin_class($p_class, $t_mapped_class_path);
		}
		else
		{
			# not found in class map, try system- and user-classes
			MainFactory::load_class($p_class);
		}
	}

}