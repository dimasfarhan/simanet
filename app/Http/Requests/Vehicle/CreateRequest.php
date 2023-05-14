<?php

namespace App\Http\Requests\Vehicle;

use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateRequest extends FormRequest
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
            'jenis_kendaraan' => 'required',
            'nomor_polisi' => 'required',
            'brand' => 'required',
            'model_type' => 'required',
            'status' => 'required|in:AVAILABLE,UNAVAILABLE,RENTED,MAINTENANCE',
            'reason' => 'required_if:status,UNAVAILABLE,MAINTENANCE'
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Status is required!',
            'status.in' => 'Status value must be either AVAILABLE,UNAVAILABLE,RENTED, or MAINTENANCE!'
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
