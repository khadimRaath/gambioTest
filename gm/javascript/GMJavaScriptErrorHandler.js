/* GMJavaScriptErrorHandler.js <?php
#   --------------------------------------------------------------
#   ActionAddToCartHandler.js 2013-12-18 wu
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

/*@cc_on @if (@_win32 && @_jscript_version >= 5) if (!window.XMLHttpRequest)
window.XMLHttpRequest = function() { return new ActiveXObject('Microsoft.XMLHTTP') }
@end @*/

function xhr(method, url, data, cb, apply_para) {
    method = method.toLowerCase();
    var req;
    req = new XMLHttpRequest();
    req.open(method, url + (data && method == 'get' ? '?' + data : ''), true);
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    if (method == 'post') {
	req.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
	req.setRequestHeader("Content-Length", data.length);
    }
    req.onreadystatechange = function() {
	if (req.readyState == 4 && req.status == 200) {
		if (cb) {
		cb.apply(null, [req].concat(apply_para));
	    }
	}
    }
    req.send(data);
}

function JSON(data) {
    var formValues = '{';
    for (var key in data)
    {
	formValues = formValues + '\"' + key + '\":\"' + data[key] + '\",';
    }
    return formValues = formValues.substr(0, formValues.length-1) + '}';
}

function handleJsError(errortype, file, line) {
    var data = new Array();
    data['error_type'] = errortype;
    data['file'] = file.toString().replace(/&/g, '__-amp-__');
    data['line'] = line;
    xhr('post', 'request_port.php?module=JavaScriptErrorHandler', 'data=' + JSON(data), noop);
}

function noop(req) {
    //alert(req.responseText);
}