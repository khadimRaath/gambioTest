/* 
	--------------------------------------------------------------
	max_upload_files.js 2013-02-28 tt@gambio
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
 
    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/

$(document).ready(function(){
	$('form').submit(function(){
        $('input:file').filter(function(){
            return !$(this).val();
        }).attr('disabled', true);        
	});
});