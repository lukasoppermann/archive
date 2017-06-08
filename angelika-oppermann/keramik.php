<? header("Content-type: text/html;charset=utf-8"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="author" content="Lukas Oppermann" />
	<meta name="keywords" content="Angelika Oppermann, Keramik" />
	<meta name="description" content="Angelika Oppermann fertigt verschiedenstes Porzellan mit naturbezogenen Motiven und Mustern." />
	<meta name="robots" content="index,follow" />
	<meta name="language" content="de" />
	<link rel="favicon" href="favicon.ico" />
	<link rel="stylesheet" type="text/css" href="screen.css" media="screen" />
	<script src="./js/mootools.v1.11.js" type="text/javascript"></script>
	<script src="./js/sliding-tabs.js" type="text/javascript"></script>

	<title>Angelika Oppermann</title>
</head>
<body>
	<div id="head">
		<div id="titel">
		   <a href="./index.php">Angelika Oppermann</a>
		</div>
	    <ul id="menue">
			<li><a class="active" href="./keramik.php">Keramik</a></li>
			<li><a href="./impressum.php">Impressum</a></li>
		</ul>
	    <div id="topline">
	    </div>
	</div>	
	<div id="heading">
		<ul id="buttons">
			<?PHP
			$fotos = glob('porzellan/' . '*.' . 'jpg');
			foreach($fotos as $foto){
			echo("<li>".$foto."</li>");
			}
			?>
		</ul>
		<img src="images/left.gif" alt="&lt;&lt;" id="previous" /><img src="images/right.gif" alt="&gt;&gt;" id="next" />	
	</div>
	<br />
	<br />
	<br />
	<br style="clear:both;"/>
	<div id="box">
			<div id="wrapper">
				<!-- this section has our panes, unfortunately we need two divs to make the effect work -->
				<div id="panes">
					<div id="content">
						<?PHP
						foreach($fotos as $foto){
							echo("<div class='pane'>
								<p><img src='".$foto."' alt='".$foto."' /></p>
							</div>");
						}
						?>	
					</div>
				</div>
			</div>
	</div>

	<script type="text/javascript" charset="utf-8">
		window.addEvent('load', function () {
			myTabs = new SlidingTabs('buttons', 'panes');		
			// this sets up the previous/next buttons, if you want them
			$('previous').addEvent('click', myTabs.previous.bind(myTabs));
			$('next').addEvent('click', myTabs.next.bind(myTabs));

			// this sets it up to work even if it's width isn't a set amount of pixels
			window.addEvent('resize', myTabs.recalcWidths.bind(myTabs))
		});

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-35402684-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>

</body>
</html>