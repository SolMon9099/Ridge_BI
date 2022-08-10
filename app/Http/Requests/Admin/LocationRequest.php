<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class LocationRequest extends FormRequest
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
            $rules['name'] = ['required', 'max:150'];
            // $rules['owners.*'] = ['required', 'max:150'];
            // $rules['managers.*'] = ['required', 'max:150'];
            if ($this->action == 'admin.location.store') {
                // $rules['code'] = ['required', 'max:150', 'unique:locations,code,NULL,id,deleted_at,NULL'];
            } elseif ($this->action == 'admin.location.update') {
                // $rules['code'] = ['required', 'max:150', "unique:locations,code,{$this->p_request->id},id,deleted_at,NULL"];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['code'] = '現場コード';
        $attributes['name'] = '設置エリア';
        // $attributes['owners.*'] = '現場責任者';
        // $attributes['managers.*'] = '現場担当者';
        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        // $messages['code.required'] = '現場コードを入力してください。';
        // $messages['code.unique'] = 'すでに登録された現場コードです。';
        $messages['name.required'] = '設置エリアを入力してください。';
        // $messages["owners.*.required"] = "現場責任者を入力してください。";
        // $messages["managers.*.required"] = "現場担当者を入力してください。";
        return $messages;
    }
}
