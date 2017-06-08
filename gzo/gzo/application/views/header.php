<?=doctype('html5')."\n"; ?>
<html>
<head>
<?	
echo favicon('media/layout/favicon.ico','media/layout/favicon.png');
echo meta(array('description'=>config('description')));
echo css('screen', TRUE);
echo title(config('title').' | GZO Oberflächentechnik GmbH');
echo "\n";
?>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600' rel='stylesheet' type='text/css'>
</head>
<body<?=variable($body_id).variable($body_class); ?>>
<div id ="bg_bottom">
	<div id="logo_lines">
		<div id="centered">
			<?=logo(array('alt' => 'GZO Oberflächentechnik GmbH - return to homepage', 'url' => 'home', 'file' => 'media/layout/gzo_logo.png'))."\n"; ?>
			<?=variable($menu['lang']); ?>
			<div id="header">
				<div class="slideshow" id="ss__wrapper">
				<?
				// default slideshow
				if($header == null)
				{
					// $slideshow = '/slideshow';
					$header = 'slideshow/';
				}
		
				if(isset($header) && substr($header,-1) == '/')
				{
					$CI = &get_instance();
					$CI->load->helper("file");
			
					$arr = get_filenames('./media/images/'.$header);
			
			
					foreach(get_filenames('./media/images/'.$header) as $file)
					{
						if(substr($file,-4) == '.jpg')
						{
							$array[] = $file;
						}
					}
			
					shuffle($array);

					foreach($array as $id => $item)
					{
						echo '<img src="'.base_url(TRUE).'media/images/'.$header.$item.'" width="950" height="350" alt="r2 gmbh - '.$item.'">';
					}
				}
				else
				{
					echo '<img src="'.base_url(TRUE).'media/images/'.$header.'" width="950" height="350" alt="r2 gmbh - '.$header.'">';
				}?>
				</div>
			</div>
			<div id="container">	
					<div id="content_container">
						<div id="sidebar"><?=variable($menu['main']); ?></div>
						<div id="main_content">
