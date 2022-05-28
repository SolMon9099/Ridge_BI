<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class NotificationGroupRequest extends FormRequest
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
            $rules['emails.0'] = ['required', 'email', 'max:150'];
            foreach ($this->p_request->input('emails') as $key => $email) {
                if ($key == 0) {
                    continue;
                }
                $rules['emails.'.$key] = ['nullable', 'email', 'max:150'];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['name'] = 'アカウント名';
        $attributes['emails'] = 'メールアドレス';
        $attributes['emails.*'] = 'メールアドレス';

        return $attributes;
    }

    public function messages()
    {
        $messages = [];
        $messages['name.required'] = 'アカウント名を入力してください。';
        $messages['emails.required'] = 'メールアドレスを入力してください。';
        $messages['emails.*.required'] = 'メールアドレスを入力してください。';
        $messages['emails.*.email'] = 'メール形式ではありません。';

        return $messages;
    }
}
