<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\AnyElement;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('AnyElement', function () {
    it('returns an error result in every case if the array is empty', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([])->equals(ValidationResult::errors([])))->toBeTruthy();
    });

    it('returns a valid result if the validation on one element succeeds', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([42, 'bepi'])->equals(ValidationResult::valid([42, 'bepi'])))->toBeTruthy();
    });

    it('returns an error result if every element fails the validation', function () {
        $anyElement = AnyElement::validation(new IsString());

        expect($anyElement->validate([true, 42])->equals(ValidationResult::errors([
            0 => [IsString::MESSAGE],
            1 => [IsString::MESSAGE]
        ])))->toBeTruthy();
    });

    it(
        'returns a custom error result if every element fails the validation and a custom formatter is passed',
        function () {
            $anyElement = AnyElement::validationWithFormatter(
                new IsString(),
                function ($key, $resultMessages, $validationMessages) {
                    $resultMessages[] = $key . $validationMessages[0];

                    return $resultMessages;
                }
            );

            expect($anyElement->validate([true, 42])->equals(ValidationResult::errors([
                0 . IsString::MESSAGE,
                1 . IsString::MESSAGE
            ])))->toBeTruthy();
        }
    );
});
