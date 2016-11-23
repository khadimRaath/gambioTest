<?php
/* --------------------------------------------------------------
   OrderTooltipController.inc.php 2015-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


MainFactory::load_class('AdminHttpViewController');

/**
 * Class OrderTooltipController
 * 
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class OrderTooltipController extends AdminHttpViewController
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	public function __construct(HttpContextReaderInterface $httpContextReader,
								HttpResponseProcessorInterface $httpResponseProcessor,
								ContentViewInterface $defaultContentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);
		
		$gxCoreLoader = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$this->db     = $gxCoreLoader->getDatabaseQueryBuilder();
	}
	
	
	/**
	 * Collect order data and send them as JSON response
	 * 
	 * @return JsonHttpControllerResponse
	 */
	public function actionDefault()
	{
		$order = array();
		$order['products'] = array();
		$order['total_price'] = '';
		
		$orderId = (int)$this->_getQueryParameter('orderId');
		
		$query = 'SELECT 
						o.currency,
						op.orders_products_id AS id,	
						op.products_quantity AS quantity,	
						op.products_name AS name,
						op.products_model AS model,
						op.final_price AS price
					FROM
						orders o,
						orders_products op
					WHERE
						o.orders_id = ' . $orderId . ' AND
						o.orders_id = op.orders_id';
		
		$result = $this->db->query($query);
		foreach($result->result_array() as $row)
		{
			$order['products'][(int)$row['id']] = array(
				'quantity' => (double)$row['quantity'],
				'name' => $row['name'],
				'model' => $row['model'],
				'price' => number_format((double)$row['price'], 2, ',', '.') . ' ' . $row['currency'],
				'attributes' => array()
			);
			
			$propertiesQuery = 'SELECT
									properties_name AS name,
									values_name AS value
								FROM orders_products_properties
								WHERE orders_products_id = ' . $row['id'];
			
			$propertiesResult = $this->db->query($propertiesQuery);
			foreach($propertiesResult->result_array() as $propertiesRow)
			{
				$order['products'][(int)$row['id']]['attributes'][] = $propertiesRow;
			}
			
			$attributesQuery = 'SELECT
									products_options AS name,
									products_options_values AS value
								FROM orders_products_attributes
								WHERE 
									orders_products_id = ' . $row['id'] . ' AND
									products_options != ""';

			$attributesResult = $this->db->query($attributesQuery);
			foreach($attributesResult->result_array() as $attributesRow)
			{
				$order['products'][(int)$row['id']]['attributes'][] = $attributesRow;
			}

			$gPrintContentManager = new GMGPrintContentManager();
			$gPrintResult = $gPrintContentManager->get_orders_products_content($row['id'], true);

			foreach($gPrintResult as $gPrintRow)
			{
				$order['products'][(int)$row['id']]['attributes'][] = array(
					'name' => $gPrintRow['NAME'],
					'value' => $gPrintRow['VALUE']
				);
			}
		}

		$query = 'SELECT title, text FROM orders_total WHERE orders_id = ' . $orderId . ' AND class = "ot_total"';
		$row = $this->db->query($query)->row_array();
		if(isset($row))
		{
			$order['total_price'] = trim(strip_tags($row['title'] . ' ' . $row['text']));
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $order);
	}
}