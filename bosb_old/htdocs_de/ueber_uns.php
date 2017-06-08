<?PHP
// Standard Header Daten
	require_once("../libs/inc/global.inc.php");
	$smarty->assign('keywords', '');	
	$smarty->assign('description', '');
	$smarty->assign('css_screen', 'web');
	$smarty->assign('css_print', 'print');		
	$smarty->assign('language', 'de');	
	$smarty->assign('titel', 'Über uns');

// Standard Header Daten ende 
// Display  Template
	$smarty->display('ueber_uns.tpl');
?>


