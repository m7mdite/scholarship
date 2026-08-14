<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
class PhotoController extends Controller
{
    // PhotoController - endpoint مستقل لإدارة بنك الصور
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
            'city_id' => 'required|exists:cities,id',
            'specialization_id' => 'required|exists:specializations,id',
        ]);

        $file = $request->file('photo');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('scholarships', $fileName, 'public');

        $photo = Photo::create([
            'image_path' => Storage::url($path),
            'city_id' => $request->city_id,
            'specialization_id' => $request->specialization_id,
        ]);

        return response()->json(['status' => 'success', 'data' => $photo], 201);
    }
}
