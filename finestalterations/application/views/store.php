<div id="map_wrapper">
	<div id="map">
	</div>
</div>
<div class="column first store-info">
	<h2 class="main-headline"><?=variable($name)?></h2>
	<a target="_blank" href="https://maps.google.com/maps?daddr=<?=str_replace(" ",'+',str_replace('<br />',' ',variable($address)))?>+Australia" class="address"><?=variable($address)?></a>
	<div class="address-addition"><?=(variable($additional_address) != "" ? $additional_address."<br />" : "")?></div>
	<div class="phone">Telephone: <?=variable($phone)?></div>
	<div class="email">Email: <?=safe_mailto(variable($email))?></div>
	<div class="trading-hours">
		<? if( isset($trading_hours) && $trading_hours != "" ){ ?>
		<h3>Trading Hours</h3>
		<div class="hours">
			<?=$trading_hours?>
		</div>
		<? } ?>
	</div>
</div>
<div class="column">
	<form id="inquiry_form" class="email-form" data-store="<?=variable($id)?>">
		<h3>Inquire</h3>
		<div class="input input-button">
			<input type="text" name="customer_name" placeholder="Full name (required)" value="Full name (required)" />
			<div class="submit" id="submit">Send</div>
		</div>
		<div class="input">
			<input type="text" name="customer_email" placeholder="Email address (required)" value="Email address (required)" />
		</div>
		<div class="input">
			<input type="text" name="customer_phone" placeholder="Phone number (if preferred contact)" value="Phone number (if preferred contact)" />
		</div>
		<div class="textarea">
			<textarea name="text" placeholder="Your message (required)">Your message (required)</textarea>
		</div>
	</form>
</div>