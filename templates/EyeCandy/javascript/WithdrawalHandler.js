/* WithdrawalHandler.js <?php
 #   --------------------------------------------------------------
 #   WithdrawalHandler.js 2014-06-24 gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function WithdrawalHandler(){if(fb)console.log('WithdrawalHandler ready');var thiz=this;this.init_binds=function(){if(fb)console.log('WithdrawalHandler init_binds');if('<?php echo $_SESSION["language_code"]; ?>'=='de'){$('.setDatepicker').datepicker({dayNamesMin:['So','Mo','Di','Mi','Do','Fr','Sa'],monthNames:['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],firstDay:1,dateFormat:'dd.mm.yy',changeMonth:false})}else{$('.setDatepicker').datepicker({firstDay:1,dateFormat:'dd.mm.yy',changeMonth:false})}$('.withdrawal_form_switcher').change(function(){var toSwitch=$(this).val();thiz.switch_withdrawal_form_getordered_on(toSwitch)})};this.switch_withdrawal_form_getordered_on=function(direction){if(direction==='ordered_on'){$('#withdrawal_form_get_on').hide();$('#withdrawal_form_ordered_on').show();$('#withdrawal_form_withdrawal_date_get_on').attr('disabled','disabled');$('#withdrawal_form_withdrawal_date_orderd_on').removeAttr('disabled')}else{$('#withdrawal_form_ordered_on').hide();$('#withdrawal_form_get_on').show();$('#withdrawal_form_withdrawal_date_get_on').removeAttr('disabled');$('#withdrawal_form_withdrawal_date_orderd_on').attr('disabled','disabled')}};this.init_binds()}
/*<?php
}
else
{
?>*/
function WithdrawalHandler() {
    if (fb)console.log('WithdrawalHandler ready');

    var thiz = this;

    this.init_binds = function () {
        if (fb)console.log('WithdrawalHandler init_binds');

		if('<?php echo $_SESSION["language_code"]; ?>' == 'de')
		{
			$('.setDatepicker').datepicker({dayNamesMin: ['So', 'Mo','Di','Mi','Do','Fr','Sa'],
				monthNames: ['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
				firstDay: 1,
				dateFormat: 'dd.mm.yy',
				changeMonth: false
			});
		}
		else
		{
			$('.setDatepicker').datepicker({firstDay: 1,
				dateFormat: 'dd.mm.yy',
				changeMonth: false
			});
		}

        $('.withdrawal_form_switcher').change(function () {
            var toSwitch = $(this).val();
            thiz.switch_withdrawal_form_getordered_on(toSwitch);
        });
    };

    this.switch_withdrawal_form_getordered_on = function(direction) {
        if (direction === 'ordered_on') {
            $('#withdrawal_form_get_on').hide();
            $('#withdrawal_form_ordered_on').show();
            $('#withdrawal_form_withdrawal_date_get_on').attr('disabled', 'disabled');
            $('#withdrawal_form_withdrawal_date_orderd_on').removeAttr('disabled');
        } else {
            $('#withdrawal_form_ordered_on').hide();
            $('#withdrawal_form_get_on').show();
            $('#withdrawal_form_withdrawal_date_get_on').removeAttr('disabled');
            $('#withdrawal_form_withdrawal_date_orderd_on').attr('disabled', 'disabled');
        }
    };


    this.init_binds();
}
/*<?php
}
?>*/