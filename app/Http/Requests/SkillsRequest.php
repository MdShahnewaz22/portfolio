<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'title' => 'required|string',
                    'percent' => 'required|string',
                    // 'image' => 'required|string',
                    'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
                    // 'file' => 'required|file|mimes:xls,doc,pdf,ppt|max:25048'
                    'file' => 'nullable|file|mimes:xls,xlsx,doc,docx,pdf,ppt,pptx|max:25048'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'title' => 'required|string',
                    'percent' => 'required|string',
                    // 'image' => 'required|string',
                    // 'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
                    // 'file' => 'required|file|mimes:xls,doc,pdf,ppt|max:25048'
                ];
                if ($this->hasFile('image')) {
                    $rules['image'] = 'nullable|file|mimes:png,jpg,jpeg|max:25048';
                } else {

                    $rules['image'] = 'nullable';
                }

                if ($this->hasFile('file')) {
                    $rules['file'] = 'nullable|file|mimes:png,jpg,jpeg,webp|max:25048';
                } else {

                    $rules['file'] = 'nullable';
                }

                return $rules;
                break;
        }
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'percent.required' => 'The percent field is required.',
            // 'image.required' => 'The image field is required.',
            // 'file.required' => 'The file field is required.',
            'file.required' => 'The notice file field is required.',
            'file.mimes' => 'The notice file field must be a file of type: xls,xlsx,doc,docx,pdf,ppt,pptx.',


        ];
    }
}
