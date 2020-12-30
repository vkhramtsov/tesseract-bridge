<?php

namespace Bicycle\Tesseract\Bridge;

use Bicycle\Tesseract\Bridge\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /** @var string[] */
    private const ALLOWED_OPTIONS = ['library_path', 'binary_path', 'capi_header_path'];

    public function testConstructorDefaultOptions(): void
    {
        $testInstance = new Configuration([]);
        self::assertNull($testInstance->getSharedLibraryPath());
        self::assertNull($testInstance->getCliBinaryPath());
        $defaultPath = realpath(
            sprintf(
                '%1$s%2$s..%2$s..%2$s..%2$s..%2$sResources%2$sdefinitions%2$stesseract_capi.h',
                __DIR__,
                DIRECTORY_SEPARATOR
            )
        );
        self::assertEquals($defaultPath, $testInstance->getCApiHeaderpath());
    }

    public function testConstructWithIncorrectOptions(): void
    {
        $options = ['test option' => 'asdasd'];
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Problem with options %s, allowed options %s',
                implode(', ', array_keys($options)),
                implode(', ', self::ALLOWED_OPTIONS)
            )
        );
        new Configuration($options);
    }

    /**
     * @return array[]
     */
    public function optionsDataProvider(): array
    {
        return [
            [['library_path' => 'test1', 'binary_path' => 'test2', 'capi_header_path' => 'test3']],
        ];
    }

    /**
     * @dataProvider optionsDataProvider
     *
     * @param array $options
     */
    public function testGetters(array $options): void
    {
        $testInstance = new Configuration($options);
        self::assertEquals($options['library_path'], $testInstance->getSharedLibraryPath());
        self::assertEquals($options['binary_path'], $testInstance->getCliBinaryPath());
        self::assertEquals($options['capi_header_path'], $testInstance->getCApiHeaderpath());
    }
}
