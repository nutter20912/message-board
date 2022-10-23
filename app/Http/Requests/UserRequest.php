<?php

namespace App\Http\Requests;

use App\Exceptions\BadRequestException;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * 是否授權
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 參數驗證格式
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'max:255', Password::min(4)->letters()],
        ];
    }

    /**
     * 驗證後回調
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->first(), 123);
        }
    }
}
