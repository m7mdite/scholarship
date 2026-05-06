<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return view('countries.index', compact('countries'));
    }

    public function create()
    {
        return view('countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:30',
            'country_rate' => 'required|numeric',
        ]);
        Country::create($request->all());
        return redirect()->route('countries.index')->with('success', 'تمت الإضافة');
    }

    public function show(Country $country)
    {
        return view('countries.show', compact('country'));
    }

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'country_name' => 'required|string|max:30',
            'country_rate' => 'required|numeric',
        ]);
        $country->update($request->all());
        return redirect()->route('countries.index')->with('success', 'تم التحديث');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('countries.index')->with('success', 'تم الحذف');
    }
}
