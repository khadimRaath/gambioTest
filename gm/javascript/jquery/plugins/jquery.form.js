/*
 * jQuery Form Plugin
 * version: 2.07 (03/04/2008)
 * @requires jQuery v1.2.2 or later
 *
 * Examples at: http://malsup.com/jquery/form/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 */
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(5($){$.z.1E=5(r){2(L r==\'5\')r={M:r};r=$.1F({1a:4.R(\'1b\')||1k.2o.2p(),E:4.R(\'1G\')||\'1H\'},r||{});3 u={};4.N(\'D-2q-2r\',[4,r,u]);2(u.1I)6 4;3 a=4.1l(r.2s);2(r.O){r.1c=r.O;F(3 n 1J r.O)a.A({7:n,8:r.O[n]})}2(r.1K&&r.1K(a,4,r)===G)6 4;4.N(\'D-P-2t\',[a,4,r,u]);2(u.1I)6 4;3 q=$.1m(a);2(r.E.2u()==\'1H\'){r.1a+=(r.1a.2v(\'?\')>=0?\'&\':\'?\')+q;r.O=B}C r.O=q;3 v=4,S=[];2(r.1n)S.A(5(){v.1n()});2(r.1o)S.A(5(){v.1o()});2(!r.W&&r.X){3 w=r.M||5(){};S.A(5(a){$(r.X).2w(a).J(w,2x)})}C 2(r.M)S.A(r.M);r.M=5(a,b){F(3 i=0,H=S.I;i<H;i++)S[i](a,b,v)};3 x=$(\'Y:2y\',4).Z();3 y=G;F(3 j=0;j<x.I;j++)2(x[j])y=T;2(r.1L||y){2($.1d.2z&&r.1M)$.2A(r.1M,1p);C 1p()}C $.2B(r);4.N(\'D-P-2C\',[4,r]);6 4;5 1p(){3 h=v[0];3 i=$.1F({},$.2D,r);3 j=\'2E\'+(1q 2F().2G());3 k=$(\'<1L 2H="\'+j+\'" 7="\'+j+\'" />\');3 l=k[0];3 m=$.1d.1N&&1k.1N.2I()<9;2($.1d.1O||m)l.2J=\'2K:G;1r.2L("");\';k.2M({2N:\'2O\',1P:\'-1Q\',1R:\'-1Q\'});3 o={11:B,17:B,2P:0,2Q:\'n/a\',2R:5(){},1S:5(){},2S:5(){}};3 g=i.2T;2(g&&!$.1T++)$.18.N("2U");2(g)$.18.N("2V",[o,i]);3 p=0;3 q=0;1e(5(){3 t=v.R(\'X\'),a=v.R(\'1b\');v.R({X:j,2W:\'1U/D-O\',2X:\'1U/D-O\',1G:\'2Y\',1b:i.1a});2(i.1s)1e(5(){q=T;12()},i.1s);3 b=[];1V{2(r.1c)F(3 n 1J r.1c)b.A($(\'<Y E="2Z" 7="\'+n+\'" 8="\'+r.1c[n]+\'" />\').1W(h)[0]);k.1W(\'1t\');l.1X?l.1X(\'1Y\',12):l.30(\'1Z\',12,G);h.P()}31{v.R(\'1b\',a);t?v.R(\'X\',t):v.32(\'X\');$(b).20()}},10);5 12(){2(p++)6;l.21?l.21(\'1Y\',12):l.33(\'1Z\',12,G);3 c=T;1V{2(q)34\'1s\';3 d,Q;Q=l.22?l.22.1r:l.23?l.23:l.1r;o.11=Q.1t?Q.1t.35:B;o.17=Q.24?Q.24:Q;o.1S=5(a){3 b={\'36-E\':i.W};6 b[a]};2(i.W==\'37\'||i.W==\'38\'){3 f=Q.1u(\'1v\')[0];o.11=f?f.8:o.11}C 2(i.W==\'25\'&&!o.17&&o.11!=B){o.17=26(o.11)}d=$.39(o,i.W)}3a(e){c=G;$.3b(i,o,\'27\',e)}2(c){i.M(d,\'M\');2(g)$.18.N("3c",[o,i])}2(g)$.18.N("3d",[o,i]);2(g&&!--$.1T)$.18.N("3e");2(i.28)i.28(o,c?\'M\':\'27\');1e(5(){k.20();o.17=B},3f)}5 26(s,a){2(1k.29){a=1q 29(\'3g.3h\');a.3i=\'G\';a.3j(s)}C a=(1q 3k()).3l(s,\'1w/25\');6(a&&a.2a&&a.2a.1f!=\'3m\')?a:B}}};$.z.3n=5(c){6 4.2b().2c(\'P.D-1g\',5(){$(4).1E(c);6 G}).J(5(){$(":P,Y:13",4).2c(\'2d.D-1g\',5(e){3 a=4.D;a.U=4;2(4.E==\'13\'){2(e.2e!=V){a.14=e.2e;a.15=e.3o}C 2(L $.z.2f==\'5\'){3 b=$(4).2f();a.14=e.2g-b.1R;a.15=e.2h-b.1P}C{a.14=e.2g-4.3p;a.15=e.2h-4.3q}}1e(5(){a.U=a.14=a.15=B},10)})})};$.z.2b=5(){4.2i(\'P.D-1g\');6 4.J(5(){$(":P,Y:13",4).2i(\'2d.D-1g\')})};$.z.1l=5(b){3 a=[];2(4.I==0)6 a;3 c=4[0];3 d=b?c.1u(\'*\'):c.3r;2(!d)6 a;F(3 i=0,H=d.I;i<H;i++){3 e=d[i];3 n=e.7;2(!n)1x;2(b&&c.U&&e.E=="13"){2(!e.1h&&c.U==e)a.A({7:n+\'.x\',8:c.14},{7:n+\'.y\',8:c.15});1x}3 v=$.Z(e,T);2(v&&v.1i==1j){F(3 j=0,2j=v.I;j<2j;j++)a.A({7:n,8:v[j]})}C 2(v!==B&&L v!=\'V\')a.A({7:n,8:v})}2(!b&&c.U){3 f=c.1u("Y");F(3 i=0,H=f.I;i<H;i++){3 g=f[i];3 n=g.7;2(n&&!g.1h&&g.E=="13"&&c.U==g)a.A({7:n+\'.x\',8:c.14},{7:n+\'.y\',8:c.15})}}6 a};$.z.3s=5(a){6 $.1m(4.1l(a))};$.z.3t=5(b){3 a=[];4.J(5(){3 n=4.7;2(!n)6;3 v=$.Z(4,b);2(v&&v.1i==1j){F(3 i=0,H=v.I;i<H;i++)a.A({7:n,8:v[i]})}C 2(v!==B&&L v!=\'V\')a.A({7:4.7,8:v})});6 $.1m(a)};$.z.Z=5(a){F(3 b=[],i=0,H=4.I;i<H;i++){3 c=4[i];3 v=$.Z(c,a);2(v===B||L v==\'V\'||(v.1i==1j&&!v.I))1x;v.1i==1j?$.3u(b,v):b.A(v)}6 b};$.Z=5(b,c){3 n=b.7,t=b.E,16=b.1f.1y();2(L c==\'V\')c=T;2(c&&(!n||b.1h||t==\'19\'||t==\'3v\'||(t==\'1z\'||t==\'1A\')&&!b.1B||(t==\'P\'||t==\'13\')&&b.D&&b.D.U!=b||16==\'K\'&&b.1C==-1))6 B;2(16==\'K\'){3 d=b.1C;2(d<0)6 B;3 a=[],1D=b.3w;3 e=(t==\'K-2k\');3 f=(e?d+1:1D.I);F(3 i=(e?d:0);i<f;i++){3 g=1D[i];2(g.2l){3 v=$.1d.1O&&!(g.3x[\'8\'].3y)?g.1w:g.8;2(e)6 v;a.A(v)}}6 a}6 b.8};$.z.1o=5(){6 4.J(5(){$(\'Y,K,1v\',4).2m()})};$.z.2m=$.z.3z=5(){6 4.J(5(){3 t=4.E,16=4.1f.1y();2(t==\'1w\'||t==\'3A\'||16==\'1v\')4.8=\'\';C 2(t==\'1z\'||t==\'1A\')4.1B=G;C 2(16==\'K\')4.1C=-1})};$.z.1n=5(){6 4.J(5(){2(L 4.19==\'5\'||(L 4.19==\'3B\'&&!4.19.3C))4.19()})};$.z.3D=5(b){2(b==V)b=T;6 4.J(5(){4.1h=!b})};$.z.K=5(b){2(b==V)b=T;6 4.J(5(){3 t=4.E;2(t==\'1z\'||t==\'1A\')4.1B=b;C 2(4.1f.1y()==\'2n\'){3 a=$(4).3E(\'K\');2(b&&a[0]&&a[0].E==\'K-2k\'){a.3F(\'2n\').K(G)}4.2l=b}})}})(3G);',62,229,'||if|var|this|function|return|name|value|||||||||||||||||||||||||||fn|push|null|else|form|type|for|false|max|length|each|select|typeof|success|trigger|data|submit|doc|attr|callbacks|true|clk|undefined|dataType|target|input|fieldValue||responseText|cb|image|clk_x|clk_y|tag|responseXML|event|reset|url|action|extraData|browser|setTimeout|tagName|plugin|disabled|constructor|Array|window|formToArray|param|resetForm|clearForm|fileUpload|new|document|timeout|body|getElementsByTagName|textarea|text|continue|toLowerCase|checkbox|radio|checked|selectedIndex|ops|ajaxSubmit|extend|method|GET|veto|in|beforeSubmit|iframe|closeKeepAlive|opera|msie|top|1000px|left|getResponseHeader|active|multipart|try|appendTo|attachEvent|onload|load|remove|detachEvent|contentWindow|contentDocument|XMLDocument|xml|toXml|error|complete|ActiveXObject|documentElement|ajaxFormUnbind|bind|click|offsetX|offset|pageX|pageY|unbind|jmax|one|selected|clearFields|option|location|toString|pre|serialize|semantic|validate|toUpperCase|indexOf|html|arguments|file|safari|get|ajax|notify|ajaxSettings|jqFormIO|Date|getTime|id|version|src|javascript|write|css|position|absolute|status|statusText|getAllResponseHeaders|setRequestHeader|global|ajaxStart|ajaxSend|encoding|enctype|POST|hidden|addEventListener|finally|removeAttr|removeEventListener|throw|innerHTML|content|json|script|httpData|catch|handleError|ajaxSuccess|ajaxComplete|ajaxStop|100|Microsoft|XMLDOM|async|loadXML|DOMParser|parseFromString|parsererror|ajaxForm|offsetY|offsetLeft|offsetTop|elements|formSerialize|fieldSerialize|merge|button|options|attributes|specified|clearInputs|password|object|nodeType|enable|parent|find|jQuery'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
 (function($) {
/**
 * ajaxSubmit() provides a mechanism for submitting an HTML form using AJAX.
 *
 * ajaxSubmit accepts a single argument which can be either a success callback function
 * or an options Object.  If a function is provided it will be invoked upon successful
 * completion of the submit and will be passed the response from the server.
 * If an options Object is provided, the following attributes are supported:
 *
 *  target:   Identifies the element(s) in the page to be updated with the server response.
 *            This value may be specified as a jQuery selection string, a jQuery object,
 *            or a DOM element.
 *            default value: null
 *
 *  url:      URL to which the form data will be submitted.
 *            default value: value of form's 'action' attribute
 *
 *  type:     The method in which the form data should be submitted, 'GET' or 'POST'.
 *            default value: value of form's 'method' attribute (or 'GET' if none found)
 *
 *  data:     Additional data to add to the request, specified as key/value pairs (see $.ajax).
 *
 *  beforeSubmit:  Callback method to be invoked before the form is submitted.
 *            default value: null
 *
 *  success:  Callback method to be invoked after the form has been successfully submitted
 *            and the response has been returned from the server
 *            default value: null
 *
 *  dataType: Expected dataType of the response.  One of: null, 'xml', 'script', or 'json'
 *            default value: null
 *
 *  semantic: Boolean flag indicating whether data must be submitted in semantic order (slower).
 *            default value: false
 *
 *  resetForm: Boolean flag indicating whether the form should be reset if the submit is successful
 *
 *  clearForm: Boolean flag indicating whether the form should be cleared if the submit is successful
 *
 *
 * The 'beforeSubmit' callback can be provided as a hook for running pre-submit logic or for
 * validating the form data.  If the 'beforeSubmit' callback returns false then the form will
 * not be submitted. The 'beforeSubmit' callback is invoked with three arguments: the form data
 * in array format, the jQuery object, and the options object passed into ajaxSubmit.
 * The form data array takes the following form:
 *
 *     [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * If a 'success' callback method is provided it is invoked after the response has been returned
 * from the server.  It is passed the responseText or responseXML value (depending on dataType).
 * See jQuery.ajax for further details.
 *
 *
 * The dataType option provides a means for specifying how the server response should be handled.
 * This maps directly to the jQuery.httpData method.  The following values are supported:
 *
 *      'xml':    if dataType == 'xml' the server response is treated as XML and the 'success'
 *                   callback method, if specified, will be passed the responseXML value
 *      'json':   if dataType == 'json' the server response will be evaluted and passed to
 *                   the 'success' callback, if specified
 *      'script': if dataType == 'script' the server response is evaluated in the global context
 *
 *
 * Note that it does not make sense to use both the 'target' and 'dataType' options.  If both
 * are provided the target will be ignored.
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * This is normally true anyway, unless the form contains input elements of type='image'.
 * If your form must be submitted with name/value pairs in semantic order and your form
 * contains an input of type='image" then pass true for this arg, otherwise pass false
 * (or nothing) to avoid the overhead for this logic.
 *
 *
 * When used on its own, ajaxSubmit() is typically bound to a form's submit event like this:
 *
 * $("#form-id").submit(function() {
 *     $(this).ajaxSubmit(options);
 *     return false; // cancel conventional submit
 * });
 *
 * When using ajaxForm(), however, this is done for you.
 *
 * @example
 * $('#myForm').ajaxSubmit(function(data) {
 *     alert('Form submit succeeded! Server returned: ' + data);
 * });
 * @desc Submit form and alert server response
 *
 *
 * @example
 * var options = {
 *     target: '#myTargetDiv'
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Submit form and update page element with server response
 *
 *
 * @example
 * var options = {
 *     success: function(responseText) {
 *         alert(responseText);
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Submit form and alert the server response
 *
 *
 * @example
 * var options = {
 *     beforeSubmit: function(formArray, jqForm) {
 *         if (formArray.length == 0) {
 *             alert('Please enter data.');
 *             return false;
 *         }
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Pre-submit validation which aborts the submit operation if form data is empty
 *
 *
 * @example
 * var options = {
 *     url: myJsonUrl.php,
 *     dataType: 'json',
 *     success: function(data) {
 *        // 'data' is an object representing the the evaluated json data
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc json data returned and evaluated
 *
 *
 * @example
 * var options = {
 *     url: myXmlUrl.php,
 *     dataType: 'xml',
 *     success: function(responseXML) {
 *        // responseXML is XML document object
 *        var data = $('myElement', responseXML).text();
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc XML data returned from server
 *
 *
 * @example
 * var options = {
 *     resetForm: true
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc submit form and reset it if successful
 *
 * @example
 * $('#myForm).submit(function() {
 *    $(this).ajaxSubmit();
 *    return false;
 * });
 * @desc Bind form's submit event to use ajaxSubmit
 *
 *
 * @name ajaxSubmit
 * @type jQuery
 * @param options  object literal containing options which control the form submission process
 * @cat Plugins/Form
 * @return jQuery
 */
$.fn.ajaxSubmit = function(options) {
    if (typeof options == 'function')
        options = { success: options };

    options = $.extend({
        url:  this.attr('action') || window.location.toString(),
        type: this.attr('method') || 'GET'
    }, options || {});

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) return this;

    var a = this.formToArray(options.semantic);
    if (options.data) {
        options.extraData = options.data;
        for (var n in options.data)
            a.push( { name: n, value: options.data[n] } );
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) return this;

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) return this;

    var q = $.param(a);

    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else
        options.data = q; // data is the query string for 'post'

    var $form = this, callbacks = [];
    if (options.resetForm) callbacks.push(function() { $form.resetForm(); });
    if (options.clearForm) callbacks.push(function() { $form.clearForm(); });

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            $(options.target).html(data).each(oldSuccess, arguments);
        });
    }
    else if (options.success)
        callbacks.push(options.success);

    options.success = function(data, status) {
        for (var i=0, max=callbacks.length; i < max; i++)
            callbacks[i](data, status, $form);
    };

    // are there files to upload?
    var files = $('input:file', this).fieldValue();
    var found = false;
    for (var j=0; j < files.length; j++)
        if (files[j])
            found = true;

    // options.iframe allows user to force iframe mode
   if (options.iframe || found) { 
       // hack to fix Safari hang (thanks to Tim Molendijk for this)
       // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
       if ($.browser.safari && options.closeKeepAlive)
           $.get(options.closeKeepAlive, fileUpload);
       else
           fileUpload();
       }
   else
       $.ajax(options);

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;


    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUpload() {
        var form = $form[0];
        var opts = $.extend({}, $.ajaxSettings, options);

        var id = 'jqFormIO' + (new Date().getTime());
        var $io = $('<iframe id="' + id + '" name="' + id + '" />');
        var io = $io[0];
        var op8 = $.browser.opera && window.opera.version() < 9;
        if ($.browser.msie || op8) io.src = 'javascript:false;document.write("");';
        $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });

        var xhr = { // mock object
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {}
        };

        var g = opts.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && ! $.active++) $.event.trigger("ajaxStart");
        if (g) $.event.trigger("ajaxSend", [xhr, opts]);

        var cbInvoked = 0;
        var timedOut = 0;

        // take a breath so that pending repaints get some cpu time before the upload starts
        setTimeout(function() {
            // make sure form attrs are set
            var t = $form.attr('target'), a = $form.attr('action');
            $form.attr({
                target:   id,
                encoding: 'multipart/form-data',
                enctype:  'multipart/form-data',
                method:   'POST',
                action:   opts.url
            });

            // support timout
            if (opts.timeout)
                setTimeout(function() { timedOut = true; cb(); }, opts.timeout);

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (options.extraData)
                    for (var n in options.extraData)
                        extraInputs.push(
                            $('<input type="hidden" name="'+n+'" value="'+options.extraData[n]+'" />')
                                .appendTo(form)[0]);
            
                // add iframe to doc and submit the form
                $io.appendTo('body');
                io.attachEvent ? io.attachEvent('onload', cb) : io.addEventListener('load', cb, false);
                form.submit();
            }
            finally {
                // reset attrs and remove "extra" input elements
                $form.attr('action', a);
                t ? $form.attr('target', t) : $form.removeAttr('target');
                $(extraInputs).remove();
            }
        }, 10);

        function cb() {
            if (cbInvoked++) return;

            io.detachEvent ? io.detachEvent('onload', cb) : io.removeEventListener('load', cb, false);

            var ok = true;
            try {
                if (timedOut) throw 'timeout';
                // extract the server response from the iframe
                var data, doc;
                doc = io.contentWindow ? io.contentWindow.document : io.contentDocument ? io.contentDocument : io.document;
                xhr.responseText = doc.body ? doc.body.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': opts.dataType};
                    return headers[header];
                };

                if (opts.dataType == 'json' || opts.dataType == 'script') {
                    var ta = doc.getElementsByTagName('textarea')[0];
                    xhr.responseText = ta ? ta.value : xhr.responseText;
                }
                else if (opts.dataType == 'xml' && !xhr.responseXML && xhr.responseText != null) {
                    xhr.responseXML = toXml(xhr.responseText);
                }
                data = $.httpData(xhr, opts.dataType);
            }
            catch(e){
                ok = false;
                $.handleError(opts, xhr, 'error', e);
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (ok) {
                opts.success(data, 'success');
                if (g) $.event.trigger("ajaxSuccess", [xhr, opts]);
            }
            if (g) $.event.trigger("ajaxComplete", [xhr, opts]);
            if (g && ! --$.active) $.event.trigger("ajaxStop");
            if (opts.complete) opts.complete(xhr, ok ? 'success' : 'error');

            // clean up
            setTimeout(function() {
                $io.remove();
                xhr.responseXML = null;
            }, 100);
        };

        function toXml(s, doc) {
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            return (doc && doc.documentElement && doc.documentElement.tagName != 'parsererror') ? doc : null;
        };
    };
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * Note that for accurate x/y coordinates of image submit elements in all browsers
 * you need to also use the "dimensions" plugin (this method will auto-detect its presence).
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.  See ajaxSubmit for a full description of the options argument.
 *
 *
 * @example
 * var options = {
 *     target: '#myTargetDiv'
 * };
 * $('#myForm').ajaxSForm(options);
 * @desc Bind form's submit event so that 'myTargetDiv' is updated with the server response
 *       when the form is submitted.
 *
 *
 * @example
 * var options = {
 *     success: function(responseText) {
 *         alert(responseText);
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Bind form's submit event so that server response is alerted after the form is submitted.
 *
 *
 * @example
 * var options = {
 *     beforeSubmit: function(formArray, jqForm) {
 *         if (formArray.length == 0) {
 *             alert('Please enter data.');
 *             return false;
 *         }
 *     }
 * };
 * $('#myForm').ajaxSubmit(options);
 * @desc Bind form's submit event so that pre-submit callback is invoked before the form
 *       is submitted.
 *
 *
 * @name   ajaxForm
 * @param  options  object literal containing options which control the form submission process
 * @return jQuery
 * @cat    Plugins/Form
 * @type   jQuery
 */
$.fn.ajaxForm = function(options) {
    return this.ajaxFormUnbind().bind('submit.form-plugin',function() {
        $(this).ajaxSubmit(options);
        return false;
    }).each(function() {
        // store options in hash
        $(":submit,input:image", this).bind('click.form-plugin',function(e) {
            var $form = this.form;
            $form.clk = this;
            if (this.type == 'image') {
                if (e.offsetX != undefined) {
                    $form.clk_x = e.offsetX;
                    $form.clk_y = e.offsetY;
                } else if (typeof $.fn.offset == 'function') { // try to use dimensions plugin
                    var offset = $(this).offset();
                    $form.clk_x = e.pageX - offset.left;
                    $form.clk_y = e.pageY - offset.top;
                } else {
                    $form.clk_x = e.pageX - this.offsetLeft;
                    $form.clk_y = e.pageY - this.offsetTop;
                }
            }
            // clear form vars
            setTimeout(function() { $form.clk = $form.clk_x = $form.clk_y = null; }, 10);
        });
    });
};


/**
 * ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
 *
 * @name   ajaxFormUnbind
 * @return jQuery
 * @cat    Plugins/Form
 * @type   jQuery
 */
$.fn.ajaxFormUnbind = function() {
    this.unbind('submit.form-plugin');
    return this.each(function() {
        $(":submit,input:image", this).unbind('click.form-plugin');
    });

};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * This is normally true anyway, unless the form contains input elements of type='image'.
 * If your form must be submitted with name/value pairs in semantic order and your form
 * contains an input of type='image" then pass true for this arg, otherwise pass false
 * (or nothing) to avoid the overhead for this logic.
 *
 * @example var data = $("#myForm").formToArray();
 * $.post( "myscript.cgi", data );
 * @desc Collect all the data from a form and submit it to the server.
 *
 * @name formToArray
 * @param semantic true if serialization must maintain strict semantic ordering of elements (slower)
 * @type Array<Object>
 * @cat Plugins/Form
 */
$.fn.formToArray = function(semantic) {
    var a = [];
    if (this.length == 0) return a;

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) return a;
    for(var i=0, max=els.length; i < max; i++) {
        var el = els[i];
        var n = el.name;
        if (!n) continue;

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(!el.disabled && form.clk == el)
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            continue;
        }

        var v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            for(var j=0, jmax=v.length; j < jmax; j++)
                a.push({name: n, value: v[j]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: n, value: v});
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle them here
        var inputs = form.getElementsByTagName("input");
        for(var i=0, max=inputs.length; i < max; i++) {
            var input = inputs[i];
            var n = input.name;
            if(n && !input.disabled && input.type == "image" && form.clk == input)
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};


/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 *
 * The semantic argument can be used to force form serialization in semantic order.
 * If your form must be submitted with name/value pairs in semantic order then pass
 * true for this arg, otherwise pass false (or nothing) to avoid the overhead for
 * this logic (which can be significant for very large forms).
 *
 * @example var data = $("#myForm").formSerialize();
 * $.ajax('POST', "myscript.cgi", data);
 * @desc Collect all the data from a form into a single string
 *
 * @name formSerialize
 * @param semantic true if serialization must maintain strict semantic ordering of elements (slower)
 * @type String
 * @cat Plugins/Form
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};


/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 *
 * The successful argument controls whether or not serialization is limited to
 * 'successful' controls (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.
 *
 * @example var data = $("input").fieldSerialize();
 * @desc Collect the data from all successful input elements into a query string
 *
 * @example var data = $(":radio").fieldSerialize();
 * @desc Collect the data from all successful radio input elements into a query string
 *
 * @example var data = $("#myForm :checkbox").fieldSerialize();
 * @desc Collect the data from all successful checkbox input elements in myForm into a query string
 *
 * @example var data = $("#myForm :checkbox").fieldSerialize(false);
 * @desc Collect the data from all checkbox elements in myForm (even the unchecked ones) into a query string
 *
 * @example var data = $(":input").fieldSerialize();
 * @desc Collect the data from all successful input, select, textarea and button elements into a query string
 *
 * @name fieldSerialize
 * @param successful true if only successful controls should be serialized (default is true)
 * @type String
 * @cat Plugins/Form
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) return;
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++)
                a.push({name: n, value: v[i]});
        }
        else if (v !== null && typeof v != 'undefined')
            a.push({name: this.name, value: v});
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};


/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $(':text').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $(':checkbox').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $(':radio').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *       array will be empty, otherwise it will contain one or more values.
 *
 * @example var data = $("#myPasswordElement").fieldValue();
 * alert(data[0]);
 * @desc Alerts the current value of the myPasswordElement element
 *
 * @example var data = $("#myForm :input").fieldValue();
 * @desc Get the value(s) of the form elements in myForm
 *
 * @example var data = $("#myForm :checkbox").fieldValue();
 * @desc Get the value(s) for the successful checkbox element(s) in the jQuery object.
 *
 * @example var data = $("#mySingleSelect").fieldValue();
 * @desc Get the value(s) of the select control
 *
 * @example var data = $(':text').fieldValue();
 * @desc Get the value(s) of the text input or textarea elements
 *
 * @example var data = $("#myMultiSelect").fieldValue();
 * @desc Get the values for the select-multiple control
 *
 * @name fieldValue
 * @param Boolean successful true if only the values for successful controls should be returned (default is true)
 * @type Array<String>
 * @cat Plugins/Form
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length))
            continue;
        v.constructor == Array ? $.merge(val, v) : val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If the given element is not
 * successful and the successful arg is not false then the returned value will be null.
 *
 * Note: If the successful flag is true (default) but the element is not successful, the return will be null
 * Note: The value returned for a successful select-multiple element will always be an array.
 * Note: If the element has no value the return value will be undefined.
 *
 * @example var data = jQuery.fieldValue($("#myPasswordElement")[0]);
 * @desc Gets the current value of the myPasswordElement element
 *
 * @name fieldValue
 * @param Element el The DOM element for which the value will be returned
 * @param Boolean successful true if value returned must be for a successful controls (default is true)
 * @type String or Array<String> or null or undefined
 * @cat Plugins/Form
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (typeof successful == 'undefined') successful = true;

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1))
            return null;

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) return null;
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                // extra pain for IE...
                var v = $.browser.msie && !(op.attributes['value'].specified) ? op.text : op.value;
                if (one) return v;
                a.push(v);
            }
        }
        return a;
    }
    return el.value;
};


/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 *
 * @example $('form').clearForm();
 * @desc Clears all forms on the page.
 *
 * @name clearForm
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.clearForm = function() {
    return this.each(function() {
        $('input,select,textarea', this).clearFields();
    });
};

/**
 * Clears the selected form elements.  Takes the following actions on the matched elements:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 *
 * @example $('.myInputs').clearFields();
 * @desc Clears all inputs with class myInputs
 *
 * @name clearFields
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (t == 'text' || t == 'password' || tag == 'textarea')
            this.value = '';
        else if (t == 'checkbox' || t == 'radio')
            this.checked = false;
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
};


/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 *
 * @example $('form').resetForm();
 * @desc Resets all forms on the page.
 *
 * @name resetForm
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType))
            this.reset();
    });
};


/**
 * Enables or disables any matching elements.
 *
 * @example $(':radio').enabled(false);
 * @desc Disables all radio buttons
 *
 * @name select
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.enable = function(b) { 
    if (b == undefined) b = true;
    return this.each(function() { 
        this.disabled = !b 
    });
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 *
 * @example $(':checkbox').select();
 * @desc Checks all checkboxes
 *
 * @name select
 * @type jQuery
 * @cat Plugins/Form
 */
$.fn.select = function(select) {
    if (select == undefined) select = true;
    return this.each(function() { 
        var t = this.type;
        if (t == 'checkbox' || t == 'radio')
            this.checked = select;
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').select(false);
            }
            this.selected = select;
        }
    });
};

})(jQuery);
/*<?php
}
?>*/
