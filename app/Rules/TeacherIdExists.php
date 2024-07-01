<?php

namespace App\Rules;

use App\Models\Teacher;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherIdExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Teacher::where('id', $value)->exists()) {
            $fail('The Teacher ID does not exist.');
        }
    }
}
