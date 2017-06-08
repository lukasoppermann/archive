<?php /* Smarty version 2.6.16, created on 2014-12-10 11:46:01
         compiled from ../templates_de/submenue.tpl */ ?>
<div id="submenue">
	<ul>
		<?php if ($this->_tpl_vars['url'] == 'index'): ?>
			<li>
				<p id="home_quote">
					Wer nicht verändern will,<br />
					wird auch verlieren,<br />
					was er bewahren möchte.<br /><br />
					<i>- Gustav Heinemann -</i>
				</p>
			</li>
			<?php elseif ($this->_tpl_vars['url'] != ""): ?>
				<?php $_from = $this->_tpl_vars['submenue']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['submenue']):
?>
					<?php if ($this->_tpl_vars['submenue']['subseite'] == $this->_tpl_vars['suburl']): ?>
	  					<li><a href="<?php echo $this->_tpl_vars['submenue']['path']; ?>
?id=<?php echo $this->_tpl_vars['submenue']['seite']; ?>
&amp;&amp;subid=<?php echo $this->_tpl_vars['submenue']['subseite']; ?>
" class="sub_active"><?php echo $this->_tpl_vars['submenue']['label']; ?>
</a></li>
						<?php $_from = $this->_tpl_vars['subsubmenue']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['subsubmenue']):
?>
					  		<?php if ($this->_tpl_vars['subsubmenue']['subsubseite'] == $this->_tpl_vars['subsuburl']): ?>
			  					<li><a href="<?php echo $this->_tpl_vars['subsubmenue']['path']; ?>
?id=<?php echo $this->_tpl_vars['subsubmenue']['seite']; ?>
&amp;&amp;subid=<?php echo $this->_tpl_vars['subsubmenue']['subseite']; ?>
&amp;&amp;subsubid=<?php echo $this->_tpl_vars['subsubmenue']['subsubseite']; ?>
" class="sub_sub_active"><img src="../media/arrow_blue2.png" alt="<?php echo $this->_tpl_vars['subsubmenue']['label']; ?>
" /><?php echo $this->_tpl_vars['subsubmenue']['label']; ?>
</a></li>
							<?php else: ?>
						  		<li><a href="<?php echo $this->_tpl_vars['subsubmenue']['path']; ?>
?id=<?php echo $this->_tpl_vars['subsubmenue']['seite']; ?>
&amp;&amp;subid=<?php echo $this->_tpl_vars['subsubmenue']['subseite']; ?>
&amp;&amp;subsubid=<?php echo $this->_tpl_vars['subsubmenue']['subsubseite']; ?>
" class="sub_sub_passive"><?php echo $this->_tpl_vars['subsubmenue']['label']; ?>
</a></li>
			  				<?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
					<?php else: ?>
				  		<li><a href="<?php echo $this->_tpl_vars['submenue']['path']; ?>
?id=<?php echo $this->_tpl_vars['submenue']['seite']; ?>
&amp;&amp;subid=<?php echo $this->_tpl_vars['submenue']['subseite']; ?>
" class="sub_passive"><?php echo $this->_tpl_vars['submenue']['label']; ?>
</a></li>
	  				<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
		</ul>
	</div>