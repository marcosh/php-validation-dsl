<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Translator;

use Marcosh\PhpValidationDSL\Translator\ConstantTranslator;

describe('Constant translator', function () {
    it('always returns the same string', function () {
        $translator = ConstantTranslator::withTranslation('bepi');

        expect($translator->translate('gigi'))->toBe('bepi');
    });
});
