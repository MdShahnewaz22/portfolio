<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceRequest extends FormRequest
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
                    'company_name' => 'required|string',
					'year_to_year' => 'required|string',
					'designation' => 'required|string',
					'description' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'company_name' => 'required|string',
					'year_to_year' => 'required|string',
					'designation' => 'required|string',
					'description' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'company_name.required' => 'The company_name field is required.',
 			'year_to_year.required' => 'The year_to_year field is required.',
 			'designation.required' => 'The designation field is required.',
 			'description.required' => 'The description field is required.',
 			

        ];
    }
}
