<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\Exception;
use Bicycle\Tesseract\Bridge\Exception\ExtensionRequiredException;
use Bicycle\Tesseract\Bridge\Exception\InputProblemException;
use Bicycle\Tesseract\Bridge\Exception\UnavailableLanguageException;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FFITest extends TestCase
{
    /** @var FFI */
    private FFI $testInstance;

    /** @var string */
    private string $testImagePath;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->testImagePath = realpath(
            sprintf(
                '%1$s%2$s..%2$s..%2$s..%2$sdata%2$simage%2$seurotext.png',
                __DIR__,
                DIRECTORY_SEPARATOR
            )
        );
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

    /**
     * @return array
     */
    public function headerFileDataProvider(): array
    {
        $emptyHeaderPath = realpath(
            sprintf(
                '%1$s%2$s..%2$s..%2$s..%2$sdata%2$stext%2$sempty_c_header.h',
                __DIR__,
                DIRECTORY_SEPARATOR
            )
        );

        return [
            [$emptyHeaderPath],
            [''],
        ];
    }

    /**
     * @dataProvider headerFileDataProvider
     *
     * @param string $headerPath
     */
    public function testWithEmptyHeaderFile(string $headerPath): void
    {
        $this->isFFIEnabled();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot use FFI without valid header file');
        $configuration = new Configuration(['capi_header_path' => $headerPath]);
        new FFI($configuration);
    }

    public function testIncorrectConfiguration(): void
    {
        $this->isFFIEnabled();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Problem with connecting library via FFI: empty library path');
        $configuration = new Configuration([]);
        new FFI($configuration);
    }

    public function testIncorrectHeaderFile(): void
    {
        $incorrectHeaderPath = realpath(
            sprintf(
                '%1$s%2$s..%2$s..%2$s..%2$sdata%2$stext%2$sincorrect_c_header.h',
                __DIR__,
                DIRECTORY_SEPARATOR
            )
        );
        $this->isFFIEnabled();
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/^Problem with connecting library via FFI\: .+$/');
        $configuration = new Configuration([
            'capi_header_path' => $incorrectHeaderPath,
            'library_path' => 'libtesseract.so.4',
        ]);
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

    public function testRecognizeFromFileNonExistentFile(): void
    {
        $this->isFFIEnabled();
        $filename = 'text.png';
        $this->expectException(InputProblemException::class);
        $this->expectExceptionMessage('Cannot read input file');
        $this->testInstance->recognizeFromFile($filename);
    }

    public function testRecognizeFromFileNonExistentLang(): void
    {
        $this->isFFIEnabled();
        $this->expectException(UnavailableLanguageException::class);
        $this->expectExceptionMessage('Unknown language(s) en for recognition.');
        $this->testInstance->recognizeFromFile($this->testImagePath, ['en']);
    }

    public function testRecognizeFromFileWithoutLang(): void
    {
        $this->isFFIEnabled();
        self::assertEquals(
            // Tesseract adds \f to end of output files, but not for C API
            rtrim(file_get_contents($this->getTestInfo('eurotext-eng.txt')), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, [])
        );
    }

    public function testRecognizeFromFileWithOneLang(): void
    {
        $this->isFFIEnabled();
        self::assertEquals(
            // Tesseract adds \f to end of output files, but not for C API
            rtrim(file_get_contents($this->getTestInfo('eurotext-eng.txt')), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, ['eng'])
        );
    }

    public function testRecognizeFromFileWithDiffLang(): void
    {
        $this->isFFIEnabled();
        self::assertNotEquals(
            // Tesseract adds \f to end of output files, but not for C API
            rtrim(file_get_contents($this->getTestInfo('eurotext-eng.txt')), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, ['spa'])
        );
    }

    /**
     * @return array[]
     */
    public function languagesDataProvider(): array
    {
        return [
            ['eurotext-deueng.txt', ['deu', 'eng']],
            ['eurotext-deuspa.txt', ['deu', 'spa']],
            ['eurotext-deuspaeng.txt', ['deu', 'spa', 'eng']],
            ['eurotext-engdeu.txt', ['eng', 'deu']],
            ['eurotext-engdeuspa.txt', ['eng', 'deu', 'spa']],
            ['eurotext-spadeu.txt', ['spa', 'deu']],
        ];
    }

    /**
     * @dataProvider languagesDataProvider
     *
     * @param string $testData
     * @param array  $languages
     */
    public function testRecognizeFromFileWithLangs(string $testData, array $languages): void
    {
        $this->isFFIEnabled();
        self::assertEquals(
            // Tesseract adds \f to end of output files, but not for C API
            rtrim(file_get_contents($this->getTestInfo($testData)), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, $languages)
        );
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
