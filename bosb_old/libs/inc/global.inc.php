<?PHP
header("Content-type: text/html;charset=utf-8");
// Smarty must include
    error_reporting(0);
    define('SMARTY_DIR', '../libs/');

// functions start
	function active($string){
		$string = preg_replace('/\/htdocs_([a-z]+)\//','',$string);
		$string = preg_replace('/.php/','',$string);
	return $string;
	}
	if (stristr($_SERVER['PHP_SELF'], 'htdocs_de')){
		$strLang = 'de';
	}elseif (stristr($_SERVER['PHP_SELF'], 'htdocs_en')){
		$strLang = 'en';
	}
// functions end

    require(SMARTY_DIR.'Smarty.class.'.$strLang.'.php');

	$smarty = new Smarty;
// Smarty must include end

	if(isset($_GET['id'])){
		$url = $_GET['id'];
		$smarty->assign('url', $url);
	}
	if(isset($_GET['id'])){
		$url = $_GET['id'];
		$smarty->assign('url_2', $url);
	}
	if(isset($_GET['subid'])){
		$suburl = $_GET['subid'];
		$smarty->assign('suburl', $suburl);
		$smarty->assign('suburl_head', '&&subid='.$suburl);
	}
	if(isset($_GET['subsubid'])){
		$subsuburl = $_GET['subsubid'];
		$smarty->assign('subsuburl', $subsuburl);
		$smarty->assign('subsuburl_head', '&&subsubid='.$subsuburl);
	}
	$smarty->assign('start_url', 'http://www.organisationsberatung.org');

	$smarty->assign('strLang', $strLang);

	if(!isset($url) && active($_SERVER['PHP_SELF'])=="index"){
		$smarty->assign('url_2', 'index');
	}
	// php_page
	if(active($_SERVER['PHP_SELF']) != 'empfehlungen' and $_GET['id'] != 'empfehlungen'){
		$smarty->assign('url_head', active($_SERVER['PHP_SELF']));
	}elseif(active($_SERVER['PHP_SELF']) == 'empfehlungen' or $_GET['id'] == 'empfehlungen'){
		$smarty->assign('url_head', 'index');
		$smarty->assign('url_2', 'index');
		$smarty->assign('suburl_head', '');
		$smarty->assign('subsuburl_head', '');
	}
	if (active($_SERVER['PHP_SELF']) == 'analyse'){
		$smarty->assign('url_head', 'angebote');
		$smarty->assign('url_2', 'angebote');
		$smarty->assign('suburl_head', '');
		$smarty->assign('subsuburl_head', '');
	}
	if (active($_SERVER['PHP_SELF']) == 'praktikum'){
		$smarty->assign('url_head', 'kontakt');
		$smarty->assign('url_2', 'kontakt');
		$smarty->assign('suburl_head', '');
		$smarty->assign('subsuburl_head', '');
	}
// Menue Start
//
// Mainmenue Deutsch
//
	$menue_list['de'][0]['label'] = "Home";
	$menue_list['de'][0]['path'] = "index.php";
	$menue_list['de'][0]['seite'] = "index";

	$menue_list['de'][1]['label'] = "Zur Person";
	$menue_list['de'][1]['path'] = "oppermann.php";
	$menue_list['de'][1]['seite'] = "oppermann";

	$menue_list['de'][2]['label'] = "Angebote";
	$menue_list['de'][2]['path'] = "angebote.php";
	$menue_list['de'][2]['seite'] = "angebote";

  $menue_list['de'][3]['label'] = "Arbeitsfelder";
  $menue_list['de'][3]['path'] = "arbeitsfelder.php";
  $menue_list['de'][3]['seite'] = "arbeitsfelder";

	$menue_list['de'][6]['label'] = "Kontakt";
	$menue_list['de'][6]['path'] = "kontakt.php";
	$menue_list['de'][6]['seite'] = "kontakt";

	$smarty->assign('menue', $menue_list[$strLang]);
//
// Submenue Deutsch
//
	$menue_list['de']['angebote'][0]['label'] = "Organisation";
	$menue_list['de']['angebote'][0]['path'] = "organisation.php";
	$menue_list['de']['angebote'][0]['seite'] = "angebote";
	$menue_list['de']['angebote'][0]['subseite'] = "organisation";

	$menue_list['de']['angebote'][1]['label'] = "Team";
	$menue_list['de']['angebote'][1]['path'] = "team.php";
	$menue_list['de']['angebote'][1]['seite'] = "angebote";
	$menue_list['de']['angebote'][1]['subseite'] = "team";

	$menue_list['de']['angebote'][2]['label'] = "Personen";
	$menue_list['de']['angebote'][2]['path'] = "personen.php";
	$menue_list['de']['angebote'][2]['seite'] = "angebote";
	$menue_list['de']['angebote'][2]['subseite'] = "personen";
//
	$menue_list['de']['kontakt'][0]['label'] = "Kontakt";
	$menue_list['de']['kontakt'][0]['path'] = "kontakt.php";
	$menue_list['de']['kontakt'][0]['seite'] = "kontakt";
	$menue_list['de']['kontakt'][0]['subseite'] = "kontakt";
//
	$menue_list['de']['kontakt'][1]['label'] = "Praktikum";
	$menue_list['de']['kontakt'][1]['path'] = "praktikum.php";
	$menue_list['de']['kontakt'][1]['seite'] = "kontakt";
	$menue_list['de']['kontakt'][1]['subseite'] = "praktikum";
//
	if(isset($_GET['id']) && $url!='index' and $url!='impressum'){
		$smarty->assign('submenue', $menue_list[$strLang][$_GET['id']]);
	}
//
// Sub Submenue Deutsch
//
	$menue_list['de']['angebote']['organisation'][0]['label'] = "Organisationsentwicklung";
	$menue_list['de']['angebote']['organisation'][0]['path'] = "organisationsentwicklung.php";
	$menue_list['de']['angebote']['organisation'][0]['seite'] = "angebote";
	$menue_list['de']['angebote']['organisation'][0]['subseite'] = "organisation";
	$menue_list['de']['angebote']['organisation'][0]['subsubseite'] = "organisationsentwicklung";

	$menue_list['de']['angebote']['organisation'][1]['label'] = "Projektmanagement";
	$menue_list['de']['angebote']['organisation'][1]['path'] = "projektmanagement.php";
	$menue_list['de']['angebote']['organisation'][1]['seite'] = "angebote";
	$menue_list['de']['angebote']['organisation'][1]['subseite'] = "organisation";
	$menue_list['de']['angebote']['organisation'][1]['subsubseite'] = "projektmanagement";

	$menue_list['de']['angebote']['organisation'][2]['label'] = "Qualitätsmanagement";
	$menue_list['de']['angebote']['organisation'][2]['path'] = "qualitaetsmanagement.php";
	$menue_list['de']['angebote']['organisation'][2]['seite'] = "angebote";
	$menue_list['de']['angebote']['organisation'][2]['subseite'] = "organisation";
	$menue_list['de']['angebote']['organisation'][2]['subsubseite'] = "qualitaetsmanagement";

	$menue_list['de']['angebote']['organisation'][3]['label'] = "Moderation";
	$menue_list['de']['angebote']['organisation'][3]['path'] = "moderation.php";
	$menue_list['de']['angebote']['organisation'][3]['seite'] = "angebote";
	$menue_list['de']['angebote']['organisation'][3]['subseite'] = "organisation";
	$menue_list['de']['angebote']['organisation'][3]['subsubseite'] = "moderation";
//
	$menue_list['de']['angebote']['team'][0]['label'] = "Inhousetraining";
	$menue_list['de']['angebote']['team'][0]['path'] = "inhousetraining.php";
	$menue_list['de']['angebote']['team'][0]['seite'] = "angebote";
	$menue_list['de']['angebote']['team'][0]['subseite'] = "team";
	$menue_list['de']['angebote']['team'][0]['subsubseite'] = "inhousetraining";

	$menue_list['de']['angebote']['team'][1]['label'] = "Assessment-Center";
	$menue_list['de']['angebote']['team'][1]['path'] = "assessment_center.php";
	$menue_list['de']['angebote']['team'][1]['seite'] = "angebote";
	$menue_list['de']['angebote']['team'][1]['subseite'] = "team";
	$menue_list['de']['angebote']['team'][1]['subsubseite'] = "assessment_center";

	$menue_list['de']['angebote']['team'][2]['label'] = "Teamentwicklung";
	$menue_list['de']['angebote']['team'][2]['path'] = "teamentwicklung.php";
	$menue_list['de']['angebote']['team'][2]['seite'] = "angebote";
	$menue_list['de']['angebote']['team'][2]['subseite'] = "team";
	$menue_list['de']['angebote']['team'][2]['subsubseite'] = "teamentwicklung";

	$menue_list['de']['angebote']['team'][3]['label'] = "Supervision";
	$menue_list['de']['angebote']['team'][3]['path'] = "supervision.php";
	$menue_list['de']['angebote']['team'][3]['seite'] = "angebote";
	$menue_list['de']['angebote']['team'][3]['subseite'] = "team";
	$menue_list['de']['angebote']['team'][3]['subsubseite'] = "supervision";

	$menue_list['de']['angebote']['team'][4]['label'] = "Moderation";
	$menue_list['de']['angebote']['team'][4]['path'] = "moderation_team.php";
	$menue_list['de']['angebote']['team'][4]['seite'] = "angebote";
	$menue_list['de']['angebote']['team'][4]['subseite'] = "team";
	$menue_list['de']['angebote']['team'][4]['subsubseite'] = "moderation_team";
//
	$menue_list['de']['angebote']['personen'][0]['label'] = "Coaching";
	$menue_list['de']['angebote']['personen'][0]['path'] = "coaching.php";
	$menue_list['de']['angebote']['personen'][0]['seite'] = "angebote";
	$menue_list['de']['angebote']['personen'][0]['subseite'] = "personen";
	$menue_list['de']['angebote']['personen'][0]['subsubseite'] = "coaching";

	$menue_list['de']['angebote']['personen'][1]['label'] = "Karriereplanung";
	$menue_list['de']['angebote']['personen'][1]['path'] = "karriereplanung.php";
	$menue_list['de']['angebote']['personen'][1]['seite'] = "angebote";
	$menue_list['de']['angebote']['personen'][1]['subseite'] = "personen";
	$menue_list['de']['angebote']['personen'][1]['subsubseite'] = "karriereplanung";

	$menue_list['de']['angebote']['personen'][2]['label'] = "Beratung";
	$menue_list['de']['angebote']['personen'][2]['path'] = "beratung.php";
	$menue_list['de']['angebote']['personen'][2]['seite'] = "angebote";
	$menue_list['de']['angebote']['personen'][2]['subseite'] = "personen";
	$menue_list['de']['angebote']['personen'][2]['subsubseite'] = "beratung";

	$menue_list['de']['angebote']['personen'][3]['label'] = "Analyse";
	$menue_list['de']['angebote']['personen'][3]['path'] = "analyse.php";
	$menue_list['de']['angebote']['personen'][3]['seite'] = "angebote";
	$menue_list['de']['angebote']['personen'][3]['subseite'] = "personen";
	$menue_list['de']['angebote']['personen'][3]['subsubseite'] = "analyse";
//
	if(isset($_GET['subid']) && isset($menue_list[$strLang][$_GET['id']][$_GET['subid']])){
		$smarty->assign('subsubmenue', $menue_list[$strLang][$_GET['id']][$_GET['subid']]);
	}
//

$smarty->assign('google', "<script>
	;var _gaq = [['_setAccount', 'UA-16029168-1'], ['_trackPageview']];
	setTimeout(function() {
		(function(d, t, a) {
		 var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
		 g[a] = a;
		 g.src = '".( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http' )."://www.google-analytics.com/ga.js';
		 s.parentNode.insertBefore(g, s);
		}(document, 'script', 'async'));
	}, 0);
</script>");



// Menue Ende
//Sonstiges
	$smarty->assign('mail', '<script type="text/javascript">document.write(
	"<n uers=\"znvygb:znvy%40obfo\056qr\">znvy\100obfo\056qr<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
	</script>');
?>
