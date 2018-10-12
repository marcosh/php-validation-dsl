<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsObject;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsObject', function () {
    $isObject = new IsObject();

    it('returns a valid result if the argument is an object', function () use ($isObject) {
        expect($isObject->validate(new \stdClass()))->toEqual(ValidationResult::valid(new \stdClass()));
    });

    it('returns an error result if the argument is not an object', function () use ($isObject) {
        expect($isObject->validate(true))->toEqual(ValidationResult::errors([IsObject::NOT_AN_OBJECT]));
    });
});
