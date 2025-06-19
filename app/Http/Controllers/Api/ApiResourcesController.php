<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resources;
use App\Services\Report\ReportServiceFactory;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiResourcesController extends Controller
{
    protected $table_name = null;

    protected $model = null;

    protected $segments = [];

    protected $collection = null;

    protected $id = null;

    protected $action = null;

    protected $response = null;

    protected $report = null;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Request $request, Resources $model, ResponseService $response, ReportServiceFactory $report)
    {
        try {
            $this->response = $response;
            $this->report = $report;
            $this->collection = $request->segment(3);
            $this->id = $request->segment(4);
            $this->action = $request->segment(5);

            if (file_exists(app_path('Models/' . Str::studly($this->collection)) . '.php')) {
                $this->model = app("App\Models\\" . Str::studly($this->collection));
            } else {
                if ($model->checkTableExists($this->collection)) {
                    $this->model = $model;
                    $this->model->setTable($this->collection);
                }
            }

            if (is_null($this->table_name)) {
                $this->table_name = $this->collection;
            }
            $this->segments = $request->segments();
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $entries = $this->model
                ->filter($request)
                ->orderFilter($request);

            $entriesCounted = $entries->count();
            $entriesData = $entries->paginateFilter($request)->get();

            $data = [
                'result' => $entriesData,
                'count' => $entriesCounted,
            ];

            return $this->response->successResponse($data);
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Display a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $entry = $this->model->statusActive()->find($this->id);

            if (! $entry) {
                return $this->response->notFoundResponse();
            }

            return $this->response->successResponse($entry->toArray());
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Store a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $validators = $this->model->validator($request);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }
            $fields = $request->only($this->model->getFields());
            foreach ($fields as $key => $value) {
                $this->model->setAttribute($key, $value);
            }
            $this->model->save();

            return $this->response->response($this->model->toArray(), 'Data created.', 201);
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Update a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
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
            $fields = $request->only($this->model->getFields());
            foreach ($fields as $key => $value) {
                $entry->setAttribute($key, $value);
            }
            $entry->save();

            return $this->response->successResponse($entry->toArray());
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Soft Delete a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
    public function softDelete(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $ids = explode(',', $this->id);

            if (count($ids) === 1) {
                $entry = $this->model->statusActive()->find($this->id);

                if (! $entry) {
                    return $this->response->notFoundResponse();
                }

                $entry->setAttribute('status', false);
                $entry->save();

                return $this->response->successResponse($entry->toArray());
            }

            if (count($ids) > 1) {
                $entries = [];
                foreach ($ids as $id) {
                    $entry = $this->model->statusActive()->find($id);

                    if ($entry) {
                        $entry->setAttribute('status', false);
                        $entry->save();
                        array_push($entries, $entry);
                    }
                }

                return $this->response->successResponse(
                    [
                        'data' => $entries,
                    ],
                    'Data deleted.'
                );
            }

            return $this->response->notFoundResponse();
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Hard Delete a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
    public function hardDelete(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $ids = explode(',', $this->id);

            if (count($ids) === 1) {
                $entry = $this->model->find($this->id);

                if (! $entry) {
                    return $this->response->notFoundResponse();
                }

                $entry->delete();

                return $this->response->successResponse($entry->toArray());
            }

            if (count($ids) > 1) {
                $entries = [];
                foreach ($ids as $id) {
                    $entry = $this->model->find($id);

                    if ($entry) {
                        $entry->delete();
                        array_push($entries, $entries);
                    }
                }

                return $this->response->successResponse(
                    [
                        'data' => $entries,
                    ],
                    'Data deleted.'
                );
            }

            return $this->response->notFoundResponse();
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * restore Deleted a data of the resoure.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request): JsonResponse
    {
        try {
            $this->checkModel();
            $ids = explode(',', $this->id);

            if (count($ids) === 1) {
                $entry = $this->model->statusInactive()->find($this->id);

                if (! $entry) {
                    return $this->response->notFoundResponse();
                }

                $entry->setAttribute('status', true);
                $entry->save();

                return $this->response->successResponse($entry->toArray());
            }

            if (count($ids) > 1) {
                $entries = [];
                foreach ($ids as $id) {
                    $entry = $this->model->statusInactive()->find($id);

                    if ($entry) {
                        $entry->setAttribute('status', true);
                        $entry->save();
                        array_push($entries, $entries);
                    }
                }

                return $this->response->successResponse(
                    [
                        'data' => $entries,
                    ],
                    'Data restored.'
                );
            }

            return $this->response->notFoundResponse();
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function export(Request $request)
    {
        try {
            $this->checkModel();
            $type = $request->get('report_type');
            $reportService = $this->report->create($type);
            unset($request['report_type']);
            $entries = $this->model
                ->filter($request)
                ->orderFilter($request)
                ->get();
            $headers = $this->model->getFields();

            return $reportService->export($headers, $entries->toArray());
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    private function checkModel()
    {
        if (is_null($this->model)) {
            return $this->response->notFoundResponse();
        }
    }
}
