<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

final class ValidationResult
{
    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var mixed
     */
    private $validContent;

    /**
     * @var array
     */
    private $messages;

    private function __construct(
        bool $isValid,
        $validContent,
        array $messages
    ) {
        $this->isValid = $isValid;
        $this->validContent = $validContent;
        $this->messages = $messages;
    }

    public static function valid($validContent): self
    {
        return new self(true, $validContent, []);
    }

    public static function errors(array $messages): self
    {
        return new self(false, null, $messages);
    }

    public function join(self $that, callable $joinValid, callable $joinErrors): self
    {
        if (! $this->isValid || ! $that->isValid) {
            return self::errors($joinErrors($this->messages, $that->messages));
        }

        return self::valid($joinValid($this->validContent, $that->validContent));
    }

    public function meet(self $that, callable $joinErrors): self
    {
        if ($this->isValid) {
            return $this;
        }

        if ($that->isValid) {
            return $that;
        }

        return self::errors($joinErrors($this->messages, $that->messages));
    }

    /**
     * @param callable $processValid : validContent -> mixed
     * @param callable $processErrors : messages -> mixed
     * @return mixed
     */
    public function process(
        callable $processValid,
        callable $processErrors
    ) {
        if (! $this->isValid) {
            return $processErrors($this->messages);
        }

        return $processValid($this->validContent);
    }

    /**
     * @param callable $map : validContent -> newValidContent
     * @return ValidationResult
     */
    public function map(callable $map): self
    {
        return $this->process(
            function ($validContent) use ($map) {
                return self::valid($map($validContent));
            },
            function ($messages) {
                return self::errors($messages);
            }
        );
    }

    public function mapErrors(callable $map): self
    {
        return $this->process(
            function ($validContent) {
                return self::valid($validContent);
            },
            function (array $messages) use ($map) {
                return self::errors($map($messages));
            }
        );
    }

    /**
     * @param callable $bind : validContent -> ValidationResult
     * @return ValidationResult
     */
    public function bind(callable $bind): self
    {
        return $this->process(
            function ($validContent) use ($bind) {
                return $bind($validContent);
            },
            function (array $messages) {
                return self::errors($messages);
            }
        );
    }

    public function equals(self $that): bool
    {
        return ($this->isValid && $that->isValid && $this->validContent === $that->validContent) ||
            (!$this->isValid && !$that->isValid && $this->messages === $that->messages);
    }
}
