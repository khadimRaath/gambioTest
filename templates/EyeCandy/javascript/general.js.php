<?php
/* --------------------------------------------------------------
   general.js.php 2008-05-06 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: general.js.php 1262 2005-09-30 10:00:32Z mz $)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


   // this javascriptfile get includes at every template page in shop, you can add your template specific
   // js scripts here

include('templates/'.CURRENT_TEMPLATE.'/javascript/gm_javascript.js.php');
?>

<!--[if IE 6]>
<link rel="stylesheet" href="templates/<?php echo CURRENT_TEMPLATE ?>/ie6fix/fixes-ie6.css" type="text/css" />
<![endif]-->

<!--[if IE 6]>
<script type="text/javascript" src="templates/<?php echo CURRENT_TEMPLATE ?>/ie6fix/DD_belated_0.0.8a-min.js"></script>
<![endif]-->

