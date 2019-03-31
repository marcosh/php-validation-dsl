<?php

declare(strict_types=1);

namespace  Marcosh\PhpValidationDSLSpec\Result;

use function Marcosh\PhpValidationDSL\Result\lift;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('lift function', function () {
    it('wraps the result of a function with no arguments in a valid ValidationResult', function () {
        $f = static function () {
            return 42;
        };

        expect((lift($f)())->equals(ValidationResult::valid(42)))->toBeTruthy();
    });

    it('lifts a function with one argument, handling the valid case', function () {
        $f = static function (int $a) {
            return $a + 3;
        };

        expect((lift($f)(ValidationResult::valid(42)))->equals(ValidationResult::valid(45)))->toBeTruthy();
    });

    it('lifts a function with one argument, handling the failure case', function () {
        $f = static function (int $a) {
            return $a + 3;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope'])))->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with two arguments, handling the valid case', function () {
        $f = static function ($a, $b) {
            return $a + $b;
        };

        expect(
            (lift($f)(ValidationResult::valid(42), ValidationResult::valid(23)))->equals(ValidationResult::valid(65))
        )->toBeTruthy();
    });

    it('lifts a function with two arguments, handling the failure of the first argument', function () {
        $f = static function ($a, $b) {
            return $a + $b;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope']), ValidationResult::valid(23)))
                ->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with two arguments, handling the failure of the second argument', function () {
        $f = static function ($a, $b) {
            return $a + $b;
        };

        expect(
            (lift($f)(ValidationResult::valid(23), ValidationResult::errors(['nope'])))
                ->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with two arguments, handling the failure of both arguments', function () {
        $f = static function ($a, $b) {
            return $a + $b;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope1']), ValidationResult::errors(['nope2'])))
                ->equals(ValidationResult::errors(['nope1', 'nope2']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the valid case', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::valid(42), ValidationResult::valid(23), ValidationResult::valid(67)))
                ->equals(ValidationResult::valid(132))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the first argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope']), ValidationResult::valid(23), ValidationResult::valid(67)))
                ->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the second argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::valid(42), ValidationResult::errors(['nope']), ValidationResult::valid(67)))
                ->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the third argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::valid(42), ValidationResult::valid(23), ValidationResult::errors(['nope'])))
                ->equals(ValidationResult::errors(['nope']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the first and second argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope1']), ValidationResult::errors(['nope2']), ValidationResult::valid(67)))
                ->equals(ValidationResult::errors(['nope1', 'nope2']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the first and third argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope1']), ValidationResult::valid(23), ValidationResult::errors(['nope3'])))
                ->equals(ValidationResult::errors(['nope1', 'nope3']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of the second and third argument', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::valid(42), ValidationResult::errors(['nope2']), ValidationResult::errors(['nope3'])))
                ->equals(ValidationResult::errors(['nope2', 'nope3']))
        )->toBeTruthy();
    });

    it('lifts a function with three arguments, handling the failure of all arguments', function () {
        $f = static function ($a, $b, $c) {
            return $a + $b + $c;
        };

        expect(
            (lift($f)(ValidationResult::errors(['nope1']), ValidationResult::errors(['nope2']), ValidationResult::errors(['nope3'])))
                ->equals(ValidationResult::errors(['nope1', 'nope2', 'nope3']))
        )->toBeTruthy();
    });
});
