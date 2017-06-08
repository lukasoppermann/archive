<a href="<?=lang_url(TRUE).'news/'.$id?>" class="teaser">
	<h4 class="headline"><?=$title?></h4>
	<img alt="<?=$title?>" src="<?=base_url()?>media/images/<?$news_img?>" />
	<div class="teaser-content">
		<?=trim_text($text, 220, array('tags' => TRUE))?>
	</div>
</a>