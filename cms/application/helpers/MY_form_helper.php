<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Extending the form helper
*
*
* version 0.3
* @author Lukas Oppermann - veare.net
*/
function form_element($name, $options, $value = NULL, $data)
{
	$CI = &get_instance();
	// container array
	$c = $options;
	// redefine special values
	$c['value'] 		= empty($value) ? (empty($data['value'][$name]) ? set_value($name) : set_value($name, $data['value'][$name])) : set_value($name, $value);		
	$c['id'] 			= empty($c['id']) ? $name : $c['id'];
	$c['name'] 			= isset($c['wrap']) ? $c['wrap'].'['.$name.']' : $name;
	$c['class'] 		= (!empty($c['class']) ? $c['class'].' '.$c['type'].'-field' : $c['type'].'-field').(!empty($data['class']) ? ' '.$data['class'] : '');	
	$c['before']		= !empty($c['before']) ? $c['before'] : '';
	$c['after']			= !empty($c['after']) ? $c['after'] : '';	
	$c['class_type']	= $c['type'];
	$c['label']			= !empty($c['label']) ? $c['label'] : lang($name);
	$c['var']			= !empty($data['var']) ? $data['var'] : '';	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// form elements
	//
	// Input Field
	if($c['type'] == 'input' || $c['type'] == 'password' || $c['type'] == 'hidden' || $c['type'] == 'file' || $c['type'] == 'upload')
	{	
		!empty($c['deactive']) ? $opt['disabled'] = 'disabled' : '';
		$opt['value'] 		= $c['value'];
		$opt['id'] 			= $c['id'];
		$opt['name'] 		= $c['name'];
		$opt['class'] 		= $c['class'];
		$old_class 			= $c['class'];
		$c['class']			= $c['class'].' inlinelabel';	
		
		if(isset($c['toggle']) && $c['toggle'] == TRUE)
		{
			$opt['class'] .=' with-toggle';
		}
		
		if($c['type'] == 'hidden')
		{
			$opt['value'] = $data['value'][$c['name']];
			$opt['type'] = 'hidden';
			return form_input($opt);
		}
		elseif($c['type'] == 'password')
		{
			$output = form_password($opt);
		}
		elseif($c['type'] == 'file' || $c['type'] == 'upload')
		{
			$c['class'] = $old_class;
			$output = form_upload($opt);
		}
		else
		{
			$output = form_input($opt);
			
			if(isset($c['toggle']) && $c['toggle'] == TRUE)
			{
				$output .= '<span class="input-toggle icon '.(isset($c['toggle_type']) ? $c['toggle_type'] : 'edit' ).'" 
				title="'.(isset($c['toggle_type']) ? $c['toggle_type'] : 'edit' ).'"></span>';
			}
		}		
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// Submit Button
	elseif($c['type'] == 'submit' || $c['type'] == 'save')
	{
		$c['class'] = $c['class'].' button input float-right';
		$c['label'] = false;
		$output = form_submit('submit', lang($c['type']), 'class = "tcd3 scd2"');
	}
	elseif($c['type'] == 'cancel')
	{
		$c['class'] = $c['class'].' float-right';
		$c['label'] = false;
		$output = '<a href="'.lang_url().$c['url'].'">'.lang($c['type']).'</a>';
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// Select Field
	elseif($c['type'] == 'select' || $c['type'] == 'dropdown')
	{
		$opt = NULL;
		!empty($c['deactive']) ? $opt .= ' disabled = "disabled"' : '';
		!empty($c['id']) ? $opt .= ' id = "'.$c['id'].'"' : '';
		!empty($c['class']) ? $opt .= ' class = "'.$c['class'].' select"' :	'';

		$items[$c['value']] = $data['data'][$c['name']][$c['value']];
		unset($data['data'][$c['name']][$c['value']]);
		foreach($data['data'][$c['name']] as $id => $item){
			$items[$id] = $item;
		}
		
		$output = form_dropdown($c['name'], $items, $c['value'], $opt);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
	// Checkbox Field
	elseif($c['type'] == 'checkbox' || $c['type'] == 'toggle')
	{
		$opt['value'] 	= !empty($c['value']) ? $c['value'] : $c['name'];
		$opt['checked'] = (isset($c['checked']) || isset($value) && $value == 1) ? TRUE : FALSE;
		$opt['id'] 		= $c['id'];
		$opt['name'] 	= $c['name'];
		$opt['class'] 	= !empty($c['class']) ? $c['class'] : '';		
		$output = form_checkbox($opt);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 	
	// Checkselect Field
	elseif($c['type'] == 'checkselect')
	{
		$opt['value'] 	= !empty($c['value']) ? $c['value'] : $c['name'];
		$opt['checked'] = (isset($c['checked']) || isset($value) && $value == 1) ? TRUE : FALSE;
		$opt['id'] 		= $c['id'];
		$opt['name'] 	= $c['name'];
		$opt['class'] 	= !empty($c['class']) ? $c['class'] : '';		
		$output = form_checkbox($opt);
	}	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// Radio Field	
	elseif($c['type'] == 'radio')
	{
		foreach($array as $key => $item)
		{
			$opt['value'] = $key;
			$opt['checked'] = (isset($options['default']) && $options['default'] == $key) ? TRUE : FALSE;	
			$opt['id'] = $value.'_'.$key;	
			$output = form_radio($opt);
		}
	}	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// Textarea Field	
	elseif($c['type'] == 'textarea')
	{
		$opt['value'] 		= $c['value'];
		$opt['id'] 			= $c['id'];
		$opt['name'] 		= $c['name'];
		$opt['class'] 		= $c['class'];

		if(isset($options['maxlength']) && $options['maxlength'] <= 200)
		{
			$opt['class'] .= ' chars-200';
			$opt['maxlength'] = $options['maxlength'];
		}
		elseif(isset($options['maxlength']) && $options['maxlength'] <= 500)
		{
			$opt['class'] .= ' chars-500';			
		}
		else
		{
			$opt['class'] .= ' chars-more';				
		}
		if((isset($options['wysiwyg']) && $options['wysiwyg'] == TRUE) || (isset($c['inline']) && $c['inline'] == FALSE) )
		{
			$opt['class'] .= ' wysiwyg';
		}
		else
		{
			$c['class']	.= ' inlinelabel';	
		}
		$output = form_textarea($opt);
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
	// Combobox
	elseif($c['type'] == 'combobox')
	{
		$opt = 'type = "select"';
		!empty($c['deactive']) ? $opt .= ' disabled = "disabled"' : '';
		!empty($c['id']) ? $opt .= ' id = "'.$c['id'].'_select"' : '';
		!empty($c['class']) ? $opt .= ' class = "'.$c['class'].' select"' : '';
		$output = form_dropdown($c['name'].'_select', $data['data'][$c['name']], $c['value'], $opt);
		//
		$opt = null;
		!empty($c['deactive']) ? $opt['disabled'] = "disabled" : '';
		!empty($c['id']) ? $opt['id'] = $c['id'] : '';
		$opt['name'] 	= $c['name'];		
		$opt['class'] 	= $c['class'];		
		$opt['value'] 	= $c['value'];		
		$output .= form_input($opt);	
		$output = '<div id="'.$c['id'].'_combobox" class="no-break rounded combobox-field">'.$output.'</div>';
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Image Browser Field
	elseif($c['type'] == 'browser')
	{	
		////// !!!!!!!		
		////// !!!!!!!		
		////// !!!!!!!
		// Needs work
		////// !!!!!!!
		$output = form_upload($opt);
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Variable replacement
	elseif($c['type'] == 'var')
	{	
		if( isset($c['var'][$c['name']]) && !is_array($c['var'][$c['name']]) )
		{
			$output = $c['var'][$c['name']];			
		}
		else
		{
			return FALSE;
		}
	}	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// get error message
	$return = form_error($c['name']) != FALSE ? form_error($c['name'], '<div class="error">', '</div>') : '';
	// part before form element
	$return .= '<div class="'.trim($c['class_type'].' '.(!empty($c['deactive']) ? 'disabled' : '').' '.$c['class']).' form-box"'.(!empty($error) ? ' error' : '').' >'.$c['before'];
	// form element label
	if(is_string($c['label']) && $c['label'] != null)
	{
		$return .= form_label($c['label'], $c['name'], array('class'=>'label'));		
	}
	elseif(is_array($c['label']) && array_key_exists($name, $c['label']))
	{
		$return .= form_label(lang($c['label'][$name]), $c['name'], array('class'=>'label'));			
	}
	elseif($c['label'] == null || $c['label'] == false)
	{
		
	}
	else{
		$return .= form_label(lang($name), $c['name'], array('class'=>'label'));		
	}
	// return
	return $return.$output.$c['after'].'</div>'."\n\t\t";
}
/**
 * Form - Create a form from given data
 *
 * @access	public
 * @param	array - parameters for the form
 * 	- config = name of the form config array (required)
 * 	- url = form action, leave blank for SELF 
 * 	- form_attributes = form attributes like id or class
 * 	- legend = set TRUE to display legend for fieldsets
 * @param	string 	- name of the template, leave blank for default 
 * @return	string
 */
function form($data, $template = NULL)
{
	// ----------------
	// get CI instance
	$CI = &get_instance();
	// ----------------
	// config
	if(!empty($data['config']))
	{
		foreach($CI->config->item($data['config']) as $key => $value)
		{
			$data[$key] = $value;
		}
	}
	// ----------------
	// prepare template
	$templates = $CI->config->item('templates');
	//
	if( empty($template) )
	{
		$template = $templates['form_default'];
	}
	elseif(substr($template, 0, 1) != '/')
	{
		$template = $templates['dir_form'].'/'.$template;
	}
	// ----------------
	// prepare url
	if( !empty($data['url']) )
	{
		if(substr($data['url'], 0, 1) == '/')
		{
			$data['url'] = lang_url().$data['url'];						
		}
		elseif(substr($data['url'], 0, 1) == '+')
		{
			$data['url'] = current_url().'/'.substr($data['url'], 1);			
		}
	}
	else
	{
		$data['url'] = current_url();
	}
	// ----------------
	return $CI->load->view($template, $data, TRUE);
}


/* End of file MY_form_helper.php */
/* Location: ./system/application/libraries/MY_form_helper.php */