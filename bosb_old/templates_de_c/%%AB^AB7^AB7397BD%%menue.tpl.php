<?php /* Smarty version 2.6.16, created on 2014-12-10 11:46:01
         compiled from ../templates_de/menue.tpl */ ?>
<ul id="menue">
	<?php $_from = $this->_tpl_vars['menue']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['menue']):
?>
	  	<?php if ($this->_tpl_vars['menue']['seite'] == $this->_tpl_vars['url']): ?>
			<li><a href="<?php echo $this->_tpl_vars['menue']['path']; ?>
?id=<?php echo $this->_tpl_vars['menue']['seite']; ?>
" class="active"><?php echo $this->_tpl_vars['menue']['label']; ?>
</a></li>
		<?php else: ?>
		  	<li><a href="<?php echo $this->_tpl_vars['menue']['path']; ?>
?id=<?php echo $this->_tpl_vars['menue']['seite']; ?>
" class="passive"><?php echo $this->_tpl_vars['menue']['label']; ?>
</a></li>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</ul>
