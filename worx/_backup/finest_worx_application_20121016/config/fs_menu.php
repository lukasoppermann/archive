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
	// Create Service
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Service',
		'path'      => '/content/edit/service',
		'rights'    => array('1','2')
	);
	// Create Workshops
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Workshop',
		'path'      => '/content/edit/workshop',
		'rights'    => array('1','2')
	);
	// Create Tip
	$config['nav_array'][2]['sub'][] = array(
		'label'     => 'Create Tip',
		'path'      => '/content/edit/tip',
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
// Settings ------------------------
// Users Settings
$config['nav_array'][3] = array(
    'label'     => 'Users',
    'path'      => '/users',
    'rights'    => array('1','2')
);
// Settings ------------------------
$config['nav_array'][4] = array(
    'label'     => 'Settings',
    'path'      => '/settings',
    'type'      => 2,
    'rights'    => array('1','2')
);
// Ticketing ------------------------
$config['nav_array'][5] = array(
    'label'     => 'Tickets',
    'path'      => '/tickets',
    'type'      => 2,
    'rights'    => array('*'),
	'class' 	=> 'tickets',
	'after' 	=> ''
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