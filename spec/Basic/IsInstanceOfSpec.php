<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsInstanceOf;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

class Foo {};

describe('IsInstanceOf', function () {
    $isInstanceOf = IsInstanceOf::withClassName(Foo::class);

    it('returns a valid result if the argument is an instance of the given class', function () use ($isInstanceOf) {
        expect($isInstanceOf->validate(new Foo()))->toEqual(ValidationResult::valid(new Foo()));
    });

    it('returns an error result if the argument is not a string', function () use ($isInstanceOf) {
        expect($isInstanceOf->validate(new \stdClass()))
            ->toEqual(ValidationResult::errors([IsInstanceOf::NOT_AN_INSTANCE]));
    });
});
