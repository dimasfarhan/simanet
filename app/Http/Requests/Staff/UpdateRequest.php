<?php

namespace App\Http\Requests\Staff;

use App\Models\Staff;
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
        $uuid = $this->route('uuid');
        $staff = Staff::where('uuid', $uuid)->first();
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at')->ignore($staff->user->id),
            ],
            'name' => 'required|string',
            'nip' => 'required|string',
            'phone' => 'required|max:13',
            'place_of_birth' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:MALE,FEMALE',
            'religion' => 'required|in:ISLAM,KRISTEN,KHATOLIK,BUDHA,HINDU,KONG_WU_CHU',
            'address' => 'required',
            'date_joined' => 'required|date',
            'is_active' => 'required|boolean',
            'dismissal_reason' => 'required_if:is_active,0|in:RESIGN,FIRED'
        ];
    }

    public function messages()
    {
        return [
            'dismissal_reason.required' => 'Dismissal reason is required!',
            'dismissal_reason.in' => 'Dismissal reason value must be either RESIGN or FIRED!'
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
