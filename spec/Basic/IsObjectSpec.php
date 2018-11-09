<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsObject;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsObject', function () {
    $isObject = new IsObject();

    it('returns a valid result if the argument is an object', function () use ($isObject) {
        $object = new \stdClass();

        expect($isObject->validate($object)->equals(ValidationResult::valid($object)))->toBeTruthy();
    });

    it('returns an error result if the argument is not an object', function () use ($isObject) {
        expect($isObject->validate(true)->equals(ValidationResult::errors([IsObject::NOT_AN_OBJECT])))->toBeTruthy();
    });

    it('returns a custom error result if the argument is not an object and a custom formatter is passed', function () {
        $isObject = IsObject::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isObject->validate(true)->equals(ValidationResult::errors(['1'])))->toBeTruthy();
    });
});
