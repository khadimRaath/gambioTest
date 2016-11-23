<?php
/* --------------------------------------------------------------
  EmbeddedMagnalisterModule.inc.php 2015-11-12 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class EmbeddedMagnalisterModule extends EmbeddedMagnalisterModule_parent
{
	/**
	 * Returns the embedded
	 *
	 * @return \AdminPageHttpControllerResponse
	 */
	public function actionMagnalister()
	{
		$title      = 'magnalister';
		$getParameters = $this->_getQueryParametersCollection();
		$parameterString = '';
		foreach($getParameters as $key => $value)
		{
			if($key === 'do')
			{
				continue;
			}
			$parameterString .= $parameterString === '' ? '?' : '&';
			$parameterString .= $key . '=' . $value;
		}
		$url = DIR_WS_ADMIN . 'magnalister.php' . $parameterString;
		
		return parent::actionDefault($title, $url);
	}
}