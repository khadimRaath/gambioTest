/*
 * JQZoom Evolution 1.0.1 - Javascript Image magnifier
 *
 * Copyright (c) Engineer Renzi Marco(www.mind-projects.it)
 *
 * $Date: 12-12-2008
 *
 *	ChangeLog:
 *  
 * $License : GPL,so any change to the code you should copy and paste this section,and would be nice to report this to me(renzi.mrc@gmail.com).
 */
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(9($){$.30.31=9(G){I H={17:\'32\',18:2l,19:2l,1a:10,1b:0,Q:"2m",2n:1s,2o:13,2p:0.3,14:1s,1p:13,2q:\'1g\',2r:\'23\',2s:\'24\',2t:\'33\',2u:13,2v:1s,2w:\'34 35\',2x:\'1t\'};G=G||{};$.36(H,G);T 4.37(9(){I a=$(4);I d=a.1q(\'14\');$(a).1Q(\'14\');$(a).J(\'38-K\',\'1r\');$(a).J(\'39-3a\',\'1r\');I f=$(a).1q(\'3b\');I g=$("1H",4);I j=g.1q(\'14\');g.1Q(\'14\');I k=U 25(g);I l={};I m=0;I n=0;I p=1u;p=U 1R();I q=(26(d).Y>0)?d:(26(j).Y>0)?j:1u;I r=U 27();I s=U 1v(a[0].2y);I t=U 1c();I u={};I v=13;I y={};I z=1u;I A=13;I B={};I C=0;I D=13;I E=13;I F=13;k.1I();$(4).3c(9(e){B.x=e.1w;B.y=e.1x;k.1S();1d()},9(){k.1S();2z()});6(H.1p){2A(9(){1d()},3d)}9 1d(){6(!A){k.28();A=1s;j=g.1q(\'14\');g.1Q(\'14\');d=a.1q(\'14\');$(a).1Q(\'14\');s=U 1v(a[0].2y);6(!v||$.1e.2B){s.1I()}V{6(H.17!=\'1j\'){z=U 1J();z.1d()}t=U 1c;t.1d()}a[0].3e();T 13}};9 2z(){6(H.17==\'1K\'&&!H.1p){g.J({\'1T\':1})}6(!H.1p){A=13;v=13;$(t.5).29(\'1L\');t.Z();6($(\'P.1M\').Y>0){z.Z()}6($(\'P.2a\').Y>0){r.Z()}g.1q(\'14\',j);a.1q(\'14\',d);$().29();a.29(\'1L\');C=0;6(1y(\'.2b\').Y>0){1y(\'.2b\').Z()}}V{6(H.2o){1k(H.17){11\'1j\':s.2c();N;1l:t.1t();N}}}6(H.1p){1d()}};9 25(c){4.5=c[0];4.1I=9(){4.5.1m=c[0].1m};4.28=9(){I a=\'\';a=$(g).J(\'2C-L-W\');m=\'\';I b=\'\';b=$(g).J(\'2C-M-W\');n=\'\';6(a){1U(i=0;i<3;i++){I x=[];x=a.1n(i,1);6(1V(x)==13){m=m+\'\'+a.1n(i,1)}V{N}}}6(b){1U(i=0;i<3;i++){6(!1V(b.1n(i,1))){n=n+b.1n(i,1)}V{N}}}m=(m.Y>0)?1W(m):0;n=(n.Y>0)?1W(n):0};4.5.2D=9(){a.J({\'2E\':\'2F\',\'1h\':\'1X\'});6(a.J(\'Q\')!=\'15\'&&a.2d().J(\'Q\')){a.J({\'2E\':\'2F\',\'Q\':\'2G\',\'1h\':\'1X\'})}6(a.2d().J(\'Q\')!=\'15\'){a.2d().J(\'Q\',\'2G\')}V{}6($.1e.2B||$.1e.3f){$(g).J({Q:\'15\',L:\'2H\',M:\'2H\'})}l.w=$(4).W();l.h=$(4).1f();l.8=$(4).1i();l.8.l=$(4).1i().M;l.8.t=$(4).1i().L;l.8.r=l.w+l.8.l;l.8.b=l.h+l.8.t;a.1f(l.h);a.W(l.w);6(H.2u){k.1S();s.1I()}};T 4};25.12.1S=9(){l.8=$(g).1i();l.8.l=$(g).1i().M;l.8.t=$(g).1i().L;l.8.r=l.w+l.8.l;l.8.b=l.h+l.8.t};9 1c(){4.5=16.2e("P");$(4.5).1Y(\'X\');4.5.3g=9(){$(t.5).Z();t=U 1c();t.1d()};4.2I=9(){1k(H.17){11\'1K\':4.1z=U 1Z();4.1z.1m=k.5.1m;4.5.1N(4.1z);$(4.5).J({\'1T\':1});N;11\'1j\':4.1z=U 1Z();4.1z.1m=s.5.1m;4.5.1N(4.1z);$(4.5).J({\'1T\':1});N;1l:N}1k(H.17){11\'1j\':u.w=l.w;u.h=l.h;N;1l:u.w=(H.18)/y.x;u.h=(H.19)/y.y;N}$(4.5).J({W:u.w+\'R\',1f:u.h+\'R\',Q:\'15\',1h:\'1r\',3h:1+\'R\'});a.3i(4.5)};T 4};1c.12.1d=9(){4.2I();1k(H.17){11\'1K\':g.J({\'1T\':H.2p});(H.1p)?t.1t():t.1o(1u);a.2f(\'1L\',9(e){B.x=e.1w;B.y=e.1x;t.1o(e)});N;11\'1j\':$(4.5).J({L:0,M:0});6(H.14){r.2g()}s.2c();a.2f(\'1L\',9(e){B.x=e.1w;B.y=e.1x;s.2J(e)});N;1l:(H.1p)?t.1t():t.1o(1u);$(a).2f(\'1L\',9(e){B.x=e.1w;B.y=e.1x;t.1o(e)});N}T 4};1c.12.1o=9(e){6(e){B.x=e.1w;B.y=e.1x}6(C==0){I b=(l.w)/2-(u.w)/2;I c=(l.h)/2-(u.h)/2;$(\'P.X\').1g();6(H.2n){4.5.K.20=\'2K\'}V{4.5.K.20=\'2h\';$(\'P.X\').23()}C=1}V{I b=B.x-l.8.l-(u.w)/2;I c=B.y-l.8.t-(u.h)/2}6(2L()){b=0+n}V 6(2M()){6($.1e.1O&&$.1e.2i<7){b=l.w-u.w+n-1}V{b=l.w-u.w+n-1}}6(2N()){c=0+m}V 6(2O()){6($.1e.1O&&$.1e.2i<7){c=l.h-u.h+m-1}V{c=l.h-u.h-1+m}}b=1A(b);c=1A(c);$(\'P.X\',a).J({L:c,M:b});6(H.17==\'1K\'){$(\'P.X 1H\',a).J({\'Q\':\'15\',\'L\':-(c-m+1),\'M\':-(b-n+1)})}4.5.K.M=b+\'R\';4.5.K.L=c+\'R\';s.1o();9 2L(){T B.x-(u.w+2*1)/2-n<l.8.l}9 2M(){T B.x+(u.w+2*1)/2>l.8.r+n}9 2N(){T B.y-(u.h+2*1)/2-m<l.8.t}9 2O(){T B.y+(u.h+2*1)/2>l.8.b+m}T 4};1c.12.1t=9(){$(\'P.X\',a).J(\'1h\',\'1r\');I b=(l.w)/2-(u.w)/2;I c=(l.h)/2-(u.h)/2;4.5.K.M=b+\'R\';4.5.K.L=c+\'R\';$(\'P.X\',a).J({L:c,M:b});6(H.17==\'1K\'){$(\'P.X 1H\',a).J({\'Q\':\'15\',\'L\':-(c-m+1),\'M\':-(b-n+1)})}s.1o();6($.1e.1O){$(\'P.X\',a).1g()}V{2A(9(){$(\'P.X\').2P(\'24\')},10)}};1c.12.1P=9(){I o={};o.M=1A(4.5.K.M);o.L=1A(4.5.K.L);T o};1c.12.Z=9(){6(H.17==\'1j\'){$(\'P.X\',a).2Q(\'24\',9(){$(4).Z()})}V{$(\'P.X\',a).Z()}};1c.12.28=9(){I a=\'\';a=$(\'P.X\').J(\'3j\');1B=\'\';I b=\'\';b=$(\'P.X\').J(\'3k\');1C=\'\';6($.1e.1O){I c=a.2R(\' \');a=c[1];I c=b.2R(\' \');b=c[1]}6(a){1U(i=0;i<3;i++){I x=[];x=a.1n(i,1);6(1V(x)==13){1B=1B+\'\'+a.1n(i,1)}V{N}}}6(b){1U(i=0;i<3;i++){6(!1V(b.1n(i,1))){1C=1C+b.1n(i,1)}V{N}}}1B=(1B.Y>0)?1W(1B):0;1C=(1C.Y>0)?1W(1C):0};9 1v(a){4.2S=a;4.5=U 1Z();4.1I=9(){6(!4.5)4.5=U 1Z();4.5.K.Q=\'15\';4.5.K.1h=\'1r\';4.5.K.M=\'-3l\';4.5.K.L=\'3m\';p=U 1R();6(H.2v&&!D){p.1g();D=1s}16.2j.1N(4.5);4.5.1m=4.2S};4.5.2D=9(){4.K.1h=\'1X\';I w=O.21($(4).W());I h=O.21($(4).1f());4.K.1h=\'1r\';y.x=(w/l.w);y.y=(h/l.h);6($(\'P.1D\').Y>0){$(\'P.1D\').Z()}v=1s;6(H.17!=\'1j\'&&A){z=U 1J();z.1d()}6(A){t=U 1c();t.1d()}6($(\'P.1D\').Y>0){$(\'P.1D\').Z()}};T 4};1v.12.1o=9(){4.5.K.M=O.1E(-y.x*1A(t.1P().M)+n)+\'R\';4.5.K.L=O.1E(-y.y*1A(t.1P().L)+m)+\'R\'};1v.12.2J=9(e){4.5.K.M=O.1E(-y.x*O.S(e.1w-l.8.l))+\'R\';4.5.K.L=O.1E(-y.y*O.S(e.1x-l.8.t))+\'R\';$(\'P.X 1H\',a).J({\'Q\':\'15\',\'L\':4.5.K.L,\'M\':4.5.K.M})};1v.12.2c=9(){4.5.K.M=O.1E(-y.x*O.S((l.w)/2))+\'R\';4.5.K.L=O.1E(-y.y*O.S((l.h)/2))+\'R\';$(\'P.X 1H\',a).J({\'Q\':\'15\',\'L\':4.5.K.L,\'M\':4.5.K.M})};9 1J(){I a=1y(g).1i().M;I b=1y(g).1i().L;4.5=16.2e("P");$(4.5).1Y(\'1M\');$(4.5).J({Q:\'15\',W:O.21(H.18)+\'R\',1f:O.21(H.19)+\'R\',1h:\'1r\',2T:3n,3o:\'2h\'});1k(H.Q){11"2m":a=(a+$(g).W()+O.S(H.1a)+H.18<$(16).W())?(a+$(g).W()+O.S(H.1a)):(a-H.18-10);1F=b+H.1b+H.19;b=(1F<$(16).1f()&&1F>0)?b+H.1b:b;N;11"M":a=(l.8.l-O.S(H.1a)-H.18>0)?(l.8.l-O.S(H.1a)-H.18):(l.8.l+l.w+10);1F=l.8.t+H.1b+H.19;b=(1F<$(16).1f()&&1F>0)?l.8.t+H.1b:l.8.t;N;11"L":b=(l.8.t-O.S(H.1b)-H.19>0)?(l.8.t-O.S(H.1b)-H.19):(l.8.t+l.h+10);1G=l.8.l+H.1a+H.18;a=(1G<$(16).W()&&1G>0)?l.8.l+H.1a:l.8.l;N;11"3p":b=(l.8.b+O.S(H.1b)+H.19<$(16).1f())?(l.8.b+O.S(H.1b)):(l.8.t-H.19-10);1G=l.8.l+H.1a+H.18;a=(1G<$(16).W()&&1G>0)?l.8.l+H.1a:l.8.l;N;1l:a=(l.8.l+l.w+H.1a+H.18<$(16).W())?(l.8.l+l.w+O.S(H.1a)):(l.8.l-H.18-O.S(H.1a));b=(l.8.b+O.S(H.1b)+H.19<$(16).1f())?(l.8.b+O.S(H.1b)):(l.8.t-H.19-O.S(H.1b));N}4.5.K.M=a+\'R\';4.5.K.L=b+\'R\';T 4};1J.12.1d=9(){6(!4.5.3q)4.5.1N(s.5);6(H.14){r.2g()}16.2j.1N(4.5);1k(H.2q){11\'1g\':$(4.5).1g();N;11\'3r\':$(4.5).2P(H.2s);N;1l:$(4.5).1g();N}$(4.5).1g();6($.1e.1O&&$.1e.2i<7){4.3s=$(\'<2U 3t="2b" 3u="3v" 3w="0"  1m="#"  K="3x-3y: 2V" 3z="2V"></2U>\').J({Q:"15",M:4.5.K.M,L:4.5.K.L,2T:3A,W:(H.18+2),1f:(H.19)}).3B(4.5)};s.5.K.1h=\'1X\'};1J.12.Z=9(){1k(H.2r){11\'23\':$(\'.1M\').Z();N;11\'3C\':$(\'.1M\').2Q(H.2t);N;1l:$(\'.1M\').Z();N}};9 27(){4.5=1y(\'<P />\').1Y(\'2a\').2W(\'\'+q+\'\');4.2g=9(){6(H.17==\'1j\'){$(4.5).J({Q:\'15\',L:l.8.b+3,M:(l.8.l+1),W:l.w}).2k(\'2j\')}V{$(4.5).2k(z.5)}}};27.12.Z=9(){$(\'.2a\').Z()};9 1R(){4.5=16.2e("P");$(4.5).1Y(\'1D\');$(4.5).2W(H.2w);$(4.5).2k(a).J(\'20\',\'2h\');4.1g=9(){1k(H.2x){11\'1t\':2X=(l.h-$(4.5).1f())/2;2Y=(l.w-$(4.5).W())/2;$(4.5).J({L:2X,M:2Y});N;1l:I a=4.1P();N}$(4.5).J({Q:\'15\',20:\'2K\'})};T 4};1R.12.1P=9(){I o=1u;o=$(\'P.1D\').1i();T o}})}})(1y);9 26(a){2Z(a.22(0,1)==\' \'){a=a.22(1,a.Y)}2Z(a.22(a.Y-1,a.Y)==\' \'){a=a.22(0,a.Y-1)}T a};',62,225,'||||this|node|if||pos|function|||||||||||||||||||||||||||||||||||var|css|style|top|left|break|Math|div|position|px|abs|return|new|else|width|jqZoomPup|length|remove||case|prototype|false|title|absolute|document|zoomType|zoomWidth|zoomHeight|xOffset|yOffset|Lens|activate|browser|height|show|display|offset|innerzoom|switch|default|src|substr|setposition|alwaysOn|attr|none|true|center|null|Largeimage|pageX|pageY|jQuery|image|parseInt|lensbtop|lensbleft|preload|ceil|topwindow|leftwindow|img|loadimage|Stage|reverse|mousemove|jqZoomWindow|appendChild|msie|getoffset|removeAttr|Loader|setpos|opacity|for|isNaN|eval|block|addClass|Image|visibility|round|substring|hide|fast|Smallimage|trim|zoomTitle|findborder|unbind|jqZoomTitle|zoom_ieframe|setcenter|parent|createElement|bind|loadtitle|hidden|version|body|appendTo|200|right|lens|lensReset|imageOpacity|showEffect|hideEffect|fadeinSpeed|fadeoutSpeed|preloadImages|showPreload|preloadText|preloadPosition|href|deactivate|setTimeout|safari|border|onload|cursor|pointer|relative|0px|loadlens|setinner|visible|overleft|overright|overtop|overbottom|fadeIn|fadeOut|split|url|zIndex|iframe|transparent|html|loadertop|loaderleft|while|fn|jqzoom|standard|slow|Loading|zoom|extend|each|outline|text|decoration|rel|hover|150|blur|opera|onerror|borderWidth|append|borderTop|borderLeft|5000px|10px|10000|overflow|bottom|firstChild|fadein|ieframe|class|name|content|frameborder|background|color|bgcolor|99|insertBefore|fadeout'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
(function ($) {
	$.fn.jqzoom = function (G) {
		var H = {
			zoomType: 'standard',
			zoomWidth: 200,
			zoomHeight: 200,
			xOffset: 10,
			yOffset: 0,
			position: "right",
			lens: true,
			lensReset: false,
			imageOpacity: 0.3,
			title: true,
			alwaysOn: false,
			showEffect: 'show',
			hideEffect: 'hide',
			fadeinSpeed: 'fast',
			fadeoutSpeed: 'slow',
			preloadImages: false,
			showPreload: true,
			preloadText: 'Loading zoom',
			preloadPosition: 'center'
		};
		G = G || {};
		$.extend(H, G);
		return this.each(function () {
			var a = $(this);
			var d = a.attr('title');
			$(a).removeAttr('title');
			$(a).css('outline-style', 'none');
			$(a).css('text-decoration', 'none');
			var f = $(a).attr('rel');
			var g = $("img", this);
			var j = g.attr('title');
			g.removeAttr('title');
			var k = new Smallimage(g);
			var l = {};
			var m = 0;
			var n = 0;
			var p = null;
			p = new Loader();
			var q = (trim(d).length > 0) ? d: (trim(j).length > 0) ? j: null;
			var r = new zoomTitle();
			var s = new Largeimage(a[0].href);
			var t = new Lens();
			var u = {};
			var v = false;
			var y = {};
			var z = null;
			var A = false;
			var B = {};
			var C = 0;
			var D = false;
			var E = false;
			var F = false;
			k.loadimage();
			$(this).hover(function (e) {
				B.x = e.pageX;
				B.y = e.pageY;
				k.setpos();
				activate()
			},
			function () {
				k.setpos();
				deactivate()
			});
			if (H.alwaysOn) {
				setTimeout(function () {
					activate()
				},
				150)
			}
			function activate() {
				if (!A) {
					k.findborder();
					A = true;
					j = g.attr('title');
					g.removeAttr('title');
					d = a.attr('title');
					$(a).removeAttr('title');
					s = new Largeimage(a[0].href);
					if (!v || $.browser.safari) {
						s.loadimage()
					} else {
						if (H.zoomType != 'innerzoom') {
							z = new Stage();
							z.activate()
						}
						t = new Lens;
						t.activate()
					}
					a[0].blur();
					return false
				}
			};
			function deactivate() {
				if (H.zoomType == 'reverse' && !H.alwaysOn) {
					g.css({
						'opacity': 1
					})
				}
				if (!H.alwaysOn) {
					A = false;
					v = false;
					$(t.node).unbind('mousemove');
					t.remove();
					if ($('div.jqZoomWindow').length > 0) {
						z.remove()
					}
					if ($('div.jqZoomTitle').length > 0) {
						r.remove()
					}
					g.attr('title', j);
					a.attr('title', d);
					$().unbind();
					a.unbind('mousemove');
					C = 0;
					if (jQuery('.zoom_ieframe').length > 0) {
						jQuery('.zoom_ieframe').remove()
					}
				} else {
					if (H.lensReset) {
						switch (H.zoomType) {
						case 'innerzoom':
							s.setcenter();
							break;
						default:
							t.center();
							break
						}
					}
				}
				if (H.alwaysOn) {
					activate()
				}
			};
			function Smallimage(c) {
				this.node = c[0];
				this.loadimage = function () {
					this.node.src = c[0].src
				};
				this.findborder = function () {
					var a = '';
					a = $(g).css('border-top-width');
					m = '';
					var b = '';
					b = $(g).css('border-left-width');
					n = '';
					if (a) {
						for (i = 0; i < 3; i++) {
							var x = [];
							x = a.substr(i, 1);
							if (isNaN(x) == false) {
								m = m + '' + a.substr(i, 1)
							} else {
								break
							}
						}
					}
					if (b) {
						for (i = 0; i < 3; i++) {
							if (!isNaN(b.substr(i, 1))) {
								n = n + b.substr(i, 1)
							} else {
								break
							}
						}
					}
					m = (m.length > 0) ? eval(m) : 0;
					n = (n.length > 0) ? eval(n) : 0
				};
				this.node.onload = function () {
					a.css({
						'cursor': 'pointer',
						'display': 'block'
					});
					if (a.css('position') != 'absolute' && a.parent().css('position')) {
						a.css({
							'cursor': 'pointer',
							'position': 'relative',
							'display': 'block'
						})
					}
					if (a.parent().css('position') != 'absolute') {
						a.parent().css('position', 'relative')
					} else {}
					if ($.browser.safari || $.browser.opera) {
						$(g).css({
							position: 'absolute',
							top: '0px',
							left: '0px'
						})
					}
					l.w = $(this).width();
					l.h = $(this).height();
					l.pos = $(this).offset();
					l.pos.l = $(this).offset().left;
					l.pos.t = $(this).offset().top;
					l.pos.r = l.w + l.pos.l;
					l.pos.b = l.h + l.pos.t;
					a.height(l.h);
					a.width(l.w);
					if (H.preloadImages) {
						k.setpos();
						s.loadimage()
					}
				};
				return this
			};
			Smallimage.prototype.setpos = function () {
				l.pos = $(g).offset();
				l.pos.l = $(g).offset().left;
				l.pos.t = $(g).offset().top;
				l.pos.r = l.w + l.pos.l;
				l.pos.b = l.h + l.pos.t
			};
			function Lens() {
				this.node = document.createElement("div");
				$(this.node).addClass('jqZoomPup');
				this.node.onerror = function () {
					$(t.node).remove();
					t = new Lens();
					t.activate()
				};
				this.loadlens = function () {
					switch (H.zoomType) {
					case 'reverse':
						this.image = new Image();
						this.image.src = k.node.src;
						this.node.appendChild(this.image);
						$(this.node).css({
							'opacity': 1
						});
						break;
					case 'innerzoom':
						this.image = new Image();
						this.image.src = s.node.src;
						this.node.appendChild(this.image);
						$(this.node).css({
							'opacity': 1
						});
						break;
					default:
						break
					}
					switch (H.zoomType) {
					case 'innerzoom':
						u.w = l.w;
						u.h = l.h;
						break;
					default:
						u.w = (H.zoomWidth) / y.x;
						u.h = (H.zoomHeight) / y.y;
						break
					}
					$(this.node).css({
						width: u.w + 'px',
						height: u.h + 'px',
						position: 'absolute',
						display: 'none',
						borderWidth: 1 + 'px'
					});
					a.append(this.node)
				};
				return this
			};
			Lens.prototype.activate = function () {
				this.loadlens();
				switch (H.zoomType) {
				case 'reverse':
					g.css({
						'opacity':
						H.imageOpacity
					});
					(H.alwaysOn) ? t.center() : t.setposition(null);
					a.bind('mousemove', function (e) {
						B.x = e.pageX;
						B.y = e.pageY;
						t.setposition(e)
					});
					break;
				case 'innerzoom':
					$(this.node).css({
						top:
						0,
						left: 0
					});
					if (H.title) {
						r.loadtitle()
					}
					s.setcenter();
					a.bind('mousemove', function (e) {
						B.x = e.pageX;
						B.y = e.pageY;
						s.setinner(e)
					});
					break;
				default:
					(H.alwaysOn) ? t.center() : t.setposition(null);
					$(a).bind('mousemove', function (e) {
						B.x = e.pageX;
						B.y = e.pageY;
						t.setposition(e)
					});
					break
				}
				return this
			};
			Lens.prototype.setposition = function (e) {
				if (e) {
					B.x = e.pageX;
					B.y = e.pageY
				}
				if (C == 0) {
					var b = (l.w) / 2 - (u.w) / 2;
					var c = (l.h) / 2 - (u.h) / 2;
					$('div.jqZoomPup').show();
					if (H.lens) {
						this.node.style.visibility = 'visible'
					} else {
						this.node.style.visibility = 'hidden';
						$('div.jqZoomPup').hide()
					}
					C = 1
				} else {
					var b = B.x - l.pos.l - (u.w) / 2;
					var c = B.y - l.pos.t - (u.h) / 2
				}
				if (overleft()) {
					b = 0 + n
				} else if (overright()) {
					if ($.browser.msie && $.browser.version < 7) {
						b = l.w - u.w + n - 1
					} else {
						b = l.w - u.w + n - 1
					}
				}
				if (overtop()) {
					c = 0 + m
				} else if (overbottom()) {
					if ($.browser.msie && $.browser.version < 7) {
						c = l.h - u.h + m - 1
					} else {
						c = l.h - u.h - 1 + m
					}
				}
				b = parseInt(b);
				c = parseInt(c);
				$('div.jqZoomPup', a).css({
					top: c,
					left: b
				});
				if (H.zoomType == 'reverse') {
					$('div.jqZoomPup img', a).css({
						'position': 'absolute',
						'top': -(c - m + 1),
						'left': -(b - n + 1)
					})
				}
				this.node.style.left = b + 'px';
				this.node.style.top = c + 'px';
				s.setposition();
				function overleft() {
					return B.x - (u.w + 2 * 1) / 2 - n < l.pos.l
				}
				function overright() {
					return B.x + (u.w + 2 * 1) / 2 > l.pos.r + n
				}
				function overtop() {
					return B.y - (u.h + 2 * 1) / 2 - m < l.pos.t
				}
				function overbottom() {
					return B.y + (u.h + 2 * 1) / 2 > l.pos.b + m
				}
				return this
			};
			Lens.prototype.center = function () {
				$('div.jqZoomPup', a).css('display', 'none');
				var b = (l.w) / 2 - (u.w) / 2;
				var c = (l.h) / 2 - (u.h) / 2;
				this.node.style.left = b + 'px';
				this.node.style.top = c + 'px';
				$('div.jqZoomPup', a).css({
					top: c,
					left: b
				});
				if (H.zoomType == 'reverse') {
					$('div.jqZoomPup img', a).css({
						'position': 'absolute',
						'top': -(c - m + 1),
						'left': -(b - n + 1)
					})
				}
				s.setposition();
				if ($.browser.msie) {
					$('div.jqZoomPup', a).show()
				} else {
					setTimeout(function () {
						$('div.jqZoomPup').fadeIn('fast')
					},
					10)
				}
			};
			Lens.prototype.getoffset = function () {
				var o = {};
				o.left = parseInt(this.node.style.left);
				o.top = parseInt(this.node.style.top);
				return o
			};
			Lens.prototype.remove = function () {
				if (H.zoomType == 'innerzoom') {
					$('div.jqZoomPup', a).fadeOut('fast', function () {
						$(this).remove()
					})
				} else {
					$('div.jqZoomPup', a).remove()
				}
			};
			Lens.prototype.findborder = function () {
				var a = '';
				a = $('div.jqZoomPup').css('borderTop');
				lensbtop = '';
				var b = '';
				b = $('div.jqZoomPup').css('borderLeft');
				lensbleft = '';
				if ($.browser.msie) {
					var c = a.split(' ');
					a = c[1];
					var c = b.split(' ');
					b = c[1]
				}
				if (a) {
					for (i = 0; i < 3; i++) {
						var x = [];
						x = a.substr(i, 1);
						if (isNaN(x) == false) {
							lensbtop = lensbtop + '' + a.substr(i, 1)
						} else {
							break
						}
					}
				}
				if (b) {
					for (i = 0; i < 3; i++) {
						if (!isNaN(b.substr(i, 1))) {
							lensbleft = lensbleft + b.substr(i, 1)
						} else {
							break
						}
					}
				}
				lensbtop = (lensbtop.length > 0) ? eval(lensbtop) : 0;
				lensbleft = (lensbleft.length > 0) ? eval(lensbleft) : 0
			};
			function Largeimage(a) {
				this.url = a;
				this.node = new Image();
				this.loadimage = function () {
					if (!this.node) this.node = new Image();
					this.node.style.position = 'absolute';
					this.node.style.display = 'none';
					this.node.style.left = '-5000px';
					this.node.style.top = '10px';
					p = new Loader();
					if (H.showPreload && !D) {
						p.show();
						D = true
					}
					document.body.appendChild(this.node);
					this.node.src = this.url
				};
				this.node.onload = function () {
					this.style.display = 'block';
					var w = Math.round($(this).width());
					var h = Math.round($(this).height());
					this.style.display = 'none';
					y.x = (w / l.w);
					y.y = (h / l.h);
					if ($('div.preload').length > 0) {
						$('div.preload').remove()
					}
					v = true;
					if (H.zoomType != 'innerzoom' && A) {
						z = new Stage();
						z.activate()
					}
					if (A) {
						t = new Lens();
						t.activate()
					}
					if ($('div.preload').length > 0) {
						$('div.preload').remove()
					}
				};
				return this
			};
			Largeimage.prototype.setposition = function () {
				this.node.style.left = Math.ceil( - y.x * parseInt(t.getoffset().left) + n) + 'px';
				this.node.style.top = Math.ceil( - y.y * parseInt(t.getoffset().top) + m) + 'px'
			};
			Largeimage.prototype.setinner = function (e) {
				this.node.style.left = Math.ceil( - y.x * Math.abs(e.pageX - l.pos.l)) + 'px';
				this.node.style.top = Math.ceil( - y.y * Math.abs(e.pageY - l.pos.t)) + 'px';
				$('div.jqZoomPup img', a).css({
					'position': 'absolute',
					'top': this.node.style.top,
					'left': this.node.style.left
				})
			};
			Largeimage.prototype.setcenter = function () {
				this.node.style.left = Math.ceil( - y.x * Math.abs((l.w) / 2)) + 'px';
				this.node.style.top = Math.ceil( - y.y * Math.abs((l.h) / 2)) + 'px';
				$('div.jqZoomPup img', a).css({
					'position': 'absolute',
					'top': this.node.style.top,
					'left': this.node.style.left
				})
			};
			function Stage() {
				var a = jQuery(g).offset().left;
				var b = jQuery(g).offset().top;
				this.node = document.createElement("div");
				$(this.node).addClass('jqZoomWindow');
				$(this.node).css({
					position: 'absolute',
					width: Math.round(H.zoomWidth) + 'px',
					height: Math.round(H.zoomHeight) + 'px',
					display: 'none',
					zIndex: 10000,
					overflow: 'hidden'
				});
				switch (H.position) {
				case "right":
					a = (a + $(g).width() + Math.abs(H.xOffset) + H.zoomWidth < $(document).width()) ? (a + $(g).width() + Math.abs(H.xOffset)) : (a - H.zoomWidth - 10);
					topwindow = b + H.yOffset + H.zoomHeight;
					b = (topwindow < $(document).height() && topwindow > 0) ? b + H.yOffset: b;
					break;
				case "left":
					a = (l.pos.l - Math.abs(H.xOffset) - H.zoomWidth > 0) ? (l.pos.l - Math.abs(H.xOffset) - H.zoomWidth) : (l.pos.l + l.w + 10);
					topwindow = l.pos.t + H.yOffset + H.zoomHeight;
					b = (topwindow < $(document).height() && topwindow > 0) ? l.pos.t + H.yOffset: l.pos.t;
					break;
				case "top":
					b = (l.pos.t - Math.abs(H.yOffset) - H.zoomHeight > 0) ? (l.pos.t - Math.abs(H.yOffset) - H.zoomHeight) : (l.pos.t + l.h + 10);
					leftwindow = l.pos.l + H.xOffset + H.zoomWidth;
					a = (leftwindow < $(document).width() && leftwindow > 0) ? l.pos.l + H.xOffset: l.pos.l;
					break;
				case "bottom":
					b = (l.pos.b + Math.abs(H.yOffset) + H.zoomHeight < $(document).height()) ? (l.pos.b + Math.abs(H.yOffset)) : (l.pos.t - H.zoomHeight - 10);
					leftwindow = l.pos.l + H.xOffset + H.zoomWidth;
					a = (leftwindow < $(document).width() && leftwindow > 0) ? l.pos.l + H.xOffset: l.pos.l;
					break;
				default:
					a = (l.pos.l + l.w + H.xOffset + H.zoomWidth < $(document).width()) ? (l.pos.l + l.w + Math.abs(H.xOffset)) : (l.pos.l - H.zoomWidth - Math.abs(H.xOffset));
					b = (l.pos.b + Math.abs(H.yOffset) + H.zoomHeight < $(document).height()) ? (l.pos.b + Math.abs(H.yOffset)) : (l.pos.t - H.zoomHeight - Math.abs(H.yOffset));
					break
				}
				this.node.style.left = a + 'px';
				this.node.style.top = b + 'px';
				return this
			};
			Stage.prototype.activate = function () {
				if (!this.node.firstChild) this.node.appendChild(s.node);
				if (H.title) {
					r.loadtitle()
				}
				document.body.appendChild(this.node);
				switch (H.showEffect) {
				case 'show':
					$(this.node).show();
					break;
				case 'fadein':
					$(this.node).fadeIn(H.fadeinSpeed);
					break;
				default:
					$(this.node).show();
					break
				}
				$(this.node).show();
				if ($.browser.msie && $.browser.version < 7) {
					this.ieframe = $('<iframe class="zoom_ieframe" name="content" frameborder="0"  src="#"  style="background-color: transparent" bgcolor="transparent"></iframe>').css({
						position: "absolute",
						left: this.node.style.left,
						top: this.node.style.top,
						zIndex: 99,
						width: (H.zoomWidth + 2),
						height: (H.zoomHeight)
					}).insertBefore(this.node)
				};
				s.node.style.display = 'block'
			};
			Stage.prototype.remove = function () {
				switch (H.hideEffect) {
				case 'hide':
					$('.jqZoomWindow').remove();
					break;
				case 'fadeout':
					$('.jqZoomWindow').fadeOut(H.fadeoutSpeed);
					break;
				default:
					$('.jqZoomWindow').remove();
					break
				}
			};
			function zoomTitle() {
				this.node = jQuery('<div />').addClass('jqZoomTitle').html('' + q + '');
				this.loadtitle = function () {
					if (H.zoomType == 'innerzoom') {
						$(this.node).css({
							position: 'absolute',
							top: l.pos.b + 3,
							left: (l.pos.l + 1),
							width: l.w
						}).appendTo('body')
					} else {
						$(this.node).appendTo(z.node)
					}
				}
			};
			zoomTitle.prototype.remove = function () {
				$('.jqZoomTitle').remove()
			};
			function Loader() {
				this.node = document.createElement("div");
				$(this.node).addClass('preload');
				$(this.node).html(H.preloadText);
				$(this.node).appendTo(a).css('visibility', 'hidden');
				this.show = function () {
					switch (H.preloadPosition) {
					case 'center':
						loadertop = (l.h - $(this.node).height()) / 2;
						loaderleft = (l.w - $(this.node).width()) / 2;
						$(this.node).css({
							top: loadertop,
							left: loaderleft
						});
						break;
					default:
						var a = this.getoffset();
						break
					}
					$(this.node).css({
						position: 'absolute',
						visibility: 'visible'
					})
				};
				return this
			};
			Loader.prototype.getoffset = function () {
				var o = null;
				o = $('div.preload').offset();
				return o
			}
		})
	}
})(jQuery);

function trim(a) {
	while (a.substring(0, 1) == ' ') {
		a = a.substring(1, a.length)
	}
	while (a.substring(a.length - 1, a.length) == ' ') {
		a = a.substring(0, a.length - 1)
	}
	return a
};
/*<?php
}
?>*/
