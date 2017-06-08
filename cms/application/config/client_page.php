<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Client Prefix
|--------------------------------------------------------------------------
|
| Prefix of client Page
|
*/
$config['client_prefix']					= "mp_";
/*
|--------------------------------------------------------------------------
| Client folders
|--------------------------------------------------------------------------
|
| Folders of the client project
|
*/
$config['client_base_dir']					= "../../../hygromatik/";
$config['client_base_url']					= "http://www:8888/hygromatik/";
$config['client_application']				= $config['client_base_dir']."application";
$config['client_folder']					= "./../hygromatik";
$config['client_config']					= $config['client_application'].'/config/';
/*
|--------------------------------------------------------------------------
| Client files
|--------------------------------------------------------------------------
|
| Files of the client project
|
*/
// config.php
$config['client_config_default']			= $config['client_config']."config";
// database.php
$config['client_config_database']			= $config['client_config']."database";
