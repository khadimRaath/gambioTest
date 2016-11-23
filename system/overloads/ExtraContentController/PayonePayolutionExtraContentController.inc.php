<?php
/* --------------------------------------------------------------
	PayonePayolutionExtraContentController.inc.php 2016-05-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Retrieves and displays a privacy note from Payolution for the Payone Payolution Safe Invoicing module.
 * In accordance with the design specifications this proxying of the content is required because
 * the customer must not know at any time that this document is delivered by a Payolution server.
*/
class PayonePayolutionExtraContentController extends PayonePayolutionExtraContentController_parent
{
	public function actionPayolutionNote()
	{
		try
		{
			$genreIdentifier = $this->_getQueryParameter('config');
			if(empty($genreIdentifier))
			{
				throw new Exception('invalid call - missing required parameter config');
			}
			$payone = new GMPayOne();
			$config = $payone->getConfig();
			if(!array_key_exists($genreIdentifier, $config))
			{
				throw new Exception('invalid call - configuration not found');
			}
			$pg_config = $config[$genreIdentifier];
			$url = 'https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?mId=';
			$url .= base64_encode($pg_config['genre_specific']['payolution_company_name']);
			$content = $this->_getExternalContent($url);
		}
		catch (Exception $e)
		{
			$content = $e->getMessage();
		}
		return MainFactory::create('HttpControllerResponse', $content);
	}
}
