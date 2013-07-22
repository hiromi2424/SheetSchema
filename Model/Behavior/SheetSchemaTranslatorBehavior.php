<?php

App::uses('ModelBehavior', 'Model');

class SheetSchemaTranslatorBehavior extends ModelBehavior {

	protected function _raiseError($Model, $message) {
		if ($Model->hasMethod('raiseError')) {
			return $Model->raiseError($message);
		}
	}

	public function translateType(Model $Model, $table, $column, $value, $result) {
		$type = preg_replace('/int$/', 'integer', $value);
		$db = $Model->getDataSource();
		if (!isset($db->columns[$type])) {
			$this->_raiseError($Model, __d('cake_schema', 'Field type [%s] is not supported. Table: %s, Column: %s', $value, $table, $column));
		}

		$result[$column]['type'] = $type;
		return $result;
	}

	public function translateLength(Model $Model, $table, $column, $value, $result) {
		if (!is_numeric($value)) {
			$this->_raiseError($Model, __d('cake_schema', 'Length [%s] is not numeric. Table: %s, Column: %s', $value, $table, $column));
		}
		$result[$column]['length'] = intval($value);
		return $result;
	}

	public function translateIndex(Model $Model, $table, $column, $value, $result) {
		$type = strtolower($value);
		if (in_array($type, $Model->getDataSource()->index)) {
			if (!isset($result['indexes'])) {
				$result['indexes'] = array();
			}

			$key = $type === 'primary' ? 'PRIMARY' : $column;
			$unique = $type !== 'index';
			$result['indexes'][$key] = compact('column', 'unique');
			$result[$column]['key'] = $type;
		} else {
			$this->_raiseError($Model, __d('cake_schema', 'Index type [%s] is not supported. Table: %s, Column: %s', $value, $table, $column));
		}

		return $result;
	}

	public function translateNull(Model $Model, $table, $column, $value, $result) {
		$value = strtolower($value);
		switch ($value) {
			case 'ok':
			case 'yes':
			case 'y':
			case '1':
				$value = true;
				break;
			case 'ng':
			case 'no':
			case 'n':
			case '0':
				$value = false;
				break;
			case 'true':
			case 'false':
				$value = $value === 'true';
				break;
			default:
				$this->_raiseError($Model, __d('cake_schema', 'The string of null value "%s" could not be understood as boolean. Table: %s, Column: %s', $value, $table, $column));
		}

		$result[$column]['null'] = $value;
		return $result;
	}

	public function translateDefault(Model $Model, $table, $column, $value, $result) {
		$result[$column]['default'] = $Model->value($value);
		return $result;
	}

	public function translateComment(Model $Model, $table, $column, $value, $result) {
		$result[$column]['comment'] = $value;
		return $result;
	}

	public function value(Model $Model, $value) {
		switch (strtolower($value)) {
			case 'null':
			case 'true':
			case 'false':
				$value = eval($value);
		}

		return $value;
	}

}
