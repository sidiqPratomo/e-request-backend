<?php

namespace App\BC\Storage;

use App\BC\Storage\Domain\FileRepository;
use App\BC\Storage\Infrastructure\LocalFileRepository;

class StorageContext
{
    public function init($app)
    {
        $app->bind(StorageContext::class, function () {
            return new StorageContext();
        });
        $app->bind(FileRepository::class, function () {
            return new LocalFileRepository();
        });
    }
}
