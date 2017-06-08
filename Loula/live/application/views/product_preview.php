<a href="<?=current_url()?>/#<?=$id?>">
	<div class="item" data-product-id="<?=$id?>" data-type="<?=$product_type?>">
		<div class="name"><?=variable($title)?></div>
		<? if(isset($images['hero']))
		{?>
			<img src="<?=base_url().'/media/images/'.variable($images['hero']['thumb_180'])?>" alt="<?=variable($title)?>" />
		<?} else {?>
			<img src="<?=base_url().'/media/layout/shoe-no-hero.jpg'?>" alt="<?=variable($title)?>" />
		<?}?>
	</div>
</a>