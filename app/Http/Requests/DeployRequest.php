<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeployRequest extends FormRequest
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
            'deploy' => 'required|in:Approved,Rejected',
            'deploy_token' => 'nullable',
            'deploy_taskId' => 'required',
            'deploy_projectName' => 'required',
            'deploy_pipeline_name' => 'required',
            'deploy_stage_name'   => 'required',
            'deploy_action_name' => 'required',
        ];
    }
}
