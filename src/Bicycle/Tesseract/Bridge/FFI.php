<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\BridgeInterface;

/**
 * Please note that here we have \FFI class instance instead of FFI\TesseractInterface.
 */
class FFI implements BridgeInterface
{
    /** @var Configuration */
    private Configuration $configuration;

    /**
     * I have to use interface here, but actually we have here \FFI class instance. Do not set type for this property!
     *
     * @var FFI\TesseractInterface
     */
    private $ffiInstance;

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
                throw new Exception\Exception('Problem with connecting library via FFI: empty library path');
            }
            /** @var FFI\TesseractInterface ffiInstance */
            $this->ffiInstance = \FFI::cdef(
                $definitions,
                $libaryPath
            );
        } catch (\FFI\Exception $e) {
            throw new Exception\Exception(sprintf('Problem with connecting library via FFI: %s', $e->getMessage()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(): string
    {
        return $this->ffiInstance->TessVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableLanguages(): array
    {
        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         */
        $baseApiHandle = $this->ffiInstance->TessBaseAPICreate();
        $initFailed = $this->ffiInstance->TessBaseAPIInit3($baseApiHandle, null, null); // Tesseract initialization
        if ($initFailed) {
            $this->ffiInstance->TessBaseAPIDelete($baseApiHandle);
            throw new Exception\Exception('Cannot initialize tesseract');
        }
        $languages = $this->ffiInstance->TessBaseAPIGetAvailableLanguagesAsVector($baseApiHandle);
        $counter = 0;
        // According to body of TessBaseAPIGetAvailableLanguagesAsVector method, last element will be nullptr
        while (!\is_null($languages[$counter])) {
            /** @psalm-suppress MixedAssignment */
            $result[] = \FFI::string($languages[$counter++]);
        }
        $this->ffiInstance->TessBaseAPIEnd($baseApiHandle);
        $this->ffiInstance->TessBaseAPIDelete($baseApiHandle);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function recognizeFromFile(string $filename, array $languages = []): string
    {
        if (!\is_readable($filename)) {
            throw new Exception\InputProblemException('Cannot read input file');
        }
        if (empty($languages)) {
            $languages[] = 'eng';
        } elseif (
            count($intersection = array_intersect($languages, $this->getAvailableLanguages())) !== count($languages)
        ) {
            $exceptionMessage = sprintf(
                'Unknown language(s) %s for recognition.',
                implode(', ', array_diff($languages, $intersection))
            );
            throw new Exception\UnavailableLanguageException($exceptionMessage);
        }

        $resultText = '';

        /**
         * @psalm-suppress MixedAssignment
         */
        $baseApiHandle = $this->ffiInstance->TessBaseAPICreate();
        $initFailed = $this->ffiInstance->TessBaseAPIInit3(
            $baseApiHandle,
            null,
            implode('+', $languages)
        ); // Tesseract initialization
        if ($initFailed) {
            $this->ffiInstance->TessBaseAPIDelete($baseApiHandle);
            throw new Exception\Exception('Cannot initialize tesseract');
        }

        if ($this->ffiInstance->TessBaseAPIProcessPages($baseApiHandle, $filename, null, 0, null)) {
            $resultText = $this->ffiInstance->TessBaseAPIGetUTF8Text($baseApiHandle);
            /** @var string $resultText */
            $resultText = \FFI::string($resultText);
        }

        $this->ffiInstance->TessBaseAPIEnd($baseApiHandle);
        $this->ffiInstance->TessBaseAPIDelete($baseApiHandle);

        return $resultText;
    }
}
