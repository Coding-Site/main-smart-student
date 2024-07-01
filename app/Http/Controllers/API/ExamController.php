<?php

namespace App\Http\Controllers\API;

use App\Models\Exam;
use Illuminate\Http\Request;
use App\Rules\MaterialIdExists;
use App\Rules\ClassroomIdExists;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exam = Exam::all();
        return response()->json(['data' => $exam], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:First Semester,Second Semester',
            'first' => 'nullable|mimes:pdf',
            'second' => 'nullable|mimes:pdf',
            'unsolved' => 'nullable|mimes:pdf',
            'solved' => 'nullable|mimes:pdf',
            'final' => 'nullable|mimes:pdf',
            'material_id' => ['required', 'integer', new MaterialIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {
            $first = '';
            $second = '';
            $unsolved = '';
            $solved = '';
            $final = '';

            if ($request->hasAny(['first', 'second', 'unsolved', 'solved', 'final'])) {
                foreach ($request->allFiles() as $pdf => $file) {
                    $filename = $file->getClientOriginalName();
                    Storage::putFileAs('public/exams', $file, $filename);
                    $$pdf = $filename;
                }
            } else {
                return response()->json(['error' => 'you have upload minimum one file'], 422);
            }

            $course = Exam::create([
                'semester' => $request->semester,
                'first' => $first,
                'second' => $second,
                'unsolved' => $unsolved,
                'solved' => $solved,
                'final' => $final,
                'material_id' => $request->material_id,
                'classroom_id' => $request->classroom_id
            ]);

            return response()->json(['data' => $course, 'message' => 'Exam added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating exams ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $exam = Exam::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'semester' => 'required|in:First Semester,Second Semester',
                'first' => 'nullable|mimes:pdf',
                'second' => 'nullable|mimes:pdf',
                'unsolved' => 'nullable|mimes:pdf',
                'solved' => 'nullable|mimes:pdf',
                'final' => 'nullable|mimes:pdf',
                'material_id' => ['required', 'integer', new MaterialIdExists],
                'classroom_id' => ['required', 'integer', new ClassroomIdExists]
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 422);
            }


            $first = false;
            $second = false;
            $unsolved = false;
            $solved = false;
            $final = false;


            if ($request->hasAny(['first', 'second', 'unsolved', 'solved', 'final'])) {

                foreach ($request->allFiles() as $pdf => $file) {

                    Storage::delete('public/exams/' . $exam[$pdf]);

                    $filename = $file->getClientOriginalName();
                    Storage::putFileAs('public/exams', $file, $filename);
                    $$pdf = $filename;
                }
            } else {
                return response()->json(['error' => 'you have upload minimum one file'], 422);
            }


            $exam->semester = $request->semester;
            $first ? $exam->first = $first : '';
            $second ? $exam->second = $second : '';
            $unsolved ? $exam->unsolved = $unsolved : '';
            $solved ? $exam->solved = $solved : '';
            $final ? $exam->final = $final : '';
            $exam->material_id = $request->material_id;
            $exam->classroom_id = $request->classroom_id;
            $exam->save();

            return response()->json(['data' => $exam, 'message' => 'Exam updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating exam'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $pdfs = ['first', 'second', 'unsolved', 'solved', 'final'];
            foreach ($pdfs as $pdf) {
                Storage::delete('public/exams/' . $exam[$pdf]);
            }
            $exam->delete();
            return response()->json(['message' => 'Exam deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'this exam dosn\'t exists'], 500);
        }
    }
}
