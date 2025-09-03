<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Lakm\PersonName\Enums\Country as CountryEnum;
use Lakm\PersonName\Enums\Ethnicity;

final class Country implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! CountryEnum::tryFrom($value) && ! Ethnicity::tryFrom($value)) {
            $fail(__('The :attribute is invalid'));
        }
    }
}
