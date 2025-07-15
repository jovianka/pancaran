<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $majorRules = [];
        if ($this->user()->type == 'student') {
            $majorRules = [
                'integer',
                Rule::exists('major', 'id')->where('faculty_id', $this->faculty_id),
                'required_with:faculty_id'
            ];
        } else {
            $majorRules = [
                'integer',
                Rule::exists('major', 'id')->where(function (Builder $query) {
                    $query->where('faculty_id', '=', $this->faculty_id)
                        ->orWhere('name', '=', 'Any');
                }),
                'required_with:faculty_id'
            ];
        }

        return [
            'poster' => 'nullable|file|image',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'event_level' => 'required|string|in:international,regional,national,university,faculty,major',
            'job_description' => 'required|file|extensions:pdf',
            'faculty_id' => 'required|integer|exists:faculty,id',
            'major_id' => $majorRules,
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'requirements' => 'required|json',
            'tags' => 'nullable|array',
            'tags.*' => 'string|lowercase'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}
