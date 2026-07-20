<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class CvController extends Controller
{


    /**
     * تحسين النبذة المهنية باستخدام Ollama محلياً (أو أي مزود آخر حسب الإعدادات)
     * في حال الفشل، تُرجع النص الأصلي.
     */
    private function enhanceBio($bio, $specialization, $language)
    {
        $provider = env('AI_PROVIDER', 'ollama');
        if ($provider === 'ollama' || $provider === 'failover') {
            return $this->enhanceWithOllama($bio, $specialization, $language);
        }
        return $bio;
    }


    private function enhanceWithOllama($bio, $specialization, $language)
    {
        $ollamaUrl = env('OLLAMA_URL', 'http://localhost:11434');
        $model = env('OLLAMA_MODEL', 'llama3.1');
        if ($language === 'arabic') {
            $prompt = "أعد صياغة النص التالي ليكون نبذة مهنية مميزة وجذابة لتخصص '$specialization'، مع الحفاظ على المعنى الأساسي. اجعل النص احترافياً ومؤثراً، ولا تزد عن 150 كلمة:\n\n$bio";
        } else {
            $prompt = "Rewrite the following professional summary to be more compelling and impressive for a '$specialization' specialist. Keep the core meaning but make it highly professional and impactful. Max 150 words:\n\n$bio";
        }
        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($ollamaUrl . '/api/chat', [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'stream' => false,
                    'options' => [
                        'num_predict' => 300,
                        'temperature' => 0.7,
                    ],
                ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['message']['content'] ?? $bio;
            } else {
                Log::warning("Ollama API failed (status {$response->status()}): " . $response->body());
                return $bio;
            }
        } catch (\Exception $e) {
            Log::error("Ollama connection error: " . $e->getMessage());
            return $bio;
        }
    }
    // ============================================================  سيفي  ====================
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
        $data['bio'] = $this->enhanceBio($validated['bio'], $validated['specialization'], $validated['language']);
        $html = view($view, $data)->render();
        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'autoArabic' => true,
            ]);

            if ($validated['language'] === 'arabic') {
                $mpdf->SetDirectionality('rtl');
            }
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', 'S');
            $fileName = 'CV_' . preg_replace('/[^A-Za-z0-9]/', '_', $validated['full_name']) . '.pdf';
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('mPDF generation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'فشل إنشاء ملف PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    // ======================================================= رسالة الدافع =======================================================
    public function generateMotivationLetter(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:arabic,english',
            'full_name' => 'required|string|max:100',
            'age' => 'required|integer|min:16|max:100',
            'scholarship_name' => 'required|string|max:200',
            'university' => 'required|string|max:200',
            'specialization' => 'required|string|max:200',
            'country' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        $data = [
            'full_name' => $validated['full_name'],
            'age' => $validated['age'],
            'scholarship_name' => $validated['scholarship_name'],
            'university' => $validated['university'],
            'specialization' => $validated['specialization'],
            'country' => $validated['country'],
            'email' => $validated['email'] ?? '',
            'phone' => $validated['phone'] ?? '',
            'date' => now()->format('d/m/Y'),
        ];

        $view = ($validated['language'] === 'arabic') ? 'motivation.arabic' : 'motivation.english';
        $html = view($view, $data)->render();

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'autoArabic' => true,
            ]);

            if ($validated['language'] === 'arabic') {
                $mpdf->SetDirectionality('rtl');
            }

            $mpdf->WriteHTML($html);
            return $mpdf->Output('Motivation_Letter_' . preg_replace('/[^A-Za-z0-9]/', '_', $validated['full_name']) . '.pdf', 'D');
        } catch (\Exception $e) {
            Log::error('Motivation letter generation failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'فشل إنشاء ملف PDF'], 500);
        }
    }
    // ======================================================= رسالة التوصية =======================================================
    public function generateRecommendationLetter(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:arabic,english',
            'doctor_name' => 'required|string|max:100',
            'doctor_email' => 'required|email',
            'student_name' => 'required|string|max:100',
            'student_age' => 'required|integer|min:16|max:100',
            'specialization' => 'required|string|max:200',
            'university' => 'nullable|string|max:200',      // اختياري
            'scholarship_name' => 'nullable|string|max:200', // اختياري
        ]);

        $data = [
            'doctor_name' => $validated['doctor_name'],
            'doctor_email' => $validated['doctor_email'],
            'student_name' => $validated['student_name'],
            'student_age' => $validated['student_age'],
            'specialization' => $validated['specialization'],
            'university' => $validated['university'] ?? 'جامعة __________',
            'scholarship_name' => $validated['scholarship_name'] ?? 'المنحة الدراسية',
            'date' => now()->format('d/m/Y'),
        ];

        $view = ($validated['language'] === 'arabic') ? 'recommendation.arabic' : 'recommendation.english';
        $html = view($view, $data)->render();

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 25,
                'margin_right' => 25,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'autoArabic' => true,
            ]);

            if ($validated['language'] === 'arabic') {
                $mpdf->SetDirectionality('rtl');
            }

            $mpdf->WriteHTML($html);
            $fileName = 'Recommendation_' . preg_replace('/[^A-Za-z0-9]/', '_', $validated['student_name']) . '.pdf';
            return $mpdf->Output($fileName, 'D');
        } catch (\Exception $e) {
            Log::error('Recommendation letter generation failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'فشل إنشاء ملف PDF'], 500);
        }
    }
}
