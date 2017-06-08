<?php $config = $this->config->item('templates'); ?>
<?php $this->load->view($config['header_default']); ?>

<div id="main_content">
	<h1 class="tcd1 icl3"><?=$title ?></h1>
<?=$content ?>
<?="\n"; ?></div>
<?php $this->load->view($config['footer_default']); ?>