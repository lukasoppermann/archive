<html>
<body style="font-family: Arial; color: #000;">
	<h1 style="font-size: 16px;">You have a new inquiry from <?=$customer_name?></h1>
	<div style="background: #efefef; border-left: 4px solid #ccc; margin-bottom: 15px; padding: 10px;">
		<h2 style="font-size: 14px; margin-top: 0;">Inquiry information</h2>
		<span>Name: </span><strong><?=$customer_name?></strong><br />
		<span>Email: </span><strong><?=$customer_email?></strong><br />
		<span>Phone: </span><strong><?=$customer_phone?></strong><br />
	</div>
	<div style="background: #efefef; border-left: 4px solid #ccc; margin-bottom: 15px; padding: 10px;">
		<h2 style="font-size: 14px;  margin-top: 0;">Message</h2>
		<?=$text?>
	</div>
</body>
</html>
