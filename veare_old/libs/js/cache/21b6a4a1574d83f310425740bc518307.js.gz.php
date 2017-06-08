<?php ob_start("ob_gzhandler"); header("content-type: text/javascript; charset: UTF-8"); header("cache-control: must-revalidate"); header("expires: ".gmdate('D, d M Y H:i:s', time() + 3600)." GMT"); ?>
$(function()
{var window_height,activatables=new Array(),activate_areas=new Array(),scrollTop=gCache.window.scrollTop(),scrollBottom=scrollTop+gCache.window.height(),resChange;check_active_fn=function()
{scrollTop=gCache.window.scrollTop();scrollBottom=scrollTop+gCache.window.height();$.each(activate_areas,function(index,top)
{if((activatables[index].bottom>=scrollTop+(gCache.window.height()*0.08))&&(activatables[index].top<=scrollBottom-(gCache.window.height()*0.08))&&(activatables[index].bottom<=scrollBottom-(gCache.window.height()*0.08))&&(activatables[index].top>=scrollTop+(gCache.window.height()*0.08)))
{activatables[index].selector.addClass('active');}
else
{activatables[index].selector.removeClass('active');}});};var prepare_active_fn=function()
{window_height=gCache.window.height();$('.about-activatable').each(function(index){activatables[index]=new Array();activatables[index].selector=$(this);activatables[index].top=Math.round(activatables[index].selector.offset().top);activatables[index].bottom=activatables[index].top+activatables[index].selector.find('.content').height();activate_areas[index]=Math.round(activatables[index].top);});if(gCache.body.hasClass('tablet-small'))
{$('.active-bg').each(function(){var _this=$(this);_this.css({'width':_this.parents('.column').width()+25,'height':_this.parents('.column').find('.text').height()+60});});}
else
{$('.active-bg.right').each(function(){var _this=$(this);_this.css({'width':_this.parents('.column').width()+60+($('#stage').width()-_this.position().left),'height':_this.parents('.column').find('.text').height()+60});});$('.active-bg.left').each(function(){var _this=$(this);_this.css({'width':_this.parents('.column').width()+60,'height':_this.parents('.column').find('.text').height()+60});});}};empty_fn=function(){};var adjust_font=function(callback)
{if(typeof(callback)==='undefined')callback=function(){};var _main_headline=$('.main-headline');var _quote_box=$('.quote-box');if(($('#stage').width()-$('#sidebar').width())<_main_headline.width())
{_main_headline.addClass('small-font');}
else
{_main_headline.removeClass('small-font');}
if(_quote_box.width()<240)
{_quote_box.css({'fontSize':'110%'});}
else
{_quote_box.css({'fontSize':''});}
setTimeout(function(){callback();},300);};pages.about={};pages.about.init=function()
{resChange=function(e,resolution)
{var _rearrange=$('.rearrange');if(resolution=='tablet-small'||resolution=='mobile-portrait'||resolution=='mobile-landscape'||resolution=='mobile')
{if(resolution!='tablet-small')
{check_active="empty_fn";}
else
{check_active="check_active_fn";}
_rearrange.each(function(){var _this=$(this);_this.find('.content').insertBefore(_this.find('.quote'));});}
else
{check_active="check_active_fn";_rearrange.each(function(){var _this=$(this);_this.find('.quote').insertBefore(_this.find('.content'));});}};resizeFN=function()
{if(!gCache.body.hasClass('mobile'))
{prepare_active_fn();}
adjust_font();};gCache.body.on('resolutionChange',resChange);$.fs_resize(resizeFN);$.fs_load(function()
{adjust_font(function()
{var _rearrange=$('.rearrange');if(gCache.body.hasClass('tablet-small')||gCache.body.hasClass('mobile-portrait')||gCache.body.hasClass('mobile-landscape')||gCache.body.hasClass('mobile'))
{_rearrange.each(function(){var _this=$(this);_this.find('.content').insertBefore(_this.find('.quote'));});if(!gCache.body.hasClass('tablet-small'))
{check_active="empty_fn";}
else
{check_active="check_active_fn";}}
else
{check_active="check_active_fn";_rearrange.each(function(){var _this=$(this);_this.find('.quote').insertBefore(_this.find('.content'));});}
prepare_active_fn();gCache.window.on('scroll',function()
{if(window_height!=gCache.window.height())
{prepare_active_fn();}
window[check_active]();});});});};pages.about.destroy=function()
{gCache.body.off('resolutionChange',resChange);};pages.about.init();});