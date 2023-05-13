<?php

namespace App\Http\Requests\Authentication;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:filter',
            'password' => 'required|string',
        ];
    }

    /**
     * @throws HttpResponseException
     * @throws Exception
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new ApiResponse(now(), $this->fingerprint());

        throw new HttpResponseException(
            $response->setValidateError($validator->errors()->getMessages(),
                ResponseAlias::HTTP_BAD_REQUEST,
                'Failed validate login request.')
        );
    }
}
