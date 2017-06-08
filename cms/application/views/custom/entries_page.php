<?php $config = $this->config->item('templates'); ?>

<?php $this->load->view($config['header_default'], array('message' => (isset($message) ? $message : ''))); ?>
	
<div id="main_content">
	<h1 class="tcd1 icl3"><?=$title ?></h1>
	<?=$content ?>
</div>

<?php $this->load->view($config['footer_default']); ?>