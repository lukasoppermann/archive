<?php // get image
	if( isset($images) && count($images) > 0 )
	{
		if( isset($images['hero']) )
		{
			$_image = 'images/'.$images['hero']['thumb_150'];
		}
		else
		{
			$_image = 'images/'.$images[key($images)]['thumb_150'];
		}
	}
	else
	{
		$_image = 'layout/empty_thumb150.jpg';
	}
?>
<!-- Cart Items -->
<div class="cart-item" data-id="<?=$id?>" data-rowid="<?=$rowid?>">
	<div class="image">
		<img src="<?=base_url()?>/media/<?=$_image?>" />
	</div>
	<div class="name">
		<a class="headline" target="_blank" href="<?=base_url()?>/instore/#<?=$id?>"><?=$title?></a>
		<p><?=trim_text(variable($text), 200, array('end_sentence' => false, 'end' => '...', 'strip_tags' => true))?></p>
	</div>
	<div class="size">
			<?
			$sizes = array_filter($sizes);
			if( isset($sizes) && is_array($sizes) && count($sizes) > 0)
			{
				echo '<select class="select-size"><option>Choose your size</option>';
				foreach( $sizes as $size)
				{
					if( $size == $options['size'] )
					{
						echo '<option selected="selected" value="'.$size.'">'.$size.'</option>';
					}
					else
					{
						echo '<option value="'.$size.'">'.$size.'</option>';
					}
				}
				echo '</select>';
			}
			?>
	</div>
	<div class="quantity">
		<?if($product_stock != '' && $product_stock != null)
		{?>
		<select class="select-qty">
			<?
			for( $i = 0; $i < $product_stock+1; $i++)
			{
				if( $i == $qty )
				{
					echo '<option selected="selected" value="'.$i.'">'.$i.'</option>';
				}
				else
				{
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
			}
			?>
		</select>
		<?}
		else
		{
			echo '<select class="select-options"><option value="0">0</option></select>';
		}
		?>
	</div>
	<div class="price">
		AUD <span><?=number_format($price, 2, '.', '')?></span>
	</div>
</div>