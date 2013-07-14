<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');
App::uses('ConnectionManager', 'Model');
App::uses('Security', 'Utility');
App::uses('Validation', 'Utility');

class SheetSchemaSetting extends SheetSchemaAppModel {

	public $useTable = false;
	public $useDbConfig = false;

	public $validationDomain = 'cake_schema';

	public $validate = array(
		'redirect_uri' => array(
			'url' => array(
				'required' => true,
				'allowEmpty' => false,
				'rule' => array('url', true),
				'message' => 'The redirect uri is not valid url.',
			),
		),
		'database' => array(
			'databases' => array(
				'required' => true,
				'allowEmpty' => false,
				'rule' => array('databases'),
			),
		),
	);

	protected $_key = null;

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->_constructValidation();
	}

	protected function _constructValidation() {
		$stringValidation = array(
			'required' => array(
				'required' => true,
				'allowEmpty' => false,
				'rule' => array('notEmpty'),
				'message' => __d('cake_schema', 'Required'),
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
			),
		);
		$messageTemplate = __d('cake_schema', 'The %s is too long.');
		$stringFields = array(
			'client_id' => __d('cake_schema', 'Client ID'),
			'client_secret' => __d('cake_schema', 'Client Secret'),
			'ignored_worksheet' => __d('cake_schema', 'Ignored Worksheet'),
		);
		$nameFields = array(
			'type' => __d('cake_schema', 'Type'),
			'length' => __d('cake_schema', 'Length'),
			'index' => __d('cake_schema', 'Index'),
			'null' => __d('cake_schema', 'Null'),
			'default' => __d('cake_schema', 'Default'),
			'comment' => __d('cake_schema', 'Comment'),
			'initial_records' => __d('cake_schema', 'InitialRecords'),
		);

		$Validator = $this->validator();
		foreach (array_merge($stringFields, $nameFields) as $field => $title) {
			if (in_array($field, array_keys($nameFields))) {
				$field = 'name_' . $field;
			}
			$Validator->add($field, $stringValidation);
			$Validator->getField($field)->getRule('maxLength')->message = sprintf($messageTemplate, $title);
		}

		$notRequired = array(
			'ignored_worksheet',
		);
		foreach ($notRequired as $field) {
			$Validator->remove($field, 'required');
			$maxLength = $Validator->getField($field)->getRule('maxLength');
			$maxLength->required = false;
			$maxLength->allowEmpty = true;
		}
	}

	public function url($check) {
		$value = current((array)$check);
		$value = str_replace('//localhost', '//127.0.0.1', $value);
		return Validation::url($value, true);
	}

	public function databases($check) {
		$value = current((array)$check);
		try {
			$db = ConnectionManager::getDataSource($value);
			return $db->isConnected() || $db->connect();
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	public function save($data = null, $validate = true, $fieldList = array()) {
		if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$this->set($data);
		if ($this->validates() && $this->store($data)) {
			return $data;
		}

		return false;
	}

	public function store($data) {
		$value = base64_encode(Security::rijndael(json_encode($data), $this->_key(), 'encrypt'));
		$template = '<?php' . PHP_EOL . '$settings = %s;';
		return false !== file_put_contents($this->_file(), sprintf($template, var_export($value, true)));
	}

	public function load() {
		$file = $this->_file();
		if (!file_exists($file)) {
			return false;
		}

		include $file;
		SheetSchemaAppModel::$settings = json_decode(Security::rijndael(base64_decode($settings), $this->_key(), 'decrypt'), true);
		return SheetSchemaAppModel::$settings;
	}

	protected function _file() {
		return TMP . 'sheet_settings.php';
	}

	protected function _key() {
		if ($this->_key === null) {
			$this->_key = Configure::read('Security.salt');
		}
		return $this->_key;
	}

}