<?php

namespace Bicycle\Tesseract\Bridge\CLI;

use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @return array[]
     */
    public function dataProvider(): array
    {
        $codes = [1, 2, 3];
        $outputArrays = [[''], ['', 1], ['asdadasd']];

        return [
            [new Result($codes[0], $outputArrays[0]), $codes[0], $outputArrays[0]],
            [new Result($codes[1], $outputArrays[1]), $codes[1], $outputArrays[1]],
            [new Result($codes[2], $outputArrays[2]), $codes[2], $outputArrays[2]],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Result $result
     * @param int    $expectedCode
     * @param array  $expectedOutput
     */
    public function testConstructor(Result $result, int $expectedCode, array $expectedOutput): void
    {
        self::assertEquals($expectedCode, $result->getReturnCode());
        self::assertEquals($expectedOutput, $result->getOutputArray());
        self::assertEquals(implode(PHP_EOL, $expectedOutput), $result->getOutputString());
    }
}
