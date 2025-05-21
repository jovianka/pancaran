<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $nimRules = '';
        if ($this->user()->type == 'student') {
            $nimRules = 'string|max:255';
        } else {
            $nimRules = 'prohibited';
        }

        $emailRules = [];
        if ($this->user()->type == 'student') {
            $emailRules = [
                'string', 'lowercase', 'email', 'ends_with:student.unud.ac.id', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ];
        } else {
            $emailRules = [
                'string', 'lowercase', 'email', 'ends_with:unud.ac.id', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ];
        }

        return [
            'avatar' => 'nullable|file|image',
            'nim' => $nimRules,
            'name' => 'string|max:255',
            'email' => $emailRules,
            'faculty_id' => 'integer|exists:faculty,id',
            'major_id' => [
                'integer',
                Rule::exists('major', 'id')->where('faculty_id', $this->faculty_id),
                'required_with:faculty_id'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'faculty_id.exists' => 'faculty doesn\'t exist',
            'major_id.exists' => 'This major doesn\'t belong to the selected faculty'
        ];
    }

}
