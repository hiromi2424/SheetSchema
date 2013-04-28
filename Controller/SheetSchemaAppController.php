<?php

App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');

class SheetSchemaAppController extends AppController {

	public $schemaSettings;

	public function beforeFilter() {
		if (!Configure::read('debug')) {
			trigger_error(__d('sheet_schema', 'Do not use SheetSchema on the web in production. Use console command instead.'));
			throw new ForbiddenException();
		}

		$this->schemaSettings = Hash::filter((array)$this->SheetSchemaSetting->load());
		parent::beforeFilter();
	}

}