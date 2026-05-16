<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Scholarship;
use App\Models\Specialization;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;

class ScholarshipController extends Controller
{
    public function getTopScholarships()
    {
        $today = Carbon::today();

        $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
            ->where('finished_date', '>=', $today) // فقط المنح التي لم تنتهِ
            ->orderBy('id', 'desc') // أحدث المنح أولاً
            ->take(15)
            ->get()
            ->map(function ($scholarship) use ($today) {
                // حساب حالة تاريخ البدء
                $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
                if ($startDate && $startDate->isFuture()) {
                    $startStatus = 'تبدأ في ' . $startDate->toDateString();
                } elseif ($startDate && $startDate->lte($today)) {
                    $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
                    // بما أن finished_date >= اليوم، فالباقي موجب
                    $startStatus = 'متبقي ' . $daysRemaining . ' يوم';
                } else {
                    $startStatus = 'تاريخ البدء غير محدد';
                }

                // الحصول على أول صورة إن وجدت
                $photoUrl = null;
                if ($scholarship->photos->isNotEmpty()) {
                    $photoPath = $scholarship->photos->first()->image_path;
                    // إذا كان المسار يبدأ بـ /storage/ أو كان متوقعاً
                    $photoUrl = url($photoPath); // أو asset($photoPath)
                }

                return [
                    'id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'finance' => $scholarship->finance,
                    'degree' => $scholarship->degree,
                    'city_name' => $scholarship->city->city_name ?? null,
                    'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                    'start_status' => $startStatus,
                    'photo_url' => $photoUrl,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب أول 15 منحة بنجاح',
            'data' => $scholarships
        ], 200);
    }
    // ======================================================================= // جلب جميع المنح (مع الصور والعلاقات)
    public function index()
    {
        $scholarships = Scholarship::with([
            'country', 'city', 'specialization', 'category', 'photos'
        ])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح بنجاح',
            'data' => $scholarships
        ], 200);
    }

    // إضافة منحة جديدة (مع صورة اختيارية)
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
            'scholarship_language' => 'nullable|string|max:30',
            'scholarship_link' => 'nullable|url|max:255',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'specialization_id' => 'required|exists:specializations,id',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // إنشاء المنحة
            $scholarship = Scholarship::create($validated);

            // رفع الصورة إن وُجدت
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('scholarships', $fileName, 'public');
                $scholarship->photos()->create([
                    'image_path' => Storage::url($path),
                    'city_id' => $validated['city_id'] ?? null,
                ]);
            }

            DB::commit();
            // تحميل العلاقات لعرضها في الاستجابة
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos']);

            return response()->json([
                'status' => 'success',
                'message' => 'تمت إضافة المنحة بنجاح',
                'data' => $scholarship
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل إضافة المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // عرض منحة محددة
    public function show($id)
    {
        $scholarship = Scholarship::with([
            'country', 'city', 'specialization', 'category',
            'photos', 'howToApply', 'applicationCriteria', 'personalExperiences'
        ])->find($id);

        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنحة بنجاح',
            'data' => $scholarship
        ], 200);
    }

    // تحديث منحة
    public function update(Request $request, $id)
    {
        $scholarship = Scholarship::find($id);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'scholarship_name' => 'sometimes|string|max:50',
            'degree' => 'sometimes|string|max:40',
            'finance' => 'nullable|string|max:40',
            'scholarship_description' => 'nullable|string|max:500',
            'donar' => 'nullable|string|max:40',
            'start_date' => 'nullable|date',
            'finished_date' => 'nullable|date',
            'scholarship_created_at' => 'nullable|date',
            'scholarship_language' => 'nullable|string|max:30',
            'scholarship_link' => 'nullable|url|max:255',
            'country_id' => 'sometimes|exists:countries,id',
            'city_id' => 'sometimes|exists:cities,id',
            'specialization_id' => 'sometimes|exists:specializations,id',
            'category_id' => 'sometimes|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $scholarship->update($validated);

            // معالجة الصورة الجديدة (إن وجدت)
            if ($request->hasFile('photo')) {
                // حذف الصورة القديمة إن وجدت
                if ($scholarship->photos->isNotEmpty()) {
                    $oldPhoto = $scholarship->photos->first();
                    // حذف الملف الفعلي من التخزين
                    $oldPath = str_replace('/storage/', '', $oldPhoto->image_path);
                    Storage::disk('public')->delete($oldPath);
                    $oldPhoto->delete();
                }
                // رفع الصورة الجديدة
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('scholarships', $fileName, 'public');
                $scholarship->photos()->create([
                    'image_path' => Storage::url($path)
                ]);
            }

            DB::commit();
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos']);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث المنحة بنجاح',
                'data' => $scholarship
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل تحديث المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // حذف منحة
    public function destroy($id)
    {
        $scholarship = Scholarship::find($id);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            // حذف الصور المرتبطة من التخزين
            foreach ($scholarship->photos as $photo) {
                $path = str_replace('/storage/', '', $photo->image_path);
                Storage::disk('public')->delete($path);
            }
            // حذف المنحة (ستحذف الصور تلقائياً عبر cascade أو يدوياً)
            $scholarship->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المنحة بنجاح',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل حذف المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }





    // =====================================
    
    // (إضافي) جلب أول 15 منحة نشطة مع معلومات مختصرة (للصفحة الرئيسية)
    public function topScholarships()
    {
        $today = now()->toDateString();

        $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
            ->where('finished_date', '>=', $today)
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->map(function ($scholarship) {
                $photoUrl = $scholarship->photos->isNotEmpty() ? $scholarship->photos->first()->image_path : null;
                $startStatus = $this->getStartStatus($scholarship);
                return [
                    'id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'finance' => $scholarship->finance,
                    'degree' => $scholarship->degree,
                    'city_name' => $scholarship->city->city_name ?? null,
                    'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                    'start_status' => $startStatus,
                    'photo_url' => $photoUrl,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب أول 15 منحة بنجاح',
            'data' => $scholarships
        ], 200);
    }

    // دالة مساعدة لحساب حالة البدء
    private function getStartStatus($scholarship)
    {
        if (!$scholarship->start_date) return 'تاريخ البدء غير محدد';
        $startDate = \Carbon\Carbon::parse($scholarship->start_date);
        $today = \Carbon\Carbon::today();
        if ($startDate->isFuture()) {
            return 'تبدأ في ' . $startDate->toDateString();
        } elseif ($startDate->lte($today)) {
            $daysRemaining = $today->diffInDays(\Carbon\Carbon::parse($scholarship->finished_date), false);
            return $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
        }
        return 'تاريخ البدء غير محدد';
    }
}
