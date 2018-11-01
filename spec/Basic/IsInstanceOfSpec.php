<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsInstanceOf;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function json_encode;

class InstanceFoo {};

describe('IsInstanceOf', function () {
    $isInstanceOf = IsInstanceOf::withClassName(InstanceFoo::class);

    it('returns a valid result if the argument is an instance of the given class', function () use ($isInstanceOf) {
        expect($isInstanceOf->validate(new InstanceFoo()))->toEqual(ValidationResult::valid(new InstanceFoo()));
    });

    it('returns an error result if the argument is not a string', function () use ($isInstanceOf) {
        expect($isInstanceOf->validate(new \stdClass()))
            ->toEqual(ValidationResult::errors([IsInstanceOf::NOT_AN_INSTANCE]));
    });

    it('returns a custom error result if the argument is not a string and a custom formatter is passed', function () {
        $isInstanceOf = IsInstanceOf::withClassNameAndFormatter(
            InstanceFoo::class,
            function (string $className, $data) {
                return [$className .  json_encode($data)];
            }
        );

        expect($isInstanceOf->validate(new \stdClass()))
            ->toEqual(ValidationResult::errors([InstanceFoo::class . '{}']));
    });
});
