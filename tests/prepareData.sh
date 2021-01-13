#!/bin/sh -xe

currentDir="$(dirname $(realpath $0))"
tmpDir="$currentDir/tmp";

mkdir -p $tmpDir
tesseract --version 2>&1 | head -n 1 | sed 's/tesseract //' > "$tmpDir/version.txt"
tesseract --list-langs 2>&1 | tail -n+2 > "$tmpDir/langs.txt"
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-eng" -l eng
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-engdeu" -l eng+deu
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-deueng" -l deu+eng
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-deuspa" -l deu+spa
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-spadeu" -l spa+deu
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-engdeuspa" -l eng+deu+spa
tesseract "$currentDir/data/image/eurotext.png" "$tmpDir/eurotext-deuspaeng" -l deu+spa+eng
