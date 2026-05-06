<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Scholarship;
use App\Models\Specialization;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    // =======================================================================
    public function create()
    {
        $countries = Country::all();
        $cities = City::all();
        $specializations = Specialization::all();
        $categories = Category::all();
        return view('scholarships.create', compact('countries', 'cities', 'specializations', 'categories'));
    }

    // ========================================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scholarship_name' => 'required|string|max:50',
            'degree' => 'required|string|max:40',
            'finance' => 'nullable|string|max:40',
            'scholarship_description' => 'nullable|string|max:500',
            'donar' => 'nullable|string|max:40',
            'start_date' => 'nullable|date',
            'finished_date' => 'nullable|date',
            'scholarship_created_at' => 'nullable|date',
            'scholarship_language' => 'nullable|string|max:30',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'specialization_id' => 'required|exists:specializations,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        Scholarship::create($validated);
        return redirect()->route('scholarships.index')->with('success', 'تمت إضافة المنحة بنجاح');
    }

    // =======================================================================
    public function index()
    {
        $scholarships = Scholarship::with(['country', 'city', 'specialization', 'category'])->get();
        return view('scholarships.index', compact('scholarships'));
    }


    // =======================================
    public function show($id)
    {
        $scholarship = Scholarship::with(['country', 'city', 'specialization', 'category', 'howToApply', 'applicationCriteria', 'photos',
         'personalExperiences',
        ],)->findOrFail($id);
        return view('scholarships.show', compact('scholarship'));
    }
}
