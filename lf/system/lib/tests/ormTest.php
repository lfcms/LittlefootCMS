<?php

use PHPUnit\Framework\TestCase;

class OrmTest extends TestCase
{
	/* # Behind the Magic! */
	
	/* ## Find */
	
	public function testMagicFilterByColumn()
	{
		$foo = (new \orm\foo_table);
		
		// utilize filterBy$column magic method
		$foo->filterByMytestvar('Mytestval');
		$firstCondition = $foo->conditions[0];
		
		// set up test data
		$fooTest = "foo_table.Mytestvar = 'Mytestval'";
		
		// assert result
		$this->assertEquals($firstCondition, $fooTest);
	}
	
	public function testMagicByColumn()
	{
		$foo = (new \orm\foo_table);
		
		// utilize filterBy$column magic method
		$foo->byMytestvar('Mytestval');
		$firstCondition = $foo->conditions[0];
		
		// set up test data
		$fooTest = "foo_table.Mytestvar = 'Mytestval'";
		
		// assert result
		$this->assertEquals($firstCondition, $fooTest);
	}
	
	public function testMagicGetByColumn()
	{
		$foo = (new \orm\foo_table);
		
		// utilize filterBy$column magic method
		$foo->getByMycolumn('Mytestval');
		$firstCondition = $foo->conditions[0];
		
		// set up test data
		$fooTest = "foo_table.Mycolumn = 'Mytestval'";
		
		// assert result
		$this->assertEquals($firstCondition, $fooTest);
	}
	
	public function testMagicGetAllByColumn()
	{
		$foo = (new \orm\foo_table);
		
		// utilize filterBy$column magic method
		$foo->getAllByMycolumn('Mytestval');
		$firstCondition = $foo->conditions[0];
		
		// set up test data
		$fooTest = "foo_table.Mycolumn = 'Mytestval'";
		
		// assert result
		$this->assertEquals($firstCondition, $fooTest);
	}
	
	public function testMagicSetField()
	{
		$foo = (new \orm\foo_table);
		
		// utilize filterBy$column magic method
		$foo->setMyfield('myValue');
		$dataArray = $foo->data;
		
		// set up test data
		$targetDataArray = [
			'Myfield' => "'myValue'"
		];
		
		// ob_start();
		// pre($foo);
		// $out = ob_get_clean();
		// stderr($out);
		
		// assert result
		$this->assertEquals($targetDataArray, $dataArray);
	}
	
	public function testMagicFindByColumn()
	{
		// run ORM call to settings table
		$settings = (new \orm\lf_settings)->findByVar('rewrite');
		
		// pull compiled SQL
		$sqlArray = $settings->_getSQL();
		
		// create target pattern
		$assertArray = [
			'crud' => "SELECT"
			, 'columns' => '*'
			, 'from' => "FROM lf_settings"
			, 'where' =>  "WHERE lf_settings.Var = 'rewrite'"
			, 'order' =>  ""
			, 'limit' =>  ""
		];
		
		// this should be the resulting SQL command
		$this->assertEquals($sqlArray, $assertArray);
	}
}
