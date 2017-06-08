<?php
// open default form
echo form_open(lang_url().'/auth', $form_attributes)."\n\t\t";
echo (isset($title) ? "<h3>d".$title."</h3>" : '')."\n\t";
// cycle through fieldsets
	$opt = array();
	foreach($fieldset as $name => $array)
	{
		// open fieldset
		echo form_fieldset(($legend == true ? lang($name) : ''), $array['attributes'])."\n\t";
		// if value is set
		if(isset($opt['value']) && is_array($opt['value']))
		{
		// cycle through items
			foreach($array['items'] as $item)
			{
				echo form_element($item, $field[$item], $opt['value'][$item], $opt);
			}
		}
		// if values are not given
		else
		{
		// cycle through items
			foreach($array['items'] as $item)
			{
				echo form_element($item, $field[$item], '', $opt);				
			}
		}
		// close fieldset
		echo form_fieldset_close("\n");
	}
	// save & cancel buttons
	echo '<div class="button input float-right">'.form_submit('submit', lang('login'), 'class = "tcd3 icl1"').'</span></div>'."\n\t\t";			
// close form
echo form_close("\n\t\t");

/* End of file default_form.php */
/* Location: ./system/application/views/forms/default_form.php */