<?php

namespace App\Enums;

enum SkripsiFileType: string
{
    case Skripsi = 'skripsi';
    case SourceCode = 'source_code';
    case Dokumentasi = 'dokumentasi';
    case Dataset = 'dataset';
    case Presentasi = 'presentasi';
    case Lainnya = 'lainnya';
}
