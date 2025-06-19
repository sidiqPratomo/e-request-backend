<?php

namespace App\Http\Controllers\Api\Storage;

use App\BC\Storage\Domain\FileRepository;
use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    protected $fileRepository;

    protected $responseService;

    public function __construct(
        FileRepository $fileRepository,
        ResponseService $responseService
    ) {
        $this->fileRepository = $fileRepository;
        $this->responseService = $responseService;
    }

    public function upload(Request $request): JsonResponse
    {
        $bucket = $request->get('bucket', 'files');
        // $path = date('Ymd') . '/' . $request->get('path', null);
        $path = $request->get('path', null);
        $file = $request->file('file');

        if (is_null($file)) {
            return $this->responseService->errorResponse(
                [
                    'file' => [
                        'message' => 'The file field is required.',
                    ],
                ],
                'File not found'
            );
        }

        if (is_array($file)) {
            return $this->multipleFile($bucket, $path, $file);
        }

        return $this->singleFile($bucket, $path, $file);
    }

    public function get(Request $request)
    {
        try {
            $bucket = $request->get('bucket');
            $path = $request->get('path');
            $filename = $request->get('filename');

            if (is_null($path)) {
                return $this->responseService->errorResponse(
                    [
                        'path' => [
                            'message' => 'The path field is required.',
                        ],
                    ],
                    'Path not found'
                );
            }

            $file = $this->fileRepository->read($bucket, $path, $filename);

            return Storage::download($file->getFullPath(), $file->getFilename());
        } catch (FileNotFoundException $exception) {
            return $this->responseService->errorResponse([], $exception->getMessage(), 404);
        } catch (Exception $exception) {
            return $this->responseService->internalServerErrorResponse($exception);
        }
    }

    private function singleFile(string $bucket, string $path, UploadedFile $file)
    {
        $entry = $this->fileRepository->write($bucket, $path, $file);

        return $this->responseService->successResponse($entry->toArray());
    }

    private function multipleFile(string $bucket, string $path, array $files)
    {
        $entries = array_map(function ($file) use ($bucket, $path) {
            return $this->fileRepository->write($bucket, $path, $file)->toArray();
        }, $files);

        return $this->responseService->successResponse($entries);
    }
}
