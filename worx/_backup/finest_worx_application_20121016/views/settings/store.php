<li class="store" data-id="<?=variable($id)?>">
	<form data-type="store" class="collapse" name="store">
	<h2 class="headline">Edit Store: <?=variable($name)?> (<?=variable($store_nr)?>)</h2>
	<div class="collapsable">
		<div class="column">
			<div class="form-element full">
				<input type="text" data-required="required" class="autosave-store" name="name" value="<?=variable($name)?>" placeholder="Name / Title of the shop" />
			</div>
			<div class="form-element full">
				<input type="text" data-required="required" class="autosave-store" name="email" value="<?=variable($email)?>" placeholder="email address" />
			</div>
			<div class="form-element full">	
				<input type="text" data-required="required" class="autosave-store" name="phone" value="<?=variable($phone)?>" placeholder="phone number" />
			</div>
			<div class="form-element full">
				<textarea class="autosave-store" data-required="required" name="address" placeholder="address"><?=str_replace('<br />', "\n",variable($address))?></textarea>
			</div>
		</div>
		<div class="column">	
			<div class="form-element full">
				<input type="text"  data-required="required" data-alphanum="alphanum" class="autosave-store" name="permalink" value="<?=variable($permalink)?>" placeholder="permalink" />
			</div>
			<div class="form-element full">
				<textarea class="autosave-store" name="additional_address" placeholder="additional address information"><?=str_replace('<br />', "\n",variable($additional_address))?></textarea>
			</div>
			<div class="form-element full">
				<textarea class="autosave-store" name="trading_hours" placeholder="trading hours"><?=str_replace('<br />', "\n",variable($trading_hours))?></textarea>
			</div>
			<div class="form-element full">
				<div class="button delete cancel">delete store</div>
			</div>
		</div>
	</div>	
	</form>
</li>