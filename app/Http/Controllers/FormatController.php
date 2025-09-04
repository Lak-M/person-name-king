<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Rules\Country as CountryRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Lakm\PersonName\Contracts\NameBuilderContract;
use Lakm\PersonName\Enums\Abbreviate;
use Lakm\PersonName\Enums\Country;
use Lakm\PersonName\Enums\Ethnicity;
use Lakm\PersonName\Exceptions\FormatNotSupportException;
use Lakm\PersonName\Exceptions\PartNotSupportException;
use Lakm\PersonName\PersonName;

final class FormatController extends Controller
{
    public function formatFullName(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string'],
            'country' => ['nullable', new CountryRule],
        ]);

        $pn = PersonName::fromFullName($validated['full_name'], !empty($validated['country']) ? $this->getCountry($validated['country']) : null);

        return $this->response($pn);
    }

    public function formatNameParts(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'prefix' => ['nullable', 'string'],
            'suffix' => ['nullable', 'string'],
            'country' => ['nullable', new CountryRule],
        ]);

        $country = !empty($validated['country']) ? $this->getCountry($validated['country']) : null;

        $pn = PersonName::build(
            firstName: $validated['first_name'],
            middleName: $validated['middle_name'] ?? null,
            lastName: $validated['last_name'] ?? null,
            prefix: $validated['prefix'] ?? null,
            suffix: $validated['suffix'] ?? null,
            country: $country,
            checkValidity: true
        );

        return $this->response($pn);
    }

    private function getCountry(string $country): Country|Ethnicity
    {
        if (Country::tryFrom($country)) {
            return Country::tryFrom($country);
        }

        return Ethnicity::tryFrom($country);
    }

    private function getData(NameBuilderContract $pn): array
    {
        $parts = [
            'full' => null,
            'first' => null,
            'middle' => null,
            'last' => null,
            'sorted' => null,
            'possessive' => null,
            'prefix' => null,
            'suffix' => null,
            'redated' => null,
            'mentionable' => null,
            'family' => null,
            'father' => null,
            'grand_father' => null,
            'kunya' => null,
            'ism' => null,
            'nasab' => null,
            'laqab' => null,
            'nisbah' => null,
        ];

        foreach ($parts as $part => $val) {
            $method = Str::camel($part);

            if (!method_exists($pn, $method)) {
                $method .= 'Name';
                if (!method_exists($pn, $method)) {
                    break;
                }
            }

            try {
                $parts[$part] = $pn->{$method}();
            } catch (PartNotSupportException|FormatNotSupportException $exception) {
                $parts[$part] = 'Not Supported';
            }
        }

        $abbreviations = [];

        try {
            foreach (Abbreviate::cases() as $abbreviate) {
                $abbreviations[] = $pn->abbreviated(format: $abbreviate);
            }
            $parts['abbreviations'] = $abbreviations;
        } catch (FormatNotSupportException $ex) {
            $parts['abbreviations'] = 'Not Supported';
        }

        return array_filter($parts);
    }

    private function response(NameBuilderContract $pn)
    {
        return Inertia::render('Home', [
            'name' => $this->getData($pn),
        ]);
    }
}
