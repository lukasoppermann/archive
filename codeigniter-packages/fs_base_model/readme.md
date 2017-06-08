# FS base model

Basic CRUD model for CodeIgniter. A normal model class is used instead of the MY_Model class, because it can be saved as a package in the CI system folder and use in all  applications by extending the `FS_base_model` when creating a new model.

## Basic example

Simply extend the `FS_base_model` when creating your individual models.

	class Blog_model extends FS_base_model {

		var $primary_table = 'posts';

		var $validate_field_existence = FALSE;

		var $fields = array(
			'id',
			'title',
			'excerpt',
			'text',
			'date_created',
			'date_modified'
		);

		var $required_fields = array(
			'title',
			'text'
		);
	}
	
Now you can use the methods from the `FS_base_model` class.

	$options = array(
		'title' => $this->input->post('titel'),
		'text' => $this->input->post('text')
	);
	
	$post = $this->blog_model->add($options);
	
## Available Options

This options can be set in the model with which the base_model class is extended `$this->variable_name`.

	protected $primary_table = '';
_string_ Specify the primary table to execute queries on 

	protected $fields = array();
_array_ Fields that are allowed to be inserted or updated

	protected $required_fields = array();
_array_ Fields that are required to insert or update a record

	protected $models = array();
_array_ Specify additional models to be loaded

	protected $primary_key = 'id';
_string_ Set the primary key for the table

	protected $validate_field_existence = FALSE;
_bool_ Boolean to toggle field existence checks. IMPORTANT: ONLY use for debugging, slow due to the amount of necessary requests.

	protected $no_primary_key = FALSE;
_bool_ Used if there is no primary key for the table

## Available methods

### add

The `add` method inserts one entry into the database with the information provided in the `$options` array and returns the id.

	$options = array(
		'title' => $this->input->post('titel'),
		'text' => $this->input->post('text')
	);

	$post = $this->blog_model->add($options);
	
##### Options
The `$options` array holds the data to add to the db in the format `'field_name'=>'value'`.

### add_batch

The `add_batch` method inserts multiple entries into the database with the information provided in the `$options` array.

	$options = array(
		array(
			'title' => $post[0]['title'],
			'text' => $post[0]['texte']
		),
		array(
			'title' => $post[1]['title'],
			'text' => $post[1]['texte']
		)
	);

	$posts = $this->blog_model->add_batch($options);
	
##### Options
The `$options` array holds the data to add to the db in individual arrays per entry.

`return_ids` _boolean_ if set to `TRUE`, the method returns an array containing the ids of the newly added entries. 
_Warning: Slows down insert because of the need of individual insert statements_

### get

	$options = array(
		'category' => $this->input->post('category'),
		'tag' => array('code','design','interface')
	);

	$posts = $this->blog_model->get($options); 
	
The `get` method runs a query and returns the query result which needs to be prepared with the result functions like result() or row() to be used further on.

##### Options
`limit` limits the amount of retrieved entries.

`offset` defines from which entry the limiting starts, e.g. 10 entries starting from entry 25 = entry 25 to 35.

`sort_direction` asc or desc, the direction by which the returned entries are sorted.

### get_array

	$options = array(
		'category' => $this->input->post('category'),
		'tag' => array('code','design','interface')
	);

	$posts = $this->blog_model->get_array($options);
	
The `get_array` method runs a query and returns the query result as an array of result arrays or as a single array if only one result is expected due to the usage of one primary key value. 

##### Options
All options available to the `get` method.

`json` option which expects an array of field names or `FALSE` as an argument. If an array of field names is given, the method decodes the values of those fields with the `json_decode` function and replaces the value with the decoded array.

### get_object

	$options = array(
		'category' => $this->input->post('category'),
		'tag' => array('code','design','interface')
	);

	$posts = $this->blog_model->get_object($options);

The `get_object` method runs a query and returns the query result as an object with the individual result objects or as a single object if only one result is expected due to the usage of one primary key value.

##### Options
All options available to the `get` method.

`json` option which expects an array of field names or `FALSE` as an argument. If an array of field names is given, the method decodes the values of those fields with the `json_decode` function and replaces the value with the decoded object.

### update

The `update` method updates one ore more entries in the database and returns the number of affected rows.

	$options = array(
		'where' => array('tag' => array('code','design','interface'), 'status' => 1 ),
		'title' => 'new title',
		'modified' => date()
		);

	$posts = $this->blog_model->update($options);

##### Options
`where` option holds the conditions to find the entries to be updated, if a value is an array like `'id'=>array(1,2,3)` the method creates a `WHERE IN` condition.

The fields and values are direct children of the $options array.

### delete

	$options = array(
		'type' => 'post',
		'id' => array(1,2,3,4)
	);

	$this->blog_model->delete($options);
	
The `delete` method deletes one or more entries from a table. The conditions are set in the same manner as they are in the get method.

##### Options

## Utility methods

### _validate_options_exist
	$this->_validate_options_exist($options);

Validates that the fields (from options array) you are trying to modify actually exist in the database.
_Warning: Only use this method for debugging, not fit for production code because of the number of queries it has to run_

### _set_editable_fields
	$this->_set_editable_fields();
	
Set editable fields by pulling fields dynamically from the table if no fields are specified in the model.

### _required
	if ( ! $this->_required($this->required_fields, $options))
	{
		return FALSE;
	}
	
_required method returns `FALSE` if the `$data` array does not contain all of the keys assigned by the `$required` array.

### _default
	$default = array(
		'date_modified' => date($this->config->item('log_date_format'))
	);
	$options = $this->_default($default, $options);

_default method combines the options array with a set of defaults giving the values in the options array priority.

### _json

	$json = $this->_json($json, $type = "array");
	
_json method turns a json string into and object (default) or array depending on value of `$type` (array|object).
	