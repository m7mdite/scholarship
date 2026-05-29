<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateScholarshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكنك إضافة صلاحيات الأدمن لاحقاً
    }

    public function rules(): array
    {
        return [
            'scholarship_name' => 'sometimes|string|max:50',
            'degree' => 'sometimes|string|max:40',
            'finance' => 'sometimes|string|max:40',
            'scholarship_description' => 'sometimes|string|max:500',
            'donar' => 'sometimes|string|max:40',
            'start_date' => 'sometimes|date|date_format:Y-m-d',
            'finished_date' => 'sometimes|date|date_format:Y-m-d',
            'scholarship_language' => 'sometimes|string|max:30',
            'scholarship_link' => 'sometimes|url|max:255',
            'country_id' => 'sometimes|exists:countries,id',
            'city_id' => 'sometimes|exists:cities,id',
            'specialization_id' => 'sometimes|exists:specializations,id',
            'category_id' => 'sometimes|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'scholarship_name.max' => 'اسم المنحة يجب ألا يزيد عن 50 حرفاً.',
            'degree.max' => 'الدرجة العلمية يجب ألا تزيد عن 40 حرفاً.',
            'finance.max' => 'حقل التمويل يجب ألا يزيد عن 40 حرفاً.',
            'scholarship_description.max' => 'وصف المنحة يجب ألا يزيد عن 500 حرف.',
            'donar.max' => 'الجهة المانحة يجب ألا تزيد عن 40 حرفاً.',
            'start_date.date' => 'تاريخ البدء يجب أن يكون تاريخاً صالحاً بصيغة YYYY-MM-DD.',
            'start_date.date_format' => 'تاريخ البدء يجب أن يكون بصيغة YYYY-MM-DD.',
            'finished_date.date' => 'تاريخ الانتهاء يجب أن يكون تاريخاً صالحاً بصيغة YYYY-MM-DD.',
            'finished_date.date_format' => 'تاريخ الانتهاء يجب أن يكون بصيغة YYYY-MM-DD.',
            'scholarship_language.max' => 'لغة المنحة يجب ألا تزيد عن 30 حرفاً.',
            'scholarship_link.url' => 'رابط المنحة يجب أن يكون رابطاً صحيحاً.',
            'scholarship_link.max' => 'رابط المنحة يجب ألا يزيد عن 255 حرفاً.',
            'country_id.exists' => 'الدولة المختارة غير موجودة.',
            'city_id.exists' => 'المدينة المختارة غير موجودة.',
            'specialization_id.exists' => 'التخصص المختار غير موجود.',
            'category_id.exists' => 'الفئة المختارة غير موجودة.',
            'photo.image' => 'الملف المرفق يجب أن يكون صورة.',
            'photo.mimes' => 'الصورة يجب أن تكون بصيغة jpeg, png, jpg.',
            'photo.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        // تبسيط الأخطاء: نأخذ أول رسالة لكل حقل
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0];
        }

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'فشل التحقق من البيانات. الرجاء التحقق من المدخلات.',
            'errors' => $formattedErrors,
            'data' => null
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}