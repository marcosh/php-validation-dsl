<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsInstanceOf;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function json_encode;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

class InstanceFoo {};

describe('IsInstanceOf', function () {
    $isInstanceOf = IsInstanceOf::withClassName(InstanceFoo::class);

    it('returns a valid result if the argument is an instance of the given class', function () use ($isInstanceOf) {
        $instance = new InstanceFoo();

        expect($isInstanceOf->validate($instance)->equals(ValidationResult::valid($instance)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a string', function () use ($isInstanceOf) {
        expect(
            $isInstanceOf->validate(new \stdClass())->equals(ValidationResult::errors([IsInstanceOf::NOT_AN_INSTANCE]))
        )->toBeTruthy();
    });

    it('returns a custom error result if the argument is not a string and a custom formatter is passed', function () {
        $isInstanceOf = IsInstanceOf::withClassNameAndFormatter(
            InstanceFoo::class,
            function (string $className, $data) {
                return [$className .  json_encode($data)];
            }
        );

        expect($isInstanceOf->validate(new \stdClass())->equals(ValidationResult::errors([InstanceFoo::class . '{}'])))
            ->toBeTruthy();
    });

    it('returns a translated error result if the argument is not a string and a translator is passed', function () {
        $isInstanceOf = IsInstanceOf::withClassNameAndTranslator(
            InstanceFoo::class,
            KeyValueTranslator::withDictionary([
                IsInstanceOf::NOT_AN_INSTANCE => 'NO INSTANCE HERE!'
            ])
        );

        expect($isInstanceOf->validate(new \stdClass())->equals(ValidationResult::errors(['NO INSTANCE HERE!'])))
            ->toBeTruthy();
    });
});
