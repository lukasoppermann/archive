<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Fs_base_model Class
 *
 * Base Model to be extend by every model
 *
 * @package					CodeIgniter
 * @subpackage			Model
 * @category			Model
 */
class Fs_base_model extends CI_Model {
	
	/**
	* Specify the primary table to execute queries on
	*
	* @var string
	*/
	protected $primary_table = '';

	/**
	* Fields that are allowed to be inserted or updated
	*
	* @var array
	*/
	protected $fields = array();

	/**
	* Fields that are required to insert or update a record
	*
	* @var array
	*/
	protected $required_fields = array();

	/**
	* Specify additional models to be loaded
	*
	* @var array
	*/
	protected $models = array();

	/**
	* Set the primary key for the table
	*
	* @var string
	*/
	protected $primary_key = 'id';

	/**
	* Boolean to toggle field existence checks. IMPORTANT: ONLY use for debugging, slow due to the amount of necessary requests.
	*
	* @var bool
	*/
	protected $validate_field_existence = FALSE;

	/**
	* Used if there is no primary key for the table
	*
	* @var bool
	*/
	protected $no_primary_key = FALSE;

	// constructor
	function __construct()
	{
		parent::__construct();
		
		// if models have been specified for loading, load them now
		if ( ! empty($this->models))
		{
			foreach ($this->models as $model)
			{
				$this->load->model($model);
			}
		}

	}
	
	/**
	 * add method creates a record in the table.
	 *
	 * Options: array of fields available
	 *
	 * @param array $options
	 * @return int ID on success, bool false on fail
	 */
	function add($options = array())
	{
		// check if all required fields are in the options array
		if ( ! $this->_required($this->required_fields, $options))
		{
			return FALSE;
		}
		
		// get all the fields that are editable for this module
		$this->_set_editable_fields();
		
		// validate that fields exist in DB, if turned on (Debugging only)
		$this->_validate_options_exist($options);
		
		// set default values for add
		$default = array(
			'date_created' => date($this->config->item('log_date_format')),
			'date_modified' => date($this->config->item('log_date_format'))
		);
		
		// combine default values with options
		$options = $this->_default($default, $options);
		
		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		foreach ($this->fields as $field)
		{
			if (isset($options[$field]))
			{
				$this->db->set($field, $options[$field]);
			}
		}
		
		// build query
		$query = $this->db->insert($this->primary_table);
		
		// check if insert was successful
		if ($query)
		{
			// if primary key is used, return primary key
			if ($this->no_primary_key == FALSE)
			{
				return $this->db->insert_id();
			}
			// else return TRUE
			else
			{
				return TRUE;
			}
		}
	}

	/**
	 * add_batch creates multiple record in the table.
	 *
	 * Options: array of fields available
	 *
	 * @param array $options
	 * @return array of IDs on success, bool false on fail
	 */
	function add_batch( $options_batch = array() )
	{
		// get all the fields that are editable for this module
		$this->_set_editable_fields();
		
		// set default values for add
		$default = array(
			'date_created' => date($this->config->item('log_date_format')),
			'date_modified' => date($this->config->item('log_date_format'))
		);
		
		// loop through options to test every add elements
		foreach( $options_batch as $options )
		{
			// check if options is array (meaning fields) and not a ral option like sort
			if( is_array($options) )
			{
				// check if all required fields are in the options array
				if ( ! $this->_required($this->required_fields, $options))
				{
					return FALSE;
				}
			
				// validate that fields exist in DB, if turned on (Debugging only)
				$this->_validate_options_exist($options);
						
				// combine default values with options
				$options = $this->_default($default, $options);

				// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
				foreach ($this->fields as $field)
				{
					if (isset($options[$field]))
					{
						$data[$field] = $options[$field];
					}
				}
			
				// add data to batch
				$batch[] = $data;
			
				// check if ids are needed
				if( isset($options_batch['return_ids']) && $options_batch['return_ids'] == true && $this->no_primary_key == FALSE )
				{
					// build query
					$query = $this->db->insert($this->primary_table, $data);
					// save ids
					$ids[] = $this->db->insert_id();
				}
			
				// reset data
				$data = array();
			}
		}
		
		// check if ids are NOT needed
		if( !isset($options_batch['return_ids']) || $options_batch['return_ids'] == false || $this->no_primary_key != FALSE )
		{
			// build batch query
			$query = $this->db->insert_batch($this->primary_table, $batch);
		}
		
		// check if insert was successful
		if ($query)
		{
			// if primary key is used, return primary key
			if( isset($options_batch['return_ids']) && $options_batch['return_ids'] == true && $this->no_primary_key == FALSE )
			{
				return $ids;
			}
			// else return TRUE
			else
			{
				return TRUE;
			}
		}
	}
	

	/**
	 * get method returns an array of qualified record objects
	 *
	 * Option: Values
	 *
	 * Returns (array of objects)
	 *
	 * @param array $options
	 * @return array result()
	 */
	function get($options = array())
	{
		// set default values
		$defaults = array(
			'sort_direction' => 'asc'
		);
		$options = $this->_default($defaults, $options);

		// get all the fields that are editable for this module
		$this->_set_editable_fields($this->primary_table);
		
		// build where statement
		foreach ($this->fields as $field)
		{
			if (isset($options[$field]))
			{
				// if field value is a string / int
				if ( !is_array($options[$field]) )
				{
					$this->db->where($field, $options[$field]);
				}
				
				// if field value is array WHERE IN statement
				else
				{
					$this->db->where_in($field, $options[$field]);
				}
			}
		}

		// add limit if set
		if (isset($options['limit']))
		{
			if (isset($options['offset']))
			{
				$this->db->limit($options['limit'], $options['offset']);
			}
			else
			{
				$this->db->limit($options['limit']);
			}
		}

		// add sort by if set
		if (isset($options['sort_by']))
		{
			$this->db->order_by($options['sort_by'], $options['sort_direction']);
		}
		
		// return query result object
		return $this->db->get($this->primary_table);
	}

	/**
	 * get_array method returns a prepared array of the selected rows
	 *
	 * Option: Values
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */
	function get_array($options = array())
	{
		// get data from db
		$query = $this->get($options);
		
		// if an id was specified we know you only are retrieving a single record so we return the object
		if ( isset($options[$this->primary_key]) && !is_array($options[$this->primary_key]) )
		{
			// get array of row
			$result = $query->row_array();
			
			// decode json
			if( isset($options['json']) && $options['json'] != FALSE )
			{
				foreach( $result as $field => $value )
				{
					if( in_array($field, $options['json']) )
					{
						$result[$field] = json_decode($value, TRUE);
					}
				}
			}
		}
		
		// if possible multiple results are present
		else
		{
			// decode json
			if( isset($options['json']) && $options['json'] != FALSE )
			{
				foreach( $result = $query->result_array() as $k => $r )
				{
					foreach( $r as $field => $value )
					{
						if( in_array($field, $options['json']) )
						{
							$result[$k][$field] = json_decode($value, TRUE);
						}
					}
				}
			}
			
			// nothing to decode
			else
			{
				foreach( $result = $query->result_array() as $k => $r )
				{
					foreach( $r as $field => $value )
					{
						$result[$k][$field] = $value;
					}
				}
			}
		}
		
		// return array
		return $result;
	}
	
	/**
	 * get_object method returns a prepared object of the selected rows
	 *
	 * Option: Values
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */
	function get_object($options = array())
	{
		// get data from db
		$query = $this->get($options);
		
		// if an id was specified we know you only are retrieving a single record so we return the object
		if ( isset($options[$this->primary_key]) && !is_array($options[$this->primary_key]) )
		{
			// get array of row
			$result = $query->row();
			
			// decode json
			if( isset($options['json']) && $options['json'] != FALSE )
			{
				foreach( $result as $field => $value )
				{
					if( in_array($field, $options['json']) )
					{
						$result->$field = json_decode($value);
					}
				}
			}
		}
		
		// if possible multiple results are present
		else
		{
			// decode json
			if( isset($options['json']) && $options['json'] != FALSE )
			{
				foreach( $result = $query->result() as $k => $r )
				{
					foreach( $r as $field => $value )
					{
						if( in_array($field, $options['json']) )
						{
							$result[$k]->$field = json_decode($value);
						}
					}
				}
			}
			
			// nothing to decode
			else
			{
				foreach( $result = $query->result() as $k => $r )
				{
					foreach( $r as $field => $value )
					{
						$result[$k]->$field = $value;
					}
				}
			}
		}
		
		// return array
		return $result;
	}

	/**
	 * update method alters a record in the table.
	 *
	 * Option: Values
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */
	function update($options = array())
	{
		// check if all required fields are in the options array
		if ( ! isset($options['where']) )
		{
			return FALSE;
		}
		// get all the fields that are editable for this module
		$this->_set_editable_fields($this->primary_table);

		// validate that fields exist in DB, if turned on (Debugging only)
		$this->_validate_options_exist($options);

		// set default values for update
		$default = array(
			'date_modified' => date($this->config->item('log_date_format'))
		);
		$options = $this->_default($default, $options);

		// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
		foreach ($this->fields as $field) 
		{
			if (isset($options[$field]))
			{
				$this->db->set($field, $options[$field]);
			}
		}

		// build where statement
		foreach ($this->fields as $field)
		{
			if (isset($options['where'][$field]))
			{
				// if field value is a string / int
				if ( !is_array($options['where'][$field]) )
				{
					$this->db->where($field, $options['where'][$field]);
				}
				
				// if field value is array WHERE IN statement
				else
				{
					$this->db->where_in($field, $options['where'][$field]);
				}
			}
		}

		// run update query
		$this->db->update($this->primary_table);

		// return affected rows
		return $this->db->affected_rows();
	}

	/**
	 * delete method removes a record from the table
	 *
	 * Option: Values
	 * --------------
	 *
	 * @param array $options
	 */
	function delete($options = array())
	{
		// build where statement
		foreach ($this->fields as $field)
		{
			if (isset($options[$field]))
			{
				// if field value is a string / int
				if ( !is_array($options[$field]) )
				{
					$this->db->where($field, $options[$field]);
				}
				
				// if field value is array WHERE IN statement
				else
				{
					$this->db->where_in($field, $options[$field]);
				}
			}
		}
		
		// delete and get affected rows
		$this->db->delete($this->primary_table);
		$affected_rows = $this->db->affected_rows();

		// reset autoincrement for primary key
		$this->db->query('ALTER TABLE '.$this->primary_table.' AUTO_INCREMENT = 1');
		
		// return affected rows
		return $affected_rows;
	}

	/**
	* Validates that the fields you are trying to modify actually exist in the database
	* 
	* Only use this method for debugging, not fit for production code because of the number of queries it has to run
	*
	* @param string $options 
	* @return void
	*/
	function _validate_options_exist($options)
	{
		if ($this->validate_field_existence == TRUE)
		{
			foreach ($options as $key => $value)
			{
				if ( ! $this->db->field_exists($key, $this->primary_table))
				{
					show_error('You are trying to insert data into a field that does not exist.  The field "'. $key .'" does not exist in the "'. $this->primary_table .'" table.');
				}
			}
		}
	}

	/**
	* set editable fields by pulling fields dynamically from the table if no fields are specified in the model
	*
	* @return void
	*/
	function _set_editable_fields()
	{
		if (empty($this->fields))
		{
			// pull the fields dynamically from the database
			$this->db->cache_on();
			$this->fields = $this->db->list_fields($this->primary_table);
			$this->db->cache_off();
		}
	}

	/**
	* _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/
	function _required($required, $data)
	{
		foreach ($required as $field)
		{
			if ( ! isset($data[$field]))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	* _default method combines the options array with a set of defaults giving the values in the options array priority.
	*
	* @param array $defaults
	* @param array $options
	* @return array
	*/
	function _default($defaults, $options)
	{
		return array_merge($defaults, $options);
	}

	/**
	* _json method creates an object/array from json string or returns and empty object/array
	*
	* @param string $json
	* @param array $options
	* @return array
	*/
	function _json($json, $type = null)
	{
		if( $type == 'array' )
		{
			if( $json = json_decode($json, TRUE) )
			{
				return $json;
			}
			else
			{
				return array();
			}
		}
		
		// return object
		else
		{
			if( $json = json_decode($json) )
			{
				return $json;
			}
			else
			{
				return new stdClass();
			}
		}
	}

// end Fs_base_model Class
}
