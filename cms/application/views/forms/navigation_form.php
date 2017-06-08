<?php
echo form_open(current_url(),array('id' => 'navigation_form', 'class' => 'form'));
echo '<div id=\'left_column\'>';
/*----------------------------------------------------------------------*/
foreach($cms_navigation as $set => $values)
{
	if($set != 'field')
	{
		echo form_fieldset('', array('id' => 'fieldset_'.$set, 'class'=>'fieldset'))."\t";
	
			foreach($values as $value)
			{
				
				// define $arry so it is NULL if not set; needs 2 $$
				$array = isset($cms_navigation['field'][$value]['array']) ? $$cms_navigation['field'][$value]['array'] : NULL;
				//
				echo form_element($value, $cms_navigation['field'][$value]['type'], $cms_navigation['field'][$value], $array);
			}

		echo form_fieldset_close();
	}
}
echo '</div>';
/*----------------------------------------------------------------------*/	
echo '<div id=\'navigation_trees\'>';
	foreach($nav_tree as $name => $tree)
	{
		echo '<div>';
		echo $tree;
		echo '</div>';
	}
echo '</div>';
/*----------------------------------------------------------------------*/	
echo '<div class="button"><span>'.form_submit(array('name'=>'submit', 'value'=>lang('form_submit'))).'</span></div>'."\n\t\t";		
echo '<div class="button"><span>'.form_submit(array('name'=>'reset', 'value'=>lang('form_cancel'))).'</span></div>'."\n\t\t";
/*----------------------------------------------------------------------*/	
echo form_close()."\n\t\t";
/* End of file navigation_form.php */
/* Location: ./system/application/views/forms/navigation_form.php */