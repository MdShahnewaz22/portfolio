<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
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
                    'institution_name' => 'required|string',
					'year_to_year' => 'required|string',
					'certificate_name' => 'required|string',
					'group' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'institution_name' => 'required|string',
					'year_to_year' => 'required|string',
					'certificate_name' => 'required|string',
					'group' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'institution_name.required' => 'The institution_name field is required.',
 			'year_to_year.required' => 'The year_to_year field is required.',
 			'certificate_name.required' => 'The certificate_name field is required.',
 			'group.required' => 'The group field is required.',


        ];
    }
}
