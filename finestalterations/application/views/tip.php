<?
	css_add('multipage, tips');
?>
<div class="left-column">
	<a href="<?=base_url().'tips/'?>" class="back">Return to Post overview</a>
	<?=variable($quotes)?>
</div>
<div class="content-container">
	<div class="content-box">
		<?=variable($banner)?>		
		<div class="site-content">
			<? if(isset($title)){ ?><h2 class="main-headline"><?=$title?></h2><? } ?>
			<?=(isset($text) ? $text : 'No information available.')?>
		</div>
	</div>
</div>