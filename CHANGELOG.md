# Release Notes for Sentry Logger

## Unreleased

> {warning} Read through the [documentation](https://github.com/diginov/craft-sentry-logger/blob/main/README.md) if you are using the advanced configuration method before updating.

### Changed
- Updated and cleaned documentation.
- Updated Composer requirements for Craft 4 compatibility.
- Updated PHP typings requirements for Craft 4 compatibility.
- Updated deprecated use of `Craft::parseEnv()` with `App::parseEnv()`.
- Updated PHP namespace from `diginov\sentry` to `diginov\sentrylogger`.

## 1.2.0 - 2022-02-09

### Added
- Added image driver type and version in additional data pushed to Sentry.
- Added the ability to customize options passed to the Sentry SDK when it initializes.
- Added default Sentry [`http_proxy`](https://craftcms.com/docs/3.x/config/config-settings.html#httpproxy) option value to equal the Craft [`httpProxy`](https://docs.sentry.io/platforms/php/configuration/options/#http-proxy) general config setting.

### Changed
- Updated and cleaned documentation.
- Simplified PHP requirement in Composer.
- Refactoring code to make the Craft 4 upgrade easier.

### Fixed
- Fixed excluded HTTP status codes validation.

## 1.1.8 - 2022-01-17

### Changed
- Updated the required version of Sentry SDK.

## 1.1.7 - 2021-07-01

### Fixed
- Database issues no longer prevent errors from being sent. ([#5](https://github.com/diginov/craft-sentry-logger/pull/5))

### Changed
- Updated documentation.

## 1.1.6 - 2021-05-30

### Fixed
- Fixed excluded HTTP status codes validation.

## 1.1.5 - 2021-05-25

### Added
- Added the `exceptPatterns` configuration parameter.

### Changed
- Updated documentation about the `exceptPatterns` parameter.

## 1.1.4 - 2021-03-25

### Changed
- Optimized the way Sentry integrations are loaded.
- Updated additional data pushed to Sentry.
- Updated and cleaned documentation.

## 1.1.3 - 2021-03-13

### Added
- Added the `environment` configuration parameter.

### Changed
- Updated documentation and examples.
- Updated plugin settings with more parameters.

## 1.1.2 - 2021-03-04

### Changed
- Updated the required version of Sentry SDK.
- Updated texts and translations.
- Code syntax and cleanup.

### Fixed
- Fixed missing parent plugin init.

## 1.1.1 - 2021-02-03

### Added
- Added a button to test the current plugin configuration.

### Changed
- Updated and cleaned documentation.

## 1.1.0 - 2021-01-27

> {warning} Read through the [documentation](https://github.com/diginov/craft-sentry-logger/blob/master/README.md) if you are using the advanced configuration method before updating.

### Changed
- Updated the way the log target is added to the log dispatcher in Craft 3.6.
- Updated the advanced configuration to not use the deprecated `App::logConfig()` in Craft 3.6.
- Code cleanup and typo correction.

### Fixed
- Fixed possible duplicate in `except` message categories.

## 1.0.9 - 2021-01-27

### Added
- Added PHP 8 support. ([#1](https://github.com/diginov/craft-sentry-logger/pull/1))

### Changed
- Updated MIT license without a year in copyright.

## 1.0.8 - 2021-01-25

### Changed
- Updated `exceptCodes` validation before adding to `except` categories.

## 1.0.7 - 2020-11-05

### Added
- Added a new `app.name` to Sentry tags.
- Added Twig version to Sentry additional data.
- Added database driver and version to Sentry additional data.

### Changed
- Cleaned additional data sent to Sentry.
- Updated documentation about the `exceptCodes` parameter.
- Updated documentation about basic and advanced configuration files.

### Fixed
- Fixed request type detection for console commands.

### Removed
- Removed request method and mimetype from Sentry additional data.

## 1.0.6 - 2020-11-01

### Added
- Added missing translation in the settings model.

### Fixed
- Fixed Twig template path and line number added to stack frames when the exception occurs in a compiled template.

## 1.0.5 - 2020-10-31

### Added
- Added request method, ajax and mimetype to Sentry additional data.

### Changed
- Changed required Craft CMS version.

### Fixed
- Fixed excluded HTTP status codes validation.

## 1.0.4 - 2020-10-30

### Changed
- Changed documentation about except and exceptCodes parameters.

## 1.0.3 - 2020-10-30

### Changed
- Changed documentation, code comments and screenshot.

## 1.0.2 - 2020-10-24

### Added
- Added Twig template path and line number to stack trace frames when it is an exception that occurs in a compiled template.

### Fixed
- Fixed the Craft Licence value in Sentry additional data.

## 1.0.1 - 2020-10-23

### Added
- Added documentation URL in composer.json.

## 1.0.0 - 2020-10-23

### Added
- Initial release.
