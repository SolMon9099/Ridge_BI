<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Illuminate\Support\Facades\Auth;

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
        $login_user = Auth::guard('admin')->user();
        $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
        if (!empty($this->action)) {
            $rules['name'] = ['required', 'max:150'];
            $rules['safie_password'] = ['nullable', 'max:64'];
            if ($this->action == 'admin.account.store') {
                $rules['authority_id'] = ['required'];
                $rules['password'] = ['required', 'max:32', 'min:8', 'confirmed'];
                $rules['email'] = ['required', 'email', 'max:150', 'unique:admins,email,NULL,id,deleted_at,NULL'];
                $rules['contract_no'] = ['required', 'max:32'];
                if ($super_admin_flag) {
                    $rules['safie_user_name'] = ['nullable', 'max:64', 'unique:admins,safie_user_name,NULL,id,deleted_at,NULL,is_main_admin,1'];
                    // $rules['safie_client_id'] = ['nullable', 'max:64', 'unique:admins,safie_client_id,NULL,id,deleted_at,NULL,is_main_admin,1'];
                    $rules['safie_client_id'] = ['nullable', 'max:64'];
                    // $rules['safie_client_secret'] = ['nullable', 'max:64', 'unique:admins,safie_client_secret,NULL,id,deleted_at,NULL,is_main_admin,1'];
                    $rules['safie_client_secret'] = ['nullable', 'max:64'];
                    $rules['contract_no'][] = 'unique:admins,contract_no,NULL,id,deleted_at,NULL';
                }
            } elseif ($this->action == 'admin.account.update') {
                $rules['password'] = ['nullable', 'max:32', 'min:8', 'confirmed'];
                $rules['email'] = ['required', 'email', 'max:150', "unique:admins,email,{$this->p_request->id},id,deleted_at,NULL"];
                if ($this->p_request->admin->authority_id != config('const.super_admin_code')) {
                    if ($super_admin_flag) {
                        $rules['contract_no'] = ['required', 'max:32', "unique:admins,contract_no,{$this->p_request->id},id,deleted_at,NULL,is_main_admin,1"];
                        $rules['safie_user_name'] = ['nullable', 'max:64', "unique:admins,safie_user_name,{$this->p_request->id},id,deleted_at,NULL,is_main_admin,1"];
                        // $rules['safie_client_id'] = ['nullable', 'max:64', "unique:admins,safie_client_id,{$this->p_request->id},id,deleted_at,NULL,is_main_admin,1"];
                        $rules['safie_client_id'] = ['nullable', 'max:64'];
                        // $rules['safie_client_secret'] = ['nullable', 'max:64', "unique:admins,safie_client_secret,{$this->p_request->id},id,deleted_at,NULL,is_main_admin,1"];
                        $rules['safie_client_secret'] = ['nullable', 'max:64'];
                    } else {
                        $rules['contract_no'] = ['required', 'max:32'];
                    }
                    $rules['authority_id'] = ['required'];
                } else {
                    $rules['contract_no'] = ['nullable', 'max:32'];
                    $rules['authority_id'] = ['nullable'];
                }
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
        $attributes['contract_no'] = '契約ID';
        $attributes['safie_user_name'] = 'セーフィーID';
        $attributes['safie_password'] = 'セーフィーパス';
        $attributes['safie_client_id'] = 'client_id';
        $attributes['safie_client_secret'] = 'client_secret';

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
            'contract_no.required' => '契約IDを入力してください。',
        ];
    }
}
