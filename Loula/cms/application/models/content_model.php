<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 * Content model
 *
 * @author		Lukas Oppermann - veare.net
 */
class Content_model extends MY_Model {
	
	//
	function cat_select($name, $type, &$current = null)
	{
		// fetch data & index by position
		$categories = index_array($this->db_fetch('client_data',array('where' => array('key' => 'product', 'type' => $type))), 'position');
		// sort 
		ksort($categories);
		// prep data for select
		foreach($categories as $pos => $val)
		{
			$values[$val['tag']] = ucfirst($val['label']);
		}
		// return select
		return form_dropdown($name, $values, $current);
	}
	
	//
	function cat_select_edit($name, $type, $data = null)
	{
		// fetch data & index by position
		$categories = index_array($this->db_fetch('client_data',array('where' => array('key' => 'product', 'type' => $type))), 'position');
		// sort 
		ksort($categories);
		// prep data for select
		$values[] = '<option value="add_new">-- add new entry --</option>';
		foreach($categories as $pos => $val)
		{
			if( is_array($data) && count($data) > 0 )
			{
				foreach($data as $key)
				{
					if( isset($val[$key]) )
					{
						if( !is_array($val[$key]) )
						{
							$d[] = 'data-'.$key.'="'.$val[$key].'"';
						}
						else
						{
							$d[] = 'data-'.$key.'="'.implode(', ', $val[$key]).'"';
						}
					}
				}
				$_d = implode(' ',$d);
			}
			$values[] = '<option value="'.$val['id'].'" '.variable($_d).'>'.ucfirst($val['label']).'</option>';
			unset($_d, $d);
		}
		// return select
		return '<select class="category-select" name="'.$name.'" id="'.$name.'">'.implode('',$values).'</select>';
	}
	
// close class	
}