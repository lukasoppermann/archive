<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Directories
|--------------------------------------------------------------------------
|
| Media Directory
*/
$config['dir_media'] 		= 'media/';
/*
| Layout Directory
*/
$config['dir_layout']	 	= 'media/layout/';
/*
| Images Directory
*/
$config['dir_images']	 	= 'media/images/';
/*
| JavaScript Directory
*/
$config['dir_js']			= 'libs/js/';
/*
| Stylesheet Directory
*/
$config['dir_css']			= 'libs/css/';
/*
|--------------------------------------------------------------------------
| Browser Abbreviations
|--------------------------------------------------------------------------
|
| Abbreviations for Browsers
|
| do not change array keys!
| values are used to assign css files (see below)
*/
$config['browsers']	= array(
							'msie' => 'ie', 
							'firefox' => 'ff', 
							'iphone' => 'iphone', 
							'opera' => 'op', 
							'chrome' => 'cm', 
							'mozilla' => 'ff', 
							'safari' => 'sf', 
							'mobile' => 'mobile'
							);
/*
|--------------------------------------------------------------------------
| Files
|--------------------------------------------------------------------------
|
| JavaScript
|
*/
$config['js']['browsercheck']				= TRUE;
//
$config['js']['browsers']					= $config['browsers'];
// Offset in milliseconds 360 000 000 = 360 000 seconds = 6000 minutes = 100 hours = ca. 4,16 days
$config['js']['gzip_expire']				= 0;
//
$config['js']['cache_dir']					= '_cache/';
//
$config['js']['dir']						= $config['dir_js'];
$config['js']['dir_script']					= $config['dir_js'].'scripts/';
//
$config['js']['files']['default'][1] 		= 'jquery-1.5.1.min'; // files in need to be loaded first
$config['js']['files']['default'][2] 		= 'ui/jquery.ui.core.min';
$config['js']['files']['default'][4]		= 'ui/jquery.ui.widget.min';
// $config['js']['files']['default'][5]		= 'ui/jquery.ui.position.min';
// $config['js']['files']['default'][6]		= 'ui/jquery.ui.selectmenu';
$config['js']['files']['default'][7]		= 'ui/jquery.ui.mouse.min';
$config['js']['files']['default'][8]		= 'ui/jquery.ui.sortable.min';
$config['js']['files']['default'][9]		= 'ui/jquery.ui.nestedSortable';
$config['js']['files']['default'][10]		= 'ui/jquery.effects.core.min';
// $config['js']['files']['default'][11]		= 'ui/jquery.effects.shake.min';
$config['js']['files']['default'][12]		= 'ui/jquery.effects.fade.min';
$config['js']['files']['default'][13]		= 'jquery.inlinelabel';
$config['js']['files']['default'][14]		= 'jquery.alphanumeric';
$config['js']['files']['default'][14]		= 'jquery.cookie';

// $config['js']['files']['default'][15] 		= 'jquery.geekga-1.2.min';
// $config['js']['files']['default'][16] 		= 'jquery.uploadify';

$config['js']['files']['default'][20] 		= 'jquery.selectbox-0.5';

$config['js']['files']['default'][22] 		= 'javascript';


$config['js']['files']['default'][21] 		= 'cms.ajax';


// $config['js']['files']['default'][1] 		= 'scripts/test';
// $config['js']['files']['default'][1] 				= '/jquery/jquery.ui';
// $config['js']['files']['default'][2] 			= 'jquery.dialog.min';
// $config['js']['files']['ie'][1] 			= 'jquery.ie.min';
//
$config['js']['scripts']['default']			= array(null/*'analytics'*/); // files with variables to be replace
// 
// variables need to ALWAYS be defined as array inside an array
$config['js']['variables']['default']		= array(array('code'=>'UA-7074034-XX')); // variables to replace
//
$config['js']['tags']['default']	=  "\t".'<script type="text/javascript" src="[$var]"></script>'."\n";
$config['js']['tags']['file']		=  "\t".'<script type="text/javascript" src="[$var]"></script>'."\n";
$config['js']['tags']['script']		=  "\t".'<script type="text/javascript">'."\n".'[$var]'."\n".'</script>'."\n";
$config['js']['tags']['ie']	 		= "\t".'<!--[if IE]>'."\n\t\t".'<link rel="stylesheet" type="text/javascript" href="[$var]">'."\n\t".'<![endif]-->'."\n";
$config['js']['tags']['ie9']	 	= "\t".'<!--[if IE 9]>'."\n\t\t".'<link rel="stylesheet" type="text/javascript" href="[$var]">'."\n\t".'<![endif]-->'."\n";
$config['js']['tags']['ie8']	 	= "\t".'<!--[if IE 8]>'."\n\t\t".'<link rel="stylesheet" type="text/javascript" href="[$var]">'."\n\t".'<![endif]-->'."\n";
$config['js']['tags']['ie7']	 	= "\t".'<!--[if IE 7]>'."\n\t\t".'<link rel="stylesheet" type="text/javascript" href="[$var]">'."\n\t".'<![endif]-->'."\n";
$config['js']['tags']['ie6']	 	= "\t".'<!--[if IE 6]>'."\n\t\t".'<link rel="stylesheet" type="text/javascript" href="[$var]">'."\n\t".'<![endif]-->'."\n";
/*
|
| Cascading Style Sheets
|
*/
$config['css']['browsercheck']		= TRUE;
//
// do not change array keys! values are used to assign css files (see below)
$config['css']['browsers']			= $config['browsers'];
//
// Offset in milliseconds 360 000 000 = 360 000 seconds = 6000 minutes = 100 hours = ca. 4,16 days
$config['css']['gzip_expire']		= 0;
//
// needs to be turned on for compression with chache_dir
$config['css']['absolute_path']		= TRUE;
$config['css']['cache_dir']			= '_cache/';
//
$config['css']['data_uri']			= FALSE;
// do data uri for given browsers
$config['css']['data_uri_accept']	= array('ff','op','cm','sf','iphone');
//
$config['css']['dir']				= $config['dir_css'];
//
// for performance optimization only use multiple styles for each category if compression is activated
// $config['css']['files']['reset'] 	= array('reset');
// $config['css']['files']['screen'][] = 'screen_old';
// $config['css']['files']['screen'][] = 'screen';
// $config['css']['files']['print'][] 	= 'print';

// $config['css']['files']['screen'][]	= 'ui/jquery.ui.all';
// $config['css']['files']['screen'][]	= 'ui/jquery.ui.selectmenu';
// $config['css']['files']['screen'][]	= 'ui/ui-lightness/jquery-ui-1.8.6.custom';
// $config['css']['files']['screen'][]	= 'ui/ui-lightness/jquery-ui-1.8.6.formandsystem';

//
// browserspecific files
// ie = internet explorer | ff = firefox | sf = safari | op = opera | cm = chrome
// $config['css']['files']['ie'] 		= array('screen.ie');
// $config['css']['files']['ie7'] 		= array('screen.ie7');
// $config['css']['files']['ie6'] 		= array('screen.ie6');
// $config['css']['files']['ie8'] 		= array('screen.ie8');
// $config['css']['files']['ie9'] 		= array('screen.ie9');
// $config['css']['files']['ff'] 		= array('screen.ff');
// $config['css']['files']['iphone'] 	= array('iphone');
//
$config['css']['tags']['reset'] 	= "\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n";
$config['css']['tags']['screen'] 	= "\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n";
$config['css']['tags']['ie']	 	= "\t".'<!--[if IE]>'."\n\t\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n\t".'<![endif]-->'."\n";
$config['css']['tags']['ie9']	 	= "\t".'<!--[if IE 9]>'."\n\t\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n\t".'<![endif]-->'."\n";
$config['css']['tags']['ie8']	 	= "\t".'<!--[if IE 8]>'."\n\t\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n\t".'<![endif]-->'."\n";
$config['css']['tags']['ie7']	 	= "\t".'<!--[if IE 7]>'."\n\t\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n\t".'<![endif]-->'."\n";
$config['css']['tags']['ie6']	 	= "\t".'<!--[if IE 6]>'."\n\t\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="screen" />'."\n\t".'<![endif]-->'."\n";
$config['css']['tags']['print'] 	= "\t".'<link rel="print" type="text/css" href="[$var]" media="print" />'."\n";
$config['css']['tags']['iphone'] 	= "\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="handheld" />'."\n";
$config['css']['tags']['mobile'] 	= "\t".'<link rel="stylesheet" type="text/css" href="[$var]" media="handheld" />'."\n";
//
/* End of file resources.php */
/* Location: ./system/application/config/resources.php */