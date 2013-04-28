<?php

App::uses('SheetSchemaAppController', 'SheetSchema.Controller');

class SheetSchemaController extends SheetSchemaAppController {

	public $uses = array(
		'SheetSchema.SheetRequest',
		'SheetSchema.SheetSchemaSetting',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		if (empty($this->schemaSettings)) {
			$this->render('welcome');
			$this->response->send();
			$this->_stop();
		}
	}

	public function beforeRender() {
		$this->set('settings', $this->schemaSettings);
	}

	public function index() {
	}

}