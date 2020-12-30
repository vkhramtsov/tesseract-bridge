<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\Exception;
use Bicycle\Tesseract\Bridge\Exception\ExtensionRequiredException;
use PHPUnit\Framework\TestCase;

class FFITest extends TestCase
{
    /** @var FFI */
    private FFI $testInstance;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Create configuration with default c header file
        $configuration = new Configuration(['library_path' => 'libtesseract.so.4']);
        if ($this->isFFIEnabled(false)) {
            $this->testInstance = new FFI($configuration);
            /**
             * Because in some versions of tesseract otherwise we will get segmentation fault with
             * !strcmp(locale, "C"):Error:Assert failed:in file baseapi.cpp, line 209.
             */
            setlocale(LC_ALL, 'C');
        }
    }

    public function testExtensionNotLoaded(): void
    {
        if ($this->isFFIEnabled(false)) {
            self::markTestSkipped('Cannot check state when FFI is loaded');
        }
        $this->expectException(ExtensionRequiredException::class);
        $this->expectExceptionMessage('FFI extension is required for this functionality');
        $configuration = new Configuration(['library_path' => 'libtesseract.so.4']);
        new FFI($configuration);
    }

    public function testIncorrectConfiguration(): void
    {
        $this->isFFIEnabled();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Problem with connecting library via FFI');
        $configuration = new Configuration([]);
        new FFI($configuration);
    }

    public function testGetVersion(): void
    {
        $this->isFFIEnabled();
        $versionInfo = trim(file_get_contents($this->getTestInfo('version.txt')));
        self::assertEquals($versionInfo, $this->testInstance->getVersion());
    }

    public function testGetAvailableLanguages(): void
    {
        $this->isFFIEnabled();
        $languagesInfo = file(
            $this->getTestInfo('langs.txt'),
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
        self::assertEquals($languagesInfo, $this->testInstance->getAvailableLanguages());
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getTestInfo(string $filename): string
    {
        $path = realpath(sprintf('%1$s%2$s..%2$s..%2$s..%2$stmp%2$s%3$s', __DIR__, DIRECTORY_SEPARATOR, $filename));
        if (!\is_readable($path) && !\is_file($path)) {
            self::markTestSkipped('Cannot test without prepared test data');
        }

        return $path;
    }

    /**
     * @param bool $markSkipped
     *
     * @return bool
     */
    private function isFFIEnabled(bool $markSkipped = true): bool
    {
        if (!extension_loaded('ffi')) {
            if ($markSkipped) {
                self::markTestSkipped();
            }

            return false;
        }

        return true;
    }
}
