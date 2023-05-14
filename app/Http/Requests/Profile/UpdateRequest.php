<?php

namespace App\Http\Requests\Profile;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    use ApiResponser;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at')->ignore(auth()->user()->id),
            ],
            'name' => 'required|string',
            'nip' => 'required|string',
            'phone' => 'required|max:13',
            'place_of_birth' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE',
            'religion' => 'required|in:ISLAM,KRISTEN,KHATOLIK,BUDHA,HINDU,KONG_WU_CHU',
            'address' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            ($this->baseResponse(
                422,
                'error',
                $validator->errors()->toArray()
            )->original),
            422
        ));
    }
}
