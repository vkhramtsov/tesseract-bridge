<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\Exception;
use Bicycle\Tesseract\Bridge\Exception\InputProblemException;
use Bicycle\Tesseract\Bridge\Exception\UnavailableLanguageException;
use PHPUnit\Framework\TestCase;

class CLITest extends TestCase
{
    /** @var CLI */
    private CLI $testInstance;

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
        $configuration = new Configuration(['binary_path' => 'tesseract']);
        $this->testInstance = new CLI($configuration);
    }

    public function testIncorrectConfiguration(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot use CLI without proper cli path');
        $configuration = new Configuration([]);
        new CLI($configuration);
    }

    public function testGetVersion(): void
    {
        $versionInfo = trim(file_get_contents($this->getTestInfo('version.txt')));
        self::assertEquals($versionInfo, $this->testInstance->getVersion());
    }

    public function testGetAvailableLanguages(): void
    {
        $languagesInfo = file(
            $this->getTestInfo('langs.txt'),
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
        self::assertEquals($languagesInfo, $this->testInstance->getAvailableLanguages());
    }

    public function testRecognizeFromFileNonExistentFile(): void
    {
        $filename = 'text.png';
        $this->expectException(InputProblemException::class);
        $this->expectExceptionMessage('Cannot read input file');
        $this->testInstance->recognizeFromFile($filename);
    }

    public function testRecognizeFromFileNonExistentLang(): void
    {
        $this->expectException(UnavailableLanguageException::class);
        $this->expectExceptionMessage('Unknown language(s) en for recognition.');
        $this->testInstance->recognizeFromFile($this->testImagePath, ['en']);
    }

    public function testRecognizeFromFileWithoutLang(): void
    {
        self::assertEquals(
            // Just because tesseract adds \f to the end of files
            rtrim(file_get_contents($this->getTestInfo('eurotext-eng.txt')), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, [])
        );
    }

    public function testRecognizeFromFileWithOneLang(): void
    {
        self::assertEquals(
            // Just because tesseract adds \f to the end of files
            rtrim(file_get_contents($this->getTestInfo('eurotext-eng.txt')), "\f"),
            $this->testInstance->recognizeFromFile($this->testImagePath, ['eng'])
        );
    }

    public function testRecognizeFromFileWithDiffLang(): void
    {
        self::assertNotEquals(
            // Just because tesseract adds \f to the end of files
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
        self::assertEquals(
            // Just because tesseract adds \f to the end of files
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
}
