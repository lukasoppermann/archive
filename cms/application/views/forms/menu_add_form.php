<?php

echo form_open(current_url(),array('id' => 'add_menu_form', 'class' => ''));

	echo form_fieldset('', array('id' => 'fieldset_'.$set, 'class'=>''))."\t";
	
		echo form_element('parent_id', 'hidden', array('class'=>'js_form', 'current_value'=>$values['parent_id']), array(NULL));
		echo form_element('type', 'hidden', array('class'=>'js_form', 'current_value'=>$values['type']), array(NULL));

		echo form_element($value, $fields[$value]['type'], $fields[$value], $array);
		

				//
				$array = isset($fields[$value]['array']) ? $$fields[$value]['array'] : NULL;
				$fields[$value]['class'] = 'js_form';
				//


	echo form_fieldset_close();

	echo '<div class="button button-glass-passive float-right tcl3 scd2 save"><span>'.lang('save').'</span></div>'."\n\t\t";		
	echo '<div class="button button-style-passive float-right tcd2 icl2 cancel"><span>'.lang('cancel').'</span></div>'."\n\t\t";

echo form_close()."\n\t\t";

/* End of file navigation_form.php */
/* Location: ./system/application/views/forms/navigation_form.php */