<?=doctype('html5')."\n"; ?>
<html>
<head>
<?	
echo favicon('media/layout/favicon.ico','media/layout/favicon.png');
echo meta(array('keywords'=>$this->config->item('tags'), 'description'=>$this->config->item('description')));
echo css('print, screen', TRUE);
echo title($this->config->item('title').' | '.$this->config->item('page_name'));
echo "\n";
?>
<link href='http://fonts.googleapis.com/css?family=Actor' rel='stylesheet' type='text/css'>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-7074034-16']);
  _gaq.push(['_setDomainName', 'r2-gmbh.com']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
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
		<? if(!isset($header) || $header == null)
			{
		?>
		<div class="slideshow">

		<?php
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
				echo '<img src="'.base_url(TRUE).'media/images/slideshow/'.$item.'" width="950" height="350" alt="r2 gmbh - '.$item.'">';
			}
			echo "</div>";
		}
		elseif(isset($slideshow) && $header == 'slideshow')
		{
			$CI = &get_instance();
			$CI->load->helper("file");
			
			$arr = get_filenames('./media/images'.$slideshow.'/');
			
			foreach(get_filenames('./media/images'.$slideshow.'/') as $file)
			{
				if(substr($file,-4) == '.jpg')
				{
					$array[] = $file;
				}
			}
			
			shuffle($array);

			echo '<div class="slideshow">';
				foreach($array as $id => $item)
				{
					echo '<img src="'.base_url(TRUE).'media/images'.$slideshow.'/'.$item.'" width="950" height="350" alt="r2 gmbh - '.$item.'">';
				}
			echo "</div>";
		}
		else
		{
			echo '<img src="'.base_url(TRUE).'media/images/'.$header.'" width="950" height="350" alt="r2 gmbh - '.$header.'">';
		}?>
	</div>
		<div id="container">	
			<div id="content_top">
			</div>
			<div id="content">
				<div id="content_container">
					<div id="sidebar">
							<?=variable($menu['main']); ?>
 					</div>
					<div id="main_content">
