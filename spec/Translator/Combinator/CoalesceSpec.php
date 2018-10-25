<?php

declare(strict_types=1);

namespace  Marcosh\PhpValidationDSLSpec\Translator\Combinator;

use Marcosh\PhpValidationDSL\Translator\Combinator\Coalesce;
use Marcosh\PhpValidationDSL\Translator\ConstantTranslator;

describe('Coalesce translator combinator', function () {
    it('returns the received string if it does not receive any translator', function () {
        $translator = Coalesce::withTranslators();

        expect($translator->translate('gigi'))->toBe('gigi');
    });

    it('returns the received string if it receives only null translators', function () {
        $translator = Coalesce::withTranslators(null, null, null);

        expect($translator->translate('gigi'))->toBe('gigi');
    });

    it('returns the translated string according to the first non-null translator', function () {
        $translator1 = ConstantTranslator::withTranslation('bepi');
        $translator2 = ConstantTranslator::withTranslation('toni');
        $translator = Coalesce::withTranslators(null, $translator1, $translator2);

        expect($translator->translate('gigi'))->toBe('bepi');
    });
});
