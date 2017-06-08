<div class="user-card widget" data-user-id="<?=$id?>">
	<div class="widget-content">
		<div class="user-image">
			<div class="image-overlay">
			</div>
		</div>
		<div class="user-card-content">
			<h4 class="name"><?=ucfirst($firstname).' '.ucfirst($lastname)?></h4>
			<span class="username"><?=$user?></span>
			<span class="email"><?=$email?></span>
			<span class="role"><?=config('user/group/'.$group.'/name')?></span>
		</div>
	</div>
</div>