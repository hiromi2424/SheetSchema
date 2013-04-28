<?php

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
$clientPath = CakePlugin::path('SheetSchema') . 'Vendor' . DS . 'google-api-php-client' . DS . 'src' . DS;
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientPath);
require_once 'auth' . DS . 'Google_Auth.php';
require_once 'auth' . DS . 'Google_OAuth2.php';
set_include_path($oldPath);

class SheetRequest extends AppModel {

	public $useTable = false;
	public $useDbConfig = false;

	public function authenticateUrl() {
		var_dump(class_exists('Google_OAuth2'));
	}

}