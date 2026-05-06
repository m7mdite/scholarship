<?php

namespace App\Http\Controllers\Api;

use App\Models\Scholarship;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::with(['country', 'city', 'specialization', 'category'])->get();
        return response()->json($scholarships);
    }

    public function store(Request $request)
    {
        // $validated = $request->validate([...]);
        $scholarship = Scholarship::create($validated);
        return response()->json($scholarship, 201);
    }

    public function show($id)
    {
        $scholarship = Scholarship::with([...])->findOrFail($id);
        return response()->json($scholarship);
    }

    public function update(Request $request, $id)
    {
        $scholarship = Scholarship::findOrFail($id);
        $validated = $request->validate([...]);
        $scholarship->update($validated);
        return response()->json($scholarship);
    }

    public function destroy($id)
    {
        Scholarship::destroy($id);
        return response()->json(null, 204);
    }
}