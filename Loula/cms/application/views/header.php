<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Lukas Oppermann - veare.net" />
    <meta name="developer" content="Lukas Oppermann - veare.net" />
    <meta name="description" content="" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="language" content="en" />
    <link type="text/plain" rel="author" href="<?=base_url();?>humans.txt" />    
    <link rel="stylesheet" type="text/css" href="<?=base_url();?>libs/css/screen.css" media="screen" />
    <title><?=variable($page_title); ?> | Steele Works CMS</title>
</head>
<body id="<?=$current?>">
	<div id="header">
	    <div id="nav">
	        <?=$nav;?>
	    </div>
		<div id="subnav">
			<?=variable($view)?>
			<div id="user_menu">
				<? if(user('user_id') != null){ ?>
					<a href="<?=base_url();?>settings/personal">Welcome, <?=ucfirst(variable($user['data']['firstname'])).' '.ucfirst(variable($user['data']['lastname']))?></a>
					<a href="<?=base_url();?>logout">(logout)</a>
				<? } ?>
			</div>
		</div>
	</div>
	<!-- close header div -->
			<?=fs_show_log();?>
	<div id="content">