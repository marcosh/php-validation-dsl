<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

use Marcosh\PhpValidationDSL\Equality;

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

    /**
     * @param bool $isValid
     * @param mixed $validContent
     * @param array $messages
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
     * @param mixed $validContent
     * @return self
     */
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
            /** @param mixed $validContent */
            function ($validContent) use ($map): self {
                return self::valid($map($validContent));
            },
            function (array $messages): self {
                return self::errors($messages);
            }
        );
    }

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
     * @param ValidationResult $apply contains a callable
     * @return self
     */
    public function apply(ValidationResult $apply): self
    {
        return $apply->process(
            function (callable $validApply): self {
                return $this->map($validApply);
            },
            /** @return mixed */
            function (array $applyMessages) {
                return $this->process(
                    /** @param mixed $validContent */
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
     * @param callable $bind : validContent -> ValidationResult
     * @return self
     */
    public function bind(callable $bind): self
    {
        return $this->process(
            /** @param mixed $validContent */
            function ($validContent) use ($bind): self {
                return $bind($validContent);
            },
            function (array $messages): self {
                return self::errors($messages);
            }
        );
    }

    public function equals(self $that): bool
    {
        if (is_object($this->validContent) && is_object($that->validContent) &&
            get_class($this->validContent) === get_class($that->validContent) &&
            $this->validContent instanceof Equality) {
            $contentEquality = $this->validContent->equals($that->validContent);
        } else {
            $contentEquality = $this->validContent === $that->validContent;
        }

        return ($this->isValid && $that->isValid && $contentEquality) ||
            (!$this->isValid && !$that->isValid && $this->messages === $that->messages);
    }
}
