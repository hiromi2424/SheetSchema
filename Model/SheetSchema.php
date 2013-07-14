<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');
App::uses('CakeSchema', 'Model');

class SheetSchema extends SheetSchemaAppModel {

	public function extractRows($rows) {
		$table = (string)$rows->title;

		$fields = array();
		$initialRecords = array();
		$initialRecordMode = false;
		$fieldNameMap = $this->_fieldNameMap();
		$rowIndex = 0;
		foreach ($rows->entry as $row) {
			$title = (string)$row->title;
			if ($title === SheetSchemaAppModel::$settings['name_initial_records']) {
				$initialRecordMode = true;
			}
			foreach ($row->children('gsx', true) as $key => $value) {
				$value = (string)$value;
				if ($initialRecordMode) {
					$initialRecords[$rowIndex][$key] = $value;
				} elseif ($value && $title !== $value && isset($fieldNameMap[$title])) {
					$fields[$key][$fieldNameMap[$title]] = $value;
				}
			}
			$rowIndex++;
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

	public function extractWorksheetRows($worksheetRows) {
		$data = array();
		foreach ($worksheetRows as $rows) {
			if (SheetSchemaAppModel::$settings['ignored_worksheet'] !== (string)$rows->title) {
				$data = Hash::merge($data, $this->extractRows($rows));
			}
		}
		return $data;
	}

	public function generateSchema($data) {
		
	}

	public function translate($fields) {
		$result = array();
		foreach ($fields as $column => $types) {
			foreach ($types as $type => $value) {
				
			}
		}

		return $result;
	}

	public function insertInitialRecords($data) {
	}

}