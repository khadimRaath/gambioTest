<?php
/* --------------------------------------------------------------
   xtc_break_string.inc.php 2014-01-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_break_string.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_break_string.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

function xtc_break_string($p_string, $p_length, $p_break_char = '-')
{
	$l = 0;
	$t_output = '';
	
	if(function_exists('mb_strlen'))
	{
		$t_string_length = mb_strlen($p_string, 'utf-8');
	}
	else
	{
		$t_string_length = strlen($p_string);
	}
	
	for($i = 0; $i < $t_string_length; $i++)
	{
		if(function_exists('mb_substr'))
		{
			$t_char = mb_substr($p_string, $i, 1, 'utf-8');
		}
		else
		{
			$t_char = substr($p_string, $i, 1);
		}
		
		if($t_char != ' ')
		{
			$l++;
		}
		else
		{
			$l = 0;
		}
		
		if($l > $p_length)
		{
			$l = 1;
			$t_output .= $p_break_char;
		}
		
		$t_output .= $t_char;
	}

	return $t_output;
}
