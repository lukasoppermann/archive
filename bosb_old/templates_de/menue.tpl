<ul id="menue">
	{foreach from=$menue key=myId item=menue}
	  	{if $menue.seite eq $url}
			<li><a href="{$menue.path}?id={$menue.seite}" class="active">{$menue.label}</a></li>
		{else}
		  	<li><a href="{$menue.path}?id={$menue.seite}" class="passive">{$menue.label}</a></li>
		{/if}
	{/foreach}
</ul>

