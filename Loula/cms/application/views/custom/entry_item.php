<li class="item" data-id="<?=$id?>" data-status="<?=variable($status)?>" data-type="<?=variable($type)?>" 
<? if(variable($type) == 2){?>data-designer="<?=variable($data['designer'])?>" data-product="<?=variable($data['product_type'])?>"<?}?>>
	<div class="level_1">
		<a class="title-link" href="<?=base_url().'content/edit/'.$id?>"><h3><?=variable($title)?></h3></a>
		<div class="time"><span><?=variable($data['last_saved'])?></span></div>
		<div class="status status-<?=variable($status)?>"></div>
		<div class="delete">
		<? if(variable($type) == "1" || variable($type) == "2"){?>
			<a href="<?=base_url().'ajax/entry/delete/'?>"></a>
		<? } ?>
		</div>
		<div class="edit"><a href="<?=base_url().'content/edit/'.$id?>"></a></div>		
	</div>	
	<div class="content-excerpt"><?=trim_text(variable($text), 200, array('end_sentence' => false, 'strip_tags' => true))?></div>
</li>