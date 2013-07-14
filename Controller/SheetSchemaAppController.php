<?php

App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');
App::uses('ConnectionManager', 'Model');

class SheetSchemaAppController extends AppController {

	public $components = array('Session');

	public $sheetSchemaSettings;

	public $uses = array(
		'SheetSchema.SheetSchemaSetting',
	);

	public function beforeFilter() {
		if (!Configure::read('debug')) {
			trigger_error(__d('sheet_schema', 'Do not use SheetSchema on the web in production. Use console command instead.'));
			throw new ForbiddenException();
		}
		$databases = array_keys(ConnectionManager::enumConnectionObjects());
		$this->set('databases', array_combine($databases, $databases));

		$this->sheetSchemaSettings = Hash::filter((array)$this->SheetSchemaSetting->load());
		$this->set('settings', $this->sheetSchemaSettings);
		parent::beforeFilter();
	}

}