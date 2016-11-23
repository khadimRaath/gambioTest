<?php

/* --------------------------------------------------------------
   HttpViewControllerRegistry.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('Registry');
MainFactory::load_class('HttpViewControllerRegistryInterface');

/**
 * Class HttpViewControllerRegistry
 *
 * @category   System
 * @package    Http
 * @extends    Registry
 * @implements HttpViewControllerRegistryInterface
 */
class HttpViewControllerRegistry extends Registry implements HttpViewControllerRegistryInterface
{
}