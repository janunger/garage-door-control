<?php

namespace GDC\Tests;

use PHPUnit_Framework_TestCase;

class AbstractTestCase extends PHPUnit_Framework_TestCase
{

    /**
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMock($className)
    {
        return $this
          ->getMockBuilder($className)
          ->disableOriginalConstructor()
          ->getMock();
    }
}
