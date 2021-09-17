<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Result;

use Marcosh\PhpValidationDSL\Basic\IsAsAsserted;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

describe('Validation result', function () {
    it('joins two valid result to a valid result joining the results', function () {
        $result1 = ValidationResult::valid('gigi');
        $result2 = ValidationResult::valid('bepi');

        $joinValid = function ($a, $b) {
            return $a . $b;
        };

        expect($result1->join($result2, $joinValid, 'array_merge'))->toEqual(ValidationResult::valid('gigibepi'));
    });

    it('joins a valid result with an invalid one to an invalid result preserving errors', function () {
        $result1 = ValidationResult::valid('gigi');
        $result2 = ValidationResult::errors(['bepi']);

        $joinValid = function ($a, $b) {
            return $a . $b;
        };

        expect($result1->join($result2, $joinValid, 'array_merge'))->toEqual(ValidationResult::errors(['bepi']));
    });

    it('joins an invalid result with a valid one to an invalid result preserving errors', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::valid('bepi');

        $joinValid = function ($a, $b) {
            return $a . $b;
        };

        expect($result1->join($result2, $joinValid, 'array_merge'))->toEqual(ValidationResult::errors(['gigi']));
    });

    it('joins two invalid results to an invalid result merging errors', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::errors(['bepi']);

        $joinValid = function ($a, $b) {
            return $a . $b;
        };

        expect($result1->join($result2, $joinValid, 'array_merge'))
            ->toEqual(ValidationResult::errors(['gigi', 'bepi']));
    });

    it('meets two valid result to a valid result joining the results', function () {
        $result1 = ValidationResult::valid('gigi');
        $result2 = ValidationResult::valid('bepi');

        expect($result1->meet(
            $result2,
            function ($x, $y) {
                return $x . $y;
            },
            function ($x) {
                return $x;
            },
            function ($y) {
                return $y;
            },
            'array_merge'
        ))->toEqual(ValidationResult::valid('gigibepi'));
    });

    it('meets a valid result with an invalid one to an valid result preserving value', function () {
        $result1 = ValidationResult::valid('gigi');
        $result2 = ValidationResult::errors(['bepi']);

        expect($result1->meet(
            $result2,
            function ($x, $y) {
                return $x . $y;
            },
            function ($x) {
                return $x;
            },
            function ($y) {
                return $y;
            },
            'array_merge'
        ))->toEqual(ValidationResult::valid('gigi'));
    });

    it('meets an invalid result with a valid one to a valid result preserving value', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::valid('bepi');

        expect($result1->meet(
            $result2,
            function ($x, $y) {
                return $x . $y;
            },
            function ($x) {
                return $x;
            },
            function ($y) {
                return $y;
            },
            'array_merge'
        ))->toEqual(ValidationResult::valid('bepi'));
    });

    it('meets two invalid results to an invalid result merging errors', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::errors(['bepi']);

        expect($result1->meet(
            $result2,
            function ($x, $y) {
                return $x . $y;
            },
            function ($x) {
                return $x;
            },
            function ($y) {
                return $y;
            },
            'array_merge'
        ))->toEqual(ValidationResult::errors(['gigi', 'bepi']));
    });

    it('processes correctly a valid result', function () {
        $result = ValidationResult::valid(42);

        $f = function ($n) {
            return $n + 1;
        };

        $id = function ($n) {
            return $n;
        };

        expect($result->process($f, $id))->toEqual(43);
    });

    it('processes correctly an invalid result', function () {
        $result = ValidationResult::errors(['gigi']);

        $id = function ($n) {
            return $n;
        };

        $arrayUp = function ($a) {
            return array_map('strtoupper', $a);
        };

        expect($result->process($id, $arrayUp))->toEqual(['GIGI']);
    });

    it('maps a valid result to a valid result with a mapped value', function () {
        $result = ValidationResult::valid(42);

        $f = function ($n) {
            return $n + 1;
        };

        expect($result->map($f))->toEqual(ValidationResult::valid(43));
    });

    it('maps an invalid result to itself', function () {
        $result = ValidationResult::errors(['gigi']);

        $f = function ($n) {
            return $n + 1;
        };

        expect($result->map($f))->toEqual($result);
    });

    it('mapErrors a valid result to itself', function () {
        $result = ValidationResult::valid(42);

        $arrayUp = function ($a) {
            return array_map('strtoupper', $a);
        };

        expect($result->mapErrors($arrayUp))->toEqual($result);
    });

    it('mapErrors an invalid result to an invalid result with mapped messages', function () {
        $result = ValidationResult::errors(['gigi']);

        $arrayUp = function ($a) {
            return array_map('strtoupper', $a);
        };

        expect($result->mapErrors($arrayUp))->toEqual(ValidationResult::errors(['GIGI']));
    });

    it('binds correctly a valid result', function () {
        $result = ValidationResult::valid(42);

        $bind = function (int $n) {
            return IsAsAsserted::withAssertion(function (int $m) use ($n) {
                return $m < $n;
            })->validate(37);
        };

        expect($result->bind($bind))->toEqual(ValidationResult::valid(37));
    });

    it('binds correctly an invalid result', function () {
        $result = ValidationResult::errors(['gigi']);

        $bind = function (int $n) {
            return IsAsAsserted::withAssertion(function (int $m) use ($n) {
                return $m < $n;
            })->validate(37);
        };

        expect($result->bind($bind))->toEqual($result);
    });

    it('applies correctly a valid function to a valid result', function () {
        $result = ValidationResult::valid(42);

        $apply = ValidationResult::valid(function (int $x) {
            return $x + 3;
        });

        expect($result->apply($apply))->toEqual(ValidationResult::valid(45));
    });

    it('applies a valid function to an invalid result producing the same invalid result', function () {
        $result = ValidationResult::errors(['gigi']);

        $apply = ValidationResult::valid(function (int $x) {
            return $x + 3;
        });

        expect($result->apply($apply))->toEqual($result);
    });

    it('applies an invalid function to a valid result producing an invalid result', function () {
        $result = ValidationResult::valid(42);

        $apply = ValidationResult::errors(['gigi']);

        expect($result->apply($apply))->toEqual($apply);
    });

    it('applies an invalid function to an invalid result producing an invalid result with both error messages', function () {
        $result = ValidationResult::errors(['gigi']);

        $apply = ValidationResult::errors(['toni']);

        expect($result->apply($apply))->toEqual(ValidationResult::errors(['toni', 'gigi']));
    });

    it('is equal to another result with the same valid content', function () {
        $result1 = ValidationResult::valid(42);
        $result2 = ValidationResult::valid(42);

        expect($result1->equals($result2))->toBeTruthy();
    });

    it('is equal to another invalid result with the same error message', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::errors(['gigi']);

        expect($result1->equals($result2))->toBeTruthy();
    });

    it('is not equal to an invalid result if valid', function () {
        $result1 = ValidationResult::valid(42);
        $result2 = ValidationResult::errors(['gigi']);

        expect($result1->equals($result2))->toBeFalsy();
    });

    it('is not equal to a valid result if invalid', function () {
        $result1 = ValidationResult::errors(['gigi']);
        $result2 = ValidationResult::valid(42);

        expect($result1->equals($result2))->toBeFalsy();
    });
});
