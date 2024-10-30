<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
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
                    'phone' => 'required|string',
					'gmail' => 'required|string',
					'github' => 'required|string',
					'skype' => 'required|string',
					'language' => 'required|string',
					'years_experience' => 'required|string',
					'handled_project' => 'required|string',
					'open_source' => 'required|string',
					'awards' => 'required|string',
					'description' => 'required|string'
                ];
                break;

            case 'PATCH':
            case 'PUT':
                return [
                    'phone' => 'required|string',
					'gmail' => 'required|string',
					'github' => 'required|string',
					'skype' => 'required|string',
					'language' => 'required|string',
					'years_experience' => 'required|string',
					'handled_project' => 'required|string',
					'open_source' => 'required|string',
					'awards' => 'required|string',
					'description' => 'required|string'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'phone.required' => 'The phone field is required.',
 			'gmail.required' => 'The gmail field is required.',
 			'github.required' => 'The github field is required.',
 			'skype.required' => 'The skype field is required.',
 			'language.required' => 'The language field is required.',
 			'years_experience.required' => 'The years_experience field is required.',
 			'handled_project.required' => 'The handled_project field is required.',
 			'open_source.required' => 'The open_source field is required.',
 			'awards.required' => 'The awards field is required.',
 			'description.required' => 'The description field is required.',
 			

        ];
    }
}
