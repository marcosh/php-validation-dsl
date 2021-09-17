<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Marcosh\PhpValidationDSL\Equality;

/**
 * @template E
 * @template A
 */
final class ValidationResult
{
    /** @var bool */
    private $isValid;

    /** @var A|null */
    private $validContent;

    /** @var E[] */
    private $messages;

    /**
     * @param A $validContent
     * @param E[] $messages
     */
    private function __construct(
        bool $isValid,
        $validContent,
        array $messages
    ) {
        $this->isValid = $isValid;
        $this->validContent = $validContent;
        $this->messages = $messages;
    }

    /**
     * @template B
     * @template F
     * @param B $validContent
     * @return self<F, B>
     */
    public static function valid($validContent): self
    {
        return new self(true, $validContent, []);
    }

    /**
     * @template B
     * @template F
     * @param F[] $messages
     * @return self<F, B>
     */
    public static function errors(array $messages): self
    {
        return new self(false, null, $messages);
    }

    /**
     * @template F
     * @template B
     * @template G
     * @template C
     * @param self<F, B> $that
     * @param callable(A, B): C $joinValid
     * @param callable(E[], F[]): G[] $joinErrors
     * @return self<G, C>
     */
    public function join(self $that, callable $joinValid, callable $joinErrors): self
    {
        if (! $this->isValid || ! $that->isValid) {
            return self::errors($joinErrors($this->messages, $that->messages));
        }

        /** @var A $thisContent */
        $thisContent = $this->validContent;
        /** @var B $thatContent */
        $thatContent = $that->validContent;

        return self::valid($joinValid($thisContent, $thatContent));
    }

    /**
     * @template F
     * @template B
     * @template G
     * @template C
     * @param self<F, B> $that
     * @param callable(A, B): C $joinBothValid
     * @param callable(A): C $joinThisValid
     * @param callable(B): C $joinThatValid
     * @param callable(E[], F[]): G[] $joinErrors
     * @return self<G, C>
     */
    public function meet(
        self $that,
        callable $joinBothValid,
        callable $joinThisValid,
        callable $joinThatValid,
        callable $joinErrors
    ): self {
        $thisContent = $this->validContent;
        $thatContent = $that->validContent;

        if ($this->isValid && $that->isValid) {
            /**
             * @var A $thisContent
             * @var B $thatContent
             */

            return self::valid($joinBothValid($thisContent, $thatContent));
        }

        if ($this->isValid) {
            /** @var A $thisContent */

            return self::valid($joinThisValid($thisContent));
        }

        if ($that->isValid) {
            /** @var B $thatContent */

            return self::valid($joinThatValid($thatContent));
        }

        return self::errors($joinErrors($this->messages, $that->messages));
    }

    /**
     * @template B
     * @param callable(A): B $processValid
     * @param callable(E[]): B $processErrors
     * @return B
     */
    public function process(
        callable $processValid,
        callable $processErrors
    ) {
        if (! $this->isValid) {
            return $processErrors($this->messages);
        }

        /** @psalm-suppress PossiblyNullArgument */
        return $processValid($this->validContent);
    }

    /**
     * @template B
     * @param callable(A): B $map
     * @return self<E, B>
     */
    public function map(callable $map): self
    {
        return $this->process(
            /** @param A $validContent */
            function ($validContent) use ($map): self {
                return self::valid($map($validContent));
            },
            function (array $messages): self {
                return self::errors($messages);
            }
        );
    }

    /**
     * @template F
     * @param callable(E[]): F[] $map
     * @return self<F, A>
     */
    public function mapErrors(callable $map): self
    {
        return $this->process(
            /** @param mixed $validContent */
            function ($validContent): self {
                return self::valid($validContent);
            },
            function (array $messages) use ($map): self {
                return self::errors($map($messages));
            }
        );
    }

    /**
     * @template B
     * @param ValidationResult<E, callable(A): B> $apply
     * @return self<E, B>
     */
    public function apply(ValidationResult $apply): self
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return $apply->process(
            /**
             * @param callable(A): B $validApply
             * @return self<E, B>
             */
            function (callable $validApply): self {
                return $this->map($validApply);
            },
            /** @return self<E, B> */
            function (array $applyMessages) {
                return $this->process(
                    /** @param A $validContent */
                    function ($validContent) use ($applyMessages): self {
                        return self::errors($applyMessages);
                    },
                    function (array $messages) use ($applyMessages): self {
                        return self::errors(array_merge($applyMessages, $messages));
                    }
                );
            }
        );
    }

    /**
     * @template B
     * @param callable(A): self<E, B> $bind
     * @return self<E, B>
     */
    public function bind(callable $bind): self
    {
        return $this->process(
            /** @param A $validContent */
            function ($validContent) use ($bind): self {
                return $bind($validContent);
            },
            function (array $messages): self {
                return self::errors($messages);
            }
        );
    }

    /**
     * @param self<E, A> $that
     * @return bool
     */
    public function equals($that): bool
    {
        if (
            is_object($this->validContent) && is_object($that->validContent) &&
            get_class($this->validContent) === get_class($that->validContent) &&
            $this->validContent instanceof Equality
        ) {
            $contentEquality = $this->validContent->equals($that->validContent);
        } else {
            $contentEquality = $this->validContent === $that->validContent;
        }

        return ($this->isValid && $that->isValid && $contentEquality) ||
            (!$this->isValid && !$that->isValid && $this->messages === $that->messages);
    }
}
