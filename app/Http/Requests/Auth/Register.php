<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'required|integer',
            'phone_number' => 'required|string|min:11|max:11',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'You Must Enter A First_Name',
            'second_name.required' => 'You Must Enter A Second_Name',
        ];
    }
}
