<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\EveryElement;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('EveryElement', function () {
    it('returns a valid result in every case if the array is empty', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate([])->equals(ValidationResult::valid([])))->toBeTruthy();
    });

    it('returns a valid result if the validation on every element succeeds', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate(['gigi', 'bepi'])->equals(ValidationResult::valid(['gigi', 'bepi'])))
            ->toBeTruthy();
    });

    it('returns an error result if one element fails the validation', function () {
        $everyElement = EveryElement::validation(new IsString());

        expect($everyElement->validate(['gigi', 42])->equals(ValidationResult::errors([
            1 => [IsString::MESSAGE]
        ])))->toBeTruthy();
    });

    it(
        'returns a custom error result if one element fails the validation and a custom formatter is passed',
        function () {
            $everyElement = EveryElement::validationWithFormatter(
                new IsString(),
                function ($key, $resultMessages, $validationMessages) {
                    $resultMessages[] = $key . $validationMessages[0];

                    return $resultMessages;
                }
            );

            expect($everyElement->validate([true, 42])->equals(ValidationResult::errors([
                0 . IsString::MESSAGE,
                1 . IsString::MESSAGE
            ])))->toBeTruthy();
        }
    );
});
