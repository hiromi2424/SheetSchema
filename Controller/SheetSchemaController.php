<?php

App::uses('SheetSchemaAppController', 'SheetSchema.Controller');

class SheetSchemaController extends SheetSchemaAppController {

	public $uses = array(
		'SheetSchema.SheetSchema',
		'SheetSchema.SheetRequest',
	);

	public $noLoginActions = array(
		'login',
		'oauth2callback',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		if (empty($this->sheetSchemaSettings)) {
			$this->_render('_welcome');
			$this->_stop();
		}

		$this->SheetRequest->configure($this->sheetSchemaSettings);

		if (!$this->_readAuth()) {
			if ($this->_needAuthenticate()) {
				$this->Session->write('SheetSchema.referer', Hash::merge(array(
					'controller' => $this->request->controller,
					'action' => $this->request->action,
				), $this->passedArgs));
				$this->_render('_login');
				$this->_stop();
			}
		} else {
			if (!$this->_needAuthenticate()) {
				$this->Session->setFlash(__d('sheet_schema', 'You are already authenticated with OAuth.'));
				$this->redirect(array('action' => 'index'));
			}

			$this->SheetRequest->setAccessToken($this->_readAuth('token'));
		}
	}

	protected function _readAuth($path = null) {
		if ($this->Session->check('SheetSchema.auth')) {
			$auth = $this->Session->read('SheetSchema.auth');
			if ($auth['expired'] <= time()) {
				$this->Session->delete('SheetSchema.auth');
				$this->Session->setFlash(__d('sheet_schema', 'OAuth Authentication was expired.'));
			} else {
				return $path === null ? $auth : Hash::get($auth, $path);
			}
		}
		return false;
	}

	protected function _needAuthenticate() {
		return !in_array(strtolower($this->request->action), $this->noLoginActions);
	}

	protected function _render($template) {
		$this->render($template);
		$this->response->send();
	}

	public function index() {
		$this->set('spreadsheets', $this->SheetRequest->listSpreadsheets());
	}

	public function view($key, $showSql = false) {
		$worksheets = $this->SheetRequest->listWorksheets($key);

		if ($showSql) {
			$prepared = $this->_prepare($worksheets);
			extract($this->_processSql($prepared));
		}
		$this->set(compact('key', 'worksheets', 'sql', 'errors', 'showSql'));
	}

	protected function _processSql($prepared) {
		extract($prepared);
		$sql = array();
		if (!$errors) {
			foreach (array_keys($Schema->tables) as $table) {
				$sql[] = $db->dropSchema($Schema, $table);
				$sql[] = $db->createSchema($Schema, $table);
			}
			$sql[] = $this->SheetSchema->insertInitialRecords($extracted['initialRecords']);
		}
		return compact('sql', 'errors', 'db', 'Schema');
	}

	protected function _prepare($worksheets) {
		$errors = array(__d('sheet_schema', 'No worksheet entries found.'));
		if (!empty($worksheets->entry)) {
			$worksheetCols = array();
			foreach ($worksheets->entry as $worksheet) {
				$worksheetCols[] = $this->SheetRequest->listCols($worksheet->key, $worksheet->worksheetId);
			}

			$database = $this->sheetSchemaSettings['database'];
			$this->SheetSchema->setDatasource($database);
			$extracted = $this->SheetSchema->extractWorksheetCols($worksheetCols);
			$errors = $this->SheetSchema->getErrors();
			if (!$errors) {
				$Schema = $this->SheetSchema->generateSchema($extracted['columns']);
				$db = ConnectionManager::getDataSource($database);
			}
		}
		return compact('extracted', 'errors', 'Schema', 'db');
	}

	public function process($key) {
		$worksheets = $this->SheetRequest->listWorksheets($key);
		$prepared = $this->_prepare($worksheets);
		extract($this->_processSql($prepared));
		if (!$errors) {
			foreach ($sql as $s) {
				if (!empty($s)) {
					$db->execute($s);
				}
			}
		}
		$this->set(compact('worksheets', 'errors', 'sql'));
	}

	public function login() {
		$authenticateUrl = $this->SheetRequest->authenticateUrl();
		$this->redirect($authenticateUrl);
	}

	public function logout() {
		$this->SheetRequest->revokeToken($this->_readAuth('token'));
		$this->Session->delete('SheetSchema.auth');
		$this->redirect(array('action' => 'index'));
	}

	public function oauth2callback() {
		try {
			if (empty($this->request->query['code'])) {
				throw new Exception(__d('sheet_schema', 'Invalid Request'));
			}
			$accessToken = $this->SheetRequest->authenticate($this->request->query['code']);
			$this->Session->write('SheetSchema.auth', array(
				'token' => $accessToken,
				'expired' => time() + 60 * 60 * 12,
			));

			$redirectTo = array('action' => 'index');
			if ($this->Session->check('SheetSchema.referer')) {
				$redirectTo = $this->Session->read('SheetSchema.referer');
				$this->Session->delete('SheetSchema.referer');
			}
			$this->redirect($redirectTo);

		} catch (Exception $e) {
			$this->Session->setFlash(__d('sheet_schema', 'An Error was occured during oauth callback: "%s"', $e->getMessage()));
			$this->redirect(array('action' => 'login'));
		}
	}

}