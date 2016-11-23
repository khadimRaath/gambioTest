<?php
/* --------------------------------------------------------------
   Id.inc.php 2015-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('IdInterface');
MainFactory::load_class('IdType');

/**
 * Class Id
 *
 * @deprecated Since v2.7.1.0, will be removed with v3.3.1.0.
 *
 * NOTICE:
 *
 * When you need to cast an Id object to integer, cast it first to string,
 * because otherwise the following command will return always 1:
 *
 * EXAMPLE:
 *
 * $id = new Id(948);
 * bad  - (int)$id 		>> 1
 * good - (int)(string)$id >> 948
 *
 * @category System
 * @package Shared
 */
class Id extends IdType implements IdInterface
{

}
