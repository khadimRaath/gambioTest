<?php
/* --------------------------------------------------------------
  GZipControl.inc.php 2013-11-12 tb@gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class GZipControl
{
	protected $v_exclude_array = array();

	public function __construct($p_exclude_array = array())
	{
		if(is_array($p_exclude_array))
		{
			$this->v_exclude_array = $p_exclude_array;
		}
	}

	public function gzip($p_filepath, $p_filename_without_extension, $p_source_pattern)
	{
		if(!function_exists('gzencode') || !class_exists('PharData'))
		{
			return array('status' => 'error', 'error_message' => 'no_gzip');
		}

		$c_filepath = (string)$p_filepath;
		if($c_filepath == '')
		{
			return array('status' => 'error', 'error_message' => 'no_filepath');
		}

		$c_filename_without_extension = trim((string)$p_filename_without_extension);
		if($c_filename_without_extension == '')
		{
			return array('status' => 'error', 'error_message' => 'no_filename');
		}

		$c_source_pattern = trim((string)$p_source_pattern);
		if($c_source_pattern == '')
		{
			return array('status' => 'error', 'error_message' => 'no_source_pattern');
		}

		$t_tar_filename = $c_filename_without_extension . '.tar';
		$t_gz_filename = $t_tar_filename . '.gz';

		///////////////////
		// create tar-file

		$t_handle = fopen($p_filepath . $t_tar_filename, 'w');
		if($t_handle == false)
		{
			return array('status' => 'error', 'error_message' => 'backup_dir_not_writeable');
		}

		$t_files = glob($c_source_pattern);

		if(is_array($t_files) && count($t_files) > 0)
		{
			foreach($t_files AS $t_file)
			{
				$this->add_tar_header($t_handle, $t_file, basename($t_file));
				$this->add_tar_content($t_handle, $t_file);
			}
		}
		else
		{
			return array('status' => 'error', 'error_message' => 'no_files_found');
		}

		$this->add_tar_footer($t_handle);
		fclose($t_handle);

		////////////////
		// gzip tar-file
		// open tar-file
		$t_handle = fopen($c_filepath . $t_tar_filename, 'rb');

		// create tar.gz file
		$t_gz_handle = fopen($c_filepath . $t_gz_filename, 'w');

		// split tar-file into small pieces to gzip them -> small pieces neccessary to avoid high memory usage while compressing
		$t_chunk_size = 4096; // bytes
		
		while(!feof($t_handle))
		{
			$t_content = fread($t_handle, $t_chunk_size);
			if(is_string($t_content) && strlen($t_content) > 0)
			{
				$t_gz_content = gzencode($t_content, 9);
				fwrite($t_gz_handle, $t_gz_content);
				unset($t_gz_content);
				unset($t_content);
			}
		}
		
		fclose($t_handle);
		fclose($t_gz_handle);
		unlink($c_filepath . $t_tar_filename);

		return array('status' => 'success', 'filename' => $t_gz_filename);
	}

	protected function add_tar_header($p_file_resource, $p_filepath, $p_archive_filepath)
	{
		$t_file_info_array = stat($p_filepath);
		$t_ouid = sprintf("%6s ", decoct($t_file_info_array[4]));
		$t_ogid = sprintf("%6s ", decoct($t_file_info_array[5]));
		$t_omode = sprintf("%6s ", decoct(fileperms($p_filepath)));
		$t_omtime = sprintf("%11s", decoct(filemtime($p_filepath)));
		
		if(@is_dir($p_filepath))
		{
			$t_type = "5";
			$t_osize = sprintf("%11s ", decoct(0));
		}
		else
		{
			$t_type = '';
			$t_osize = sprintf("%11s ", decoct(filesize($p_filepath)));
			clearstatcache();
		}
		
		$t_dmajor = '';
		$t_dminor = '';
		$t_gname = '';
		$t_linkname = '';
		$t_magic = '';
		$t_prefix = '';
		$t_uname = '';
		$t_version = '';
		$t_chunk_before_checksum = pack("a100a8a8a8a12A12", $p_archive_filepath, $t_omode, $t_ouid, $t_ogid, $t_osize, $t_omtime);
		$t_chunk_after_checksum = pack("a1a100a6a2a32a32a8a8a155a12", $t_type, $t_linkname, $t_magic, $t_version, $t_uname, $t_gname, $t_dmajor, $t_dminor, $t_prefix, '');

		$t_checksum = 0;
		
		for($i = 0; $i < 148; $i++)
		{
			$t_checksum += ord(substr($t_chunk_before_checksum, $i, 1));
		}
		
		for($i = 148; $i < 156; $i++)
		{
			$t_checksum += ord(' ');
		}
		
		for($i = 156, $j = 0; $i < 512; $i++, $j++)
		{
			$t_checksum += ord(substr($t_chunk_after_checksum, $j, 1));
		}

		fwrite($p_file_resource, $t_chunk_before_checksum, 148);
		$t_checksum = sprintf("%6s ", decoct($t_checksum));
		$t_bdchecksum = pack("a8", $t_checksum);
		fwrite($p_file_resource, $t_bdchecksum, 8);
		fwrite($p_file_resource, $t_chunk_after_checksum, 356);
		
		return true;
	}

	protected function add_tar_content($p_file_resource, $p_filepath)
	{
		if(@is_dir($p_filepath))
		{
			return;
		}
		else
		{
			$t_filesize = filesize($p_filepath);
			$t_padding = $t_filesize % 512 ? 512 - $t_filesize % 512 : 0;
			$t_handle = fopen($p_filepath, "rb");
			
			while(!feof($t_handle))
			{
				fwrite($p_file_resource, fread($t_handle, 1024 * 1024));
			}
			
			$t_pack_string = sprintf("a%d", $t_padding);
			fwrite($p_file_resource, pack($t_pack_string, ''));
		}
	}

	protected function add_tar_footer($p_file_resource)
	{
		fwrite($p_file_resource, pack('a1024', ''));
	}

	public function extract_gzip($p_filepath, $p_extract_to = false)
	{
		if(!class_exists('PharData'))
		{
			return array('status' => 'error', 'error_message' => 'no_gzip');
		}

		$c_filepath = (string)$p_filepath;
		
		if($p_extract_to == false)
		{
			$c_extract_to = dirname($c_filepath);
		}
		elseif(is_string($p_extract_to) && strlen($p_extract_to) > 0)
		{
			$c_extract_to = $p_extract_to;
			
			if(substr($p_extract_to, -1) == '/')
			{
				$c_extract_to = substr($p_extract_to, 0, -1);
			}
		}

		$t_tar_filename = basename(substr($c_filepath, 0, -3)); // cut .gz
		
		// decompress from gz
		$t_gz_handle = gzopen($c_filepath, "rb");
		$t_tar_handle = fopen($c_extract_to . '/' . $t_tar_filename, "w");
		
		while($t_string = gzread($t_gz_handle, 4096))
		{
			fwrite($t_tar_handle, $t_string, strlen($t_string));
		}
		
		gzclose($t_gz_handle);
		fclose($t_tar_handle);

		// unarchive from tar
		$coo_phar_data = new PharData($c_extract_to . '/' . $t_tar_filename);
		$coo_phar_data->extractTo($c_extract_to, null, true); // BUG: empty folders cannot be extracted
		unset($coo_phar_data);
		unlink($c_extract_to . '/' . $t_tar_filename);

		return array('status' => 'success');
	}
}