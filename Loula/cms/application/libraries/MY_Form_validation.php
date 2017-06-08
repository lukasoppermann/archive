<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{
     function __construct($config = array())
     {
          parent::__construct($config);
     }
 
    /**
     * add_error
     *
     * Adds error messages to error array
     *
     * @return  array
     */
    function add_error($message)
    {
		if(!isset($this->_error_array['separate']))
		{
			$this->_error_array['separate'] =$message;
		}
		else
		{
			$this->_error_array['separate'] .=$message;
		}
    }
}
