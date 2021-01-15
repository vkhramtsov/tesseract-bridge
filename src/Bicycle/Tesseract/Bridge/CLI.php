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
     * {@inheritDoc}
     */
    public function recognizeFromFile(string $filename, array $languages = []): string
    {
        if (!\is_readable($filename)) {
            throw new Exception\InputProblemException('Cannot read input file');
        }
        $languagesArg = '';
        if (!empty($languages)) {
            if (
                count($intersection = array_intersect(
                    $languages,
                    $this->getAvailableLanguages()
                )) !== count($languages)
            ) {
                $exceptionMessage = sprintf(
                    'Unknown language(s) %s for recognition.',
                    implode(', ', array_diff($languages, $intersection))
                );
                throw new Exception\UnavailableLanguageException($exceptionMessage);
            }
            $languagesArg = sprintf('-l %s', escapeshellarg(implode('+', $languages)));
        }

        $tmpOutFile = sprintf(
            '%s%sphp_tesseract_ocr_%s',
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR,
            /**
             * It looks like 5 will be enough (640 Kb...).
             * Also we need to remove directory separator from generated string.
             */
            str_replace([DIRECTORY_SEPARATOR], '_', base64_encode(random_bytes(5)))
        );
        $realTmpOutFile = $tmpOutFile . '.txt';
        $this->executeCommand(
            [
                escapeshellarg($filename),
                escapeshellarg($tmpOutFile),
                $languagesArg,
            ]
        );

        // Adding .txt because tesseract automatically add .txt to output files
        $recognizedText = rtrim(file_get_contents($realTmpOutFile), "\f");

        unlink($realTmpOutFile);

        return $recognizedText;
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
