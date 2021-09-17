<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsInteger;
use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\All;
use Marcosh\PhpValidationDSL\Combinator\Focus;
use Marcosh\PhpValidationDSL\Combinator\MapErrors;
use Marcosh\PhpValidationDSL\Combinator\TranslateErrors;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('TranslateErrors', function () {
    $translator = KeyValueTranslator::withDictionary([
        IsString::MESSAGE => 'Only strings here!',
        IsInteger::MESSAGE => 'Only integers here!'
    ]);

    $validator = TranslateErrors::validationWithTranslator(new IsString(), $translator);

    it('does not modify the result of a correct validation', function () use ($validator) {
        expect($validator->validate('gigi')->equals(ValidationResult::valid('gigi')))->toBeTruthy();
    });

    it('translates the result of a failed validation', function () use ($validator) {
        expect($validator->validate(42)->equals(ValidationResult::errors(['Only strings here!'])))->toBeTruthy();
    });

    it('translates nested results of a failed validation', function () use ($translator) {
        $validator = TranslateErrors::validationWithTranslator(
            All::validations([
                Focus::on(
                    function ($data) {
                        return $data['a'];
                    },
                    MapErrors::to(
                        new IsString(),
                        function (array $messages) {
                            return ['a' => $messages];
                        }
                    )
                ),
                Focus::on(
                    function ($data) {
                        return $data['b'];
                    },
                    MapErrors::to(
                        new IsInteger(),
                        function (array $messages) {
                            return ['b' => $messages];
                        }
                    )
                )
            ]),
            $translator
        );

        $data = [
            'a' => 42,
            'b' => 'gigi'
        ];

        $errors = [
            'a' => ['Only strings here!'],
            'b' => ['Only integers here!']
        ];

        expect($validator->validate($data)->equals(ValidationResult::errors($errors)))->toBeTruthy();
    });
});
