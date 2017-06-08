<?php $config = $this->config->item('templates'); ?>
<?php $this->load->view($config['header_default']); ?>
<div id="main_content">
	<?=$content ?>
</div>

<?php $this->load->view($config['footer_default']); ?>