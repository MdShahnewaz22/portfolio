<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Parsonal_InfoRequest extends FormRequest
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
                    'name' => 'required|string',
                    'designation' => 'required|string',
                    'residence' => 'required|string',
                    'city' => 'required|string',
                    'age' => 'required|string',
                    // 'image' => 'nullable|string'
                    'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'name' => 'required|string',
                    'designation' => 'required|string',
                    'residence' => 'required|string',
                    'city' => 'required|string',
                    'age' => 'required|string',
                    // 'image' => 'required|string'
                    // 'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
                ];
                if ($this->hasFile('image')) {
                    $rules['image'] = 'nullable|file|mimes:png,jpg,jpeg|max:25048';
                } else {

                    $rules['image'] = 'nullable';
                }
                return $rules;
                break;
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'designation.required' => 'The designation field is required.',
            'residence.required' => 'The residence field is required.',
            'city.required' => 'The city field is required.',
            'age.required' => 'The age field is required.',
            // 'image.required' => 'The image field is required.',


        ];
    }
}
