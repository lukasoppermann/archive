<?=doctype('html5')."\n"; ?>
<html>
<head>
<?	
echo favicon('media/layout/favicon.ico','media/layout/favicon.png');
echo meta(array('keywords'=>$this->config->item('tags'), 'description'=>$this->config->item('description')));
echo css('print, screen', FALSE);
echo title($this->config->item('title').' | '.$this->config->item('page_name'));
echo "\n";
?>
<link href='http://fonts.googleapis.com/css?family=Actor' rel='stylesheet' type='text/css'>
</head>
<body<?=variable($body_id).variable($body_class); ?>>
<div id="centered">
	<div id="top_menu">
		<?=variable($menu['lang']); ?>
		<?=!empty($meta_menu) ? $meta_menu : ''; ?>
	</div>
	<div id="header">
		<div class="overlay"></div>
		<?=logo(array('alt' => 'R2 Felgenveredleung', 'url' => 'home', 'file' => 'media/layout/r2_logo.png'))."\n"; ?>

			<div class="slideshow">
				
				<div class="img">
					<img width="950" height="350" alt="GZO Oberflächentechnik GmbH - Highlights der IAA 2011" src="http://www:8888/gzo_new/media/images/03-Gussteile-8117.jpg" class=""></div>
				<div class="img">
					<img width="950" height="350" alt="GZO Oberflächentechnik GmbH - Highlights der IAA 2011" data-src="http://www:8888/gzo_new/media/images/24-Nockenwelle-140723.jpg" class="later"></div>					
		<!-- <?php
		$array = array(
			"exklusivrad_02.jpg",
			"exklusivrad_poliert.jpg",
			"felge_1_detail_08.jpg",
			"felge_2_detail_04.jpg",
			"felge_3_detail_02.jpg",
			"felge_3_detail_10.jpg",
			"felge_4_detail_02.jpg",
			"felge_5_detail_08.jpg",
			"felge_5_detail_09.jpg",
			"felge_5_detail_14.jpg",
			"felge_5_detail_15.jpg",
			"felge_6_detail_07.jpg",
			"felge_7_detail_09.jpg",
			"felge_7_detail_91.jpg",
			"felgendetail_8062.jpg",
			"felgenkollektion.jpg"
			);
			shuffle($array);
		foreach($array as $id => $item)
		{
			echo '<img src="http://www.r2-gmbh.com/media/images/slideshow/'.$item.'" width="950" height="350" alt="r2 gmbh - '.$item.'">';
		}
		?> -->
		</div>
	</div>
		<div id="container">	
			<div id="content_top">
			</div>
			<div id="content">
				<div id="content_container">
					<div id="sidebar">
							<?=variable($menu['main']); ?>
							<?=variable($link); ?>
 					</div>
					<div id="main_content">
