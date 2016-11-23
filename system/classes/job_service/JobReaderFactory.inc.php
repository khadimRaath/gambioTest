<?php
/* --------------------------------------------------------------
   JobReaderFactory.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JobReaderFactory
{
	protected $jobReaderArray = array(
								'ProductsFieldReplace' => 'ProductsFieldReplaceJobReader',
								'FieldReplace' => 'FieldReplaceJobReader',
								'ShopNotice' => 'ShopNoticeJobReader');

	public function getJobReader($p_readerName)
	{
		$jobReaderArray = $this->getJobReaderArray();

		switch($p_readerName)
		{
			case 'ShopNotice':
				$shopLanguageReader = MainFactory::create_object('ShopLanguageReader');
				$jobReader = MainFactory::create_object('ShopNoticeJobReader', array($shopLanguageReader) );
				break;

			default:
				if(isset($jobReaderArray[$p_readerName]) == false)
				{
					throw new Exception('unknown JobReader [' . strip_tags($p_readerName) . ']');
				}
				$jobReader = MainFactory::create_object($jobReaderArray[$p_readerName]);
		}

		return $jobReader;
	}

	protected function getJobReaderArray()
	{
		return $this->jobReaderArray;
	}

} 