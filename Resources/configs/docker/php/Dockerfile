ARG PHP_VERSION
FROM php:$PHP_VERSION
RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y libffi-dev tesseract-ocr tesseract-ocr-eng tesseract-ocr-deu tesseract-ocr-spa \
    && docker-php-source extract \
    && docker-php-ext-install ffi \
    && docker-php-source delete \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug
