<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\ExtensionRequiredException;
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
            throw new ExtensionRequiredException('FFI extension is required for this functionality');
        }
        // Prevent configuration change in runtime
        $this->configuration = clone $configuration;
        $definitions = file_get_contents($configuration->getCApiHeaderpath());
        if (empty($definitions)) {
            throw new Exception\Exception('Cannot use FFI without valid header file');
        }
        $this->ffiInstance = \FFI::cdef(
            $definitions,
            $this->configuration->getSharedLibraryPath()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion(): string
    {
        return $this->ffiInstance->TessVersion();
    }
}
