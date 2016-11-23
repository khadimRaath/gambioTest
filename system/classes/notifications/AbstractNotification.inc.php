<?php
/* --------------------------------------------------------------
   AbstractNotification.inc.php 2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class AbstractNotification
 */
abstract class AbstractNotification
{
	public abstract function getId();
	public abstract function setId($p_id);
	public abstract function isActive();
	public abstract function setActive($p_active);
	public abstract function getContentArray();
	public abstract function getContentByLanguageId($p_languageId);
	public abstract function setContentArray(array $p_contentArray);
	public abstract function setContentByLanguageId($p_languageId, $p_content);
}