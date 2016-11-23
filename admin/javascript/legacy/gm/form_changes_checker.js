/* form_changes_checker.js <?php
#   --------------------------------------------------------------
#   form_changes_checker.js 2013-04-03 dwue@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

form_changes_checker_data_array = new Array();

jQuery.fn.form_changes_checker = function( p_action, p_reset_array ) 
{
	var t_target = this;
	var t_changes_array = new Array();
	
	if( p_action == "initialize" )
	{
		if( p_reset_array != false )
		{
			form_changes_checker_data_array = new Array();
		}
		
		$.each( $( t_target ) , function( form_key, form_value )
		{
			var t_form_id = $( form_value ).attr( "id" );
			var t_form_data = $( "*", form_value ).serialize();

			form_changes_checker_data_array[ t_form_id ] = t_form_data;	
		});
		
		return false;
	}
	else 
	{
		$.each( $( t_target ) , function( form_key, form_value )
		{
			var t_form_id = $( form_value ).attr( "id" );
			var t_form_data = $( "*", form_value ).serialize();

			if( typeof form_changes_checker_data_array[ t_form_id ] == "undefined" || t_form_data != form_changes_checker_data_array[ t_form_id ] )
			{
				t_changes_array.push( t_form_id );
			}		
		});		
		return t_changes_array;
	}
}