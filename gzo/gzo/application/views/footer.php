<? if(variable($teaser) != NULL && variable($teaser) != FALSE){ ?>
					<div id="teaser_bar">
						<?=variable($teaser);?>
					</div>
<? } ?>
				</div>
			</div>
			<div id="footer">
				<?=variable($menu['footer']); ?>
				<div class="footer_meta">
					<?=copyright(array('copyright' => 'Copyright &copy', 'by' => 'GZO Oberflächentechnik GmbH - All Rights Reserved.')); ?>
				</div>
			</div>
		</div>
	</div>
</div>	

<script type="text/javascript" src="<?=base_url()?>/libs/js/simpleslider-0.9.min.js"></script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15375517-1']);
  _gaq.push(['_setDomainName', 'gzo-gmbh.com']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = 'http://www.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
simpleslider('16:9');
</script>
</body>
</html>