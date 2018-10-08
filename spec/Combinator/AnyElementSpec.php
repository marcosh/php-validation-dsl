<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\AnyElement;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('EveryElement', function () {
    it('returns an error result in every case if the array is empty', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([]))->toEqual(ValidationResult::errors([]));
    });

    it('returns a valid result if the validation on one element succeeds', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([42, 'bepi']))->toEqual(ValidationResult::valid([42, 'bepi']));
    });

    it('returns an error result if every element fails the validation', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([true, 42]))->toEqual(ValidationResult::errors([
            0 => [IsString::NOT_A_STRING],
            1 => [IsString::NOT_A_STRING]
        ]));
    });
});
