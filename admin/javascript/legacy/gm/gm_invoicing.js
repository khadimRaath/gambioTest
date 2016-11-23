/* gm_invoicing.js <?php
#   --------------------------------------------------------------
#   gm_invoicing.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/


$(document).ready(
	function()
	{
		var dates = $("#GM_INVOICING_DATE_FROM, #GM_INVOICING_DATE_TO").datepicker(
			{
			
				dayNamesMin: ['So', 'Mo','Di','Mi','Do','Fr','Sa'],
				monthNames: ['Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
				firstDay: 1,				
				dateFormat: 'yy-mm-dd',
				changeMonth: false,
				onSelect: 
					function(selectedDate)
					{
						var option = this.id == "GM_INVOICING_DATE_FROM" ? "minDate" : "maxDate",
						instance = $( this ).data( "datepicker" ),
						date = $.datepicker.parseDate(
							instance.settings.dateFormat ||
							$.datepicker._defaults.dateFormat,
							selectedDate, instance.settings );
							dates.not(this).datepicker( "option", option, date );
					}
			}
		);
	}
);