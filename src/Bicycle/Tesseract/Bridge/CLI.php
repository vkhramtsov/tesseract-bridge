<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\CLI\Result;
use Bicycle\Tesseract\BridgeInterface;

class CLI implements BridgeInterface
{
    /** @var Configuration */
    private Configuration $configuration;

    /**
     * {@inheritDoc}
     */
    public function __construct(Configuration $configuration)
    {
        // Prevent configuration change in runtime
        $this->configuration = clone $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(): string
    {
        $output = $this->executeCommand(['--version'])->getOutputArray();
        if (empty($output)) {
            return '';
        }
        $matches = [];
        preg_match('/[\d\.]+.*/', $output[0], $matches);

        return $matches[0] ?? '';
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    private function executeCommand(array $arguments): Result
    {
        $output = null;
        $resultCode = null;
        exec(
            sprintf('%s %s', $this->configuration->getCliBinaryPath(), implode(' ', $arguments)),
            $output,
            $resultCode
        );

        return new Result($resultCode, $output);
    }
}
