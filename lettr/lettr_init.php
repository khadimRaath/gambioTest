<?php
/*	--------------------------------------------------------------
	lettr_init.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * Bindet alle Notwendigen API-Klassen ein.
   * 
   * @package Lettr
   * @access public
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  require_once(DIR_FS_CATALOG.'lettr/lettr_exceptions.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr_validation.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr_client.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr_resource.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr_recipient.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr_delivery.class.php');
  require_once(DIR_FS_CATALOG.'lettr/lettr.class.php');