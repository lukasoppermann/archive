<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config = array(
	'upload_name' => array(
		array(
			'field' => 'upload_name',
			'label' => 'Angezeigter Name',
			'rules' => 'required'
		)
	),
	'product_cat' => array(
		array(
			'field' => 'headline',
			'label' => 'Kategoriename',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'content',
			'label' => 'Beschreibungstext',
			'rules' => 'trim|required'
		)		
	),                     
);