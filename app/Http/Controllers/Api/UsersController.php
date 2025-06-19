<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    private $model;

    protected $reference;

    protected $forms;

    protected $response;

    public function __construct(Request $request, User $model, ResponseService $response)
    {
        try {
            if (file_exists(app_path('Models/' . Str::studly('users')) . '.php')) {
                $this->model = app("App\Models\\" . Str::studly('users'));
            } else {
                if ($model->checkTableExists('users')) {
                    $this->model = $model;
                    $this->model->setTable('users');
                }
            }

            $this->reference = $this->model->getReference();
            $this->forms = $this->model->getForms();
            $this->response = $response;
        } catch (Exception $e) {
            //throw $th;
        }
    }

    public function create(Request $request)
    {
        try {
            $validators = $this->model->validator($request);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }
            $fields = $request->only($this->model->getFields());
            foreach ($fields as $key => $value) {
                $this->model->setAttribute($key, $value);
            }

            $roles = $request['role'];
            if (! empty($oles) || count($roles) < 1) {
                return $this->response->errorResponse(['message' => 'Role is invalid']);
            }

            $this->model['password'] = bcrypt($this->model['password']);
            $this->model->save();

            $userId = $this->model->id;
            $userRoles = array_map(function ($roleName) use ($userId) {
                $role = Roles::where('code', $roleName)->first();

                if (is_null($role)) {
                    return;
                }

                $roleId = $role['id'];
                $entry = [
                    'users_id' => $userId,
                    'roles_id' => $roleId,
                ];
                RoleUsers::create($entry);

                return $roleId;
            }, $roles);
            unset($this->model['password']);
            $this->model['role'] = $userRoles;

            return $this->response->successResponse($this->model->toArray(), 'Data created.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $entry = User::where('status', 1)->where('id', $id)->first();

            if (! $entry) {
                return $this->response->notFoundResponse();
            }

            $validators = $entry->validator($request);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $roles = $request['role'];
            if (! empty($oles) || count($roles) < 1) {
                return $this->response->errorResponse(['message' => 'Role is invalid']);
            }

            $fields = $request->only($entry->getFields());
            unset($fields['password']);
            $entry->update($fields);
            $existingUsersRoles = RoleUsers::where('users_id', $id)->get();

            if ($existingUsersRoles) {
                $existingUsersRoles = RoleUsers::where('users_id', $id)->delete();
            }

            $userId = $id;
            $role = array_map(function ($roleName) use ($userId) {
                $role = Roles::where('code', $roleName)->first();

                if (is_null($role)) {
                    return;
                }

                $roleId = $role['id'];
                $entryRoleUser = [
                    'users_id' => $userId,
                    'roles_id' => $roleId,
                ];
                RoleUsers::create($entryRoleUser);

                return $roleId;
            }, $roles);
            unset($entry->password);
            $data = $entry->toArray();
            $data['role'] = $role;

            return $this->response->successResponse($data, 'Data created.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    public function read($id)
    {
        $users = User::find($id);
        if (! $users) {
            return response()->json([
                'errors' => [
                    'message' => 'Not Found',
                    'status' => 404,
                ],
            ], 404);
        }

        $roleUsers = RoleUsers::where('users_id', $id)->pluck('roles_id');
        $users['role'] = [];

        if ($roleUsers->count() > 0) {
            $role = Roles::where('id', $roleUsers[0])->pluck('code');
            if ($role->count() > 0) {
                $roleStr = strval($role[0]);
                $users['role'] = [$roleStr];
            }
        }

        return $this->response->successResponse($users->toArray(), 'Data retrieved');
    }
}
