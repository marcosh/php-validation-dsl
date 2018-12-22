<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Basic;

use Marcosh\PhpValidationDSL\Basic\Valid;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Valid', function () {
    $valid = new Valid();

    it('returns a valid result', function () use ($valid) {
        expect($valid->validate('anything')->equals(ValidationResult::valid('anything')))->toBeTruthy();
    });
});
