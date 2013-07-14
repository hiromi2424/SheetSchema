<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');
App::uses('CakeSchema', 'Model');

/*
 * SheetSchema Model Class.
 *
 * The part of this class is to parse/generate response/request data, schema and internal meta data.
 */
class SheetSchema extends SheetSchemaAppModel {

	public $useTable = false;

	protected $_errors = array();

	public function extractCols($cols) {
		$table = (string)$cols->title;

		$fields = array();
		$initialRecords = array();
		$initialRecordRow = 0;
		$fieldNameMap = $this->_fieldNameMap();
		$fieldNames = array();
		$rowNames = array();
		foreach ($cols->entry as $col) {
			foreach ($col->children('gs', true) as $key => $value) {
				if ($key === 'cell') {
					$attributes = $value->attributes();
					// number of row/col indexes begin with 1(-1)
					// and first row/col contain only meta data(-1)
					$rowIndex = intval($attributes->row) - 2;
					$colIndex = intval($attributes->col) - 2;

					$value = (string)$value;

					if ($colIndex === -1) {
						// skip first cell
						if ($rowIndex !== -1) {
							if ($value === SheetSchemaAppModel::$settings['name_initial_records']) {
								$initialRecordRow = $rowIndex;
							} elseif ($value) {
								if (isset($fieldNameMap[$value])) {
									$rowNames[$rowIndex] = $fieldNameMap[$value];
								}
							}
						}
					} elseif ($initialRecordRow) {
						$initialRecords[$rowIndex - $initialRecordRow][$colIndex] = $value;
					} elseif ($value) {
						if ($rowIndex === -1) {
							$fieldNames[$colIndex] = $value;
						} elseif (isset($fieldNames[$colIndex], $rowNames[$rowIndex])) {
							$fields[$fieldNames[$colIndex]][$rowNames[$rowIndex]] = $value;
						}
					}
				}
			}
		}

		return array($table => compact('fields', 'initialRecords'));
	}

	protected function _fieldNameMap() {
		$result = array();
		foreach (SheetSchemaAppModel::$settings as $key => $value) {
			if (preg_match('/^name_/', $key) && $key !== 'name_initial_records') {
				$result[$value] = str_replace('name_', '', $key);
			}
		}
		return $result;
	}

	public function extractWorksheetCols($worksheetCols) {
		$result = array(
			'columns' => array(),
			'initialRecords' => array(),
		);
		foreach ($worksheetCols as $cols) {
			if (SheetSchemaAppModel::$settings['ignored_worksheet'] !== (string)$cols->title) {
				$extracted = $this->extractCols($cols);
				list($table, $data) = each($extracted);
				$result['columns'][$table] = $this->translate($data['fields']);
				$data['initialRecords'][$table]
			}
		}

		return $result;
	}

	public function generateSchema($data) {
		
	}

	protected function _raiseError($error) {
		$this->_errors = $error;
	}

	public function getErrors() {
		return $this->_errors;
	}

	public function translate($fields) {
		$result = array();
		$this->_errors = array();
		foreach ($fields as $column => $types) {
			$result[$column] = array();
			foreach ($types as $type => $value) {
				$method = '_translate' . ucfirst(strtolower($type));
				if (!method_exists($this, $method)) {
					throw new LogicException(__d('cake_schema', '[%s] column is not supported.', $type));
				} else {
					$this->$method($column, $value, $result);
				}
			}
			if (!isset($result[$column]['null'])) {
				$result[$column]['null'] = true;
			}

			if (!isset($result[$column]['default']) && $result[$column]['null']) {
				$result[$column]['default'] = null;
			}
		}

		if (!empty($this->_errors)) {
			return false;
		}
		return $result;
	}

	protected function _translateType($column, $value, &$result) {
		$type = preg_replace('/int$/', 'integer', $value);
		$db = $this->getDataSource();
		if (!isset($db->columns[$type])) {
			return $this->_raiseError(__d('cake_schema', 'Field type [%s] is not supported.', $value));
		}

		$result[$column]['type'] = $type;
	}

	protected function _translateLength($column, $value, &$result) {
		$result[$column]['length'] = intval($value);
	}

	protected function _translateIndex($column, $value, &$result) {
		$type = strtolower($value);
		if (!in_array($type, $this->getDataSource()->index)) {
			return $this->_raiseError(__d('cake_schema', 'Index type [%s] is not supported.', $value));
		}

		if (!isset($result['indexes'])) {
			$result['indexes'] = array();
		}

		$key = $type === 'primary' ? 'PRIMARY' : $column;
		$unique = $type !== 'index';
		$result['indexes'][$key] = compact('column', 'unique');
		$result[$column]['key'] = $type;
	}

	protected function _translateNull($column, $value, &$result) {
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
			default:
				$this->_raiseError(__d('cake_schema', 'The string of null value "%s" could not be understood as boolean', $value));
		}
		$result[$column]['null'] = $value;
	}

	protected function _translateDefault($column, $value, &$result) {
		$result[$column]['default'] = $this->_value($value);
	}

	protected function _translateComment($column, $value, &$result) {
		$result[$column]['comment'] = $value;
	}

	protected function _value($value) {
		switch ($value) {
			case 'null':
			case 'true':
			case 'false':
				$value = eval($value);
		}

		return $value;
	}

	public function insertInitialRecords($data) {
	}

}