	<!-- <?php echo $this->benchmark->elapsed_time();?> -->
			</div>
		</div>
	</div>
</div>	
	<div id="footer" class="hide-on-load">
		<ul class="follow-nav" aria-hidden="true">
			<li class="item"><a class="link twitter" href="https://twitter.com/#!/vearenet" target="_blank"><span class="icon-small twitter"></span></a></li>
			<li class="item"><a class="link dribbble" href="http://dribbble.com/lukasoppermann" target="_blank"><span class="icon-small dribbble"></span></a></li>
			<li class="item"><a class="link xing" href="https://www.xing.com/profile/Lukas_Oppermann" target="_blank"><span class="icon-small xing"></span></a></li>
			<li class="item"><a class="link linkedin" href="https://www.linkedin.com/in/lukasoppermann" target="_blank"><span class="icon-small linkedin"></span></a></li>
		</ul>
	</div>
<?
	if(mobile() != true)
	{
		$this->fs_google->fonts(array('Open Sans:300,400,300italic'), variable($fonts_callback));
	}
?>
<? echo js('default', TRUE); ?>
<? echo js('page', TRUE, "data-path='".current_url()."' "); ?>
</div>
</body>
</html>
