<?php

namespace Bicycle\Tesseract\Bridge\FFI;

/**
 * @internal
 */
interface TesseractInterface
{
    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @return string
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessVersion(): string;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @return mixed
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPICreate();

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed       $apiHandle got from TessBaseAPICreate
     * @param string|null $dataPath  actually it is C pointer (const char*)
     * @param string|null $language  actually it is C pointer (const char*)
     *
     * @return bool
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIInit3($apiHandle, ?string $dataPath, ?string $language): bool;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed $apiHandle got from TessBaseAPICreate
     *
     * @return array
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIGetAvailableLanguagesAsVector($apiHandle): array;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed $apiHandle got from TessBaseAPICreate
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIEnd($apiHandle): void;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed $apiHandle got from TessBaseAPICreate
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIDelete($apiHandle): void;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed       $apiHandle
     * @param string      $filename
     * @param string|null $retryConfig
     * @param int         $timeoutMillisec
     * @param mixed|null  $renderer
     *
     * @return int
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIProcessPages(
        $apiHandle,
        string $filename,
        ?string $retryConfig,
        int $timeoutMillisec,
        $renderer
    ): int;

    /**
     * Unfortunately phpcs works only with single comment, so we have to have two comments here.
     *
     * @param mixed $apiHandle got from TessBaseAPICreate
     */
    //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function TessBaseAPIGetUTF8Text($apiHandle): string;
}
