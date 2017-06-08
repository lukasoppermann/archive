<?PHP
// Standard Header Daten
	require_once("../libs/inc/global.inc.php");
	$smarty->assign('keywords', '');	
	$smarty->assign('description', '');
	$smarty->assign('css_screen', 'web');
	$smarty->assign('css_print', 'print');		
	$smarty->assign('language', 'de');	
	$smarty->assign('titel', 'Kontakt');

// Standard Header Daten ende 
	$smarty->assign('mail_allgemein', '<script type="text/javascript">document.write(
"<n uers=\"znvygb:znvy%40obfo\056qr\">znvy\100obfo\056qr<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
</script>');
	$smarty->assign('mail_andreas', '<script type="text/javascript">document.write(
"<n uers=\"znvygb:n\056bccreznaa%40obfo\056qr\">n\056bccreznaa\100obfo\056qr<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
</script>');
	$smarty->assign('mail_marion', '<script type="text/javascript">document.write(
"<n uers=\"znvygb:z\056fpurax%40obfo\056qr\">z\056fpurax\100obfo\056qr<\057n>".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));
</script>');
// Display  Template
	$smarty->display('kontakt.tpl');
?>


