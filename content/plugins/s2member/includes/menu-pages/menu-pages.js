/**
 * Core JavaScript routines for administrative menu pages.
 *
 * This is the development version of the code.
 * Which ultimately produces menu-pages-min.js.
 *
 * This file is included with all WordPress themes/plugins by WebSharks, Inc.
 *
 * Copyright: © 2009-2011
 * {@link http://websharks-inc.com/ WebSharks, Inc.}
 * (coded in the USA)
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}.
 *
 * @package WebSharks\Menu Pages
 * @since x.xx
 */
jQuery(document).ready( // DOM ready handler.
	function($ /* jQuery passed through this arg. */)
	{
		$(window).resize(window.tb_position = function()
		{
			var w = ($(window).width() > 720) ? 720 : $(window).width(), h = $(window).height(), admin_bar_h = ($('body.admin-bar').length) ? 28 : 0;
			$('#TB_window').css({'width': w - 50 + 'px', 'height': h - 45 - admin_bar_h + 'px', 'top': 25 + admin_bar_h + 'px', 'margin-top': 0, 'margin-left': '-' + parseInt(((w - 50) / 2), 10) + 'px'});
			$('#TB_ajaxContent').css({'width': w - 50 + 'px', 'height': h - 75 - admin_bar_h + 'px', 'margin': 0, 'padding': 0});
		});
		var $rs = $('table.ws-menu-page-table td.ws-menu-page-table-r'),
			$rsToggler = $('> .toggler', $rs), $rsWrapper = $('> .wrapper', $rs);

		var $groups = $('div.ws-menu-page-group'/* Query groups. */);
		$groups.each(function(index/* Go through each group, one at a time. */)
		             {
			             var $this = $(this), $group = $this,
				             title = $.trim($group.attr('title')),
				             $header = $('<div class="ws-menu-page-group-header">' + title + '</div>');

			             $header.css({'z-index': 1}), $header.insertBefore($group),
				             $header.click(function()
				                           {
					                           var $this = $(this),
						                           $group = $this.next();

					                           if($this.hasClass('open'))
						                           $this.add($group).removeClass('open');
					                           else $this.add($group).addClass('open');

					                           return false;
				                           });
			             if($groups.length > 1 && index === 0)
			             {
				             $('<div class="ws-menu-page-groups-hide"><i class="fa fa-chevron-up"></i></div>')
					             .insertBefore('div.ws-menu-page-toolbox')
					             .click(function(/* Toggle all closed. */)
					                    {
						                    $('div.ws-menu-page-group-header')
							                    .each(function()
							                          {
								                          var $this = $(this), $group = $this.next();
								                          $this.add($group).removeClass('open');
							                          });
						                    return false;
					                    });
				             $('<div class="ws-menu-page-groups-show"><i class="fa fa-chevron-down"></i></div>')
					             .insertBefore('div.ws-menu-page-toolbox')
					             .click(function(/* Toggle all open. */)
					                    {
						                    $('div.ws-menu-page-group-header')
							                    .each(function()
							                          {
								                          var $this = $(this), $group = $this.next();
								                          $this.add($group).addClass('open');
							                          });
						                    return false;
					                    });
			             }
			             if($group.attr('default-state') === 'open')
				             $header.trigger('click');
		             });
		$rsToggler.click(function()
		                 {
			                 if($rs.hasClass('open'))
				                 $rsWrapper.hide(), $rs.removeClass('open');
			                 else $rsWrapper.show(), $rs.addClass('open');
		                 });
		if($rsToggler.attr('default-state') === 'open') $rsToggler.click();

		$('div.ws-menu-page-r-group-header')
			.click(function(/* Toggle groups in sidebar. */)
			       {
				       var $this = $(this),
					       $group = $this.next();

				       if($this.hasClass('open'))
					       $this.add($group).removeClass('open');
				       else $this.add($group).addClass('open');

				       return false;
			       });
		$('div.ws-menu-page-group-header:first, div.ws-menu-page-r-group-header:first').css({'margin-top': '0'});
		$('div.ws-menu-page-group-header:last, div.ws-menu-page-r-group-header:last, div.ws-menu-page-group:last').css({'margin-bottom': '0'});

		$('input.ws-menu-page-media-btn').filter(function(){ return ($(this).attr('rel')) ? true : false; })
			.click(function()
			       {
				       var $this = $(this);

				       window.send_to_editor = function(html)
				       {
					       var $inp, $txt, rel = $.trim($this.attr('rel')), oBg, src;

					       if(rel && ($inp = $('input#' + rel)).length > 0)
					       {
						       oBg = $inp.css('background-color'),
							       src = $.trim($(html).attr('src'));
						       src = (!src) ? $.trim($('img', html).attr('src')) : src;

						       $inp.val(src),
							       $inp.css({'background-color': '#FFFFCC'}),
							       setTimeout(function()
							                  {
								                  $inp.css({'background-color': oBg});
							                  }, 2000);
						       tb_remove();
					       }
					       else if(rel && ($txt = $('textarea#' + rel)).length > 0)
					       {
						       oBg = $txt.css('background-color'),
							       src = $.trim($(html).attr('src'));
						       src = (!src) ? $.trim($('img', html).attr('src')) : src;

						       $txt.val($.trim($txt.val()) + '\n' + src),
							       $txt.css({'background-color': '#FFFFCC'}),
							       setTimeout(function()
							                  {
								                  $txt.css({'background-color': oBg});
							                  }, 2000);
						       tb_remove();
					       }
				       };
				       tb_show('', './media-upload.php?type=image&TB_iframe=true');

				       return false;
			       });
		$('form#ws-updates-form')
			.submit(function(/* MailChimp validation. */)
			        {
				        var errors = ''; // Initialize.

				        if(!$.trim($('input#ws-updates-fname').val()))
					        errors += 'First Name missing, please try again.\n\n';

				        if(!$.trim($('input#ws-updates-lname').val()))
					        errors += 'Last Name missing, please try again.\n\n';

				        if(!$.trim($('input#ws-updates-email').val()))
					        errors += 'Email missing, please try again.\n\n';

				        else if(!$('input#ws-updates-email').val().match(/^([a-z_~0-9\+\-]+)(((\.?)([a-z_~0-9\+\-]+))*)(@)([a-z0-9]+)(((-*)([a-z0-9]+))*)(((\.)([a-z0-9]+)(((-*)([a-z0-9]+))*))*)(\.)([a-z]{2,6})$/i))
					        errors += 'Invalid email address, please try again.\n\n';

				        if(errors = $.trim(errors))
				        {
					        alert('— Oops, you missed something: —\n\n' + errors);
					        return false;
				        }
				        return true;
			        });
	});