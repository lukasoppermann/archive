<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
	<title>Checkout</title>
    <!--script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script-->
</head>
<body>
    <h4>Paypal Payflow Checkout</h4>

	<?php echo form_open('checkout/payment'); ?>
	    
	BILLTOFIRSTNAME: <input type="text" name="BILLTOFIRSTNAME" value="John" /><br/>
    BILLTOLASTNAME: <input type="text" name="BILLTOLASTNAME" value="Smith" /><br/>
    BILLTOSTREET: <input type="text" name="BILLTOSTREET" value="1 Main St" /><br/>
    BILLTOCITY: <input type="text" name="BILLTOCITY" value="San Jose" /><br/>
    BILLTOSTATE: <input type="text" name="BILLTOSTATE" value="CA" /><br/>
    BILLTOZIP: <input type="text" name="BILLTOZIP" value="95101" /><br/>
    BILLTOCOUNTRY: <input type="text" name="BILLTOCOUNTRY" value="US" /><br/>
    
    SHIPTOFIRSTNAME: <input type="text" name="SHIPTOFIRSTNAME" value="Jane" /><br/>
    SHIPTOLASTNAME: <input type="text" name="SHIPTOLASTNAME" value="Jones" /><br/>
    SHIPTOSTREET: <input type="text" name="SHIPTOSTREET" value="1 Park Ave" /><br/>
    SHIPTOCITY: <input type="text" name="SHIPTOCITY" value="San Jose" /><br/>
    SHIPTOSTATE: <input type="text" name="SHIPTOSTATE" value="CA" /><br/>
    SHIPTOZIP: <input type="text" name="SHIPTOZIP" value="95101" /><br/>
    SHIPTOCOUNTRY: <input type="text" name="SHIPTOCOUNTRY" value="US" /><br/>
     
    Total: <?php echo $CURRENCY; ?> $<?php echo $AMT; ?><br/>
    
    <input type="hidden" name="AMT" value="<?php echo $AMT; ?>"/>
    <input type="hidden" name="CURRENCY" value="<?php echo $CURRENCY; ?>"/>
    <input type="submit" value="Submit" />
    
	<?php echo form_close(); ?>
    
    <?php echo validation_errors('<p class="error">'); ?>
	
</body>
</html>	