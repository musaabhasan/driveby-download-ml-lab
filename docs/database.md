# Database Schema

The database uses MySQL 8.0 with `utf8mb4` collation.

## Tables

- `feature_definitions`: feature keys, descriptions, and scoring weights.
- `classifier_profiles`: top classifier model cards and reported metrics.
- `sample_sources`: local benign and malicious HTML examples for demonstration.
- `inspections`: inspected source hash, preview, risk score, consensus label, feature JSON, and prediction JSON.
- `experiments`: research improvement backlog and dataset/model experiments.
- `audit_events`: activity trail for saved inspections.

## Data Handling

Webpage source can contain sensitive internal URLs, tokens, hidden form values, or infrastructure details. The portal stores a hash and preview of the source by default, plus extracted features and prediction evidence.

Before production use, define retention, redaction, role access, and export policies.
