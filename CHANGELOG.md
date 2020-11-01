# Release Notes for Sentry Logger

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
