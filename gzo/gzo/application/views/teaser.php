<div class="teaser">
	<? if(isset($teaser_image) && $teaser_image != null)
	{
		echo '<div class="teaser-image">
		<img src="'.base_url().'media/images/'.$teaser_image.'" alt="'.$title.'">
		</div>';
	}
	?>
	<?=$teaser?>
	<a href="<?=lang_url()?>news<?=$permalink?>" class="link">mehr lesen</a>
</div>