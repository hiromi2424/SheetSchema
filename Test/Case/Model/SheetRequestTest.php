<?php

class SheetRequestTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->SheetRequest = ClassRegistry::init('SheetSchema.SheetRequest');
	}

	public function tearDown() {
		unset($this->SheetRequest);
		parent::tearDown();
	}

	public function testAuthenticateUrl() {
		$result = $this->SheetRequest->authenticateUrl();
		$Setting = ClassRegistry::init('SheetSchema.SheetSchemaSetting');
		$Setting->store(['test' => 'hogeasohgfouadbgoasdbga;dgasdibug']);
	}

}