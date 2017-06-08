<!--/////NICHT ÄNDERN!!!//////////////////////////////-->
	{include file="../templates_de/header.tpl"}
<!--/////////////////////////////////////////////////////////////////-->
<ul id="sitemap_list">
	{foreach from=$sitemap_menue key=myId item=menue}
		  	<li><a href="{$menue.path}?id={$menue.seite}" class="sitemap_menue">{$menue.label}</a></li>
			{foreach name=outer item=sitemap2 from=$sitemap_sub}
				{foreach key=myId item=submenue from=$sitemap2}
					{if $submenue.seite == $menue.seite}
  						<li><a href="{$submenue.path}?id={$submenue.seite}&amp;&amp;subid={$submenue.subseite}" class="sitemap_submenue">{$submenue.label}</a></li>
					{/if}	
				{/foreach}
			{/foreach}
	{/foreach}
</ul>
<!--/////NICHT ÄNDERN!!!//////////////////////////////-->
	{include file="../templates_de/footer.tpl"}
<!--/////////////////////////////////////////////////////////////////-->
