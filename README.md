# Drive-by Download ML Lab

A PHP 8.x and MySQL 8.0 research portal based on the paper **"Detection of Drive-by Download Attacks Using Machine Learning Approach"** by Monther Aldwairi, Musaab Hasan, and Zayed Balbahaith.

The project turns the paper's static webpage analysis workflow into a practical platform for safe URL/source inspection, HTML feature extraction, classifier profile comparison, experiment tracking, and research extension.

## Paper Reference

Aldwairi, M., Hasan, M., & Balbahaith, Z. (2017). **Detection of Drive-by Download Attacks Using Machine Learning Approach**. *International Journal of Information Security and Privacy*, 11(4), 16-28. IGI Global. https://doi.org/10.4018/IJISP.2017100102

The paper evaluated 23 machine-learning classifiers on 5,435 webpages using static features extracted from HTML source code. Bagged Trees was reported as the strongest classifier, with 90.1% accuracy, 96.24% true positive rate, and 26.07% false positive rate in the article abstract.

## What This Repository Provides

- Safe URL/source inspection workflow that reads HTML as text and does not execute browser code.
- Static feature extraction aligned with the paper's 15-feature approach.
- Research dashboard with dataset metrics, classifier model cards, experiments, and recent inspections.
- Inspector interface for either a live URL or pasted HTML source.
- JSON API for integration with other security dashboards or research notebooks.
- MySQL schema for feature definitions, classifier profiles, inspected sources, experiments, and audit events.
- Security-conscious PHP implementation with CSRF validation, security headers, PDO prepared statements, URL validation, private-network blocking, timeout controls, and source size limits.
- Docker-based local development setup.
- Lint and functional tests for the extractor, detector, citation metadata, and safe-fetch policy.

## Research Alignment

The implementation follows the paper's core workflow:

1. Collect benign and malicious webpage sources.
2. Parse HTML source without executing page scripts.
3. Extract static webpage features.
4. Compare candidate classifier profiles.
5. Present a practical inspection interface before a user visits a suspicious URL.

This repository does not include the original MATLAB-trained classifier artifacts. It provides a professional PHP/MySQL research lab scaffold with transparent heuristic scoring and model-card references, ready for later integration with exported trained models or an external scoring service.

## Quick Start

```bash
cp .env.example .env
docker compose up --build
```

Then open:

- Application: `http://localhost:8081`
- Inspector: `http://localhost:8081/inspector`
- Paper alignment: `http://localhost:8081/paper`
- Health endpoint: `http://localhost:8081/health`
- JSON summary: `http://localhost:8081/api/summary`

## Local Checks

```bash
php bin/lint.php
php bin/test.php
```

## Repository Structure

```text
public/              Web entry point and assets
src/                 PHP services, repository, security, and support classes
config/              Paper metadata, features, and classifier profiles
database/            MySQL schema and seed data
docs/                Architecture, paper alignment, security, testing, and extension notes
bin/                 Lint and test scripts
```

## Production Notes

- Add authentication and role-based access before operational use.
- Treat inspected URLs and HTML as security evidence.
- Store secrets outside source control.
- Enforce HTTPS and centralized logging.
- Keep outbound fetches behind egress controls and malware-safe infrastructure.
- Use the platform as research support and decision aid, not as the only web security control.

## License

MIT License. See [LICENSE](LICENSE).
