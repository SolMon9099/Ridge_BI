<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class NotificationMsgRequest extends FormRequest
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
            $rules['title'] = ['required', 'max:150'];
            $rules['content'] = ['required', 'string'];
        }
        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['title'] = 'メッセージ名';
        $attributes['content'] = 'メッセージ';

        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        $messages['title.required'] = 'タイトルを入力してください。';
        $messages['content.required'] = '内容を入力してください。';
        return $messages;
    }
}
