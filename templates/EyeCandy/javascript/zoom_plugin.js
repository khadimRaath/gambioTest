/* <?php
#   --------------------------------------------------------------
#   zoom_plugin.js 2012-03-13 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(3($){$.1o.6=3(){j c=Q,m,n,s,t,9=$(c).k(\'l\'),d=0,f=0,D=9.E().o,F=9.E().p,G=$(c).1p(),W=G.u(),X=G.E().o,Y=G.E().p,H,v,w,z=0,A=0,2=Z,h=Z,q,r;$.1q(10(9)).1r(3(){$("#B").I();$("#R").I();d=9.u();f=9.J();g($.11.1s){g($.11.1t<=7){d+=4;f+=4}}$(9).12("1u",3(e){m=e.13;n=e.14;g($("#B").15==0){j a=16 17(),S=16 17();2=$("<K></K>").L("18","R");2.u(8.6.19);2.J(8.6.1a);2.i("o",X+W+8.6.1v);2.i("p",Y);$(a).T(3(){2.M(Q);U()});$(S).T(3(){2.M(Q);U()});$(a).1w(3(){S.N=$(9).L("N").1b("/1c/","/1x/")});a.N=$(9).L("N").1b("/1c/","/1y/")}})});3 U(){$("1z").M(2);$(1d).12("1e",3(e){m=e.13;n=e.14;g(m<D||m>D+d||n<F||n>F+f){$(1d).1A("1e");1f(H);$("#B").I();$("#R").I()}O{V()}});v=$(2).k("l").u();w=$(2).k("l").J();q=1g.1h(8.6.19/v*d);r=1g.1h(8.6.1a/w*f);h=$("<K></K>").L("18","B");h.u(q);h.J(r);$(c).M(h);V();j a=((s/d)*v)>>0,C=((t/f)*w)>>0;z=a;A=C;$(2).k("l").i("o",-a);$(2).k("l").i("p",-C);2.1i({P:1,1j:"1k(P=1B)"},8.6.1l);h.1i({P:0.5,1j:"1k(P=1C)"},8.6.1l);H=1D(1m,8.6.1E)}3 10(a){j b=$.1F();$(a).T(3(){b.1G()});1H b}3 1m(){g($("#B").15>0){h.i("o",s);h.i("p",t);j a=((s/d)*v)>>0,C=((t/f)*w)>>0;z+=(a-z)/8.6.1n;A+=(C-A)/8.6.1n;$(2).k("l").i("o",-z);$(2).k("l").i("p",-A)}O{1f(H)}}3 V(){j x=(m-D-(q*0.5))>>0;g(x<0){x=0}O g(x>(d-q)){x=(d-q)}s=x;j y=(n-F-(r*0.5))>>0;g(y<0){y=0}O g(y>(f-r)){y=(f-r)}t=y}}})(1I);',62,107,'||zoom_window|function|||zoom_plugin||js_options|small_image||||small_image_width||small_image_height|if|zoom_pointer|css|var|find|img|mouse_pos_x|mouse_pos_y|left|top|zoom_pointer_width|zoom_pointer_height|pointer_pos_x|pointer_pos_y|width|zoom_image_width|zoom_image_height|||zoom_image_x_pos|zoom_image_y_pos|zoomPointer|destV|small_image_x_pos|offset|small_image_y_pos|small_image_container|interval|remove|height|div|attr|append|src|else|opacity|this|zoomWindow|zoom_image_popup|load|update_interval|getMousePosition|small_image_container_width|small_image_container_x_pos|small_image_container_y_pos|null|loadImg|browser|bind|pageX|pageY|length|new|Image|id|preview_image_width|preview_image_height|replace|info_images|document|mousemove|clearInterval|Math|ceil|animate|filter|alpha|fade_in_time|update_view|slow_move_factor|fn|parent|when|then|msie|version|mouseenter|image_distance|error|popup_images|original_images|body|unbind|100|50|setInterval|update_interval_time|Deferred|resolve|return|jQuery'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
(function($){
    $.fn.zoom_plugin = function(){         

        // zoomable image
        var self = this,
        mouse_pos_x,
        mouse_pos_y,
        pointer_pos_x,
        pointer_pos_y,
        small_image = $(self).find('img'),
        small_image_width = 0,
        small_image_height = 0,
        small_image_x_pos = small_image.offset().left,
        small_image_y_pos = small_image.offset().top,
        small_image_container = $(self).parent(),
        small_image_container_width = small_image_container.width(),
        small_image_container_x_pos = small_image_container.offset().left,
        small_image_container_y_pos = small_image_container.offset().top,
        interval,
        zoom_image_width,
        zoom_image_height,
        zoom_image_x_pos = 0,
        zoom_image_y_pos = 0,
        zoom_window = null,
        zoom_pointer = null,
        zoom_pointer_width,
        zoom_pointer_height;

            $.when(loadImg(small_image)).then(function() {
                    $("#zoomPointer").remove();
                    $("#zoomWindow").remove();
                                      
                    small_image_width = small_image.width();
                    small_image_height = small_image.height();
                    
                    if($.browser.msie){
                            if($.browser.version <= 7){
                                    small_image_width += 4;
                                    small_image_height += 4;
                            }
                    }

                    $(small_image).bind("mouseenter", function(e){
                            mouse_pos_x = e.pageX;
                            mouse_pos_y = e.pageY;
                               
                            if($("#zoomPointer").length == 0){
                                var zoom_image_original = new Image();
                                var zoom_image_popup = new Image();
                                
                                zoom_window = $("<div></div>").attr("id", "zoomWindow");
                                zoom_window.width(js_options.zoom_plugin.preview_image_width);
                                zoom_window.height(js_options.zoom_plugin.preview_image_height);
                                zoom_window.css("left", small_image_container_x_pos + small_image_container_width + js_options.zoom_plugin.image_distance);
                                zoom_window.css("top", small_image_container_y_pos);
                                
                                $(zoom_image_original).load(function(){
                                    zoom_window.append(this);
                                    update_interval();
                                });
                                
                                $(zoom_image_popup).load(function(){
                                    zoom_window.append(this);
                                    update_interval();
                                });
                                
                                $(zoom_image_original).error(function(){
                                    zoom_image_popup.src = $(small_image).attr("src").replace("/info_images/", "/popup_images/");
                                });
                                
                                zoom_image_original.src = $(small_image).attr("src").replace("/info_images/", "/original_images/");
                            }
                    });
            });	

            function update_interval(){
                $("body").append(zoom_window);
                
                $(document).bind("mousemove", function(e){
                        mouse_pos_x = e.pageX;
                        mouse_pos_y = e.pageY;                                   

                        if(mouse_pos_x < small_image_x_pos || mouse_pos_x > small_image_x_pos + small_image_width || mouse_pos_y < small_image_y_pos || mouse_pos_y > small_image_y_pos + small_image_height){
                                $(document).unbind("mousemove");
                                clearInterval(interval);
                                $("#zoomPointer").remove();
                                $("#zoomWindow").remove();
                        }else{
                                getMousePosition();
                        }
                });

                zoom_image_width = $(zoom_window).find("img").width();
                zoom_image_height = $(zoom_window).find("img").height();

                zoom_pointer_width = Math.ceil(js_options.zoom_plugin.preview_image_width / zoom_image_width * small_image_width);
                zoom_pointer_height = Math.ceil(js_options.zoom_plugin.preview_image_height / zoom_image_height * small_image_height);

                zoom_pointer = $("<div></div>").attr("id", "zoomPointer");
                zoom_pointer.width(zoom_pointer_width);
                zoom_pointer.height(zoom_pointer_height);

                $(self).append(zoom_pointer);

                getMousePosition();

                var destU = ((pointer_pos_x / small_image_width) * zoom_image_width) >> 0;
                var destV = ((pointer_pos_y / small_image_height) * zoom_image_height) >> 0;

                zoom_image_x_pos = destU;
                zoom_image_y_pos = destV;

                $(zoom_window).find("img").css("left", -destU);
                $(zoom_window).find("img").css("top", -destV);

                zoom_window.animate({opacity: 1,
                                                            filter: "alpha(opacity=100)"}, js_options.zoom_plugin.fade_in_time);

                zoom_pointer.animate({opacity: 0.5,
                                                            filter: "alpha(opacity=50)"}, js_options.zoom_plugin.fade_in_time);

                interval = setInterval(update_view, js_options.zoom_plugin.update_interval_time);
            }

            function loadImg(selector) {
                    var dfd = $.Deferred();
                    $(selector).load(function() { dfd.resolve(); });
                    return dfd;
            }

            function update_view(){
                    if($("#zoomPointer").length > 0){

                            zoom_pointer.css("left", pointer_pos_x);
                            zoom_pointer.css("top", pointer_pos_y);

                            var destU = ((pointer_pos_x / small_image_width) * zoom_image_width) >> 0;
                            var destV = ((pointer_pos_y / small_image_height) * zoom_image_height) >> 0;

                            zoom_image_x_pos += (destU - zoom_image_x_pos) / js_options.zoom_plugin.slow_move_factor;
                            zoom_image_y_pos += (destV - zoom_image_y_pos) / js_options.zoom_plugin.slow_move_factor;

                            $(zoom_window).find("img").css("left", -zoom_image_x_pos);
                            $(zoom_window).find("img").css("top", -zoom_image_y_pos);

                        }else{
                                clearInterval(interval);
                        }				
            }

            function getMousePosition(){
              var x = (mouse_pos_x - small_image_x_pos - (zoom_pointer_width * 0.5)) >> 0;			 
              if (x < 0) {
                  x = 0;
              }
              else if (x > (small_image_width - zoom_pointer_width)) {
                  x = (small_image_width - zoom_pointer_width);
              }
              pointer_pos_x = x;
            
              var y = (mouse_pos_y - small_image_y_pos - (zoom_pointer_height * 0.5)) >> 0;
              if (y < 0) {
                  y = 0;
              }
              else if (y > (small_image_height - zoom_pointer_height)) {
                  y = (small_image_height - zoom_pointer_height);
              }
              pointer_pos_y = y;
            }
         
    };  
})(jQuery);
/*<?php
}
?>*/
