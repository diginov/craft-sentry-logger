# Release Notes for Sentry Logger

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
