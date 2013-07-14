<?php

class Google_SpreadsheetService_ServiceResource extends Google_ServiceResource {

	// API actually returns xml instead of json.
	public function __call($name, $arguments) {
		$backup = Google_Client::$useBatch;
		Google_Client::$useBatch = true;
		$request = parent::__call($name, $arguments);
		Google_Client::$useBatch = $backup;

		$response = Google_Client::$io->makeRequest($request);

		$code = $response->getResponseHttpCode();
		$body = $response->getResponseBody();

		if ($code == '200') {
			return $body;
		}

		return null;
	}

	protected function _parseXml($xml) {
		if ($xml === null) {
			return null;
		}
		CakeLog::debug($xml);

		$internalErrors = libxml_use_internal_errors(true);
		$result = new SimpleXMLElement($xml, LIBXML_NOCDATA);
		libxml_use_internal_errors($internalErrors);
		return $result;
	}

}

class Google_SpreadsheetsServiceResource extends Google_SpreadsheetService_ServiceResource {

	public function listSpreadsheets() {
		$params = array('visibility' => 'private', 'projection' => 'full');
		$data = $this->__call('list', array($params));
		return $this->_parseXml($data);
	}

}

class Google_WorksheetsServiceResource extends Google_SpreadsheetService_ServiceResource {

	public function listWorksheets($key, $optParams = array()) {
		$params = array_merge(array('visibility' => 'private', 'projection' => 'full'), $optParams, compact('key'));
		$data = $this->__call('list', array($params));
		return $this->_parseXml($data);
	}

	public function listRows($key, $worksheetId, $optParams = array()) {
		$params = array_merge(array('visibility' => 'private', 'projection' => 'full'), $optParams, compact('key', 'worksheetId'));
		$data = $this->__call('listRows', array($params));
		return $this->_parseXml($data);
	}

	public function colsFeed($key, $worksheetId, $optParams = array()) {
		$params = array_merge(array('visibility' => 'private', 'projection' => 'full'), $optParams, compact('key', 'worksheetId'));
		$data = $this->__call('cols', array($params));
		return $this->_parseXml($data);
	}

}

/**
 * Service definition for Google Spreadsheets API version 3.0.
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/google-apps/spreadsheets/" target="_blank">API Documentation</a>
 * </p>
 *
 */
class Google_SpreadsheetService extends Google_Service {
	/**
	* Constructs the internal representation of the Spreadsheet service.
	*
	* @param Google_Client $client
	*/
	public function __construct(Google_Client $client) {
		$this->servicePath = 'https://spreadsheets.google.com/feeds/';
		$this->version = '3.0';
		$this->serviceName = 'spreadsheet';

		$client->addService($this->serviceName, $this->version);

		$this->_setupServiceResource();
	}

	protected function _setupServiceResource() {
		$pathString = array(
			'location' => 'path',
			'required' => true,
			'type' => 'string',
		);
		$projection = $visibility = $key = $worksheetId = $pathString;

		$spreadsheetMethodBase = array(
			'parameters' => compact('visibility', 'projection'),
			'id' => 'spreadsheets.spreadsheets.list',
			'httpMethod' => 'GET',
			'path' => 'spreadsheets/{visibility}/{projection}',
		);
		$spreadsheetsMethods = array(
			'list' => $spreadsheetMethodBase,
		);
		$this->spreadsheets = new Google_SpreadsheetsServiceResource($this, $this->serviceName, 'spreadsheets', array('methods' => $spreadsheetsMethods));

		$worksheetMethodBase = array(
			'parameters' => compact('visibility', 'projection', 'key'),
			'id' => 'spreadsheets.worksheets.list',
			'httpMethod' => 'GET',
			'path' => 'worksheets/{key}/{visibility}/{projection}',
		);
		$worksheetsMethods = array(
			'list' => $worksheetMethodBase,
			'listRows' => self::_mergeRecursive($worksheetMethodBase, array(
				'parameters' => compact('worksheetId'),
				'path' => 'list/{key}/{worksheetId}/{visibility}/{projection}',
			)),
			'cols' => self::_mergeRecursive($worksheetMethodBase, array(
				'parameters' => compact('worksheetId'),
				'path' => 'cells/{key}/{worksheetId}/{visibility}/{projection}',
			)),
		);
		$this->worksheets = new Google_WorksheetsServiceResource($this, $this->serviceName, 'worksheets', array('methods' => $worksheetsMethods));
	}

	protected static function _mergeRecursive($a, $b) {
		foreach ($b as $key => $val) {
			if (isset($a[$key]) && is_array($a[$key]) && is_array($val)) {
				$a[$key] = self::_mergeRecursive($a[$key], $val);
			} else {
				$a[$key] = $val;
			}
		}

		return $a;
	}

}


