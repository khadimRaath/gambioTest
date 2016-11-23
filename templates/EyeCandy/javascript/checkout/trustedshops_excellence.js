/* --------------------------------------------------------------
	trustedshops_excellence.js 2015-02-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function() {
	$('button#remove_tsbp').on('click', function(e) {
		e.preventDefault();
		$.ajax({
			"data": {
				"remove_tsbp": "true",
			},
			"url": "<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG.'request_port.php?module=TrustedShopsExcellence&'.session_name().'='.session_id(); ?>",
			"type": "POST"
		}).done(function(data) {
			location = '<?php echo xtc_href_link('checkout_confirmation.php', '', 'SSL'); ?>';
		});
	})
	$('button#add_tsbp').on('click', function(e) {
		e.preventDefault();
		$.ajax({
			"data": {
				"add_tsbp": "true",
				"amount": $("input[name=tsbp_amount]").val()
			},
			"url": "<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG.'request_port.php?module=TrustedShopsExcellence&'.session_name().'='.session_id(); ?>",
			"type": "POST"
		}).done(function(data) {
			location = '<?php echo xtc_href_link('checkout_confirmation.php', '', 'SSL'); ?>';
		});
	})
});

