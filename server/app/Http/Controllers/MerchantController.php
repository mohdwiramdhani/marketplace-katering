<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MerchantController extends BaseController
{
    public function show()
    {
        $user = Auth::user();

        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        return $this->sendResponse($merchant, 'Merchant retrieved successfully.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'company_name' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input = $request->all();

        if ($request->has('name')) {
            $user->name = $input['name'];
        }
        if ($request->has('email')) {
            $user->email = $input['email'];
        }
        if ($request->has('password')) {
            $user->password = bcrypt($input['password']);
        }
        $user->save();

        $merchant = Merchant::where('merchant_id', $user->id)->first();
        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        if ($request->has('company_name')) {
            $merchant->company_name = $input['company_name'];
        }
        if ($request->has('contact')) {
            $merchant->contact = $input['contact'];
        }
        if ($request->has('address')) {
            $merchant->address = $input['address'];
        }
        if ($request->has('description')) {
            $merchant->description = $input['description'];
        }

        $merchant->save();

        $response = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
                'id' => $user->id,
            ],
            'merchant' => [
                'merchant_id' => $merchant->merchant_id,
                'company_name' => $merchant->company_name,
                'contact' => $merchant->contact,
                'address' => $merchant->address,
                'description' => $merchant->description,
                'updated_at' => $merchant->updated_at,
                'created_at' => $merchant->created_at,
            ]
        ];

        return $this->sendResponse($response, 'Merchant and user updated successfully.');
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'merchant_' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/merchant_photos', $filename);

            if ($merchant->photo) {
                Storage::delete($merchant->photo);
            }

            $merchant->photo = $path;
        }

        $merchant->save();

        return $this->sendResponse(['photo' => $merchant->photo], 'Photo uploaded successfully.');
    }

    public function index()
    {
        $merchants = Merchant::all();

        return $this->sendResponse($merchants, 'All merchants retrieved successfully.');
    }

    public function showDetail($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        return $this->sendResponse($merchant, 'Merchant detail retrieved successfully.');
    }
}
