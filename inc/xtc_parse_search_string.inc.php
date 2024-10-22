<?php
/* --------------------------------------------------------------
   xtc_parse_search_string.inc.php 2014-02-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_parse_search_string.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_parse_search_string.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function xtc_parse_search_string($search_str = '', &$objects)
{
	$search_str = trim(strtolower_wrapper($search_str));

	// Break up $search_str on whitespace; quoted string will be reconstructed later
	$pieces = preg_split('/[[:space:]]+/', $search_str);
	$objects = array();
	$tmpstring = '';
	$flag = '';

	for($k = 0; $k < count($pieces); $k++)
	{
		while(substr_wrapper($pieces[$k], 0, 1) == '(')
		{
			$objects[] = '(';
			if(strlen_wrapper($pieces[$k]) > 1)
			{
				$pieces[$k] = substr_wrapper($pieces[$k], 1);
			}
			else
			{
				$pieces[$k] = '';
			}
		}

		$post_objects = array();

		while(substr_wrapper($pieces[$k], -1) == ')')
		{
			$post_objects[] = ')';
			
			if(strlen_wrapper($pieces[$k]) > 1)
			{
				$pieces[$k] = substr_wrapper($pieces[$k], 0, -1);
			}
			else
			{
				$pieces[$k] = '';
			}
		}

		// Check individual words

		if(substr_wrapper($pieces[$k], -1) != '"' && substr_wrapper($pieces[$k], 0, 1) != '"')
		{
			$objects[] = trim($pieces[$k]);

			for($j = 0; $j < count($post_objects); $j++)
			{
				$objects[] = $post_objects[$j];
			}
		}
		else
		{
			/* This means that the $piece is either the beginning or the end of a string.
			  So, we'll slurp up the $pieces and stick them together until we get to the
			  end of the string or run out of pieces.
			 */

			// Add this word to the $tmpstring, starting the $tmpstring
			$tmpstring = trim(str_replace('"', ' ', $pieces[$k]));

			// Check for one possible exception to the rule. That there is a single quoted word.
			if(substr_wrapper($pieces[$k], -1) == '"')
			{
				// Turn the flag off for future iterations
				$flag = 'off';

				$objects[] = trim($pieces[$k]);

				for($j = 0; $j < count($post_objects); $j++)
				{
					$objects[] = $post_objects[$j];
				}

				unset($tmpstring);

				// Stop looking for the end of the string and move onto the next word.
				continue;
			}

			// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
			$flag = 'on';

			// Move on to the next word
			$k++;

			// Keep reading until the end of the string as long as the $flag is on

			while($flag == 'on' && $k < count($pieces))
			{
				while(substr_wrapper($pieces[$k], -1) == ')')
				{
					$post_objects[] = ')';
					
					if(strlen_wrapper($pieces[$k]) > 1)
					{
						$pieces[$k] = substr_wrapper($pieces[$k], 0, -1);
					}
					else
					{
						$pieces[$k] = '';
					}
				}

				// If the word doesn't end in double quotes, append it to the $tmpstring.
				if(substr_wrapper($pieces[$k], -1) != '"')
				{
					// Tack this word onto the current string entity
					$tmpstring .= ' ' . $pieces[$k];

					// Move on to the next word
					$k++;
					continue;
				}
				else
				{
					/* If the $piece ends in double quotes, strip the double quotes, tack the
					  $piece onto the tail of the string, push the $tmpstring onto the $haves,
					  kill the $tmpstring, turn the $flag "off", and return.
					 */
					$tmpstring .= ' ' . trim(str_replace('"', ' ', $pieces[$k]));

					// Push the $tmpstring onto the array of stuff to search for
					$objects[] = trim($tmpstring);

					for($j = 0; $j < count($post_objects); $j++)
					{
						$objects[] = $post_objects[$j];
					}

					unset($tmpstring);

					// Turn off the flag to exit the loop
					$flag = 'off';
				}
			}
		}
	}

	// add default logical operators if needed
	$temp = array();
	for($i = 0; $i < (count($objects) - 1); $i++)
	{
		$temp[] = $objects[$i];
		
		if($objects[$i] != 'and' &&
			$objects[$i] != 'or' &&
			$objects[$i] != '(' &&
			$objects[$i + 1] != 'and' &&
			$objects[$i + 1] != 'or' &&
			$objects[$i + 1] != ')')
		{
			$temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
		}
	}
	
	$temp[] = $objects[$i];
	$objects = $temp;

	$keyword_count = 0;
	$operator_count = 0;
	$balance = 0;
	
	for($i = 0; $i < count($objects); $i++)
	{
		if($objects[$i] == '(')
		{
			$balance --;
		}
		
		if($objects[$i] == ')')
		{
			$balance ++;
		}
		
		if($objects[$i] == 'and' || $objects[$i] == 'or')
		{
			$operator_count ++;
		}
		elseif($objects[$i] && $objects[$i] != '(' && $objects[$i] != ')')
		{
			$keyword_count ++;
		}
	}

	if(($operator_count < $keyword_count) && ($balance == 0))
	{
		return true;
	}
	else
	{
		return false;
	}
}
