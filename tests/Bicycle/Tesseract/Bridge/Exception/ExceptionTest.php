<?php

namespace Bicycle\Tesseract\Bridge\Exception;

use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testException(): void
    {
        self::assertInstanceOf(\Exception::class, new Exception());
    }
}
