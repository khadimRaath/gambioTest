/* 
	--------------------------------------------------------------
	lightbox_google_admin_categories.js 2012-05-04 tb@gambio
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2012 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
 
    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/

$(document).ready(function()
{
    $("a.lightbox_google_admin_categories").bind('click', function(){
        $( this ).lightbox_plugin('lightbox_open', {'width': 840});
        return false;
    });
});