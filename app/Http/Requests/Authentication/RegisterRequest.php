<?php

namespace App\Http\Requests\Authentication;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:5|max:100',
            'email' => 'required|email:filter|unique:users,email',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed']
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
            $response->setValidateError(
                $validator->errors()->getMessages(),
                ResponseAlias::HTTP_BAD_REQUEST,
                'Failed validate register request.')
        );
    }
}
