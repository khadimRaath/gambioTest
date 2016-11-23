/*!
 * CrossSlide jQuery plugin v0.6.2
 *
 * Copyright 2007-2010 by Tobia Conforto <tobia.conforto@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(7(){6 $=2e,R=($.K.1M?\'1M\':\'R\'),1y=\'1j 2f 11.\';7 1k(a){1N(6 i=1;i<1a.y;i++)a=a.1l(1b 2g(\'\\\\{\'+(i-1)+\'}\',\'g\'),1a[i]);w a}7 D(){1a[0]=\'2h: \'+1a[0];1O 1b 1P(1k.1Q(2i,1a));}7 1z(a){6 b=1;6 c=a.1l(/^\\s*|\\s*$/g,\'\').2j(/\\s+/);4(c.y>3)1O 1b 1P();4(c[0]==\'A\')4(c.y==1)c=[\'A\',\'A\'];N 4(c.y==2&&c[1].1m(/^[\\d.]+x$/i))c=[\'A\',\'A\',c[1]];4(c.y==3)b=2k(c[2].1m(/^([\\d.]+)x$/i)[1]);6 d=c[0]+\' \'+c[1];4(d==\'F I\'||d==\'I F\')w{9:0,u:0,8:b};4(d==\'F A\'||d==\'A F\')w{9:0,u:.5,8:b};4(d==\'F 12\'||d==\'12 F\')w{9:0,u:1,8:b};4(d==\'A I\'||d==\'I A\')w{9:.5,u:0,8:b};4(d==\'A A\')w{9:.5,u:.5,8:b};4(d==\'A 12\'||d==\'12 A\')w{9:.5,u:1,8:b};4(d==\'S I\'||d==\'I S\')w{9:1,u:0,8:b};4(d==\'S A\'||d==\'A S\')w{9:1,u:.5,8:b};4(d==\'S 12\'||d==\'12 S\')w{9:1,u:1,8:b};w{9:1R(c[0].1m(/^(\\d+)%$/)[1])/1S,u:1R(c[1].1m(/^(\\d+)%$/)[1])/1S,8:b}}$.K.1A=7(q,r,s){6 t=J,T=J.B(),U=J.C();4(!t.B()||!t.C()){T=1T(J.G(\'B\').1l(\'1U\',\'\'));U=1T(J.G(\'C\').1l(\'1U\',\'\'))}4(t.y!=1)D(\'1A() 2l 2m 2n 2o 2p 1 1V\');t.L(0).1W=[q,r,s];r=$.2q(r,7(p){w $.1n({},p)});4(!q.1B)q.1B=q.1o?\'2r\':\'1c\';4(!s)s=7(){};(7(b){6 c=0;7 1d(i,a){a.2s=7(e){c++;r[i].B=a.B;r[i].C=a.C;4(c==r.y)b()};a.13=r[i].13;4(i+1<r.y)1d(i+1,1b 1X())}1d(0,1b 1X())})(7(){4(!q.1p)D(\'11 1p 14.\');4(q.1e&&q.1f)D(\'2t 2u 2v 2w 1e 1Y 1f 2x 2y 2z O.\');6 g=z.H(q.1p*1q);4(q.1f)6 h=z.H(q.1f*1q);4(q.1e)6 j=q.1e/1q,1g=z.H(g*j);t.2A().G({2B:\'1r\',2C:0});4(!/^(1Z|20|2D)$/.2E(t.G(\'1C\')))t.G({1C:\'20\'});4(21(T)||T==0||21(U)||U==0)D(\'2F 1V 2G 2H 2I 2J 2K B 1Y C\');4(q.2L)r.2M(7(){w z.2N()-0.5});1N(6 i=0;i<r.y;++i){6 p=r[i];4(!p.13)D(\'11 13 14 15 V {0}.\',i+1);4(j){2O(p.22){1s\'2P\':p.v={9:.5,u:0,8:1};p.E={9:.5,u:1,8:1};6 k=p.C-U-2*1g;1t;1s\'2Q\':p.v={9:.5,u:1,8:1};p.E={9:.5,u:0,8:1};6 k=p.C-U-2*1g;1t;1s\'F\':p.v={9:0,u:.5,8:1};p.E={9:1,u:.5,8:1};6 k=p.B-T-2*1g;1t;1s\'S\':p.v={9:1,u:.5,8:1};p.E={9:0,u:.5,8:1};6 k=p.B-T-2*1g;1t;2R:D(\'11 1u 1D 22 14 15 V {0}.\',i+1)}4(k<=0)D(\'2S 2T: 23 V {0} 24 1E 2U 1u \'+\'2V 24 1E 2W 1u 1p 2X 1E 2Y.\',i+1);p.1h=z.H(k/j)}N 4(!h){4(!p.v||!p.E||!p.O)D(\'11 23 1e/1f 2Z, 1u v/E/O 30 \'+\'15 V {0}.\',i+1);25{p.v=1z(p.v)}26(e){D(\'1D "v" 14 15 V {0}.\',i+1)}25{p.E=1z(p.E)}26(e){D(\'1D "E" 14 15 V {0}.\',i+1)}4(!p.O)D(\'11 "O" 14 15 V {0}.\',i+1);p.1h=z.H(p.O*1q)}4(p.v)$.31([p.v,p.E],7(i,a){a.B=z.H(p.B*a.8);a.C=z.H(p.C*a.8);a.F=z.H((T-a.B)*a.9);a.I=z.H((U-a.C)*a.u)});6 l,W;W=l=$(1k(\'<X 13="{0}"/>\',p.13));4(p.1v)W=$(1k(\'<a 1v="{0}"></a>\',p.1v)).32(l);4(p.27)W.33(p.27);4(p.1F)l.1G(\'1F\',p.1F);4(p.1H)W.1G(\'1H\',p.1H);4(p.1v&&p.1I)W.1G(\'1I\',p.1I);W.34(t)}35 j;7 16(p,a){6 b=[0,g/(p.1h+2*g),1-g/(p.1h+2*g),1][a];w{F:z.H(p.v.F+b*(p.E.F-p.v.F)),I:z.H(p.v.I+b*(p.E.I-p.v.I)),B:z.H(p.v.B+b*(p.E.B-p.v.B)),C:z.H(p.v.C+b*(p.E.C-p.v.C))}}6 m=t.17(\'X\').G({1C:\'1Z\',1i:\'1r\',I:0,F:0,36:0});m.P(0).G({1i:\'28\'});4(!h)m.P(0).G(16(r[0],q.1o?0:1));6 n=q.1d;7 1J(i,a){4(i%2==0){4(h){6 b=i/2,1w=(b-1+r.y)%r.y,29=m.P(b),1x=m.P(1w);6 c=7(){s(b,29.L(0));1x.G(\'1i\',\'1r\');37(a,h)}}N{6 d=i/2,1w=(d-1+r.y)%r.y,1K=m.P(d),1x=m.P(1w),O=r[d].1h,2a=16(r[d],q.1o?3:2);6 c=7(){s(d,1K.L(0));1x.G(\'1i\',\'1r\');1K[R](2a,O,q.1B,a)}}}N{6 e=z.38(i/2),Q=z.39(i/2)%r.y,18=m.P(e),M=m.P(Q),Y={},Z={1i:\'28\'},10={};4(Q>e){Z.19=0;10.19=1;4(q.2b)Y.19=0}N{Y.19=0;4(q.2b){Z.19=0;10.19=1}}4(!h){$.1n(Z,16(r[Q],0));4(!q.1o){$.1n(Y,16(r[e],3));$.1n(10,16(r[Q],1))}}4($.2c(10)){6 c=7(){s(Q,M.L(0),e,18.L(0));M.G(Z);18[R](Y,g,\'1c\',a)}}N 4($.2c(Y)){6 c=7(){s(Q,M.L(0),e,18.L(0));M.G(Z);M[R](10,g,\'1c\',a)}}N{6 c=7(){s(Q,M.L(0),e,18.L(0));M.G(Z);M[R](10,g,\'1c\');18[R](Y,g,\'1c\',a)}}}4(q.1d&&i==r.y*2-2){6 f=c;c=7(){4(--n)f()}}4(i>0)w 1J(i-1,c);N w c}6 o=1J(r.y*2-1,7(){w o()});o()});w t};$.K.3a=7(){J.17(\'X\').1L()};$.K.3b=7(){J.17(\'X\').1L().2d()};$.K.3c=7(){J.17(\'X\').1L().2d();$.K.1A.1Q(J,J.L(0).1W)};$.K.3d=7(){4(!$.K.1j)D(1y);J.17(\'X\').1j()};$.K.3e=7(){4(!$.K.1j)D(1y);J.17(\'X\').3f()}})();',62,202,'||||if||var|function|zoom|xrel|||||||||||||||||||||yrel|from|return||length|Math|center|width|height|abort|to|left|css|round|top|this|fn|get|img_to|else|time|eq|i_to|animate|right|self_width|self_height|picture|elm|img|from_anim|to_init|to_anim|missing|bottom|src|parameter|in|position_to_css|find|img_from|opacity|arguments|new|linear|loop|speed|sleep|fade_px|time_ms|visibility|pause|format|replace|match|extend|variant|fade|1000|hidden|case|break|or|href|i_hide|img_hide|pause_missing|parse_position_param|crossSlide|easing|position|malformed|too|alt|attr|rel|target|create_chain|img_slide|stop|startAnimation|for|throw|Error|apply|parseInt|100|Number|px|element|crossSlideArgs|Image|and|absolute|relative|isNaN|dir|either|is|try|catch|onclick|visible|img_sleep|slide_anim|doubleFade|isEmptyObject|remove|jQuery|plugin|RegExp|CrossSlide|null|split|parseFloat|must|be|called|on|exactly|map|swing|onload|you|cannot|set|both|at|the|same|empty|overflow|padding|fixed|test|container|does|not|have|its|own|shuffle|sort|random|switch|up|down|default|impossible|animation|small|div|large|duration|long|option|params|each|append|click|appendTo|delete|border|setTimeout|floor|ceil|crossSlideFreeze|crossSlideStop|crossSlideRestart|crossSlidePause|crossSlideResume|resume'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
/* Changelog:
 *
 * 0.6.2  2010-09-29  Added support for rel attribute
 * 0.6.1  2010-08-02  Bugfixes
 * 0.6    2010-07-03  Variant Ken Burns effect
 * 0.5    2010-06-13  Support for animation control and event callbacks
 * 0.4.2  2010-06-07  Bugfix
 * 0.4.1  2010-06-04  Added target option
 * 0.4    2010-05-20  Better error reporting, published on GitHub
 * 0.3.7  2009-05-14  Fixed bug when container div's CSS position is not set
 * 0.3.6  2009-04-16  Added alt option
 * 0.3.5  2009-03-12  Fixed usage of href parameter in 'Ken Burns' mode
 * 0.3.4  2009-03-12  Added shuffle option,
 *                    contrib. by Ralf Santbergen <ralf_santbergen@hotmail.com>
 * 0.3.3  2008-12-14  Added onclick option
 * 0.3.2  2008-11-11  Fixed CSS bugs, contrib. by Erwin Bot <info@ixgcms.nl>
 * 0.3.1  2008-11-11  Better error messages
 * 0.3    2008-10-22  Option to repeat the animation a fixed number of times
 * 0.2    2008-10-15  Linkable images, contrib. by Tim Rainey <tim@zmlabs.com>
 * 0.1.1  2008-09-18  Compatibility with prototype.js
 * 0.1    2008-08-21  Re-released under GPL v2
 * 0.1    2007-08-19  Initial release
 */

(function() {
	var $ = jQuery,
		animate = ($.fn.startAnimation ? 'startAnimation' : 'animate'),
		pause_missing = 'pause plugin missing.';

	// utility to format a string with {0}, {1}... placeholders
	function format(str) {
		for (var i = 1; i < arguments.length; i++)
			str = str.replace(new RegExp('\\{' + (i-1) + '}', 'g'), arguments[i]);
		return str;
	}

	// utility to abort with a message to the error console
	function abort() {
		arguments[0] = 'CrossSlide: ' + arguments[0];
		throw new Error(format.apply(null, arguments));
	}

	// utility to parse "from" and "to" parameters
	function parse_position_param(param) {
		var zoom = 1;
		var tokens = param.replace(/^\s*|\s*$/g, '').split(/\s+/);
		if (tokens.length > 3) throw new Error();
		if (tokens[0] == 'center')
			if (tokens.length == 1)
				tokens = ['center', 'center'];
			else if (tokens.length == 2 && tokens[1].match(/^[\d.]+x$/i))
				tokens = ['center', 'center', tokens[1]];
		if (tokens.length == 3)
			zoom = parseFloat(tokens[2].match(/^([\d.]+)x$/i)[1]);
		var pos = tokens[0] + ' ' + tokens[1];
		if (pos == 'left top' || pos == 'top left')
			return { xrel: 0, yrel: 0, zoom: zoom };
		if (pos == 'left center' || pos == 'center left')
			return { xrel: 0, yrel: .5, zoom: zoom };
		if (pos == 'left bottom' || pos == 'bottom left')
			return { xrel: 0, yrel: 1, zoom: zoom };
		if (pos == 'center top' || pos == 'top center')
			return { xrel: .5, yrel: 0, zoom: zoom };
		if (pos == 'center center')
			return { xrel: .5, yrel: .5, zoom: zoom };
		if (pos == 'center bottom' || pos == 'bottom center')
			return { xrel: .5, yrel: 1, zoom: zoom };
		if (pos == 'right top' || pos == 'top right')
			return { xrel: 1, yrel: 0, zoom: zoom };
		if (pos == 'right center' || pos == 'center right')
			return { xrel: 1, yrel: .5, zoom: zoom };
		if (pos == 'right bottom' || pos == 'bottom right')
			return { xrel: 1, yrel: 1, zoom: zoom };
		return {
			xrel: parseInt(tokens[0].match(/^(\d+)%$/)[1]) / 100,
			yrel: parseInt(tokens[1].match(/^(\d+)%$/)[1]) / 100,
			zoom: zoom
		};
	}

	$.fn.crossSlide = function(opts, plan, callback)
	{
		var self = this,
				self_width = this.width(),
				self_height = this.height();

		// BOF GM_MOD
		if(! self.width() || ! self.height())
		{
			self_width = Number(this.css('width').replace('px', ''));
			self_height = Number(this.css('height').replace('px', ''));
		}
		// EOF GM_MOD

		// must be called on exactly 1 element
		if (self.length != 1)
			abort('crossSlide() must be called on exactly 1 element')

		// saving params for crossSlide.restart
		self.get(0).crossSlideArgs = [ opts, plan, callback ];

		// make working copy of plan
		plan = $.map(plan, function(p) {
			return $.extend({}, p);
		});

		// options with default values
		if (! opts.easing)
			opts.easing = opts.variant ? 'swing' : 'linear';
		if (! callback)
			callback = function() {};

		// first preload all the images, while getting their actual width and height
		(function(proceed) {

			var n_loaded = 0;
			function loop(i, img) {
				// this loop is a for (i = 0; i < plan.length; i++)
				// with independent var i, img (for the onload closures)
				img.onload = function(e) {
					n_loaded++;
					plan[i].width = img.width;
					plan[i].height = img.height;
					if (n_loaded == plan.length)
						proceed();
				}
				img.src = plan[i].src;
				if (i + 1 < plan.length)
					loop(i + 1, new Image());
			}
			loop(0, new Image());

		})(function() { // then proceed

			// check global params
			if (! opts.fade)
				abort('missing fade parameter.');
			if (opts.speed && opts.sleep)
				abort('you cannot set both speed and sleep at the same time.');

			// conversion from sec to ms; from px/sec to px/ms
			var fade_ms = Math.round(opts.fade * 1000);
			if (opts.sleep)
				var sleep = Math.round(opts.sleep * 1000);
			if (opts.speed)
				var speed = opts.speed / 1000,
						fade_px = Math.round(fade_ms * speed);

			// set container css
			self.empty().css({
				overflow: 'hidden',
				padding: 0
			});
			if (! /^(absolute|relative|fixed)$/.test(self.css('position')))
				self.css({ position: 'relative' });
			// BOF GM_MOD:
			if (isNaN(self_width) || self_width == 0 || isNaN(self_height) || self_height == 0)
						abort('container element does not have its own width and height');
			
			// random sorting
			if (opts.shuffle)
				plan.sort(function() {
					return Math.random() - 0.5;
				});

			// prepare each image
			for (var i = 0; i < plan.length; ++i) {

				var p = plan[i];
				if (! p.src)
					abort('missing src parameter in picture {0}.', i + 1);

				if (speed) { // speed/dir mode

					// check parameters and translate speed/dir mode into full mode
					// (from/to/time)
					switch (p.dir) {
						case 'up':
							p.from = { xrel: .5, yrel: 0, zoom: 1 };
							p.to = { xrel: .5, yrel: 1, zoom: 1 };
							var slide_px = p.height - self_height - 2 * fade_px;
							break;
						case 'down':
							p.from = { xrel: .5, yrel: 1, zoom: 1 };
							p.to = { xrel: .5, yrel: 0, zoom: 1 };
							var slide_px = p.height - self_height - 2 * fade_px;
							break;
						case 'left':
							p.from = { xrel: 0, yrel: .5, zoom: 1 };
							p.to = { xrel: 1, yrel: .5, zoom: 1 };
							var slide_px = p.width - self_width - 2 * fade_px;
							break;
						case 'right':
							p.from = { xrel: 1, yrel: .5, zoom: 1 };
							p.to = { xrel: 0, yrel: .5, zoom: 1 };
							var slide_px = p.width - self_width - 2 * fade_px;
							break;
						default:
							abort('missing or malformed dir parameter in picture {0}.', i+1);
					}
					if (slide_px <= 0)
						abort('impossible animation: either picture {0} is too small or '
							+ 'div is too large or fade duration too long.', i + 1);
					p.time_ms = Math.round(slide_px / speed);

				} else if (! sleep) { // full mode

					// check and parse parameters
					if (! p.from || ! p.to || ! p.time)
						abort('missing either speed/sleep option, or from/to/time params '
							+ 'in picture {0}.', i + 1);
					try {
						p.from = parse_position_param(p.from)
					} catch (e) {
						abort('malformed "from" parameter in picture {0}.', i + 1);
					}
					try {
						p.to = parse_position_param(p.to)
					} catch (e) {
						abort('malformed "to" parameter in picture {0}.', i + 1);
					}
					if (! p.time)
						abort('missing "time" parameter in picture {0}.', i + 1);
					p.time_ms = Math.round(p.time * 1000)
				}

				// precalculate left/top/width/height bounding values
				if (p.from)
					$.each([ p.from, p.to ], function(i, each) {
						each.width = Math.round(p.width * each.zoom);
						each.height = Math.round(p.height * each.zoom);
						each.left = Math.round((self_width - each.width) * each.xrel);
						each.top = Math.round((self_height - each.height) * each.yrel);
					});

				// append the image (or anchor) element to the container
				var img, elm;
				elm = img = $(format('<img src="{0}"/>', p.src));
				if (p.href)
					elm = $(format('<a href="{0}"></a>', p.href)).append(img);
				if (p.onclick)
					elm.click(p.onclick);
				if (p.alt)
					img.attr('alt', p.alt);
				if (p.rel)
					elm.attr('rel', p.rel);
				if (p.href && p.target)
					elm.attr('target', p.target);
				elm.appendTo(self);
			}
			delete speed; // speed mode has now been translated to full mode

			// utility to compute the css for a given phase between p.from and p.to
			// 0: begin fade-in, 1: end fade-in, 2: begin fade-out, 3: end fade-out
			function position_to_css(p, phase) {
				var pos = [ 0, fade_ms / (p.time_ms + 2 * fade_ms),
					1 - fade_ms / (p.time_ms + 2 * fade_ms), 1 ][phase];
				return {
					left: Math.round(p.from.left + pos * (p.to.left - p.from.left)),
					top: Math.round(p.from.top + pos * (p.to.top - p.from.top)),
					width: Math.round(p.from.width + pos * (p.to.width - p.from.width)),
					height: Math.round(p.from.height + pos * (p.to.height-p.from.height))
				};
			}

			// find images to animate and set initial css attributes
			var imgs = self.find('img').css({
				position: 'absolute',
				visibility: 'hidden',
				top: 0,
				left: 0,
				border: 0
			});

			// show first image
			imgs.eq(0).css({ visibility: 'visible' });
			if (! sleep)
				imgs.eq(0).css(position_to_css(plan[0], opts.variant ? 0 : 1));

			// create animation chain
			var countdown = opts.loop;
			function create_chain(i, chainf) {
				// building the chain backwards, or inside out

				if (i % 2 == 0) {
					if (sleep) {
						// single image sleep
						var i_sleep = i / 2,
								i_hide = (i_sleep - 1 + plan.length) % plan.length,
								img_sleep = imgs.eq(i_sleep),
								img_hide = imgs.eq(i_hide);
						var newf = function() {
							callback(i_sleep, img_sleep.get(0));
							img_hide.css('visibility', 'hidden');
							setTimeout(chainf, sleep);
						};
					} else {
						// single image animation
						var i_slide = i / 2,
								i_hide = (i_slide - 1 + plan.length) % plan.length,
								img_slide = imgs.eq(i_slide),
								img_hide = imgs.eq(i_hide),
								time = plan[i_slide].time_ms,
								slide_anim = position_to_css(plan[i_slide],
									opts.variant ? 3 : 2);
						var newf = function() {
							callback(i_slide, img_slide.get(0));
							img_hide.css('visibility', 'hidden');
							img_slide[animate](slide_anim, time, opts.easing, chainf);
						};
					}
				} else {
					// double image animation
					var i_from = Math.floor(i / 2),
							i_to = Math.ceil(i / 2) % plan.length,
							img_from = imgs.eq(i_from),
							img_to = imgs.eq(i_to),
							from_anim = {},
							to_init = { visibility: 'visible' },
							to_anim = {};
					if (i_to > i_from) {
						to_init.opacity = 0;
						to_anim.opacity = 1;
						if (opts.doubleFade)
							from_anim.opacity = 0;
					} else {
						from_anim.opacity = 0;
						if (opts.doubleFade) {
							to_init.opacity = 0;
							to_anim.opacity = 1;
						}
					}
					if (! sleep) {
						// moving images
						$.extend(to_init, position_to_css(plan[i_to], 0));
						if (! opts.variant) {
							$.extend(from_anim, position_to_css(plan[i_from], 3));
							$.extend(to_anim, position_to_css(plan[i_to], 1));
						}
					}
					if ($.isEmptyObject(to_anim)) {
						var newf = function() {
							callback(i_to, img_to.get(0), i_from, img_from.get(0));
							img_to.css(to_init);
							img_from[animate](from_anim, fade_ms, 'linear', chainf);
						};
					} else if ($.isEmptyObject(from_anim)) {
						var newf = function() {
							callback(i_to, img_to.get(0), i_from, img_from.get(0));
							img_to.css(to_init);
							img_to[animate](to_anim, fade_ms, 'linear', chainf);
						};
					} else {
						var newf = function() {
							callback(i_to, img_to.get(0), i_from, img_from.get(0));
							img_to.css(to_init);
							img_to[animate](to_anim, fade_ms, 'linear');
							img_from[animate](from_anim, fade_ms, 'linear', chainf);
						};
					}
				}

				// if the loop option was requested, push a countdown check
				if (opts.loop && i == plan.length * 2 - 2) {
					var newf_orig = newf;
					newf = function() {
						if (--countdown) newf_orig();
					}
				}

				if (i > 0)
					return create_chain(i - 1, newf);
				else
					return newf;
			}
			var animation = create_chain(plan.length * 2 - 1,
				function() { return animation(); });

			// start animation
			animation();
		});
		return self;
	};

	$.fn.crossSlideFreeze = function()
	{
		this.find('img').stop();
	}

	$.fn.crossSlideStop = function()
	{
		this.find('img').stop().remove();
	}

	$.fn.crossSlideRestart = function()
	{
		this.find('img').stop().remove();
		$.fn.crossSlide.apply(this, this.get(0).crossSlideArgs);
	}

	$.fn.crossSlidePause = function()
	{
		if (! $.fn.pause)
			abort(pause_missing);
		this.find('img').pause();
	}

	$.fn.crossSlideResume = function()
	{
		if (! $.fn.pause)
			abort(pause_missing);
		this.find('img').resume();
	}
})();
/*<?php
}
?>*/
