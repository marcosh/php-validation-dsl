<?php

declare(strict_types=1);

namespace  Marcosh\PhpValidationDSLSpec\Translator;

use Marcosh\PhpValidationDSL\Translator\KeyValueTranslator;

describe('KeyValue translator', function () {
    it('returns the translated string if present', function () {
        $translator = KeyValueTranslator::withDictionary(['gigi' => 'bepi']);

        expect($translator->translate('gigi'))->toBe('bepi');
    });

    it('returns the received string if it is not in the dictionary', function () {
        $translator = KeyValueTranslator::withDictionary(['gigi' => 'bepi']);

        expect($translator->translate('toni'))->toBe('toni');
    });
});
