<div class="widget">
	<div class="widget-content">
		<a class="post-teaser-headline" href="<?=base_url().'en/blog/'.$permalink?>"><h3><?=$title?></h3><span class="icon open"></span></a>
		<!-- Teaser Content -->
		<p><?=variable($excerpt)?></p>
		<!-- Additional Options -->
		<div class="time"><?=$date?></div>
		<div class="tags"><?=show_tags($tags, 3)?></div>
	</div>
</div>