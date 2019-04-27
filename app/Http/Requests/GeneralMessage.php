<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralMessage extends FormRequest
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
            case 'POST':
                {
                    return [
                        'name' => 'required|max:50',
                        'email' => 'required|max:50',
                        'subject' => 'required|max:50',
                        'message' => 'required|max:50',
                        'catch_bots' => 'required',
                        'catch_bots_confirm' => 'required|same:catch_bots',
                    ];
                }
            default:
                break;
        }

    }

    public function message(){
        return[
            'catch_bots_confirm.same' => __("Invalid captcha entered"),
        ];
    }
}
