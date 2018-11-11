<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsAsAsserted;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsAsAsserted', function () {
    $isAsAsserted = IsAsAsserted::withAssertion(function ($data) {
        return $data === 42;
    });

    it('returns a valid result if the assertion is satisfied', function () use ($isAsAsserted) {
        expect($isAsAsserted->validate(42)->equals(ValidationResult::valid(42)))->toBeTruthy();
    });

    it('returns an error result if the assertion is not satisfied', function () use ($isAsAsserted) {
        expect($isAsAsserted->validate('fortytwo')->equals(ValidationResult::errors([IsAsAsserted::NOT_AS_ASSERTED])))
            ->toBeTruthy();
    });

    it(
        'returns a custom error result if the assertion is not satisfied and a custom formatter is passed',
        function () {
            $isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
                function ($data) {
                    return $data === 42;
                },
                function ($data) {
                    return ['not 42'];
                }
            );

            expect($isAsAsserted->validate('fortytwo')->equals(ValidationResult::errors(['not 42'])))->toBeTruthy();
        }
    );
});