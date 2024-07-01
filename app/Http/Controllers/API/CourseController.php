<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Rules\TeacherIdExists;
use App\Rules\MaterialIdExists;
use App\Rules\ClassroomIdExists;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->json(['data' => $courses], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:paid,free',
            'semester' => 'required|in:First Semester,Second Semester',
            'expire_date' => 'nullable|date',
            'semester_price' => 'required|integer|min:0',
            'month_price' => 'required|integer|min:0',
            'material_id' => ['required', 'integer', new MaterialIdExists],
            'teacher_id' => ['required', 'integer', new TeacherIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $course = Course::create([
                'type' => $request->type,
                'semester' => $request->semester,
                'expire_date' => $request->expire_date,
                'semester_price' => $request->semester_price,
                'month_price' => $request->month_price,
                'material_id' => $request->material_id,
                'teacher_id' => $request->teacher_id,
                'classroom_id' => $request->classroom_id,
            ]);
            return response()->json(['data' => $course, 'message' => 'Course added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating course'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            return response()->json(['data' => $course], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type' => 'required|in:paid,free',
                'semester' => 'required|in:First Semester,Second Semester',
                'expire_date' => 'nullable|date',
                'semester_price' => 'required|integer|min:0',
                'month_price' => 'required|integer|min:0',
                'material_id' => ['required', 'integer', new MaterialIdExists],
                'teacher_id' => ['required', 'integer', new TeacherIdExists],
                'classroom_id' => ['required', 'integer', new ClassroomIdExists]
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 422);
            }

            $course->type = $request->type;
            $course->semester = $request->semester;
            $course->expire_date = $request->expire_date;
            $course->semester_price = $request->semester_price;
            $course->month_price = $request->month_price;
            $course->material_id = $request->material_id;
            $course->teacher_id = $request->teacher_id;
            $course->classroom_id = $request->classroom_id;

            $course->save();

            return response()->json(['data' => $course, 'message' => 'Course updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Course::findOrFail($id)->delete();
            return response()->json(['message' => 'Course deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Course not found'], 404);
        }
    }
}
