<?php

App::uses('SheetSchemaAppController', 'SheetSchema.Controller');

class SheetSchemaSettingsController extends SheetSchemaAppController {

	public function index() {
		if ($this->request->isPost()) {
			if ($this->SheetSchemaSetting->save($this->request->data)) {
				$this->Session->setFlash(__d('sheet_schema', 'Settings was successfully saved.'));
				$this->redirect(array('controller' => 'SheetSchema', 'action' => 'index'));
			} else {
				$this->Session->setFlash(__d('sheet_schema', 'Settings could not be saved.'));
			}
		} else {
			$this->request->data = array('SheetSchemaSetting' => $this->sheetSchemaSettings);
		}
	}

}