<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class CameraRequest extends FormRequest
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
            $rules['location_id'] = ['required'];
            $rules['installation_position'] = ['required', 'max:150'];
            if ($this->action == 'admin.camera.store') {
                $rules['camera_id'] = ['required', 'max:150', 'unique:cameras,camera_id,NULL,id,deleted_at,NULL'];
            } elseif ($this->action == 'admin.camera.update') {
                $rules['camera_id'] = ['required', 'max:150', "unique:cameras,camera_id,{$this->p_request->id},id,deleted_at,NULL"];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['camera_id'] = 'カメラNo';
        $attributes['location_id'] = '現場名';
        $attributes['installation_position'] = '設置場所';

        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        $messages['camera_id.required'] = 'カメラNoを入力してください。';
        $messages['camera_id.unique'] = 'すでに登録されたカメラNoです。';
        $messages['location_id.required'] = '現場名を入力してください。';
        $messages['installation_position.required'] = '設置場所を入力してください。';

        return $messages;
    }
}
