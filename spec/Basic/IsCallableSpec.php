<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsCallable;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

class CallableFoo
{
    public static function bar(): void {}
    public function baz(): void {}
}

describe('IsCallable', function () {
    $isCallable = new IsCallable();

    it('returns a valid result if the argument is a built in function', function () use ($isCallable) {
        expect($isCallable->validate('sprintf')->equals(ValidationResult::valid('sprintf')))->toBeTruthy();
    });

    it('returns a valid result if the argument is an anonymous function', function () use ($isCallable) {
        $function = function () {};

        expect($isCallable->validate($function)->equals(ValidationResult::valid($function)))->toBeTruthy();
    });

    it('returns a valid result if the argument is a static method', function () use ($isCallable) {
        $staticMethod = [CallableFoo::class, 'bar'];

        expect($isCallable->validate($staticMethod)->equals(ValidationResult::valid($staticMethod)))->toBeTruthy();
    });

    it('returns a valid result if the argument is an instance method', function () use ($isCallable) {
        $instanceMethod = [new CallableFoo(), 'baz'];

        expect($isCallable->validate($instanceMethod)->equals(ValidationResult::valid($instanceMethod)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a callable', function () use ($isCallable) {
        expect($isCallable->validate('true')->equals(ValidationResult::errors([IsCallable::MESSAGE])))
            ->toBeTruthy();
    });

    it('returns a custom error if the argument is not a callable and a custom formatter is passed', function () {
        $isCallable = IsCallable::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isCallable->validate('true')->equals(ValidationResult::errors(['true'])))->toBeTruthy();
    });

    it('returns a translated error if the argument is not a callable and a translator is passed', function () {
        $isCallable = IsCallable::withTranslator(KeyValueTranslator::withDictionary([
            IsCallable::MESSAGE => 'NOT A CALLABLE!'
        ]));

        expect($isCallable->validate('true')->equals(ValidationResult::errors(['NOT A CALLABLE!'])))->toBeTruthy();
    });
});
