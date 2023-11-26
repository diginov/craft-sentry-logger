# Release Notes for Sentry Logger

## 4.1.4 - 2023-11-26

### Changed
- Updated texts and translations.
- Updated the required version of Sentry SDK.

## 4.1.3 - 2023-06-08

### Changed
- Updated the required version of Sentry SDK.

## 4.1.2 - 2023-04-09

### Changed
- Updated the required version of Sentry SDK.

## 4.1.1 - 2022-08-19

### Fixed
- Fixed IP address filtering in `before_send` callback that prevent console logs from being sent.

## 4.1.0 - 2022-08-17

### Added
- Added the `userPrivacy` configuration parameter to specify what sensible data will be sent to Sentry.

### Changed
- Updated the required version of Sentry SDK.
- Updated documentation and examples.
- Refactoring code and cleanup.

## 4.0.1 - 2022-05-06

### Changed
- Updated advanced configuration examples to use `App::env('CRAFT_ENVIRONMENT')` instead of the constant directly.

## 4.0.0 - 2022-05-04

> {warning} Read through the [documentation](https://github.com/diginov/craft-sentry-logger/blob/main/README.md) if you are using the advanced configuration method before updating.

### Changed
- Updated Composer requirements for Craft 4 compatibility.
- Updated PHP typings requirements for Craft 4 compatibility.
- Updated deprecated use of `Craft::parseEnv()` with `App::parseEnv()`.
- Updated PHP namespace from `diginov\sentry` to `diginov\sentrylogger`.
- Updated log dispatcher target name.
- Updated and cleaned documentation.
