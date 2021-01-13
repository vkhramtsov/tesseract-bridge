<?php

namespace Bicycle\Tesseract;

use Bicycle\Tesseract\Bridge\Configuration;

interface BridgeInterface
{
    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration);

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return array
     */
    public function getAvailableLanguages(): array;

    /**
     * @param string $filename
     * @param array  $languages
     *
     * @return string
     */
    public function recognizeFromFile(string $filename, array $languages = []): string;
}
