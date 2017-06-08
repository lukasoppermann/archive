<?php $config = $this->config->item('templates'); ?>
<?php $this->load->view($config['header_default']); ?>

<?php echo $content ?>

<?php $this->load->view($config['footer_default']); ?>