<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CvController extends Controller
{
    public function generateCV(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:arabic,english',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'languages' => 'required|array',
            'languages.*' => 'string',
            'skills' => 'required|array',
            'skills.*' => 'string',
            'age' => 'nullable|integer|min:16|max:100',
            'specialization' => 'required|string|max:100',
            'bio' => 'required|string|min:20',
        ]);

        $data = [
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '',
            'languages' => $validated['languages'],
            'skills' => $validated['skills'],
            'age' => $validated['age'] ?? '',
            'specialization' => $validated['specialization'],
            'bio' => $validated['bio'],
        ];

        $view = ($validated['language'] === 'arabic') ? 'cv.arabic' : 'cv.english';

        try {
            $pdf = Pdf::loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');

            // إعدادات أفضل لدعم العربية
            $pdf->setOptions([
                'defaultFont' => 'dejavu sans', // هذا الخط يدعم العربية جزئياً، لكن الأفضل تثبيت خط متكامل
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

            return $pdf->download('CV_' . preg_replace('/[^A-Za-z0-9]/', '_', $validated['full_name']) . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل إنشاء ملف PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}