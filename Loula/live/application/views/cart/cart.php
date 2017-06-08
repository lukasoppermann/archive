<div id="cart_overlay" style="display:none;"></div>
<h1>Your Cart at Loula</h1>
<!-- Cart Column Names -->
<div class="cart-headlines">
	<div class="image">
	</div>
	<div class="name">
		Product name & description
	</div>
	<div class="size">
		Size
	</div>
	<div class="quantity">
		Quantity
	</div>
	<div class="price">
		Price per product
	</div>
</div>
<!-- ITEMS -->
<?=variable($items); ?>
<!-- END ITEMS -->
<!-- Update Button -->
<div class="bottom">

		<img class="paypal-accept-top" alt="we accept paypal and credit cards with paypal" src="<?=base_url()?>media/layout/paypal.gif" />

	<div class="right">
		<div class="total-price">$ <span class="total-amount"><?=number_format($total, 2, '.', '')?></span><span class="tax">(inc. gst.)</span><span class="arrow"></span></div>
		<div class="checkout button">checkout</div>
	</div>
</div>
<div id="error_bubble" style="display:none">
	<p>Please select a size for all products</p>
</div>
<div id="confirm_bubble" style="display:none">
	<p class="total-text">The total amount of your order is</p>
	<span class='total'>$ <span class="amnt"><?=number_format($total, 2, '.', '')?></span></span>
	<p class="confirm-text">Please press "pay now" to confirm and pay via paypal or credit card.</p>
	<form action="<?=$this->config->item('paypal_form_url')?>" method="post">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="return" value="<?=base_url().'home/confirm'?>" />
		<input type='hidden' name='address_override' value='1'>
		<input type="hidden" name="notify_url" value="<?=base_url().'ipn'?>">
		<input type="hidden" name="business" value="<?=$this->config->item('paypal_seller')?>">
		<input type="hidden" id="paypalamount" name="amount" value="<?=number_format($total, 2, '.', '')?>">
		<input type="hidden" name="currency_code" value="<?=$this->config->item('paypal_currency')?>">
		<input type="hidden" name="no_shipping" value="2">
	    <input name="first_name" type="hidden" value="Customer's First Name" />
	    <input name="last_name" type="hidden" value="Customer's Last Name" />
		<input type="hidden" name="item_name" value="Loula Shoes Australia">
		<input type="hidden" id="item_number" name="item_number" value="<?=time()?>">
		<input type="hidden" name="hosted_button_id" value="ZNWAMSCMEZ5AA">
		<input type="submit" name="submit" class="paynow button" value="Pay Now">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1"><br style="clear:left;" />
		<img class="paypal-accept" alt="we accept paypal and credit cards with paypal" src="<?=base_url()?>media/layout/paypal.gif" />		
	</form>
</div>