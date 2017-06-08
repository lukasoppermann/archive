<?php
// open default form
echo form_open($url, $form_attributes)."\n\t\t";
echo (isset($form_attributes['title']) ? "<h3>".$form_attributes['title']."</h3>" : '')."\n\t";
// cycle through fieldsets
	foreach($fieldset as $name => $array)
	{
		// open fieldset
		if(isset($array['attributes']['legend']) && $array['attributes']['legend'] === true){
			$legend = lang($name);
		}
		elseif(isset($array['attributes']['legend']) && $array['attributes']['legend'] != false)
		{
			$legend = $array['attributes']['legend'];
		}
		unset($array['attributes']['legend']);
		echo form_fieldset((isset($legend) ? $legend : ''), $array['attributes'])."\n\t";
		echo (isset($array['attributes']['title']) ? "<h3>".$array['attributes']['title']."</h3>" : '')."\n\t";
		// cycle through items
		foreach($array['items'] as $item)
		{
			// if value is set
			if(isset($opt['value'][$item]))
			{
				echo form_element($item, $field[$item], $opt['value'][$item], $opt);
			}
			// if values are not given
			else
			{
				echo form_element($item, $field[$item], '', $opt);				
			}
		}
		// close fieldset	
		echo form_fieldset_close("\n");
	}
	echo "<div id='submit-button'>";
	echo '<div class="button input float-right">'.form_submit('submit', lang(!empty($button['submit']['label']) ? $button['submit']['label'] : 'submit' ), 'class = "tcd3 scd2"').'</span></div>'."\n\t\t";
	echo "</div>";
	// save & cancel buttons
	// echo '<div class="button button-glass-passive float-right tcl3 scd2 save"><span>'.lang('save').'</span></div>'."\n\t\t";			
	
// close form
echo form_close("\n\t\t");

/* End of file default_form.php */
/* Location: ./system/application/views/forms/default_form.php */