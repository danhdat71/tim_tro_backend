<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use App\Traits\ResponseTrait;

class BaseRequest extends FormRequest
{
    use ResponseTrait;

    public $request;

    public function __construct()
    {
        $this->request = request();
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $result = $this->responseMessageValidate($errors);

        throw new HttpResponseException($result);
    }
}
