<?php

App::uses('SheetSchemaAppModel', 'SheetSchema.Model');

class SheetSchemaTest extends CakeTestCase {

	public static $testObjects = array();

	public static $backups = array();

	public static function setUpBeforeClass() {
		$testFilesPath = CakePlugin::path('SheetSchema') . 'Test' . DS . 'files' . DS;
		self::$testObjects = array(
			'cols' => new SimpleXMLElement(file_get_contents($testFilesPath . 'list_cols.xml')),
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

	public function testExtractCols() {
		$result = $this->SheetSchema->extractCols(self::$testObjects['cols']);
		$expected = array(
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
				'group_id' => array(
					'type' => 'integer',
					'index' => 'index',
					'null' => 'no',
				),
				'description' => array(
					'type' => 'text',
				),
			),
			'initialRecords' => array(
				[
					0 => '1',
					1 => 'John',
					2 => '3',
					3 => '2',
				],
				[
					0 => '2',
					1 => 'Mike',
					3 => '5',
				],
			),
		);
		$this->assertSame($expected, $result);
	}

	public function testTranslate() {
		$result = $this->SheetSchema->translate(array(
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
			'group_id' => array(
				'type' => 'integer',
				'index' => 'index',
				'null' => 'no',
			),
			'description' => array(
				'type' => 'text',
			),
		));

		$expected = array(
			'id' => array(
				'type' => 'integer',
				'null' => false,
				'key' => 'primary',
			),
			'name' => array(
				'type' => 'string',
				'length' => 30,
				'null' => false,
				'comment' => 'customer name',
			),
			'number' => array(
				'type' => 'integer',
				'null' => true,
				'default' => null,
			),
			'group_id' => array(
				'type' => 'integer',
				'null' => false,
				'key' => 'index',
			),
			'description' => array(
				'type' => 'text',
				'null' => true,
				'default' => null,
			),
			'indexes' => array(
				'PRIMARY' => array('column' => 'id', 'unique' => true),
				'group_id' => array('column' => 'group_id', 'unique' => false),
			)
		);
		$this->assertEquals($expected, $result);
	}

}