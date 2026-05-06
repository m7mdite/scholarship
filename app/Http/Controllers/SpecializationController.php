<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Category;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::with('category')->get();
        return view('specializations.index', compact('specializations'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('specializations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialization_name' => 'required|string|max:30',
            'category_id' => 'required|exists:categories,id',
        ]);

        Specialization::create($request->all());
        return redirect()->route('specializations.index')->with('success', 'تمت إضافة التخصص بنجاح');
    }

    public function show(Specialization $specialization)
    {
        return view('specializations.show', compact('specialization'));
    }

    public function edit(Specialization $specialization)
    {
        $categories = Category::all();
        return view('specializations.edit', compact('specialization', 'categories'));
    }

    public function update(Request $request, Specialization $specialization)
    {
        $request->validate([
            'specialization_name' => 'required|string|max:30',
            'category_id' => 'required|exists:categories,id',
        ]);

        $specialization->update($request->all());
        return redirect()->route('specializations.index')->with('success', 'تم تحديث التخصص بنجاح');
    }

    public function destroy(Specialization $specialization)
    {
        $specialization->delete();
        return redirect()->route('specializations.index')->with('success', 'تم حذف التخصص بنجاح');
    }
}