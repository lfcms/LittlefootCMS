<?php

use PHPUnit\Framework\TestCase;

class MemTest extends TestCase
{
	public function testGetNull()
	{
		$this->assertNull( (new \lf\mem)->get('____UNSET_KEY') );
	}
	
    public function testSet()
    {
		// Arrange
		$key = 'some_key';
		$value = 'some_value';
		
        // Act
		(new \lf\mem)->set($key, $value);

        // Assert
        $this->assertEquals($value, (new \lf\mem)->get($key) );
    }
}
 