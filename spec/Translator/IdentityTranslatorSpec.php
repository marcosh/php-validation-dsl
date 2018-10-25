<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Translator;

use Marcosh\PhpValidationDSL\Translator\IdentityTranslator;

describe('Identity translator', function () {
    it('returns the string it receives', function () {
        $translator = new IdentityTranslator();

        expect($translator->translate('gigi'))->toBe('gigi');
    });
});
