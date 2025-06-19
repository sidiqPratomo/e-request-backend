<?php

namespace App\BC\Storage\Domain;

use App\Framework\Domain\DefaultDomain;

class File extends DefaultDomain
{
    protected string $bucket;

    protected string $path;

    protected string $mime;

    protected string $filename;

    protected string $originalFilename;

    public function setBucket(string $bucket)
    {
        $this->bucket = $bucket;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function setMime(string $mime)
    {
        $this->mime = $mime;
    }

    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setOriginalFilename(string $originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    public function getFullPath(): string
    {
        return $this->bucket . '/' . $this->path . '/' . $this->filename;
    }

    public function getPath(): string
    {
        return $this->bucket . '/' . $this->path;
    }

    public function toArray(): array
    {
        return [
            'bucket' => $this->bucket,
            'path' => $this->path,
            'mime' => $this->mime,
            'filename' => $this->filename,
            'originalFilename' => $this->originalFilename,
        ];
    }
}
