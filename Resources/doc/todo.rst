Things to do
============

- Remove ``PHP_CS_FIXER_IGNORE_ENV=1`` from ``.travis.yml`` after closing ``https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/4702``
- Run travis build with ``FFI`` extension enabled. Check state of ``https://github.com/travis-ci/php-src-builder/pull/49`` first. Revise psalm config (remove suppress section for src/Bicycle/Tesseract/Bridge/FFI.php).
- Switch build to phing which able to work with php8
