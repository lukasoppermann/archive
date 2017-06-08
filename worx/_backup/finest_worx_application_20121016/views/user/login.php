<form action="<?=$url?>" method="post" accept-charset="utf-8" name="login" id="login">
	<div class="wrapper">
		<div class="widget login">
			<div class="widget-content">
				<!-- ////////////////////////////////////////////////////////////////////////////////// -->
				<!-- User Image -->
				<div class="user-image cms-profile">
					<div class="overlay"></div>
					<img class="profile-image" src="<?=media('worx_profile.png', 'layout')?>">
				</div>
				<!-- ////////////////////////////////////////////////////////////////////////////////// -->
				<!-- User Name or Email -->
				<div class="form-element one-row<?=(set_value('username') == null ? ' empty' : '')?>">
					<!-- User Input Field -->
					<input class="username input<?=(form_error('username') != null ? ' error' : ''); ?>" 
					type="text" name="username" placeholder="username / email" value="<?=set_value('username')?>" />
				</div>
				<!-- ////////////////////////////////////////////////////////////////////////////////// -->
				<!-- User Password -->
				<div class="form-element one-row<?=(set_value('password') == null ? ' empty' : '')?>">
					<!-- Password Input Field -->
					<input class="password input<?=(form_error('password') != null ? ' error' : ''); ?>" 
					type="password" name="password" placeholder="password" value="<?=set_value('password')?>" />
				</div>
				<!-- ////////////////////////////////////////////////////////////////////////////////// -->
				<!-- Submit Form -->
				<input id="sign_in" class="button" type="submit" value="Sign in" />
				<!-- ////////////////////////////////////////////////////////////////////////////////// -->
				<!-- Close Form -->
			</div>
			<!-- ////////////////////////////////////////////////////////////////////////////////// -->
			<!-- Error Messages -->
			<div class="login-errors <?=(validation_errors() != null ? 'error' : '');?>">
				<?php echo validation_errors('<div class="error">', '</div>'); ?>
			</div>
			<!-- ////////////////////////////////////////////////////////////////////////////////// -->
		</div>
		<!-- closing wrapper -->
	</div>
</form>