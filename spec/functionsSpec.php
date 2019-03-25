<?php

declare(strict_types=1);

namespace  Marcosh\PhpValidationDSLSpec;

use function Marcosh\PhpValidationDSL\curry;

class Adder
{
    public static function sum(int $a, int $b): int
    {
        return $a + $b;
    }
}

describe('curry function', function () {
    it('does not modify a function with no arguments', function () {
        $f = function () {
            return 42;
        };

        expect(curry($f)())->toBe(42);
    });

    it('does not modify a function with one argument', function () {
        $f = function (int $a) {
            return $a + 3;
        };

        expect(curry($f)(2))->toBe(5);
    });

    it('curries a function with two arguments', function () {
       $f = function (int $a, int $b) {
            return $a + $b;
       };

       expect (curry($f)(2)(3))->toBe(5);
    });

    it('curries a function with three arguments', function () {
        $f = function (int $a, int $b, int $c) {
            return $a + $b + $c;
        };

        expect (curry($f)(2)(3)(4))->toBe(9);
    });

    it('curries an object method', function () {
        $foo = new class {
            public function bar(int $a, int $b): int {
                return $a + $b;
            }
        };

        $f = [$foo, 'bar'];

        expect (curry($f)(2)(3))->toBe(5);
    });

    it('curries a static method', function () {
        $f = [Adder::class, 'sum'];

        expect (curry($f)(2)(3))->toBe(5);
    });

    it('curries as invokable object', function () {
        $f = new class {
            public function __invoke(int $a, int $b): int
            {
                return $a + $b;
            }
        };

        expect (curry($f)(2)(3))->toBe(5);
    });
});

