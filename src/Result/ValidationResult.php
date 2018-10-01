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

    public function mapError(callable $map): self
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
}
