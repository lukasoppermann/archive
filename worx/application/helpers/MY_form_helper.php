<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter MY_form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Lukas Oppermann - veare.net
 * @link		http://doc.formandsystem.com/helpers/form
 */

// ------------------------------------------------------------------------

/**
 * Form Error
 *
 * Returns the error for a specific form field.  This is a helper for the
 * form validation class.
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */
function form_data($item = '')
{
	if (FALSE === ($OBJ =& _get_validation_object()))
	{
		return '';
	}

	return $OBJ->form_data($item);
}
// ------------------------------------------------------------------------
/**
 * fs_select
 *
 * @param array
 * @param string
 * @return html
 */
function fs_select( $options, $current = null, $opts = null )
{
	if( isset($options) && is_array($options) )
	{
		//  merge $opts
		$opts = array_merge(array(
			'name' => '', 
			'id' => '', 
			'class' => '',
			'select_class' => 'hidden-elements',
			'selection_class' => 'selection',
			'selection_li_class' => 'option',
			'selected_class' => 'selected',
			'opts' => ''), 
		(array) $opts);
		// loop through elements
		foreach( $options as $key => $option )
		{
			// set selected
			if( !isset($selected) || $current == $key )
			{
				if( !is_array($option) )
				{
					$selected = $option;
				}
				else
				{
					$selected = $option['name'];
				}
			}
			$checked = null;
			$active = null;
			// set active select
			if( $current == $key )
			{
				$checked = ' selected = "selected"';
				$active = ' active';
			}
			//
			if( !is_array($option) )
			{
				$select[] 		= '<option value="'.$key.'"'.$checked.'>'.$option.'</option>';
				$selection[]	= '<li'.($opts['selection_li_class'] != null ? ' class="'.$opts['selection_li_class'].$active.'"' : '').
								' data-value="'.$key.'">'.$option.'</li>';
			}
			else
			{
				$select[] 		= '<option value="'.$key.'"'.$checked.'>'.$option['name'].'</option>';
				$selection[]	= '<li'.($opts['selection_li_class'] != null ? ' class="'.$opts['selection_li_class'].$active.'"' : '').
								' data-value="'.$key.'" '.$option['data'].'>'.$option['name'].'</li>';
			}
		}
		// return select
		return '<div'.($opts['id'] != null ? ' id="'.$opts['id'].'"' : '').
						($opts['class'] != null ? ' class="'.$opts['class'].'"' : '').
						($opts['opts'] != null ? ' '.$opts['opts'] : '').'>'.
							'<span'.($opts['selected_class'] != null ? ' class="'.$opts['selected_class'].'"' : '').'>'.$selected.'</span>'.
						'<ul'.($opts['selection_class'] != null ? ' class="'.$opts['selection_class'].'"' : '').'>'.implode('',$selection).'</ul>'.
						'<select'.($opts['select_class'] != null ? ' class="'.$opts['select_class'].'"' : '').
						($opts['name'] != null ? ' name="'.$opts['name'].'"' : '').'>'.
						implode('',$select).'</select>'.
				'</div>';
	}
}
/* End of file form_helper.php */
/* Location: ./system/helpers/form_helper.php */