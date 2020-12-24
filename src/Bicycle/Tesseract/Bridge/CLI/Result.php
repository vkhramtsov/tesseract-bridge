<?php

namespace Bicycle\Tesseract\Bridge\CLI;

class Result
{
    /** @var int */
    private int $returnCode;

    /** @var array */
    private array $outputArray;

    /**
     * @param int   $returnCode
     * @param array $outputArray
     */
    public function __construct(int $returnCode, array $outputArray)
    {
        $this->returnCode = $returnCode;
        $this->outputArray = $outputArray;
    }

    /**
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    /**
     * @return array
     */
    public function getOutputArray(): array
    {
        return $this->outputArray;
    }

    public function getOutputString(): string
    {
        return implode(PHP_EOL, $this->getOutputArray());
    }
}
