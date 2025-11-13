# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2025-11-13

### Added

- Add explicit support for PHP 8.5 in composer.json constraints.
- [Development Docker Image] Add `PHP_VERSION` and `WITH_XDEBUG` environment variables and build args to make Docker image more flexible.

### Changed

- Remove composer.json repository override for 'phoneburner/php-coding-standard' (as it is now available on Packagist)
- [Development Docker Image] Switch from PECL to PIE for installing PHP extensions.
- [Development Docker Image] Optional Xdebug extension is no longer installed by default.
- [Development Docker Image] Install git, fixing Composer root-version warning message.

### Fixed

- Fix whitespace issues in .gitattributes

## 1.0.0 - 2025-07-29

### Added

- Initial Project Release.
