<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class CourseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'school_class_id' => 'nullable|exists:school_classes,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->name) {
            $this->merge([
                'slug' => Str::slug($this->name . '-' . uniqid()),
            ]);
        }
    }
}
