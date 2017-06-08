<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Main Menu
$config['nav_array'][1] = array(
   'label'     => 'About us',
   'path'      => '/about',
   'rights'    => array('*')
);
// Tips & Tricks ------------------------
$config['nav_array'][2] = array(
    'label'     => 'Alterations Services',
    'path'      => '/services',
    'rights'    => array('*')
);
// Clients ------------------------
$config['nav_array'][3] = array(
    'label'     => 'Retail Partners',
    'path'      => '/clients',
    'rights'    => array('*')
);
// Workshops ------------------------
$config['nav_array'][4] = array(
    'label'     => 'Tailoring Schools',
    'path'      => '/workshops',
    'rights'    => array('*')
);
// Tips & Tricks ------------------------
$config['nav_array'][5] = array(
    'label'     => 'Tips and Tricks',
    'path'      => '/tips',
    'rights'    => array('*')
);
// Franchising ------------------------
$config['nav_array'][6] = array(
   'label'     => 'Franchising',
   'path'      => '/franchise',
   'rights'    => array('*')
);
// Contact ------------------------
$config['nav_array'][7] = array(
   'label'     => 'Contact',
   'path'      => '#contact',
	 'class' 		 => 'contact-us',
   'rights'    => array('*')
);