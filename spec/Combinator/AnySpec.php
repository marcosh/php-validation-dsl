<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsBool;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\Any;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('Any', function () {
    it('returns a error result in every case if it does not contain any validator', function () {
        $any = Any::validations([]);

        expect($any->validate('gigi')->equals(ValidationResult::errors([Any::NOT_EVEN_ONE => []])))->toBeTruthy();
    });

    it('returns a valid result if one validator succeeds', function () {
        $any = Any::validations([
            new IsString(),
            new IsBool()
        ]);

        expect($any->validate(true)->equals(ValidationResult::valid(true)))->toBeTruthy();
    });

    it('returns an error result if every validator fails with all the errors combined', function () {
        $any = Any::validations([
            new IsString(),
            new IsBool()
        ]);

        expect($any->validate(42)->equals(ValidationResult::errors([
            Any::NOT_EVEN_ONE => [
                IsString::MESSAGE,
                IsBool::MESSAGE
            ]
        ])))->toBeTruthy();
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

            expect($any->validate(42)->equals(ValidationResult::errors([
                IsString::MESSAGE,
                IsBool::MESSAGE
            ])))->toBeTruthy();
        }
    );

    it(
        'returns a translated error result if every validator fails with the errors combined by the translator',
        function () {
            $any = Any::validationsWithTranslator([
                new IsString(),
                new IsBool()
            ], KeyValueTranslator::withDictionary([
                Any::NOT_EVEN_ONE => 'NOT EVEN ONE!'
            ]));

            expect($any->validate(42)->equals(ValidationResult::errors([
                'NOT EVEN ONE!' => [
                    IsString::MESSAGE,
                    IsBool::MESSAGE
                ]
            ])))->toBeTruthy();
        }
    );
});
