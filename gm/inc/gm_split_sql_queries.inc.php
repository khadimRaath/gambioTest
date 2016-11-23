<?php
/* --------------------------------------------------------------
  gm_split_sql_queries.inc.php 2014-07-22 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

function gm_split_sql_queries($query)
{
	$query = trim($query);
	
	if(substr($query, -1) == ';')
	{
		$query = substr($query, 0, -1);
	}

	$start_pos = 0;
	$quote = '';

	$single_quote_pos = strpos($query, "'", $start_pos);
	$double_quote_pos = strpos($query, '"', $start_pos);

	if($single_quote_pos !== false && $double_quote_pos !== false)
	{
		if($single_quote_pos < $double_quote_pos)
		{
			$quote = "'";
			$query = str_replace("\'", "###SINGLE_QUOTE###", $query);
		}
		else
		{
			$quote = '"';
			$query = str_replace('\"', '###DOUBLE_QUOTE###', $query);
		}
	}
	elseif($single_quote_pos !== false)
	{
		$quote = "'";
		$query = str_replace("\'", "###SINGLE_QUOTE###", $query);
	}
	elseif($double_quote_pos !== false)
	{
		$quote = '"';
		$query = str_replace('\"', '###DOUBLE_QUOTE###', $query);
	}

	if($quote != '')
	{
		$quotation_mark_pos = strpos($query, $quote, $start_pos);
		$quotation_mark_pos2 = strpos($query, $quote, $quotation_mark_pos + 1);

		if($quotation_mark_pos !== false)
		{
			while($quotation_mark_pos2 !== false)
			{
				$semicolon = strpos($query, ";", $start_pos);
				
				if($semicolon < $quotation_mark_pos && $semicolon !== false)
				{
					$start_pos2 = $quotation_mark_pos2;
					
					while($semicolon < $quotation_mark_pos && $semicolon !== false)
					{
						$string1 = substr($query, 0, $semicolon);
						$string2 = substr($query, $semicolon + 1);
						$query = $string1 . "#|#" . $string2;
						$quotation_mark_pos += 2;
						$semicolon = strpos($query, ";", $start_pos);
						$start_pos2 += 2;
					}

					$start_pos = $start_pos2;
					
					if($start_pos < strlen($query))
					{
						$quotation_mark_pos = strpos($query, $quote, $start_pos + 1);
						
						if($quotation_mark_pos === false || $quotation_mark_pos > strlen($query))
						{
							$quotation_mark_pos2 = false;
						}
						else
						{
							$quotation_mark_pos2 = strpos($query, $quote, $quotation_mark_pos + 1);
						}
					}
					else
					{
						$quotation_mark_pos2 = false;
					}
				}
				else
				{
					$start_pos = $quotation_mark_pos2;
					
					if($start_pos < strlen($query) && $start_pos != '')
					{
						$quotation_mark_pos = strpos($query, $quote, $start_pos + 1);
						
						if($quotation_mark_pos === false || $quotation_mark_pos > strlen($query))
						{
							$quotation_mark_pos2 = false;
						}
						else
						{
							$quotation_mark_pos2 = strpos($query, $quote, $quotation_mark_pos + 1);
						}
					}
					else
					{
						$quotation_mark_pos2 = false;
					}
				}
			}
		}
	}
	else
	{
		$semicolon = strpos($query, ";", $start_pos);
		
		while($semicolon !== false)
		{
			$string1 = substr($query, 0, $semicolon);
			$string2 = substr($query, $semicolon + 1);
			$query = $string1 . '#|#' . $string2;
			$semicolon = strpos($query, ";", $start_pos);
		}
	}

	$start_pos = strrpos($query, "'");
	if($start_pos !== false)
	{
		$semicolon = strpos($query, ";", $start_pos);
		
		if($semicolon !== false)
		{
			$string1 = substr($query, 0, $semicolon);
			$string2 = substr($query, $semicolon + 1);
			$query = $string1 . '#|#' . $string2;
		}
	}

	$query = str_replace("###SINGLE_QUOTE###", "\'", $query);
	$query = str_replace('###DOUBLE_QUOTE###', '\"', $query);
	$queries = explode('#|#', $query);

	return $queries;
}
