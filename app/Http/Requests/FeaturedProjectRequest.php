<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeaturedProjectRequest extends FormRequest
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
                    'project_name' => 'required|string',
                    'live_link' => 'required|string',
                    'image.*' => 'nullable|mimes:png,jpg,jpeg|max:25048',
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'project_name' => 'required|string',
                    'live_link' => 'required|string',

                ];

                if ($this->hasFile('image')) {
                    $rules['image.*'] = 'nullable|file|mimes:png,jpg,jpeg|max:25048';
                } else {

                    $rules['image.*'] = 'nullable';
                }
                return $rules;


                break;
        }
    }

    public function messages()
    {
        return [
            'project_name.required' => 'The project_name field is required.',
            'live_link.required' => 'The live_link field is required.',
            // 'image.required' => 'The image field is required.',


        ];
    }
}
