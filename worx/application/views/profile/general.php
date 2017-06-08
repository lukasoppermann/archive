<h2 class="headline">Profile Information</h2>
<form id="profile_form" data-page="general">
	<div class="column">
		<div class="form-element full">
			<input type="text" name="name" value="<?=ucFirst(variable($firstname)).' '.ucFirst(variable($lastname))?>" placeholder="First- and lastname" />
		</div>
		<div class="form-element full">
			<input type="text" name="email" value="<?=strtolower(variable($email))?>" placeholder="Email address" />
		</div>
		<div class="form-element full">	
			<input type="password" name="old_password" value="" placeholder="Current password" />
		</div>
		<div class="form-element full">	
			<input type="password" name="new_password" value="" placeholder="New password" /><span class="help">min. length 8 characters</span>
		</div>
		<div class="form-element full">	
			<input type="password" name="re_password" value="" placeholder="Repeat new password" />
		</div>
	</div>
	<div class="form-element full">
		<div id="save" class="button left">save changes</div>
	</div>
</form>