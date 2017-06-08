	</div>
		<div id="footer" class="tcd2 icl1 footer-gradiant">
			<?=!empty($footer) ? $footer : ''; ?>
		<div class="footer_meta">
			<a href="#">Memory Usage: <?= $this->benchmark->memory_usage();?> - Total Execution Time: <?=$this->benchmark->elapsed_time();?></a>
			<?=copyright(array('copyright' => 'copyright &copy', 'by' => 'by Form&System', 'url' => 'http://www.formandsystem.com/copyright')); ?>
		</div>
	</div><?=!empty($dialog) ? $dialog : ''; ?>		
	<?=$this->javascript->output(FALSE); ?>	
</body>
</html>