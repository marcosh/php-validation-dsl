<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\Any;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Any', function () {
    it('returns a error result in every case if it does not contain any validator', function () {
        $any = Any::validations([]);

        expect($any->validate('gigi'))->toEqual(ValidationResult::errors([Any::NOT_EVEN_ONE => []]));
    });

    it('returns a valid result if one validator succeeds', function () {
        $any = Any::validations([
            new IsString(),
            new IsBool()
        ]);

        expect($any->validate(true))->toEqual(ValidationResult::valid(true));
    });

    it('returns an error result if every validator fails with all the errors combined', function () {
        $any = Any::validations([
            new IsString(),
            new IsBool()
        ]);

        expect($any->validate(42))->toEqual(ValidationResult::errors([
            Any::NOT_EVEN_ONE => [
                IsString::NOT_A_STRING,
                IsBool::NOT_A_BOOL
            ]
        ]));
    });

    it(
        'returns a custom error result if every validator fails with the errors combined by the error formatter',
        function () {
            $any = Any::validationsWithFormatter([
                new IsString(),
                new IsBool()
            ], function (array $messages) {
                return $messages;
            });

            expect($any->validate(42))->toEqual(ValidationResult::errors([
                IsString::NOT_A_STRING,
                IsBool::NOT_A_BOOL
            ]));
        }
    );
});
