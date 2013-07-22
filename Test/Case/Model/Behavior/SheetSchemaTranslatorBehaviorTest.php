<?php
App::uses('SheetSchema', 'SheetSchema.Model');
App::uses('SheetSchemaTranslatorBehavior', 'SheetSchema.Model/Behavior');

/**
 * SheetSchemaTranslatorBehavior Test Case
 *
 */
class SheetSchemaTranslatorBehaviorTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SheetSchemaTranslator = new SheetSchemaTranslatorBehavior();
		$this->Model = $this->getMock('SheetSchema', array('raiseError'));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SheetSchemaTranslator);

		parent::tearDown();
	}

/**
 * testTranslateType method
 *
 * @return void
 */
	public function testTranslateType() {
		$result = $this->SheetSchemaTranslator->translateType($this->Model, 'table', 'id', 'integer', array());
		$this->assertArrayHasKey('id', $result);
		$this->assertArrayHasKey('type', $result['id']);
		$this->assertSame('integer', $result['id']['type']);

		$result = $this->SheetSchemaTranslator->translateType($this->Model, 'table', 'id', 'int', array());
		$this->assertSame('integer', $result['id']['type']);

		//$this->Model->expects($this->once())->method('raiseError');
		$result = $this->SheetSchemaTranslator->translateType($this->Model, 'table', 'id', 'invalid_type', array());
	}

/**
 * testTranslateLength method
 *
 * @return void
 */
	public function testTranslateLength() {
		$result = $this->SheetSchemaTranslator->translateLength($this->Model, 'table', 'id', '134', array());
		$this->assertArrayHasKey('id', $result);
		$this->assertArrayHasKey('length', $result['id']);
		$this->assertSame(134, $result['id']['length']);

		$this->Model->expects($this->once())->method('raiseError');
		$this->SheetSchemaTranslator->translateLength($this->Model, 'table', 'id', 'hoge', array());
	}

/**
 * testTranslateIndex method
 *
 * @return void
 */
	public function testTranslateIndex() {
		// primary
		$result = $this->SheetSchemaTranslator->translateIndex($this->Model, 'table', 'id', 'primary', array());
		$this->assertArrayHasKey('indexes', $result);
		$this->assertArrayHasKey('PRIMARY', $result['indexes']);
		$expected = array('column' => 'id', 'unique' => true);
		$this->assertSame($expected, $result['indexes']['PRIMARY']);
		$this->assertArrayHasKey('id', $result);
		$this->assertArrayHasKey('key', $result['id']);
		$this->assertSame('primary', $result['id']['key']);

		// index
		$result = $this->SheetSchemaTranslator->translateIndex($this->Model, 'table', 'group_id', 'index', array());
		$this->assertArrayHasKey('indexes', $result);
		$this->assertArrayHasKey('group_id', $result['indexes']);
		$expected = array('column' => 'group_id', 'unique' => false);
		$this->assertSame($expected, $result['indexes']['group_id']);
		$this->assertArrayHasKey('group_id', $result);
		$this->assertArrayHasKey('key', $result['group_id']);
		$this->assertSame('index', $result['group_id']['key']);

		// unique
		$result = $this->SheetSchemaTranslator->translateIndex($this->Model, 'table', 'profile_id', 'unique', array());
		$this->assertArrayHasKey('indexes', $result);
		$this->assertArrayHasKey('profile_id', $result['indexes']);
		$expected = array('column' => 'profile_id', 'unique' => true);
		$this->assertSame($expected, $result['indexes']['profile_id']);
		$this->assertArrayHasKey('profile_id', $result);
		$this->assertArrayHasKey('key', $result['profile_id']);
		$this->assertSame('unique', $result['profile_id']['key']);

		// invalid
		$this->Model->expects($this->once())->method('raiseError');
		$this->SheetSchemaTranslator->translateIndex($this->Model, 'table', 'invalid', 'invalid_index', array());
	}

/**
 * testTranslateNull method
 *
 * @return void
 */
	public function testTranslateNull() {
		$trueValues = array(
			'ok',
			'OK',
			'yes',
			'YES',
			'y',
			'Y',
			'1',
			'true',
			'TRUE'
		);
		foreach ($trueValues as $trueValue) {
			$result = $this->SheetSchemaTranslator->translateNull($this->Model, 'table', 'field', $trueValue, array());
			$this->assertArrayHasKey('field', $result);
			$this->assertArrayHasKey('null', $result['field']);
			$this->assertTrue($result['field']['null'], "$trueValue was not interpreted as true");
		}

		$falseValues = array(
			'ng',
			'NG',
			'no',
			'NO',
			'n',
			'N',
			'0',
			'false',
			'FALSE'
		);
		foreach ($falseValues as $falseValue) {
			$result = $this->SheetSchemaTranslator->translateNull($this->Model, 'table', 'field', $falseValue, array());
			$this->assertArrayHasKey('field', $result);
			$this->assertArrayHasKey('null', $result['field']);
			$this->assertFalse($result['field']['null'], "$trueValue was not interpreted as false");
		}

		$this->Model->expects($this->once())->method('raiseError');
		$this->SheetSchemaTranslator->translateNull($this->Model, 'table', 'invalid', 'yeah', array());
	}

/**
 * testTranslateDefault method
 *
 * @return void
 */
	public function testTranslateDefault() {
	}

/**
 * testTranslateComment method
 *
 * @return void
 */
	public function testTranslateComment() {
	}

/**
 * testValue method
 *
 * @return void
 */
	public function testValue() {
	}

}
