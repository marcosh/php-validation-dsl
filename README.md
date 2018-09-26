# php-validation-dsl

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

A `Validation` is a sum type which could be either valid, containing some valid
`$data`, or invalid, containing some error messages.

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
