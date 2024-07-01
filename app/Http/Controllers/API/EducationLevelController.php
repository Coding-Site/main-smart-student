<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EducationLevel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EducationLevelController extends Controller
{
    public function index()
    {
        $educationLevels = EducationLevel::all();
        return response()->json(['data' => $educationLevels], 200);
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
        $educationLevel = new EducationLevel();
        $educationLevel->fill($request->only(['name', 'name_en']));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/levels', $image, $imageName);
            $educationLevel->image = $imageName;
        }
        $educationLevel->save();
        return response()->json(['data' => $educationLevel, 'message' => 'educationLevel added successfully'], 200);
    }

    public function show($id)
    {
        try {
            $educationLevel = EducationLevel::findOrFail($id);
            return response()->json(['data' => $educationLevel], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'educationLevel not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'name_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $educationLevel = EducationLevel::findOrFail($id);
            $educationLevel->fill($request->all());
            if ($request->hasFile('image')) {
                if ($educationLevel->image) {
                    Storage::delete('public/levels/'. $educationLevel->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $educationLevel->image = $imageName;
                Storage::putFileAs('public/levels', $image, $imageName);
            }
            $educationLevel->save();
            return response()->json(['data' => $educationLevel, 'message' =>'educationLevel updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'educationLevel not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $educationLevel = EducationLevel::findOrFail($id);
            $educationLevel->delete();
            return response()->json(['message' => 'educationLevel deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'educationLevel not found'], 404);
        }
    }
}
