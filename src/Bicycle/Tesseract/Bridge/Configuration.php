<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\ConfigurationException;

class Configuration
{
    /** @var string[] */
    private const ALLOWED_OPTIONS = ['library_path', 'binary_path', 'capi_header_path'];

    /** @var array */
    private array $options;

    /**
     * @param array $options
     *
     * @throws ConfigurationException
     */
    public function __construct(array $options)
    {
        $this->validateOptions($options);
        $this->options = $options;
    }

    /**
     * @return string|null
     */
    public function getSharedLibraryPath(): ?string
    {
        return $this->options['library_path'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCliBinaryPath(): ?string
    {
        return $this->options['binary_path'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCApiHeaderpath(): ?string
    {
        return
            $this->options['capi_header_path'] ??
                realpath(
                    sprintf(
                        '%1$s%2$s..%2$s..%2$s..%2$s..%2$sResources%2$sdefinitions%2$stesseract_capi.h',
                        __DIR__,
                        DIRECTORY_SEPARATOR
                    )
                );
    }

    /**
     * @param array $options
     */
    private function validateOptions(array $options): void
    {
        $problematicOptions = [];
        foreach (array_keys($options) as $option) {
            if (!in_array($option, static::ALLOWED_OPTIONS, true) || !\is_string($options[$option])) {
                $problematicOptions[] = $option;
            }
        }
        if (count($problematicOptions)) {
            throw new ConfigurationException(sprintf('Problem with options %s', implode(', ', $problematicOptions)));
        }
    }
}
