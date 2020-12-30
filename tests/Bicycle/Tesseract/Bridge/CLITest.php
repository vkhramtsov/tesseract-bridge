<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\Exception;
use PHPUnit\Framework\TestCase;

class CLITest extends TestCase
{
    /** @var CLI */
    private CLI $testInstance;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $cliPath = $this->findExecutable();
        $configuration = new Configuration(['binary_path' => $cliPath]);
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
     * @return string
     */
    private function findExecutable(): string
    {
        $cliPath = '';
        switch (PHP_OS) {
            case 'Linux':
                $cliPath = '/usr/bin/tesseract';
                break;
            case 'FreeBSD':
                $cliPath = '/usr/local/bin/tesseract';
                break;
        }

        if (!empty($cliPath) && \is_executable($cliPath)) {
            return $cliPath;
        }
        $this->fail('No tesseract cli found');
    }
}
