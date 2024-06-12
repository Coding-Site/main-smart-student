<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::all();
        return response()->json(['data' => $materials], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'name_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $material = new Material();
        $material->fill($request->only(['name', 'name_en']));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/materials', $image, $imageName);
            $material->image = $imageName;
        }
        $material->save();
        return response()->json(['data' => $material, 'message' => 'material added successfully'], 200);
    }

    public function show($id)
    {
        try {
            $material = Material::findOrFail($id);
            return response()->json(['data' => $material], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'material not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update($request->all());
            return response()->json(['success' => 'material updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'material not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->delete();
            return response()->json(['success' => 'material deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'material not found'], 404);
        }
    }
}
