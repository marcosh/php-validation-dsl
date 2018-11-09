<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\Regex;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Regex', function () {
    $regex = Regex::withPattern('/^[\p{L} ]*$/u');

    it('returns a valid result if the pattern has a match', function () use ($regex) {
        expect($regex->validate('gigi')->equals(ValidationResult::valid('gigi')))->toBeTruthy();
    });

    it('returns an error result if the pattern does not match', function () use ($regex) {
        expect($regex->validate('gigi@zucon')->equals(ValidationResult::errors([Regex::MATCH_FAILED])))->toBeTruthy();
    });

    it('returns a custom error result if the pattern does not match and a custom formatter is passed', function () {
        $regex = Regex::withPatternAndFormatter('/^[\p{L} ]*$/u', function ($pattern, $data) {
            return [$pattern . $data];
        });

        expect($regex->validate('gigi@zucon')->equals(ValidationResult::errors(['/^[\p{L} ]*$/u' . 'gigi@zucon'])))
            ->toBeTruthy();
    });
});
