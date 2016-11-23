/* PullDownLinkHandler.js <?php
#   --------------------------------------------------------------
#   PullDownLinkHandler.js 2014-04-15 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 o(){1(8)9.e(\'o O\');p d=q(),r=q(),s=2,6=i;2.w=3(){1(8)9.e(\'o w\');$(\'.x\').f(\'4\');$(\'.x\').j(\'4\',3(){1(8)9.e(\'.x 4\');s.E(2);P t})};2.E=3(a){p b=$(a).5(\'g\');1($(b).k(\'Q\')==\'R\'){1($(a).F(\'G[u]\').5(\'u\').y>0){$(\'#\'+$(a).F(\'G[u]\').5(\'u\')+\' a\').S(3(){1($(2).5(\'g\')&&$(2).5(\'g\').y>0){1(7(d[$(2).5(\'g\')])==\'l\'){h(d[$(2).5(\'g\')])}$($(2).5(\'g\')).m()}})}p c=v($(a).H().z)+v($(a).T())+v($(a).k(\'I-z\').J(\'A\',\'\'))+v($(a).k(\'I-U\').J(\'A\',\'\'));$(b).k(\'K\',$(a).H().K).k(\'z\',c+\'A\').V();1($(a).W(\'X\')==t&&7(Y)==\'L\'){d[b]=M("$(\'"+b+"\').m()",N)}}Z{1(7(d[b])==\'l\'){h(d[b])}$(b).m()}$(b).f(\'4\');$(b).j(\'4\',3(){6=t;r[b]=i;1(7(d[b])==\'l\'){h(d[b])}});$(b).f(\'B\');$(b).j(\'B\',3(){1(8)9.e(b+\': B\');6=t;1(7(d[b])==\'l\'){h(d[b])}});$(b).f(\'C\');$(b).j(\'C\',3(){1(8)9.e(b+\': C\');6=i;1(7(d[b])==\'l\'&&7(r[b])==\'L\'){h(d[b]);d[b]=M("$(\'"+b+"\').m()",N)}});$(\'.D\').f(\'4\',s.n);$(\'.D\').j(\'4\',s.n)};2.n=3(){1(8)9.e(\'o n 10: \'+6);p a=\'\';11(a 12 d){h(d[a]);1($(a).y>0&&6==i){$(a).m()}}1(6==i){d=q();r=q();$(\'.D\').f(\'4\',2.n)}};2.w()}',62,65,'|if|this|function|click|attr|t_allow_close_all|typeof|fb|console|||||log|die|rel|clearTimeout|true|live|css|number|slideUp|close_all|PullDownLinkHandler|var|Object|coo_clicked_elements|coo_this|false|id|Number|init_binds|pulldown_link|length|top|px|mouseenter|mouseleave|wrap_shop|start|closest|div|offset|padding|replace|left|undefined|setTimeout|3000|ready|return|display|none|each|height|bottom|slideDown|hasClass|pulldown_link_no_auto_close|gm_style_edit_mode_running|else|allowed|for|in'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function PullDownLinkHandler()
{
	if(fb)console.log('PullDownLinkHandler ready');

	var coo_clicked_ids = Object();
	var coo_clicked_elements = Object();
	var coo_this = this;
	var t_allow_close_all = true;

	this.init_binds = function()
	{
		if(fb)console.log('PullDownLinkHandler init_binds');

		$('.pulldown_link').die('click');
		$('.pulldown_link').live('click', function()
		{
			if(fb)console.log('.pulldown_link click');

			coo_this.start(this);

			return false;
		});
	}

	this.start = function(p_element)
	{
		var t_container = $(p_element).attr('rel');

		// open menu
		if($(t_container).css('display') == 'none'){
			// hide opened dropdowns belonging to the div-container where the clicked link is placed in
			if($(p_element).closest('div[id]').attr('id').length > 0)
			{
				$('#' + $(p_element).closest('div[id]').attr('id') + ' a').each(function()
				{
					if($(this).attr('rel') && $(this).attr('rel').length > 0)
					{
						if(typeof(coo_clicked_ids[$(this).attr('rel')]) == 'number')
						{
							clearTimeout(coo_clicked_ids[$(this).attr('rel')]);
						}
						$($(this).attr('rel')).slideUp();
					}
				});
			}

			var t_top = Number($(p_element).offset().top) +
						Number($(p_element).height()) +
						Number($(p_element).css('padding-top').replace('px', '')) +
						Number($(p_element).css('padding-bottom').replace('px', ''));
			$(t_container).css('left', $(p_element).offset().left).css('top', t_top + 'px').slideDown();

			if($(p_element).hasClass('pulldown_link_no_auto_close') == false && typeof(gm_style_edit_mode_running) == 'undefined')
			{
				coo_clicked_ids[t_container] = setTimeout("$('" + t_container + "').slideUp()", 3000);
			}
		}
		// close menu
		else
		{
			if(typeof(coo_clicked_ids[t_container]) == 'number')
			{
				clearTimeout(coo_clicked_ids[t_container]);
			}
			$(t_container).slideUp();
		}

		// stop menu sliding up by clicking into it
		$(t_container).die('click');
		$(t_container).live('click', function()
		{
			t_allow_close_all = false;

			coo_clicked_elements[t_container] = true;

			if(typeof(coo_clicked_ids[t_container]) == 'number')
			{
				clearTimeout(coo_clicked_ids[t_container]);
			}
		});

		// stop menu sliding up on mouseenter
		$(t_container).die('mouseenter');
		$(t_container).live('mouseenter', function()
		{
			if(fb)console.log(t_container + ': mouseenter');

			t_allow_close_all = false;

			if(typeof(coo_clicked_ids[t_container]) == 'number')
			{
				clearTimeout(coo_clicked_ids[t_container]);
			}
		});

		// start timeout for sliding up menu
		$(t_container).die('mouseleave');
		$(t_container).live('mouseleave', function()
		{
			if(fb)console.log(t_container + ': mouseleave');

			t_allow_close_all = true;

			if(typeof(coo_clicked_ids[t_container]) == 'number' && typeof(coo_clicked_elements[t_container]) == 'undefined')
			{
				clearTimeout(coo_clicked_ids[t_container]);
				coo_clicked_ids[t_container] = setTimeout("$('" + t_container + "').slideUp()", 3000);
			}
		});

		$('.wrap_shop').die('click', coo_this.close_all);
		$('.wrap_shop').live('click', coo_this.close_all);
	}

	this.close_all = function()
	{
		if(fb)console.log('PullDownLinkHandler close_all allowed: ' + t_allow_close_all);

		var t_element = '';

		for(t_element in coo_clicked_ids)
		{
			clearTimeout(coo_clicked_ids[t_element]);
			if($(t_element).length > 0 && t_allow_close_all == true)
			{
				$(t_element).slideUp();
			}
		}

		// reset
		if(t_allow_close_all == true)
		{
			coo_clicked_ids = Object();
			coo_clicked_elements = Object();
			$('.wrap_shop').die('click', this.close_all);
		}
	}

	this.init_binds();
}
/*<?php
}
?>*/

