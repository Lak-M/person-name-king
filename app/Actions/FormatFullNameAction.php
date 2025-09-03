<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\Name;
use Lakm\PersonName\Enums\Country;
use Lakm\PersonName\Enums\Ethnicity;
use Lakm\PersonName\Exceptions\InvalidNameException;
use Lakm\PersonName\PersonName;

final class FormatFullNameAction
{
    /**
     * @throws InvalidNameException
     */
    public function execute(string $fullName, Country|Ethnicity $country): Name
    {
        $nameBuilder = PersonName::fromFullName($fullName, $country);

        return new Name(
            first: $nameBuilder->first(),
            middle: $nameBuilder->middle(),
            last: $nameBuilder->last(),
            sorted: $nameBuilder->sorted(),
            possessive: $nameBuilder->possessive(),
            prefix: $nameBuilder->prefix(),
            suffix: $nameBuilder->suffix(),
            redated: $nameBuilder->redated(),
            mentionable: $nameBuilder->mentionable(),
            family: $nameBuilder->family(),
            abbreviations: $nameBuilder->abbreviations,
            father: $nameBuilder->fatherName,
            grand_father: $nameBuilder->grandFatherName,
            kunya: $nameBuilder->kunya,
            ism: $nameBuilder->ism,
            nasab: $nameBuilder->nasab,
            laqab: $nameBuilder->laqab,
            nisbah: $nameBuilder->nisbah,
        );
    }
}
