<article class="portfolio-item article open_sans">
	<div class="type column-five whitespace <?=variable($category)?>"><?=(str_replace('-',' ',variable($category)))?></div>
	<?=variable($text)?>
	<div class="tags whitespace">
		<div class="tags-container">
			<span class="icon tag"></span><?=show_tags($tags, 5, false,', ')?>
		</div>
	</div>	
</article>