<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsIterable;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('IsIterable', function () {
    $isIterable = new IsIterable();

    it('returns a valid result if the argument is an array', function () use ($isIterable) {
        expect($isIterable->validate([])->equals(ValidationResult::valid([])))->toBeTruthy();
    });

    it('returns a valid result if the argument is a Traversable', function () use ($isIterable) {
        $iterator = new \ArrayIterator();

        expect($isIterable->validate($iterator)->equals(ValidationResult::valid($iterator)))->toBeTruthy();
    });

    it('returns an error result if the argument is not an iterable', function () use ($isIterable) {
        expect($isIterable->validate('gigi')->equals(ValidationResult::errors([IsIterable::NOT_AN_ITERABLE])))
            ->toBeTruthy();
    });

    it('returns a custom error result if the argument is not iterable and a custom formatter is passed', function () {
        $isIterable = IsIterable::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isIterable->validate('gigi')->equals(ValidationResult::errors(['gigi'])))->toBeTruthy();
    });

    it('returns a translated error result if the argument is not iterable and a translator is passed', function () {
        $isIterable = IsIterable::withTranslator(KeyValueTranslator::withDictionary([
            IsIterable::NOT_AN_ITERABLE => 'NO ITERABLE HERE!'
        ]));

        expect($isIterable->validate('gigi')->equals(ValidationResult::errors(['NO ITERABLE HERE!'])))->toBeTruthy();
    });
});
