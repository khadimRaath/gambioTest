<?php
/* --------------------------------------------------------------
   CustomerB2BStatus.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerB2BStatusInterface');

/**
 * Class CustomerB2BStatus
 *
 * @category System
 * @package Customer
 * @subpackage ValueObjects
 */
class CustomerB2BStatus implements CustomerB2BStatusInterface
{
	/**
	 * Customer's B2B status.
	 * @var bool
	 */
	protected $status;


	/**
	 * CustomerB2BStatus constructor.
	 *
	 * @param bool $p_status Customer's B2B status.
	 * @throws InvalidArgumentException On invalid argument type.
	 */
	public function __construct($p_status)
	{
		if(!is_bool($p_status))
		{
			throw new InvalidArgumentException('$p_status (' . gettype($p_status) . ') is not a boolean');
		}

		$this->status = $p_status;
	}


	/**
	 * Returns the status.
	 * @return bool Customer B2B status.
	 */
	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return (string)(int)$this->status;
	}
}
