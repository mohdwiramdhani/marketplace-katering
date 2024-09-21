<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:customer,merchant',
            'company_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if ($user->role === 'merchant') {
            Merchant::create([
                'merchant_id' => $user->id,
                'company_name' => $input['company_name'],
                'contact' => $input['contact'],
                'address' => $input['address'],
            ]);
        } else {
            Customer::create([
                'customer_id' => $user->id,
                'company_name' => $input['company_name'],
                'contact' => $input['contact'],
                'address' => $input['address']
            ]);
        }

        return $this->sendResponse([], 'User registered successfully.');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'role' => auth('api')->user()->role,
            'token' => $token
        ]);
    }

    public function getUser()
    {
        $user = auth('api')->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }
}
