<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check && auth()->user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case "POST":
                return [
                    'name' => 'required|unique',
                    'desc' => 'max:50',
                ];
                break;
            case "PUT":
            case "PATCH":
                return [
                    'name' => 'required|unique',
                    'desc' => 'max:50',
                ];
                break;

            case "DELETE":
                return [

                ];
                break;
            default:
                break;
        }
    }
}
