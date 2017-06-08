<?PHP
// Standard Header Daten
	require_once("../libs/inc/global.inc.php");
	$smarty->assign('keywords', '');	
	$smarty->assign('description', '');
	$smarty->assign('css_screen', 'web');
	$smarty->assign('css_print', 'print');		
	$smarty->assign('language', 'de');	
	$smarty->assign('titel', 'Analyse');

// Standard Header Daten ende 
	$smarty->assign('mail_analyse', '<script type="text/javascript">document.write(
"<n uers=\"znvygb:nanylfr%40obfo\056qr\">nanylfr\100obfo\056qr<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
</script>');
// Display  Template
	$smarty->display('analyse.tpl');
?>


