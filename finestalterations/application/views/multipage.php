<?
	css_add('multipage');
?>
<div class="menu-box">
	<h3><?=$headline?></h3>
	<ul class="site-menu">
		<?=variable($site_menu)?>
	</ul>
</div>
<div class="content-container">
	<div class="content-box">
		<?=variable($banner)?>
		<div class="site-content">
		<? if(isset($site_headline)){ ?><h2 class="main-headline"><?=$site_headline?></h2><? } ?>
		<?=(isset($site_content) ? $site_content : 'No information available.')?>
		</div>
	</div>
</div>