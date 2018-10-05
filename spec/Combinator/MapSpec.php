<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\Map;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Map', function () {
    it('does not modify the result of a failed validation', function () {
        expect(Map::to(new IsString(), 'strtolower')->validate(42))->toEqual((new IsString())->validate(42));
    });

    it('does modify the result of a correct validation', function () {
        expect(Map::to(new IsString(), 'strtolower')->validate('GIGI'))->toEqual(ValidationResult::valid('gigi'));
    });
});
