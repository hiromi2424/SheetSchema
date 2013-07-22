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

	public $actsAs = [
		'SheetSchema.SheetSchemaTranslator',
	];

	public function extractCols($cols) {
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

		return compact('fields', 'initialRecords');
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
		$result = array();
		foreach ($worksheetCols as $cols) {
			$table = (string)$cols->title;
			if (SheetSchemaAppModel::$settings['ignored_worksheet'] !== $table) {
				$extracted = $this->extractCols($cols);
				$result['fields'][$table] = array_keys($extracted['fields']);
				$result['columns'][$table] = $this->translate($table, $extracted['fields']);
				$result['initialRecords'][$table] = $extracted['initialRecords'];
			}
		}

		return $result;
	}

	public function generateSchema($data) {
		$Schema = new CakeSchema;
		$Schema->build($data);
		return $Schema;
	}

	public function raiseError($error) {
		$this->_errors[] = $error;
	}

	public function getErrors() {
		return $this->_errors;
	}

	public function translate($table, $fields) {
		$result = array();
		$this->_errors = array();
		foreach ($fields as $column => $types) {
			$result[$column] = array();
			foreach ($types as $type => $value) {
				$method = 'translate' . ucfirst(strtolower($type));
				if (!$this->hasMethod($method)) {
					throw new LogicException(__d('cake_schema', '[%s] column is not supported.', $type));
				} else {
					$result = $this->$method($table, $column, $value, $result);
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

/**
 * Generates INSERT statements
 *
 * @todo Future version will generate sql with core feature using CakePHP3.0
 * @param string $table The table being inserted into.
 * @param array $fields The array of field/column names being inserted.
 * @param array $records The array of values to insert. The values should
 *   be an array of rows. Each row must have the values in the same order
 *   as $fields.
 * @return array List of generated SQL
 */
	public function generateInsertStatements($table, $fields, $records) {
		$db = $this->getDataSource();
		$table = $db->fullTableName($table);
		$columns = implode(', ', array_map(array($db, 'name'), $fields));

		$pdoMap = array(
			'integer' => PDO::PARAM_INT,
			'float' => PDO::PARAM_STR,
			'boolean' => PDO::PARAM_BOOL,
			'string' => PDO::PARAM_STR,
			'text' => PDO::PARAM_STR
		);
		$columnMap = array();

		$sqlBase = "INSERT INTO {$table} ({$columns}) VALUES";

		$result = array();
		foreach ($records as $record) {
			$values = array();
			foreach ($fields as $key => $field) {
				$value = isset($record[$key]) ? $record[$key] : null;
				$value = $db->value($value);
				$values[] = $value;
			}
			$result[] = $sqlBase . '(' . implode(', ', $values) . ');';
		}
		return $result;
	}
}
