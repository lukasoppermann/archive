<!-- Product Details -->
<div class="product-details">
	<!-- Headline -->
	<h1>
		<span class="designer"><?=$label?></span>
		<span class="product-name"><?=$title?></span>
	</h1>
	<!-- Description -->
	<div class="description">
		<p><?=$text?></p>
		<!-- <? if(isset($sizes) && count(array_filter($sizes)) > 0 )
		{
			echo '<p>Small to size. See Size & Fit table</p>';
		}?> -->
		<?if(variable($product_code) != null){
		echo "<p>Product code: ".variable($product_code)."</p>";	
		}?>		
	</div>
	<!-- Product Order -->	
	<div class="product-order">
		<!-- Options -->
		<div class="options">
			<? if(isset($sizes) && count(array_filter($sizes)) > 0 )
			{
				echo '<select class="select-options">
				<option>Choose your size</option>';
				foreach($sizes as $size)
				{
					echo '<option value="'.$size.'">'.$size.'</option>';
				}
				echo '<select>
				<div class="what-size-am-i">
					<a href="#what-size-am-i" class="dialog-box-link" data-dialog-type="what-size-am-i">What size am I?</a>
				</div>';
			}
			?>
		</div>
		<!-- Order Buttons -->
		<div class="order-buttons">
			<!-- Define current url -->
			<?if( isset($store) )
			{
				$_store = variable($store[key($store)]);
			}
			if( isset($sales_start) && $sales_start != null)
			{
				// explode date dd/mm/yy
				$start 	= explode('/',$sales_start);
				$end 	= explode('/',$sales_end);
				// check if sale is active
				if( mktime(0,0,0, $start[1], $start[0], $start[2]) <= time() && ( count($end) == 0 || mktime(0,0,0, $end[1], $end[0], $end[2]) >= time() ) )
				{
					$_store = 'sale';
				}
			}
			$cur_link = base_url().$_store;
			?>
			<!-- End Current Link -->
			<? if($current_store != 'instore' && isset($product_stock) && $product_stock > 0){ 
				if( $_store == 'sale' ){ echo '<div class="old-price-wrapper"><span class="orignial-price">original price:</span> <a class="old-price">$ '.$price.'</a></div>'; }
			?>
				<div class="add-to-cart">
					<? if( variable($price) != null || ( variable($sales_price) != null && $_store == 'sale') )
					{
						$_price = variable($price);
						if($_store == 'sale' && variable($sales_price) != null)
						{
							$_price = variable($sales_price);
						}
						echo '<div class="price">$ '.$_price.'<span class="tax">(inc. gst.)</span><span class="arrow"></span></div>
								<div class="button" id="add_to_cart" data-product-id="'.$id.'">Add to cart</div>';
					}?>
				</div>
			<?}?>
			<!-- Inquiry -->
			<div class="contact-us button dialog-box-link" data-dialog-type="contact-us" data-url="<?=$cur_link ?>/#<?=$id?>">inquire</div>
		<? if( ( variable($price) != null || ( $_store == 'sale' && variable($sales_price) != null )) && $current_store != 'instore' && isset($product_stock) && $product_stock > 0 ){ echo '</div>'; }?>
			<!-- Tweet -->
			<div id="custom-tweet-button">
				<a href="https://twitter.com/share?url=<?=$cur_link ?>/%23<?="".$id?>&related=LoulaNews&hashtags=loulaShoes&via=LoulaNews&text=Check%20out%20<?=urlencode($title.' by '.$label)?>%20at%20Loula&" target="_blank">Tweet</a>
			</div>
			<!-- Facebook -->
			<div id="custom-fb-button">
				<iframe src="http://www.facebook.com/plugins/like.php?href=<?=urlencode($cur_link.'/#'.$id) ?>&amp;layout=standard&amp;show_faces=false&amp;width=50&amp;action=like&amp;font=tahoma&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:65px">
				</iframe>
			</div>
			<!-- Close Box -->
		<!-- Close Order Buttons -->	
		<? if( (variable($price) == null && $_store != 'sale') || ( $_store == 'sale' && variable($sales_price) == null ) || $current_store == 'instore' || (!isset($product_stock) || $product_stock <= 0)){ echo '</div>'; }?>
	<!-- Close Product Order -->	
	</div>
</div>	
<!-- Get Gallery Images -->
<? 
if(isset($images) && is_array($images))
{
	$c = 0;
	unset($images['hero']);
	foreach($images as $img)
	{
		$big[$c] = '<img class="big-image" src="'.base_url().'media/images/'.$img['filename'].'" alt="'.$img['alt'].' - Loula, Melbourne" />';	
		$small[$c] = '<div class="shot" data-slide="'.$c.'">'.
						'<img width="100" height="100" src="'.base_url().'media/images/'.$img['thumb_150'].'" alt="shoe name - front" />'.
					'</div>';
		$c++;
	}?>
<!-- Create Slideshow -->
<div id="slideshow">
	<? if(isset($big) && count($big) > 0) {
		echo implode('', $big);
	}else{
		echo '<img class="big-image" src="'.base_url().'media/layout/big-no-image.jpg" alt="Loula, Melbourne" />';
	}?>
</div>
<!-- Add Product Shots -->
<div class="product-shots">
	<? if(isset($small) && is_array($small)) {
		echo implode('', $small);
	}?>
</div>
<!-- Images not set -->
<? }
else
{
	echo '<div id="slideshow">';
		if(isset($big) && count($big) > 0) {
			echo implode('', $big);
		}else{
			echo '<img class="big-image" src="'.base_url().'media/layout/big-no-image.jpg" alt="Loula, Melbourne" />';
		}
	echo '</div>';
}
?>