<h2 class="headline">Contact Information</h2>
<form data-type="contact">
	<div class="column">
		<div class="form-element full">
			<input class="autosave" type="text" name="company" value="<?=variable($settings['contact'][0]['company'])?>" placeholder="Company name" />
		</div>
		<div class="form-element full">
			<input class="autosave" type="text" name="name" value="<?=variable($settings['contact'][0]['name'])?>" placeholder="Name / Title of the shop" />
		</div>
		<div class="form-element full">
			<input class="autosave" type="text" name="email" value="<?=variable($settings['contact'][0]['email'])?>" placeholder="Email address" />
		</div>
		<div class="form-element full">	
			<input class="autosave" type="text" name="phone" value="<?=variable($settings['contact'][0]['phone'])?>" placeholder="Phone number" />
		</div>
	</div>
	<div class="column">	
		<div class="form-element full">
			<textarea class="autosave" name="address" placeholder="address"><?=str_replace('<br />', "\n",variable($settings['contact'][0]['address']))?></textarea>
		</div>
		<div class="form-element full">
			<textarea class="autosave" name="additional_address" placeholder="additional address information"><?=str_replace('<br />', "\n",variable($settings['contact'][0]['additional_address']))?></textarea>
		</div>
	</div>	
</form>

<form data-type="new_store" class="collapse collapsed" name="new_store">
<h2 class="headline">Add New Store</h2>
<div class="collapsable" style="display: none;">
	<div class="column">
		<div class="form-element full">
			<input type="text" name="name" value="" placeholder="Name / Title of the shop" />
		</div>
		<div class="form-element full">
			<input type="text" name="email" value="" placeholder="email address" />
		</div>
		<div class="form-element full">	
			<input type="text" name="phone" value="" placeholder="phone number" />
		</div>
		<div class="form-element full">
			<textarea name="address" placeholder="address"></textarea>
		</div>
	</div>
	<div class="column">	
		<div class="form-element full">	
			<input type="text" name="permalink" value="" placeholder="permalink" />
		</div>
		<div class="form-element full">
			<textarea name="additional_address" placeholder="additional address information"></textarea>
		</div>
		<div class="form-element full">
			<textarea name="trading_hours" placeholder="trading hours"></textarea>
		</div>
		<div class="form-element full">
			<div id="add_store" class="button">add store</div>
		</div>
	</div>
</div>	
</form>
<ul class="stores">
	<?=variable($stores)?>
</ul>