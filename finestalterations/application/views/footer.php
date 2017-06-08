	</div>
</div>	
<div id="footer">
	<div class="content">
		<div class="footer-wrapper">
			<form id="contact_form" class="email-form">
				<h3>Contact Us</h3>
				<div class="input input-button">
					<input type="text" name="name" placeholder="Full name" value="" />
					<div class="submit" id="submit">Send</div>
				</div>
				<div class="input">
					<input type="text" name="email" placeholder="Email address" value="" />
				</div>
				<div class="textarea">
					<textarea name="message" placeholder="Your Message"></textarea>
				</div>
			</form>
			<div class="stores">
				<h3>Locations</h3>	
				<?=variable($this->data['stores_footer'])?>
		</div>	
	</div>
</div>
<div id="footer_bar">
	<div class="footer-menu">
		<ul class="float-left">
			<li><a class="newsletter" href="#newsletter">Newsletter</a></li>
			<?php
				if($user = config('twitter/follow'))
				{
					echo '<li class="follow"><a href="https://twitter.com/'.$user.'" target="_blank"><img src="'.media('saba-on-tw.gif', 'layout').'"></a></li>';
				}
				if($user = config('facebook/follow'))
				{
					echo '<li class="follow"><a href="https://facebook.com/'.$user.'" target="_blank"><img src="'.media('saba-on-fb.gif', 'layout').'"></a></li>';
				}
			?>
		</ul>
		<ul class="float-right">
			<li><a class="contact-us" href="#contact-us">Contact Us</a></li>
			<li><a href="<?=base_url()?>career">Career</a></li>
			<li><a href="<?=base_url()?>franchise">Franchise</a></li>
			<!-- <li><a href="http://www.suitstomeasure.com.au">Suits to measure</a></li> -->
		</ul>
	</div>
</div>
<div class="dialog-wrapper">
	<div class="dialog-inner-wrapper">
		<div class="dialog"></div>
	</div>
</div>
<div class="dialog-overlay"></div>
<? 
echo js('default', FALSE); 
?>
</body>
</html>
