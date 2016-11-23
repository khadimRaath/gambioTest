<?php
/* --------------------------------------------------------------
   FeatureProductFinder.inc.php 2010-11-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * class FeatureProductFinder
 * 
 */
/******************************* Abstract Class ****************************
  FeatureProductFinder does not have any pure virtual methods, but its author
  defined it as an abstract class, so you should not use it directly.
  Inherit from it instead and create only objects from the derived classes
*****************************************************************************/

class FeatureProductFinder
{

		/** Aggregations: */

		/** Compositions: */

		/**
		 * ids for inclusive-OR operations
		 *
		 * @param int p_categories_id 
		 * @param bool p_recursively 
		 * @return 
		 * @access public
		 */
		function add_categories_id( $p_categories_id,  $p_recursively = true )
		{
				
		} // end of member function add_categories_id

		/**
		 * ids for AND operations
		 *
		 * @param int p_feature_value_id 
		 * @return 
		 * @access public
		 */
		function add_feature_value_id( $p_feature_value_id )
		{
				
		} // end of member function add_feature_value_id

		/**
		 * 
		 *
		 * @param float p_from 
		 * @param float p_to 
		 * @return 
		 * @access public
		 */
		function set_price_range( $p_from,  $p_to = null )
		{
				
		} // end of member function set_price_range

		/**
		 * 
		 *
		 * @return array
		 * @access public
		 */
		function get_products_array( )
		{
				
		} // end of member function get_products_array





} // end of FeatureProductFinder
?>