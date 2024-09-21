<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends BaseController
{
    public function show()
    {
        $user = Auth::user();

        $customer = Customer::where('customer_id', $user->id)->first();

        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }

        return $this->sendResponse($customer, 'Customer retrieved successfully.');
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
            'address' => 'sometimes|string|max:255'
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

        $customer = Customer::where('customer_id', $user->id)->first();
        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }

        if ($request->has('company_name')) {
            $customer->company_name = $input['company_name'];
        }
        if ($request->has('contact')) {
            $customer->contact = $input['contact'];
        }
        if ($request->has('address')) {
            $customer->address = $input['address'];
        }

        $customer->save();

        $response = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
                'id' => $user->id,
            ],
            'customer' => [
                'customer_id' => $customer->customer_id,
                'company_name' => $customer->company_name,
                'contact' => $customer->contact,
                'address' => $customer->address,
                'description' => $customer->description,
                'updated_at' => $customer->updated_at,
                'created_at' => $customer->created_at,
            ]
        ];

        return $this->sendResponse($response, 'Customer and user updated successfully.');
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
        $customer = Customer::where('customer_id', $user->id)->first();

        if (!$customer) {
            return $this->sendError('Customer not found.', [], 404);
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'customer_' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/customer_photos', $filename);

            if ($customer->photo) {
                Storage::delete($customer->photo);
            }

            $customer->photo = $path;
        }

        $customer->save();

        return $this->sendResponse(['photo' => $customer->photo], 'Photo uploaded successfully.');
    }
}
