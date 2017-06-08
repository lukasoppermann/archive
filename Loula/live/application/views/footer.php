	<!-- close content div -->
	</div>
	<!-- Footer -->
	<div id="footer">
		<!-- Footer Navigation -->
		<div id="footer_nav">
			<ul id="footer_menu">
				<li><a class="to-bottom" href="#contact">Contact Us</a></li>
				<li><a class="to-bottom" href="#about">About Us</a></li>
				<li><a class="dialog-box-link" data-dialog-type="policy" href="#policy">Return Policy</a></li>
				<li class="social"><a class="twitter" target="_blank" title="follow Loula on twitter" href="https://twitter.com/#!/LoulaNews"></a></li>
				<li class="social"><a class="facebook" target="_blank" title="become a fan of Loula on facebook" href="http://www.facebook.com/pages/Loula/127804390568942"></a></li>
			</ul>
		<!-- Likes -->
			<ul id="footer_menu_like">
				<!-- Facebook -->
				<li id="fb_footer">
					<iframe src="http://www.facebook.com/plugins/like.php?href=<?=current_url() ?>&amp;layout=standard&amp;show_faces=false&amp;width=50&amp;action=like&amp;font=tahoma&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:65px"></iframe>
				</li>
				<!-- Twitter -->
				<li>
					<a target="_blank" href="https://twitter.com/share/?hashtags=loulaShoes&related=LoulaNews&via=LoulaNews&text=Check%20out%20Loula,%20women's%20high%20fashion%20shoes&url=<?=current_url()?>&lang=en">Share on Twitter</a>
				</li>
			</ul>
			<!-- Cart Button -->
			<div id="shopping_cart" style="display: none">
				<a href="">Your Cart (<span class="amount"><?=(variable(count( $this->cart->contents() )) > 0 ? variable(count($this->cart->contents())) : '0' )?></span>)</a>
			</div>
		</div>
		<!-- Footer Body -->
		<div id="footer_body">
			<?=$about_us; ?>
			<?=$footer_images; ?>
			<?=$contact_us; ?>
			<?=$map?>
		</div>
	</div>
	<!-- Default Modal Box -->
	<div class="dialog_box" style="display:none;">
		<div class="close">close</div>
		<div id="dialog_content">
			<img class="loading" src="<?=base_url()?>/libs/css/loading.gif" />
		</div>
	</div>
	<!-- Mail Chimp Modal Box -->
	<div class="mailchimp_box" style="display:none;">
		<div class="close">close</div>
		<div id="mailchimp_content">
			<?=$this->load->view('custom/mailchimp');?>
		</div>
	</div>
	<!-- Shopping Cart Modal Box -->
	<div class="shopping_cart" style="display:none;">
		<div class="close">close</div>
		<div id="cart">
		</div>
	</div>
	<!-- Order Confirm -->
	<?=variable($confirm) ?>
	<div id="overlay" style="display:none;">
	</div>	
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-12538535-8']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript" charset="utf-8">
	var CI_ROOT = '<?=base_url()?>';
	var CURRENT_URL = '<?=current_url()?>';
	var CLIENT_BASE = '<?=config("client_base", true)?>';
	var CLIENT_IMAGES = '<?=config("client_base", true).config("client_media", true).config("client_images", true)?>';	
</script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.min-1.7.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.slimbox.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.imageloaded.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.masonry.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.fs_filter-0.0.1.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/jquery.cycle.js"></script>
<script type="text/javascript" src="<?=base_url()?>libs/js/javascript.js"></script>
<!--<script type="text/javascript" src="<?=base_url()?>libs/js/compressed.js"></script>-->
</body>
</html>