<?php
echo form_open(current_url(),array('id' => 'add_page_form', 'class' => 'form'));
echo '<div id=\'left_column\'>';
/*----------------------------------------------------------------------*/
// Language Menu
echo "<ul id='settings_lang_menu'>";
foreach($languages['name'] as $key => $language)
{
	if($key == $current_lang_id)
	{
		echo "<li class='active ".$languages['languages'][$cms_current_lang][$key]."'><span>".$languages['languages'][$cms_current_lang][$key]."</span></li>";
	}
	else
	{
		echo "<li class='".$languages['languages'][$cms_current_lang][$key]."'><a href='".base_url_lang().'/pages/'.$language."'>".$languages['languages'][$cms_current_lang][$key]."</a></li>";
	}
}
echo "</ul>";
/*----------------------------------------------------------------------*/
foreach($cms_pages as $set => $values)
{
	if($set != 'field')
	{
		echo form_fieldset('', array('id' => 'fieldset_'.$set, 'class'=>'fieldset'))."\t";
	
			foreach($values as $value)
			{
				// define $arry so it is NULL if not set; needs 2 $$
				$array = isset($cms_pages['field'][$value]['array']) ? $$cms_pages['field'][$value]['array'] : NULL;
				//
				echo form_element($value, $cms_pages['field'][$value]['type'], $cms_pages['field'][$value], $array);
			}

		echo form_fieldset_close();
	}
}
echo '</div>';
/*----------------------------------------------------------------------*/	
echo '<div id=\'right_column\'>';
echo $nav_tree;
echo '</div>';
/*----------------------------------------------------------------------*/	
echo '<div class="button"><span>'.form_submit(array('name'=>'submit', 'value'=>lang('form_submit'))).'</span></div>'."\n\t\t";		
echo '<div class="button"><span>'.form_submit(array('name'=>'reset', 'value'=>lang('form_cancel'))).'</span></div>'."\n\t\t";
/*----------------------------------------------------------------------*/	
echo form_close()."\n\t\t";
/* End of file pages_form.php */
/* Location: ./system/application/views/forms/pages_form.php */