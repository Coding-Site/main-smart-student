<?php

namespace App\Http\Controllers\API;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Rules\MaterialIdExists;
use App\Rules\ClassroomIdExists;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::all();
        return response()->json(['data' => $banks], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:First Semester,Second Semester',
            'unsolved' => 'nullable|mimes:pdf',
            'solved' => 'nullable|mimes:pdf',
            'book' => 'nullable|mimes:pdf',
            'material_id' => ['required', 'integer', new MaterialIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {
            $unsolved = '';
            $solved = '';
            $book = '';

            if ($request->hasAny(['unsolved', 'book', 'solved'])) {
                foreach ($request->allFiles() as $pdf => $file) {
                    $filename = $file->getClientOriginalName();
                    Storage::putFileAs('public/banks', $file, $filename);
                    $$pdf = $filename;
                }
            } else {
                return response()->json(['error' => 'you have upload minimum one file'], 422);
            }

            $course = Bank::create([
                'semester' => $request->semester,
                'unsolved' => $unsolved,
                'solved' => $solved,
                'book' => $book,
                'material_id' => $request->material_id,
                'classroom_id' => $request->classroom_id
            ]);
            return response()->json(['data' => $course, 'message' => 'Bank added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating banks ' . $e->getMessage()], 500);
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
            $bank = Bank::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'semester' => 'required|in:First Semester,Second Semester',
                'unsolved' => 'nullable|mimes:pdf',
                'solved' => 'nullable|mimes:pdf',
                'book' => 'nullable|mimes:pdf',
                'material_id' => ['required', 'integer', new MaterialIdExists],
                'classroom_id' => ['required', 'integer', new ClassroomIdExists]
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 422);
            }


            $unsolved = false;
            $solved = false;
            $book = false;


            if ($request->hasAny(['unsolved', 'book', 'solved'])) {

                foreach ($request->allFiles() as $pdf => $file) {

                    Storage::delete('public/banks/' . $bank[$pdf]);

                    $filename = $file->getClientOriginalName();
                    Storage::putFileAs('public/banks', $file, $filename);
                    $$pdf = $filename;
                }
            } else {
                return response()->json(['error' => 'you have upload minimum one file'], 422);
            }


            $bank->semester = $request->semester;
            $unsolved ? $bank->unsolved = $unsolved : '';
            $solved ? $bank->solved = $solved : '';
            $book ? $bank->book = $book : '';
            $bank->material_id = $request->material_id;
            $bank->classroom_id = $request->classroom_id;
            $bank->save();

            return response()->json(['data' => $bank, 'message' => 'Bank added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating banks'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $bank = Bank::findOrFail($id);
            $pdfs = ['unsolved', 'solved', 'book'];
            foreach ($pdfs as $pdf) {
                Storage::delete('public/banks/' . $bank[$pdf]);
            }
            $bank->delete();
            return response()->json(['message' => 'bank deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'this bank dosn\'t exists'], 500);
        }
    }
}
