<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Combinator;

use Marcosh\PhpValidationDSL\Basic\IsString;
use Marcosh\PhpValidationDSL\Combinator\Focus;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Focus', function () {
    it('returns a valid result if the mapped data is valid', function () {
        $focus = Focus::on(
            function ($data) {
                return $data['nested'];
            },
            new IsString()
        );

        expect($focus->validate(['nested' => 'gigi']))->toEqual(ValidationResult::valid('gigi'));
    });

    it('returns an error result if the mapped data is not valid', function () {
        $focus = Focus::on(
            function ($data) {
                return $data['nested'];
            },
            new IsString()
        );

        expect($focus->validate(['nested' => 42]))->toEqual(ValidationResult::errors([IsString::NOT_A_STRING]));
    });
});
