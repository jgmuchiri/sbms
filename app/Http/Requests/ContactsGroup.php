<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactsGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
                {
                    return [
                        'group_name' => 'required|max:50|unique',
                        'desc'=>'max:100'
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'group_name' => 'required|max:50|unique,group_name,'.$this->id,
                        'desc'=>'max:100'
                    ];
                }
            default:
                break;
        }
    }
}
