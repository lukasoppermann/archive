<?php
echo form_open(current_url(),array('id' => 'settings_form', 'class' => 'form'))."\n\t\t";
/*----------------------------------------------------------------------*/
// Language Specifiv Settings
echo form_fieldset(lang('settings_for').$current_language)."\t";
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
		echo "<li class='".$languages['languages'][$cms_current_lang][$key]."'><a href='".base_url_lang().'/settings/settings-general/'.$language."'>".$languages['languages'][$cms_current_lang][$key]."</a></li>";
	}
}
echo "</ul>";
/*----------------------------------------------------------------------*/
// Language
echo form_hidden(array('settings_language' => $current_lang_id))."\n\t\t";
/*----------------------------------------------------------------------*/
	foreach($form as $group => $values)
	{
		echo form_fieldset(lang('title_'.$group))."\t";
			foreach($values as $value)
			{

			// define $arry so it is NULL if not set; needs 2 $$
			$array = isset($form_element[$value]['array']) ? $$form_element[$value]['array'] : NULL;
			// add current value
			$form_element[$value]['current_value'] = (isset($current_data[$current_lang_id][$value]) ? $current_data[$current_lang_id][$value] : '');
			// add wrapper
			$form_element[$value]['name_wrapper'] = 'settings';
			//
			echo form_element($value, $form_element[$value]['type'], $form_element[$value], $array);
			}
		echo form_fieldset_close();	
	}

echo form_fieldset_close();
/*----------------------------------------------------------------------*/	
	echo '<div class="button"><span>'.form_submit(array('name'=>'submit', 'value'=>lang('form_submit'))).'</span></div>'."\n\t\t";		
	echo '<div class="button"><span>'.form_submit(array('name'=>'reset', 'value'=>lang('form_cancel'))).'</span></div>'."\n\t\t";
echo form_close()."\n\t\t";

/* End of file settings_form.php */
/* Location: ./system/application/views/forms/settings_form.php */