Dear <?=$customer_name?>,<br />
you receive this email because of your inquiry at <?$company?>.
<table>
	<tr><td>Ticket Nr.:</td)><td><b><?=$ticket_nr?></b></td></tr>
	<tr><td>Status:</td)><td><b><?=config('ticket_states/'.$status)?></b></td></tr>
	<tr><td>Pickup Date:</td)><td><b><?=(isset($resolved) ? $resolved : 'not set').(isset($pickup_time) ? ' at '.$pickup_time : '')?></b></td></tr>
	<tr><td>Price:</td><td><b>$ <?=$price?></b></td></tr>
</table>