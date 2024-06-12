<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
        return response()->json(['data' => $classrooms], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'name_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $classroom = new Classroom();
        $classroom->fill($request->only(['name', 'name_en']));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/classrooms', $image, $imageName);
            $classroom->image = $imageName;
        }
        $classroom->save();
        return response()->json(['data' => $classroom, 'message' => 'classroom added successfully'], 200);
    }

    public function show($id)
    {
        try {
            $classroom = Classroom::findOrFail($id);
            return response()->json(['data' => $classroom], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $classroom = Classroom::findOrFail($id);
            $classroom->update($request->all());
            return response()->json(['success' => 'Classroom updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $classroom = Classroom::findOrFail($id);
            $classroom->delete();
            return response()->json(['success' => 'Classroom deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Classroom not found'], 404);
        }
    }
}
