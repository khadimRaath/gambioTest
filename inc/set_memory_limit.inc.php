<?php
/* --------------------------------------------------------------
   set_memory_limit.php 2016-07-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function set_memory_limit($limitInMegaBytes = 128)
{
	$minMemoryLimitGiven = false;
	$minMemoryLimit      = (string)$limitInMegaBytes . 'M';
	
	if(function_exists('ini_get') && function_exists('ini_set'))
	{
		$serverMemoryLimit = @ini_get('memory_limit');
		
		if(preg_match('/[\d]+M/', (string)$serverMemoryLimit))
		{
			$memoryLimit = (int)substr($serverMemoryLimit, 0, -1);
			if($memoryLimit < $limitInMegaBytes)
			{
				@ini_set('memory_limit', $minMemoryLimit);
				if(@ini_get('memory_limit') === $minMemoryLimit)
				{
					$minMemoryLimitGiven = true;
				}
			}
			else
			{
				$minMemoryLimitGiven = true;
			}
		}
		elseif(preg_match('/^[\d]+$/', (string)$serverMemoryLimit))
		{
			$memoryLimit    = (int)$serverMemoryLimit;
			$minMemoryLimit = $limitInMegaBytes * 1024 * 1024;
			
			if($memoryLimit < $minMemoryLimit)
			{
				@ini_set('memory_limit', $minMemoryLimit);
				if(@ini_get('memory_limit') === $minMemoryLimit)
				{
					$minMemoryLimitGiven = true;
				}
			}
			else
			{
				$minMemoryLimitGiven = true;
			}
		}
	}
	
	return $minMemoryLimitGiven;
}