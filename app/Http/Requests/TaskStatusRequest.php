<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusRequest extends FormRequest
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
        $id = $this->taskStatus ? $this->taskStatus->id : null;

        return [
            'name' => 'required|string|unique:task_statuses,name,' . $id,
            'value' => 'required|integer|unique:task_statuses,value,' . $id,
            'status' => 'required|string|in:0,1',
        ];
    }
}
