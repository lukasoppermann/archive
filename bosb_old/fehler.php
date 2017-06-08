<?PHP
$strLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$strLang = preg_replace('/-([a-z]+)/','',$strLang);
header("location:/htdocs_$strLang/fehler.php?fehler=$_GET[fehler]");
?>
