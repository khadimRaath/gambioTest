<?php
/* --------------------------------------------------------------
  DownloadProcess.inc.php 2016-07-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(download.php,v 1.9 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (download.php,v 1.7 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: download.php 831 2005-03-13 10:16:09Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_random_name.inc.php');
require_once(DIR_FS_INC . 'xtc_unlink_temp_dir.inc.php');

// include needed classes
MainFactory::load_class('DataProcessing');

class DownloadProcess extends DataProcessing
{
	protected $order_id;
	protected $download_id;
	protected $customer_id;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['download_id']	= array('type' => 'int');
		$this->validation_rules_array['order_id']		= array('type' => 'int');
		$this->validation_rules_array['customer_id']	= array('type' => 'int');
	}
	
	public function proceed()
	{
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('order_id', 'download_id'));
		
		if(empty($t_uninitialized_array))
		{
			if(!isset($_SESSION['customer_id']))
			{
				echo 'You must be logged in!';
				return false;
			}

			// Check that order_id, customer_id and filename match
			$downloads_query = xtc_db_query("SELECT 
												date_format(o.date_purchased, '%Y-%m-%d') AS date_purchased_day, 
												opd.download_maxdays, 
												opd.download_count, 
												opd.download_maxdays, 
												opd.orders_products_filename,
												o.abandonment_download,
												UNIX_TIMESTAMP(o.date_purchased) as date_purchased_unix,
												UNIX_TIMESTAMP(now()) as time
											FROM
												" . TABLE_ORDERS . " o, 
												" . TABLE_ORDERS_PRODUCTS . " op, 
												" . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd 
											WHERE 
													o.customers_id = '" . $this->customer_id . "' 
												AND o.orders_id = '" . $this->order_id . "' 
												AND o.orders_id = op.orders_id 
												AND op.orders_products_id = opd.orders_products_id 
												AND opd.orders_products_download_id = '" . $this->download_id . "' 
												AND opd.orders_products_filename != ''"
											);
			if(!xtc_db_num_rows($downloads_query))
			{
				echo 'No download found!';
				return false;
			}
			
			$downloads = xtc_db_fetch_array($downloads_query);
			
			// MySQL 3.22 does not have INTERVAL
			list ($dt_year, $dt_month, $dt_day) = explode('-', $downloads['date_purchased_day']);
			$download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads['download_maxdays'], $dt_year);
			
			// backup locale setting
			$locale = setlocale(LC_ALL, 0);
			
			// change locale to multibyte character charset allowing characters like umlauts
			// en_US.UTF8 should always be available
			setlocale(LC_ALL, 'en_US.UTF8');
			
			$t_filename = basename($downloads['orders_products_filename']);
			
			// restore locale setting
			setlocale(LC_ALL, $locale);
			
			// Abort if time expired (maxdays = 0 means no time limit)
			if(($downloads['download_maxdays'] != 0) && ($download_timestamp <= $downloads['time']))
			{
				echo 'The requested download is no longer available!';
				return false;
			}
			
			// Abort if remaining count is <=0
			if($downloads['download_count'] <= 0)
			{
				echo 'The requested download is no longer available!';
				return false;
			}
			
			// Abort if file is not there
			if(!file_exists(DIR_FS_DOWNLOAD.$t_filename))
			{
				echo 'Requested file does\'t exists';
				return false;
			}
			
			if($downloads['abandonment_download'] == 1)
			{
				$t_download_abandonment_time = gm_get_conf('DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT');
			}
			else
			{
				$t_download_abandonment_time = gm_get_conf('DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT');
			}

			$t_time_until_download_allowed = ($downloads['date_purchased_unix'] + $t_download_abandonment_time) - $downloads['time'];

			if($t_download_abandonment_time > 0 && $t_time_until_download_allowed > 0) {
				/** @var $coo_download_delay DownloadDelay */
				$coo_download_delay = MainFactory::create_object('DownloadDelay');
				$coo_download_delay->convert_seconds_to_days($t_time_until_download_allowed);

				$t_days = $coo_download_delay->get_delay_days();
				$t_hours = $coo_download_delay->get_delay_hours();
				$t_minutes = $coo_download_delay->get_delay_minutes();
				$t_seconds = $coo_download_delay->get_delay_seconds();

				$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('withdrawal', $_SESSION['languages_id']) );
				/** @var $coo_text_time_left DownloadTimerStringOutput */
				$coo_text_time_left = MainFactory::create_object('DownloadTimerStringOutput', array(
					$t_days,
					$t_hours,
					$t_minutes,
					$t_seconds,
					$coo_text_mgr
				));

				$t_output = $coo_text_time_left->get_msg();

				$t_output = '
<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Download</title>
</head>
<body>
		<div style="font-family: Arial, Helvetia, Verdana, sans-serif; font-size: 16px; margin: 10px auto; text-align: center;">

		<p>$t_output</p>

		</div>

</body>
</html>';

				die($t_output);
			}

			$t_filepath = DIR_FS_DOWNLOAD . $t_filename;
			$t_filesize = filesize($t_filepath);

			// Now decrement counter
			xtc_db_query("UPDATE " . TABLE_ORDERS_PRODUCTS_DOWNLOAD." 
						SET 
							download_count = download_count-1 
						WHERE 
							orders_products_download_id = '" . $this->download_id . "'");

			// Now send the file with header() magic
			header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
			header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-Type: Application/octet-stream");
			header("Content-Length: " . $t_filesize);
			header("Content-disposition: attachment; filename=\"" . $t_filename . "\"");

			if(DOWNLOAD_BY_REDIRECT == 'true')
			{
				// This will work only on Unix/Linux hosts
				xtc_unlink_temp_dir(DIR_FS_DOWNLOAD_PUBLIC);
				$tempdir = xtc_random_name();
				umask(0000);
				mkdir(DIR_FS_DOWNLOAD_PUBLIC . $tempdir, 0777);
				symlink(DIR_FS_DOWNLOAD . $t_filename, DIR_FS_DOWNLOAD_PUBLIC . $tempdir . '/' . $t_filename);
				xtc_redirect(DIR_WS_DOWNLOAD_PUBLIC . $tempdir . '/' . $t_filename);
			} 
			else
			{
				$t_chunksize = 1 * (1024 * 1024); // how many bytes per chunk
				if($t_filesize > $t_chunksize) {
					$t_handle = fopen($t_filepath, 'rb');
					$t_buffer = '';
					while(!feof($t_handle))
					{
						$t_buffer = fread($t_handle, $t_chunksize);
						echo $t_buffer;
						ob_flush();
						flush();
					}
					fclose($t_handle);
				}
				else
				{
					// This will work on all systems, but will need considerable resources
					readfile($t_filepath);
				}
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return true;
	}
}