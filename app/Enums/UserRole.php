<?php

namespace App\Enums;

enum UserRole: string
{
    case Mahasiswa = 'mahasiswa';
    case KepalaPerpustakaan = 'kepala_perpustakaan';
}
