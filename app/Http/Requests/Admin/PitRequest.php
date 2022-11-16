<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class PitRequest extends FormRequest
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
            if ($this->action == 'admin.pit.create_rule') {
                $rules['selected_camera'] = ['required'];
            } else {
                $rules['selected_camera'] = ['nullable'];
            }

            if ($this->action == 'admin.pit.store') {
                $rules['max_permission_time'] = ['required'];
                $rules['min_members'] = ['required', 'max:2'];
                $rules['init_persons'] = ['max:2'];
                $rules['name'] = ['max:6'];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['selected_camera'] = 'カメラ';
        $attributes['name'] = 'ルール名';

        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        $messages['selected_camera.required'] = 'カメラを選択してください。';
        $messages['max_permission_time.required'] = 'アラート対象滞在時間を選択してください。';
        $messages['min_members.required'] = 'ピット内人数を入力してください。';
        $messages['min_members.max'] = 'ピット内人数は2桁以下にして入力してください。';
        $messages['init_persons.max'] = '現在のピット内人数は2桁以下にして入力してください。';

        return $messages;
    }
}
