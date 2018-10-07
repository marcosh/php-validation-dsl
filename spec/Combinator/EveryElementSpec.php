<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\EveryElement;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('EveryElement', function () {
    it('returns a valid result in every case if the array is empty', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate([]))->toEqual(ValidationResult::valid([]));
    });

    it('returns a valid result if the validation on every element succeeds', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate(['gigi', 'bepi']))->toEqual(ValidationResult::valid(['gigi', 'bepi']));
    });

    it('returns an error result if one element fails the validation', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate(['gigi', 42]))->toEqual(ValidationResult::errors([
            1 => [IsString::NOT_A_STRING]
        ]));
    });
});
