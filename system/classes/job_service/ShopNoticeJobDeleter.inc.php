<?php
/* --------------------------------------------------------------
   ShopNoticeJobDeleter.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class ShopNoticeJobDeleter
{
	public function delete(ShopNoticeJob $noticeJob)
	{
		$shopNoticeJobId = $noticeJob->getShopNoticeJobId();
		$this->deleteById($shopNoticeJobId);
	}


	public function deleteById($p_shopNoticeJobId)
	{
		$c_shopNoticeJobId = (int)$p_shopNoticeJobId;
		if($c_shopNoticeJobId == 0)
		{
			throw new Exception('No Id to delete');
		}

		$this->delete_from_shop_notice_jobs($c_shopNoticeJobId);
		$this->delete_from_shop_notice_job_content($c_shopNoticeJobId);
	}


	protected function delete_from_shop_notice_jobs($p_shopNoticeJobId)
	{
		$sql = '
			DELETE FROM shop_notice_jobs
			WHERE
				shop_notice_job_id = "'.(int)$p_shopNoticeJobId.'"
		';
		xtc_db_query($sql);
	}


	protected function delete_from_shop_notice_job_content($p_shopNoticeJobId)
	{
		$sql = '
			DELETE FROM shop_notice_job_contents
			WHERE
				shop_notice_job_id = "'.(int)$p_shopNoticeJobId.'"
		';
		xtc_db_query($sql);
	}
}