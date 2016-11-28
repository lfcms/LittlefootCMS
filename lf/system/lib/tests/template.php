<?php

use PHPUnit\Framework\TestCase;

class asdf extends TestCase
{
    // ...

    public function testCanBeNegated()
    {
        // Arrange
        $a = (new \lf\user);

        // Act
        $b = $a->setEmail('asdf@fdsa.com');

        // Assert
        $this->assertEquals('asdf@fdsa.com', $b->getEmail());
    }

    // ...
}
