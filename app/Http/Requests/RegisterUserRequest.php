<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'username' => 'required|string|max:20|unique:users,username',
            'phone' => 'required|string|unique:users,phone',
            'birth_date' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Você precisa escolher um nome de usuário.',
            'phone.required' => 'Você precisa informar um número de telefone.',
            'birth_date.required' => 'Você precisa informar uma data de nascimento.',
            'password.required' => 'Você precisa escolher uma senha.',
            'password.confirmed' => 'Você precisa confirmar a sua senha.',

            'username.max' => 'Escolha um nome de usuário menor.',
            'username.unique' => 'O nome de usuário já existe.',
            'phone.unique' => 'O número de telefone já está cadastrado.',
            'phone.size' => 'O número de telefone precisa ter 11 dígitos.',
            'password.min' => 'A sua senha precisa ter no mínimo 6 dígitos.',
        ];
    }
}
