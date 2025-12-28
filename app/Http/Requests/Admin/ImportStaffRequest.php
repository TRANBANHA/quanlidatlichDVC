<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:5120',
                function ($attribute, $value, $fail) {
                    $allowedMimes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                        'application/vnd.ms-excel', // xls
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'text/x-csv',
                        'application/x-csv',
                    ];
                    
                    $allowedExtensions = ['xlsx', 'xls', 'csv'];
                    
                    $mimeType = $value->getMimeType();
                    $extension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($mimeType, $allowedMimes) && !in_array($extension, $allowedExtensions)) {
                        $fail('File phải có định dạng: xlsx, xls hoặc csv.');
                    }
                },
            ],
            'don_vi_id' => 'required|exists:don_vi,id',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file để import.',
            'file.mimes' => 'File phải có định dạng: xlsx, xls hoặc csv.',
            'file.max' => 'Kích thước file không được vượt quá 5MB.',
            'don_vi_id.required' => 'Vui lòng chọn đơn vị/phường.',
            'don_vi_id.exists' => 'Đơn vị/phường không hợp lệ.',
        ];
    }
}
