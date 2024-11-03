# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.11] - 03.11.2024.

### Added

- [Outsider] Support for SQLite and MySQL, SQLite will be a default db
- [Shade] Support for adding components with `@component()` directive

### Fixed

- [Whisper] PHP server and Tailwindcss now run at the same time and output in the same log

### Removed

- Removed Docker, because it was too much for now, will add the support for it later with `php whisper add:docker` and `php whisper serve:docker`
  - Also all of Abyss projects will be when being deployed to production containerized with Docker, soon to come with a new tool **Oblivion**

## [0.1.1] - 31.10.2024.

### Added

- Shade Compiler
- Shade template engine with support for blade like syntax
- Ability to create layouts with shade and wrap your pages in them with `@layout()` directive
