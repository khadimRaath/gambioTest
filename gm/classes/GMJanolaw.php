<?php
/* --------------------------------------------------------------
   GMJanolaw.php 2016-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMJanolaw_ORIGIN
{
	public $m_user_id = false;
	public $m_shop_id = false;
	public $m_cache_seconds = 7200; # 2hours

	protected $content_types_array = array();
	protected $mode_suffix_array = array();
	protected $formats_array = array();
	protected $languages_array = array();
	protected $default_language = 'de';
	protected $version_info = false;

	public function __construct()
	{
		$this->m_user_id = xtc_cleanName(MODULE_GAMBIO_JANOLAW_USER_ID);
		$this->m_shop_id = xtc_cleanName(MODULE_GAMBIO_JANOLAW_SHOP_ID);

		$this->languages_array = array('de');
		$this->default_language = $this->mapLanguage($_SESSION['language_code']);

		$this->mode_suffix_array[] = '';
		$this->mode_suffix_array[] = '_include';

		$this->formats_array[] = 'html';
		$this->formats_array[] = 'txt';
		//$this->formats_array[] = 'pdf';

		if($this->get_status() == true)
		{
			$this->version_info = $this->versionCheck();
			if($this->version_info['multilang'] === true)
			{
				$this->multilang = true;
				$this->languages_array[] = 'gb';
				$this->languages_array[] = 'fr';
			}
			if($this->version_info['version'] > 1)
			{
				$this->content_types_array['legaldetails'] = 'legaldetails'; // impressum
				$this->content_types_array['terms'] = 'terms'; // agb
				$this->content_types_array['revocation'] = 'revocation'; // widerrufsbelehrung
				$this->content_types_array['datasecurity'] = 'datasecurity'; // datenschutzerklärung
				if($this->version_info['version'] >= 3)
				{
					$this->content_types_array['model-withdrawal-form'] = 'model-withdrawal-form';
				}
			}
			else
			{
				$this->content_types_array['legaldetails'] = 'impressum';
				$this->content_types_array['terms'] = 'agb';
				$this->content_types_array['revocation'] = 'widerrufsbelehrung';
				$this->content_types_array['datasecurity'] = 'datenschutzerklärung';
			}

			# phantom call for creating checkout cache-file
			foreach($this->content_types_array as $content_type_key => $content_type)
			{
				foreach($this->languages_array as $language)
				{
					$this->get_page_content($content_type_key, false, true, '', $language);
					$this->get_pdf_file($content_type, $language);
				}
			}
		}
	}

	public function mapLanguage($language_iso_2)
	{
		if(in_array($language_iso_2, $this->languages_array))
		{
			$mappedLanguage = $language_iso_2;
		}
		elseif($this->multilang === true && $language_iso_2 === 'en')
		{
			$mappedLanguage = 'gb';
		}
		else
		{
			$mappedLanguage = 'de';
		}
		return $mappedLanguage;
	}

	public function get_status()
	{
    	if(defined('MODULE_GAMBIO_JANOLAW_STATUS') == false || MODULE_GAMBIO_JANOLAW_STATUS == 'False')
    	{
    		# module not found or not activated.
    		return false;
    	}
    	# module installed and active
		return true;
	}

    public function get_page_content($p_page_name, $p_include_mode=true, $p_html_format=true, $p_cache_filename='', $language = null)
    {
    	if($this->get_status() == false) {
    		return 'Das Janolaw-Modul ist nicht aktiviert.';
    	}

    	$language = $this->mapLanguage($language);
		$c_page_name = $this->content_types_array[xtc_cleanName($p_page_name)];

		if($p_include_mode === true && $p_html_format === true) {
			$t_include_mode_suffix = '_include';
		} else {
			$t_include_mode_suffix = '';
		}

		if($p_html_format === true) {
			$t_format_suffix = 'html';
		} else {
			$t_format_suffix = 'txt';
		}


		if($p_cache_filename != '')
		{
			$t_cache_file = DIR_FS_CATALOG . 'cache/'. xtc_cleanName($p_cache_filename) .'.'. $t_format_suffix;
		}
		else {
			# build page-specific source path for cache file
			$t_cache_file = DIR_FS_CATALOG . 'cache/'.
								$this->m_user_id .'-'.
								$this->m_shop_id .'-'.
								$language .'-'.
								$c_page_name.
								$t_include_mode_suffix.'.'.$t_format_suffix;
		}

		$t_create_cache = false;

		if(file_exists($t_cache_file) == false) {
			$t_create_cache = true;
		}
		elseif(filesize($t_cache_file) < 100) {
			$t_create_cache = true;
		}
		elseif(filemtime($t_cache_file) < time() - $this->m_cache_seconds) {
			$t_create_cache = true;
		}

		# load page and create cache
		if($t_create_cache)
		{
			$this->update_cache_file($t_cache_file, $c_page_name, $t_include_mode_suffix, $t_format_suffix, $language);
		}

		# use cache file for output
		$t_content = @file_get_contents($t_cache_file);

		if($p_html_format && !empty($t_content))
		{
			# fix URL for stylesheet to avoid mixed content
			$original_css = '<link href="http://www.janolaw.de/agb-service/shops/janolaw-test.css"';
			$fixed_css = '<link href="https://www.janolaw.de/agb-service/shops/janolaw-test.css"';
			$t_content = str_replace($original_css, $fixed_css, $t_content);
		}

		# display page content
		return $t_content;
    }

	# Janolaw server down -> update cache file dates to stop updating for next 2 hours
	public function touch_cache_files()
	{
		foreach($this->content_types_array AS $t_content_type)
		{
			foreach($this->languages_array as $language)
			{
				foreach($this->mode_suffix_array AS $t_mode_suffix)
				{
					foreach($this->formats_array AS $t_format)
					{
						$t_cache_file = DIR_FS_CATALOG . 'cache/'.
										$this->m_user_id .'-'.
										$this->m_shop_id .'-'.
										$language .'-'.
										$t_content_type.
										$t_mode_suffix.'.'.$t_format;

						if(file_exists($t_cache_file))
						{
							touch($t_cache_file);
						}
					}
				}
			}
		}
	}

	public function update_cache_file($p_cache_file, $p_page_name, $p_include_mode_suffix, $p_format_suffix, $language = null)
	{
		$c_page_name = xtc_cleanName($p_page_name);
		$language = $this->mapLanguage($language);

		if(strpos($p_cache_file, DIR_FS_CATALOG . 'cache/') !== 0
			|| strpos($p_cache_file, '..') !== false
			|| in_array($p_include_mode_suffix, $this->mode_suffix_array) === false
			|| in_array($p_format_suffix, $this->formats_array) === false)
		{
			return false;
		}

		# build source url for getting page content
		$language_part = $this->version_info['version'] > 1 ? $language .'/' : '';
		$t_source_url = 'http://www.janolaw.de/agb-service/shops/'.
							$this->m_user_id .'/'.
							$this->m_shop_id .'/'.
							$language_part.
							$c_page_name.
							$p_include_mode_suffix.'.'.$p_format_suffix;

		# load page from janolaw site
		$t_content = $this->loadResource($t_source_url);

		# looking for success
		if($t_content != false || strlen($t_content) > 100)
		{
			# write page content to cache file on success
			$fp = fopen($p_cache_file, 'w+');
			$t_content = utf8_encode_wrapper($t_content);
			fwrite($fp, $t_content);
			fclose($fp);
		}
		else
		{
			$this->touch_cache_files();
		}

		return true;
	}

	public function get_pdf_file($p_page_name, $language = null)
	{
		if($this->version_info !== false && $this->version_info['version'] < 3)
		{
			// PDF unsupported
			return false;
		}

		$c_page_name = xtc_cleanName($p_page_name);
		$language = $this->mapLanguage($language);

		$cache_file_name =  DIR_FS_CATALOG . 'media/content/'.
							$this->m_user_id .'-'.
							$this->m_shop_id .'-'.
							$language .'-'.
							$c_page_name.'.pdf';

		$t_create_cache = file_exists($cache_file_name) == false ||
		                  filesize($cache_file_name) < 100 ||
		                  filemtime($cache_file_name) < (time() - $this->m_cache_seconds);

		if($t_create_cache)
		{
			$t_source_url = 'http://www.janolaw.de/agb-service/shops/'.
								$this->m_user_id .'/'.
								$this->m_shop_id .'/'.
								$language .'/'.
								$c_page_name.'.pdf';

			# load page from janolaw site
			$t_content = $this->loadResource($t_source_url);

			# looking for success
			if($t_content != false || strlen($t_content) > 100)
			{
				# write pdf content to cache file on success
				file_put_contents($cache_file_name, $t_content);
			}
		}

		if(file_exists($cache_file_name))
		{
			return $cache_file_name;
		}
		else
		{
			return false;
		}
	}

	public function versionCheck()
	{
		$cache_file = DIR_FS_CATALOG.'cache/janolaw-versioninfo.pdc';

		$t_create_cache = file_exists($cache_file) == false ||
		                  filemtime($cache_file) < (time() - $this->m_cache_seconds);

		if($t_create_cache)
		{
			$version = 0;
			$version_resource[1] = 'http://www.janolaw.de/agb-service/shops/%s/%s/impressum_include.html';
			$version_resource[2] = 'http://www.janolaw.de/agb-service/shops/%s/%s/de/legaldetails_include.html';
			$version_resource[3] = 'http://www.janolaw.de/agb-service/shops/%s/%s/de/legaldetails.pdf';
			$multilang_resource = 'http://www.janolaw.de/agb-service/shops/%s/%s/gb/legaldetails_include.html';
			foreach($version_resource as $check_version => $url)
			{
				$url = sprintf($url, $this->m_user_id, $this->m_shop_id);
				if($this->loadResource($url) !== false)
				{
					$version = $check_version;
				}
			}
			$multilang_available = $this->loadResource(sprintf($multilang_resource, $this->m_user_id, $this->m_shop_id)) !== false;

			$version_info = array(
				'version' => $version,
				'multilang' => $multilang_available,
				'last_checked' => date('c'),
			);

			file_put_contents($cache_file, serialize($version_info));
		}

		$version_info = unserialize(file_get_contents($cache_file));

		return $version_info;
	}

	protected function loadResource($p_source_url)
	{
		$t_source_url = $p_source_url;
		$t_content = false;
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $t_source_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$t_content = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			if($info['http_code'] != '200')
			{
				$t_content = false;
			}
		}
		elseif(function_exists('file_get_contents'))
		{
			$t_content = @file_get_contents($t_source_url);
		}
		return $t_content;
	}

}
MainFactory::load_origin_class('GMJanolaw');