<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class ThiefRequest extends FormRequest
{
    protected $action;
    protected $p_request;

    public function __construct(Request $request, Factory $factory)
    {
        $this->action = !empty($request->route()->getName()) ? $request->route()->getName() : '';
        $this->p_request = $request;
    }

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
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if (!empty($this->action)) {
            if ($this->action == 'admin.thief.create_rule') {
                $rules['selected_camera'] = ['required'];
            } else {
                $rules['selected_camera'] = ['nullable'];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['selected_camera'] = 'カメラ';

        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        $messages['selected_camera.required'] = 'カメラを選択してください。';

        return $messages;
    }
}
