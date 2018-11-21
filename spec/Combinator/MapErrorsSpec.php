<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\MapErrors;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('MapErrors', function () {
    $map = function (array $messages) {
        return array_map('strtoupper', $messages);
    };

    it('does not modify the result of a correct validation', function () use ($map) {
        expect(MapErrors::to(new IsString(), $map)->validate('gigi')->equals(ValidationResult::valid('gigi')))
            ->toBeTruthy();
    });

    it('does modify the result of a failed validation', function () use ($map) {
        expect(
            MapErrors::to(new IsString(), $map)->validate(42)->equals(ValidationResult::errors([
                strtoupper(IsString::MESSAGE)
            ]))
        )->toBeTruthy();
    });
});
