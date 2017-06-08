<div id="submenue">
	<ul>
		{if $url eq "index"}
			<li>
				<p id="home_quote">
					Wer nicht verändern will,<br />
					wird auch verlieren,<br />
					was er bewahren möchte.<br /><br />
					<i>- Gustav Heinemann -</i>
				</p>
			</li>
			{elseif $url != ""}
				{foreach from=$submenue key=myId item=submenue}
					{if $submenue.subseite eq $suburl}
	  					<li><a href="{$submenue.path}?id={$submenue.seite}&amp;&amp;subid={$submenue.subseite}" class="sub_active">{$submenue.label}</a></li>
						{foreach from=$subsubmenue key=myId item=subsubmenue}
					  		{if $subsubmenue.subsubseite eq $subsuburl}
			  					<li><a href="{$subsubmenue.path}?id={$subsubmenue.seite}&amp;&amp;subid={$subsubmenue.subseite}&amp;&amp;subsubid={$subsubmenue.subsubseite}" class="sub_sub_active"><img src="../media/arrow_blue2.png" alt="{$subsubmenue.label}" />{$subsubmenue.label}</a></li>
							{else}
						  		<li><a href="{$subsubmenue.path}?id={$subsubmenue.seite}&amp;&amp;subid={$subsubmenue.subseite}&amp;&amp;subsubid={$subsubmenue.subsubseite}" class="sub_sub_passive">{$subsubmenue.label}</a></li>
			  				{/if}
						{/foreach}
					{else}
				  		<li><a href="{$submenue.path}?id={$submenue.seite}&amp;&amp;subid={$submenue.subseite}" class="sub_passive">{$submenue.label}</a></li>
	  				{/if}
				{/foreach}
			{/if}
		</ul>
	</div>