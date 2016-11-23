<?php
/* --------------------------------------------------------------
   GMLightboxControl.php 2015-05-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

	
	class GMLightboxControl_ORIGIN {
		
		var $previous;
		var $actual;
		
		function __construct() {
			
			$this->previous = 'false';
			$this->actual = 'false';
			
		}
		
		function set_actual($new_actual){
			$this->previous = $this->actual;
			$this->actual = $new_actual;
		}
		
	}
MainFactory::load_origin_class('GMLightboxControl');