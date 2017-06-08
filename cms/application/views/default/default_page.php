<?php $config = $this->config->item('templates'); ?>
<?php $this->load->view($config['header_default']); ?>

<?php  $this->load->view($template); ?>

<?php $this->load->view($config['footer_default']); ?>