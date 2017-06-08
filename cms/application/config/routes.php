<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "dashboard";
$route['scaffolding_trigger'] = "";

/*
| -------------------------------------------------------------------------
| URL LANGUAGE ROUTES
| -------------------------------------------------------------------------
*/
//route example: http://domain.tld/en/controller => http://domain.tld/controller
// Dashboard
$route['(\w{2})/dashboard'] = 'dashboard';
$route['(\w{2})/dashboard/:any'] = "dashboard";
// Navigation
$route['(\w{2})/menu'] = 'menu';
$route['(\w{2})/menu/connect'] = "menu/connect";
$route['(\w{2})/menu/(:any)'] = "menu/index/$2";
// Entries
$route['(\w{2})/entries'] = 'entries/overview';
$route['(\w{2})/entries/overview'] = "entries/overview";
$route['(\w{2})/entries/new-entry/(:any)'] = "entries/new_entry/$2";
$route['(\w{2})/entries/new-entry'] = "entries/new_entry";
$route['(\w{2})/entries/edit-entry/(:any)'] = "entries/edit_entry/$2";
$route['(\w{2})/entries/templates'] = "entries/templates";
$route['(\w{2})/entries/channels'] = "entries/channels";
$route['(\w{2})/entries/([a-z_-]+)/(:num)'] = "entries/overview/$2/$3";
$route['(\w{2})/entries/(:any)'] = "entries/overview/$2";
// Settings
$route['(\w{2})/downloads'] = 'downloads';
$route['(\w{2})/downloads/:any'] = "downloads";
// Downloads
$route['(\w{2})/settings'] = 'settings';
$route['(\w{2})/settings/:any'] = "settings";
// Settings
$route['(\w{2})/auth'] = 'auth';
$route['(\w{2})/auth/:any'] = "auth";
// Ajax Actions
$route['(\w{2})/ajax'] = 'ajax';
$route['(\w{2})/ajax/(:any)'] = "ajax/$2";

$route['(\w{2})/ajax_upload'] = 'ajax_upload';
// $route['(\w{2})/ajax_actions/rearrange_menu'] = "ajax_actions/rearrange_menu";
//
$route['(\w{2})/(.*)'] = $route['default_controller'];
$route['(\w{2})'] = $route['default_controller'];


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */