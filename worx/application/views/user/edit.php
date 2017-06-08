<h1>Edit User</h1>
<form method="post" accept-charset="utf-8" class="form">
	<input type="hidden" name="id" value="<?=variable($id)?>" />
	<div class="form-element">	
		<label for="user">username</label>
		<input type="text" name="user" value="<?=variable($user)?>" placeholder="username" />
	</div>
	<div class="form-element">
		<label for="name">name</label>
		<input type="text" name="name" value="<?=ucfirst(variable($firstname)).(variable($firstname) ? ' ':'').ucfirst(variable($lastname))?>" placeholder="full name" />
	</div>
	<div class="form-element">
		<label for="email">email</label>
		<input type="text" name="email" value="<?=variable($email)?>" placeholder="email" />
	</div>
	<div class="form-element">
		<label for="pass">new password</label>
		<input type="password" name="pass" value="" placeholder="password" />
	</div>
	<div class="form-element">
		<label for="pass">retype password</label>
		<input type="password" name="re_pass" value="" placeholder="password" />
	</div>
	<!-- Select Group -->
	<div class="form-element percent-50 left">
		<label class="left" for="group">group</label>	
		<select name="group">
		<? foreach($groups as $_group){
 			if( array_key_exists($_group['creator_right'], user('rights')) )
			{
				$selected = ($group['_id'] == $_group['_id']) ? ' selected="selected"' : '';
				echo '<option value="'.$_group['_id'].'"'.$selected.'>'.$_group['name'].'</option>';
			}
		} ?>
		</select>
	</div>
	<div class="form-element percent-50 right">
		<label class="left" for="store">store</label>	
		<select name="store">
			<option value="all">all</option>
		<? foreach($stores as $key => $_store)
		{
			$selected = ($store == $key) ? ' selected="selected"' : '';
			echo '<option value="'.$key.'"'.$selected.'>'.$_store.'</option>';
		} ?>
		</select>
	</div>
	<div class="button delete left">
		delete
	</div>
	<div class="button save right">
		save changes
	</div>
</form>