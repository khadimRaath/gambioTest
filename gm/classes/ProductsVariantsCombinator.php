<?php
/* --------------------------------------------------------------
   ProductsVariantsCombinator.php 2015-05-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


class ProductsVariantsCombinator_ORIGIN {
	protected static $_options;
	protected static $_properties_names;
	protected static $_properties_values_names;

	public static function getAttributes($lang_id = 2) {
		if(self::$_options == null) {
			$query = "SELECT 
							po.products_options_id, 
							po.products_options_name, 
							pov.products_options_values_id, 
							pov.products_options_values_name
						FROM
							products_options po,
							products_options_values pov,
							products_options_values_to_products_options pov2po
						WHERE
							po.products_options_id = pov2po.products_options_id AND
							po.language_id = '" . (int)$lang_id . "' AND
							pov.products_options_values_id = pov2po.products_options_values_id AND
							pov.language_id = '" . (int)$lang_id . "'";
//			$query = "SELECT po.products_options_id, po.products_options_name, pov.products_options_values_id, pov.products_options_values_name
//				FROM `products_options` po
//				join products_options_values_to_products_options pov2po on pov2po.products_options_id = po.products_options_id
//				join products_options_values pov on pov.products_options_values_id = pov2po.products_options_values_id and pov.language_id = ".(int)$lang_id."
//				where po.language_id = ".(int)$lang_id."
//				order by products_options_id asc, products_options_values_id asc";
			$result = xtc_db_query($query);
			$options = array();
			while($row = xtc_db_fetch_array($result)) {
				if(!is_array($options[$row['products_options_id']])) {
					$options[$row['products_options_id']] = array(
							'name' => $row['products_options_name'],
							'values' => array()
					);
				}
				$options[$row['products_options_id']]['values'][$row['products_options_values_id']] = $row['products_options_values_name'];
			}
			self::$_options = $options;
		}
		return self::$_options;
	}
	
	public static function getPropertyName($properties_id, $language_id) {
		if(empty(self::$_properties_names)) {
			self::$_properties_names = array();
			$result = xtc_db_query("SELECT properties_id, properties_name, language_id FROM properties_description");
			while($row = xtc_db_fetch_array($result)) {
				if(!is_array(self::$_properties_names[$row['properties_id']])) {
					self::$_properties_names[$row['properties_id']] = array();
				}
				self::$_properties_names[$row['properties_id']][$row['language_id']] = $row['properties_name'];
			}
		}
		return self::$_properties_names[$properties_id][$language_id];
	}
	
	public static function getPropertiesValuesName($properties_values_id, $language_id) {
		if(empty(self::$_properties_values_names)) {
			self::$_properties_values_names = array();
			$result = xtc_db_query("SELECT properties_values_id, values_name, language_id FROM properties_values_description");
			while($row = xtc_db_fetch_array($result)) {
				if(!is_array(self::$_properties_values_names[$row['properties_values_id']])) {
					self::$_properties_values_names[$row['properties_values_id']] = array();
				}
				self::$_properties_values_names[$row['properties_values_id']][$row['language_id']] = $row['values_name'];
			}
		}
		return self::$_properties_values_names[$properties_values_id][$language_id];
	}

	public static function getPropertiesCombis($products_id, $language_id = 2) {
		$query = "SELECT * FROM products_properties_index WHERE products_id = :pid AND language_id = :lang_id";
		$query = strtr($query, array(':pid' => (int)$products_id, ':lang_id' => (int)$language_id));
		$result = xtc_db_query($query);
		$properties_combis = array();
		while($row = xtc_db_fetch_array($result)) {
			if(!is_array($properties_combis[$row['products_properties_combis_id']])) {
				$properties_combis[$row['products_properties_combis_id']] = array();
			}
			$properties_combis[$row['products_properties_combis_id']][] = array(
					'properties_id' => $row['properties_id'],
					'properties_values_id' => $row['properties_values_id'],
					'properties_name' => $row['properties_name'],
					'values_name' => $row['values_name'],
			);
		}
		return $properties_combis;
	}

	public static function getVariants($products_id, $language_id = 2) {
		$attrcombis = self::getAttributesCombis($products_id);
		$pproperties = self::getPropertiesCombis($products_id);
		$variants = array();
		if(!empty($attrcombis) && !empty($pproperties)) {
			foreach($attrcombis as $attrcombi) {
				foreach($pproperties as $pcombi_id => $pcombi) {
					$variant = array(
							'options' => $attrcombi,
							'properties_combis_id' => $pcombi_id,
							'properties' => $pcombi,
					);
					$variants[] = $variant;
				}
			}
		}
		else if(!empty($attrcombis) && empty($pproperties)) {
			foreach($attrcombis as $attrcombi) {
				$variant = array(
						'options' => $attrcombi,
				);
				$variants[] = $variant;
			}
		}
		else if(empty($attrcombis) && !empty($pproperties)) {
			foreach($pproperties as $pcombi_id => $pcombi) {
				$variant = array(
						'properties_combis_id' => $pcombi_id,
						'properties' => $pcombi,
				);
				$variants[] = $variant;
			}
		}
		return $variants;
	}

	public static function getAttributesCombis($products_id) {
		$paquery = "SELECT options_id, options_values_id FROM products_attributes pa WHERE pa.products_id = ".(int)$products_id;
		$paresult = xtc_db_query($paquery);
		$options = array();
		while($parow = xtc_db_fetch_array($paresult)) {
			if(!isset($options[$parow['options_id']])) {
				$options[$parow['options_id']] = array();
			}
			$options[$parow['options_id']][] = $parow['options_values_id'];
		}

		$iterator = array();
		foreach($options as $oid => $ovid) {
			$iterator[$oid] = 0;
		}

		$combis = array();
		if(!empty($options)) {
			do {
				$new_combi = array();
				foreach($iterator as $oid => $pointer) {
					$new_combi[$oid] = $options[$oid][$pointer];
				}
				$combis[] = $new_combi;
			} while(self::doIteration($iterator, $options) !== false);
		}
		return $combis;
	}

	protected static function doIteration(&$iterator, $base) {
		$positions = array_keys($iterator);
		$iterator[$positions[0]] += 1;
		for($pos = 0, $max = count($positions) - 1; $pos <= $max; $pos++) {
			if($pos == $max && $iterator[$positions[$pos]] >= count($base[$positions[$pos]])) {
				return false;
			}
			if($iterator[$positions[$pos]] >= count($base[$positions[$pos]])) {
				$iterator[$positions[$pos]] = 0;
				$iterator[$positions[$pos + 1]] += 1;
			}
		}
		return true;
	}
}

MainFactory::load_origin_class('ProductsVariantsCombinator');