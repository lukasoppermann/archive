<!--/////NICHT ÄNDERN!!!//////////////////////////////-->
	{include file="../templates_de/header.tpl"}
<!--/////////////////////////////////////////////////////////////////-->
<h1>Fehler Nummer {$error_number}</h1>
{if $error_number == 404}
	Die angeforderte Seite existiert nicht.
{elseif  $error_number == 403}
	Sie haben keine zureichende Berechtigung für die angeforderte Seite.
{else}
	Ein unbekannter Fehler ist aufgetreten.
{/if}
<br /><br />
Bitte melden sie uns diesen Fehler, damit wir uns um die Behebung des selbigen kümmern können.<br /><br />
{$mail_fehler}
<!--/////NICHT ÄNDERN!!!//////////////////////////////-->
	{include file="../templates_de/footer.tpl"}
<!--/////////////////////////////////////////////////////////////////-->
