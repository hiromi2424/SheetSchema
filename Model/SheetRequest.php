<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');
App::uses('Xml', 'Utility');
require_once CakePlugin::path('SheetSchema') . 'Vendor' . DS . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';
App::uses('Google_SpreadsheetService', 'SheetSchema.Lib');

class SheetRequest extends SheetSchemaAppModel {

	public $useTable = false;
	public $useDbConfig = false;

	protected $_settings = array();
	protected $_client = null;
	protected $_service = null;

	const SCOPE = 'https://spreadsheets.google.com/feeds';

	public function configure($settings = array()) {
		$this->_settings = $settings;
	}

	public function authenticateUrl() {
		return $this->getClient()->createAuthUrl();
	}

	public function authenticate($code) {
		return $this->getClient()->authenticate($code);
	}

	public function listSpreadsheets() {
		$result = $this->getService()->spreadsheets->listSpreadsheets();

		if (!empty($result->entry)) {
			foreach ($result->entry as $sheet) {
				$sheet->key = $this->_extractId($sheet);
			}
		}
		return $result;
	}

	public function listWorksheets($key) {
		$result = $this->getService()->worksheets->listWorksheets($key);

		if (!empty($result)) {
			$result->key = $key;
			foreach ($result->entry as $worksheet) {
				$worksheet->key = $key;
				$worksheet->worksheetId = $this->_extractId($worksheet);
		$this->getService()->worksheets->colsFeed($key, $worksheet->worksheetId);
			}
		}
		return $result;
	}

	public function listRows($key, $worksheetId) {
		$result = $this->getService()->worksheets->listRows($key, $worksheetId);

		if (!empty($result)) {
			$result->key = $key;
			$result->worksheetId = $worksheetId;
			foreach ($result->entry as $row) {
				$row->rowId = $this->_extractId($row);
			}
		}
		return $result;
	}

	protected function _extractId($object) {
		$exploded = explode('/', $object->id);
		return end($exploded);
	}

	public function revokeToken($token = null) {
		return $this->getClient()->revokeToken($token);
	}

	public function setAccessToken($token = null) {
		return $this->getClient()->setAccessToken($token);
	}

	public function getClient() {
		if ($this->_client === null) {
			if (empty($this->_settings)) {
				trigger_error(__d('sheet_schema', 'The settings is not be set.'));
			}
			// global settings for google api php client
			global $apiConfig;

			$apiConfig = Set::merge($apiConfig, array(
				'oauth2_client_id' => $this->_settings['client_id'],
				'oauth2_client_secret' => $this->_settings['client_secret'],
				'oauth2_redirect_uri' => $this->_settings['redirect_uri'],
				'services' => array(
					'spreadsheet' => array(
						'scope' => self::SCOPE,
					),
				),
			));

			$this->_client = new Google_Client;
			$this->_service = new Google_SpreadsheetService($this->_client);
		}
		return $this->_client;
	}

	public function getService() {
		$this->getClient();
		return $this->_service;
	}

}