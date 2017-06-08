<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
<head>	
<?=meta(array(	'charset' => 'utf8', 
				'copyright' => date('Y').' by Form&System - formandsystem.com', 
				'developer' => 'Lukas Oppermann - veare.net')); 
?>
<?=favicon('media/layout/favicon.ico'); ?>
<?=$this->stylesheet->output(FALSE); ?>
	<title><?=!empty($title) ? $title : ''; ?> | Form&amp;System</title>
</head>
<body<?=!empty($page_id) ? ' id="'.$page_id.'"' : ''; ?><?=!empty($page_class) ? ' class="'.$page_class.'"' : ''; ?>>
<div id="container">	
	<div id="header" class="text-lighter header-gradiant">
<?=logo('media/layout/formandsystem_logo.png', 'http://www.formandsystem.com','Form&amp;System - Content Management System (CMS) for professional webdesign solutions'); ?>
	<?=!empty($main_menu) ? $main_menu : ''; ?>
	<?=!empty($top_right) ? $top_right : ''; ?>
</div>
<? if(!empty($breadcrumbs) || !empty($bread_right)) { ?>
	<div id="breadcrumbs" class=" breadcrumbs-gradiant tcl4 scl2"><?=!empty($breadcrumbs) ? $breadcrumbs : ''; ?>
	<div class="right"><?=!empty($bread_right) ? $bread_right : ''; ?></div>
	</div>
	<? } ?>
<div id="notices">
	<?=!empty($message) ? $message : ''; ?>
</div>