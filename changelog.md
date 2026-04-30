# Changelog

All notable changes to `Antondate` will be documented in this file.

## v0.0.8

### Added
- `AntonDate::toLowerBoundInteger()` and `AntonDate::toUpperBoundInteger()`:
  treat the date as an interval and return its bounds as integers
  (`1923` → `19230101`/`19231231`; `1923-02` → `19230201`/`19230228`).
- `AntonDate::intervalIsAfter(AntonDate $other)`: range-aware ordering
  check that handles partial dates correctly. `1923-05-15` is not after
  the year-only `1923` (the latter covers the former), but it is after
  `1922`. Returns `false` when either side is unset.

The existing `isGreaterThan`/`isLessThan` are unchanged; they still
project to a single `YYYYMMDD` integer with missing parts as `00`, which
silently mishandles asymmetric partial-date comparisons. Prefer the new
`intervalIsAfter` for validating start/end ranges.

## Version 1.0

### Added
- Everything
