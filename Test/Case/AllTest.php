<?php

require_once dirname(__FILE__) . DS . 'SheetSchemaGroupTestCase.php';

class AllSheetSchemaTest extends SheetSchemaGroupTestCase {

/**
 *
 *
 * @return PHPUnit_Framework_TestSuite the instance of PHPUnit_Framework_TestSuite
 */
	public static function suite() {
		$suite = new self;
		$files = $suite->getTestFiles();
		$suite->addTestFiles($files);

		return $suite;
	}
}