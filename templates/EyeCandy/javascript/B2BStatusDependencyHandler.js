/* B2BStatusDependencyHandler.js <?php
 #   --------------------------------------------------------------
 #   B2BStatusDependencyHandler.js 2014-12-17 gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/

function B2BStatusDependencyHandler()
{
    if(fb)console.log('B2BStatusDependencyHandler ready');
    
    var coo_this = this;
    coo_this.oldB2BStatus = 0;

    this.init_binds = function()
    {
        if(fb)console.log('B2BStatusDependencyHandler init_binds');
        $('input.input-text[name="company"]').on('keydown', coo_this.rememberB2BStatus);
        $('input.input-text[name="company"]').on('keyup', coo_this.toggleB2BStatus);
        $('input.input-text[name="company"]').on('change', coo_this.toggleB2BStatus);
        $('input.input-text[name="vat"]').on('keydown', coo_this.rememberB2BStatus);
        $('input.input-text[name="vat"]').on('keyup', coo_this.toggleB2BStatus);
        $('input.input-text[name="vat"]').on('change', coo_this.toggleB2BStatus);
	    $('#create_account').on('submit', coo_this.enableBeforeSubmit);
    }

    this.rememberB2BStatus = function()
    {
        if(fb)console.log('B2BStatusDependencyHandler rememberB2BStatus');
        if($('input.input-text[name="company"]').length && $('input.input-text[name="company"]').val() == '' ||
            $('input.input-text[name="vat"]').length && $('input.input-text[name="vat"]').val() == '')
        {
            coo_this.oldB2BStatus = ($('input[name="b2b_status"][type="radio"][value="1"]').prop('checked') ? 1 : 0);
        }
    }
    
    this.toggleB2BStatus = function()
    {
        if(fb)console.log('B2BStatusDependencyHandler toggleB2BStatus');

        if($('input.input-text[name="company"]').length && $('input.input-text[name="company"]').val() != '' || 
            $('input.input-text[name="vat"]').length && $('input.input-text[name="vat"]').val() != '')
        {
            $('input[name="b2b_status"][type="radio"]').prop('disabled', 'disabled');
            $('input[name="b2b_status"][type="radio"][value="1"]').prop('checked', 'checked');
            $('input[name="b2b_status"][type="hidden"]').val(1);
        }
        else
        {
            $('input[name="b2b_status"][type="hidden"]').val(<?php echo ACCOUNT_DEFAULT_B2B_STATUS == 'true' ? 1 : 0; ?>);
            $('input[name="b2b_status"][type="radio"][value="' + (coo_this.oldB2BStatus ? 1 : 0) + '"]').prop('checked', 'checked');
            $('input[name="b2b_status"][type="radio"]').enable();
        }
    }

	this.enableBeforeSubmit = function() {
		$('input[name="b2b_status"][type="radio"]').removeAttr('disabled');
		return true;
	};

    this.init_binds();
}

