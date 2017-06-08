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
|	example.com/class/method/id/
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
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "portfolio";
$route['404_override'] = '';
// Blog
$route['(\w{2})/blog'] = 'blog/index';
$route['(\w{2})/blog/?(.*)?'] = 'blog/entry/$2';
// Portfolio
$route['(\w{2})/portfolio'] = 'portfolio';
$route['(\w{2})/portfolio/?(.*)?'] = 'portfolio/index/$2';
// About
$route['(\w{2})/about'] = 'about';
$route['(\w{2})/about/?(.*)?'] = 'about/$2';
// Labs
$route['(\w{2})/labs'] = 'labs';
$route['(\w{2})/labs/?(.*)?'] = 'labs/index/$2';
// Contact
$route['(\w{2})/contact'] = 'contact';
$route['(\w{2})/contact/?(.*)?'] = 'contact/$2';
// Default
$route['(\w{2})/?(.*)?'] = $route['default_controller']."/index/$2";
$route['(.*)'] = $route['default_controller']."/index/$1";
// -------------------------------------------------------------------------
/* End of file routes.php */
/* Location: ./application/config/routes.php */