<?php

namespace App\BC\Storage\Domain;

use Illuminate\Http\UploadedFile;

interface FileRepository
{
    public function write(string $bucket, string $path, UploadedFile $file): File;

    public function read(string $bucket, string $path, string $filename): File;
}
