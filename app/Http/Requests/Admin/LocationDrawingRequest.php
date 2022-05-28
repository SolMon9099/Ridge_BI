<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class LocationDrawingRequest extends FormRequest
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
            $rules['location_id'] = ['required', 'min:0', 'not_in:0'];
            $rules['floor_number'] = ['required', 'max:150'];
            $rules['drawing_file_path'] = ['required'];
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['location_id'] = '現場';
        $attributes['floor_number'] = '階数';
        $attributes['drawing_file_path'] = '図面ファイル';

        return $attributes;
    }

    public function messages()
    {
        return [
            'location_id.required' => '現場を入力してください。',
            'location_id.min' => '現場を入力してください。',
            'location_id.not_in' => '現場を入力してください。',
            'floor_number.required' => '階数を入力してください。',
            'drawing_file_path.required' => '図面ファイルを入力してください。',
        ];
    }
}
