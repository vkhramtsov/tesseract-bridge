/**
This file was created using include/tesseract/capi.h from https://github.com/tesseract-ocr/tesseract/
**/
typedef struct TessBaseAPI TessBaseAPI;
typedef struct TessResultRenderer TessResultRenderer;
const char* TessVersion();
TessBaseAPI* TessBaseAPICreate();
void TessBaseAPIDelete(TessBaseAPI* handle);
char** TessBaseAPIGetAvailableLanguagesAsVector(const TessBaseAPI* handle);
void TessBaseAPIEnd(TessBaseAPI* handle);
int TessBaseAPIInit3(TessBaseAPI* handle, const char* datapath, const char* language);
int TessBaseAPIProcessPages(TessBaseAPI* handle, const char* filename, const char* retry_config, int timeout_millisec, TessResultRenderer* renderer);
char* TessBaseAPIGetUTF8Text(TessBaseAPI* handle);
