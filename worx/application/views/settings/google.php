<form class="column" data-type="google_analytics">
	<h2 class="headline">Google Analytics Tracking Code</h2>
	<div class="form-element width200">
		<label for="analytics_code">Analytics Code</label><input type="text" class="autosave" name="code" value="<?=variable($settings['google_analytics'][0]['code'])?>" placeholder="UA-XXXXXXXX-X" />
	</div>
	<h2 class="headline">Google Analytics Login Credentials</h2>
	<div class="form-element width200">
		<label for="user">User Name</label><input type="text" class="autosave" name="user" value="<?=variable($settings['google_analytics'][0]['user'])?>" placeholder="user@googlemail.com" />
	</div>
	<div class="form-element width200">
		<label for="password">Password</label><input type="password" class="autosave" name="password" value="<?=variable($settings['google_analytics'][0]['password'])?>" placeholder="password" />
	</div>
</form>