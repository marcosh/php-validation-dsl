<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\IsResource;
use Marcosh\PhpValidationDSL\Result\ValidationResult;
use function fopen;

describe('IsResource', function () {
    $isResource = new IsResource();

    it('returns a valid result if the argument is a resource', function () use ($isResource) {
        $resource = fopen(__FILE__, 'rb');

        expect($isResource->validate($resource)->equals(ValidationResult::valid($resource)))->toBeTruthy();
    });

    it('returns an error result if the argument is not a resource', function () use ($isResource) {
        expect($isResource->validate('gigi')->equals(ValidationResult::errors([IsResource::NOT_A_RESOURCE])))
            ->toBeTruthy();
    });

    it('returns a custom error result if the argument is not a resource and custom formatter is passed', function () {
        $isResource = IsResource::withFormatter(function ($data) {
            return [(string) $data];
        });

        expect($isResource->validate('gigi')->equals(ValidationResult::errors(['gigi'])))->toBeTruthy();
    });
});
