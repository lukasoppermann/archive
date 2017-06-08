<li class="item drag" data-id="<?=$id?>" data-status="<?=variable($status)?>" data-type="<?=variable($type)?>" 
<? if(variable($type) == 2){?>data-designer="<?=variable($data['designer'])?>" data-product="<?=variable($data['product_type'])?>"<?}?> data-pos="<?=variable($data['position'])?>">
	<div class="edit"><a href="<?=base_url().'content/edit/'.$id?>"></a></div>	
	<div class="image">
		<img src="<?=$thumb?>" alt="<?=variable($title)?>" />
	</div>	
	<span class="title-link"><h3><?=trim_text(variable($title), 15, array('end_sentence' => false, 'strip_tags' => true))?></h3></span>
	<div class="status status-<?=variable($status)?>"></div>
</li>