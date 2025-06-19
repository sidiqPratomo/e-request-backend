<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeavessResource;
use App\Models\Resources;
use App\Services\Report\ReportServiceFactory;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeavesController extends ApiResourcesController
{
    public function __construct(Request $request, Resources $model, ResponseService $response, ReportServiceFactory $report)
    {
        parent::__construct($request, $model, $response, $report);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $this->checkModel();

            $validators = $this->model->validator($request);

            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $fields = $this->processRequestFields($request);

            $this->model->fill($fields);
            $this->model->save();

            $this->model->save();

            return $this->response->response($request->all(), 'Data Created', 201);
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function read(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $entry = $this->model->statusActive()->find($this->id);

            if (! $entry) {
                return $this->response->notFoundResponse();
            }

            $formattedEntry = new LeavessResource($entry);

            return $this->response->successResponse($formattedEntry->toArray($request));
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $entry = $this->model->statusActive()->find($this->id);

            if (! $entry) {
                return $this->response->notFoundResponse();
            }

            $validators = $this->model->validator($request);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $fields = $this->processRequestFields($request);

            $entry->fill($fields);
            $entry->save();

            return $this->response->successResponse($entry->toArray());
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    protected function processRequestFields(Request $request): array
    {
        $fields = $request->only($this->model->getFields());

        foreach ($fields as $key => $value) {
            if (in_array($key, ['hobbies', 'profile_picture', 'supporting_document'], true)) {
                $fields[$key] = json_encode($value);
            }
        }

        return $fields;
    }

    private function checkModel()
    {
        if (is_null($this->model)) {
            return $this->response->notFoundResponse();
        }
    }
}

