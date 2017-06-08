<div class="block text<?=(isset($image) && is_array($image)) ? ' has-image' : ''?><?=(isset($title) && $title != null) || (isset($text) && $text != null ) ? ' has-text' : ''?>">
	<?=((isset($permalink) && $permalink != '') ? '<a class="link" href="'.(substr($permalink,0,4) != 'www.' && substr($permalink,0,4) != 'http' ? base_url(FALSE).$permalink : $permalink).'">' : '')?>	
	<? if(isset($image) && is_array($image)) {?>
	<div class="image">
		<img src="<?=base_url().config('dir_images', TRUE).$image['filename'].'_thumb_280.'.$image['ext']?>" alt="<?=variable($image['filename'])?>">
	</div>
	<?}?>
	<? if( (isset($title) && $title != null) || (isset($text) && $text != null ) ){ ?>
	<div class="text-content">
		<h3 class="headline"><?=variable($title)?></h3>
		<div class="content">
			<?=text_limiter(variable($text), 500)?>
		</div>
	</div>
	<?}?>	
	<?=((isset($permalink) && $permalink != '') ? '</a>' : '')?>
</div>