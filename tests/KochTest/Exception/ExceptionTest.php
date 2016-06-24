<?php

namespace KochTest\Exception;

use Koch\Exception\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Exception
     */
    protected $exception;

    public function setUp()
    {
        try {
            $message  = 'ExceptionMessage';
            $code     = 666;
            $previous = null;
            throw new Exception($message, $code, $previous);
        } catch (Exception $e) {
            $this->exception = $e;
        }
    }

    public function tearDown()
    {
        unset($this->exception);
        parent::tearDown();
    }

    public function testException()
    {
        $this->assertEquals('ExceptionMessage', $this->exception->getMessage());
        $this->assertEquals(666, $this->exception->getCode());
    }
}
