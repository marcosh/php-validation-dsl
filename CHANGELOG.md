# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.3.0]

- added `Valid` validator
- added `Errors` validator
- added `IsGreaterThan` and `IsLessThan` validators
- added `Apply` combinator
- added `Bind` combinator
- added `composer-require-check`, `phpcs`, `phpstan` and `psalm` to `CI`
- use `MESSAGE` constant in all validators
- use `phpstan` strict rules
- use `psalm`
- add `Equality` interface to check object equality
- add `curry` and `uncurry` functions
- add `lift`, `sdo` and `mdo` functions

## [0.2.3] - 2018-12-05

- return received `$data` in `Focus` combinator

## [0.2.2] - 2018-12-03

- return `$data` in valid result for `Any` combinator

## [0.2.1] - 2018-11-30

- pass on `$context` in `Map` and `MapErrors` combinators

## [0.2.0] - 2018-11-22

- set `All` constructor as private
- set `Any` constructor as private
- added `MapError` validator
- added `HasNotKey` validator
- added `InArray` validator
- added `IsNumeric` validator
- added `TranslateErrors` combinator

## [0.1.0]

Basic validators and combinators