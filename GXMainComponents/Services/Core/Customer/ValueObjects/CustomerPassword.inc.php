<?php
/* --------------------------------------------------------------
   CustomerPassword.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerPasswordInterface');

/**
 * Value Object
 *
 * Class CustomerPassword
 *
 * Represents a customer password
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerPasswordInterface
 */
class CustomerPassword implements CustomerPasswordInterface
{
	/**
	 * Customer's password.
	 * @var string
	 */
	protected $md5password;


	/**
	 * Constructor for the class CustomerPassword.
	 *
	 * Validates password and build md5-hash.
	 *
	 * @param string $p_password    Customer's password.
	 * @param bool   $p_disableHash (optional) Will not hash the provided password string.
	 *
	 * @throws InvalidArgumentException If $p_password is not a string.
	 */
	public function __construct($p_password, $p_disableHash = false)
	{
		if(!is_string($p_password))
		{
			throw new InvalidArgumentException('$p_password is not a string');
		}
		
		if(!is_bool($p_disableHash))
		{
			throw new InvalidArgumentException('$p_disableHash is not a bool');
		}
		
		$this->md5password = ($p_disableHash === false) ? md5($p_password) : $p_password;
	}


	/**
	 * Returns the equivalent string value (MD5-hash).
	 * @return string Equivalent string value (MD5-hash).
	 */
	public function __toString()
	{
		return $this->md5password;
	}
} 