<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSLSpec\Example;

use Marcosh\PhpValidationDSL\Basic\IsGreaterThan;
use Marcosh\PhpValidationDSL\Basic\Regex;
use Marcosh\PhpValidationDSL\Equality;
use Marcosh\PhpValidationDSL\Result\ValidationResult;

/**
 * @param callable $f : ($a, $b) -> something
 * @return callable $a -> ($b -> something)
 */
function curry($f)
{
    return function ($a) use ($f) {
        return function ($b) use ($a, $f) {
            return $f($a, $b);
        };
    };
}

/**
 * This example is replicated from https://fsharpforfunandprofit.com/posts/elevated-world-3/#validation
 */

class CustomerId implements Equality
{
    /**
     * @var int should be positive
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param int $id
     * @return ValidationResult containing a CustomerId
     */
    public static function buildValid(int $id): ValidationResult
    {
        return IsGreaterThan::withBound(0)
            ->validate($id)
            ->map(function (int $id) {
                return new self($id);
            });
    }

    public function id(): int
    {
        return $this->id;
    }

    public function equals($that): bool
    {
        return $that instanceof self && $this->id === $that->id;
    }
}

class EmailAddress implements Equality
{
    /**
     * @var string should contain "@"
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @param string $email
     * @return ValidationResult containing an EmailAddress
     */
    public static function buildValid(string $email): ValidationResult
    {
        return Regex::withPattern('/^[\w.]+@[\w.]+$/u')
            ->validate($email)
            ->map(function (string $email) {
                return new self($email);
            });
    }

    public function email(): string
    {
        return $this->email;
    }

    public function equals($that): bool
    {
        return $that instanceof self && $this->email === $that->email;
    }
}

class CustomerInfo implements Equality
{
    /**
     * @var CustomerId
     */
    private $id;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    public function __construct(
        CustomerId $id,
        EmailAddress $emailAddress
    ) {
        $this->id = $id;
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param int $id
     * @param string $email
     * @return ValidationResult containing a CustomerInfo
     */
    public static function buildValidApplicative(int $id, string $email): ValidationResult
    {
        $idResult = CustomerId::buildValid($id);
        $emailResult = EmailAddress::buildValid($email);

        return $emailResult->apply($idResult->map(curry(function (CustomerId $id, EmailAddress $emailAddress) {
            return new self($id, $emailAddress);
        })));
    }

    /**
     * @param int $id
     * @param string $email
     * @return ValidationResult containing a CustomerInfo
     */
    public static function buildValidMonadic(int $id, string $email): ValidationResult
    {
        $idResult = CustomerId::buildValid($id);
        $emailResult = EmailAddress::buildValid($email);

        return $idResult->bind(function (CustomerId $id) use ($emailResult) {
            return $emailResult->bind(function (EmailAddress $email) use ($id) {
                return ValidationResult::valid(new self($id, $email));
            });
        });
    }

    public function id(): CustomerId
    {
        return $this->id;
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function equals($that): bool
    {
        return $that instanceof self &&
            $this->id->equals($that->id) &&
            $this->emailAddress->equals($that->emailAddress);
    }
}

describe('applicative style', function () {
    it('validates correctly a customer with correct id and email', function () {
        expect(
            CustomerInfo::buildValidApplicative(42, 'gigi@zucon.it')->equals(
                ValidationResult::valid(new CustomerInfo(new CustomerId(42), new EmailAddress('gigi@zucon.it')))
            )
        )->toBeTruthy();
    });

    it('returns the correct error if the id is negative' , function () {
        expect(
            CustomerInfo::buildValidApplicative(-42, 'gigi@zucon.it')->equals(
                ValidationResult::errors([IsGreaterThan::MESSAGE])
            )
        )->toBeTruthy();
    });

    it('returns the correct error if the email is not valid', function () {
        expect(
            CustomerInfo::buildValidApplicative(42, 'gigi')->equals(
                ValidationResult::errors([Regex::MESSAGE])
            )
        )->toBeTruthy();
    });

    it('returns the correct error messages if both is and email are not valid', function () {
        expect(
            CustomerInfo::buildValidApplicative(-42, 'gigi')->equals(
                ValidationResult::errors([Regex::MESSAGE, IsGreaterThan::MESSAGE])
            )
        )->toBeTruthy();
    });
});

describe('monadic style', function () {
    it('validates correctly a customer with correct id and email', function () {
        expect(
            CustomerInfo::buildValidMonadic(42, 'gigi@zucon.it')->equals(
                ValidationResult::valid(new CustomerInfo(new CustomerId(42), new EmailAddress('gigi@zucon.it')))
            )
        )->toBeTruthy();
    });

    it('returns the correct error if the id is negative' , function () {
        expect(
            CustomerInfo::buildValidMonadic(-42, 'gigi@zucon.it')->equals(
                ValidationResult::errors([IsGreaterThan::MESSAGE])
            )
        )->toBeTruthy();
    });

    it('returns the correct error if the email is not valid', function () {
        expect(
            CustomerInfo::buildValidMonadic(42, 'gigi')->equals(
                ValidationResult::errors([Regex::MESSAGE])
            )
        )->toBeTruthy();
    });

    it('returns the correct error messages if both is and email are not valid', function () {
        expect(
            CustomerInfo::buildValidMonadic(-42, 'gigi')->equals(
                ValidationResult::errors([IsGreaterThan::MESSAGE])
            )
        )->toBeTruthy();
    });
});
