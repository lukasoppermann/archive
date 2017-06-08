<?=doctype('html5')."\n"; ?>
<html lang="<?=config('lang_abbr')?>">
<head>
<?
echo favicon('favicon');
echo meta();
echo css('default', TRUE);
echo fs_debug_print_css();
echo title((variable($meta_title) != null ? $meta_title : 'Welcome').' | Worx cms');
?>
</head>
<body<?=variable($body_id).variable($body_class); ?>>
<div id="page_wrapper">
	<div id="header">
		<?=logo(array('file' => media('worx_sprite.png', 'layout'), 'alt' => 'WORX cms', 'url' => base_url(TRUE).'dashboard'))."\n"; ?>
		<?=variable($menu['main'])?>
		<?=variable($menu['user'])?>
	</div>
	<div id="top_draw" style="display: none;">
		<div class="content"></div>
		<div class="loader"></div>
		<div class="button close-draw">close</div>
	</div>
		<div id="content">	
        <!-- DEBUG CONSOLE -->
		    <?=fs_show_log();?>
        <!-- END DEBUG CONSOLE -->		    
		<? if(isset($sidebar))
		{ 
		echo '<div id="sidebar">
				<form name="sidebar" action="#">';
			foreach($sidebar['element'] as $element)
			{
				echo "<div class='sidebar-element-container ".variable($element['class'])."'>";
				if(isset($element['title']))
				{
					echo '<h4 class="sidebar-headline">'.$element['title'].'</h4>';
				}
				if(isset($element['content']))
				{
					echo '<div class="sidebar-element">'.$element['content'].'</div>';
				}
				echo "</div>";
			}
		echo '</form></div>';
		} ?>