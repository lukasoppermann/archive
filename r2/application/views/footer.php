		</div>
		</div>
				</div>
		<div id="content_bottom">
			
		</div>
		
			<div id="footer">
				<?=variable($menu['footer']); ?>
			<div class="footer_meta">
				<?=copyright(array('copyright' => 'Copyright &copy', 'by' => 'GZO Oberflächentechnik GmbH - All Rights Reserved.')); ?>
			</div>
		</div>
		</div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="<?=base_url().'libs/js/jquery.cycle.js'?>"></script>
<script type="text/javascript">
$(document).ready(function() {
	// Google Analytics
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-7074034-9']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
});
</script>
</body>
</html>
