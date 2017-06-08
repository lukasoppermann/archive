<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Form Validation
| -------------------------------------------------------------------
|
*/
$config = array(
	'error_delimiter' => array(
		'<p class="error">',
		'</p>'
	),	
	'validation_email' => array(
		array(
		        'field' => 'email',
		        'label' => 'lang:form_email',
		        'rules' => 'trim|required|valid_email'
		     ),
		array(
		        'field' => 'name',
		        'label' => 'lang:form_name',
		        'rules' => 'trim|required'
		     ),
		array(
		        'field' => 'subject',
		        'label' => 'lang:form_subject',
		        'rules' => 'trim|required'
		     ),
		array(
		        'field' => 'message',
		        'label' => 'lang:form_message',
		        'rules' => 'trim|required'
		     )
	),
    'validation_signup' => array(
        array(
                'field' => 'emailaddress',
                'label' => 'EmailAddress',
                'rules' => 'required|valid_email'
             ),
        array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|alpha'
             ),
        array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'required'
             ),
        array(
                'field' => 'message',
                'label' => 'MessageBody',
                'rules' => 'required'
             )
        )                          
);

/* End of file form_validation.php */
/* Location: ./system/application/config/form_validation.php */