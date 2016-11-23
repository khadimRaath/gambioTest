<?php
/*	--------------------------------------------------------------
	lettr_exceptions.class.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Exception                    extends Exception { };
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_IllegalArgumentException     extends Lettr_Exception { };
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_CurlException                extends Lettr_Exception {};
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_RestException                extends Lettr_Exception {};
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_UnprocessableEntityException extends Lettr_ClientErrorException {};
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_ClientErrorException extends Lettr_RestException {};
  
  /**
   * @package Lettr
   * @subpackage Exception
   * @access public
   * @author Digineo GmbH, kontakt@digineo.de, http://www.digineo.de
   */
  class Lettr_ServerErrorException extends Lettr_RestException {};