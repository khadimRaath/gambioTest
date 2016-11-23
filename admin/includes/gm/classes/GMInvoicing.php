<?php
/* --------------------------------------------------------------
   GMInvoicing.php 2014-06-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
?><?php

	/*
	*	class to export orders
	*/
	class GMInvoicing_ORIGIN
	{
		/*
		*	@var string
		*/
		var $v_fopen_mode = 'w+';

		/*
		*	the export file name
		*	@var string
		*/
		var $v_export_filename;

		/*
		*	path to the export dir
		*	@var string
		*/
		var $v_export_dir;

		/*
		*	CSV separator
		*	@var string
		*/
		var $v_csv_separator;
		
		/*
		*	CSV text sign 
		*	@var string
		*/
		var $v_csv_text_sign;

		/*
		*	@var string
		*/
		var $v_export_file_pointer;

		/*
		*	date from
		*	@var String
		*/
		var $v_date_from;

		/*
		*	date to
		*	@var String
		*/
		var $v_date_to;

		/*
		*	order fields
		*	@var array()
		*/
		var $v_order_fields;

		/*
		*	order total fields
		*	@var array()
		*/
		var $v_order_total_fields;

		/*
		*	order status id
		*	@var int
		*/
		var $v_order_status_id;

		/*
		*	Constructor 
		*	@return void
		*/
		function __construct()
		{		
			return;
		}	

		/*
		*	function to set the date from
		*	@param string $p_date_from
		*	@return void
		*/
		function set_date_from($p_date_from)
		{	
			$t_date_from = trim($p_date_from);
			
			if(!empty($t_date_from))
			{
				$t_date_from = $t_date_from . " 23:59:59";
			}

			$this->v_date_from = $t_date_from;

			return;
		}	

		/*
		*	function to set the date to
		*	@param string $p_date_to
		*	@return String $t_date_to
		*/
		function set_date_to($p_date_to)
		{		
			$t_date_to = trim($p_date_to);
			
			if(!empty($t_date_to))
			{
				$t_date_return	= $t_date_to;
				$t_date_to		= $t_date_to . " 23:59:59";
			}
			else
			{
				$t_date_return	= date('Y-m-d');
				$t_date_to		= date('Y-m-d H:m:s');
			}

			$this->v_date_to = $t_date_to;

			return $t_date_return;
		}	

		/*
		*	function to set the order status id
		*	@param int $p_order_status_id
		*	@return void
		*/
		function set_order_status_id($p_order_status_id)
		{		
			$this->v_order_status_id = $p_order_status_id;

			return;
		}	

		/*
		*	function to set the order fields
		*	@param array $p_order_fields
		*	@return void
		*/
		function set_order_fields($p_order_fields)
		{		
			$this->v_order_fields = $p_order_fields;

			return;
		}	

		/*
		*	function to set the order total fields
		*	@param array $p_order_total_fields
		*	@return void
		*/
		function set_order_total_fields($p_order_total_fields)
		{		
			$this->v_order_total_fields = $p_order_total_fields;

			return;
		}	

		/*
		*	function to set the export file name
		*	@param String $p_export_filename
		*	@return void
		*/
		function set_export_filename($p_export_filename)
		{		
			$this->v_export_filename = $p_export_filename;

			return;
		}	

		/*
		*	function to set the export directory
		*	@param String $p_export_dir
		*	@return void
		*/
		function set_export_dir($p_export_dir)
		{		
			$this->v_export_dir = $p_export_dir;

			return;
		}	

		/*
		*	function to set the csv separator
		*	@param String $p_csv_separator
		*	@return void
		*/
		function set_csv_separator($p_csv_separator)
		{		
			$this->v_csv_separator = $p_csv_separator;

			return;
		}	

		/*
		*	function to set the csv text sign
		*	@param String $p_csv_text_sign
		*	@return void
		*/
		function set_csv_text_sign($p_csv_text_sign)
		{		
			$this->v_csv_text_sign = $p_csv_text_sign;

			return;
		}

		/*
		*	function to get the export file name
		*	@return String
		*/
		function get_export_filename()
		{		
			return $this->v_export_filename;
		}	
		
		/*
		*	function to check if the export filename is empty or not
		*	@return boolean
		*/
		function check_filename()
		{
			if(!empty($this->v_export_filename))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
		*	check if export file has correct fileperms
		*	@return boolean
		*/
		function export_file_perms()
		{
			if (@is_writable($this->v_export_dir . $this->v_export_filename))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
		*	check if export dir have correct fileperms
		*	@return boolean
		*/
		function export_dir_perms()
		{
			if (@is_writable($this->v_export_dir))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
		*	open export file
		*	@param string $p_mode
		*	-> 'w+' (export) to create a new export file & for reading and writing; 
		*	@return boolean
		*/
		function open_export_file($p_mode)
		{
			$t_mode = $p_mode;

			$this->v_export_file_pointer = fopen($this->v_export_dir . $this->v_export_filename, $p_mode);

			if($this->v_export_file_pointer !== false) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}	

		/*
		*	write export file
		*	@param string $p_line
		*	@return void
		*/
		function write_export_file($p_line)
		{
			$t_line = $p_line;

			fwrite($this->v_export_file_pointer, $t_line);

			return;
		}	

		/*
		*	close expirt file
		*	@return void
		*/
		function close_export_file()
		{
			fclose($this->v_export_file_pointer);
			
			return;
		}	

		/*
		*	prepare export
		*	-> create file
		*	@return int t_error_id
		*	-> 0 no error
		*	-> 1 filename empty
		*	-> 2 dirperms incorrect
		*	-> 3 cannot open file
		*	
		*/
		function prepare_export()
		{
			/* check export filename */
			if($this->check_filename() === true)
			{
				/* check dir perms */
				if($this->export_dir_perms() === true)
				{
					/* try to open file */
					if($this->open_export_file($this->v_fopen_mode) === true)
					{
						return 0;
					}
					else
					{
						return 3;
					}
				} 
				else
				{
					return 2;
				}			
			} 
			else
			{
				return 1;
			}			
		}

		/*
		*	get field name
		*	@param	String $p_field
		*	@return	String
		*/
		function get_field_name($p_field)
		{
			return substr($p_field, strpos($p_field, '.')+1);
		}

		/*
		*	get csv table header
		*	@return String
		*/
		function get_csv_table_header()
		{
			$t_export_header = '';
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

			for($i = 0; $i < count($this->v_order_fields); $i++)
			{
				$t_field		= $this->get_field_name($this->v_order_fields[$i]);

				$t_field_name	= @constant('GM_INVOICING_' . strtoupper($t_field));

				$t_export_header .= $this->v_csv_text_sign . $t_field_name . $this->v_csv_text_sign . $this->v_csv_separator;
			}

			for($i = 0; $i < count($this->v_order_total_fields); $i++)
			{
				$t_ot_class_file = DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/order_total/' . $this->v_order_total_fields[$i] . '.php';
				
				if(@file_exists($t_ot_class_file))
				{
					$coo_lang_file_master->init_from_lang_file($t_ot_class_file);

					$t_ot_module	= str_replace('ot_', '', $this->v_order_total_fields[$i]);

					$t_module_name = strip_tags(@constant(strtoupper('MODULE_ORDER_TOTAL_' . $t_ot_module . '_TITLE'))); 

					if(empty($t_module_name))
					{
						$t_module_name = $this->v_order_total_fields[$i]; 
					}
				}
				else
				{
					$t_module_name = $this->v_order_total_fields[$i]; 
				}

				$t_export_header .= $this->v_csv_text_sign . $t_module_name . $this->v_csv_text_sign . $this->v_csv_separator;
			}

			return $t_export_header;
		}

		/*
		*	get data from table order total
		*	@param int $p_order_id
		*	@return String
		*/
		function get_order_total_data($p_order_id)
		{
			$t_export_row = '';

			for($i = 0; $i < count($this->v_order_total_fields); $i++)
			{
				$t_query = xtc_db_query("
											SELECT 
												text											
											FROM " . 
												TABLE_ORDERS_TOTAL . "
											WHERE
												orders_id	= '" . (int)$p_order_id								. "'
											AND
												class		= '" . addslashes($this->v_order_total_fields[$i])	. "'
											LIMIT 1
				");	

				if((int)xtc_db_num_rows($t_query) > 0)
				{		
					$t_row		= xtc_db_fetch_array($t_query);
					
					$t_field	= trim(strip_tags($t_row['text']));

					$t_export_row .= $this->v_csv_text_sign . $t_field . $this->v_csv_text_sign . $this->v_csv_separator;					
				}
				else
				{
					$t_export_row .= $this->v_csv_text_sign . "" . $this->v_csv_text_sign . $this->v_csv_separator;					
				}
			}

			return $t_export_row;			
		}

		/*
		*	export
		*/
		function export()
		{
			/* check if $this->v_order_fields is an array */
			if(!is_array($this->v_order_fields))
			{
				$this->v_order_fields = array();
			}

			/* column o.orders_id is needed - check if column orders_id is selected */
			if(!in_array('o.orders_id', $this->v_order_fields))
			{
				$this->v_order_fields[] = 'o.orders_id';
			}

			/* clean and write csv header into csv file */
			$t_export_header = $this->get_csv_table_header();
			$this->write_export_file(substr($t_export_header, 0, -1) . "\n");

			/* add orders status id to mysql where condition if it is set */
			$t_sql_where = '';
			if(!empty($this->v_order_status_id))
			{
				$t_sql_where = " AND osh.orders_status_id = '" . (int)$this->v_order_status_id . "'";
			}

			/* create fields to select */
			if((int)count($this->v_order_fields) == 1)
			{
				$t_sql_fields = implode($this->v_order_fields, '');
			}
			else
			{
				$t_sql_fields = implode($this->v_order_fields, ',');
			}

			$t_query = xtc_db_query("
										SELECT " . 
											$t_sql_fields . "											
										FROM " . 
											TABLE_ORDERS_STATUS_HISTORY . " osh
										LEFT JOIN " . 										
											TABLE_ORDERS . " o
										ON 
											osh.orders_id		= o.orders_id
										WHERE
											osh.date_added BETWEEN '" . $this->v_date_from . "' AND '" . $this->v_date_to . "'" . 
										$t_sql_where . "
										GROUP BY 
											osh.orders_id
										ORDER BY 
											osh.date_added
										ASC
			");	

			if((int)xtc_db_num_rows($t_query) > 0)
			{		
				while($t_row = xtc_db_fetch_array($t_query))
				{
					$t_export_row = '';
					
					/* get order data */
					for($i = 0; $i < count($this->v_order_fields); $i++)
					{
						$t_field = $this->get_field_name($this->v_order_fields[$i]);
							
						$t_export_row .= $this->v_csv_text_sign . $t_row[$t_field] .  $this->v_csv_text_sign . $this->v_csv_separator;
					}
												
					/* get order total data */
					if((int)count($this->v_order_total_fields) > 0)
					{
						$t_export_row .= $this->get_order_total_data($t_row['orders_id']);
					}
					
					/* write data into opened csv file */
					$this->write_export_file(substr($t_export_row, 0, -1) . "\n");

				}
				/* close csv file */
				$this->close_export_file();
			}
		}
	}

MainFactory::load_origin_class('GMInvoicing');
