/* 
	--------------------------------------------------------------
	validation_plugin.js 2013-07-26 tb@gambio
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

jQuery.fn.validation_plugin = function() 
{
	var t_target = this;
	var t_error_contents = new Array();
	
	// TODO: USE input[class^='validate_'], textarea[class^='validate_']
	jQuery.each( js_options.validation, function( t_valdiation_key, t_validation_value )
	{
		$( ".validate_"+t_valdiation_key, t_target ).each( function( t_dom_key, t_dom_element )
		{
			var t_modifier = '';
			if( typeof t_validation_value.modifier != 'undefined' )
			{
				t_modifier = t_validation_value.modifier;
			}
			var t_error_code_regex = new RegExp( t_validation_value.pattern, t_modifier );
			if( $( this ).val().match( t_error_code_regex ) )
			{
				$( this ).closest( ".row" ).removeClass( "error" );
				$( this ).tooltip_plugin('reset_content');
			}
			else
			{
				$( this ).closest( ".row" ).addClass( "error" );
				if( t_valdiation_key.indexOf( "-" ) >= 0 )
				{
					var t_validation_key_data = t_valdiation_key.split( "-" );
					console.log(t_validation_key_data);
					$( this ).tooltip_plugin('update', js_options.error_handling[t_validation_key_data[0]][t_validation_key_data[1]] );
				}
				else
				{
					$( this ).tooltip_plugin('update', js_options.error_handling[t_valdiation_key] );
				}
				t_error_contents.push( $( this ).attr( "id" ) );
			}
		});
	});

	return t_error_contents;
}