FAQ
===

- FFI integration fails with segmenation fault and error message ``!strcmp(locale, "C"):Error:Assert failed:in file baseapi.cpp, line 209``.
  This is problem in tesseract engine itself. You could fix it with quick and dirty solution like ``setlocale(LC_ALL, 'C');`` before API call;
