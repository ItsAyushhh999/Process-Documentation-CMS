<?php

namespace App\Http\Requests\Request\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'url'=>'nullable',
            'development_pipeline' => 'nullable',
            'staging_pipeline' => 'nullable',
            'production_Pipeline' => 'nullable',
            'description' => 'nullable',
        ];
    }
}
