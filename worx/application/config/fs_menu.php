<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// Main Menu
$config['nav_array'][0] = array(
    'label'     => 'Dashboard',
    'path'      => '/dashboard',
    'rights'    => array('*')
);
// Homepage ------------------------
$config['nav_array'][1] = array(
    'label'     => 'Homepage',
    'path'      => '/homepage',
    'rights'    => array('1','2')
);
// Content ------------------------
$config['nav_array'][2] = array(
    'label'     => 'Content',
    'path'      => '/content',
    'rights'    => array('1','2')
);
	// Edit Content
	$config['nav_array'][2]['sub'][] = array(
	'default' 	=> true,
	    'label'     => 'Edit Content',
	    'path'      => '/content/list',
	    'rights'    => array('1','2')
	);
	// Create Product
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Product',
		'path'      => '/content/edit/product',
		'rights'    => array('1','2')
	);
	// Create News
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create News',
		'path'      => '/content/edit/news',
		'rights'    => array('1','2')
	);
	// Create Page
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Page',
		'path'      => '/content/edit/page',
		'rights'    => array('1','2')
	);
	// Create Career
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Career',
		'path'      => '/content/edit/career',
		'rights'    => array('1','2')
	);
	// Create History
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create History',
		'path'      => '/content/edit/history',
		'rights'    => array('1','2')
	);
	// Create Company
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Company',
		'path'      => '/content/edit/company',
		'rights'    => array('1','2')
	);
	// Create Policies
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Policies',
		'path'      => '/content/edit/policies',
		'rights'    => array('1','2')
	);
	// Create Contact
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Contact',
		'path'      => '/content/edit/contact',
		'rights'    => array('1','2')
	);
// Media ------------------------
$config['nav_array'][3] = array(
    'label'     => 'Multimedia',
    'path'      => '/media',
    'type'      => 2,
    'rights'    => array('*'),
	'class' 	=> 'media',
	'after' 	=> ''
);
// Settings ------------------------
// Users Settings
$config['nav_array'][4] = array(
    'label'     => 'Users',
    'path'      => '/users',
    'rights'    => array('1','2')
);
// Settings ------------------------
$config['nav_array'][5] = array(
    'label'     => 'Settings',
    'path'      => '/settings',
    'type'      => 2,
    'rights'    => array('1','2')
);
// Settings ------------------------
$config['nav_array'][6] = array(
    'label'     => 'Orders',
    'path'      => '/orders',
    'type'      => 2,
    'rights'    => array('1','2')
);
// -------------------------------
// User menu
$config['user_nav_array'][1] = array(
    'label'     => 'Profile',
    'path'      => '/profile',
    'rights'    => array('*')
);
$config['user_nav_array'][1]['sub'][] = array(
	'label'     => 'Profile Settings',
	'path'      => '/profile',
	'rights'    => array('*')
);
$config['user_nav_array'][1]['sub'][] = array(
    'label'     => 'Logout',
    'path'      => '/logout',
    'rights'    => array('*')
);
// Content ------------------------
$config['user_nav_array'][2] = array(
    'label'     => 'Help',
    'path'      => '/help',
    'rights'    => array('*')
);
$config['user_nav_array'][2]['sub'][] = array(
    'label'     => 'Show Help',
    'path'      => '/help/show',
    'rights'    => array('1')
);
$config['user_nav_array'][2]['sub'][] = array(
    'label'     => 'Edit Help',
    'path'      => '/help/edit',
    'rights'    => array('1')
);