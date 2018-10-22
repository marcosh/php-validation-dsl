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

        expect($isResource->validate($resource))->toEqual(ValidationResult::valid($resource));
    });

    it('returns an error result if the argument is not a resource', function () use ($isResource) {
        expect($isResource->validate('gigi'))->toEqual(ValidationResult::errors([IsResource::NOT_A_RESOURCE]));
    });
});
