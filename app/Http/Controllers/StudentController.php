<?php


namespace App\Http\Controllers;


use App\Models\Student;
use Illuminate\Http\Request;


class StudentController extends Controller
{
    // GET /api/students — return all students
    public function index()
    {
        $students = Student::all();
        return response()->json([
            'success' => true,
            'data'    => $students,
        ], 200);
    }


    // POST /api/students — create a new student
    public function store(Request $request)
    {
        $student = Student::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data'    => $student,
        ], 201);
    }


    // GET /api/students/{id} — return one student
    public function show(string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }
        return response()->json(['success' => true, 'data' => $student], 200);
    }


    // PUT /api/students/{id} — update a student
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }
        $student->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data'    => $student,
        ], 200);
    }


    // DELETE /api/students/{id} — delete a student
    public function destroy(string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }
        $student->delete();
        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
        ], 200);
    }
}

