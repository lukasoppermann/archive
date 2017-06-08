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
	<title>Angelika Oppermann</title>
</head>
<body>
	<div id="head">
		<div id="titel">
		   <a href="./index.php">Angelika Oppermann</a>
		</div>
	    <ul id="menue">
			<li><a href="./keramik.php">Keramik</a></li>
			<li><a class="active" href="./impressum.php">Impressum</a></li>
		</ul>
	    <div id="topline">
	    </div>
	</div>	
		<div id="heading"></div>
		<br />
		<br />
		<br />
		<br style="clear:both;"/>
		<div id="box">
			<div style="background-image: url('images/angelika.jpg'); width: 850px; height: 450px;">
			<div id="impressum" style="height: 440px; margin-top: 0px;">	
			<?
			  echo "<table cellspacing=\"5\" cellpadding=\"5\" valign=\"top\" align=\"center\">
			                <tr valign=\"top\">
			                <td>
			                <form method=\"post\" action=\"$SELF_PHP\">
			                <table cellspacing=\"4\" cellpadding=\"4\" valign=\"top\">
			                <tr><td></td><td>Angelika Oppermann<br><br>
			                Danziger Straße 11<br>
			                10435 Berlin<br>
			                +49 176 62563976
			                <br>
			                <br>
			                <a href=\"mailto:mail@angelika-oppermann.de\">mail@angelika-oppermann.de</a><br><br><br> </td></tr>

			                <tr>
			                <td></td>
			                </tr>
			                <tr>
			                <td>Absender:</td>
			                <td><input type=\"text\" name=\"from\" size=\"36\"></td>
			                </tr>
			                <tr>
			                <td>Betreff:</td>
			                <td><input type=\"text\" name=\"bet\" value=\"\" size=\"36\"></td>
			                </tr>
			                <tr>
			                <td valign=\"top\">Nachricht:</td>
			                <td><textarea rows=\"8\" cols=\"27\" name=\"text\"></textarea></td>
			                </tr>
			                <tr>
			                <td></td>
			                <td><input type=\"submit\" value=\"senden\" name=\"submit\" size=\"50\">
			                <input type=\"reset\" value=\"löschen\" name=\"reset\" size=\"50\"></td>
			                </tr>
			                </table>
			                </form>";
			        if(!($_POST[submit])){
			                echo "";
			        }elseif ($_POST[submit] && $_POST[text]!="" && $_POST[bet]!=""){
			                mail('mail@angelika-oppermann.de', $_POST[bet],$_POST[text],"From:" . $_POST[from]);
			                echo "<font>" . "Der Brief wurde erfolgreich überbracht." . "</font>";
			        }elseif($_POST[submit] || $_POST[text]=="" || $_POST[bet]==""){
			                echo "Es war uns leider nicht möglich den Brief zu überbringen, es muss alles angegeben werden.";
			        }
			        echo "                </td>
			                </tr>
			                </table>";
			?>
			</div>
			</div>
		</div>
		<script type="text/javascript">

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