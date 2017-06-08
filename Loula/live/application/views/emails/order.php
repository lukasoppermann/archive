<html>
<body style="font-family: Arial; color: #000;">
	<h1 style="font-size: 16px;">You have a new Order from <?=$post['first_name']?> <?=$post['last_name']?></h1>
	<div style="background: #efefef; border-left: 4px solid #ccc; margin-bottom: 15px; padding: 10px;">
		<h2 style="font-size: 14px; margin-top: 0;">Order Information</h2>
		<span>Status: </span><strong><?=$status?></strong><br />
		<span>Order ID: </span><strong><?=$order_id?></strong><br />
		<span>Total: </span><strong>$<?=$total?></strong><br />
	</div>
	<div style="background: #efefef; border-left: 4px solid #ccc; margin-bottom: 15px; padding: 10px;">
		<h2 style="font-size: 14px;  margin-top: 0;">Products</h2>
		<?=$products?>
	</div>
	<div style="background: #efefef; border-left: 4px solid #ccc; margin-bottom: 15px; padding: 10px;">
		<h2 style="font-size: 14px; margin-top: 0;">Customer Information</h2>
		<span>Customer: </span><strong><?=$post['address_name']?></strong><br />
		<span>Country: </span><strong><?=$post['address_country']?> (<?=$post['address_country_code']?>)</strong><br />
		<span>Postal Code: </span><strong><?=$post['address_zip']?></strong><strong><?=$post['address_state']?></strong><br />
		<span>City: </span><strong><?=$post['address_city']?></strong><br />
		<span>Street: </span><strong><?=$post['address_street']?></strong><br />
		<br /><span>Email: </span><strong><?=$post['payer_email']?></strong><br />
	</div>
</body>
</html>
