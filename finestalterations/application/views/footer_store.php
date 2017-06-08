<?
	if( isset($permalink) )
	{
		$url = base_url().'stores/'.variable($permalink);
		$link = '';
	}
	else
	{
		$url = '#';
		$link = ' no-link';
	}
?>
<a class="store<?=$link?>" href="<?=$url?>">
	<div class="store-content">
		<h4><?=variable($name)?></h4>
		<p><?=variable($address)?><br />
		<?=(variable($additional_address) != "" ? $additional_address."<br />" : "")?>
		Telephone: <?=variable($phone)?><br />
		Email: <?=safe_mailto(variable($email),variable($email),array('link' => FALSE))?></p>
	</div>
</a>