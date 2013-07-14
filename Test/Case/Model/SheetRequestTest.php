<?php

class SheetRequestTest extends CakeTestCase {

	public static $testResponses = array();

	public static function setUpBeforeClass() {
		$testFilesPath = CakePlugin::path('SheetSchema') . 'Test' . DS . 'files' . DS;
		self::$testResponses = array(
			'listSpreadsheets' => file_get_contents($testFilesPath . 'list_spreadsheets.xml'),
			'listWorksheets' => file_get_contents($testFilesPath . 'list_worksheets.xml'),
			'listCols' => file_get_contents($testFilesPath . 'list_cols.xml'),
		);
	}

	public static function tearDownAfterClass() {
		self::$testResponses = array();
	}

	public function setUp() {
		parent::setUp();
		$this->SheetRequest = $this->getMockForModel('SheetSchema.SheetRequest', array(
			'getClient',
			'getService',
		));
		$client = $this->getMockBuilder('Google_Client')
			->disableOriginalConstructor()
			->setMethods(array(
				'createAuthUrl',
				'authenticate',
			))
			->getMock()
		;
		$service = $this->getMockBuilder('Google_SpreadsheetService')
			->disableOriginalConstructor()
			->getMock()
		;
		$service->spreadsheets = $this->getMockBuilder('Google_SpreadsheetsServiceResource')
			->disableOriginalConstructor()
			->setMethods(array(
				'__call',
			))
			->getMock()
		;
		$service->worksheets = $this->getMockBuilder('Google_WorksheetsServiceResource')
			->disableOriginalConstructor()
			->setMethods(array(
				'__call',
			))
			->getMock()
		;
		$this->SheetRequest->expects($this->any())
			->method('getClient')
			->will($this->returnValue($client))
		;
		$this->SheetRequest->expects($this->any())
			->method('getService')
			->will($this->returnValue($service))
		;
	}

	public function tearDown() {
		unset($this->SheetRequest);
		parent::tearDown();
	}

	public function testAuthenticateUrl() {
		$this->SheetRequest->getClient()->expects($this->once())
			->method('createAuthUrl')
			->will($this->returnValue('https://example.com'))
		;
		$expected = 'https://example.com';
		$result = $this->SheetRequest->authenticateUrl();
		$this->assertSame($expected, $result);
	}

	public function testAuthenticate() {
		$this->SheetRequest->getClient()->expects($this->once())
			->method('authenticate')
			->will($this->returnValue(array('token' => 'test_token')))
			->with($this->equalTo('test_code'))
		;
		$expected = array('token' => 'test_token');
		$result = $this->SheetRequest->authenticate('test_code');
		$this->assertSame($expected, $result);
	}

	public function testListSpreadsheets() {
		$this->SheetRequest->getService()->spreadsheets->expects($this->once())
			->method('__call')
			->will($this->returnValue(self::$testResponses['listSpreadsheets']))
		;
		$result = $this->SheetRequest->listSpreadsheets();
		$this->assertInstanceOf('SimpleXMLElement', $result);
		$this->assertSame(2, $result->entry->count());
		$this->assertSame('spreadsheetkey1', (string)$result->entry[0]->key);
	}

	public function testListWorksheets() {
		$this->SheetRequest->getService()->worksheets->expects($this->once())
			->method('__call')
			->will($this->returnValue(self::$testResponses['listWorksheets']))
		;
		$result = $this->SheetRequest->listWorksheets('spreadsheetkey1');
		$this->assertInstanceOf('SimpleXMLElement', $result);
		$this->assertSame('spreadsheetkey1', (string)$result->key);
		$this->assertSame(2, $result->entry->count());
		$this->assertSame('worksheetid2', (string)$result->entry[1]->worksheetId);
	}

	public function testListCols() {
		$this->SheetRequest->getService()->worksheets->expects($this->once())
			->method('__call')
			->will($this->returnValue(self::$testResponses['listCols']))
		;
		$result = $this->SheetRequest->listCols('spreadsheetkey1', 'worksheetid1');
		$this->assertInstanceOf('SimpleXMLElement', $result);
		$this->assertSame('spreadsheetkey1', (string)$result->key);
		$this->assertSame('worksheetid1', (string)$result->worksheetId);
		$this->assertSame(32, $result->entry->count());
		$this->assertSame('R1C2', (string)$result->entry[1]->colId);
	}

}