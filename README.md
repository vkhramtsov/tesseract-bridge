[![Codacy Badge](https://api.codacy.com/project/badge/Grade/2e87afedaea348908638368b7a020c32)](https://app.codacy.com/gh/vkhramtsov/tesseract-bridge?utm_source=github.com&utm_medium=referral&utm_content=vkhramtsov/tesseract-bridge&utm_campaign=Badge_Grade_Settings)
A wrapper to work with Tesseract OCR inside PHP via CLI and/or FFI interfaces. [![Build Status](https://travis-ci.org/vkhramtsov/tesseract-bridge.svg?branch=master)](https://travis-ci.org/vkhramtsov/tesseract-bridge)

:bangbang: **Tested only on FreeBSD and Debian and Ubuntu platforms with [Tesseract OCR][] version 3 and 4 (see build logs).**

## Installation

Via [Composer][]:

    $ composer require bicycle/tesseract-bridge

## Usage

### Basic usage

  ![example](tests/data/image/eurotext.png)

- CLI
  ```php
  use Bicycle\Tesseract\Bridge as TesseractBridge;
  
  $configuration = TesseractBridge\Configuration(['binary_path' => 'tesseract']);
  $bridge = new TesseractBridge\CLI($configuration);
  echo $bridge->testGetVersion();
  print_r($bridge->getAvailableLanguages());
  echo $bridge->recognizeFromFile('eurotext.png'); // Set proper path here
  ```

- FFI
  ```php
  use Bicycle\Tesseract\Bridge as TesseractBridge;
  
  $configuration = TesseractBridge\Configuration(['binary_path' => 'tesseract']);
  $bridge = new TesseractBridge\FFI($configuration);
  echo $bridge->testGetVersion();
  print_r($bridge->getAvailableLanguages());
  echo $bridge->recognizeFromFile('eurotext.png'); // Set proper path here
  ```

### With languages

- CLI
  ```php
  use Bicycle\Tesseract\Bridge as TesseractBridge;
  
  $configuration = TesseractBridge\Configuration(['binary_path' => 'tesseract']);
  $bridge = new TesseractBridge\CLI($configuration);
  echo $bridge->testGetVersion();
  print_r($bridge->getAvailableLanguages());
  echo $bridge->recognizeFromFile('eurotext.png', ['deu']); // Set proper path here
  ```

- FFI
  ```php
  use Bicycle\Tesseract\Bridge as TesseractBridge;
  
  $configuration = TesseractBridge\Configuration(['binary_path' => 'tesseract']);
  $bridge = new TesseractBridge\FFI($configuration);
  echo $bridge->testGetVersion();
  print_r($bridge->getAvailableLanguages());
  echo $bridge->recognizeFromFile('eurotext.png', ['deu']); // Set proper path here
  ```

## How to contribute

You can contribute to this project by:

* Opening an [Issue][] if you found a bug or wish to propose a new feature;
* Opening [PR][] if you want to improve/create/fix something

## Additional
Please check our [FAQ][]

## License

tesseract-ocr-for-php is released under the [MIT License][].


[Tesseract OCR]: https://github.com/tesseract-ocr/tesseract
[Composer]: https://getcomposer.org/
[Issue]: ../../issues
[PR]: ../../pulls
[FAQ]: ./Resources/doc/faq.rst
[MIT License]: ./LICENSE
