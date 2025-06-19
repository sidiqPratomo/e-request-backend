<?php

namespace App\BC\Storage\Infrastructure;

use App\BC\Storage\Domain\File;
use App\BC\Storage\Domain\FileRepository;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalFileRepository implements FileRepository
{
    public function write(string $bucket, string $path, UploadedFile $file): File
    {
        try {
            $fullpath = $this->generateFullPath($bucket, $path);
            $filename = $file->getClientOriginalName();
            $filename = md5($filename . rand(1, 99999) . time()) . '_' . $file->getClientOriginalName();
            $file->storeAs($fullpath, $filename);

            $entry = new File();
            $entry->setBucket($bucket);
            $entry->setPath($path);
            $entry->setMime($file->getClientMimeType());
            $entry->setFilename($filename);
            $entry->setOriginalFilename($file->getClientOriginalName());

            return $entry;
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    public function read(string $bucket, string $path, string $filename): File
    {
        try {
            if (empty($filename)) {
                throw new Exception('No bucket specified', 404);
            }

            if (empty($path) || empty($bucket)) {
                throw new Exception('No path specified', 404);
            }

            $fullpath = $this->generateFullUrl($bucket, $path, $filename);
            if (Storage::exists($fullpath)) {
                $file = new File();
                $file->setBucket($bucket);
                $file->setPath($path);
                $file->setMime(Storage::mimeType($fullpath));
                $file->setFilename(basename(Storage::path($fullpath)));
                $file->setOriginalFilename($this->generateOriginalFilename($file->getFilename()));

                return $file;
            }

            throw new FileNotFoundException('file not found', 404);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    private function generateOriginalFilename(string $filename): string
    {
        if ($filename) {
            $removeRandomFilename = strpos($filename, '_');
            $originFilename = substr($filename, $removeRandomFilename + 1);

            return $originFilename;
        }

        return '';
    }

    private function generateFullPath(string $bucket, string $path): string
    {
        return $bucket . '/' . $path;
    }

    private function generateFullUrl(string $bucket, string $path, string $filename): string
    {
        return $bucket . '/' . $path . '/' . $filename;
    }
}
