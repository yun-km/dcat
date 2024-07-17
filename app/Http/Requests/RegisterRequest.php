<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:'.User::class
            ],
            'verification' => [
                'required',
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ];
    }

    public function attributes()
    {
        return [
            __('auth.email') => __("Email"),
            __('auth.verification') => __("Verification"),
            __('auth.password') => __("Password"),
        ];
    }
}
