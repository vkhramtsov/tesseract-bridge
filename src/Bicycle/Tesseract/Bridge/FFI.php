<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\BridgeInterface;

class FFI implements BridgeInterface
{
    /** @var Configuration */
    private Configuration $configuration;

    /** @var \FFI */
    private \FFI $ffiInstance;

    /**
     * {@inheritDoc}
     */
    public function __construct(Configuration $configuration)
    {
        if (!extension_loaded('ffi')) {
            throw new Exception\ExtensionRequiredException('FFI extension is required for this functionality');
        }
        // Prevent configuration change in runtime
        $this->configuration = clone $configuration;
        $headerPath = $this->configuration->getCApiHeaderpath();
        if (
            empty($headerPath) ||
            empty($definitions = file_get_contents($headerPath))
        ) {
            throw new Exception\Exception('Cannot use FFI without valid header file');
        }
        try {
            $libaryPath = $this->configuration->getSharedLibraryPath();
            if (empty($libaryPath)) {
                throw new Exception\Exception('Problem with connecting library via FFI');
            }
            /** @var \FFI ffiInstance */
            $this->ffiInstance = \FFI::cdef(
                $definitions,
                $libaryPath
            );
        } catch (\FFI\Exception $e) {
            throw new Exception\Exception('Problem with connecting library via FFI');
        }
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedInferredReturnType
     */
    public function getVersion(): string
    {
        /**
         * @psalm-suppress UndefinedMethod
         * @psalm-suppress MixedReturnStatement
         */
        return $this->ffiInstance->TessVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableLanguages(): array
    {
        $result = [];
        /**
         * @psalm-suppress UndefinedMethod
         * @psalm-suppress MixedAssignment
         */
        $baseApiHandle = $this->ffiInstance->TessBaseAPICreate();
        /**
         * @psalm-suppress UndefinedMethod
         *
         * @var bool $initFailed
         */
        $initFailed = $this->ffiInstance->TessBaseAPIInit3($baseApiHandle, null, null); // Tesseract initialization
        if ($initFailed) {
            throw new Exception\Exception('Cannot initialize tesseract');
        }
        /**
         * @psalm-suppress UndefinedMethod
         *
         * @var array $languages
         */
        $languages = $this->ffiInstance->TessBaseAPIGetAvailableLanguagesAsVector($baseApiHandle);
        $counter = 0;
        // According to body of TessBaseAPIGetAvailableLanguagesAsVector method, last element will be nullptr
        while (!\is_null($languages[$counter])) {
            /** @psalm-suppress MixedAssignment */
            $result[] = \FFI::string($languages[$counter++]);
        }
        /** @psalm-suppress UndefinedMethod */
        $this->ffiInstance->TessBaseAPIEnd($baseApiHandle);
        /** @psalm-suppress UndefinedMethod */
        $this->ffiInstance->TessBaseAPIDelete($baseApiHandle);

        return $result;
    }
}
