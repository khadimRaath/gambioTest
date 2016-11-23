<?php
/* --------------------------------------------------------------
   EmailContent.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailContentInterface');

/**
 * Class EmailContent
 *
 * Email content could be either plain text or HTML.
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class EmailContent implements EmailContentInterface
{
	/**
	 * E-Mail content.
	 * @var string Email Content
	 */
	protected $content;


	/**
	 * Constructor
	 *
	 * Executes the validation checks upon the email content.
	 *
	 * @param string $p_content Could be either plain text or HTML.
	 * @throws InvalidArgumentException On invalid argument.
	 */
	public function __construct($p_content)
	{
		if(!is_string($p_content))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string content) $p_content: '
			                                   . print_r($p_content, true));
		}

		$this->content = $p_content;
	}


	/**
	 * Returns the email content value.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString()
	{
		return $this->content;
	}
}