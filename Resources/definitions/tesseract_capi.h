/**
This file was created using include/tesseract/capi.h from https://github.com/tesseract-ocr/tesseract/
**/
const char* TessVersion();
typedef struct TessBaseAPI TessBaseAPI;
TessBaseAPI* TessBaseAPICreate();
void TessBaseAPIDelete(TessBaseAPI* handle);
char** TessBaseAPIGetAvailableLanguagesAsVector(const TessBaseAPI* handle);
void TessBaseAPIEnd(TessBaseAPI* handle);
int TessBaseAPIInit3(TessBaseAPI* handle, const char* datapath, const char* language);
