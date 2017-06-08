<?=doctype('html5')."\n"; ?>
<?=html()."\n"; ?>
<head>
<?
echo favicon('favicon');
echo meta(array('description' => variable($meta_description), 'keywords' => variable($tags)));
echo css('default', TRUE);
// echo fs_debug_print_css();
echo title((variable($meta_title) != null ? trim($meta_title) : 'Welcome').' | Finest Alterations');
?>
</head>
<body<?=variable($body_id).variable($body_class); ?>>
<div id="page_wrapper">
	<div id="header">
		<?=logo(array('file' => media('finestalterations_sprite.png', 'layout'), 'alt' => 'Finest Alterations - Clothing specialist', 'url' => active_url(TRUE).'home'))."\n"; ?>
		<?=variable($menu['main'])?>
		<div id="inquire">
			<div class="inquire-button">Get Quote</div>
			<ul class="store-list">
				<?=$inquire_menu?>
			</ul>
		</div>
	</div>
		<div id="content">