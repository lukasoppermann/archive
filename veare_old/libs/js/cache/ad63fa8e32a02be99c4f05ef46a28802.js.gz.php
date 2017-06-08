<?php ob_start("ob_gzhandler"); header("content-type: text/javascript; charset: UTF-8"); header("cache-control: must-revalidate"); header("expires: ".gmdate('D, d M Y H:i:s', time() + 3600)." GMT"); ?>
(function($){$.fn.fs_equal_height=function(){var _this,_column=$(this),position_top,row_divs=new Array(),current_tallest,current_row_start,current_div;_column.height('auto');_column.each(function(){_this=$(this);position_top=_this.offset().top;if(current_row_start!=position_top)
{var len=row_divs.length;if(len>1)
{for(var current_div=0;current_div<len;current_div++)
{row_divs[current_div].height(current_tallest);}}
row_divs.length=0;current_row_start=position_top;current_tallest=_this.height();row_divs.push(_this);}
else
{row_divs.push(_this);current_tallest=Math.max(current_tallest,_this.height());}
var len=row_divs.length;if(len>1)
{for(var current_div=0;current_div<len;current_div++)
{row_divs[current_div].height(current_tallest);}}});return _column;}})(jQuery);;(function($){$.fn.fs_anchor=function(){if($.browser.safari)
{var _scroll_body=$("body");}
else
{var _scroll_body=$("html,body");}
var _this=$(this);_this.each(function()
{var _this=$(this);var _anker=$(this.hash);_this.on('click',function(event)
{event.preventDefault();var offset=Math.round(_anker.offset().top);_scroll_body.animate({'scrollTop':offset-70},400);});});}})(jQuery);;(function($,window)
{var plugin_name='fs_sticky_top',_this,_window,pos_start,active=false,_body,resize_fn;var methods={settings:{},init:function(settings)
{methods.settings=$.extend({},$.fn[plugin_name].defaults,settings);_this=$(this);_window=$(window);_body=$('body');if(_this!=undefined)
{pos_start=Math.round(_this.offset().top+_this.height()+methods.settings.add_offset);methods.do_scroll();_window.bind('scroll',methods.do_scroll);resize_fn=function(fn){clearTimeout(fn);fn=setTimeout(methods.refresh,100);};_window.on('resize',resize_fn);}},refresh:function()
{if(active===false)
{pos_start=Math.round(_this.offset().top+_this.height()+methods.settings.add_offset);}
methods.do_scroll();},do_scroll:function()
{if(_window.scrollTop()>pos_start&&!_body.hasClass('mobile')&&!_body.hasClass('tablet-small')&&!_body.hasClass('loaded-tablet'))
{if(active===false)
{active=true;_this.addClass(methods.settings.active_class).animate({'top':0},methods.settings.speed,methods.settings.easing);methods.settings.scroll_active_fn.apply(this);}}
else
{active=false;_this.removeClass(methods.settings.active_class).attr('style','');methods.settings.scroll_deactive_fn.apply(this);}},destory:function(){active=false;_this.removeClass(methods.settings.active_class).attr('style','');methods.settings.scroll_deactive_fn.apply(this);_window.unbind('scroll',methods.do_scroll);_window.on('resize',resize_fn);}};$.fn[plugin_name]=function(method){if(methods[method])
{if(_this!=undefined)
{return methods[method].apply(this,Array.prototype.slice.call(arguments,1));}}
else if(typeof method==='object'||!method)
{return methods.init.apply(this,arguments);}
else
{$.error('Method '+method+' does not exist on jQuery.'+[plugin_name]);}}
$.fn[plugin_name].defaults={fx:'slide',add_offset:10,speed:500,easing:'swing',add_top_link:false,active_class:'fixed',scroll_active_fn:function(){},scroll_deactive_fn:function(){},};})(jQuery,window);;(function($,window,document)
{var plugin_name='fs_slides',methods={init:function(settings)
{return $(this).each(function(){var _slide=$(this),data=_slide.data(plugin_name),opts=$.extend({},$.fn[plugin_name].defaults,settings);if(!data)
{_slide.data(plugin_name,{target:_slide,opts:opts,remaining:opts.speed,images:_slide.find(opts.image),first:_slide.find(opts.image).first(),wrap:_slide.find(opts.wrap),refreshed:false});}
else
{_slide.data(plugin_name).refreshed==false;}
_slide.on({click:function()
{methods.next(_slide);methods.reset(_slide);_slide.data(plugin_name).start=new Date();},mouseenter:function()
{methods.pause(_slide);},mouseleave:function()
{methods.resume(_slide);}});$(document).ready(function()
{methods.refresh(_slide);methods.load(_slide,_slide.data(plugin_name).first);methods.autoplay(_slide);});$(window).on('resize',function(){clearTimeout(_slide.data(plugin_name).resize_fn);_slide.data(plugin_name).resize_fn=setTimeout(function(){methods.refresh(_slide);},100);});});},refresh:function(_slides)
{var parent_width=_slides.parent().width();var max_width=_slides.data(plugin_name).opts.max_width;if(max_width==0){max_width=parent_width;}
var image=_slides.data(plugin_name).images.first().find('img');if(image[0].naturalWidth!=undefined&&image[0].naturalWidth!=0)
{var img_width=image.width();}
if((_slides.data(plugin_name).opts.width==0&&parent_width>=max_width)||(_slides.data(plugin_name).opts.width>=max_width&&parent_width>=max_width)||(parent_width>max_width&&_slides.data(plugin_name).opts.width<max_width))
{_slides.data(plugin_name).opts.width=max_width;}
else if(parent_width<=max_width)
{_slides.data(plugin_name).opts.width=parent_width;}
_slides.data(plugin_name).images.width(_slides.data(plugin_name).opts.width);_slides.css({'width':_slides.data(plugin_name).opts.width,'height':'auto'});_slides.data(plugin_name).wrap.width(_slides.data(plugin_name).images.length*_slides.data(plugin_name).opts.width);methods.first(_slides);},load:function(_this,image)
{var _img=image.find('img');if(!_img.attr('src')||(_img.attr('src')!=_img.attr('data-src')&&_img.attr('src')!=_img.attr('data-mobile-src')))
{if(_img.data('mobile-src')!=undefined&&$('body').hasClass('mobile'))
{_img.attr('src',_img.data('mobile-src'));}
else
{_img.attr('src',_img.data('src'));}
if(_img.height()!='0')
{image.addClass('loaded');if(_this.data(plugin_name).refreshed!==true)
{_this.data(plugin_name).refreshed=true;methods.refresh(_this);}
var next=image.next(_this.data(plugin_name).opts.image);var size=_this.data(plugin_name).images.size();var index=_this.data(plugin_name).images.index(next);if(next.find('img').data('src')!=undefined&&!next.hasClass('loaded'))
{methods.load(_this,next);}
else
{while(index>-1&&index<size&&(next.find('img').data('src')==undefined||!next.hasClass('loaded')))
{next=next.next(_this.data(plugin_name).opts.image);index=_this.data(plugin_name).images.index(next);if(next.find('img').data('src')!=undefined&&!next.hasClass('loaded'))
{methods.load(_this,next);break;}}}}
else
{_img.load(function()
{image.addClass('loaded');if(_this.data(plugin_name).refreshed!=true)
{_this.data(plugin_name).refreshed=true;methods.refresh(_this);}
methods.load(_this,image.next(_this.data(plugin_name).image));});}}
else
{image.addClass('loaded');if(_this.data(plugin_name).refreshed!==true)
{_this.data(plugin_name).refreshed=true;methods.refresh(_this);}}},autoplay:function(_this)
{_this.data(plugin_name).start=new Date();methods.resume(_this);},pause:function(_this)
{window.clearInterval(_this.data(plugin_name).autoplay);_this.data(plugin_name).remaining-=new Date()-_this.data(plugin_name).start;},reset:function(_this)
{_this.data(plugin_name).remaining=_this.data(plugin_name).opts.speed;window.clearInterval(_this.data(plugin_name).autoplay);_this.data(plugin_name).autoplay=window.setInterval(function(){methods.next(_this)},_this.data(plugin_name).remaining);},resume:function(_this)
{window.clearInterval(_this.data(plugin_name).autoplay);_this.data(plugin_name).autoplay=window.setInterval(function()
{methods.next(_this);methods.reset(_this);_this.data(plugin_name).start=new Date();},_this.data(plugin_name).remaining);},next:function(_this)
{var _current=_this.find('.'+_this.data(plugin_name).opts.active);var _next=_current.next(_this.data(plugin_name).opts.image);if(_next.length>0)
{_this.data(plugin_name).wrap.animate({'left':'-='+_this.data(plugin_name).opts.width});_current.removeClass(_this.data(plugin_name).opts.active);_next.addClass(_this.data(plugin_name).opts.active);}
else
{_this.data(plugin_name).wrap.animate({'left':'0'});_current.removeClass(_this.data(plugin_name).opts.active);_this.data(plugin_name).first.addClass(_this.data(plugin_name).opts.active);}},first:function(_this)
{_this.data(plugin_name).current=_this.find('.'+_this.data(plugin_name).opts.active);_this.data(plugin_name).wrap.animate({'left':'0'});if(_this.data(plugin_name).current!=undefined)
{_this.data(plugin_name).current.removeClass(_this.data(plugin_name).opts.active);}
_this.data(plugin_name).first.addClass(_this.data(plugin_name).opts.active);},previous:function(_this)
{},destroy:function()
{$(this).each(function()
{var _slides=$(this);_slides.off("click mouseenter mouseleave");methods.pause(_slides);});}};$.fn[plugin_name]=function(method)
{var settings=arguments;if(methods[method])
{return $(this).each(function(){methods[method].apply(this,Array.prototype.slice.call(settings,1));});}
else if(typeof method==='object'||!method)
{return $(this).each(function(){methods.init.apply(this,settings);});}
else
{$.error('Method '+method+' does not exist on jQuery.'+plugin_name);}}
$.fn[plugin_name].defaults={fx:'slide',loaded:'loaded',active:'active',wrap:'.image-wrap',image:'.slide',width:0,height:0,max_width:0,min_height:0,speed:5000,easing:'swing'};})(jQuery,window,document);$(function(){var _logo=$('#logo');pages.portfolio_item={};pages.portfolio_item.init=function()
{var _portfolio_item=$('.current-page').find('.portfolio-item'),_section_menu=_portfolio_item.find('.section-menu'),_equalize=_portfolio_item.find('.equalize');$.fs_load(function(){if(_section_menu.length>0)
{_section_menu.fs_sticky_top({scroll_active_fn:function(){_logo.stop().animate({'marginTop':'50px'},300);},scroll_deactive_fn:function(){if(!gCache.body.hasClass('mobile')&&!gCache.body.hasClass('tablet-small')&&!gCache.body.hasClass('loaded-tablet'))
{_logo.stop().animate({'marginTop':'20px'},300);}
else
{_logo.stop().animate({'marginTop':'0'},300);}}});_section_menu.find('a').fs_anchor();setTimeout(function()
{_equalize.fs_equal_height();},100);}
_portfolio_item.find('.slideshow').fs_slides({'min_height':150});});$.fs_resize(function()
{_equalize.fs_equal_height();});};pages.portfolio_item.destroy=function()
{$('.section-menu').fs_sticky_top('destory');$('.slideshow').fs_slides('destroy');};if(pages.loaded!=true)
{pages.portfolio_item.init();}});