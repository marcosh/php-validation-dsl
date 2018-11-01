<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsIterable;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('IsIterable', function () {
    $isIterable = new IsIterable();

    it('returns a valid result if the argument is an array', function () use ($isIterable) {
        expect($isIterable->validate([]))->toEqual(ValidationResult::valid([]));
    });

    it('returns a valid result if the argument is a Traversable', function () use ($isIterable) {
        expect($isIterable->validate(new \ArrayIterator()))->toEqual(ValidationResult::valid(new \ArrayIterator()));
    });

    it('returns an error result if the argument is not an iterable', function () use ($isIterable) {
        expect($isIterable->validate('gigi'))->toEqual(ValidationResult::errors([IsIterable::NOT_AN_ITERABLE]));
    });

    it('returns a custom error result if the argument is not iterable and a custom formatter is passed', function () {
        $isIterable = IsIterable::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isIterable->validate('gigi'))->toEqual(ValidationResult::errors(['gigi']));
    });
});
