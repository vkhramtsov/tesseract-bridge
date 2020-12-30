#!/bin/sh -xe

tmpDir="$(dirname $(realpath $0))/tmp";

mkdir -p $tmpDir
tesseract --version 2>&1 | head -n 1 | sed 's/tesseract //' > "$tmpDir/version.txt"
tesseract --list-langs 2>&1 | tail -n+2 > "$tmpDir/langs.txt"
