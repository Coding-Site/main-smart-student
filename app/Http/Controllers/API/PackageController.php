<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return response()->json(['data' => $packages], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'name_en' => 'nullable|string',
            'price' => 'required|integer',
            'selling_price' => 'required|integer',
            'expiry_date' => 'nullable|date|after:today',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $package = new Package();
        $package->fill($request->only(['name', 'name_en','price','selling_price','expiry_date']));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/packages', $image, $imageName);
            $package->image = $imageName;
        }
        $package->save();
        return response()->json(['data' => $package, 'message' => 'package added successfully'], 200);
    }

    public function show($id)
    {
        try {
            $package = Package::findOrFail($id);
            return response()->json(['data' => $package], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'package not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'price' => 'required|integer',
            'selling_price' => 'required|integer',
            'expiry_date' => 'nullable|date|after:today',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $package = Package::findOrFail($id);
            $package->fill($request->all());
            if ($request->hasFile('image')) {
                if ($package->image) {
                    Storage::delete('public/packages/'. $package->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $package->image = $imageName;
                Storage::putFileAs('public/packages', $image, $imageName);
            }
            $package->save();
            return response()->json(['success' => 'package updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'package not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            $package->delete();
            return response()->json(['success' => 'package deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'package not found'], 404);
        }
    }
}
