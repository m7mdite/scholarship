<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreScholarshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scholarship_name' => 'required|string|max:50',
            'degree' => 'required|string|max:40',
            'finance' => 'required|string|max:40',
            'scholarship_description' => 'required|string|max:500',
            'donar' => 'required|string|max:40',
            'start_date' => 'required|date',
            'finished_date' => 'required|date',
            'scholarship_language' => 'required|string|max:30',
            'scholarship_link' => 'required|url|max:255',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'specialization_id' => 'required|exists:specializations,id',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }

    /**
     * رسائل الأخطاء باللغة العربية لكل حقل
     */
    public function messages(): array
    {
        return [
            'scholarship_name.required' => 'اسم المنحة مطلوب.',
            'scholarship_name.max' => 'اسم المنحة يجب ألا يزيد عن 50 حرفاً.',
            'degree.required' => 'الدرجة العلمية مطلوبة.',
            'degree.max' => 'الدرجة العلمية يجب ألا تزيد عن 40 حرفاً.',
            'finance.required' => 'حقل التمويل مطلوب.',
            'finance.max' => 'حقل التمويل يجب ألا يزيد عن 40 حرفاً.',
            'scholarship_description.required' => 'وصف المنحة مطلوب.',
            'scholarship_description.max' => 'وصف المنحة يجب ألا يزيد عن 500 حرف.',
            'donar.required' => 'الجهة المانحة مطلوبة.',
            'donar.max' => 'الجهة المانحة يجب ألا تزيد عن 40 حرفاً.',
            'start_date.required' => 'تاريخ البدء مطلوب.',
            'start_date.date' => 'تاريخ البدء يجب أن يكون تاريخاً صالحاً.',
            'finished_date.required' => 'تاريخ الانتهاء مطلوب.',
            'finished_date.date' => 'تاريخ الانتهاء يجب أن يكون تاريخاً صالحاً.',
            'scholarship_language.required' => 'لغة المنحة مطلوبة.',
            'scholarship_link.required' => 'رابط المنحة مطلوب.',
            'scholarship_link.url' => 'رابط المنحة يجب أن يكون رابطاً صحيحاً.',
            'country_id.required' => 'يرجى اختيار الدولة.',
            'country_id.exists' => 'الدولة المختارة غير موجودة.',
            'city_id.required' => 'يرجى اختيار المدينة.',
            'city_id.exists' => 'المدينة المختارة غير موجودة.',
            'specialization_id.required' => 'يرجى اختيار التخصص.',
            'specialization_id.exists' => 'التخصص المختار غير موجود.',
            'category_id.required' => 'يرجى اختيار الفئة.',
            'category_id.exists' => 'الفئة المختارة غير موجودة.',
            'photo.image' => 'الملف المرفق يجب أن يكون صورة.',
            'photo.mimes' => 'الصورة يجب أن تكون بصيغة jpeg, png, jpg.',
            'photo.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ];
    }

    /**
     * تخصيص شكل الرد عند فشل التحقق
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        // تحويل تنسيق الأخطاء إلى مصفوفة بسيطة (اختياري)
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0]; // أول رسالة فقط لكل حقل
        }

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'فشل التحقق من البيانات. الرجاء التحقق من المدخلات.',
            'errors' => $formattedErrors,
            'data' => null
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}