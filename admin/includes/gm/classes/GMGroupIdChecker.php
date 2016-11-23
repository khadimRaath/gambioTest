<?php
/* --------------------------------------------------------------
   GMGroupIdChecker.php 2014-06-21 gm
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

	/*
	*	class to manage the group ids of the content managerc ..
	*/
	class GMGroupIdChecker_ORIGIN
	{
		/*
		*	actual used languages id
		*	@var int
		*/
		var $v_languages_id;
		
		/*
		*	constructor
		*	@param	int $p_content_group_id
		*/		
		function __construct($p_languages_id)
		{	
			$this->v_languages_id		= $p_languages_id;
			
			return;
		}


		/*
		*	check if actual content group id already exists
		*	@param	int $p_content_group_id
		*	@param	int $p_languages_id
		*	@return boolean
		*/		
		function content_group_id_exist($p_content_group_id, $p_languages_id)
		{
			$t_content_group_id = gm_prepare_string($p_content_group_id);
			$t_languages_id		= gm_prepare_string($p_languages_id);

			$t_query = xtc_db_query("
										SELECT
											*
										FROM " . 
											TABLE_CONTENT_MANAGER . "
										WHERE
											content_group	= '" . $t_content_group_id	. "'
										AND 
											languages_id	= '" . $t_languages_id		. "'											
			");

			if((int)xtc_db_num_rows($t_query) > 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		/*
		*	check if actual content group id already exists
		*	@param	int $p_content_group_id
		*	@return boolean/int
		*/		
		function suggest_content_group_id($p_languages_id = '')
		{
			if(empty($p_languages_id))
			{
				$t_languages_id = $this->v_languages_id;
			}
			else
			{
				$t_languages_id = $p_languages_id;
			}


			$t_query = xtc_db_query("
										SELECT
											content_group
										FROM " .
											TABLE_CONTENT_MANAGER . "
										WHERE
											languages_id = '" . $t_languages_id	. "'
											AND content_group < 3889891
										ORDER BY
											content_group DESC
										LIMIT 1
			");

			if((int)xtc_db_num_rows($t_query) > 0)
			{
				$t_row = xtc_db_fetch_array($t_query);
				$t_content_group_id = (int)$t_row['content_group'] + 1;
				return $t_content_group_id;
			}
		}
	}

MainFactory::load_origin_class('GMGroupIdChecker');
