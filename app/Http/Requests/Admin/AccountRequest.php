<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class AccountRequest extends FormRequest
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
            $rules['authority_id'] = ['required'];
            $rules['name'] = ['required', 'max:150'];
            if ($this->action == 'admin.account.store') {
                $rules['password'] = ['required', 'max:32', 'min:8', 'confirmed'];
                $rules['email'] = ['required', 'email', 'max:150', 'unique:admins,email,NULL,id,deleted_at,NULL'];
            } elseif ($this->action == 'admin.account.update') {
                $rules['password'] = ['nullable', 'max:32', 'min:8', 'confirmed'];
                $rules['email'] = ['required', 'email', 'max:150', "unique:admins,email,{$this->p_request->id},id,deleted_at,NULL"];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes['name'] = 'アカウント名';
        $attributes['password'] = 'パスワード';
        $attributes['email'] = 'メールアドレス';
        $attributes['department'] = '部門';

        return $attributes;
    }

    public function messages()
    {
        return [
            'authority_id.required' => '権限を選択してください。',
            'name.required' => 'アカウント名を入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.unique' => 'すでに登録されたメールアドレスです。',
            'password.required' => 'パスワードを入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
        ];
    }
}
