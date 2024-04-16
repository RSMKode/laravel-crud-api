<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Student;

class StudentController extends Controller
{
    protected $validLanguages = ['es-ES', 'en-GB', 'en-US', 'fr-FR', 'de-DE', 'it-IT', 'pt-PT',];

    public function index()
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            $data = [
                'message' => 'No students found',
                'status' => 200
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'message' => 'Students found',
            'students' => $students,
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }

    public function show($id)
    {
        $student = Student::find($id);

        if (!$student) {
            $data = [
                'message' => 'Student not found',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'message' => 'Student found',
            'student' => $student,
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|digits:9',
            'language' => 'required|string|in:' . implode(',', $this->validLanguages) . '|max:5'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        $student = Student::create(
            $request->only(['name', 'email', 'phone', 'language'])
        );

        if (!$student) {
            $data = [
                'message' => 'Error creating student',
                'status' => 500
            ];
            return response()->json($data, $data['status']);
        }

        $data = [
            'message' => 'Student created',
            'student' => $student,
            'status' => 201
        ];

        return response()->json($data, $data['status']);
    }

    public function update(Request $request, $id)
    {
        $httpMethod = $request->method();

        if ($httpMethod === 'PATCH') {
            return $this->updatePartial($request, $id);
        }

        $student = Student::find($id);

        if (!$student) {
            $data = [
                'message' => 'Student not found',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $id,
            'phone' => 'required|digits:9',
            'language' => 'required|string|in:' . implode(',', $this->validLanguages) . '|max:5'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        $student->name = $request->name;
        $student->email = $request->email;
        $student->phone = $request->phone;
        $student->language = $request->language;

        $student->save();

        // $student = Student::find($id);

        $data = [
            'message' => 'Student updated',
            'student' => $student,
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }

    public function updatePartial(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            $data = [
                'message' => 'Student not found',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|unique:students,email,' . $id,
            'phone' => 'digits:9',
            'language' => 'string|in:' . implode(',', $this->validLanguages) . '|max:5'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, $data['status']);
        }

        if ($request->has('name')) {
            $student->name = $request->name;
        }
        if ($request->has('email')) {
            $student->email = $request->email;
        }
        if ($request->has('phone')) {
            $student->phone = $request->phone;
        }
        if ($request->has('language')) {
            $student->language = $request->language;
        }

        $student->save();

        // $student = Student::find($id);

        $data = [
            'message' => 'Student updated partially, fields updated: ' . implode(', ', array_keys($request->all())),
            'student' => $student,
            'status' => 200
        ];

        return response()->json($data, $data['status']);
    }

    public function destroy($id)
    {
        $student = Student::find($id);

        if (!$student) {
            $data = [
                'message' => 'Student not found',
                'status' => 404
            ];
            return response()->json($data, $data['status']);
        }

        $student->delete();

        $data = [
            'message' => 'Student deleted',
            'student' => $student,
            'status' => 200
        ];

        return response()->json($data, $data['status']);


        return $student;
    }
}
