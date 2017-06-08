<div class="blog_teaser">
	<div class="blog_text">
		<h3><?=$title?></h3>
		<div class="excerpt">
			<?=$excerpt?>
		</div>
		<? if(isset($permalink)){ ?>
		<span class="referral"><a href="<?=current_url().$permalink?>" class="link"><?=lang('read_more')?></a></span>
		<? } ?>
	</div>
	<? if(isset($image) && $image != null){ ?>
	<div class="blog_img">
			<img alt="<?=$title?>" src="http://www.gzo-gmbh.com/media/images/<?=$image?>">
	</div>
	<? } ?>
</div>