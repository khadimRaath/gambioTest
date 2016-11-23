<?php
/* --------------------------------------------------------------
   EmailSubject.inc.php 2015-06-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailSubjectInterface');

/**
 * Class EmailSubject
 *
 * Subject assigned to an email.
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class EmailSubject implements EmailSubjectInterface
{
	/**
	 * Defines the maximum db field length.
	 * @var int
	 */
	const MAX_LENGTH = 256;

	/**
	 * Email subject.
	 * @var string
	 */
	protected $subject;


	/**
	 * Constructor
	 *
	 * Executes the validation checks of the email subject.
	 *
	 * @throws InvalidArgumentException If the provided argument is not valid.
	 *
	 * @param string $p_subject E-Mail subject.
	 */
	public function __construct($p_subject)
	{
		if(!is_string($p_subject))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string subject) $p_subject: '
			                                   . print_r($p_subject, true));
		}

		if(strlen(trim($p_subject)) > self::MAX_LENGTH)
		{
			throw new InvalidArgumentException('Argument exceeded the maximum database field length ('
			                                   . self::MAX_LENGTH . '):' . $p_subject);
		}

		$this->subject = $p_subject;
	}


	/**
	 * Returns the email subject value.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString()
	{
		return $this->subject;
	}

}