<?php

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility');

class SheetSchemaSetting extends AppModel {

	public $useTable = false;
	public $useDbConfig = false;

	public $validate = array(
		'client_id' => array(
			'maxLength' => array(
				'required' => false,
				'rule' => array('maxLength', 255),
				'message' => 'The Client ID seems too long.',
			),
		),
		'client_secret' => array(
			'maxLength' => array(
				'required' => false,
				'rule' => array('maxLength', 255),
				'message' => 'The Client secret seems too long.',
			),
		),
		'redirect_uri' => array(
			'url' => array(
				'required' => false,
				'rule' => array('url'),
				'message' => 'The redirect uri is not valid url.',
			),
		),
	);

	protected $_key = null;

	public function save($data = null, $validate = true, $fieldList = array()) {
		if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$this->set($data);
		if ($this->validates()) {
			$this->store($data);
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
		return json_decode(Security::rijndael(base64_decode($settings), $this->_key(), 'decrypt'), true);
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