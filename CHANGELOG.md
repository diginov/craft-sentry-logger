# Release Notes for Sentry Logger

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
