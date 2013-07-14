<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');

class SheetSchemaTest extends CakeTestCase {

	public static $testObjects = array();

	public static $backups = array();

	public static function setUpBeforeClass() {
		$testFilesPath = CakePlugin::path('SheetSchema') . 'Test' . DS . 'files' . DS;
		self::$testObjects = array(
			'rows' => new SimpleXMLElement(file_get_contents($testFilesPath . 'list_rows.xml')),
		);
		self::$backups['settings'] = SheetSchemaAppModel::$settings;
		SheetSchemaAppModel::$settings = array(
			'ignored_worksheet' => 'cover',
			'name_type' => 'type',
			'name_length' => 'length',
			'name_index' => 'index',
			'name_null' => 'null',
			'name_default' => 'default',
			'name_comment' => 'comment',
			'name_initial_records' => 'initial_records',
		);
	}

	public static function tearDownAfterClass() {
		self::$testObjects = array();
		SheetSchemaAppModel::$settings = self::$backups['settings'];
	}

	public function setUp() {
		parent::setUp();
		$this->SheetSchema = ClassRegistry::init('SheetSchema.SheetSchema');
	}

	public function tearDown() {
		unset($this->SheetSchema);
		parent::tearDown();
	}

	public function testExtractRows() {
		$result = $this->SheetSchema->extractRows(self::$testObjects['rows']);
		$expected = array(
			'customers' => array(
				'fields' => array(
					'id' => array(
						'type' => 'int',
						'index' => 'primary',
						'null' => 'no',
					),
					'name' => array(
						'type' => 'string',
						'length' => '30',
						'null' => 'no',
						'comment' => 'customer name',
					),
					'number' => array(
						'type' => 'integer',
					),
					'group' => array(
						'type' => 'integer',
					),
				),
				'initialRecords' => array(),
			),
		);
		$this->assertSame($expected, $result);
	}

}