<?php

namespace App\Http\Controllers\Api;

use App\Models\RolePrivileges;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesController extends ApiResourcesController
{
    public function create(Request $request): JsonResponse
    {
        if (! $request->code) {
            $request['code'] = Str::uuid()->toString();
        }

        try {
            $validators = $this->model->validator($request);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }
            $fields = $request->only($this->model->getFields());
            foreach ($fields as $key => $value) {
                $this->model->setAttribute($key, $value);
            }
            $this->model->save();

            $roleId = $this->model->id;
            $privileges = $request->privileges;

            $rolePrivileges = array_map(function ($item) use ($roleId) {
                return [
                    'role' => $roleId,
                    'uri' => $item['uri'],
                    'method' => $item['method'],
                    'action' => isset($item['action']) ? $item['action'] : '',
                ];
            }, $privileges);

            DB::table('role_privileges')->insert($rolePrivileges);

            return $this->response->successResponse($this->model->toArray(), 'Data created.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function read(Request $request): JsonResponse
    {
        try {
            $entry = $this->model->statusActive()->find($this->id);

            if (! $entry) {
                return $this->response->notFoundResponse();
            }

            $privileges = RolePrivileges::whereRole($this->id)->get();
            $mappings = array_map(function ($entry) {
                return $entry['uri'] . '|' . $entry['method'];
            }, $privileges->toArray());

            $entry['privilege'] = $mappings;

            return $this->response->successResponse($entry->toArray());
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
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

            $roleId = $entry->id;
            $existingPrivilege = RolePrivileges::whereRole($roleId)->pluck('id');
            RolePrivileges::whereIn('id', $existingPrivilege)->delete();

            $privileges = $request->privileges;
            $entry['privileges'] = $rolePrivileges = array_map(function ($item) use ($roleId) {
                return [
                    'role' => $roleId,
                    'uri' => $item['uri'],
                    'method' => $item['method'],
                    'action' => isset($item['action']) ? $item['action'] : '',
                ];
            }, $privileges);

            DB::table('role_privileges')->insert($rolePrivileges);

            return $this->response->successResponse($entry->toArray(), 'Data Updated.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }
}
