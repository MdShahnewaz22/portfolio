<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
                    // 'image' => 'required|string',
                    'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
					'date' => 'required|date',
					'title' => 'required|string',
					'posted_by' => 'required|string',
					'category' => 'required|string',
					'posted_on' => 'required|date',
					'description' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    // 'image' => 'required|string',
                    // 'image' => 'nullable|mimes:png,jpg,jpeg|max:25048',
					'date' => 'required|date',
					'title' => 'required|string',
					'posted_by' => 'required|string',
					'category' => 'required|string',
					'posted_on' => 'required|date',
					'description' => 'required|string'
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
            // 'image.required' => 'The image field is required.',
 			'date.required' => 'The date field is required.',
 			'title.required' => 'The title field is required.',
 			'posted_by.required' => 'The posted_by field is required.',
 			'category.required' => 'The category field is required.',
 			'posted_on.required' => 'The posted_on field is required.',
 			'description.required' => 'The description field is required.',


        ];
    }
}
