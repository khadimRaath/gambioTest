<?php
/* --------------------------------------------------------------
   collapse_left_menu.php 2015-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?>
<div class="collapse-left-menu"
	data-gx-compatibility="collapse_left_menu"
	data-collapse_left_menu-user-id="<?php echo $_SESSION['customer_id']; ?>"
>
	<span class="menu-toggle-button">
		<span class="cursor-pointer">
			<i id="menu-button-indicator"></i>
			<i class="fa fa-bars"></i>
		</span>
	</span>
</div>
