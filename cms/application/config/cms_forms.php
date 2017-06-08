<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| CMS Menu Item
|--------------------------------------------------------------------------
|
| Menu Items
|
| Scheme: $config['form_pages'][ field type (e.g. input) ][ page data (e.g. content)] = array( options (e.g. maxlength = 150) )
*/
$config['form_menu']['field']['parent_id'] 		= array('type' => 'hidden');
$config['form_menu']['field']['type'] 			= array('type' => 'hidden');
$config['form_menu']['field']['label'] 			= array('type' => 'input');
$config['form_menu']['field']['path'] 			= array('type' => 'input', 'deactive' => TRUE, 'toggle' => TRUE);
$config['form_menu']['field']['title']			= array('type' => 'input');
$config['form_menu']['button']['submit'] 		= array('label' => 'save');
/*
| Group
*/
$config['form_menu']['fieldset']['add_item']['items'] 		= array('parent_id','type','label','path','title');
$config['form_menu']['fieldset']['add_item']['attributes'] 	= array('class' => 'fieldset inline');
$config['form_menu']['form_attributes'] 					= array('autocomplete' => 'off', 'id' => 'add_item');
/*
| Menu Items Edit
*/
$config['form_menu_edit']['field']['id'] 			= array('type' => 'hidden');
$config['form_menu_edit']['field']['type'] 			= array('type' => 'hidden');
$config['form_menu_edit']['field']['label'] 		= array('type' => 'input');
$config['form_menu_edit']['field']['path'] 			= array('type' => 'input', 'deactive' => TRUE, 'toggle' => TRUE);
$config['form_menu_edit']['field']['title']			= array('type' => 'input');
$config['form_menu_edit']['button']['submit'] 		= array('label' => 'save');
/*
| Group
*/
$config['form_menu_edit']['fieldset']['edit_item']['items'] 		= array('id','type','label','path','title');
$config['form_menu_edit']['fieldset']['edit_item']['attributes'] 	= array('class' => 'fieldset inline');
$config['form_menu_edit']['form_attributes'] 						= array('autocomplete' => 'off', 'id' => 'edit_item');
/*
|--------------------------------------------------------------------------
| CMS Login
|--------------------------------------------------------------------------
|
| Login
|
| Scheme: $config['form_pages'][ field type (e.g. input) ][ page data (e.g. content)] = array( options (e.g. maxlength = 150) )
*/
$config['form_login']['field']['user'] 			= array('type' => 'input');
$config['form_login']['field']['password'] 		= array('type' => 'password');
$config['form_login']['button']['submit'] 		= array('label' => 'login');
/*
| Group
*/
$config['form_login']['fieldset']['login']['items'] 			= array('user','password');
$config['form_login']['fieldset']['login']['attributes'] 		= array('class' => 'fieldset inline', 'id' => 'login_form');
$config['form_login']['form_attributes'] 						= array('id' => 'login', 'autocomplete' => 'off', 'class'=>'dialog-box small', 'title' => 'Bitte melden Sie sich an.');
/*
|--------------------------------------------------------------------------
| CMS Entries
|--------------------------------------------------------------------------
|
| Entries
|
| Scheme: $config['form_pages'][ field type (e.g. input) ][ page data (e.g. content)] = array( options (e.g. maxlength = 150) )
*/
$config['form_entries']['field']['headline'] 		= array('type' => 'input', 'class' => 'fullsize');
$config['form_entries']['field']['content']			= array('type' => 'textarea', 'wysiwyg' => true);
$config['form_entries']['field']['excerpt'] 		= array('type' => 'textarea', 'maxlength' => '500', 'class' => 'fullsize');
$config['form_entries']['field']['description'] 	= array('type' => 'textarea', 'maxlength' => '150', 'class' => 'fullsize');
$config['form_entries']['field']['publish'] 		= array('type' => 'toggle');
$config['form_entries']['field']['status'] 			= array('type' => 'select');
$config['form_entries']['field']['language'] 		= array('type' => 'select');

$config['form_entries']['field']['type'] 			= array('type' => 'select');
$config['form_entries']['field']['menu'] 			= array('type' => 'var', 'name' => 'select_menu');
$config['form_entries']['field']['categories'] 		= array('type' => 'checkselect');
$config['form_entries']['field']['tags'] 			= array('type' => 'input', 'autosuggest' => true);
$config['form_entries']['field']['keywords'] 		= array('type' => 'input'/*, 'autosuggest' => true*/);
$config['form_entries']['field']['trackbacks'] 		= array('type' => 'input');
$config['form_entries']['field']['save'] 			= array('type' => 'save');
/*
| Group
*/
$config['form_entries']['fieldset']['left']['items'] 		= array('headline','content','excerpt','description');
$config['form_entries']['fieldset']['left']['attributes'] 	= array('class' => 'fieldset left');
//
$config['form_entries']['fieldset']['right']['items'] 		= array('save','type','status','language','menu',/*'trackbacks','categories','tags','keywords'*/);
$config['form_entries']['fieldset']['right']['attributes'] 	= array('class' => 'fieldset right cms-box');
/*
| FORM TYPE 1 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
$config['form_entrie_type_1']['field']['headline'] 		= array('type' => 'input', 'class' => 'fullsize');
$config['form_entrie_type_1']['field']['content']		= array('type' => 'textarea', 'wysiwyg' => true);
$config['form_entrie_type_1']['field']['excerpt'] 		= array('type' => 'textarea', 'maxlength' => '500', 'class' => 'fullsize');
$config['form_entrie_type_1']['field']['description'] 	= array('type' => 'textarea', 'maxlength' => '150', 'class' => 'fullsize');
$config['form_entrie_type_1']['field']['language'] 		= array('type' => 'select');
$config['form_entrie_type_1']['field']['status'] 		= array('type' => 'select');

$config['form_entrie_type_1']['field']['type'] 			= array('type' => 'select');
$config['form_entrie_type_1']['field']['menu'] 			= array('type' => 'var', 'name' => 'select_menu');
$config['form_entrie_type_1']['field']['save'] 			= array('type' => 'save');
/*
| Group
*/
$config['form_entrie_type_1']['fieldset']['left']['items'] 		= array('headline','excerpt','content','description');
$config['form_entrie_type_1']['fieldset']['left']['attributes'] 	= array('class' => 'fieldset left');
//
$config['form_entrie_type_1']['fieldset']['right']['items'] 		= array('save','type','language','status','menu');
$config['form_entrie_type_1']['fieldset']['right']['attributes'] 	= array('class' => 'fieldset right cms-box');
/*
| FORM TYPE 2 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
$config['form_entrie_type_2']['field']['headline'] 		= array('type' => 'input', 'class' => 'fullsize');
$config['form_entrie_type_2']['field']['content']		= array('type' => 'textarea', 'wysiwyg' => true);
$config['form_entrie_type_2']['field']['excerpt'] 		= array('type' => 'textarea', 'maxlength' => '500', 'class' => 'fullsize');
$config['form_entrie_type_2']['field']['description'] 	= array('type' => 'textarea', 'maxlength' => '150', 'class' => 'fullsize');
$config['form_entrie_type_2']['field']['language'] 		= array('type' => 'select');
$config['form_entrie_type_2']['field']['status'] 		= array('type' => 'select');

$config['form_entrie_type_2']['field']['type'] 			= array('type' => 'select');
$config['form_entrie_type_2']['field']['menu'] 			= array('type' => 'var', 'name' => 'select_menu');
$config['form_entrie_type_2']['field']['save'] 			= array('type' => 'save');
/*
| Group
*/
$config['form_entrie_type_2']['fieldset']['left']['items'] 		= array('headline','excerpt','content','description');
$config['form_entrie_type_2']['fieldset']['left']['attributes'] 	= array('class' => 'fieldset left');
//
$config['form_entrie_type_2']['fieldset']['right']['items'] 		= array('save','type','language','status','menu');
$config['form_entrie_type_2']['fieldset']['right']['attributes'] 	= array('class' => 'fieldset right cms-box');
/*
| FORM TYPE 3 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
$config['form_entrie_type_3']['field']['headline'] 		= array('type' => 'input', 'class' => 'fullsize', 'label' => 'Produktname');
$config['form_entrie_type_3']['field']['content']		= array('type' => 'textarea', 'wysiwyg' => true, 'label' => 'Beschreibungstext');
$config['form_entrie_type_3']['field']['details']		= array('type' => 'textarea', 'wysiwyg' => true, 'label' => 'Technische Details');
$config['form_entrie_type_3']['field']['head_image']	= array('type' => 'upload', 'label' => 'Titelbild');
$config['form_entrie_type_3']['field']['description'] 	= array('type' => 'textarea', 'maxlength' => '150');
$config['form_entrie_type_3']['field']['language'] 		= array('type' => 'select');
$config['form_entrie_type_3']['field']['status'] 		= array('type' => 'select');

$config['form_entrie_type_3']['field']['type'] 			= array('type' => 'select');
$config['form_entrie_type_3']['field']['save'] 			= array('type' => 'save');
$config['form_entrie_type_3']['field']['cancel'] 		= array('type' => 'cancel', 'url' => '/entries/overview');
/*
| Group
*/
$config['form_entrie_type_3']['fieldset']['left']['items'] 		= array('headline','content','details');
$config['form_entrie_type_3']['fieldset']['left']['attributes'] 	= array('class' => 'fieldset left');
//
$config['form_entrie_type_3']['fieldset']['right']['items'] 		= array('save','cancel','status','type','language','head_image','description');
$config['form_entrie_type_3']['fieldset']['right']['attributes'] 	= array('class' => 'fieldset right cms-box');
/*
| FORM TYPE 4 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
$config['form_entrie_type_4']['field']['headline'] 		= array('type' => 'input', 'class' => 'fullsize');
$config['form_entrie_type_4']['field']['content']		= array('type' => 'textarea', 'wysiwyg' => true);
$config['form_entrie_type_4']['field']['description'] 	= array('type' => 'textarea', 'maxlength' => '150', 'class' => 'fullsize');
$config['form_entrie_type_4']['field']['language'] 		= array('type' => 'select');
$config['form_entrie_type_4']['field']['status'] 		= array('type' => 'select');

$config['form_entrie_type_4']['field']['type'] 			= array('type' => 'select');
$config['form_entrie_type_4']['field']['save'] 			= array('type' => 'save');
/*
| Group
*/
$config['form_entrie_type_4']['fieldset']['left']['items'] 		= array('headline','content');
$config['form_entrie_type_4']['fieldset']['left']['attributes'] 	= array('class' => 'fieldset left');
//
$config['form_entrie_type_4']['fieldset']['right']['items'] 		= array('save','status','type','language');
$config['form_entrie_type_4']['fieldset']['right']['attributes'] 	= array('class' => 'fieldset right cms-box');
//
/*
|--------------------------------------------------------------------------
| CMS Settings
|--------------------------------------------------------------------------
|
| Settings
|
*/
$config['form_settings_general']['field']['page_name'] 		= array('type' => 'input', 'label' => 'Titel der Webseite');
$config['form_settings_general']['field']['slogan_de'] 		= array('type' => 'input', 'label' => 'deutscher Slogan');
$config['form_settings_general']['field']['slogan_en'] 		= array('type' => 'input', 'label' => 'englischer Slogan');
// $config['form_settings_general']['field']['disclaimer'] 	= array('type' => 'textarea', 'label' => 'Haftungsausschluss', 'inline' => false);
$config['form_settings_general']['field']['company'] 		= array('type' => 'input', 'label' =>'Firmenname');
$config['form_settings_general']['field']['email'] 			= array('type' => 'input', 'label' => 'Email');
$config['form_settings_general']['field']['phone'] 			= array('type' => 'input', 'label' => 'Telefonnummer');
$config['form_settings_general']['field']['fax'] 			= array('type' => 'input', 'label' => 'Faxnummer');
$config['form_settings_general']['field']['street'] 		= array('type' => 'input', 'label' => 'Straße und Nr.');
$config['form_settings_general']['field']['city'] 			= array('type' => 'input', 'label' => 'Plz und Stadt');
$config['form_settings_general']['field']['zusatz'] 		= array('type' => 'textarea', 'label' => 'Zusatz');
$config['form_settings_general']['field']['zusatz_en'] 		= array('type' => 'textarea', 'label' => 'Zusatz (en)');

$config['form_settings_general']['field']['keywords'] 		= array('type' => 'textarea', 'label' => '5-8 Wortgruppen mit Komma getrennt', 'class' => 'max-150');
$config['form_settings_general']['field']['description'] 	= array('type' => 'textarea', 'label' => 'Kurzbeschreibung für Google (150 Zeichen)', 'class' => 'max-150');

$config['form_settings_general']['button']['submit'] 		= array('label' => 'save');
/*
| Group
*/

$config['form_settings_general']['fieldset']['page_info']['items'] 			= array('page_name','slogan_de','slogan_en');
$config['form_settings_general']['fieldset']['page_info']['attributes'] 	= array('class' => 'fieldset inline cms-box', 'id' => 'page_settings', 'title' => 'Einstellungen der Webseite');
$config['form_settings_general']['fieldset']['company_info']['items'] 		= array('company','email','phone','fax','street','city','zusatz', 'zusatz_en');
$config['form_settings_general']['fieldset']['company_info']['attributes'] 	= array('class' => 'fieldset inline cms-box', 'id' => 'company_settings', 'title' => 'Kontaktdaten');

$config['form_settings_general']['fieldset']['seo_info']['items'] 		= array('keywords','description');
$config['form_settings_general']['fieldset']['seo_info']['attributes'] 	= array('class' => 'fieldset inline cms-box', 'id' => 'seo_settings', 'title' => 'SEO Einstellungen');

$config['form_settings_general']['form_attributes'] 						= array('id' => 'settings');
/* End of file cms_forms.php */
/* Location: ./system/application/config/cms_forms.php */