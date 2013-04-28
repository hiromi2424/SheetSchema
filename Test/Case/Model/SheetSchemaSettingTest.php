<?php

class SheetSchemaSettingTest extends CakeTestCase {

	protected static $_testSettingsFile;

	public static function setUpBeforeClass() {
		self::$_testSettingsFile = TMP . 'test_sheet_schema_settings.php';
	}

	public function setUp() {
		parent::setUp();
		$this->SheetSchemaSetting = $this->getMockForModel('SheetSchema.SheetSchemaSetting', array(
			'_file',
		));

		$this->SheetSchemaSetting->expects($this->any())
			->method('_file')
			->will($this->returnValue(self::$_testSettingsFile))
		;
	}

	public function tearDown() {
		unset($this->SheetSchemaSetting);
		if (file_exists(self::$_testSettingsFile)) {
			unlink(self::$_testSettingsFile);
		}
		parent::tearDown();
	}

	public function testStore() {
		$result = $this->SheetSchemaSetting->store(array('test' => 'hogehoge'));
		$this->assertTrue($result);
		$this->assertTrue(file_exists(self::$_testSettingsFile));

		include(self::$_testSettingsFile);
		$this->assertTrue(isset($settings));
	}

	/**
	 * @depends testStore
	 */
	public function testLoad() {
		$result = $this->SheetSchemaSetting->load();
		$this->assertFalse($result);

		$data = array('test' => 'hogehoge');
		$this->SheetSchemaSetting->store($data);
		$result = $this->SheetSchemaSetting->load();
		$this->assertSame($data, $result);
	}

}