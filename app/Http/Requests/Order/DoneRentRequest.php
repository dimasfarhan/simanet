<?php

namespace App\Http\Requests\Order;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DoneRentRequest extends FormRequest
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
            'photo_rear' => 'required|image|mimes:png,jpg,jpeg',
            'photo_front' => 'required|image|mimes:png,jpg,jpeg',
            'photo_side' => 'required|image|mimes:png,jpg,jpeg'
        ];
    }

    public function messages()
    {
        return [
            'photo_rear.mimes' => 'Photo rear format must be either png, jpg, or jpeg',
            'photo_front.mimes' => 'Photo front format must be either png, jpg, or jpeg',
            'photo_side.mimes' => 'Photo side format must be either png, jpg, or jpeg'
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
