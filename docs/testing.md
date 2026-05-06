# Testing Guide

Run:

```bash
php bin/lint.php
php bin/test.php
```

The tests validate:

- Paper citation metadata.
- Feature extraction for benign and malicious HTML examples.
- Risk score calculation.
- Classifier consensus behavior.
- Private-network URL blocking.
- Detector result structure.

## Manual Smoke Test

1. Start the app with Docker Compose or the PHP built-in server.
2. Open `/health`.
3. Open `/`.
4. Open `/inspector`.
5. Submit benign HTML such as `<html><body><h1>Guide</h1></body></html>`.
6. Submit malicious-pattern HTML with hidden iframe, onload, eval, or redirection indicators.
7. Open `/paper`.
8. Open `/api/summary`.

## Database Validation

Load the migration and seed files into MySQL and confirm:

- 15 feature definitions.
- 5 classifier profiles.
- Seeded benign and malicious source samples.
- Seeded research experiments.
