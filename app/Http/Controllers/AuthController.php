<?php

namespace App\Http\Controllers;

use App\Models\RolePrivileges;
use App\Models\User;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $response;

    protected $messages = [
        'required' => 'The :attribute field is required.',
        'unique' => 'The :attribute field is unique.',
        'same' => 'The :attribute and :other must match.',
        'size' => 'The :attribute must be exactly :size.',
        'between' => 'The :attribute value :input is not between :min - :max.',
        'in' => 'The :attribute must be one of the following types: :values',
    ];

    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request, User $model): JsonResponse
    {
        try {
            $rules = [
                'first_name' => 'required|string',
                'last_name' => 'string',
                'username' => 'required|string|unique:users',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
                'gender' => 'nullable|integer',
                'phone_number' => 'nullable|string',
            ];

            $validators = Validator::make($request->all(), $rules);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $request->merge(['channel' => 'registration']);
            $fields = $request->only($model->getTableFields());
            foreach ($fields as $key => $value) {
                if ($key === 'password') {
                    $model->setAttribute($key, Hash::make($value));

                    continue;
                }
                $model->setAttribute($key, $value);
            }
            $model->save();

            return $this
                ->response
                ->response(
                    [$model],
                    Str::title('You\'re registered!'),
                    201
                );
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $rules = [
                'username' => 'required',
                'password' => 'required|string',
                'remember_me' => 'boolean|nullable',
            ];

            $validators = Validator::make($request->all(), $rules);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $credentials = request(['username', 'password']);
            $auth = Auth::attempt($credentials);
            if (! $auth) {
                return $this
                    ->response
                    ->unauthorizedResponse(
                        'Invalid user or password'
                    );
            }

            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');

            $tokenExpireMinutes = env('PASSPORT_TOKEN_EXPIRE_MINUTES', 15);

            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addMinutes($tokenExpireMinutes);
            $token->save();

            $roles = [];
            $permissions = [];
            if (isset($user['roles'])) {
                $user['role'] = array_map(function ($role) {
                    return $role['roles_id']['name'];
                }, $user['roles']->toArray());

                foreach ($user['roles']->toArray() as $role) {
                    $rolePrivileges = RolePrivileges::where('role', $role['id'])->get();
                    foreach ($rolePrivileges as $rolePrivelege) {
                        array_push($permissions, $rolePrivelege->toArray());
                    }
                }
            }
            $roles['privileges'] = $permissions;
            $roles['role'] = [$role];

            unset($user['password']);

            $data = [
                'user' => $user,
                'role' => $roles,
                'access_token' => $tokenResult->accessToken,
            ];

            return response()->json($data, Response::HTTP_OK);
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Refresh Token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $currentToken = $request->user()->token();
            $currentTokenExpiry = Carbon::parse($currentToken->expires_at);

            if ($currentTokenExpiry->greaterThanOrEqualTo(Carbon::now())) {
                $currentToken->revoke();

                $tokenExpireMinutes = env('PASSPORT_TOKEN_EXPIRE_MINUTES', 15);

                $tokenResult = $request->user()->createToken('Personal Access Token');
                $token = $tokenResult->token;
                $token->expires_at = Carbon::now()->addMinutes($tokenExpireMinutes);
                $token->save();

                $data = [
                    'access_token' => $tokenResult->accessToken,
                ];

                return response()->json($data, Response::HTTP_OK);
            }

            return $this->response->unauthorizedResponse('Token Expired');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();

            return $this->response->successResponse([], 'Successfully logged out.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $rules = [
                'email' => 'required|email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'dob' => 'required|date',
                'gender' => 'required|string',
                'phone_number' => 'required|string',
                'address' => 'nullable|array',
            ];

            $validators = Validator::make($request->all(), $rules);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $user = Auth::user();
            $fields = $request->only([
                'email',
                'first_name',
                'last_name',
                'dob',
                'gender',
                'phone_number',
            ]);
            foreach ($fields as $key => $value) {
                $user->setAttribute($key, $value);
            }
            $user->save();

            return $this->response->successResponse(
                [
                    'user' => $user,
                ],
                'User updated.'
            );
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $rules = [
                'password' => 'required|string|confirmed',
                'password_confirmation' => 'required_with:password|same:password|min:6',
            ];

            $validators = Validator::make($request->all(), $rules);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $user = Auth::user();
            $user->password = bcrypt($request->password);
            $user->save();

            return $this->response->successResponse([
                'user' => $user,
                'Password changed.',
            ]);
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $rules = [
                'email' => 'required|email',
            ];

            $validators = Validator::make($request->all(), $rules, $this->messages);
            if ($validators->fails()) {
                return $this->validatorErrors($validators);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            $status == Password::RESET_LINK_SENT ? true : false;
            if (! $status) {
                return $this->response->errorResponse([], 'Cannot send to email.', 400);
            }

            return $this->response->successResponse([], 'Reset link sended.');
        } catch (Exception $error) {
            return $this->generalError($error);
        }
    }
}
