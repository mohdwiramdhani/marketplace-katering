<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        $menus = Menu::where('merchant_id', $merchant->merchant_id)->get();

        return $this->sendResponse(['menus' => $menus], 'Menus retrieved successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        $menu = Menu::where('id', $id)->where('merchant_id', $merchant->merchant_id)->first();

        if (!$menu) {
            return $this->sendError('Menu not found.', [], 404);
        }

        return $this->sendResponse(['menu' => $menu], 'Menu retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|in:food,drink'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        $menu = new Menu();
        $menu->name = $request->input('name');
        $menu->quantity = $request->input('quantity');
        $menu->price = $request->input('price');
        $menu->description = $request->input('description');
        $menu->merchant_id = $merchant->merchant_id;
        $menu->type = $request->input('type');

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'menu_' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/menu_photos', $filename);
            $menu->photo = $path;
        }

        $menu->save();

        return $this->sendResponse(['menu' => $menu], 'Menu created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'sometimes|required|in:food,drink'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        $menu = Menu::where('id', $id)->where('merchant_id', $merchant->merchant_id)->first();

        if (!$menu) {
            return $this->sendError('Menu not found.', [], 404);
        }

        $menu->name = $request->input('name', $menu->name);
        $menu->quantity = $request->input('quantity', $menu->quantity);
        $menu->price = $request->input('price', $menu->price);
        $menu->description = $request->input('description', $menu->description);
        $menu->type = $request->input('type', $menu->type);

        if ($request->hasFile('photo')) {
            if ($menu->photo) {
                Storage::delete($menu->photo);
            }

            $photo = $request->file('photo');
            $filename = 'menu_' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/menu_photos', $filename);
            $menu->photo = $path;
        }

        $menu->save();

        return $this->sendResponse(['menu' => $menu], 'Menu updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $merchant = Merchant::where('merchant_id', $user->id)->first();

        if (!$merchant) {
            return $this->sendError('Merchant not found.', [], 404);
        }

        $menu = Menu::where('id', $id)->where('merchant_id', $merchant->merchant_id)->first();

        if (!$menu) {
            return $this->sendError('Menu not found.', [], 404);
        }

        if ($menu->photo) {
            Storage::delete($menu->photo);
        }

        $menu->delete();

        return $this->sendResponse([], 'Menu deleted successfully.');
    }

    public function allMenus()
    {
        $menus = Menu::all();

        return $this->sendResponse(['menus' => $menus], 'All menus retrieved successfully.');
    }

    public function menuDetail($id)
    {
        $menu = Menu::with('merchant')->find($id);

        if (!$menu) {
            return $this->sendError('Menu not found.', [], 404);
        }

        return $this->sendResponse([
            'menu' => $menu,
            'merchant_name' => $menu->merchant ? $menu->merchant->name : null,
        ], 'Menu details retrieved successfully.');
    }


    public function search(Request $request)
    {
        $type = $request->input('type');

        $query = Menu::query();

        if ($type) {
            $query->where('type', $type);
        }

        $menus = $query->get();

        return $this->sendResponse(['menus' => $menus], 'Menus retrieved successfully.');
    }
}
