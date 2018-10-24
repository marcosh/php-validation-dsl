<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsCallable;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

class CallableFoo
{
    public static function bar() {}
    public function baz() {}
}

describe('IsCallable', function () {
    $isCallable = new IsCallable();

    it('returns a valid result if the argument is a built in function', function () use ($isCallable) {
        expect($isCallable->validate('sprintf'))->toEqual(ValidationResult::valid('sprintf'));
    });

    it('returns a valid result if the argument is an anonymous function', function () use ($isCallable) {
        $function = function () {};

        expect($isCallable->validate($function))->toEqual(ValidationResult::valid($function));
    });

    it('returns a valid result if the argument is a static method', function () use ($isCallable) {
        $staticMethod = [CallableFoo::class, 'bar'];

        expect($isCallable->validate($staticMethod))->toEqual(ValidationResult::valid($staticMethod));
    });

    it('returns a valid result if the argument is an instance method', function () use ($isCallable) {
        $instanceMethod = [new CallableFoo(), 'baz'];

        expect($isCallable->validate($instanceMethod))->toEqual(ValidationResult::valid($instanceMethod));
    });

    it('returns an error result if the argument is not a boolean', function () use ($isCallable) {
        expect($isCallable->validate('true'))->toEqual(ValidationResult::errors([IsCallable::NOT_A_CALLABLE]));
    });
});
