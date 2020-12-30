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
        $cliBinary = $this->configuration->getCliBinaryPath();
        if (empty($cliBinary)) {
            throw new Exception\Exception('Cannot use CLI without proper cli path');
        }
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
        preg_match('/[\d\.]+.*$/', $output[0], $matches);

        return $matches[0] ?? '';
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableLanguages(): array
    {
        $output = $this->executeCommand(['--list-langs'])->getOutputArray();
        if (empty($output)) {
            return [];
        }

        return array_slice($output, 1);
    }

    /**
     * @param array $arguments
     *
     * @return Result
     */
    private function executeCommand(array $arguments): Result
    {
        $output = null;
        $resultCode = null;
        /** @var string $cliPath see constructor */
        $cliPath = $this->configuration->getCliBinaryPath();
        exec(
            sprintf('%s %s 2>&1', $cliPath, implode(' ', $arguments)),
            $output,
            $resultCode
        );

        return new Result($resultCode, $output);
    }
}
