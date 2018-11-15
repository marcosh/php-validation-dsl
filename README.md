# php-validation-dsl

[![Build Status](https://travis-ci.com/marcosh/php-validation-dsl.svg?branch=master)](https://travis-ci.org/marcosh/php-validation-dsl)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/marcosh/php-validation-dsl/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/marcosh/php-validation-dsl/?branch=master)
[![Code Climate](https://codeclimate.com/github/marcosh/php-validation-dsl/badges/gpa.svg)](https://codeclimate.com/github/marcosh/php-validation-dsl)

A DSL for validating data in a functional fashion

## Basic idea

The idea is pretty simple. All goes around the following interface

```php
interface Validation
{
    public function validate($data): ValidationResult;
}
```

where some `$data` comes in and a `ValidationResult` comes out.

A `ValidationResult` is a sum type which could be either valid, containing some
valid `$data`, or invalid, containing some error messages.

### Immutability

Everything is immutable, so once you have created a validator you can not
modify it, you can just create a new one.

On the other hand, immutability implies statelessness, and therefore you can
reuse safely the same validator multiple times with different data.

### Compositionality

The library provides (or will provide) a huge number of combinators which will
allow creating complex validators just putting together simple ones.

It goes without saying that you can create new validators to be used together
with the existing ones.

## Example

Suppose you want to validate some data with the following format

```php
[
    'name' => ... // non empty string
    'age' => ... // non-negative integer
]
```

To describe what we would like to check in plain text, we need to verify that:

- the data we receive are an array
- we have a `name` field and it should be a non-empty string
- we have an `age` field and it should be a non-negative integer

The validator which does this check should look like:

```php
Sequence::validations([
    new IsArray(),
    All::validations([
        Sequence::validations([
            HasKey::withKey('name'),
            Focus::on(
                function ($data) {
                    return $data['name'];
                },
                Sequence::validations([
                    new IsString(),
                    new NonEmpty()
                ])
            )
        ]),
        Sequence::validations([
            HasKey::withKey('age'),
            Focus::on(
                function ($data) {
                    return $data['age'];
                },
                Sequence::validations([
                    new IsInteger(),
                    IsAsAsserted::withAssertion(function ($data) {
                        return $data >= 0;
                    })
                ])
            )
        ])
    ])
]);
```

Let's go through it step by step to understand every single piece.

We start with a `Sequence` validator. Its semantic is that it is going to
perform a series of validation sequentially, one after the other, returning an
error as soon as one fails.

In our case we start by checking that our data are an array, with the `IsArray`
validator. Once we know we have an array, we can check if the two required
fields exist. We can do these two operations independently, and, if they both
fail, we want both the error messages. We use the `All` validator exactly for
this, to say that all the listed conditions need to be verified and that we
want all the error messages.

At this point we have the validations for `name` and the validations for `age`.
For the former, we first check that the `name` key is present, with the
`HasKey` validator. Then we want to validate the value of the `name` key; to do
this we need to focus our attention not on the whole data structure, but just
on the single value. We use the `Focus` validator to specify a callable which
allows to inspect the specific value. At this point we use `Sequence` again to
check that the value is a string, using the `IsString` validator, and to assert
that it is not empty, with the `NonEmpty` validator.

For the `age` field, we do something similar. The only difference is that we
check if it is an integer with the `IsInteger` validator and we use the
`IsAsAsserted` validator, built with a user defined callable, to check that the
value is a positive integer.

This example shows the basic usage of the library. In the next paragraphs we
will see how to customize its behaviour and how to use more advanced
functionalities.

## Context

How can you use a runtime value to validate some data, while you are creating
your validators at build time? Well, that's what the `context` is there for.

Let's provide a motivating example for this: suppose we want to write a
validator to check whether we are violating a uniqueness condition while
updating a member in a collection. We will need to check it the data already
exist in our collection, excluding the record we are currently updating.
So we will need, beyond the data themselves, an identifier of the record which
we are currently updating. We can pass this information in the context. Then
the validator could look like

```php
class CheckDuplicateExceptCurrentRecord
{
    private $recordRepository;
    
    public function __construct($recordRepository)
    {
        $this->recordRespository = $recordRepository;
    }

    public function validate($data, $context)
    {
        if ($recordRepository->containsDataExcludingRecord($data, $context['id'])) {
            return ValidationResult::errors(['DUPLICATE RECORD']);
        }
        
        return ValidationResult::valid($data);
    }
}
```

Then we can use our validator as follows

```php
$validator = new CheckDuplicateExceptCurrentRecord($recordRepository);

$validator->validate($data, ['id' => $currentRecordId]);
```

## Custom error formatters

The library itself does not want to impose how error messages should be
structured and formatted. Therefore it allows the user to define his own error
messages and his own error messages structure.

To do this every validator included in the library may be built using a custom
error formatter.

The error formatter is nothing else but a callable which receives as input all
the data known by the validator. Often this arguments will be just the data
which need to be validated, but sometimes the error formatter could receive
also the configuration parameters of the validator.

For example, the `IsInstanceOf` validator has a named constructor

```php
public static function withClassNameAndFormatter(
    string $className,
    callable $errorFormatter
):
```

where the `$errorFormatter` needs to be a callable receiving as parameters the
`$className` and the validation `$data`. For example we could use it as follows

```php
$myValidator = IsInstanceOf::withClassNameAndFormatter(
    Foo::class,
    function ($className, $data) {
        return [
            sprintf(
                'The data %s is not an instance of %s',
                json_encode($data),
                $className
            )
        ];
    }
);
```

### Translators

One specific usage of custom error formatters it translating the error
messages.

Every validator has also a named constructor receiving a `Translator` to
translate the library-defined error messages.

## How to use this library

This library is not build to be a ready-made artifact that you can install and
start using immediately. On the contrary it provides just some basic elements
which you can use to easily build your own validators.

The idea is that everyone could create his own library of validators, specific
for his own domain and use case, composing the basic validators and custom ones
with the help of the provided combinators.
